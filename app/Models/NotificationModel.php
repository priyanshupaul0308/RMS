<?php

declare(strict_types=1);

namespace App\Models;

/**
 * NotificationModel - System and user in-app notifications
 */
class NotificationModel extends BaseModel
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
        'created_at',
    ];

    /**
     * Get recent notifications for a user.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getNotificationsForUser(int $userId, int $limit = 20): array
    {
        return $this->groupStart()
                ->where('user_id', $userId)
                ->orWhere('user_id', null)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->findAll($limit);
    }

    /**
     * Get unread count.
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->groupStart()
            ->where('user_id', $userId)
            ->orWhere('user_id', null)
            ->groupEnd()
            ->where('read_at', null)
            ->countAllResults();
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(int $id, int $userId): bool
    {
        return (bool) $this->where('id', $id)
            ->groupStart()
                ->where('user_id', $userId)
                ->orWhere('user_id', null)
            ->groupEnd()
            ->set(['read_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Mark all as read for user.
     */
    public function markAllAsRead(int $userId): bool
    {
        return (bool) $this->groupStart()
                ->where('user_id', $userId)
                ->orWhere('user_id', null)
            ->groupEnd()
            ->where('read_at', null)
            ->set(['read_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Create notification entry.
     */
    public function createNotification(?int $userId, string $type, string $title, string $body, array $data = [], int $branchId = 1): int
    {
        $restaurantId = 1;
        if (session()->has('restaurant_id')) {
            $restaurantId = (int) session('restaurant_id');
        } elseif ($this->currentRestaurantId) {
            $restaurantId = (int) $this->currentRestaurantId;
        }

        return (int) $this->insert([
            'restaurant_id' => $restaurantId,
            'branch_id'     => $branchId,
            'user_id'       => $userId,
            'type'          => $type,
            'title'         => $title,
            'body'          => $body,
            'data'          => !empty($data) ? json_encode($data) : null,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
