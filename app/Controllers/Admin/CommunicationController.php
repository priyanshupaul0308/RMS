<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Models\CommunicationLogModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CommunicationController - Multi-channel alerts, orders, reservations, low-stock, and staff/customer broadcasts
 */
class CommunicationController extends BaseController
{
    protected NotificationModel $notificationModel;
    protected CommunicationLogModel $commLogModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->notificationModel = new NotificationModel();
        $this->commLogModel      = new CommunicationLogModel();
    }

    /**
     * Notification & Communication Hub
     */
    public function index(): string
    {
        $this->authorize('communication.view');

        $branchId = $this->currentBranchId ?? 1;
        $category = (string) ($this->request->getGet('category') ?: 'all');
        $channel  = (string) ($this->request->getGet('channel') ?: 'all');

        $logs = $this->commLogModel->getLogs($branchId, $category, $channel, 100);
        $inAppNotifs = $this->notificationModel->getNotificationsForUser($this->currentUserId ?? 1, 20);
        $unreadCount = $this->notificationModel->getUnreadCount($this->currentUserId ?? 1);

        // Count category metrics
        $orderCount = 0;
        $resCount = 0;
        $stockCount = 0;
        $staffCount = 0;

        foreach ($logs as $l) {
            match ($l['category']) {
                'order'        => $orderCount++,
                'reservation'  => $resCount++,
                'low_stock'    => $stockCount++,
                'announcement' => $staffCount++,
                default        => null,
            };
        }

        return $this->renderView('admin/communication/index', [
            'pageTitle'    => 'Notification & Communication Center',
            'logs'         => $logs,
            'inAppNotifs'  => $inAppNotifs,
            'unreadCount'  => $unreadCount,
            'category'     => $category,
            'channel'      => $channel,
            'orderCount'   => $orderCount,
            'resCount'     => $resCount,
            'stockCount'   => $stockCount,
            'staffCount'   => $staffCount,
        ]);
    }

    /**
     * Dispatch new Communication / Notification
     */
    public function send(): ResponseInterface
    {
        $this->authorize('communication.manage');

        $rules = [
            'recipient_type'    => 'required|in_list[customer,staff,broadcast]',
            'recipient_name'    => 'required|min_length[2]|max_length[100]',
            'recipient_contact' => 'required',
            'channel'           => 'required|in_list[in_app,sms,email,whatsapp]',
            'category'          => 'required|in_list[order,reservation,payment,low_stock,announcement,promo]',
            'subject'           => 'required|min_length[3]|max_length[150]',
            'message'           => 'required',
        ];

        if ($errors = $this->validatePost($rules)) {
            return $this->jsonError('Validation error', 422, $errors);
        }

        $branchId = $this->currentBranchId ?? 1;
        $userId = $this->currentUserId ?? 1;

        $logId = $this->commLogModel->logMessage(
            $branchId,
            (string) $this->request->getPost('recipient_type'),
            trim((string) $this->request->getPost('recipient_name')),
            trim((string) $this->request->getPost('recipient_contact')),
            (string) $this->request->getPost('channel'),
            (string) $this->request->getPost('category'),
            trim((string) $this->request->getPost('subject')),
            trim((string) $this->request->getPost('message')),
            $userId
        );

        // Also push an in-app notification if channel is in_app or broadcast
        $channel = $this->request->getPost('channel');
        if (in_array($channel, ['in_app', 'whatsapp', 'sms'], true)) {
            $this->notificationModel->createNotification(
                null,
                (string) $this->request->getPost('category'),
                trim((string) $this->request->getPost('subject')),
                trim((string) $this->request->getPost('message')),
                ['log_id' => $logId],
                $branchId
            );
        }

        return $this->jsonSuccess(['id' => $logId], 'Message dispatched successfully.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(): ResponseInterface
    {
        $this->notificationModel->markAllAsRead($this->currentUserId ?? 1);
        return $this->jsonSuccess([], 'All notifications marked as read.');
    }
}
