<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CouponModel - Discount codes, limits, percentages, and validity rules
 */
class CouponModel extends BaseModel
{
    protected $table            = 'coupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'code',
        'name',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit_total',
        'usage_limit_per_user',
        'times_used',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $validationRules = [
        'code'  => 'required|min_length[3]|max_length[40]',
        'name'  => 'required|min_length[3]|max_length[120]',
        'type'  => 'required|in_list[percentage,fixed]',
        'value' => 'required|numeric|greater_than[0]',
    ];

    /**
     * Validate a coupon against an order amount and optional user.
     *
     * @return array{valid: bool, message: string, discount: float, coupon: ?array<string, mixed>}
     */
    public function validateCoupon(string $code, float $orderAmount, ?int $customerId = null): array
    {
        $code = strtoupper(trim($code));
        $coupon = $this->where('code', $code)->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Coupon code not found.', 'discount' => 0.0, 'coupon' => null];
        }

        if ((int) $coupon['is_active'] !== 1) {
            return ['valid' => false, 'message' => 'This coupon is no longer active.', 'discount' => 0.0, 'coupon' => null];
        }

        $today = date('Y-m-d');
        if ($today < $coupon['start_date']) {
            return ['valid' => false, 'message' => 'This coupon is not valid yet.', 'discount' => 0.0, 'coupon' => null];
        }

        if ($today > $coupon['end_date']) {
            return ['valid' => false, 'message' => 'This coupon has expired.', 'discount' => 0.0, 'coupon' => null];
        }

        if ($coupon['usage_limit_total'] > 0 && $coupon['times_used'] >= $coupon['usage_limit_total']) {
            return ['valid' => false, 'message' => 'This coupon has reached its total usage limit.', 'discount' => 0.0, 'coupon' => null];
        }

        if ($orderAmount < (float) $coupon['min_order_amount']) {
            $minFormatted = number_format((float) $coupon['min_order_amount'], 2);
            return [
                'valid'   => false,
                'message' => "Minimum order amount of ₹{$minFormatted} required for this coupon.",
                'discount' => 0.0,
                'coupon'  => null,
            ];
        }

        if ($customerId !== null && (int) $coupon['usage_limit_per_user'] > 0) {
            $userUses = (new CouponUsageModel())->where('coupon_id', $coupon['id'])
                ->where('customer_id', $customerId)
                ->countAllResults();
            if ($userUses >= (int) $coupon['usage_limit_per_user']) {
                return ['valid' => false, 'message' => 'You have already reached the maximum usage limit for this coupon.', 'discount' => 0.0, 'coupon' => null];
            }
        }

        // Calculate discount
        $discount = 0.0;
        if ($coupon['type'] === 'percentage') {
            $discount = ($orderAmount * (float) $coupon['value']) / 100.0;
            if (!empty($coupon['max_discount_amount']) && (float) $coupon['max_discount_amount'] > 0) {
                $discount = min($discount, (float) $coupon['max_discount_amount']);
            }
        } else {
            $discount = min((float) $coupon['value'], $orderAmount);
        }

        $discount = round($discount, 2);

        return [
            'valid'    => true,
            'message'  => "Coupon applied! ₹" . number_format($discount, 2) . " discount.",
            'discount' => $discount,
            'coupon'   => $coupon,
        ];
    }
}
