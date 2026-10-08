<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Libraries\RBACLibrary;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RoleController – Role & Permission matrix management.
 */
class RoleController extends BaseController
{
    protected RoleModel   $roleModel;
    protected RBACLibrary $rbac;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->roleModel = new RoleModel();
        $this->rbac      = new RBACLibrary();
    }

    public function index(): string
    {
        $this->authorize('roles.view');

        $roles = $this->roleModel->findAll();

        return $this->renderView('admin/roles/index', [
            'pageTitle' => 'Roles & Permissions',
            'roles'     => $roles,
        ]);
    }

    public function permissions(int $roleId): string
    {
        $this->authorize('roles.edit');

        $role               = $this->roleModel->find($roleId);
        $permissionMatrix   = $this->rbac->getPermissionsGroupedByModule();
        $rolePermissions    = $this->rbac->getRolePermissions($roleId);

        return $this->renderView('admin/roles/permissions', [
            'pageTitle'        => "Edit Permissions – {$role['name']}",
            'role'             => $role,
            'permissionMatrix' => $permissionMatrix,
            'rolePermissions'  => $rolePermissions,
        ]);
    }

    public function savePermissions(int $roleId): ResponseInterface
    {
        $this->authorize('roles.edit');

        // Protect admin role from modification
        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return $this->jsonError('Role not found.', 404);
        }
        if ($role['slug'] === 'admin') {
            return $this->jsonError('Administrator permissions cannot be modified.');
        }

        // Support JSON request payload (rmsPost), standard POST, or getVar
        $json = $this->request->getJSON(true);
        $raw = $json['permissions'] ?? $this->request->getPost('permissions') ?? $this->request->getVar('permissions') ?? [];
        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?? [];
        }
        $permissionIds = array_values(array_filter(
            array_map('intval', (array) $raw),
            static fn (int $id) => $id > 0
        ));

        $this->rbac->saveRolePermissions($roleId, $permissionIds);

        $this->roleModel->writeAudit('roles', 'permissions_updated', 'roles', $roleId, null, $permissionIds);

        return $this->jsonSuccess([
            'role_id'           => $roleId,
            'permissions_count' => count($permissionIds),
        ], 'Permissions saved successfully.');
    }
}
