<?php

declare(strict_types=1);

namespace App\Models;

/**
 * StaffAttendanceModel - Attendance records, check-in/out, and overtime calculations
 */
class StaffAttendanceModel extends BaseModel
{
    protected $table            = 'staff_attendance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'branch_id',
        'shift_id',
        'date',
        'check_in',
        'check_out',
        'total_hours',
        'overtime_hours',
        'status',
        'check_in_ip',
        'check_out_ip',
        'notes',
    ];

    /**
     * Check in staff member.
     */
    public function checkIn(int $userId, int $branchId, ?int $shiftId = null, ?string $ip = null): array
    {
        $today = date('Y-m-d');
        $existing = $this->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return ['status' => false, 'message' => 'Staff already checked in today at ' . $existing['check_in']];
        }

        $now = date('Y-m-d H:i:s');
        $status = 'present';

        // Check if late based on shift
        if ($shiftId) {
            $shift = (new ShiftModel())->find($shiftId);
            if ($shift) {
                $scheduledStart = strtotime($today . ' ' . $shift['start_time']);
                if (time() > ($scheduledStart + 900)) { // 15 mins grace period
                    $status = 'late';
                }
            }
        }

        $id = $this->insert([
            'user_id'      => $userId,
            'branch_id'    => $branchId,
            'shift_id'     => $shiftId,
            'date'         => $today,
            'check_in'     => $now,
            'status'       => $status,
            'check_in_ip'  => $ip,
        ]);

        return ['status' => true, 'id' => $id, 'message' => 'Checked in successfully at ' . date('h:i A')];
    }

    /**
     * Check out staff member and compute total & overtime hours.
     */
    public function checkOut(int $attendanceId, ?string $ip = null): array
    {
        $record = $this->find($attendanceId);
        if (!$record) {
            return ['status' => false, 'message' => 'Attendance record not found'];
        }

        if (!empty($record['check_out'])) {
            return ['status' => false, 'message' => 'Staff already checked out at ' . $record['check_out']];
        }

        $now = date('Y-m-d H:i:s');
        $inTime = strtotime($record['check_in']);
        $outTime = strtotime($now);
        $totalHours = round(($outTime - $inTime) / 3600, 2);

        // Standard shift hours is 8.0, beyond that is overtime
        $overtimeHours = $totalHours > 8.0 ? round($totalHours - 8.0, 2) : 0.00;

        $this->update($attendanceId, [
            'check_out'      => $now,
            'total_hours'    => $totalHours,
            'overtime_hours' => $overtimeHours,
            'check_out_ip'   => $ip,
        ]);

        return [
            'status'         => true,
            'total_hours'    => $totalHours,
            'overtime_hours' => $overtimeHours,
            'message'        => "Checked out successfully. Total: {$totalHours}h (Overtime: {$overtimeHours}h)",
        ];
    }

    /**
     * Get attendance report with staff and shift details.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAttendanceReport(int $branchId, string $startDate, string $endDate): array
    {
        return $this->select('staff_attendance.*, users.first_name, users.last_name, users.email, shifts.name as shift_name, shifts.start_time as shift_start, shifts.end_time as shift_end')
            ->join('users', 'users.id = staff_attendance.user_id')
            ->join('shifts', 'shifts.id = staff_attendance.shift_id', 'left')
            ->where('staff_attendance.branch_id', $branchId)
            ->where('staff_attendance.date >=', $startDate)
            ->where('staff_attendance.date <=', $endDate)
            ->orderBy('staff_attendance.date', 'DESC')
            ->orderBy('staff_attendance.check_in', 'DESC')
            ->findAll();
    }
}
