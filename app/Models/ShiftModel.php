<?php

declare(strict_types=1);

namespace App\Models;

/**
 * ShiftModel - Manages restaurant working shifts
 */
class ShiftModel extends BaseModel
{
    protected $table            = 'shifts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'name',
        'start_time',
        'end_time',
        'break_duration_mins',
        'color_code',
        'is_active',
    ];

    protected $validationRules = [
        'name'       => 'required|min_length[2]|max_length[80]',
        'start_time' => 'required',
        'end_time'   => 'required',
    ];

    /**
     * Get all active shifts for a branch.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getActiveShifts(int $branchId = 1): array
    {
        return $this->where('branch_id', $branchId)
            ->where('is_active', 1)
            ->orderBy('start_time', 'ASC')
            ->findAll();
    }
}
