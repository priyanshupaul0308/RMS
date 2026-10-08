<?php

declare(strict_types=1);

namespace App\Models;

/**
 * StaffShiftModel - Shift rosters and assignments
 */
class StaffShiftModel extends BaseModel
{
    protected $table            = 'staff_shifts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'shift_id',
        'user_id',
        'branch_id',
        'shift_date',
        'status',
        'notes',
    ];

    /**
     * Get roster schedule for a branch and date range.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRoster(int $branchId, string $startDate, string $endDate): array
    {
        return $this->select('staff_shifts.*, shifts.name as shift_name, shifts.start_time, shifts.end_time, shifts.color_code, users.first_name, users.last_name, users.email')
            ->join('shifts', 'shifts.id = staff_shifts.shift_id')
            ->join('users', 'users.id = staff_shifts.user_id')
            ->where('staff_shifts.branch_id', $branchId)
            ->where('staff_shifts.shift_date >=', $startDate)
            ->where('staff_shifts.shift_date <=', $endDate)
            ->orderBy('staff_shifts.shift_date', 'ASC')
            ->orderBy('shifts.start_time', 'ASC')
            ->findAll();
    }
}
