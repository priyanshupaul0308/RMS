<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CommunicationLogModel - Multi-channel customer and staff communication records
 */
class CommunicationLogModel extends BaseModel
{
    protected $table            = 'communication_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'recipient_type',
        'recipient_id',
        'recipient_name',
        'recipient_contact',
        'channel',
        'category',
        'subject',
        'message',
        'status',
        'sent_by',
        'sent_at',
    ];

    /**
     * Log and dispatch a communication message.
     */
    public function logMessage(
        int $branchId,
        string $recipientType,
        string $recipientName,
        string $recipientContact,
        string $channel,
        string $category,
        string $subject,
        string $message,
        int $sentBy = 1,
        ?int $recipientId = null
    ): int {
        return (int) $this->insert([
            'restaurant_id'     => 1,
            'branch_id'         => $branchId,
            'recipient_type'    => $recipientType,
            'recipient_id'      => $recipientId,
            'recipient_name'    => $recipientName,
            'recipient_contact' => $recipientContact,
            'channel'           => $channel,
            'category'          => $category,
            'subject'           => $subject,
            'message'           => $message,
            'status'            => 'delivered',
            'sent_by'           => $sentBy,
            'sent_at'           => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get communication records with filters.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLogs(int $branchId, ?string $category = null, ?string $channel = null, int $limit = 50): array
    {
        $builder = $this->where('branch_id', $branchId);

        if ($category && $category !== 'all') {
            $builder->where('category', $category);
        }

        if ($channel && $channel !== 'all') {
            $builder->where('channel', $channel);
        }

        return $builder->orderBy('sent_at', 'DESC')->findAll($limit);
    }
}
