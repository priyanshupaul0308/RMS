<?php

declare(strict_types=1);

namespace App\Models;

/**
 * TableModel – Restaurant table layout, seating capacity, and real-time status.
 */
class TableModel extends BaseModel
{
    protected $table         = 'restaurant_tables';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'floor_id', 'table_number',
        'seating_capacity', 'shape', 'status', 'current_order_id',
        'is_active', 'sort_order',
    ];
    protected $useTimestamps = true;

    /**
     * Get tables by floor.
     *
     * @param int $floorId
     * @return array<int, array<string, mixed>>
     */
    public function getTablesByFloor(int $floorId): array
    {
        return $this->select('restaurant_tables.*, orders.order_number, orders.final_total')
            ->join('orders', 'orders.id = restaurant_tables.current_order_id', 'left')
            ->where('restaurant_tables.floor_id', $floorId)
            ->where('restaurant_tables.is_active', 1)
            ->orderBy('restaurant_tables.table_number', 'ASC')
            ->findAll();
    }

    /**
     * Update table status and active order.
     *
     * @param int $tableId
     * @param string $status 'available'|'occupied'|'reserved'|'dirty'
     * @param int|null $orderId
     * @return bool
     */
    public function updateStatus(int $tableId, string $status, ?int $orderId = null): bool
    {
        return (bool) $this->update($tableId, [
            'status'           => $status,
            'current_order_id' => $status === 'available' ? null : $orderId,
        ]);
    }
}
