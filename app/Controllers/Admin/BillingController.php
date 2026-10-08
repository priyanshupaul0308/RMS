<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderPaymentModel;
use App\Models\TableModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * BillingController – Multi-tender split settlements, tax engines, and payment ledgers.
 */
class BillingController extends BaseController
{
    protected OrderModel        $orderModel;
    protected OrderPaymentModel $paymentModel;
    protected TableModel        $tableModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->orderModel   = new OrderModel();
        $this->paymentModel = new OrderPaymentModel();
        $this->tableModel   = new TableModel();
    }

    /**
     * Payment transactions ledger.
     */
    public function transactions(): string
    {
        $this->authorize('billing.view');

        $payments = $this->paymentModel->select('order_payments.*, orders.order_number, orders.customer_name, payment_methods.name AS method_name, users.first_name AS cashier_name')
            ->join('orders',          'orders.id = order_payments.order_id', 'left')
            ->join('payment_methods', 'payment_methods.id = order_payments.payment_method_id', 'left')
            ->join('users',           'users.id = order_payments.received_by', 'left')
            ->orderBy('order_payments.id', 'DESC')
            ->findAll(100);

        return $this->renderView('admin/billing/transactions', [
            'pageTitle' => 'Payment Transactions',
            'payments'  => $payments,
        ]);
    }

    /**
     * Multi-Tender Split Billing settlement (e.g. part Cash + part Card/UPI).
     */
    public function splitPayment(): ResponseInterface
    {
        $this->authorize('billing.create');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['order_id']) || empty($json['splits'])) {
            return $this->jsonError('Invalid split payment request data.', 400);
        }

        $orderId = (int)$json['order_id'];
        $splits  = (array)$json['splits'];

        try {
            $billingService = new \App\Services\BillingService();
            $result = $billingService->settleOrder(
                $orderId,
                $splits,
                (int)$this->currentUserId,
                (int)($this->currentBranchId ?: 1)
            );

            return $this->jsonSuccess($result, "Split payment settled successfully for Order #{$result['order_number']}!");
        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($e->getMessage(), 400);
        } catch (\Throwable $e) {
            log_message('error', 'Split payment settlement failed: ' . $e->getMessage());
            return $this->jsonError('Failed to process split payment: ' . $e->getMessage(), 500);
        }
    }
}
