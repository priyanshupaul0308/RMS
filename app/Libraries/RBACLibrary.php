<?php

declare(strict_types=1);

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * RBACLibrary – Role-Based Access Control engine.
 *
 * Resolves effective permissions for a user by merging:
 *   1. Role-level permissions (from role_permissions)
 *   2. User-level overrides (from user_permissions)
 *      – granted=1  → add permission
 *      – granted=0  → remove permission (deny override)
 *
 * Results are cached in session for performance.
 */
class RBACLibrary
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Resolve all effective permission slugs for a user.
     *
     * @return string[]
     */
    public function getUserPermissionSlugs(int $userId, int $roleId): array
    {
        // ── Role permissions ──
        $rolePerms = $this->db
            ->table('role_permissions rp')
            ->select('p.slug')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $roleId)
            ->get()
            ->getResultArray();

        $slugs = array_column($rolePerms, 'slug');

        // ── User overrides ──
        $overrides = $this->db
            ->table('user_permissions up')
            ->select('p.slug, up.granted')
            ->join('permissions p', 'p.id = up.permission_id')
            ->where('up.user_id', $userId)
            ->get()
            ->getResultArray();

        foreach ($overrides as $override) {
            if ((int) $override['granted'] === 1) {
                // Grant: add if not already present
                if (!in_array($override['slug'], $slugs, true)) {
                    $slugs[] = $override['slug'];
                }
            } else {
                // Deny: remove
                $slugs = array_filter($slugs, static fn ($s) => $s !== $override['slug']);
            }
        }

        return array_values($slugs);
    }

    /**
     * Check if a user has a specific permission.
     */
    public function userCan(int $userId, int $roleId, string $slug): bool
    {
        // Admins bypass everything
        $role = $this->db->table('roles')->where('id', $roleId)->get()->getRowArray();
        if ($role && $role['slug'] === 'admin') {
            return true;
        }
        return in_array($slug, $this->getUserPermissionSlugs($userId, $roleId), true);
    }

    /**
     * Get all permission IDs assigned to a role.
     *
     * @return int[]
     */
    public function getRolePermissions(int $roleId): array
    {
        $granted = $this->db
            ->table('role_permissions')
            ->select('permission_id')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($granted, 'permission_id'));
    }

    /**
     * Save role permissions (replace strategy).
     *
     * @param int[]  $permissionIds
     */
    public function saveRolePermissions(int $roleId, array $permissionIds): bool
    {
        $this->db->table('role_permissions')->where('role_id', $roleId)->delete();

        if (empty($permissionIds)) {
            return true;
        }

        $rows = array_map(
            static fn (int $pid) => ['role_id' => $roleId, 'permission_id' => $pid, 'granted_at' => date('Y-m-d H:i:s')],
            $permissionIds
        );

        return $this->db->table('role_permissions')->insertBatch($rows) !== false;
    }

    /**
     * Group all permissions by module for the permissions matrix UI.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getPermissionsGroupedByModule(): array
    {
        $perms = $this->db
            ->table('permissions')
            ->orderBy('module', 'ASC')
            ->orderBy('action', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];
        foreach ($perms as $perm) {
            $grouped[$perm['module']][] = $perm;
        }
        return $grouped;
    }

    // -------------------------------------------------------------------------
    // Session helpers
    // -------------------------------------------------------------------------

    /**
     * Populate session with user auth data post-login.
     */
    public function populateSession(array $user): void
    {
        $permissions = $this->getUserPermissionSlugs((int) $user['id'], (int) $user['role_id']);

        session()->set([
            'user_id'       => $user['id'],
            'restaurant_id' => $user['restaurant_id'],
            'branch_id'     => $user['branch_id'],
            'role_id'       => $user['role_id'],
            'role_slug'     => $user['role_slug'] ?? '',
            'user_name'     => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            'user_email'    => $user['email'],
            'user_avatar'   => $user['avatar'] ?? null,
            'permissions'   => $permissions,
            'last_activity' => time(),
            'last_db_check' => time(),
            'session_timeout' => 7200,
            'user'          => [
                'id'         => $user['id'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'email'      => $user['email'],
                'avatar'     => $user['avatar'] ?? null,
                'role_slug'  => $user['role_slug'] ?? '',
                'branch_id'  => $user['branch_id'],
            ],
        ]);
    }
}
