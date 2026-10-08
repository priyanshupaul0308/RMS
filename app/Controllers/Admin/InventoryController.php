<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InventoryItemModel;
use App\Models\SupplierModel;
use App\Models\PurchaseOrderModel;
use App\Models\WasteLogModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * InventoryController
 * 
 * Phase 6: Back-of-House Inventory, Procurement, Suppliers, and Spoilage Management.
 */
class InventoryController extends BaseController
{
    protected InventoryItemModel $inventoryModel;
    protected SupplierModel      $supplierModel;
    protected PurchaseOrderModel $poModel;
    protected WasteLogModel      $wasteModel;
    protected AuditLogModel      $auditModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->inventoryModel = new InventoryItemModel();
        $this->supplierModel  = new SupplierModel();
        $this->poModel        = new PurchaseOrderModel();
        $this->wasteModel     = new WasteLogModel();
        $this->auditModel     = new AuditLogModel();
    }

    /**
     * Inventory list and stock monitoring dashboard
     */
    public function index(): string
    {
        $this->authorize('inventory.view');

        $branchId     = $this->currentBranchId ?: 1;
        $categoryId   = $this->request->getGet('category_id') ? (int)$this->request->getGet('category_id') : null;
        $onlyLowStock = (bool)$this->request->getGet('low_stock');

        $items         = $this->inventoryModel->getItemsWithDetails($branchId, $categoryId, $onlyLowStock);
        $lowStockCount = $this->inventoryModel->getLowStockCount($branchId);

        $db         = \Config\Database::connect();
        $categories = $db->table('inventory_categories')->where('is_active', 1)->get()->getResultArray();
        $units      = $db->table('inventory_units')->where('is_active', 1)->get()->getResultArray();
        $suppliers  = $this->supplierModel->where('is_active', 1)->findAll();

        // Calculate total inventory valuation
        $totalValuation = 0.0;
        foreach ($items as $item) {
            $totalValuation += ((float)$item['current_stock'] * (float)$item['unit_cost']);
        }

        return $this->renderView('admin/inventory/index', [
            'pageTitle'      => 'Inventory & Stock Management',
            'title'          => 'Inventory & Stock Management',
            'active_menu'    => 'inventory',
            'items'          => $items,
            'categories'     => $categories,
            'units'          => $units,
            'suppliers'      => $suppliers,
            'lowStockCount'  => $lowStockCount,
            'totalValuation' => $totalValuation,
            'selectedCat'    => $categoryId,
            'onlyLowStock'   => $onlyLowStock
        ]);
    }

    /**
     * Create new raw inventory item
     */
    public function storeItem(): ResponseInterface
    {
        $this->authorize('inventory.create');

        $rules = [
            'name'            => 'required|min_length[2]|max_length[120]',
            'unit_id'         => 'required|is_natural_no_zero',
            'unit_cost'       => 'required|numeric',
            'current_stock'   => 'required|numeric',
            'min_stock_level' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = (string)$this->request->getPost('name');
        $sku  = (string)$this->request->getPost('sku');
        if (empty($sku)) {
            $sku = 'RAW-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 4)) . '-' . random_int(10, 99);
        }

        $branchId = $this->currentBranchId ?: 1;

        $newId = $this->inventoryModel->insert([
            'restaurant_id'     => $this->currentRestaurantId ?: 1,
            'branch_id'         => $branchId,
            'category_id'       => $this->request->getPost('category_id') ? (int)$this->request->getPost('category_id') : null,
            'unit_id'           => (int)$this->request->getPost('unit_id'),
            'supplier_id'       => $this->request->getPost('supplier_id') ? (int)$this->request->getPost('supplier_id') : null,
            'name'              => $name,
            'sku'               => $sku,
            'current_stock'     => (float)$this->request->getPost('current_stock'),
            'min_stock_level'   => (float)$this->request->getPost('min_stock_level'),
            'ideal_stock_level' => (float)($this->request->getPost('ideal_stock_level') ?? 25.0),
            'unit_cost'         => (float)$this->request->getPost('unit_cost'),
            'is_active'         => 1
        ]);

        $this->auditModel->recordEvent('inventory', 'create_item', "Added raw material item: {$name} (SKU: {$sku})", $this->currentUserId);

        return redirect()->to(site_url('admin/inventory'))->with('success', "Item '{$name}' created successfully!");
    }

    /**
     * Stock adjustment (manual in/out/reconciliation)
     */
    public function adjustStock(): ResponseInterface
    {
        $this->authorize('inventory.edit');

        $rules = [
            'item_id'  => 'required|is_natural_no_zero',
            'quantity' => 'required|numeric|greater_than[0]',
            'type'     => 'required|in_list[in,out,reconciliation]',
            'reason'   => 'required|min_length[3]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Invalid adjustment parameters.');
        }

        $itemId   = (int)$this->request->getPost('item_id');
        $quantity = (float)$this->request->getPost('quantity');
        $type     = (string)$this->request->getPost('type');
        $reason   = (string)$this->request->getPost('reason');
        $userId   = $this->currentUserId;

        $item = $this->inventoryModel->find($itemId);
        if (!$item) {
            return redirect()->back()->with('error', 'Inventory item not found.');
        }

        if ($this->currentBranchId && (int)($item['branch_id'] ?? 0) !== $this->currentBranchId) {
            return redirect()->back()->with('error', 'Unauthorized branch access to inventory item.');
        }

        $ok = $this->inventoryModel->adjustStock($itemId, $quantity, $type, $reason, $userId);
        if ($ok) {
            $this->auditModel->recordEvent('inventory', 'adjust_stock', "Stock adjustment ({$type} {$quantity}) for Item #{$itemId}: {$reason}", $userId);
            return redirect()->to(site_url('admin/inventory'))->with('success', 'Stock level adjusted successfully.');
        }

        return redirect()->to(site_url('admin/inventory'))->with('error', 'Failed to adjust stock.');
    }

    /**
     * Suppliers / Purveyors Directory
     */
    public function suppliers(): string
    {
        $this->authorize('inventory.view');

        $suppliers = $this->supplierModel->getSuppliersWithStats($this->currentRestaurantId ?: 1);

        return $this->renderView('admin/inventory/suppliers', [
            'pageTitle'   => 'Suppliers & Vendors',
            'title'       => 'Suppliers & Vendors',
            'active_menu' => 'inventory',
            'suppliers'   => $suppliers
        ]);
    }

    /**
     * Create / Store Supplier
     */
    public function storeSupplier(): ResponseInterface
    {
        $this->authorize('purchases.create');

        $rules = [
            'name'  => 'required|min_length[2]|max_length[120]',
            'phone' => 'required|min_length[5]|max_length[25]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->supplierModel->insert([
            'restaurant_id'  => $this->currentRestaurantId ?: 1,
            'name'           => (string)$this->request->getPost('name'),
            'contact_person' => (string)$this->request->getPost('contact_person'),
            'email'          => (string)$this->request->getPost('email'),
            'phone'          => (string)$this->request->getPost('phone'),
            'address'        => (string)$this->request->getPost('address'),
            'tax_number'     => (string)$this->request->getPost('tax_number'),
            'payment_terms'  => (string)($this->request->getPost('payment_terms') ?? 'Net 30'),
            'is_active'      => 1
        ]);

        return redirect()->to(site_url('admin/inventory/suppliers'))->with('success', 'Supplier registered successfully.');
    }

    /**
     * Purchase Orders (PO) Dashboard
     */
    public function purchaseOrders(): string
    {
        $this->authorize('inventory.view');

        $branchId  = $this->currentBranchId ?: 1;
        $orders    = $this->poModel->getOrdersWithDetails($branchId);
        $suppliers = $this->supplierModel->where('is_active', 1)->findAll();
        $items     = $this->inventoryModel->getItemsWithDetails($branchId);

        return $this->renderView('admin/inventory/purchase_orders', [
            'pageTitle'   => 'Purchase Orders & Procurement',
            'title'       => 'Purchase Orders & Procurement',
            'active_menu' => 'inventory',
            'orders'      => $orders,
            'suppliers'   => $suppliers,
            'items'       => $items
        ]);
    }

    /**
     * Create / Draft Purchase Order with Batch Item Insertion
     */
    public function storePo(): ResponseInterface
    {
        $this->authorize('purchases.create');

        $supplierId = (int)$this->request->getPost('supplier_id');
        $itemIds    = $this->request->getPost('items') ?? [];
        $quantities = $this->request->getPost('quantities') ?? [];
        $prices     = $this->request->getPost('prices') ?? [];

        if (!$supplierId || empty($itemIds)) {
            return redirect()->back()->with('error', 'Please select a supplier and at least one item.');
        }

        $poNumber = $this->poModel->generatePoNumber();
        $userId   = $this->currentUserId;
        $branchId = $this->currentBranchId ?: 1;

        $db = \Config\Database::connect();
        $db->transStart();

        $totalAmount = 0.0;
        $poBatchItems = [];

        foreach ($itemIds as $idx => $itemId) {
            $qty = (float)($quantities[$idx] ?? 1);
            $prc = (float)($prices[$idx] ?? 0);
            $lineTotal = round($qty * $prc, 2);
            $totalAmount += $lineTotal;

            $poBatchItems[] = [
                'inventory_item_id' => (int)$itemId,
                'quantity_ordered'  => $qty,
                'quantity_received' => 0.0,
                'unit_price'        => $prc,
                'total_price'       => $lineTotal
            ];
        }

        $poId = $this->poModel->insert([
            'po_number'     => $poNumber,
            'restaurant_id' => $this->currentRestaurantId ?: 1,
            'branch_id'     => $branchId,
            'supplier_id'   => $supplierId,
            'order_date'    => date('Y-m-d'),
            'expected_date' => date('Y-m-d', strtotime('+3 days')),
            'status'        => 'sent',
            'total_amount'  => $totalAmount,
            'notes'         => (string)$this->request->getPost('notes'),
            'created_by'    => $userId
        ]);

        // Efficient single batch insert eliminating N individual queries
        foreach ($poBatchItems as &$item) {
            $item['purchase_order_id'] = $poId;
        }
        unset($item);

        if (!empty($poBatchItems)) {
            $db->table('purchase_order_items')->insertBatch($poBatchItems);
        }

        $db->transComplete();

        if ($db->transStatus()) {
            $this->auditModel->recordEvent('procurement', 'create_po', "Generated Purchase Order #{$poNumber} for \${$totalAmount}", $userId);
            return redirect()->to(site_url('admin/inventory/purchase-orders'))->with('success', "Purchase Order {$poNumber} created and dispatched!");
        }

        return redirect()->back()->with('error', 'Failed to generate Purchase Order.');
    }

    /**
     * Receive PO items into stock
     */
    public function receivePo(int $id): ResponseInterface
    {
        $this->authorize('inventory.edit');

        $userId = $this->currentUserId;
        $ok     = $this->poModel->receiveOrder($id, $userId);

        if ($ok) {
            $this->auditModel->recordEvent('procurement', 'receive_po', "Received PO #{$id} and credited stock levels with verified GRN", $userId);
            return redirect()->to(site_url('admin/inventory/purchase-orders'))->with('success', "PO #{$id} received! Stock levels have been automatically updated.");
        }

        return redirect()->back()->with('error', "Could not receive PO #{$id}. It may already be completed.");
    }

    /**
     * Spoilage & Wastage Log
     */
    public function waste(): string
    {
        $this->authorize('inventory.view');

        $branchId    = $this->currentBranchId ?: 1;
        $reason      = $this->request->getGet('reason') ? (string)$this->request->getGet('reason') : null;

        $logs        = $this->wasteModel->getLogsWithDetails($branchId, $reason);
        $monthlyLoss = $this->wasteModel->getMonthlyLoss($branchId);
        $items       = $this->inventoryModel->getItemsWithDetails($branchId);

        return $this->renderView('admin/inventory/waste', [
            'pageTitle'   => 'Wastage & Spoilage Log',
            'title'       => 'Wastage & Spoilage Log',
            'active_menu' => 'inventory',
            'logs'        => $logs,
            'monthlyLoss' => $monthlyLoss,
            'items'       => $items,
            'selectedRsn' => $reason
        ]);
    }

    /**
     * Record Spoilage Entry
     */
    public function storeWaste(): ResponseInterface
    {
        if (!$this->can('waste.create') && !$this->can('inventory.adjust') && !$this->can('inventory.view')) {
            $this->authorize('waste.create');
        }

        $rules = [
            'inventory_item_id' => 'required|is_natural_no_zero',
            'quantity'          => 'required|numeric|greater_than[0]',
            'waste_reason'      => 'required|in_list[spoilage,expired,damaged,burnt,prep_error,other]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Invalid waste logging fields.');
        }

        $itemId   = (int)$this->request->getPost('inventory_item_id');
        $quantity = (float)$this->request->getPost('quantity');
        $reason   = (string)$this->request->getPost('waste_reason');
        $notes    = (string)$this->request->getPost('notes');
        $userId   = $this->currentUserId;

        $item = $this->inventoryModel->find($itemId);
        if (!$item) {
            return redirect()->back()->with('error', 'Inventory item not found.');
        }

        if ($this->currentBranchId && (int)($item['branch_id'] ?? 0) !== $this->currentBranchId) {
            return redirect()->back()->with('error', 'Unauthorized branch access to inventory item.');
        }

        $ok = $this->wasteModel->recordWaste($itemId, $quantity, $reason, $notes, $userId);
        if ($ok) {
            $this->auditModel->recordEvent('inventory', 'log_waste', "Logged {$quantity} units waste ({$reason}) on Item #{$itemId}", $userId);
            return redirect()->to(site_url('admin/inventory/waste'))->with('success', 'Wastage incident logged and stock deducted.');
        }

        return redirect()->back()->with('error', 'Failed to record wastage.');
    }

    /**
     * Recipes & Bill of Materials (BOM)
     */
    public function recipes(): string
    {
        $this->authorize('inventory.view');

        $db = \Config\Database::connect();

        // Ensure unit_cost column exists in item_recipes
        try {
            $fields = $db->getFieldNames('item_recipes');
            if (!in_array('unit_cost', $fields, true)) {
                $db->query("ALTER TABLE item_recipes ADD COLUMN unit_cost DECIMAL(10,2) DEFAULT NULL AFTER unit_id");
            }
        } catch (\Throwable $e) {
            // Non-blocking
        }

        $menuItems = $db->table('menu_items')
            ->select('id, name, price')
            ->where('is_available', 1)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $branchId = $this->currentBranchId ?: 1;
        $rawItems = $this->inventoryModel->getItemsWithDetails($branchId);
        $units    = $db->table('inventory_units')->where('is_active', 1)->get()->getResultArray();

        // Get all recipe BOM mappings with custom or ingredient unit_cost
        $recipes = $db->table('item_recipes r')
            ->select('r.*, m.name as menu_item_name, m.price as menu_item_price, i.name as raw_item_name, COALESCE(r.unit_cost, i.unit_cost) as unit_cost, u.short_code as unit_code, u.name as unit_name')
            ->join('menu_items m', 'm.id = r.menu_item_id')
            ->join('inventory_items i', 'i.id = r.inventory_item_id')
            ->join('inventory_units u', 'u.id = r.unit_id')
            ->orderBy('m.name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->renderView('admin/inventory/recipes', [
            'pageTitle'   => 'Recipe Engineering & BOM',
            'title'       => 'Recipe Engineering & BOM',
            'active_menu' => 'inventory',
            'recipes'     => $recipes,
            'menuItems'   => $menuItems,
            'rawItems'    => $rawItems,
            'units'       => $units
        ]);
    }

    /**
     * Save Recipe Ingredient Mapping (with Manual Price / Cost Support)
     */
    public function storeRecipe(): ResponseInterface
    {
        if (!$this->can('menu_items.edit') && !$this->can('inventory.adjust') && !$this->can('inventory.view')) {
            $this->authorize('inventory.view');
        }

        $menuId   = (int)$this->request->getPost('menu_item_id');
        $invId    = (int)$this->request->getPost('inventory_item_id');
        $qty      = (float)$this->request->getPost('quantity_required');
        $unitId   = (int)$this->request->getPost('unit_id');
        $unitCost = $this->request->getPost('unit_cost') !== null ? (float)$this->request->getPost('unit_cost') : null;

        if (!$menuId || !$invId || $qty <= 0) {
            return redirect()->back()->with('error', 'Please fill all recipe mapping fields.');
        }

        $db = \Config\Database::connect();

        // Ensure unit_cost column exists in item_recipes
        try {
            $fields = $db->getFieldNames('item_recipes');
            if (!in_array('unit_cost', $fields, true)) {
                $db->query("ALTER TABLE item_recipes ADD COLUMN unit_cost DECIMAL(10,2) DEFAULT NULL AFTER unit_id");
            }
        } catch (\Throwable $e) {
            // Non-blocking
        }

        $data = [
            'menu_item_id'      => $menuId,
            'inventory_item_id' => $invId,
            'quantity_required' => $qty,
            'unit_id'           => $unitId,
        ];

        if ($unitCost !== null && $unitCost >= 0) {
            $data['unit_cost'] = $unitCost;
            // Also update the inventory item's unit_cost so the new manual price is reflected across inventory
            try {
                $this->inventoryModel->update($invId, ['unit_cost' => $unitCost]);
            } catch (\Throwable $e) {
                // Non-blocking
            }
        }

        $db->table('item_recipes')->replace($data);

        return redirect()->to(site_url('admin/inventory/recipes'))->with('success', 'Recipe ingredient mapping and raw material price saved successfully!');
    }

    /**
     * Delete Recipe Ingredient Mapping
     */
    public function deleteRecipe(): ResponseInterface
    {
        $this->authorize('inventory.view');

        $menuId = (int)$this->request->getPost('menu_item_id');
        $invId  = (int)$this->request->getPost('inventory_item_id');

        if ($menuId && $invId) {
            $db = \Config\Database::connect();
            $db->table('item_recipes')
                ->where('menu_item_id', $menuId)
                ->where('inventory_item_id', $invId)
                ->delete();
            return redirect()->to(site_url('admin/inventory/recipes'))->with('success', 'Recipe component mapping removed.');
        }

        return redirect()->back()->with('error', 'Invalid recipe component parameters.');
    }

    /**
     * Update Raw Material Item (including unit cost / price)
     */
    public function updateItem(int $id): ResponseInterface
    {
        $this->authorize('inventory.view');

        $item = $this->inventoryModel->find($id);
        if (!$item) {
            return redirect()->back()->with('error', 'Raw material item not found.');
        }

        $data = [
            'name'            => (string)$this->request->getPost('name'),
            'category_id'     => $this->request->getPost('category_id') ? (int)$this->request->getPost('category_id') : null,
            'unit_id'         => (int)$this->request->getPost('unit_id'),
            'min_stock_level' => (float)$this->request->getPost('min_stock_level'),
            'unit_cost'       => (float)$this->request->getPost('unit_cost'),
            'supplier_id'     => $this->request->getPost('supplier_id') ? (int)$this->request->getPost('supplier_id') : null,
        ];

        $this->inventoryModel->update($id, $data);

        return redirect()->to(site_url('admin/inventory'))->with('success', 'Raw material item and unit price updated.');
    }
}
