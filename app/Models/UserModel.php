<?php

declare(strict_types=1);

namespace App\Models;

use App\Libraries\RBACLibrary;
use CodeIgniter\I18n\Time;

/**
 * UserModel – Manages system users across all roles.
 */
class UserModel extends BaseModel
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'role_id', 'employee_id',
        'first_name', 'last_name', 'email', 'phone',
        'password', 'password_hash', 'avatar', 'gender', 'date_of_birth',
        'address', 'emergency_contact', 'emergency_phone',
        'joining_date', 'last_login_at', 'last_login_ip',
        'failed_login_count', 'locked_until', 'password_changed_at',
        'remember_token', 'email_verified_at', 'is_active',
        'deactivated_at', 'deactivated_by',
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    // ── Validation rules ──────────────────────────────────────────────────────

    protected $validationRules = [
        'first_name'    => 'required|max_length[80]',
        'email'         => 'required|valid_email|max_length[150]|is_unique[users.email,id,{id}]',
        'role_id'       => 'required|integer',
        'restaurant_id' => 'required|integer',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email address is already registered.',
        ],
    ];

    // ── Callbacks ─────────────────────────────────────────────────────────────

    protected $beforeInsert = ['hashPasswordIfSet'];
    protected $beforeUpdate = ['hashPasswordIfSet'];

    /**
     * Auto-hash the password field before insert/update.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function hashPasswordIfSet(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash(
                $data['data']['password'],
                PASSWORD_BCRYPT,
                ['cost' => 12]
            );
            unset($data['data']['password']);
        }
        return $data;
    }

    // ── Auth helpers ──────────────────────────────────────────────────────────

    /**
     * Attempt to authenticate a user by email and plain-text password.
     * Handles brute-force lockout logic.
     *
     * @return array{success: bool, user: array|null, message: string}
     */
    public function authenticate(string $email, string $password, string $ipAddress = ''): array
    {
        $maxAttempts  = (int) (config('App')->maxLoginAttempts  ?? 5);
        $lockoutMins  = (int) (config('App')->lockoutMinutes    ?? 15);

        $user = $this
            ->select('users.*, roles.slug AS role_slug')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.email', $email)
            ->first();

        if ($user === null) {
            return ['success' => false, 'user' => null, 'message' => 'Invalid email or password.'];
        }

        // ── Lockout check ──
        if ($user['locked_until'] !== null && strtotime($user['locked_until']) > time()) {
            $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
            return ['success' => false, 'user' => null, 'message' => "Account locked. Try again in {$remaining} minutes."];
        }

        // ── Inactive check ──
        if (!(bool) $user['is_active']) {
            return ['success' => false, 'user' => null, 'message' => 'Your account is deactivated.'];
        }

        // ── Password verify ──
        if (!password_verify($password, $user['password_hash'])) {
            $newCount = (int) $user['failed_login_count'] + 1;
            $update   = ['failed_login_count' => $newCount];

            if ($newCount >= $maxAttempts) {
                $update['locked_until'] = date('Y-m-d H:i:s', time() + ($lockoutMins * 60));
            }
            $this->update($user['id'], $update);

            $this->writeAudit('auth', 'login_failed', 'users', $user['id'], null, null,
                "Failed login attempt from {$ipAddress}");

            return ['success' => false, 'user' => null, 'message' => 'Invalid email or password.'];
        }

        // ── Success: reset counters, update last login ──
        $this->update($user['id'], [
            'failed_login_count' => 0,
            'locked_until'       => null,
            'last_login_at'      => date('Y-m-d H:i:s'),
            'last_login_ip'      => $ipAddress,
        ]);

        $this->writeAudit('auth', 'login', 'users', $user['id'], null, null,
            "Successful login from {$ipAddress}");

        return ['success' => true, 'user' => $user, 'message' => 'Login successful.'];
    }

    /**
     * Change a user password (validates old password first for self-change).
     */
    public function changePassword(int $userId, string $newPassword, ?string $oldPassword = null): array
    {
        $user = $this->find($userId);
        if ($user === null) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if ($oldPassword !== null && !password_verify($oldPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }

        $this->update($userId, [
            'password_hash'       => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
            'password_changed_at' => date('Y-m-d H:i:s'),
        ]);

        $this->writeAudit('users', 'password_changed', 'users', $userId);

        return ['success' => true, 'message' => 'Password changed successfully.'];
    }

    /**
     * Get full user list with role and branch names for admin view.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUsersWithDetails(int $restaurantId): array
    {
        return $this
            ->select('users.*, roles.name AS role_name, roles.slug AS role_slug, branches.name AS branch_name')
            ->join('roles',    'roles.id = users.role_id',      'left')
            ->join('branches', 'branches.id = users.branch_id', 'left')
            ->where('users.restaurant_id', $restaurantId)
            ->orderBy('users.first_name', 'ASC')
            ->findAll();
    }

    /**
     * Toggle user active status with audit.
     */
    public function toggleUserStatus(int $userId, int $deactivatedBy): bool
    {
        $user = $this->find($userId);
        if ($user === null) {
            return false;
        }

        $newStatus = (int) !$user['is_active'];
        $data = ['is_active' => $newStatus];

        if ($newStatus === 0) {
            $data['deactivated_at'] = date('Y-m-d H:i:s');
            $data['deactivated_by'] = $deactivatedBy;
        } else {
            $data['deactivated_at'] = null;
            $data['deactivated_by'] = null;
        }

        $this->update($userId, $data);

        $this->writeAudit(
            'users',
            $newStatus ? 'user_activated' : 'user_deactivated',
            'users',
            $userId,
            ['is_active' => $user['is_active']],
            ['is_active' => $newStatus]
        );

        return true;
    }
}
