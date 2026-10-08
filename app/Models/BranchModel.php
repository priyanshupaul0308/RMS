<?php

declare(strict_types=1);

namespace App\Models;

/**
 * BranchModel – Manages restaurant branches / outlets.
 */
class BranchModel extends BaseModel
{
    protected $table      = 'branches';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'restaurant_id', 'name', 'code', 'address', 'city', 'state',
        'country', 'postal_code', 'phone', 'email', 'gstin',
        'fssai_license', 'opening_time', 'closing_time', 'is_active',
    ];

    protected $validationRules = [
        'restaurant_id' => 'required|integer',
        'name'          => 'required|max_length[150]',
        'code'          => 'permit_empty|max_length[20]',
        'email'         => 'permit_empty|valid_email',
    ];

    /**
     * Get branches for a restaurant with manager count.
     */
    public function getBranchesForRestaurant(int $restaurantId): array
    {
        return $this
            ->select('branches.*, COUNT(u.id) AS staff_count')
            ->join('users u', 'u.branch_id = branches.id', 'left')
            ->where('branches.restaurant_id', $restaurantId)
            ->groupBy('branches.id')
            ->orderBy('branches.name', 'ASC')
            ->findAll();
    }

    /**
     * Get active branches for dropdowns.
     */
    public function getActiveBranches(int $restaurantId): array
    {
        return $this
            ->select('id, name, code')
            ->where(['restaurant_id' => $restaurantId, 'is_active' => 1])
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
