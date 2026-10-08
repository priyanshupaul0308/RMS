<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Libraries\RBACLibrary;

/**
 * AuthFilter – Protects routes requiring an authenticated session.
 *
 * Responsibilities:
 *  - Verify active session
 *  - Detect and enforce brute-force lockout expiry
 *  - Refresh session data if stale
 *  - Redirect unauthenticated requests to login
 *  - Enforce session timeout
 */
class AuthFilter implements FilterInterface
{
    /** Session timeout in seconds (overridden from settings) */
    private const DEFAULT_TIMEOUT = 7200;

    /**
     * Pre-request: validate authentication.
     *
     * @param array<string, mixed>|null $arguments
     * @return ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        $session = session();

        // ── 1. Check session exists ──────────────────────────────────────────
        if (!$session->has('user_id')) {
            return $this->redirectToLogin($request);
        }

        // ── 2. Enforce session timeout ───────────────────────────────────────
        $timeout = (int) ($session->get('session_timeout') ?? self::DEFAULT_TIMEOUT);
        $lastActivity = (int) ($session->get('last_activity') ?? 0);

        if ($lastActivity > 0 && (time() - $lastActivity) > $timeout) {
            $session->destroy();
            return $this->redirectToLogin($request, 'Your session has expired. Please log in again.');
        }

        // ── 3. Update last activity ───────────────────────────────────────────
        $session->set('last_activity', time());

        // ── 4. Verify user still active in DB (every 60 s to reduce DB hits) ─
        $lastDbCheck = (int) ($session->get('last_db_check') ?? 0);

        if ((time() - $lastDbCheck) > 60) {
            $userModel = new UserModel();
            $user = $userModel->find((int) $session->get('user_id'));

            if ($user === null || !(bool) $user['is_active']) {
                $session->destroy();
                return $this->redirectToLogin($request, 'Your account has been deactivated.');
            }

            // ── 5. Check lockout ─────────────────────────────────────────────
            if ($user['locked_until'] !== null && strtotime($user['locked_until']) > time()) {
                $session->destroy();
                return $this->redirectToLogin($request, 'Your account is temporarily locked.');
            }

            // ── 6. Refresh permissions in session ────────────────────────────
            $rbac = new RBACLibrary();
            $permissions = $rbac->getUserPermissionSlugs((int) $user['id'], (int) $user['role_id']);
            $session->set('permissions', $permissions);
            $session->set('last_db_check', time());
        }

        return null; // Continue request
    }

    /**
     * Post-request (no-op for auth).
     *
     * @param array<string, mixed>|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        return $response;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function redirectToLogin(RequestInterface $request, string $message = ''): ResponseInterface
    {
        if ($request->isAJAX()) {
            return service('response')
                ->setContentType('application/json')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => $message ?: 'Unauthenticated.',
                    'redirect'=> site_url('login'),
                ]);
        }

        if ($message) {
            session()->setFlashdata('error', $message);
        }

        return redirect()->to(site_url('login'));
    }
}
