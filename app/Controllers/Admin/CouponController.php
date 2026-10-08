<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CouponModel;
use App\Models\CouponUsageModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CouponController - Manages discounts, promotional codes, usage limits and redemptions
 */
class CouponController extends BaseController
{
    protected CouponModel $couponModel;
    protected CouponUsageModel $usageModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->couponModel = new CouponModel();
        $this->usageModel  = new CouponUsageModel();
    }

    /**
     * Coupon List & Summary Dashboard
     */
    public function index(): string
    {
        $this->authorize('coupons.view');

        $coupons = $this->couponModel->orderBy('id', 'DESC')->findAll();

        $activeCount = 0;
        $totalRedemptions = 0;

        foreach ($coupons as $c) {
            if ((int) $c['is_active'] === 1 && $c['end_date'] >= date('Y-m-d')) {
                $activeCount++;
            }
            $totalRedemptions += (int) $c['times_used'];
        }

        // Total discount savings given
        $savingsRow = $this->usageModel->selectSum('discount_amount', 'total_savings')->first();
        $totalSavings = (float) ($savingsRow['total_savings'] ?? 0);

        return $this->renderView('admin/coupons/index', [
            'pageTitle'        => 'Discount & Coupon Management',
            'coupons'          => $coupons,
            'activeCount'      => $activeCount,
            'totalRedemptions' => $totalRedemptions,
            'totalSavings'     => $totalSavings,
        ]);
    }

    /**
     * Create or Update Coupon
     */
    public function store(): ResponseInterface
    {
        $this->authorize('coupons.manage');

        $rules = [
            'code'       => 'required|min_length[3]|max_length[40]',
            'name'       => 'required|min_length[3]|max_length[120]',
            'type'       => 'required|in_list[percentage,fixed]',
            'value'      => 'required|numeric|greater_than[0]',
            'start_date' => 'required',
            'end_date'   => 'required',
        ];

        if ($errors = $this->validatePost($rules)) {
            return $this->jsonError('Validation error', 422, $errors);
        }

        $id = (int) $this->request->getPost('id');
        $code = strtoupper(trim((string) $this->request->getPost('code')));

        // Check unique code
        $existing = $this->couponModel->where('code', $code)->first();
        if ($existing && (int) $existing['id'] !== $id) {
            return $this->jsonError("Coupon code '{$code}' already exists.", 422);
        }

        $data = [
            'restaurant_id'        => $this->currentRestaurantId ?? 1,
            'branch_id'            => $this->currentBranchId ?? 1,
            'code'                 => $code,
            'name'                 => trim((string) $this->request->getPost('name')),
            'type'                 => $this->request->getPost('type'),
            'value'                => (float) $this->request->getPost('value'),
            'min_order_amount'     => (float) ($this->request->getPost('min_order_amount') ?: 0),
            'max_discount_amount'  => $this->request->getPost('max_discount_amount') ? (float) $this->request->getPost('max_discount_amount') : null,
            'usage_limit_total'    => (int) ($this->request->getPost('usage_limit_total') ?: 100),
            'usage_limit_per_user' => (int) ($this->request->getPost('usage_limit_per_user') ?: 1),
            'start_date'           => $this->request->getPost('start_date'),
            'end_date'             => $this->request->getPost('end_date'),
            'is_active'            => (int) ($this->request->getPost('is_active') ?? 1),
        ];

        if ($id > 0) {
            $this->couponModel->update($id, $data);
            $msg = 'Coupon updated successfully.';
        } else {
            $data['times_used'] = 0;
            $id = (int) $this->couponModel->insert($data);
            $msg = 'Coupon created successfully.';
        }

        return $this->jsonSuccess(['id' => $id], $msg);
    }

    /**
     * Toggle active status
     */
    public function toggle(int $id): ResponseInterface
    {
        $this->authorize('coupons.manage');

        $coupon = $this->couponModel->find($id);
        if (!$coupon) {
            return $this->jsonError('Coupon not found.', 404);
        }

        $newStatus = (int) $coupon['is_active'] === 1 ? 0 : 1;
        $this->couponModel->update($id, ['is_active' => $newStatus]);

        return $this->jsonSuccess(
            ['is_active' => $newStatus],
            $newStatus === 1 ? 'Coupon activated.' : 'Coupon deactivated.'
        );
    }

    /**
     * AJAX Validate Coupon from POS
     */
    public function validateCode(): ResponseInterface
    {
        $code = (string) $this->request->getVar('code');
        $amount = (float) $this->request->getVar('amount');
        $customerId = $this->request->getVar('customer_id') ? (int) $this->request->getVar('customer_id') : null;

        if (!$code) {
            return $this->jsonError('Please provide a coupon code.', 422);
        }

        $result = $this->couponModel->validateCoupon($code, $amount, $customerId);

        if (!$result['valid']) {
            return $this->jsonError($result['message'], 400);
        }

        return $this->jsonSuccess($result, $result['message']);
    }

    /**
     * Discount & Redemptions Report
     */
    public function reports(): string
    {
        $this->authorize('coupons.view');

        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));

        $usages = $this->usageModel->select('coupon_usages.*, coupons.code, coupons.name as coupon_name, coupons.type, orders.order_number, orders.final_total as order_total')
            ->join('coupons', 'coupons.id = coupon_usages.coupon_id')
            ->join('orders', 'orders.id = coupon_usages.order_id', 'left')
            ->where('coupon_usages.used_at >=', $startDate . ' 00:00:00')
            ->where('coupon_usages.used_at <=', $endDate . ' 23:59:59')
            ->orderBy('coupon_usages.used_at', 'DESC')
            ->findAll();

        if ($this->request->getGet('export') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="coupon_report_' . $startDate . '_to_' . $endDate . '.csv"');
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ['Code', 'Coupon Name', 'Order Number', 'Order Total', 'Discount Given', 'Used At']);
            
            foreach ($usages as $u) {
                fputcsv($fp, [
                    $u['code'],
                    $u['coupon_name'],
                    $u['order_number'] ?? 'N/A',
                    $u['order_total'] ?? '0.00',
                    $u['discount_amount'],
                    $u['used_at'],
                ]);
            }
            fclose($fp);
            exit;
        }

        return $this->renderView('admin/coupons/reports', [
            'pageTitle' => 'Coupon & Discount Reports',
            'usages'    => $usages,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }
}
