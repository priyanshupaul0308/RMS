<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RBACFilter – Route-level permission enforcement middleware.
 *
 * Usage in routes:
 *   $routes->get('admin/users', 'UserController::index', ['filter' => 'rbac:users.view']);
 *
 * The permission slug is passed as the first argument.
 */
class RBACFilter implements FilterInterface
{
    /**
     * @param array<string>|null $arguments  e.g. ['users.view']
     */
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        $session = session();

        if (!$session->has('user_id')) {
            return $this->denyAccess($request, 'Authentication required.');
        }

        // Admins always pass
        if ($session->get('role_slug') === 'admin') {
            return null;
        }

        // No specific permission required
        if (empty($arguments)) {
            return null;
        }

        $requiredPermission = $arguments[0];
        $userPermissions    = $session->get('permissions') ?? [];

        if (!in_array($requiredPermission, $userPermissions, true)) {
            return $this->denyAccess($request, "Access denied: '{$requiredPermission}' permission required.");
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        return $response;
    }

    // -------------------------------------------------------------------------

    private function denyAccess(RequestInterface $request, string $message): ResponseInterface
    {
        if ($request->isAJAX()) {
            return service('response')
                ->setContentType('application/json')
                ->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => $message]);
        }

        session()->setFlashdata('error', $message);
        return redirect()->to(site_url('admin/dashboard'));
    }
}
