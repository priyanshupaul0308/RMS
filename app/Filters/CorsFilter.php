<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CorsFilter – Adds CORS headers for API endpoints.
 */
class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        if ($request->getMethod() === 'options') {
            return service('response')
                ->setHeader('Access-Control-Allow-Origin',  '*')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Authorization')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->setStatusCode(200);
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        return $response
            ->setHeader('Access-Control-Allow-Origin',  '*')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-TOKEN, Authorization')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
