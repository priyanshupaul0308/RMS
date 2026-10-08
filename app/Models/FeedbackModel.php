<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * FeedbackModel
 * 
 * Manages customer feedback, satisfaction scores (Food, Service, Ambience), and comments.
 */
class FeedbackModel extends Model
{
    protected $table            = 'customer_feedback';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'customer_id',
        'order_id',
        'customer_name',
        'customer_phone',
        'rating',
        'food_rating',
        'service_rating',
        'ambience_rating',
        'comments',
        'created_at'
    ];

    protected $useTimestamps = false;

    /**
     * Get recent feedback with aggregated score summary
     */
    public function getFeedbackList(int $limit = 50): array
    {
        return $this->orderBy('id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Calculate average customer satisfaction metrics
     */
    public function getSatisfactionMetrics(): array
    {
        $res = $this->db->table($this->table)
            ->selectAvg('rating', 'avg_overall')
            ->selectAvg('food_rating', 'avg_food')
            ->selectAvg('service_rating', 'avg_service')
            ->selectAvg('ambience_rating', 'avg_ambience')
            ->selectCount('id', 'total_reviews')
            ->get()
            ->getRowArray();

        return [
            'overall'       => round((float)($res['avg_overall'] ?? 5.0), 1),
            'food'          => round((float)($res['avg_food'] ?? 5.0), 1),
            'service'       => round((float)($res['avg_service'] ?? 5.0), 1),
            'ambience'      => round((float)($res['avg_ambience'] ?? 5.0), 1),
            'total_reviews' => (int)($res['total_reviews'] ?? 0)
        ];
    }
}
