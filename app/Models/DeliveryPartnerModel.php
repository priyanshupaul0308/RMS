<?php

declare(strict_types=1);

namespace App\Models;

/**
 * DeliveryPartnerModel - Rider profiles, vehicles, ratings, and availability status
 */
class DeliveryPartnerModel extends BaseModel
{
    protected $table            = 'delivery_partners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'name',
        'phone',
        'email',
        'vehicle_type',
        'vehicle_number',
        'commission_rate',
        'availability_status',
        'is_active',
        'rating',
        'total_deliveries',
    ];

    protected $validationRules = [
        'name'           => 'required|min_length[2]|max_length[100]',
        'phone'          => 'required|min_length[8]|max_length[25]',
        'vehicle_type'   => 'required|in_list[bike,scooter,car,van]',
        'vehicle_number' => 'required|min_length[3]|max_length[30]',
    ];

    /**
     * Get available delivery partners for an incoming delivery order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAvailableRiders(int $branchId = 1): array
    {
        return $this->where('branch_id', $branchId)
            ->where('is_active', 1)
            ->where('availability_status', 'available')
            ->orderBy('rating', 'DESC')
            ->findAll();
    }
}
