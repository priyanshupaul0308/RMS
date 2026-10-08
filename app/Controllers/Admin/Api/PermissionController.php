<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PermissionController - API endpoint for checking user permissions
 */
class PermissionController extends BaseController
{
    /**
     * Get permission slugs for a user
     */
    public function user(int $userId): ResponseInterface
    {
        $this->authorize('roles.view');

        $user = db_connect()->table('users')->where('id', $userId)->get()->getRowArray();
        if (!$user) {
            return $this->jsonError('User not found.', 404);
        }

        $perms = $this->rbac->getUserPermissionSlugs($userId, (int) $user['role_id']);

        return $this->jsonSuccess(['user_id' => $userId, 'permissions' => $perms]);
    }
}
