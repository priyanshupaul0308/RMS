<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\TableModel;
use App\Models\KotModel;
use App\Models\AuditLogModel;
use CodeIgniter\Database\BaseConnection;

/**
 * OrderService
 * 
 * Orchestrates order placement, pricing/tax computations, kitchen ticket generation,
 * recipe-based raw material stock deductions, and table lifecycle management.
 */
class OrderService
{
    protected BaseConnection $db;
    protected OrderModel      $orderModel;
    protected OrderItemModel  $orderItemModel;
    protected TableModel      $tableModel;
    protected KotModel        $kotModel;
    protected AuditLogModel   $auditModel;

    public function __construct()
    {
        $this->db             = \Config\Database::connect();
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->tableModel     = new TableModel();
        $this->kotModel       = new KotModel();
        $this->auditModel     = new AuditLogModel();
    }

    /**
     * Process and place an order atomically with recipe-driven stock consumption.
     *
     * @param array<string, mixed> $payload Order parameters from POS
     * @param int                  $restaurantId Current tenant restaurant ID
     * @param int                  $branchId Current branch ID
     * @param int                  $userId Staff member placing order
     * @return array<string, mixed> Result payload with order & KOT details
     * @throws \RuntimeException On transaction or validation failure
     */
    public function placeOrder(array $payload, int $restaurantId, int $branchId, int $userId): array
    {
        $items = $payload['items'] ?? [];
        if (empty($items)) {
            throw new \InvalidArgumentException('Order cart cannot be empty.');
        }

        $orderType       = (string)($payload['order_type'] ?? 'dine_in');
        $tableId         = !empty($payload['table_id']) ? (int)$payload['table_id'] : null;
        $customerName    = trim((string)($payload['customer_name'] ?? ''));
        $customerPhone   = trim((string)($payload['customer_phone'] ?? ''));
        $customerAddress = trim((string)($payload['customer_address'] ?? ''));
        $notes           = trim((string)($payload['notes'] ?? ''));

        // If customerName is empty or generic, check if the table has an active seated or confirmed reservation today
        if (($customerName === '' || $customerName === 'Walk-in Guest') && $tableId) {
            $res = $this->db->table('reservations')
                ->where('table_id', $tableId)
                ->where('reservation_date', date('Y-m-d'))
                ->whereIn('status', ['seated', 'confirmed'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($res && !empty($res['customer_name'])) {
                $customerName = trim((string)$res['customer_name']);
                if (empty($customerPhone) && !empty($res['customer_phone'])) {
                    $customerPhone = trim((string)$res['customer_phone']);
                }
            }
        }

        if ($customerName === '') {
            $customerName = 'Walk-in Guest';
        }

        if ($orderType === 'dine_in' && !$tableId) {
            throw new \InvalidArgumentException('A dining table must be selected for Dine-In orders.');
        }

        // Auto-register new customer in CRM directory so their profile appears in CRM immediately
        if (!empty($customerPhone)) {
            $existingCust = $this->db->table('customers')
                ->where('restaurant_id', $restaurantId)
                ->where('phone', $customerPhone)
                ->get()
                ->getRowArray();
            if (!$existingCust) {
                $displayName = ($customerName !== '' && $customerName !== 'Walk-in Guest')
                    ? $customerName
                    : 'Customer ' . substr($customerPhone, -4);
                $this->db->table('customers')->insert([
                    'restaurant_id'  => $restaurantId,
                    'name'           => $displayName,
                    'phone'          => $customerPhone,
                    'loyalty_points' => 0,
                    'total_visits'   => 0,
                    'lifetime_spend' => 0.00,
                    'vip_status'     => 0,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->db->transStart();

        // 1. Calculate Financials
        $subtotal = 0.00;
        foreach ($items as $item) {
            $qty = max(1.0, (float)($item['quantity'] ?? 1));
            $unitPrice = (float)($item['unit_price'] ?? 0);
            $subtotal += ($unitPrice * $qty);
        }

        // Fetch Tax Rates for Restaurant (or default 5% GST)
        $taxPercent = 5.00;
        $taxRateRow = $this->db->table('tax_rates')
            ->where('restaurant_id', $restaurantId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();
        if ($taxRateRow) {
            $taxPercent = (float)$taxRateRow['rate'];
        }

        $taxAmount = round(($subtotal * $taxPercent) / 100, 2);

        // Service charge if dine-in
        $serviceCharge = 0.00;
        if ($orderType === 'dine_in') {
            $serviceSetting = $this->db->table('settings')
                ->where('restaurant_id', $restaurantId)
                ->where('key', 'service_charge_pct')
                ->get()
                ->getRowArray();
            $svcPct = $serviceSetting ? (float)$serviceSetting['value'] : 5.00;
            $serviceCharge = round(($subtotal * $svcPct) / 100, 2);
        }

        $finalTotal = $subtotal + $taxAmount + $serviceCharge;

        // 2. Check for Table Tab Append
        $orderId = null;
        $orderNumber = null;
        if ($tableId) {
            $table = $this->tableModel->find($tableId);
            if (!empty($table['current_order_id'])) {
                $existing = $this->orderModel->find((int)$table['current_order_id']);
                if ($existing && in_array($existing['status'], ['pending', 'confirmed', 'preparing', 'ready', 'served'], true)) {
                    $orderId     = (int)$existing['id'];
                    $orderNumber = (string)$existing['order_number'];

                    $this->orderModel->update($orderId, [
                        'subtotal'       => (float)$existing['subtotal'] + $subtotal,
                        'tax_amount'     => (float)$existing['tax_amount'] + $taxAmount,
                        'service_charge' => (float)($existing['service_charge'] ?? 0) + $serviceCharge,
                        'final_total'    => (float)$existing['final_total'] + $finalTotal,
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // 3. Create Order if not appending
        if ($orderId === null) {
            $orderNumber = $this->orderModel->generateOrderNumber();
            $orderId = (int)$this->orderModel->insert([
                'restaurant_id'    => $restaurantId,
                'branch_id'        => $branchId,
                'order_number'     => $orderNumber,
                'order_type'       => $orderType,
                'table_id'         => $tableId,
                'customer_name'    => $customerName,
                'customer_phone'   => $customerPhone ?: null,
                'customer_address' => $customerAddress ?: null,
                'waiter_id'        => $userId,
                'cashier_id'       => $userId,
                'status'           => 'confirmed',
                'subtotal'         => $subtotal,
                'tax_amount'       => $taxAmount,
                'service_charge'   => $serviceCharge,
                'final_total'      => $finalTotal,
                'payment_status'   => 'unpaid',
                'notes'            => $notes ?: null,
            ]);
        }

        // 4. Generate KOT for Kitchen Routing
        $kotNumber = $this->kotModel->generateKotNumber();
        $kotId = (int)$this->kotModel->insert([
            'order_id'   => $orderId,
            'branch_id'  => $branchId,
            'kot_number' => $kotNumber,
            'status'     => 'sent',
            'printed_at' => date('Y-m-d H:i:s'),
        ]);

        // 5. Insert Line Items & Auto-Deduct Raw Inventory from Recipes
        foreach ($items as $it) {
            $menuItemId = (int)$it['menu_item_id'];
            $qty        = max(1.0, (float)($it['quantity'] ?? 1));
            $unitPrice  = (float)($it['unit_price'] ?? 0);
            $itemSub    = $unitPrice * $qty;
            $itemTax    = round(($itemSub * $taxPercent) / 100, 2);

            $this->orderItemModel->insert([
                'order_id'      => $orderId,
                'menu_item_id'  => $menuItemId,
                'item_name'     => (string)$it['item_name'],
                'unit_price'    => $unitPrice,
                'quantity'      => $qty,
                'subtotal'      => $itemSub,
                'tax_amount'    => $itemTax,
                'total'         => $itemSub + $itemTax,
                'special_notes' => !empty($it['special_notes']) ? (string)$it['special_notes'] : null,
                'status'        => 'sent',
                'kot_id'        => $kotId,
            ]);

            // Execute Recipe-driven Raw Inventory Deduction
            $this->deductRecipeStock($menuItemId, $qty, $branchId, $orderNumber);
        }

        // 6. Occupy Table if Dine-In
        if ($tableId) {
            $this->tableModel->updateStatus($tableId, 'occupied', $orderId);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Failed to place order due to a database transaction error.');
        }

        // 7. Record Immutable Audit Trail
        $this->auditModel->recordEvent(
            'orders',
            'create_order',
            "Order #{$orderNumber} placed ({$orderType}) for \${$finalTotal} (KOT: {$kotNumber})",
            $userId,
            'orders',
            $orderId
        );

        return [
            'order_id'     => $orderId,
            'order_number' => $orderNumber,
            'kot_number'   => $kotNumber,
            'subtotal'     => $subtotal,
            'tax_amount'   => $taxAmount,
            'final_total'  => $finalTotal,
            'order_type'   => $orderType,
            'table_id'     => $tableId,
        ];
    }

    /**
     * Deduct raw ingredients defined in item_recipes for the given menu dish portion.
     */
    protected function deductRecipeStock(int $menuItemId, float $orderQty, int $branchId, string $orderNumber): void
    {
        $recipes = $this->db->table('item_recipes')
            ->where('menu_item_id', $menuItemId)
            ->get()
            ->getResultArray();

        if (empty($recipes)) {
            return; // No recipe mapping defined for this item
        }

        foreach ($recipes as $rec) {
            $invId = (int)$rec['inventory_item_id'];
            $reqPerPortion = (float)$rec['quantity_required'];
            $totalDeduction = round($reqPerPortion * $orderQty, 3);

            // Decrement inventory stock
            $this->db->table('inventory_items')
                ->where('id', $invId)
                ->where('branch_id', $branchId)
                ->set('current_stock', "GREATEST(0, current_stock - {$totalDeduction})", false)
                ->update();

            // Record stock adjustment trail
            $item = $this->db->table('inventory_items')->where('id', $invId)->get()->getRowArray();
            if ($item) {
                $this->db->table('stock_adjustments')->insert([
                    'branch_id'         => $branchId,
                    'inventory_item_id' => $invId,
                    'adjustment_type'   => 'out',
                    'quantity'          => $totalDeduction,
                    'previous_stock'    => (float)$item['current_stock'] + $totalDeduction,
                    'new_stock'         => (float)$item['current_stock'],
                    'reason'            => "Recipe consumption for Order {$orderNumber} (Qty: {$orderQty})",
                    'adjusted_by'       => null,
                    'created_at'        => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
