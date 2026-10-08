<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CashRegisterModel – Cash drawer shift management, float tracking, and end-of-day reconciliation.
 */
class CashRegisterModel extends BaseModel
{
    protected $table         = 'cash_registers';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'user_id', 'opened_at', 'closed_at',
        'opening_float', 'closing_cash_counted', 'expected_cash',
        'discrepancy', 'status', 'notes',
    ];
    protected $useTimestamps = true;

    /**
     * Get active/open register for the user/branch.
     *
     * @param int $userId
     * @param int|null $branchId
     * @return array<string, mixed>|null
     */
    public function getOpenRegister(int $userId, ?int $branchId = null): ?array
    {
        $builder = $this->where('status', 'open');
        if ($branchId !== null) {
            $builder->where('branch_id', $branchId);
        }
        return $builder->orderBy('id', 'DESC')->first();
    }

    /**
     * Calculate live shift summary (Sales, Cash-ins, Cash-outs, Expected Cash).
     *
     * @param int $registerId
     * @return array<string, mixed>
     */
    public function getShiftSummary(int $registerId): array
    {
        $register = $this->find($registerId);
        if (!$register) {
            return [];
        }

        $db = db_connect();
        $openedAt = $register['opened_at'];
        $closedAt = !empty($register['closed_at']) ? $register['closed_at'] : null;
        $branchId = (int)($register['branch_id'] ?? 1);

        $cashSales = 0.0;
        $digitalSales = 0.0;

        // 1. Calculate from order_payments table (explicit tender entries)
        $tenderPaymentsQuery = $db->table('order_payments op')
            ->select('op.order_id, op.payment_method_id, op.amount, pm.slug as method_slug, pm.name as method_name')
            ->join('orders o', 'o.id = op.order_id', 'left')
            ->join('payment_methods pm', 'pm.id = op.payment_method_id', 'left')
            ->where('op.created_at >=', $openedAt)
            ->groupStart()
                ->where('o.branch_id', $branchId)
                ->orWhere('o.branch_id IS NULL')
            ->groupEnd();

        if ($closedAt !== null) {
            $tenderPaymentsQuery->where('op.created_at <=', $closedAt);
        }

        $tenderPayments = $tenderPaymentsQuery->get()->getResultArray();

        $accountedOrderIds = [];
        foreach ($tenderPayments as $tp) {
            $isCash = ($tp['payment_method_id'] == 1 ||
                strtolower((string)($tp['method_slug'] ?? '')) === 'cash' ||
                strtolower((string)($tp['method_name'] ?? '')) === 'cash');

            $amt = (float)$tp['amount'];
            if ($isCash) {
                $cashSales += $amt;
            } else {
                $digitalSales += $amt;
            }
            if (!empty($tp['order_id'])) {
                $accountedOrderIds[] = (int)$tp['order_id'];
            }
        }

        // 2. Calculate from paid orders that do not have order_payments entries
        // Matches orders marked completed/paid during this shift (using updated_at or created_at)
        $directOrdersBuilder = $db->table('orders o')
            ->select('o.id, o.final_total, o.payment_method_id, pm.slug as method_slug, pm.name as method_name')
            ->join('payment_methods pm', 'pm.id = o.payment_method_id', 'left')
            ->where('o.payment_status', 'paid')
            ->groupStart()
                ->where('o.branch_id', $branchId)
                ->orWhere('o.branch_id IS NULL')
            ->groupEnd();

        if ($closedAt !== null) {
            $directOrdersBuilder->groupStart()
                ->groupStart()
                    ->where('o.updated_at >=', $openedAt)
                    ->where('o.updated_at <=', $closedAt)
                ->groupEnd()
                ->orGroupStart()
                    ->where('o.created_at >=', $openedAt)
                    ->where('o.created_at <=', $closedAt)
                ->groupEnd()
            ->groupEnd();
        } else {
            $directOrdersBuilder->groupStart()
                ->where('o.updated_at >=', $openedAt)
                ->orWhere('o.created_at >=', $openedAt)
            ->groupEnd();
        }

        if (!empty($accountedOrderIds)) {
            $directOrdersBuilder->whereNotIn('o.id', array_unique($accountedOrderIds));
        }

        $directOrders = $directOrdersBuilder->get()->getResultArray();
        foreach ($directOrders as $do) {
            $amt = (float)$do['final_total'];
            $isCash = (empty($do['payment_method_id']) ||
                $do['payment_method_id'] == 1 ||
                strtolower((string)($do['method_slug'] ?? '')) === 'cash' ||
                strtolower((string)($do['method_name'] ?? '')) === 'cash');

            if ($isCash) {
                $cashSales += $amt;
            } else {
                $digitalSales += $amt;
            }
        }

        // Manual cash in (register cash drops / float additions)
        $cashInRow = $db->table('cash_transactions')
            ->selectSum('amount', 'total_in')
            ->where('register_id', $registerId)
            ->where('type', 'cash_in')
            ->get()->getRowArray();
        $cashIn = (float)($cashInRow['total_in'] ?? 0);

        // Manual cash out (petty cash / payouts)
        $cashOutRow = $db->table('cash_transactions')
            ->selectSum('amount', 'total_out')
            ->where('register_id', $registerId)
            ->where('type', 'cash_out')
            ->get()->getRowArray();
        $cashOut = (float)($cashOutRow['total_out'] ?? 0);

        $openingFloat = (float)$register['opening_float'];
        $expectedCash = $openingFloat + $cashSales + $cashIn - $cashOut;

        // Keep register table expected_cash synced
        if ((float)($register['expected_cash'] ?? -1) !== $expectedCash) {
            $this->update($registerId, ['expected_cash' => $expectedCash]);
            $register['expected_cash'] = $expectedCash;
        }

        return [
            'register'       => $register,
            'opening_float'  => $openingFloat,
            'cash_sales'     => $cashSales,
            'digital_sales'  => $digitalSales,
            'total_sales'    => $cashSales + $digitalSales,
            'cash_in'        => $cashIn,
            'cash_out'       => $cashOut,
            'expected_cash'  => $expectedCash,
        ];
    }
}
