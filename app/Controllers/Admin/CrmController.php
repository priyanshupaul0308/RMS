<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\FeedbackModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CrmController
 * 
 * Phase 7: Customer Relationship Management (CRM), Loyalty Points & Guest Feedback.
 */
class CrmController extends BaseController
{
    protected CustomerModel $customerModel;
    protected FeedbackModel $feedbackModel;
    protected AuditLogModel $auditModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->customerModel = new CustomerModel();
        $this->feedbackModel = new FeedbackModel();
        $this->auditModel    = new AuditLogModel();
    }

    /**
     * Customer profiles directory
     */
    public function index(): string
    {
        $this->authorize('customers.view');

        $search = $this->request->getGet('search') ? (string)$this->request->getGet('search') : null;
        $customers = $this->customerModel->getCustomers($search, $this->currentRestaurantId ?: 1);

        $totalCustomers = count($customers);
        $totalVip = 0;
        $totalSpend = 0.0;
        foreach ($customers as $c) {
            if (!empty($c['vip_status'])) {
                $totalVip++;
            }
            $totalSpend += (float)$c['lifetime_spend'];
        }

        return $this->renderView('admin/crm/index', [
            'pageTitle'      => 'Customer Relationship Management (CRM)',
            'title'          => 'Customer Relationship Management (CRM)',
            'active_menu'    => 'crm',
            'customers'      => $customers,
            'totalCustomers' => $totalCustomers,
            'totalVip'       => $totalVip,
            'totalSpend'     => $totalSpend,
            'search'         => $search
        ]);
    }

    /**
     * Store new customer profile
     */
    public function store(): ResponseInterface
    {
        $this->authorize('customers.create');

        $rules = [
            'name'  => 'required|min_length[2]|max_length[120]',
            'phone' => 'required|min_length[5]|max_length[25]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $restaurantId = $this->currentRestaurantId ?: 1;
        $phone        = (string)$this->request->getPost('phone');
        $existing     = $this->customerModel->where('restaurant_id', $restaurantId)->where('phone', $phone)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Customer with phone {$phone} already exists!");
        }

        $name = (string)$this->request->getPost('name');
        $this->customerModel->insert([
            'restaurant_id'  => $restaurantId,
            'name'           => $name,
            'phone'          => $phone,
            'email'          => (string)$this->request->getPost('email'),
            'address'        => (string)$this->request->getPost('address'),
            'loyalty_points' => (int)($this->request->getPost('loyalty_points') ?? 50),
            'vip_status'     => $this->request->getPost('vip_status') ? 1 : 0,
            'notes'          => (string)$this->request->getPost('notes')
        ]);

        $this->auditModel->recordEvent('crm', 'create_customer', "Registered new customer {$name} ({$phone})", $this->currentUserId);

        return redirect()->to(site_url('admin/crm'))->with('success', "Customer '{$name}' registered successfully!");
    }

    /**
     * Loyalty points reward / redemption adjustment
     */
    public function adjustPoints(): ResponseInterface
    {
        $this->authorize('customers.edit');

        $customerId = (int)$this->request->getPost('customer_id');
        $delta      = (int)$this->request->getPost('points_delta');
        $reason     = (string)$this->request->getPost('reason');

        if (!$customerId || $delta === 0 || empty($reason)) {
            return redirect()->back()->with('error', 'Invalid loyalty points adjustment.');
        }

        $customer = $this->customerModel->find($customerId);
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        $ok = $this->customerModel->updatePoints($customerId, $delta, $reason);
        if ($ok) {
            $desc = ($delta > 0) ? "Awarded +{$delta} points" : "Redeemed {$delta} points";
            $this->auditModel->recordEvent('loyalty', 'adjust_points', "{$desc} for Customer #{$customerId}: {$reason}", $this->currentUserId);
            return redirect()->to(site_url('admin/crm'))->with('success', 'Loyalty points updated!');
        }

        return redirect()->back()->with('error', 'Failed to update points.');
    }

    /**
     * Customer Feedback & Ratings Dashboard
     */
    public function feedback(): string
    {
        $this->authorize('customers.view');

        $feedbackList = $this->feedbackModel->getFeedbackList(50);
        $metrics      = $this->feedbackModel->getSatisfactionMetrics();

        return $this->renderView('admin/crm/feedback', [
            'pageTitle'    => 'Guest Feedback & Reviews',
            'title'        => 'Guest Feedback & Reviews',
            'active_menu'  => 'crm',
            'feedbackList' => $feedbackList,
            'metrics'      => $metrics
        ]);
    }

    /**
     * Store feedback entry
     */
    public function storeFeedback(): ResponseInterface
    {
        $this->authorize('customers.create');

        $rules = [
            'customer_name' => 'required|min_length[2]|max_length[120]',
            'rating'        => 'required|in_list[1,2,3,4,5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Please provide a valid rating and name.');
        }

        $this->feedbackModel->insert([
            'customer_name'   => (string)$this->request->getPost('customer_name'),
            'customer_phone'  => (string)$this->request->getPost('customer_phone'),
            'rating'          => (int)$this->request->getPost('rating'),
            'food_rating'     => (int)($this->request->getPost('food_rating') ?? 5),
            'service_rating'  => (int)($this->request->getPost('service_rating') ?? 5),
            'ambience_rating' => (int)($this->request->getPost('ambience_rating') ?? 5),
            'comments'        => (string)$this->request->getPost('comments'),
            'created_at'      => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(site_url('admin/crm/feedback'))->with('success', 'Customer feedback logged successfully!');
    }
}
