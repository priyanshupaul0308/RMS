<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\BranchModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * UserController – CRUD and lifecycle management for system users.
 */
class UserController extends BaseController
{
    protected UserModel   $userModel;
    protected RoleModel   $roleModel;
    protected BranchModel $branchModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->userModel   = new UserModel();
        $this->roleModel   = new RoleModel();
        $this->branchModel = new BranchModel();
    }

    // ── Index ──────────────────────────────────────────────────────────────

    public function index(): string
    {
        $this->authorize('users.view');

        $users = $this->userModel->getUsersWithDetails($this->currentRestaurantId);

        return $this->renderView('admin/users/index', [
            'pageTitle' => 'User Management',
            'users'     => $users,
        ]);
    }

    // ── Create form ────────────────────────────────────────────────────────

    public function create(): string
    {
        $this->authorize('users.create');

        return $this->renderView('admin/users/form', [
            'pageTitle' => 'Add New User',
            'user'      => [],
            'roles'     => $this->roleModel->getActiveRoles(),
            'branches'  => $this->branchModel->getActiveBranches($this->currentRestaurantId),
            'isEdit'    => false,
        ]);
    }

    // ── Store ──────────────────────────────────────────────────────────────

    public function store(): ResponseInterface
    {
        $this->authorize('users.create');

        $rules = [
            'first_name' => 'required|max_length[80]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[8]',
            'role_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'restaurant_id' => $this->currentRestaurantId,
            'branch_id'     => $this->request->getPost('branch_id') ?: null,
            'role_id'       => (int) $this->request->getPost('role_id'),
            'first_name'    => $this->request->getPost('first_name'),
            'last_name'     => $this->request->getPost('last_name'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'password'      => $this->request->getPost('password'),
            'employee_id'   => $this->request->getPost('employee_id'),
            'joining_date'  => $this->request->getPost('joining_date') ?: null,
            'is_active'     => 1,
        ];

        $newId = $this->userModel->insert($data);

        $this->userModel->writeAudit('users', 'create', 'users', (int) $newId, null, $data,
            "New user created: {$data['email']}");

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    // ── Edit form ──────────────────────────────────────────────────────────

    public function edit(int $id): string|ResponseInterface
    {
        $this->authorize('users.edit');

        $user = $this->userModel->find($id);
        if ($user === null || (int) $user['restaurant_id'] !== $this->currentRestaurantId) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        return $this->renderView('admin/users/form', [
            'pageTitle' => 'Edit User',
            'user'      => $user,
            'roles'     => $this->roleModel->getActiveRoles(),
            'branches'  => $this->branchModel->getActiveBranches($this->currentRestaurantId),
            'isEdit'    => true,
        ]);
    }

    // ── Update ─────────────────────────────────────────────────────────────

    public function update(int $id): ResponseInterface
    {
        $this->authorize('users.edit');

        $user = $this->userModel->find($id);
        if ($user === null || (int) $user['restaurant_id'] !== $this->currentRestaurantId) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }

        $rules = [
            'first_name' => 'required|max_length[80]',
            'email'      => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'branch_id'  => $this->request->getPost('branch_id') ?: null,
            'role_id'    => (int) $this->request->getPost('role_id'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'phone'      => $this->request->getPost('phone'),
            'employee_id'=> $this->request->getPost('employee_id'),
            'restaurant_id' => (int) ($user['restaurant_id'] ?: $this->currentRestaurantId),
            'id'            => $id,
        ];

        // Only update password if provided
        $newPassword = (string) $this->request->getPost('password');
        if (!empty($newPassword)) {
            $data['password'] = $newPassword;
        }

        // Controller has already performed complete validation including unique email ignore rule
        if (!$this->userModel->skipValidation(true)->update($id, $data)) {
            $errors = $this->userModel->errors();
            log_message('error', 'UserModel update failed for ID ' . $id . ': ' . json_encode($errors));
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->userModel->writeAudit('users', 'update', 'users', $id, $user, $data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    // ── Toggle active status ───────────────────────────────────────────────

    public function toggle(int $id): ResponseInterface
    {
        $this->authorize('users.delete');

        if ($id === $this->currentUserId) {
            return $this->jsonError('You cannot deactivate your own account.');
        }

        $this->userModel->toggleUserStatus($id, $this->currentUserId);
        return $this->jsonSuccess([], 'User status updated.');
    }

    // ── My Profile ────────────────────────────────────────────────────────

    public function profile(): string
    {
        $user = $this->userModel
            ->select('users.*, roles.name AS role_name, branches.name AS branch_name')
            ->join('roles',    'roles.id = users.role_id',      'left')
            ->join('branches', 'branches.id = users.branch_id', 'left')
            ->find($this->currentUserId);

        return $this->renderView('admin/users/profile', [
            'pageTitle' => 'My Profile',
            'user'      => $user,
        ]);
    }

    // ── Change Password ────────────────────────────────────────────────────

    public function changePassword(): ResponseInterface
    {
        $rules = [
            'current_password'  => 'required',
            'new_password'      => 'required|min_length[8]',
            'confirm_password'  => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $result = $this->userModel->changePassword(
            $this->currentUserId,
            $this->request->getPost('new_password'),
            $this->request->getPost('current_password')
        );

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('admin.users.profile')->with('success', 'Password changed successfully.');
    }
}
