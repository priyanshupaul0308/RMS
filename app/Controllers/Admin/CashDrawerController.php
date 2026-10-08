<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CashRegisterModel;
use App\Models\CashTransactionModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CashDrawerController – Cash drawer shift management, float tracking, and end-of-day reconciliation.
 */
class CashDrawerController extends BaseController
{
    protected CashRegisterModel    $registerModel;
    protected CashTransactionModel $transactionModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->registerModel    = new CashRegisterModel();
        $this->transactionModel = new CashTransactionModel();
    }

    /**
     * Cash Drawer Dashboard.
     */
    public function index(): string
    {
        $this->authorize('billing.view');

        $activeRegister = $this->registerModel->getOpenRegister($this->currentUserId, $this->currentBranchId);
        $summary = [];
        $transactions = [];

        if ($activeRegister) {
            $summary = $this->registerModel->getShiftSummary((int)$activeRegister['id']);
            $transactions = $this->transactionModel->where('register_id', $activeRegister['id'])
                ->orderBy('id', 'DESC')->findAll();
        }

        // Recent closed shifts with strict branch isolation
        $pastShiftsQuery = $this->registerModel->select('cash_registers.*, users.first_name AS cashier_name')
            ->join('users', 'users.id = cash_registers.user_id', 'left')
            ->where('cash_registers.status', 'closed');

        if ($this->currentBranchId) {
            $pastShiftsQuery->where('cash_registers.branch_id', $this->currentBranchId);
        }

        $pastShifts = $pastShiftsQuery->orderBy('cash_registers.id', 'DESC')->findAll(10);

        return $this->renderView('admin/cash_drawer/index', [
            'pageTitle'      => 'Cash Drawer & Shifts',
            'activeRegister' => $activeRegister,
            'summary'        => $summary,
            'transactions'   => $transactions,
            'pastShifts'     => $pastShifts,
        ]);
    }

    /**
     * Open a new register shift.
     */
    public function open(): ResponseInterface
    {
        $this->authorize('billing.create');

        $rules = [
            'opening_float' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $float = (float) $this->request->getPost('opening_float');

        $newId = $this->registerModel->insert([
            'restaurant_id' => $this->currentRestaurantId,
            'branch_id'     => $this->currentBranchId ?: 1,
            'user_id'       => $this->currentUserId,
            'opened_at'     => date('Y-m-d H:i:s'),
            'opening_float' => $float,
            'expected_cash' => $float,
            'status'        => 'open',
        ]);

        $this->registerModel->writeAudit('billing', 'open_shift', 'cash_registers', (int)$newId, null, [
            'opening_float' => $float,
        ], "Cash register shift opened with float ₹{$float}");

        return redirect()->route('admin.cash-drawer')->with('success', "Shift started with opening float ₹{$float}.");
    }

    /**
     * Record cash in or cash out (petty cash / payout).
     */
    public function transaction(): ResponseInterface
    {
        $this->authorize('billing.create');

        $rules = [
            'register_id' => 'required|integer',
            'type'        => 'required|in_list[cash_in,cash_out]',
            'amount'      => 'required|numeric|greater_than[0]',
            'reason'      => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $regId  = (int) $this->request->getPost('register_id');
        $type   = $this->request->getPost('type');
        $amount = (float) $this->request->getPost('amount');
        $reason = $this->request->getPost('reason');

        $this->transactionModel->insert([
            'register_id' => $regId,
            'type'        => $type,
            'amount'      => $amount,
            'reason'      => $reason,
            'user_id'     => $this->currentUserId,
        ]);

        return redirect()->route('admin.cash-drawer')->with('success', "{$type} transaction of ₹{$amount} recorded.");
    }

    /**
     * Close shift & reconcile counted cash.
     */
    public function close(): ResponseInterface
    {
        $this->authorize('billing.create');

        $rules = [
            'register_id'           => 'required|integer',
            'closing_cash_counted'  => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $regId        = (int) $this->request->getPost('register_id');
        $cashCounted  = (float) $this->request->getPost('closing_cash_counted');
        $notes        = $this->request->getPost('notes') ?: null;

        $summary = $this->registerModel->getShiftSummary($regId);
        $expectedCash = (float)($summary['expected_cash'] ?? 0);
        $discrepancy  = $cashCounted - $expectedCash;

        $this->registerModel->update($regId, [
            'closed_at'            => date('Y-m-d H:i:s'),
            'closing_cash_counted' => $cashCounted,
            'expected_cash'        => $expectedCash,
            'discrepancy'          => $discrepancy,
            'status'               => 'closed',
            'notes'                => $notes,
        ]);

        $this->registerModel->writeAudit('billing', 'close_shift', 'cash_registers', $regId, null, [
            'counted'     => $cashCounted,
            'expected'    => $expectedCash,
            'discrepancy' => $discrepancy,
        ], "Shift closed. Counted: ₹{$cashCounted}, Expected: ₹{$expectedCash}, Diff: ₹{$discrepancy}");

        $diffMsg = ($discrepancy == 0) ? 'Cash is perfectly balanced.' : ($discrepancy > 0 ? "Overage: +₹{$discrepancy}" : "Shortage: -₹" . abs($discrepancy));

        return redirect()->route('admin.cash-drawer')->with('success', "Shift reconciled and closed. {$diffMsg}");
    }

    /**
     * Update closed shift audit notes and reconciliation values.
     */
    public function update(int $id): ResponseInterface
    {
        $this->authorize('billing.create');

        $shift = $this->registerModel->find($id);
        if (!$shift) {
            if ($this->request->isAJAX()) {
                return $this->jsonError('Shift audit record not found.', 404);
            }
            return redirect()->route('admin.cash-drawer')->with('error', 'Shift audit record not found.');
        }

        $notes        = $this->request->getPost('notes') !== null ? trim((string)$this->request->getPost('notes')) : null;
        $cashCounted  = $this->request->getPost('closing_cash_counted');
        $openingFloat = $this->request->getPost('opening_float');

        $updateData = [
            'notes' => !empty($notes) ? $notes : null,
        ];

        // If counted cash was provided, recalculate discrepancy
        if ($cashCounted !== null && $cashCounted !== '' && is_numeric($cashCounted)) {
            $countedVal   = (float)$cashCounted;
            $expectedCash = (float)($shift['expected_cash'] ?? 0);
            $updateData['closing_cash_counted'] = $countedVal;
            $updateData['discrepancy']          = $countedVal - $expectedCash;
        }

        if ($openingFloat !== null && $openingFloat !== '' && is_numeric($openingFloat)) {
            $updateData['opening_float'] = (float)$openingFloat;
        }

        $this->registerModel->update($id, $updateData);

        $this->registerModel->writeAudit('billing', 'update_shift_audit', 'cash_registers', $id, $shift, $updateData, "Updated shift #{$id} audit details and notes");

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess(['id' => $id, 'notes' => $updateData['notes'] ?? ''], "Shift #{$id} audit updated successfully.");
        }

        return redirect()->route('admin.cash-drawer')->with('success', "Shift #{$id} audit updated successfully.");
    }

    /**
     * Delete shift audit record and its transactions.
     */
    public function delete(int $id): ResponseInterface
    {
        $this->authorize('billing.create');

        $shift = $this->registerModel->find($id);
        if (!$shift) {
            if ($this->request->isAJAX()) {
                return $this->jsonError('Shift audit record not found.', 404);
            }
            return redirect()->route('admin.cash-drawer')->with('error', 'Shift audit record not found.');
        }

        $db = db_connect();
        $db->transStart();

        // Remove linked transactions if any
        $this->transactionModel->where('register_id', $id)->delete();

        // Delete register audit record
        $this->registerModel->delete($id);

        $db->transComplete();

        $this->registerModel->writeAudit('billing', 'delete_shift_audit', 'cash_registers', $id, $shift, null, "Deleted shift #{$id} audit record");

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess([], "Shift #{$id} audit record deleted successfully.");
        }

        return redirect()->route('admin.cash-drawer')->with('success', "Shift #{$id} audit record deleted successfully.");
    }
}
