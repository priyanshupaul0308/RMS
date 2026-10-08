<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CouponUsageModel - Tracks individual coupon redemptions
 */
class CouponUsageModel extends BaseModel
{
    protected $table            = 'coupon_usages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'coupon_id',
        'order_id',
        'customer_id',
        'discount_amount',
        'used_at',
    ];

    /**
     * Record a coupon redemption.
     */
    public function recordRedemption(int $couponId, int $orderId, ?int $customerId, float $discount): int
    {
        $id = (int) $this->insert([
            'coupon_id'       => $couponId,
            'order_id'        => $orderId,
            'customer_id'     => $customerId,
            'discount_amount' => $discount,
            'used_at'         => date('Y-m-d H:i:s'),
        ]);

        // Increment times_used on coupon
        $this->db->table('coupons')->where('id', $couponId)->increment('times_used');

        return $id;
    }
}
