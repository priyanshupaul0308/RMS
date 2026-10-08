<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FloorModel;
use App\Models\TableModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * FloorController – Floor Plan Management & Table Statuses.
 */
class FloorController extends BaseController
{
    protected FloorModel $floorModel;
    protected TableModel $tableModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->floorModel = new FloorModel();
        $this->tableModel = new TableModel();
    }

    /**
     * Floor & Table Layout Overview.
     */
    public function index(): string
    {
        $this->authorize('floors.view');

        $floors = $this->floorModel->getFloorsWithTables($this->currentBranchId);

        return $this->renderView('admin/floors/index', [
            'pageTitle' => 'Floor Plan & Tables',
            'floors'    => $floors,
        ]);
    }

    /**
     * Quick status update for a table (e.g. Clean & Available, Reserved).
     */
    public function updateTableStatus(int $tableId): ResponseInterface
    {
        if (!$this->can('floors.manage') && !$this->can('floors.view')) {
            $this->authorize('floors.manage');
        }

        $status = $this->request->getPost('status') ?? $this->request->getJSON(true)['status'] ?? '';
        $valid  = ['available', 'occupied', 'reserved', 'dirty'];

        if (!in_array($status, $valid, true)) {
            return $this->jsonError('Invalid table status.');
        }

        $table = $this->tableModel->find($tableId);
        if (!$table) {
            return $this->jsonError('Table not found.');
        }

        if ($this->currentBranchId && (int)$table['branch_id'] !== $this->currentBranchId) {
            return $this->jsonError('Unauthorized branch table modification.');
        }

        $orderId = ($status === 'available' || empty($table['current_order_id'])) ? null : (int)$table['current_order_id'];
        $this->tableModel->updateStatus($tableId, $status, $orderId);

        $this->tableModel->writeAudit('floors', 'table_status_change', 'restaurant_tables', $tableId, [
            'old_status' => $table['status']
        ], [
            'new_status' => $status
        ], "Table {$table['table_number']} set to {$status}");

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess(['new_status' => $status], "Table {$table['table_number']} marked as {$status}.");
        }

        return redirect()->back()->with('success', "Table {$table['table_number']} status updated.");
    }

    /**
     * Create / Add New Table to Floor Plan.
     */
    public function storeTable(): ResponseInterface
    {
        if (!$this->can('floors.manage') && !$this->can('floors.view')) {
            $this->authorize('floors.view');
        }

        $floorId  = (int)$this->request->getPost('floor_id');
        $tblNum   = trim((string)$this->request->getPost('table_number'));
        $capacity = (int)($this->request->getPost('seating_capacity') ?: 4);
        $shape    = (string)($this->request->getPost('shape') ?: 'square');
        $branchId = (int)($this->currentBranchId ?: 1);
        $restaurantId = (int)($this->currentRestaurantId ?: 1);

        if (empty($tblNum) || $floorId <= 0) {
            return redirect()->back()->with('error', 'Please provide a valid table number and select a floor.');
        }

        // Check for duplicate table number within the branch & floor
        $existing = $this->tableModel
            ->where('branch_id', $branchId)
            ->where('floor_id', $floorId)
            ->where('table_number', $tblNum)
            ->where('is_active', 1)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', "Table {$tblNum} already exists on this floor.");
        }

        $maxSort = $this->tableModel->where('floor_id', $floorId)->selectMax('sort_order')->first();
        $nextSort = ((int)($maxSort['sort_order'] ?? 0)) + 1;

        $newId = $this->tableModel->insert([
            'restaurant_id'    => $restaurantId,
            'branch_id'        => $branchId,
            'floor_id'         => $floorId,
            'table_number'     => $tblNum,
            'seating_capacity' => max(1, $capacity),
            'shape'            => in_array($shape, ['square', 'round', 'rectangle'], true) ? $shape : 'square',
            'status'           => 'available',
            'sort_order'       => $nextSort,
            'is_active'        => 1,
        ]);

        if ($newId) {
            $this->tableModel->writeAudit('floors', 'create_table', 'restaurant_tables', (int)$newId, [], [
                'table_number' => $tblNum,
                'floor_id'     => $floorId,
            ], "Created table {$tblNum}");

            return redirect()->to(site_url('admin/floors'))->with('success', "Table '{$tblNum}' added successfully!");
        }

        return redirect()->back()->with('error', 'Failed to add table.');
    }

    /**
     * Update Table Details.
     */
    public function updateTable(int $tableId): ResponseInterface
    {
        if (!$this->can('floors.manage') && !$this->can('floors.view')) {
            $this->authorize('floors.view');
        }

        $table = $this->tableModel->find($tableId);
        if (!$table) {
            return redirect()->back()->with('error', 'Table not found.');
        }

        $floorId  = (int)($this->request->getPost('floor_id') ?: $table['floor_id']);
        $tblNum   = trim((string)$this->request->getPost('table_number'));
        $capacity = (int)($this->request->getPost('seating_capacity') ?: 4);
        $shape    = (string)($this->request->getPost('shape') ?: 'square');

        if (empty($tblNum)) {
            return redirect()->back()->with('error', 'Table number cannot be empty.');
        }

        $this->tableModel->update($tableId, [
            'floor_id'         => $floorId,
            'table_number'     => $tblNum,
            'seating_capacity' => max(1, $capacity),
            'shape'            => in_array($shape, ['square', 'round', 'rectangle'], true) ? $shape : 'square',
        ]);

        return redirect()->to(site_url('admin/floors'))->with('success', "Table '{$tblNum}' updated successfully.");
    }

    /**
     * Remove / Delete Table from Floor Plan.
     */
    public function deleteTable(int $tableId): ResponseInterface
    {
        if (!$this->can('floors.manage') && !$this->can('floors.view')) {
            $this->authorize('floors.view');
        }

        $table = $this->tableModel->find($tableId);
        if (!$table) {
            return redirect()->back()->with('error', 'Table not found.');
        }

        if ($this->currentBranchId && (int)$table['branch_id'] !== $this->currentBranchId) {
            return redirect()->back()->with('error', 'Unauthorized branch table access.');
        }

        // Safety check: Cannot remove if occupied or currently linked to an active order
        if ($table['status'] === 'occupied' || !empty($table['current_order_id'])) {
            return redirect()->back()->with('error', "Cannot remove Table '{$table['table_number']}' because it is currently occupied with an active order.");
        }

        // Check if there are active seated reservations
        $activeRes = db_connect()->table('reservations')
            ->where('table_id', $tableId)
            ->whereIn('status', ['seated', 'confirmed'])
            ->where('reservation_date', date('Y-m-d'))
            ->countAllResults();

        if ($activeRes > 0) {
            return redirect()->back()->with('error', "Cannot remove Table '{$table['table_number']}' because it has active reservations today.");
        }

        // Soft delete by setting is_active = 0 so historical records/reports remain consistent
        $this->tableModel->update($tableId, [
            'is_active' => 0,
            'status'    => 'available',
        ]);

        $this->tableModel->writeAudit('floors', 'delete_table', 'restaurant_tables', $tableId, [
            'table_number' => $table['table_number']
        ], ['is_active' => 0], "Removed table {$table['table_number']}");

        return redirect()->to(site_url('admin/floors'))->with('success', "Table '{$table['table_number']}' removed successfully.");
    }
}
