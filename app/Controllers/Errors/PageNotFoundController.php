<?php

declare(strict_types=1);

namespace App\Controllers\Errors;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PageNotFoundController - Handles 404 Not Found and 403 Forbidden overrides
 */
class PageNotFoundController extends BaseController
{
    /**
     * Render a branded 404 / 403 error page.
     */
    public function index(): string|ResponseInterface
    {
        if ($this->request->isAJAX()) {
            return $this->jsonError('The requested resource was not found or access is restricted.', 404);
        }

        $this->response->setStatusCode(404);

        return $this->renderView('errors/html/error_404_custom', [
            'pageTitle' => 'Page Not Found – RMS',
        ], 'layouts/main');
    }
}
