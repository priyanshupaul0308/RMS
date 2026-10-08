<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\RBACLibrary;

/**
 * BaseController – Foundation for all RMS controllers.
 *
 * Initialises shared services and provides:
 *  - Current user context (from session)
 *  - RBAC permission checks
 *  - Structured JSON response helpers
 *  - View rendering with common layout data
 *  - Audit logging trigger
 */
abstract class BaseController extends Controller
{
    /**
     * Helpers to auto-load.
     */
    protected $helpers = ['url', 'form', 'html', 'text'];

    // -------------------------------------------------------------------------
    // Shared context (populated from session in initController)
    // -------------------------------------------------------------------------
    protected ?array $currentUser       = null;
    protected ?int   $currentUserId     = null;
    protected ?int   $currentRestaurantId = null;
    protected ?int   $currentBranchId   = null;
    protected ?string $currentRoleSlug  = null;
    protected array   $userPermissions  = [];

    /** @var RBACLibrary */
    protected RBACLibrary $rbac;

    // -------------------------------------------------------------------------
    // initController
    // -------------------------------------------------------------------------
    public function initController(
        RequestInterface  $request,
        ResponseInterface $response,
        LoggerInterface   $logger
    ): void {
        parent::initController($request, $response, $logger);

        $this->rbac = new RBACLibrary();

        // Populate from session
        if (session()->has('user_id')) {
            $this->currentUserId        = (int) session('user_id');
            $this->currentRestaurantId  = (int) session('restaurant_id');
            $this->currentBranchId      = session('branch_id') ? (int) session('branch_id') : null;
            $this->currentRoleSlug      = session('role_slug');
            $this->currentUser          = session('user') ?? [];

            // Dynamically refresh permissions from DB for live permission updates
            $roleId = session('role_id') ?? ($this->currentUser['role_id'] ?? null);
            if ($roleId) {
                $this->userPermissions = $this->rbac->getUserPermissionSlugs($this->currentUserId, (int) $roleId);
                session()->set('permissions', $this->userPermissions);
            } else {
                $this->userPermissions = session('permissions') ?? [];
            }
        }
    }

    // -------------------------------------------------------------------------
    // Permission helpers
    // -------------------------------------------------------------------------

    /**
     * Check if the current user has a permission slug.
     * Admins bypass all checks.
     */
    protected function can(string $permissionSlug): bool
    {
        if ($this->currentRoleSlug === 'admin') {
            return true;
        }
        return in_array($permissionSlug, $this->userPermissions, true);
    }

    /**
     * Abort with 403 if the current user lacks a permission.
     */
    protected function authorize(string $permissionSlug): void
    {
        if (!$this->can($permissionSlug)) {
            if ($this->request->isAJAX()) {
                $this->jsonError("Access denied. You do not have permission ({$permissionSlug}).", 403)->send();
                exit;
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException(
                'You do not have permission to access this resource.'
            );
        }
    }

    // -------------------------------------------------------------------------
    // JSON Response helpers
    // -------------------------------------------------------------------------

    /**
     * Return a JSON success response.
     *
     * @param mixed $data
     */
    protected function jsonSuccess(mixed $data = [], string $message = 'Success', int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setContentType('application/json')
            ->setStatusCode($statusCode)
            ->setJSON([
                'status'  => 'success',
                'success' => true,
                'message' => $message,
                'data'    => $data,
            ]);
    }

    /**
     * Return a JSON error response.
     */
    protected function jsonError(string $message = 'Error', int $statusCode = 400, mixed $errors = []): ResponseInterface
    {
        return $this->response
            ->setContentType('application/json')
            ->setStatusCode($statusCode)
            ->setJSON([
                'status'  => 'error',
                'success' => false,
                'message' => $message,
                'errors'  => $errors,
            ]);
    }

    // -------------------------------------------------------------------------
    // View rendering helper
    // -------------------------------------------------------------------------

    /**
     * Render a view with a shared data set injected automatically.
     *
     * @param string               $view
     * @param array<string, mixed> $data
     * @param string               $layout  Layout file path
     */
    protected function renderView(string $view, array $data = [], string $layout = 'layouts/main'): string
    {
        $liveOrdersCount = 0;
        try {
            if ($this->currentUser) {
                $db = db_connect();
                $b = $db->table('orders')
                    ->whereIn('status', ['confirmed', 'preparing', 'ready']);

                if (!empty($this->currentBranchId)) {
                    $b->where('branch_id', (int)$this->currentBranchId);
                } elseif (!empty($this->currentRestaurantId)) {
                    $b->where('restaurant_id', (int)$this->currentRestaurantId);
                }
                $liveOrdersCount = (int)$b->countAllResults();
            }
        } catch (\Throwable $e) {
            $liveOrdersCount = 0;
        }

        $sharedData = [
            'currentUser'         => $this->currentUser,
            'currentUserId'       => $this->currentUserId,
            'currentRoleSlug'     => $this->currentRoleSlug,
            'currentRestaurantId' => $this->currentRestaurantId,
            'currentBranchId'     => $this->currentBranchId,
            'userPermissions'     => $this->userPermissions,
            'appVersion'          => config('App')->appVersion,
            'liveOrdersCount'     => $liveOrdersCount,
        ];

        $data = array_merge($sharedData, $data);
        $data['contentView'] = $view;

        return view($layout, $data);
    }

    // -------------------------------------------------------------------------
    // Validation helper
    // -------------------------------------------------------------------------

    /**
     * Validate POST data; return errors array or empty on pass.
     *
     * @param array<string, mixed> $rules
     * @return array<string, string>
     */
    protected function validatePost(array $rules): array
    {
        if (!$this->validate($rules)) {
            return $this->validator->getErrors();
        }
        return [];
    }

    // -------------------------------------------------------------------------
    // Redirect with flash helpers
    // -------------------------------------------------------------------------

    protected function redirectSuccess(string $route, string $message): ResponseInterface
    {
        return redirect()->route($route)->with('success', $message);
    }

    protected function redirectError(string $route, string $message): ResponseInterface
    {
        return redirect()->route($route)->with('error', $message);
    }

    // -------------------------------------------------------------------------
    // CSRF token helper for AJAX views
    // -------------------------------------------------------------------------

    /**
     * Return CSRF data array for embedding in views.
     *
     * @return array<string, string>
     */
    protected function csrfData(): array
    {
        return [
            csrf_token() => csrf_hash(),
        ];
    }
}
