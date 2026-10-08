<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BranchModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * BranchController – Branch CRUD and management.
 */
class BranchController extends BaseController
{
    protected BranchModel $branchModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->branchModel = new BranchModel();
    }

    public function index(): string
    {
        $this->authorize('branches.view');

        $branches = $this->branchModel->getBranchesForRestaurant($this->currentRestaurantId);

        return $this->renderView('admin/branches/index', [
            'pageTitle' => 'Branch Management',
            'branches'  => $branches,
        ]);
    }

    public function create(): string
    {
        $this->authorize('branches.create');

        return $this->renderView('admin/branches/form', [
            'pageTitle' => 'Add Branch',
            'branch'    => [],
            'isEdit'    => false,
        ]);
    }

    public function store(): ResponseInterface
    {
        $this->authorize('branches.create');

        $rules = [
            'name'  => 'required|max_length[150]',
            'code'  => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = array_merge($this->request->getPost(), [
            'restaurant_id' => $this->currentRestaurantId,
        ]);

        $newId = $this->branchModel->insert($data);
        $this->branchModel->writeAudit('branches', 'create', 'branches', (int) $newId, null, $data);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully.');
    }

    public function edit(int $id): string|ResponseInterface
    {
        $this->authorize('branches.edit');

        $branch = $this->branchModel->find($id);
        if ($branch === null || (int) $branch['restaurant_id'] !== $this->currentRestaurantId) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found.');
        }

        return $this->renderView('admin/branches/form', [
            'pageTitle' => 'Edit Branch',
            'branch'    => $branch,
            'isEdit'    => true,
        ]);
    }

    public function update(int $id): ResponseInterface
    {
        $this->authorize('branches.edit');

        $branch = $this->branchModel->find($id);
        if ($branch === null || (int) $branch['restaurant_id'] !== $this->currentRestaurantId) {
            return redirect()->route('admin.branches.index')->with('error', 'Branch not found.');
        }

        $data = $this->request->getPost();
        unset($data['restaurant_id']); // Never allow changing restaurant

        $this->branchModel->update($id, $data);
        $this->branchModel->writeAudit('branches', 'update', 'branches', $id, $branch, $data);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully.');
    }

    public function toggle(int $id): ResponseInterface
    {
        $this->authorize('branches.delete');

        $this->branchModel->toggleActive($id);
        return $this->jsonSuccess([], 'Branch status updated.');
    }
}
