<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * BranchScopeFilter – Enforces branch-level data fencing.
 *
 * If the URL contains a `branch_id` parameter that does not match
 * the user's assigned branch (for non-admin roles), the request is blocked.
 */
class BranchScopeFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        $session  = session();
        $roleSlug = $session->get('role_slug');

        // Admins and Managers with NULL branch access all branches
        if (in_array($roleSlug, ['admin', 'manager'], true) && $session->get('branch_id') === null) {
            return null;
        }

        $userBranchId    = (int) $session->get('branch_id');
        $requestBranchId = (int) $request->getGet('branch_id');

        if ($requestBranchId > 0 && $requestBranchId !== $userBranchId) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setContentType('application/json')
                    ->setStatusCode(403)
                    ->setJSON(['status' => 'error', 'message' => 'Branch access denied.']);
            }
            session()->setFlashdata('error', 'You do not have access to this branch.');
            return redirect()->to(site_url('admin/dashboard'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        return $response;
    }
}
