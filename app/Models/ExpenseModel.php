<?php

declare(strict_types=1);

namespace App\Models;

/**
 * ExpenseModel - Daily operating expenses, receipt tracking, and approval workflow
 */
class ExpenseModel extends BaseModel
{
    protected $table            = 'expenses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'category_id',
        'amount',
        'expense_date',
        'payment_method',
        'vendor_name',
        'invoice_receipt_no',
        'receipt_file',
        'status',
        'recorded_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $validationRules = [
        'category_id'    => 'required|is_natural_no_zero',
        'amount'         => 'required|numeric|greater_than[0]',
        'expense_date'   => 'required',
        'payment_method' => 'required|in_list[cash,bank_transfer,company_card,cheque]',
    ];

    /**
     * Get expenses with category and user information.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getExpenses(int $branchId, string $startDate, string $endDate, ?string $status = null): array
    {
        $builder = $this->select('expenses.*, expense_categories.name as category_name, recorder.first_name as recorded_by_fname, recorder.last_name as recorded_by_lname, approver.first_name as approved_by_fname, approver.last_name as approved_by_lname')
            ->join('expense_categories', 'expense_categories.id = expenses.category_id')
            ->join('users as recorder', 'recorder.id = expenses.recorded_by', 'left')
            ->join('users as approver', 'approver.id = expenses.approved_by', 'left')
            ->where('expenses.branch_id', $branchId)
            ->where('expenses.expense_date >=', $startDate)
            ->where('expenses.expense_date <=', $endDate);

        if ($status) {
            $builder->where('expenses.status', $status);
        }

        return $builder->orderBy('expenses.expense_date', 'DESC')
            ->orderBy('expenses.id', 'DESC')
            ->findAll();
    }

    /**
     * Get monthly breakdown by category.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCategoryTotals(int $branchId, string $startDate, string $endDate): array
    {
        return $this->select('expense_categories.name as category_name, SUM(expenses.amount) as total_amount, COUNT(expenses.id) as count')
            ->join('expense_categories', 'expense_categories.id = expenses.category_id')
            ->where('expenses.branch_id', $branchId)
            ->where('expenses.status', 'approved')
            ->where('expenses.expense_date >=', $startDate)
            ->where('expenses.expense_date <=', $endDate)
            ->groupBy('expenses.category_id')
            ->orderBy('total_amount', 'DESC')
            ->findAll();
    }

    /**
     * Approve or reject an expense.
     */
    public function approveExpense(int $id, int $approverId, string $status = 'approved'): bool
    {
        return (bool) $this->update($id, [
            'status'      => $status,
            'approved_by' => $approverId,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
