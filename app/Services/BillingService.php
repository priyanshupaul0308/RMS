<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderModel;
use App\Models\TableModel;
use App\Models\AuditLogModel;
use CodeIgniter\Database\BaseConnection;

/**
 * BillingService
 * 
 * Handles multi-tender payments, shift drawer cash reconciliation,
 * customer lifetime spend synchronization, and loyalty points accrual.
 */
class BillingService
{
    protected BaseConnection $db;
    protected OrderModel      $orderModel;
    protected TableModel      $tableModel;
    protected AuditLogModel   $auditModel;

    public function __construct()
    {
        $this->db         = \Config\Database::connect();
        $this->orderModel = new OrderModel();
        $this->tableModel = new TableModel();
        $this->auditModel = new AuditLogModel();
    }

    /**
     * Settle an order with split or single payment tenders.
     *
     * @param int                  $orderId Target order
     * @param array<int, array>    $payments Array of tenders: [['payment_method_id' => 1, 'amount' => 50.0, 'reference_number' => '...']]
     * @param int                  $userId Staff cashier processing settlement
     * @param int                  $branchId Operational branch
     * @return array<string, mixed> Settlement confirmation
     * @throws \RuntimeException
     */
    public function settleOrder(int $orderId, array $payments, int $userId, int $branchId, array $discountData = []): array
    {
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            throw new \InvalidArgumentException("Order #{$orderId} not found.");
        }

        if ($order['payment_status'] === 'paid') {
            throw new \InvalidArgumentException("Order #{$order['order_number']} is already paid.");
        }

        $discountAmount = max(0.0, (float)($discountData['discount_amount'] ?? 0));
        $couponId       = !empty($discountData['coupon_id']) ? (int)$discountData['coupon_id'] : null;
        $couponCode     = !empty($discountData['coupon_code']) ? trim((string)$discountData['coupon_code']) : null;
        $discountReason = !empty($discountData['discount_reason']) ? trim((string)$discountData['discount_reason']) : null;

        // If couponCode provided but couponId missing, look it up
        if (!$couponId && !empty($couponCode)) {
            $cpRow = $this->db->table('coupons')->where('code', strtoupper($couponCode))->get()->getRowArray();
            if ($cpRow) {
                $couponId = (int)$cpRow['id'];
            }
        }

        // Base total and net final total after discount
        $baseTotal  = (float)$order['final_total'];
        $finalTotal = max(0.0, $baseTotal - $discountAmount);
        $totalPaid  = 0.0;
        $cashTenderedTotal = 0.0;

        foreach ($payments as $p) {
            $amt = (float)($p['amount'] ?? 0);
            $totalPaid += $amt;
        }

        if ($totalPaid < ($finalTotal - 0.05)) { // Allow minor rounding variance
            throw new \InvalidArgumentException(sprintf("Total payment (₹%.2f) does not cover the final bill (₹%.2f).", $totalPaid, $finalTotal));
        }

        $this->db->transStart();

        // 1. Record Each Payment Tender
        foreach ($payments as $p) {
            $methodId = (int)($p['payment_method_id'] ?? 1);
            $amount   = (float)($p['amount'] ?? 0);
            $refNo    = !empty($p['reference_number']) ? (string)$p['reference_number'] : null;

            $this->db->table('order_payments')->insert([
                'order_id'          => $orderId,
                'payment_method_id' => $methodId,
                'amount'            => $amount,
                'reference_number'  => $refNo,
                'received_by'       => $userId,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);

            // Check if Cash tender (method 1 is Cash in payment_methods)
            $method = $this->db->table('payment_methods')->where('id', $methodId)->get()->getRowArray();
            if ($method && (strtolower($method['slug'] ?? '') === 'cash' || strtolower($method['name'] ?? '') === 'cash')) {
                $cashTenderedTotal += $amount;
            }
        }

        // 2. Mark Order Completed & Paid (storing discount and updated final_total)
        $primaryMethodId = (int)($payments[0]['payment_method_id'] ?? 1);
        $orderUpdate = [
            'status'            => 'completed',
            'payment_status'    => 'paid',
            'payment_method_id' => $primaryMethodId,
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        if ($discountAmount > 0) {
            $orderUpdate['discount_amount'] = $discountAmount;
            $orderUpdate['final_total']     = $finalTotal;
            if ($couponId) {
                $orderUpdate['coupon_id'] = $couponId;
            }
            if ($discountReason) {
                $orderUpdate['discount_reason'] = $discountReason;
            }
        }
        $this->orderModel->update($orderId, $orderUpdate);

        // 3. Record Coupon Redemption if applicable
        if ($couponId && $discountAmount > 0) {
            $customerId = null;
            if (!empty($order['customer_phone'])) {
                $cust = $this->db->table('customers')->where('phone', $order['customer_phone'])->get()->getRowArray();
                if ($cust) {
                    $customerId = (int)$cust['id'];
                }
            }
            $usageModel = new \App\Models\CouponUsageModel();
            $usageModel->recordRedemption($couponId, $orderId, $customerId, $discountAmount);
        }

        // 4. Sync Cash Drawer Shift (if active register open)
        if ($cashTenderedTotal > 0) {
            $this->db->table('cash_registers')
                ->where('branch_id', $branchId)
                ->where('status', 'open')
                ->set('expected_cash', "COALESCE(expected_cash, opening_float) + {$cashTenderedTotal}", false)
                ->update();
        }

        // 4. Release Table to Dirty for Busser Staff
        if (!empty($order['table_id'])) {
            $this->tableModel->updateStatus((int)$order['table_id'], 'dirty', null);
        }

        // 5. Update CRM Customer Lifetime Spend & Loyalty Points (Redemption + Accrual)
        if (!empty($order['customer_phone'])) {
            $customerPhone  = trim((string)$order['customer_phone']);
            $redeemedPoints = max(0, (int)($discountData['redeemed_points'] ?? 0));
            $pointsEarned   = (int)floor($finalTotal / 10); // 1 pt per ₹10 spent
            $customer       = $this->db->table('customers')->where('phone', $customerPhone)->get()->getRowArray();

            if ($customer) {
                $customerId    = (int)$customer['id'];
                $currentPoints = (int)$customer['loyalty_points'];
                $actualRedeemed = min($currentPoints, $redeemedPoints);
                $balanceAfterRedeem = max(0, $currentPoints - $actualRedeemed);
                $finalPointsBalance = $balanceAfterRedeem + $pointsEarned;
                $newLifetimeSpend   = (float)$customer['lifetime_spend'] + $finalTotal;

                $this->db->table('customers')
                    ->where('id', $customerId)
                    ->update([
                        'total_visits'   => (int)$customer['total_visits'] + 1,
                        'lifetime_spend' => $newLifetimeSpend,
                        'loyalty_points' => $finalPointsBalance,
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);

                // Record redemption transaction log if points redeemed
                if ($actualRedeemed > 0) {
                    $this->db->table('loyalty_transactions')->insert([
                        'customer_id'     => $customerId,
                        'order_id'        => $orderId,
                        'points_earned'   => 0,
                        'points_redeemed' => $actualRedeemed,
                        'balance_after'   => $balanceAfterRedeem,
                        'description'     => "Redeemed {$actualRedeemed} pts (₹" . number_format((float)$actualRedeemed, 2) . ") for Order #{$order['order_number']}",
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);
                }

                // Record points earned transaction log
                if ($pointsEarned > 0) {
                    $this->db->table('loyalty_transactions')->insert([
                        'customer_id'     => $customerId,
                        'order_id'        => $orderId,
                        'points_earned'   => $pointsEarned,
                        'points_redeemed' => 0,
                        'balance_after'   => $finalPointsBalance,
                        'description'     => "Dining reward for Order #{$order['order_number']}",
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);
                }
            } else {
                // Auto-create new customer in CRM directory so their loyalty & visits are tracked
                $custName = (!empty($order['customer_name']) && $order['customer_name'] !== 'Walk-in Guest')
                    ? trim((string)$order['customer_name'])
                    : 'Customer ' . substr($customerPhone, -4);

                $this->db->table('customers')->insert([
                    'restaurant_id'  => (int)($order['restaurant_id'] ?? 1),
                    'name'           => $custName,
                    'phone'          => $customerPhone,
                    'loyalty_points' => $pointsEarned,
                    'total_visits'   => 1,
                    'lifetime_spend' => $finalTotal,
                    'vip_status'     => 0,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
                $newCustId = (int)$this->db->insertID();

                if ($pointsEarned > 0 && $newCustId > 0) {
                    $this->db->table('loyalty_transactions')->insert([
                        'customer_id'     => $newCustId,
                        'order_id'        => $orderId,
                        'points_earned'   => $pointsEarned,
                        'points_redeemed' => 0,
                        'balance_after'   => $pointsEarned,
                        'description'     => "Welcome dining reward for Order #{$order['order_number']}",
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Failed to settle order due to database error.');
        }

        // 6. Record Audit Trail
        $this->auditModel->recordEvent(
            'billing',
            'settle_order',
            "Settled Order #{$order['order_number']} for ₹{$finalTotal} across " . count($payments) . " tender(s)",
            $userId,
            'orders',
            $orderId
        );

        return [
            'order_id'     => $orderId,
            'order_number' => $order['order_number'],
            'total_paid'   => $totalPaid,
            'tenders'      => count($payments),
        ];
    }
}
