<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * CustomerModel
 * 
 * CRM management, customer profiles, lifetime dining spend, and loyalty points.
 */
class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'restaurant_id',
        'name',
        'phone',
        'email',
        'address',
        'loyalty_points',
        'total_visits',
        'lifetime_spend',
        'vip_status',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get customers with optional phone/name search
     */
    public function getCustomers(?string $search = null, int $restaurantId = 1): array
    {
        $builder = $this->where('restaurant_id', $restaurantId);

        if ($search !== null && $search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        return $builder->orderBy('vip_status', 'DESC')
            ->orderBy('lifetime_spend', 'DESC')
            ->findAll();
    }

    /**
     * Add or deduct loyalty points with transaction log
     */
    public function updatePoints(int $customerId, int $pointsDelta, string $description, ?int $orderId = null): bool
    {
        $cust = $this->find($customerId);
        if (!$cust) {
            return false;
        }

        $currentPoints = (int)$cust['loyalty_points'];
        $newPoints = max(0, $currentPoints + $pointsDelta);

        $earned = max(0, $pointsDelta);
        $redeemed = ($pointsDelta < 0) ? abs($pointsDelta) : 0;

        $this->db->transStart();

        $this->update($customerId, ['loyalty_points' => $newPoints]);

        $this->db->table('loyalty_transactions')->insert([
            'customer_id'     => $customerId,
            'order_id'        => $orderId,
            'points_earned'   => $earned,
            'points_redeemed' => $redeemed,
            'balance_after'   => $newPoints,
            'description'     => $description,
            'created_at'      => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Get loyalty transaction history for a customer
     */
    public function getLoyaltyHistory(int $customerId): array
    {
        return $this->db->table('loyalty_transactions')
            ->where('customer_id', $customerId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }
}
