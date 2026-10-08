<?php

declare(strict_types=1);

namespace App\Models;

/**
 * AccountingModel - Financial calculations, sales, purchases, expenses, P&L, and tax ledger
 */
class AccountingModel extends BaseModel
{
    protected $table = 'journal_entries';

    /**
     * Get Sales summary and records for a given date range.
     *
     * @return array<string, mixed>
     */
    public function getSalesSummary(int $branchId, string $startDate, string $endDate): array
    {
        $orders = $this->db->table('orders')
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $grossSales = 0.0;
        $totalTax = 0.0;
        $totalDiscounts = 0.0;
        $totalServiceCharges = 0.0;
        $netSales = 0.0;

        $typeBreakdown = [
            'dine_in'  => 0.0,
            'takeaway' => 0.0,
            'delivery' => 0.0,
        ];

        foreach ($orders as $o) {
            $final = (float) $o['final_total'];
            $tax = (float) ($o['tax_amount'] ?? 0);
            $disc = (float) ($o['discount_amount'] ?? 0);
            $sc = (float) ($o['service_charge'] ?? 0);
            $sub = (float) ($o['subtotal'] ?? ($final - $tax + $disc - $sc));

            $grossSales += $final;
            $totalTax += $tax;
            $totalDiscounts += $disc;
            $totalServiceCharges += $sc;
            $netSales += $sub;

            $type = $o['order_type'] ?? 'dine_in';
            if (isset($typeBreakdown[$type])) {
                $typeBreakdown[$type] += $final;
            } else {
                $typeBreakdown[$type] = $final;
            }
        }

        return [
            'orders'                => $orders,
            'count'                 => count($orders),
            'gross_sales'           => round($grossSales, 2),
            'net_sales'             => round($netSales, 2),
            'total_tax'             => round($totalTax, 2),
            'total_discounts'       => round($totalDiscounts, 2),
            'total_service_charges' => round($totalServiceCharges, 2),
            'type_breakdown'        => $typeBreakdown,
        ];
    }

    /**
     * Get Purchase procurement records from purchase orders.
     *
     * @return array<string, mixed>
     */
    public function getPurchasesSummary(int $branchId, string $startDate, string $endDate): array
    {
        $hasPoTable = $this->db->tableExists('purchase_orders');
        if (!$hasPoTable) {
            return ['records' => [], 'total_purchases' => 0.0, 'count' => 0];
        }

        $records = $this->db->table('purchase_orders')
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->where('purchase_orders.branch_id', $branchId)
            ->where('purchase_orders.created_at >=', $startDate . ' 00:00:00')
            ->where('purchase_orders.created_at <=', $endDate . ' 23:59:59')
            ->orderBy('purchase_orders.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $totalPurchases = 0.0;
        foreach ($records as $r) {
            $totalPurchases += (float) ($r['total_amount'] ?? 0);
        }

        return [
            'records'         => $records,
            'total_purchases' => round($totalPurchases, 2),
            'count'           => count($records),
        ];
    }

    /**
     * Get Operating Expenses summary.
     *
     * @return array<string, mixed>
     */
    public function getExpensesSummary(int $branchId, string $startDate, string $endDate): array
    {
        $hasExpTable = $this->db->tableExists('expenses');
        if (!$hasExpTable) {
            return ['records' => [], 'total_expenses' => 0.0, 'count' => 0];
        }

        $records = $this->db->table('expenses')
            ->select('expenses.*, expense_categories.name as category_name')
            ->join('expense_categories', 'expense_categories.id = expenses.category_id', 'left')
            ->where('expenses.branch_id', $branchId)
            ->where('expenses.status', 'approved')
            ->where('expenses.expense_date >=', $startDate)
            ->where('expenses.expense_date <=', $endDate)
            ->orderBy('expenses.expense_date', 'DESC')
            ->get()
            ->getResultArray();

        $totalExpenses = 0.0;
        $categoryBreakdown = [];

        foreach ($records as $r) {
            $amt = (float) $r['amount'];
            $totalExpenses += $amt;
            $cat = $r['category_name'] ?? 'General';
            $categoryBreakdown[$cat] = ($categoryBreakdown[$cat] ?? 0.0) + $amt;
        }

        return [
            'records'            => $records,
            'total_expenses'     => round($totalExpenses, 2),
            'category_breakdown' => $categoryBreakdown,
            'count'              => count($records),
        ];
    }

    /**
     * Generate Comprehensive Profit and Loss Statement.
     *
     * @return array<string, mixed>
     */
    public function getProfitLossStatement(int $branchId, string $startDate, string $endDate): array
    {
        $sales = $this->getSalesSummary($branchId, $startDate, $endDate);
        $purchases = $this->getPurchasesSummary($branchId, $startDate, $endDate);
        $expenses = $this->getExpensesSummary($branchId, $startDate, $endDate);

        // Revenue
        $grossRevenue = $sales['gross_sales'];
        $discounts = $sales['total_discounts'];
        $netRevenue = round($grossRevenue - $discounts, 2);

        // Cost of Goods Sold (Purchases / Raw Material Intake)
        $cogs = $purchases['total_purchases'];
        $grossProfit = round($netRevenue - $cogs, 2);
        $grossMarginPercent = $netRevenue > 0 ? round(($grossProfit / $netRevenue) * 100, 1) : 0.0;

        // Operating Expenses
        $operatingExpenses = $expenses['total_expenses'];

        // Net Operating Profit
        $netProfit = round($grossProfit - $operatingExpenses, 2);
        $netMarginPercent = $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0.0;

        // Tax Collected
        $taxCollected = $sales['total_tax'];

        return [
            'start_date'           => $startDate,
            'end_date'             => $endDate,
            'gross_revenue'        => $grossRevenue,
            'discounts'            => $discounts,
            'net_revenue'          => $netRevenue,
            'cogs'                 => $cogs,
            'gross_profit'         => $grossProfit,
            'gross_margin_percent' => $grossMarginPercent,
            'operating_expenses'   => $operatingExpenses,
            'net_profit'           => $netProfit,
            'net_margin_percent'   => $netMarginPercent,
            'tax_collected'        => $taxCollected,
            'category_breakdown'   => $expenses['category_breakdown'],
            'sales_type_breakdown' => $sales['type_breakdown'],
        ];
    }

    /**
     * Get Tax Summary Records.
     *
     * @return array<string, mixed>
     */
    public function getTaxRecords(int $branchId, string $startDate, string $endDate): array
    {
        $sales = $this->getSalesSummary($branchId, $startDate, $endDate);
        $taxRates = $this->db->table('tax_rates')->where('is_active', 1)->get()->getResultArray();

        $taxableTurnover = $sales['net_sales'];
        $totalTaxCollected = $sales['total_tax'];

        return [
            'taxable_turnover'    => $taxableTurnover,
            'total_tax_collected' => $totalTaxCollected,
            'tax_rates'           => $taxRates,
            'orders_count'        => $sales['count'],
        ];
    }
}
