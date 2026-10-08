<?php

declare(strict_types=1);

namespace App\Models;

/**
 * DeliveryOrderModel - Order assignment, tracking, and delivery confirmation
 */
class DeliveryOrderModel extends BaseModel
{
    protected $table            = 'delivery_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'order_id',
        'partner_id',
        'branch_id',
        'assigned_at',
        'picked_up_at',
        'delivered_at',
        'status',
        'delivery_fee',
        'tip_amount',
        'customer_rating',
        'delivery_notes',
    ];

    /**
     * Assign order to a delivery partner.
     */
    public function assignOrder(int $orderId, int $partnerId, int $branchId, float $deliveryFee = 0.0, ?string $notes = null): int
    {
        $id = (int) $this->insert([
            'order_id'       => $orderId,
            'partner_id'     => $partnerId,
            'branch_id'      => $branchId,
            'assigned_at'    => date('Y-m-d H:i:s'),
            'status'         => 'assigned',
            'delivery_fee'   => $deliveryFee,
            'delivery_notes' => $notes,
        ]);

        // Update partner status to on_delivery
        $this->db->table('delivery_partners')
            ->where('id', $partnerId)
            ->update(['availability_status' => 'on_delivery']);

        return $id;
    }

    /**
     * Update delivery progress status.
     */
    public function updateDeliveryStatus(int $assignmentId, string $status, ?int $rating = null): bool
    {
        $now = date('Y-m-d H:i:s');
        $data = ['status' => $status];

        if ($status === 'picked_up') {
            $data['picked_up_at'] = $now;
        } elseif ($status === 'delivered') {
            $data['delivered_at'] = $now;
            if ($rating !== null) {
                $data['customer_rating'] = $rating;
            }
        }

        $res = $this->update($assignmentId, $data);

        // If delivered or failed, free up the partner and increment total deliveries
        if ($res && in_array($status, ['delivered', 'failed'], true)) {
            $assignment = $this->find($assignmentId);
            if ($assignment) {
                $partnerId = (int) $assignment['partner_id'];
                $this->db->table('delivery_partners')
                    ->where('id', $partnerId)
                    ->update([
                        'availability_status' => 'available',
                    ]);

                if ($status === 'delivered') {
                    $this->db->table('delivery_partners')
                        ->where('id', $partnerId)
                        ->increment('total_deliveries');
                }
            }
        }

        return (bool) $res;
    }

    /**
     * Get active deliveries with partner & order details.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDeliveries(int $branchId = 1, ?string $status = null): array
    {
        $builder = $this->select('delivery_orders.*, delivery_partners.name as partner_name, delivery_partners.phone as partner_phone, delivery_partners.vehicle_type, delivery_partners.vehicle_number, orders.order_number, orders.final_total as total_amount, orders.customer_name, orders.customer_phone, orders.customer_address as delivery_address')
            ->join('delivery_partners', 'delivery_partners.id = delivery_orders.partner_id')
            ->join('orders', 'orders.id = delivery_orders.order_id')
            ->where('delivery_orders.branch_id', $branchId);

        if ($status) {
            $builder->where('delivery_orders.status', $status);
        }

        return $builder->orderBy('delivery_orders.id', 'DESC')->findAll();
    }
}
