<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use App\Models\MaintenanceRequestModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * MaintenanceController - Manages restaurant equipment, breakdown work orders, repairs and costs
 */
class MaintenanceController extends BaseController
{
    protected EquipmentModel $equipmentModel;
    protected MaintenanceRequestModel $requestModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->equipmentModel = new EquipmentModel();
        $this->requestModel   = new MaintenanceRequestModel();
    }

    /**
     * Equipment Dashboard & Maintenance Requests
     */
    public function index(): string
    {
        $this->authorize('maintenance.view');

        $branchId = $this->currentBranchId ?? 1;

        $equipmentList = $this->equipmentModel->getBranchEquipment($branchId);
        $requests = $this->requestModel->getRequests($branchId);

        $operationalCount = 0;
        $underMaintenanceCount = 0;
        $brokenCount = 0;

        foreach ($equipmentList as $eq) {
            match ($eq['status']) {
                'operational'       => $operationalCount++,
                'under_maintenance' => $underMaintenanceCount++,
                'broken'            => $brokenCount++,
                default             => null,
            };
        }

        // Calculate total maintenance cost logged
        $costRow = $this->requestModel->selectSum('cost', 'total_cost')
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->first();
        $totalRepairCost = (float) ($costRow['total_cost'] ?? 0);

        return $this->renderView('admin/maintenance/index', [
            'pageTitle'             => 'Equipment & Maintenance Management',
            'equipmentList'         => $equipmentList,
            'requests'              => $requests,
            'operationalCount'      => $operationalCount,
            'underMaintenanceCount' => $underMaintenanceCount,
            'brokenCount'           => $brokenCount,
            'totalRepairCost'       => $totalRepairCost,
        ]);
    }

    /**
     * Add or Update Equipment
     */
    public function saveEquipment(): ResponseInterface
    {
        $this->authorize('maintenance.manage');

        $rules = [
            'name'     => 'required|min_length[2]|max_length[120]',
            'location' => 'required|min_length[2]|max_length[80]',
            'status'   => 'required|in_list[operational,under_maintenance,broken,retired]',
        ];

        if ($errors = $this->validatePost($rules)) {
            return $this->jsonError('Validation error', 422, $errors);
        }

        $id = (int) $this->request->getPost('id');
        $data = [
            'restaurant_id'      => $this->currentRestaurantId ?? 1,
            'branch_id'          => $this->currentBranchId ?? 1,
            'name'               => trim((string) $this->request->getPost('name')),
            'model_number'       => trim((string) $this->request->getPost('model_number')),
            'serial_number'      => trim((string) $this->request->getPost('serial_number')),
            'location'           => trim((string) $this->request->getPost('location')),
            'purchase_date'      => $this->request->getPost('purchase_date') ?: null,
            'warranty_expiry'    => $this->request->getPost('warranty_expiry') ?: null,
            'cost'               => (float) ($this->request->getPost('cost') ?: 0),
            'status'             => $this->request->getPost('status'),
            'last_serviced_date' => $this->request->getPost('last_serviced_date') ?: null,
            'next_service_due'   => $this->request->getPost('next_service_due') ?: null,
        ];

        if ($id > 0) {
            $this->equipmentModel->update($id, $data);
            $msg = 'Equipment asset updated successfully.';
        } else {
            $id = (int) $this->equipmentModel->insert($data);
            $msg = 'Equipment asset registered successfully.';
        }

        return $this->jsonSuccess(['id' => $id], $msg);
    }

    /**
     * Submit Maintenance Request
     */
    public function createRequest(): ResponseInterface
    {
        $this->authorize('maintenance.manage');

        $rules = [
            'equipment_id' => 'required|is_natural_no_zero',
            'title'        => 'required|min_length[3]|max_length[150]',
            'priority'     => 'required|in_list[low,medium,high,emergency]',
            'description'  => 'required',
        ];

        if ($errors = $this->validatePost($rules)) {
            return $this->jsonError('Validation error', 422, $errors);
        }

        $data = [
            'equipment_id'       => (int) $this->request->getPost('equipment_id'),
            'branch_id'          => $this->currentBranchId ?? 1,
            'title'              => trim((string) $this->request->getPost('title')),
            'priority'           => $this->request->getPost('priority'),
            'description'        => trim((string) $this->request->getPost('description')),
            'reported_by'        => $this->currentUserId ?? 1,
            'assigned_to_vendor' => trim((string) $this->request->getPost('assigned_to_vendor')),
            'status'             => 'reported',
            'reported_at'        => date('Y-m-d H:i:s'),
        ];

        $id = $this->requestModel->insert($data);

        // Update equipment to under_maintenance
        $this->equipmentModel->update($data['equipment_id'], ['status' => 'under_maintenance']);

        return $this->jsonSuccess(['id' => $id], 'Maintenance work order logged.');
    }

    /**
     * Update Maintenance Request Status
     */
    public function updateRequestStatus(int $id): ResponseInterface
    {
        $this->authorize('maintenance.manage');

        $status = (string) $this->request->getPost('status');
        $cost = (float) ($this->request->getPost('cost') ?: 0.0);
        $notes = (string) $this->request->getPost('resolution_notes');
        $invoice = (string) $this->request->getPost('invoice_number');

        if (!in_array($status, ['reported', 'in_progress', 'completed', 'cancelled'], true)) {
            return $this->jsonError('Invalid status.', 422);
        }

        $ok = $this->requestModel->updateRequestStatus($id, $status, $cost, $notes, $invoice);

        if (!$ok) {
            return $this->jsonError('Failed to update maintenance request.', 400);
        }

        return $this->jsonSuccess([], 'Maintenance request updated to ' . ucfirst(str_replace('_', ' ', $status)));
    }
}
