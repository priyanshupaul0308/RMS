<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExpenseModel;
use App\Models\ExpenseCategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ExpenseController - Manages daily operational expenses, receipts, approvals and reporting
 */
class ExpenseController extends BaseController
{
    protected ExpenseModel $expenseModel;
    protected ExpenseCategoryModel $categoryModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->expenseModel  = new ExpenseModel();
        $this->categoryModel = new ExpenseCategoryModel();
    }

    /**
     * Expense Dashboard & Today's List
     */
    public function index(): string
    {
        $this->authorize('expenses.view');

        $branchId = $this->currentBranchId ?? 1;
        $restaurantId = $this->currentRestaurantId ?? 1;
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        $categories = $this->categoryModel->getActiveCategories($restaurantId);
        $expenses = $this->expenseModel->getExpenses($branchId, $monthStart, $today);

        $todayTotal = 0.0;
        $monthTotal = 0.0;
        $pendingCount = 0;

        foreach ($expenses as $e) {
            $amt = (float) $e['amount'];
            if ($e['status'] === 'approved') {
                $monthTotal += $amt;
                if ($e['expense_date'] === $today) {
                    $todayTotal += $amt;
                }
            } elseif ($e['status'] === 'pending') {
                $pendingCount++;
            }
        }

        return $this->renderView('admin/expenses/index', [
            'pageTitle'    => 'Expense Management',
            'expenses'     => $expenses,
            'categories'   => $categories,
            'todayTotal'   => $todayTotal,
            'monthTotal'   => $monthTotal,
            'pendingCount' => $pendingCount,
            'today'        => $today,
        ]);
    }

    /**
     * Record Expense (with receipt upload)
     */
    public function store(): ResponseInterface
    {
        $this->authorize('expenses.manage');

        $rules = [
            'category_id'    => 'required|is_natural_no_zero',
            'amount'         => 'required|numeric|greater_than[0]',
            'expense_date'   => 'required',
            'payment_method' => 'required|in_list[cash,bank_transfer,company_card,cheque]',
        ];

        if ($errors = $this->validatePost($rules)) {
            return $this->jsonError('Validation error', 422, $errors);
        }

        $receiptPath = null;
        $file = $this->request->getFile('receipt_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/receipts';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $receiptPath = 'uploads/receipts/' . $newName;
        }

        $data = [
            'restaurant_id'      => $this->currentRestaurantId ?? 1,
            'branch_id'          => $this->currentBranchId ?? 1,
            'category_id'        => (int) $this->request->getPost('category_id'),
            'amount'             => (float) $this->request->getPost('amount'),
            'expense_date'       => $this->request->getPost('expense_date'),
            'payment_method'     => $this->request->getPost('payment_method'),
            'vendor_name'        => trim((string) $this->request->getPost('vendor_name')),
            'invoice_receipt_no' => trim((string) $this->request->getPost('invoice_receipt_no')),
            'receipt_file'       => $receiptPath,
            'status'             => $this->can('expenses.manage') ? 'approved' : 'pending',
            'recorded_by'        => $this->currentUserId ?? 1,
            'approved_by'        => $this->can('expenses.manage') ? ($this->currentUserId ?? 1) : null,
            'approved_at'        => $this->can('expenses.manage') ? date('Y-m-d H:i:s') : null,
            'notes'              => trim((string) $this->request->getPost('notes')),
        ];

        $id = $this->expenseModel->insert($data);

        // Push in-app notification
        try {
            $branchId = $this->currentBranchId ?? 1;
            (new \App\Models\NotificationModel())->createNotification(
                null,
                'expense',
                'New Expense: ₹' . number_format((float)$data['amount'], 2),
                ($data['vendor_name'] ? "Vendor: {$data['vendor_name']} - " : '') . "Recorded by " . ($this->currentUser['first_name'] ?? 'Staff'),
                ['expense_id' => $id, 'amount' => $data['amount']],
                $branchId
            );
        } catch (\Throwable $ne) {
            log_message('error', 'Notification dispatch failed: ' . $ne->getMessage());
        }

        return $this->jsonSuccess(['id' => $id], 'Expense recorded successfully.');
    }

    /**
     * Approve or Reject Expense
     */
    public function approve(int $id): ResponseInterface
    {
        $this->authorize('expenses.manage');

        $status = (string) $this->request->getPost('status');
        if (!in_array($status, ['approved', 'rejected'], true)) {
            $status = 'approved';
        }

        $ok = $this->expenseModel->approveExpense($id, $this->currentUserId ?? 1, $status);

        if (!$ok) {
            return $this->jsonError('Failed to update expense status.', 400);
        }

        return $this->jsonSuccess([], 'Expense marked as ' . $status);
    }

    /**
     * Expense Reports
     */
    public function reports(): string
    {
        $this->authorize('expenses.view');

        $branchId = $this->currentBranchId ?? 1;
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));

        $expenses = $this->expenseModel->getExpenses($branchId, $startDate, $endDate);
        $categoryTotals = $this->expenseModel->getCategoryTotals($branchId, $startDate, $endDate);

        if ($this->request->getGet('export') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="expenses_' . $startDate . '_to_' . $endDate . '.csv"');
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ['Date', 'Category', 'Vendor', 'Invoice / Receipt', 'Amount', 'Payment Method', 'Status', 'Recorded By']);
            
            foreach ($expenses as $e) {
                fputcsv($fp, [
                    $e['expense_date'],
                    $e['category_name'],
                    $e['vendor_name'] ?? 'N/A',
                    $e['invoice_receipt_no'] ?? 'N/A',
                    $e['amount'],
                    strtoupper($e['payment_method']),
                    strtoupper($e['status']),
                    ($e['recorded_by_fname'] ?? '') . ' ' . ($e['recorded_by_lname'] ?? ''),
                ]);
            }
            fclose($fp);
            exit;
        }

        return $this->renderView('admin/expenses/reports', [
            'pageTitle'      => 'Expense Analytics & Reports',
            'expenses'       => $expenses,
            'categoryTotals' => $categoryTotals,
            'startDate'      => $startDate,
            'endDate'        => $endDate,
        ]);
    }
}
