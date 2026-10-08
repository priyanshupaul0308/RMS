<?php

declare(strict_types=1);

namespace App\Models;

/**
 * KotModel – Kitchen Order Ticket generator, prep timing, and KDS integration.
 */
class KotModel extends BaseModel
{
    protected $table         = 'kots';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'order_id', 'branch_id', 'kot_number', 'status', 'station',
        'printed_at', 'cooking_started_at', 'ready_at', 'bumped_by', 'reprint_count',
    ];
    protected $useTimestamps = true;

    /**
     * Generate sequential KOT number.
     *
     * @return string
     */
    public function generateKotNumber(): string
    {
        $countToday = $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        $seq = str_pad((string)($countToday + 1), 3, '0', STR_PAD_LEFT);
        return "KOT-" . date('His') . "-{$seq}";
    }

    /**
     * Get active KOTs for real-time Kitchen Display System (KDS).
     *
     * @param string|null $station
     * @param int|null $branchId
     * @return array<int, array<string, mixed>>
     */
    public function getActiveKotsForKds(?string $station = null, ?int $branchId = null): array
    {
        $builder = $this->select('kots.*, orders.order_number, orders.order_type, orders.customer_name, restaurant_tables.table_number, floors.name AS floor_name, users.first_name AS waiter_name')
            ->join('orders',            'orders.id = kots.order_id', 'left')
            ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left')
            ->join('floors',            'floors.id = restaurant_tables.floor_id', 'left')
            ->join('users',             'users.id = orders.waiter_id', 'left')
            ->whereIn('kots.status', ['sent', 'preparing']);

        if ($branchId !== null) {
            $builder->where('kots.branch_id', $branchId);
        }

        if (!empty($station) && $station !== 'all') {
            $builder->where('kots.station', $station);
        }

        $kots = $builder->orderBy('kots.id', 'ASC')->findAll();
        if (empty($kots)) {
            return [];
        }

        // Batch eager-load items for all active KOT orders (eliminates N+1 query problem)
        $orderIds = array_unique(array_filter(array_column($kots, 'order_id')));
        $itemsByOrder = [];
        if (!empty($orderIds)) {
            $itemModel = new OrderItemModel();
            $allItems  = $itemModel->whereIn('order_id', $orderIds)->findAll();
            foreach ($allItems as $it) {
                $itemsByOrder[$it['order_id']][] = $it;
            }
        }

        $now = time();

        foreach ($kots as &$kot) {
            // Calculate elapsed minutes
            $createdTs = strtotime($kot['created_at']);
            $elapsedSec = max(0, $now - $createdTs);
            $kot['elapsed_minutes'] = (int) floor($elapsedSec / 60);
            $kot['elapsed_seconds'] = $elapsedSec % 60;
            $kot['elapsed_display'] = sprintf('%02d:%02d', $kot['elapsed_minutes'], $kot['elapsed_seconds']);

            // Urgency color: normal (< 10m), warning (10-20m), urgent (> 20m)
            if ($kot['elapsed_minutes'] >= 20) {
                $kot['urgency'] = 'urgent';
            } elseif ($kot['elapsed_minutes'] >= 10) {
                $kot['urgency'] = 'warning';
            } else {
                $kot['urgency'] = 'normal';
            }

            // Assign batch-loaded items
            $kot['items'] = $itemsByOrder[$kot['order_id']] ?? [];
        }

        return $kots;
    }

    /**
     * Start preparation timer on a KOT.
     *
     * @param int $kotId
     * @return bool
     */
    public function startCooking(int $kotId): bool
    {
        $kot = $this->find($kotId);
        if (!$kot) return false;

        $this->update($kotId, [
            'status'             => 'preparing',
            'cooking_started_at' => date('Y-m-d H:i:s'),
        ]);

        $orderModel = new OrderModel();
        $orderModel->update((int)$kot['order_id'], ['status' => 'preparing']);

        return true;
    }

    /**
     * Bump / Mark KOT ready in the kitchen.
     *
     * @param int $kotId
     * @param int|null $userId
     * @return bool
     */
    public function bumpKot(int $kotId, ?int $userId = null): bool
    {
        $kot = $this->find($kotId);
        if (!$kot) return false;

        $this->update($kotId, [
            'status'     => 'completed',
            'ready_at'   => date('Y-m-d H:i:s'),
            'bumped_by'  => $userId,
        ]);

        // Advance parent order status to ready so waiters can serve
        $orderModel = new OrderModel();
        $orderModel->update((int)$kot['order_id'], ['status' => 'ready']);

        return true;
    }
}
