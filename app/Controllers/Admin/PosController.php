<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FloorModel;
use App\Models\TableModel;
use App\Models\MenuCategoryModel;
use App\Models\MenuItemModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\KotModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PosController – Front-of-House Point of Sale & Order Engine.
 */
class PosController extends BaseController
{
    protected FloorModel        $floorModel;
    protected TableModel        $tableModel;
    protected MenuCategoryModel $categoryModel;
    protected MenuItemModel     $itemModel;
    protected OrderModel        $orderModel;
    protected OrderItemModel    $orderItemModel;
    protected KotModel          $kotModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->floorModel     = new FloorModel();
        $this->tableModel     = new TableModel();
        $this->categoryModel  = new MenuCategoryModel();
        $this->itemModel      = new MenuItemModel();
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->kotModel       = new KotModel();
    }

    /**
     * Main POS Terminal Interface.
     */
    public function index(): string
    {
        $this->authorize('pos.access');

        $floors     = $this->floorModel->getFloorsWithTables($this->currentBranchId);
        $categories = $this->categoryModel->getActiveCategoriesWithCount($this->currentRestaurantId);
        $menuItems  = $this->itemModel->getItemsForPos();

        $taxRates = db_connect()->table('tax_rates')
            ->where('restaurant_id', $this->currentRestaurantId)
            ->where('is_active', 1)
            ->get()->getResultArray();

        $paymentMethods = db_connect()->table('payment_methods')
            ->where('restaurant_id', $this->currentRestaurantId)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $activeCoupons = db_connect()->table('coupons')
            ->where('restaurant_id', $this->currentRestaurantId)
            ->where('is_active', 1)
            ->where('end_date >=', date('Y-m-d'))
            ->orderBy('min_order_amount', 'ASC')
            ->get()->getResultArray();

        return $this->renderView('admin/pos/index', [
            'pageTitle'      => 'POS Terminal',
            'floors'         => $floors,
            'categories'     => $categories,
            'menuItems'      => $menuItems,
            'taxRates'       => $taxRates,
            'paymentMethods' => $paymentMethods,
            'activeCoupons'  => $activeCoupons,
        ]);
    }

    /**
     * AJAX endpoint: Create a new order or append items to an active table tab.
     */
    public function createOrder(): ResponseInterface
    {
        $this->authorize('orders.create');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['items'])) {
            return $this->jsonError('Cart is empty. Please add items to place an order.', 400);
        }

        try {
            $orderService = new \App\Services\OrderService();
            $result = $orderService->placeOrder(
                $json,
                (int)$this->currentRestaurantId,
                (int)($this->currentBranchId ?: 1),
                (int)$this->currentUserId
            );

            // Push in-app notification
            try {
                (new \App\Models\NotificationModel())->createNotification(
                    null,
                    'order',
                    "New Order #{$result['order_number']}",
                    "New order placed (KOT: {$result['kot_number']})",
                    ['order_id' => $result['order_id'] ?? null, 'order_number' => $result['order_number']],
                    (int)($this->currentBranchId ?: 1)
                );
            } catch (\Throwable $ne) {
                log_message('error', 'Notification dispatch failed: ' . $ne->getMessage());
            }

            return $this->jsonSuccess($result, "Order {$result['order_number']} created successfully! (KOT: {$result['kot_number']})");
        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($e->getMessage(), 400);
        } catch (\Throwable $e) {
            log_message('error', 'POS order placement failed: ' . $e->getMessage());
            return $this->jsonError('Failed to place order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * AJAX endpoint: Fetch active order on a table.
     */
    public function getTableOrder(int $tableId): ResponseInterface
    {
        $this->authorize('orders.view');

        $table = $this->tableModel->find($tableId);
        $reservation = db_connect()->table('reservations')
            ->where('table_id', $tableId)
            ->where('reservation_date', date('Y-m-d'))
            ->whereIn('status', ['seated', 'confirmed'])
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if (!$table || empty($table['current_order_id'])) {
            return $this->jsonSuccess([
                'active'      => false,
                'table'       => $table,
                'reservation' => $reservation,
            ]);
        }

        $order = $this->orderModel->getOrderWithDetails((int) $table['current_order_id']);
        if (!$order) {
            return $this->jsonSuccess([
                'active'      => false,
                'table'       => $table,
                'reservation' => $reservation,
            ]);
        }

        return $this->jsonSuccess([
            'active'      => true,
            'table'       => $table,
            'order'       => $order,
            'reservation' => $reservation,
        ]);
    }

    /**
     * AJAX endpoint: Validate a promotional coupon against bill amount.
     */
    public function validateCoupon(): ResponseInterface
    {
        $this->authorize('pos.access');

        $json = $this->request->getJSON(true) ?? [];
        $code = trim((string)($json['code'] ?? ''));
        $orderAmount = (float)($json['order_amount'] ?? 0);
        $customerPhone = trim((string)($json['customer_phone'] ?? ''));

        if (empty($code)) {
            return $this->jsonError('Please enter a coupon code.', 400);
        }

        $couponModel = new \App\Models\CouponModel();
        $customerId = null;
        if (!empty($customerPhone)) {
            $customer = db_connect()->table('customers')->where('phone', $customerPhone)->get()->getRowArray();
            if ($customer) {
                $customerId = (int)$customer['id'];
            }
        }

        $result = $couponModel->validateCoupon($code, $orderAmount, $customerId);
        if (!$result['valid']) {
            return $this->jsonError($result['message'], 400);
        }

        return $this->jsonSuccess([
            'coupon_id'       => (int)$result['coupon']['id'],
            'code'            => $result['coupon']['code'],
            'name'            => $result['coupon']['name'],
            'type'            => $result['coupon']['type'],
            'value'           => (float)$result['coupon']['value'],
            'discount_amount' => (float)$result['discount'],
            'message'         => "Coupon '{$result['coupon']['code']}' applied! Saved ₹" . number_format((float)$result['discount'], 2),
        ]);
    }

    /**
     * AJAX endpoint: Look up customer by phone number or search query with loyalty info.
     */
    public function lookupCustomer(): ResponseInterface
    {
        $this->authorize('pos.access');

        $phone = trim((string)($this->request->getGet('phone') ?? ''));
        $query = trim((string)($this->request->getGet('query') ?? ''));

        if (empty($phone) && empty($query)) {
            return $this->jsonError('Please provide a phone number or search query.', 400);
        }

        $restaurantId = $this->currentRestaurantId ?: 1;
        $customerModel = new \App\Models\CustomerModel();

        $builder = $customerModel->where('restaurant_id', $restaurantId);
        if (!empty($phone)) {
            $builder->where('phone', $phone);
        } else {
            $builder->groupStart()
                ->like('phone', $query)
                ->orLike('name', $query)
                ->groupEnd();
        }

        $customer = $builder->first();

        if (!$customer) {
            return $this->jsonSuccess([
                'exists'   => false,
                'customer' => null,
                'message'  => 'New Customer (No previous visits)'
            ]);
        }

        return $this->jsonSuccess([
            'exists'   => true,
            'customer' => [
                'id'             => (int)$customer['id'],
                'name'           => $customer['name'],
                'phone'          => $customer['phone'],
                'email'          => $customer['email'],
                'loyalty_points' => (int)$customer['loyalty_points'],
                'points_value'   => (float)$customer['loyalty_points'], // 1 Point = ₹1.00 discount
                'total_visits'   => (int)$customer['total_visits'],
                'lifetime_spend' => (float)$customer['lifetime_spend'],
                'vip_status'     => (int)$customer['vip_status'],
            ],
            'message'  => 'Returning Customer Found'
        ]);
    }

    /**
     * AJAX endpoint: Settle / Pay & complete order and release table with discount & loyalty points redemption support.
     */
    public function completeOrder(int $orderId): ResponseInterface
    {
        $this->authorize('billing.create');

        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return $this->jsonError('Order not found.', 404);
        }

        $json            = $this->request->getJSON(true) ?? [];
        $paymentMethodId = !empty($json['payment_method_id']) ? (int)$json['payment_method_id'] : 1;
        $discountAmount  = max(0.0, (float)($json['discount_amount'] ?? 0));
        $couponId        = !empty($json['coupon_id']) ? (int)$json['coupon_id'] : null;
        $couponCode      = !empty($json['coupon_code']) ? trim((string)$json['coupon_code']) : null;
        $discountReason  = !empty($json['discount_reason']) ? trim((string)$json['discount_reason']) : null;
        $redeemedPoints  = max(0, (int)($json['redeemed_points'] ?? 0));

        $discountData = [
            'discount_amount' => $discountAmount,
            'coupon_id'       => $couponId,
            'coupon_code'     => $couponCode,
            'discount_reason' => $discountReason,
            'redeemed_points' => $redeemedPoints,
        ];

        $netPayable = max(0.0, (float)$order['final_total'] - $discountAmount);

        try {
            $billingService = new \App\Services\BillingService();
            $payments = [
                [
                    'payment_method_id' => $paymentMethodId,
                    'amount'            => $netPayable,
                    'reference_number'  => 'POS-SETTLE-' . time(),
                ]
            ];
            $result = $billingService->settleOrder(
                $orderId,
                $payments,
                (int)$this->currentUserId,
                (int)($this->currentBranchId ?: 1),
                $discountData
            );

            $discMsg = $discountAmount > 0 ? " (Discount: ₹" . number_format($discountAmount, 2) . ($redeemedPoints > 0 ? " via {$redeemedPoints} pts" : "") . ")" : "";

            // Push in-app notification
            try {
                (new \App\Models\NotificationModel())->createNotification(
                    null,
                    'payment',
                    "Order #{$order['order_number']} Settled",
                    "Payment of ₹" . number_format($netPayable, 2) . " completed via POS.{$discMsg}",
                    ['order_id' => $orderId, 'order_number' => $order['order_number'], 'amount' => $netPayable],
                    (int)($this->currentBranchId ?: 1)
                );
            } catch (\Throwable $ne) {
                log_message('error', 'Notification dispatch failed: ' . $ne->getMessage());
            }

            return $this->jsonSuccess($result, "Order {$order['order_number']} settled successfully for ₹" . number_format($netPayable, 2) . "{$discMsg}!");
        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($e->getMessage(), 400);
        } catch (\Throwable $e) {
            log_message('error', 'POS settlement failed: ' . $e->getMessage());
            return $this->jsonError('Failed to settle order: ' . $e->getMessage(), 500);
        }
    }
}
