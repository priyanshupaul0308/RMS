<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DeliveryPartnerModel;
use App\Models\DeliveryOrderModel;
use App\Models\OrderModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * DeliveryController - Manages delivery riders, dispatch assignments, and tracking
 */
class DeliveryController extends BaseController
{
    protected DeliveryPartnerModel $partnerModel;
    protected DeliveryOrderModel $deliveryOrderModel;
    protected OrderModel $orderModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->partnerModel       = new DeliveryPartnerModel();
        $this->deliveryOrderModel = new DeliveryOrderModel();
        $this->orderModel         = new OrderModel();
    }

    /**
     * Dispatch Board & Active Deliveries
     */
    public function index(): string
    {
        $this->authorize('delivery.view');

        $branchId = $this->currentBranchId ?? 1;

        $partners = $this->partnerModel->where('branch_id', $branchId)->orderBy('name', 'ASC')->findAll();
        $activeDeliveries = $this->deliveryOrderModel->getDeliveries($branchId);

        // Orders ready for delivery assignment (delivery order_type, confirmed or preparing, not yet assigned)
        $unassignedOrders = $this->orderModel->select('orders.*, orders.final_total as total_amount')
            ->join('delivery_orders', 'delivery_orders.order_id = orders.id', 'left')
            ->where('orders.branch_id', $branchId)
            ->where('orders.order_type', 'delivery')
            ->whereIn('orders.status', ['confirmed', 'preparing', 'ready'])
            ->where('delivery_orders.id IS NULL', null, false)
            ->findAll();

        $availableRidersCount = 0;
        $onDeliveryRidersCount = 0;
        foreach ($partners as $p) {
            if ($p['availability_status'] === 'available') {
                $availableRidersCount++;
            } elseif ($p['availability_status'] === 'on_delivery') {
                $onDeliveryRidersCount++;
            }
        }

        return $this->renderView('admin/delivery/index', [
            'pageTitle'              => 'Delivery Partner Management',
            'partners'               => $partners,
            'activeDeliveries'       => $activeDeliveries,
            'unassignedOrders'       => $unassignedOrders,
            'availableRidersCount'   => $availableRidersCount,
            'onDeliveryRidersCount'  => $onDeliveryRidersCount,
        ]);
    }

    /**
     * Save Delivery Partner Profile
     */
    public function savePartner(): ResponseInterface
    {
        $this->authorize('delivery.manage');

        $rules = [
            'name'           => 'required|min_length[2]|max_length[100]',
            'phone'          => 'required|min_length[8]|max_length[25]',
            'vehicle_type'   => 'required|in_list[bike,scooter,car,van]',
            'vehicle_number' => 'required|min_length[3]|max_length[30]',
        ];

        if ($errors = $this->validatePost($rules)) {
            return $this->jsonError('Validation error', 422, $errors);
        }

        $id = (int) $this->request->getPost('id');
        $data = [
            'restaurant_id'       => $this->currentRestaurantId ?? 1,
            'branch_id'           => $this->currentBranchId ?? 1,
            'name'                => trim((string) $this->request->getPost('name')),
            'phone'               => trim((string) $this->request->getPost('phone')),
            'email'               => $this->request->getPost('email') ? trim((string) $this->request->getPost('email')) : null,
            'vehicle_type'        => $this->request->getPost('vehicle_type'),
            'vehicle_number'      => strtoupper(trim((string) $this->request->getPost('vehicle_number'))),
            'commission_rate'     => (float) ($this->request->getPost('commission_rate') ?: 15.0),
            'availability_status' => (string) ($this->request->getPost('availability_status') ?: 'available'),
            'is_active'           => (int) ($this->request->getPost('is_active') ?? 1),
        ];

        if ($id > 0) {
            $this->partnerModel->update($id, $data);
            $msg = 'Delivery partner updated successfully.';
        } else {
            $data['rating'] = 5.00;
            $data['total_deliveries'] = 0;
            $id = (int) $this->partnerModel->insert($data);
            $msg = 'Delivery partner registered successfully.';
        }

        return $this->jsonSuccess(['id' => $id], $msg);
    }

    /**
     * Assign Order to Rider
     */
    public function assign(): ResponseInterface
    {
        $this->authorize('delivery.manage');

        $orderId = (int) $this->request->getPost('order_id');
        $partnerId = (int) $this->request->getPost('partner_id');
        $branchId = $this->currentBranchId ?? 1;
        $fee = (float) ($this->request->getPost('delivery_fee') ?: 0.0);
        $notes = (string) $this->request->getPost('delivery_notes');

        if (!$orderId || !$partnerId) {
            return $this->jsonError('Please select both an Order and a Delivery Partner.', 422);
        }

        $assignmentId = $this->deliveryOrderModel->assignOrder($orderId, $partnerId, $branchId, $fee, $notes);

        // Push in-app notification
        try {
            (new \App\Models\NotificationModel())->createNotification(
                null,
                'delivery',
                "Delivery Dispatched",
                "Order #{$orderId} assigned to delivery partner.",
                ['order_id' => $orderId, 'assignment_id' => $assignmentId],
                $branchId
            );
        } catch (\Throwable $ne) {
            log_message('error', 'Notification dispatch failed: ' . $ne->getMessage());
        }

        return $this->jsonSuccess(['id' => $assignmentId], 'Order dispatched to partner successfully.');
    }

    /**
     * Update Delivery Progress & Status
     */
    public function updateStatus(int $assignmentId): ResponseInterface
    {
        $this->authorize('delivery.manage');

        $status = (string) $this->request->getPost('status');
        $rating = $this->request->getPost('customer_rating') ? (int) $this->request->getPost('customer_rating') : null;

        if (!in_array($status, ['assigned', 'picked_up', 'in_transit', 'delivered', 'failed'], true)) {
            return $this->jsonError('Invalid delivery status.', 422);
        }

        $ok = $this->deliveryOrderModel->updateDeliveryStatus($assignmentId, $status, $rating);

        if (!$ok) {
            return $this->jsonError('Failed to update delivery status.', 400);
        }

        // Push in-app notification
        try {
            $branchId = $this->currentBranchId ?? 1;
            (new \App\Models\NotificationModel())->createNotification(
                null,
                'delivery',
                "Delivery: " . ucfirst(str_replace('_', ' ', $status)),
                "Delivery assignment #{$assignmentId} updated to {$status}.",
                ['assignment_id' => $assignmentId, 'status' => $status],
                $branchId
            );
        } catch (\Throwable $ne) {
            log_message('error', 'Notification dispatch failed: ' . $ne->getMessage());
        }

        return $this->jsonSuccess([], 'Delivery status updated to ' . ucfirst(str_replace('_', ' ', $status)));
    }
}
