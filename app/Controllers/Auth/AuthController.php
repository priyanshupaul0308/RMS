<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PasswordResetModel;
use App\Libraries\RBACLibrary;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthController – Handles login, logout, password reset.
 */
class AuthController extends BaseController
{
    protected UserModel  $userModel;
    protected RBACLibrary $rbac;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->userModel = new UserModel();
        $this->rbac      = new RBACLibrary();
    }

    // -------------------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------------------

    public function login(): string|ResponseInterface
    {
        if (session()->has('user_id')) {
            return redirect()->to(site_url('admin/dashboard'));
        }
        return view('auth/login', [
            'pageTitle' => 'Sign In – RMS',
            'csrf'      => $this->csrfData(),
        ]);
    }

    public function authenticate(): ResponseInterface
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email',    FILTER_SANITIZE_EMAIL);
        $password = $this->request->getPost('password', FILTER_DEFAULT);
        $ip       = $this->request->getIPAddress();

        $result = $this->userModel->authenticate($email, $password, $ip);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        // Populate session
        $this->rbac->populateSession($result['user']);

        return redirect()->to(site_url('admin/dashboard'))
            ->with('success', 'Welcome back, ' . $result['user']['first_name'] . '!');
    }

    // -------------------------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------------------------

    public function logout(): ResponseInterface
    {
        if (session()->has('user_id')) {
            $userId = session('user_id');
            /** @var \App\Models\BaseModel $model */
            $this->userModel->writeAudit('auth', 'logout', 'users', $userId);
        }

        session()->destroy();
        return redirect()->to(site_url('login'))->with('success', 'You have been logged out.');
    }

    // -------------------------------------------------------------------------
    // FORGOT PASSWORD
    // -------------------------------------------------------------------------

    public function forgotPassword(): string
    {
        return view('auth/forgot_password', ['pageTitle' => 'Forgot Password – RMS']);
    }

    public function sendResetLink(): ResponseInterface
    {
        $email = $this->request->getPost('email', FILTER_SANITIZE_EMAIL);
        $user  = $this->userModel->where('email', $email)->first();

        // Always show success to prevent user enumeration
        if ($user !== null && (bool) $user['is_active']) {
            $token = bin2hex(random_bytes(32));
            db_connect()->table('password_resets')->insert([
                'user_id'    => $user['id'],
                'token'      => hash('sha256', $token),
                'expires_at' => date('Y-m-d H:i:s', time() + 3600),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            try {
                $emailService = \Config\Services::email();
                $emailService->setTo($user['email']);
                $emailService->setSubject('Password Reset Request - RMS');
                $emailService->setMessage("Click the following link to reset your password:\n\n" . site_url("reset-password/{$token}") . "\n\nThis link will expire in 1 hour.");
                $emailService->send(false);
            } catch (\Throwable $e) {
                log_message('error', "Failed to dispatch reset email: " . $e->getMessage());
            }
            log_message('info', "Password reset token generated and dispatched for user {$user['id']}");
        }

        return redirect()->to(site_url('forgot-password'))
            ->with('success', 'If your email is registered, you will receive a reset link shortly.');
    }

    // -------------------------------------------------------------------------
    // RESET PASSWORD
    // -------------------------------------------------------------------------

    public function resetPassword(string $token): string|ResponseInterface
    {
        $hashedToken = hash('sha256', $token);
        $resetRow = db_connect()->table('password_resets')
            ->where('token', $hashedToken)
            ->where('used_at IS NULL')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get()->getRowArray();

        if ($resetRow === null) {
            return redirect()->to(site_url('login'))->with('error', 'Invalid or expired reset link.');
        }

        return view('auth/reset_password', [
            'pageTitle' => 'Reset Password – RMS',
            'token'     => $token,
        ]);
    }

    public function processReset(): ResponseInterface
    {
        $token       = $this->request->getPost('token');
        $hashedToken = hash('sha256', $token ?? '');

        $resetRow = db_connect()->table('password_resets')
            ->where('token', $hashedToken)
            ->where('used_at IS NULL')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get()->getRowArray();

        if ($resetRow === null) {
            return redirect()->to(site_url('login'))->with('error', 'Invalid or expired reset link.');
        }

        $rules = [
            'password'         => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->userModel->changePassword(
            (int) $resetRow['user_id'],
            $this->request->getPost('password')
        );

        if ($result['success']) {
            db_connect()->table('password_resets')
                ->where('id', $resetRow['id'])
                ->update(['used_at' => date('Y-m-d H:i:s')]);
        }

        return redirect()->to(site_url('login'))->with('success', 'Password reset successfully. Please log in.');
    }
}
