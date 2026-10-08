<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ShiftModel;
use App\Models\StaffShiftModel;
use App\Models\StaffAttendanceModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AttendanceController - Manages staff shifts, attendance check-in/out, overtime, and reports
 */
class AttendanceController extends BaseController
{
    protected ShiftModel $shiftModel;
    protected StaffShiftModel $staffShiftModel;
    protected StaffAttendanceModel $attendanceModel;
    protected UserModel $userModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        $this->ensureAttendancePermissions();
        parent::initController($request, $response, $logger);
        $this->shiftModel       = new ShiftModel();
        $this->staffShiftModel  = new StaffShiftModel();
        $this->attendanceModel  = new StaffAttendanceModel();
        $this->userModel        = new UserModel();
    }

    /**
     * Ensure attendance permissions are granted strictly according to roles:
     * Only Admin (1) and Manager (2) get attendance.manage.
     * All staff (1, 2, 3, 4, 5, 6) get attendance.view so they can view and clock in.
     */
    protected function ensureAttendancePermissions(): void
    {
        try {
            $db = db_connect();
            $permManage = $db->table('permissions')->where('slug', 'attendance.manage')->get()->getRowArray();
            $permView   = $db->table('permissions')->where('slug', 'attendance.view')->get()->getRowArray();

            $manageId = $permManage ? (int) $permManage['id'] : null;
            $viewId   = $permView ? (int) $permView['id'] : null;

            if ($manageId) {
                // Strictly revoke attendance.manage from all operational roles (cashier: 3, waiter: 4, chef: 5, inventory: 6)
                $db->table('role_permissions')
                    ->where('permission_id', $manageId)
                    ->whereNotIn('role_id', [1, 2])
                    ->delete();

                // Ensure Super Admin (1) and Manager (2) have attendance.manage
                foreach ([1, 2] as $rId) {
                    $has = $db->table('role_permissions')->where('role_id', $rId)->where('permission_id', $manageId)->countAllResults();
                    if ($has === 0) {
                        $db->table('role_permissions')->insert([
                            'role_id'       => $rId,
                            'permission_id' => $manageId,
                            'granted_at'    => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            if ($viewId) {
                // Ensure all operational roles have attendance.view so they can view attendance & clock in
                foreach ([1, 2, 3, 4, 5, 6] as $rId) {
                    $has = $db->table('role_permissions')->where('role_id', $rId)->where('permission_id', $viewId)->countAllResults();
                    if ($has === 0) {
                        $db->table('role_permissions')->insert([
                            'role_id'       => $rId,
                            'permission_id' => $viewId,
                            'granted_at'    => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Non-blocking
        }
    }

    /**
     * Attendance Dashboard & Today's Records
     */
    public function index(): string
    {
        if (!$this->can('attendance.view') && !$this->can('attendance.manage')) {
            $this->authorize('attendance.view');
        }

        $branchId = $this->currentBranchId ?? 1;
        $today = date('Y-m-d');
        $userId = (int) $this->currentUserId;

        $shifts = $this->shiftModel->getActiveShifts($branchId);
        $staffMembers = $this->userModel->where('branch_id', $branchId)->where('is_active', 1)->findAll();
        
        // Today's attendance records
        $todayAttendance = $this->attendanceModel->getAttendanceReport($branchId, $today, $today);

        // Fetch user's own attendance for today
        $myAttendance = $this->attendanceModel
            ->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        // Fetch user's assigned shift for today
        $myAssignedShift = $this->staffShiftModel
            ->select('staff_shifts.*, shifts.name as shift_name, shifts.start_time, shifts.end_time, shifts.color_code')
            ->join('shifts', 'shifts.id = staff_shifts.shift_id', 'left')
            ->where('staff_shifts.user_id', $userId)
            ->where('staff_shifts.shift_date', $today)
            ->first();

        // Calculate KPI summary
        $checkedInCount = 0;
        $lateCount = 0;
        $totalHoursToday = 0.0;
        $totalOtToday = 0.0;

        foreach ($todayAttendance as $att) {
            $checkedInCount++;
            if ($att['status'] === 'late') {
                $lateCount++;
            }
            $totalHoursToday += (float) $att['total_hours'];
            $totalOtToday += (float) $att['overtime_hours'];
        }

        $isManagerOrAdmin = in_array($this->currentRoleSlug, ['admin', 'manager'], true);

        return $this->renderView('admin/attendance/index', [
            'pageTitle'        => 'Staff Attendance & Shifts',
            'shifts'           => $shifts,
            'staffMembers'     => $staffMembers,
            'todayAttendance'  => $todayAttendance,
            'checkedInCount'   => $checkedInCount,
            'lateCount'        => $lateCount,
            'totalHoursToday'  => round($totalHoursToday, 2),
            'totalOtToday'     => round($totalOtToday, 2),
            'today'            => $today,
            'currentUserId'    => $this->currentUserId,
            'myAttendance'     => $myAttendance,
            'myAssignedShift'  => $myAssignedShift,
            'isManagerOrAdmin' => $isManagerOrAdmin,
        ]);
    }

    /**
     * Get current user's today attendance status (for topbar clock & quick clock-in)
     */
    public function myStatus(): ResponseInterface
    {
        $userId = (int) $this->currentUserId;
        $today = date('Y-m-d');
        $branchId = (int) ($this->currentBranchId ?: 1);

        $attendance = $this->attendanceModel
            ->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        $assignedShift = $this->staffShiftModel
            ->select('staff_shifts.*, shifts.name as shift_name, shifts.start_time, shifts.end_time, shifts.color_code')
            ->join('shifts', 'shifts.id = staff_shifts.shift_id', 'left')
            ->where('staff_shifts.user_id', $userId)
            ->where('staff_shifts.shift_date', $today)
            ->first();

        $activeShifts = $this->shiftModel->getActiveShifts($branchId);

        $isCheckedIn = !empty($attendance);
        $isCheckedOut = !empty($attendance['check_out']);
        $isOnDuty = $isCheckedIn && !$isCheckedOut;

        return $this->jsonSuccess([
            'user_id'         => $userId,
            'user_name'       => trim(($this->currentUser['first_name'] ?? '') . ' ' . ($this->currentUser['last_name'] ?? '')),
            'role_slug'       => $this->currentRoleSlug,
            'is_manager'      => in_array($this->currentRoleSlug, ['admin', 'manager'], true),
            'is_checked_in'   => $isCheckedIn,
            'is_checked_out'  => $isCheckedOut,
            'is_on_duty'      => $isOnDuty,
            'check_in_time'   => $isCheckedIn ? date('h:i A', strtotime($attendance['check_in'])) : null,
            'check_out_time'  => $isCheckedOut ? date('h:i A', strtotime($attendance['check_out'])) : null,
            'attendance_id'   => $attendance['id'] ?? null,
            'assigned_shift'  => $assignedShift,
            'active_shifts'   => $activeShifts,
            'total_hours'     => $attendance['total_hours'] ?? null,
        ]);
    }

    /**
     * Check-in / Clock-in staff member
     */
    public function checkIn(): ResponseInterface
    {
        $userId = (int) ($this->request->getVar('user_id') ?: $this->request->getPost('user_id'));
        if ($userId <= 0) {
            $userId = (int) $this->currentUserId;
        }

        // Only Manager and Super Admin can clock in other employees
        if ($userId !== (int) $this->currentUserId && !in_array($this->currentRoleSlug, ['admin', 'manager'], true)) {
            return $this->jsonError('Access denied. You can only clock in for yourself.', 403);
        }

        $shiftId = $this->request->getVar('shift_id') ? (int) $this->request->getVar('shift_id') : ($this->request->getPost('shift_id') ? (int) $this->request->getPost('shift_id') : null);
        $branchId = $this->currentBranchId ?? 1;
        $ip = $this->request->getIPAddress();

        // Auto-detect assigned shift for today if not provided
        if (!$shiftId) {
            $assigned = $this->staffShiftModel
                ->where('user_id', $userId)
                ->where('shift_date', date('Y-m-d'))
                ->first();
            if ($assigned) {
                $shiftId = (int) $assigned['shift_id'];
            }
        }

        $res = $this->attendanceModel->checkIn($userId, $branchId, $shiftId, $ip);

        if (!$res['status']) {
            return $this->jsonError($res['message'], 400);
        }

        return $this->jsonSuccess($res, $res['message']);
    }

    /**
     * Check-out staff member
     */
    public function checkOut(int $attendanceId): ResponseInterface
    {
        $att = $this->attendanceModel->find($attendanceId);
        if (!$att) {
            return $this->jsonError('Attendance record not found.', 404);
        }

        $targetUserId = (int) $att['user_id'];
        // Only Manager and Super Admin can check out other employees
        if ($targetUserId !== (int) $this->currentUserId && !in_array($this->currentRoleSlug, ['admin', 'manager'], true)) {
            return $this->jsonError('Access denied. You can only clock out for yourself.', 403);
        }

        $ip = $this->request->getIPAddress();
        $res = $this->attendanceModel->checkOut($attendanceId, $ip);

        if (!$res['status']) {
            return $this->jsonError($res['message'], 400);
        }

        return $this->jsonSuccess($res, $res['message']);
    }

    /**
     * Quick clock-out for currently logged in staff member
     */
    public function clockOutSelf(): ResponseInterface
    {
        $userId = (int) $this->currentUserId;
        $today = date('Y-m-d');

        $attendance = $this->attendanceModel
            ->where('user_id', $userId)
            ->where('date', $today)
            ->where('check_out IS NULL', null, false)
            ->first();

        if (!$attendance) {
            return $this->jsonError('No active clock-in session found for today.', 404);
        }

        $ip = $this->request->getIPAddress();
        $res = $this->attendanceModel->checkOut((int) $attendance['id'], $ip);
        if (!$res['status']) {
            return $this->jsonError($res['message'], 400);
        }

        return $this->jsonSuccess($res, $res['message']);
    }

    /**
     * Save / Create Shift (Super Admin and Manager ONLY)
     */
    public function saveShift(): ResponseInterface
    {
        // Only Super Admin and Manager can create or edit shifts
        if (!in_array($this->currentRoleSlug, ['admin', 'manager'], true)) {
            return $this->jsonError('Access denied. Only Managers and Super Admins can configure shifts.', 403);
        }

        $rules = [
            'name'       => 'required|min_length[2]|max_length[80]',
            'start_time' => 'required',
            'end_time'   => 'required',
        ];

        if ($errors = $this->validatePost($rules)) {
            return $this->jsonError('Validation error', 422, $errors);
        }

        try {
            $shiftId   = (int) $this->request->getPost('id');
            $startTime = trim((string) $this->request->getPost('start_time'));
            $endTime   = trim((string) $this->request->getPost('end_time'));

            // Normalize time values (handles HH:MM, HH:MM:SS, and 12-hr AM/PM formats)
            if ($startTime !== '' && strtotime($startTime) !== false) {
                $startTime = date('H:i:s', strtotime($startTime));
            }
            if ($endTime !== '' && strtotime($endTime) !== false) {
                $endTime = date('H:i:s', strtotime($endTime));
            }

            $data = [
                'restaurant_id'       => $this->currentRestaurantId ?? 1,
                'branch_id'           => $this->currentBranchId ?? 1,
                'name'                => trim((string) $this->request->getPost('name')),
                'start_time'          => $startTime,
                'end_time'            => $endTime,
                'break_duration_mins' => (int) ($this->request->getPost('break_duration_mins') ?: 30),
                'color_code'          => (string) ($this->request->getPost('color_code') ?: '#3b82f6'),
                'is_active'           => 1,
            ];

            if ($shiftId > 0) {
                $this->shiftModel->update($shiftId, $data);
                $msg = 'Shift updated successfully.';
            } else {
                $shiftId = (int) $this->shiftModel->insert($data);
                $msg = 'Shift created successfully.';
            }

            return $this->jsonSuccess(['id' => $shiftId], $msg);
        } catch (\Throwable $e) {
            log_message('error', 'Error saving shift: ' . $e->getMessage());
            return $this->jsonError('Unable to save shift: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Assign Shift to Staff Member (Super Admin and Manager ONLY)
     */
    public function assignShift(): ResponseInterface
    {
        // Only Super Admin and Manager can assign shifts
        if (!in_array($this->currentRoleSlug, ['admin', 'manager'], true)) {
            return $this->jsonError('Access denied. Only Managers and Super Admins can assign shifts.', 403);
        }

        $userId = (int) $this->request->getPost('user_id');
        $shiftId = (int) $this->request->getPost('shift_id');
        $date = (string) $this->request->getPost('shift_date');
        $notes = (string) $this->request->getPost('notes');
        $branchId = $this->currentBranchId ?? 1;

        if (!$userId || !$shiftId || !$date) {
            return $this->jsonError('Staff member, Shift, and Date are required.', 422);
        }

        try {
            $existing = $this->staffShiftModel
                ->where('user_id', $userId)
                ->where('shift_date', $date)
                ->first();

            if ($existing) {
                $this->staffShiftModel->update($existing['id'], [
                    'shift_id'   => $shiftId,
                    'branch_id'  => $branchId,
                    'status'     => 'scheduled',
                    'notes'      => $notes,
                ]);
                $id = (int) $existing['id'];
                $msg = 'Shift updated successfully.';
            } else {
                $id = (int) $this->staffShiftModel->insert([
                    'user_id'    => $userId,
                    'shift_id'   => $shiftId,
                    'branch_id'  => $branchId,
                    'shift_date' => $date,
                    'status'     => 'scheduled',
                    'notes'      => $notes,
                ]);
                $msg = 'Shift assigned successfully.';
            }

            return $this->jsonSuccess(['id' => $id], $msg);
        } catch (\Throwable $e) {
            log_message('error', 'Error assigning shift: ' . $e->getMessage());
            return $this->jsonError('Unable to assign shift: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Attendance and Overtime Reports (Super Admin and Manager ONLY)
     */
    public function reports(): string|ResponseInterface
    {
        if (!in_array($this->currentRoleSlug, ['admin', 'manager'], true)) {
            return redirect()->route('admin.attendance.index')->with('error', 'Access denied. Only Managers and Super Admins can view attendance reports.');
        }

        $branchId = $this->currentBranchId ?? 1;
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));

        $records = $this->attendanceModel->getAttendanceReport($branchId, $startDate, $endDate);

        // Check if CSV export requested
        if ($this->request->getGet('export') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="attendance_report_' . $startDate . '_to_' . $endDate . '.csv"');
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ['Staff Name', 'Email', 'Date', 'Shift', 'Check-In', 'Check-Out', 'Total Hours', 'Overtime Hours', 'Status']);
            
            foreach ($records as $r) {
                fputcsv($fp, [
                    $r['first_name'] . ' ' . $r['last_name'],
                    $r['email'],
                    $r['date'],
                    $r['shift_name'] ?? 'Regular',
                    $r['check_in'],
                    $r['check_out'] ?? 'Pending',
                    $r['total_hours'],
                    $r['overtime_hours'],
                    strtoupper($r['status']),
                ]);
            }
            fclose($fp);
            exit;
        }

        return $this->renderView('admin/attendance/reports', [
            'pageTitle' => 'Attendance & Overtime Reports',
            'records'   => $records,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }
}
