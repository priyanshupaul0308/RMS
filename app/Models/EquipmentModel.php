<?php

declare(strict_types=1);

namespace App\Models;

/**
 * EquipmentModel - Kitchen, bar and operational equipment registry
 */
class EquipmentModel extends BaseModel
{
    protected $table            = 'equipment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'name',
        'model_number',
        'serial_number',
        'location',
        'purchase_date',
        'warranty_expiry',
        'cost',
        'status',
        'last_serviced_date',
        'next_service_due',
    ];

    protected $validationRules = [
        'name'     => 'required|min_length[2]|max_length[120]',
        'location' => 'required|min_length[2]|max_length[80]',
        'status'   => 'required|in_list[operational,under_maintenance,broken,retired]',
    ];

    /**
     * Get all equipment for a branch with maintenance status.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBranchEquipment(int $branchId = 1): array
    {
        return $this->where('branch_id', $branchId)
            ->orderBy('status', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
