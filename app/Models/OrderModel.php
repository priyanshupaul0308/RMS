<?php

declare(strict_types=1);

namespace App\Models;

/**
 * OrderModel – Core order processing engine for Dine-In, Takeaway & Delivery.
 */
class OrderModel extends BaseModel
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'order_number', 'order_type',
        'table_id', 'customer_name', 'customer_phone', 'customer_address',
        'waiter_id', 'cashier_id', 'status', 'subtotal', 'tax_amount',
        'discount_amount', 'coupon_id', 'discount_reason', 'service_charge', 'final_total', 'payment_status',
        'payment_method_id', 'notes',
    ];
    protected $useTimestamps = true;

    /**
     * Generate unique human-readable order number.
     *
     * @return string
     */
    public function generateOrderNumber(): string
    {
        $datePrefix = date('Ymd');
        $countToday = $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        $seq = str_pad((string)($countToday + 1), 4, '0', STR_PAD_LEFT);
        return "ORD-{$datePrefix}-{$seq}";
    }

    /**
     * Get order details with table, branch, and items.
     *
     * @param int $orderId
     * @return array<string, mixed>|null
     */
    public function getOrderWithDetails(int $orderId): ?array
    {
        $order = $this->select('orders.*, restaurant_tables.table_number, floors.name AS floor_name, users.first_name AS waiter_name, payment_methods.name AS payment_method_name')
            ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left')
            ->join('floors',            'floors.id = restaurant_tables.floor_id', 'left')
            ->join('users',             'users.id = orders.waiter_id', 'left')
            ->join('payment_methods',   'payment_methods.id = orders.payment_method_id', 'left')
            ->where('orders.id', $orderId)
            ->first();

        if ($order === null) {
            return null;
        }

        // If customer_name is empty or generic, auto-resolve from reservation on this table
        if ((empty($order['customer_name']) || $order['customer_name'] === 'Walk-in Guest') && !empty($order['table_id'])) {
            $orderDate = !empty($order['created_at']) ? date('Y-m-d', strtotime($order['created_at'])) : date('Y-m-d');
            $res = $this->db->table('reservations')
                ->where('table_id', (int) $order['table_id'])
                ->where('reservation_date', $orderDate)
                ->whereIn('status', ['seated', 'confirmed', 'completed'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($res && !empty($res['customer_name'])) {
                $order['customer_name'] = $res['customer_name'];
                if (empty($order['customer_phone']) && !empty($res['customer_phone'])) {
                    $order['customer_phone'] = $res['customer_phone'];
                }
                // Persist back to database orders table
                try {
                    $this->update($orderId, [
                        'customer_name'  => $res['customer_name'],
                        'customer_phone' => $res['customer_phone'] ?? null,
                    ]);
                } catch (\Throwable $e) {
                    // Non-blocking
                }
            }
        }

        $itemModel = new OrderItemModel();
        $order['items'] = $itemModel->where('order_id', $orderId)->findAll();

        return $order;
    }

    /**
     * Get list of live orders with filters.
     *
     * @param int|null $branchId
     * @param string|null $status
     * @param string|null $type
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $paymentStatus
     * @param string|null $search
     * @return array<int, array<string, mixed>>
     */
    public function getLiveOrders(
        ?int $branchId = null,
        ?string $status = null,
        ?string $type = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $paymentStatus = null,
        ?string $search = null
    ): array {
        $builder = $this->select('orders.*, restaurant_tables.table_number, floors.name AS floor_name, COUNT(order_items.id) AS total_items, payment_methods.name AS payment_method_name')
            ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left')
            ->join('floors',            'floors.id = restaurant_tables.floor_id', 'left')
            ->join('payment_methods',   'payment_methods.id = orders.payment_method_id', 'left')
            ->join('order_items',       'order_items.order_id = orders.id', 'left');

        if ($branchId !== null) {
            $builder->where('orders.branch_id', $branchId);
        }

        if (!empty($status) && $status !== 'all') {
            $builder->where('orders.status', $status);
        }

        if (!empty($type) && $type !== 'all') {
            $builder->where('orders.order_type', $type);
        }

        if (!empty($paymentStatus) && $paymentStatus !== 'all') {
            $builder->where('orders.payment_status', $paymentStatus);
        }

        if (!empty($startDate)) {
            $builder->where('DATE(orders.created_at) >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('DATE(orders.created_at) <=', $endDate);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('orders.order_number', $search)
                ->orLike('orders.customer_name', $search)
                ->orLike('orders.customer_phone', $search)
                ->orLike('restaurant_tables.table_number', $search)
                ->groupEnd();
        }

        return $builder->groupBy('orders.id')
            ->orderBy('orders.id', 'DESC')
            ->findAll();
    }
}
