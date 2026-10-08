<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Api;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * NotificationController - API endpoints for live topbar and in-app notifications
 */
class NotificationController extends BaseController
{
    protected NotificationModel $notificationModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get live notifications for the logged in user
     * GET /admin/api/notifications
     */
    public function index(): ResponseInterface
    {
        $userId = $this->currentUserId ?? (session('user_id') ? (int) session('user_id') : 1);
        $limit  = (int) ($this->request->getGet('limit') ?: 15);

        // Auto-seed welcome notifications if table has no notifications yet
        $existingCount = $this->notificationModel->countAllResults(false);
        if ($existingCount === 0) {
            $this->notificationModel->createNotification(
                null,
                'system',
                'System Connected',
                'RMS Real-time notification system is active and monitoring kitchen, POS, and delivery alerts.',
                ['system' => true]
            );
            $this->notificationModel->createNotification(
                null,
                'orders',
                'Order Alerts Active',
                'New order placements and table settlements will now display here in real-time.',
                ['system' => true]
            );
        }

        $notifications = $this->notificationModel->getNotificationsForUser($userId, $limit);
        $unreadCount   = $this->notificationModel->getUnreadCount($userId);

        // Map notifications for response
        $mapped = array_map(function ($n) {
            return [
                'id'         => (int) $n['id'],
                'type'       => $n['type'] ?? 'system',
                'title'      => $n['title'],
                'body'       => $n['body'] ?? '',
                'data'       => !empty($n['data']) ? json_decode($n['data'], true) : null,
                'read_at'    => $n['read_at'],
                'created_at' => date('M d, h:i A', strtotime($n['created_at'])),
                'is_unread'  => empty($n['read_at']),
            ];
        }, $notifications);

        $liveOrdersCount = 0;
        try {
            $db = db_connect();
            $b = $db->table('orders')->whereIn('status', ['confirmed', 'preparing', 'ready']);
            if (!empty($this->currentBranchId)) {
                $b->where('branch_id', (int)$this->currentBranchId);
            } elseif (!empty($this->currentRestaurantId)) {
                $b->where('restaurant_id', (int)$this->currentRestaurantId);
            }
            $liveOrdersCount = (int)$b->countAllResults();
        } catch (\Throwable $e) {
            $liveOrdersCount = 0;
        }

        return $this->response
            ->setContentType('application/json')
            ->setStatusCode(200)
            ->setJSON([
                'status'            => 'success',
                'success'           => true,
                'message'           => 'Notifications loaded',
                'data'              => $mapped,
                'live_orders_count' => $liveOrdersCount,
            ]);
    }

    /**
     * Mark a single notification or all notifications as read
     * POST /admin/api/notifications/read
     */
    public function markRead(): ResponseInterface
    {
        $userId = $this->currentUserId ?? (session('user_id') ? (int) session('user_id') : 1);
        $json   = $this->request->getJSON(true) ?? [];
        $id     = (int) ($this->request->getPost('id') ?: ($json['id'] ?? 0));
        $all    = (bool) ($this->request->getPost('all') ?: ($json['all'] ?? false));

        if ($all || $id <= 0) {
            $this->notificationModel->markAllAsRead($userId);
            $msg = 'All notifications marked as read.';
        } else {
            $this->notificationModel->markAsRead($id, $userId);
            $msg = 'Notification marked as read.';
        }

        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        return $this->jsonSuccess(['unread_count' => $unreadCount], $msg);
    }

    /**
     * Get unread notifications count
     * GET /admin/api/notifications/unread-count
     */
    public function unreadCount(): ResponseInterface
    {
        $userId = $this->currentUserId ?? 1;
        $count  = $this->notificationModel->getUnreadCount($userId);

        return $this->jsonSuccess(['count' => $count]);
    }
}
