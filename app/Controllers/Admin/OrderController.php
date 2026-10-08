<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\TableModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * OrderController – Order Pipeline, Live Kitchen Tracking, and Receipts.
 */
class OrderController extends BaseController
{
    protected OrderModel     $orderModel;
    protected OrderItemModel $orderItemModel;
    protected TableModel     $tableModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->tableModel     = new TableModel();
    }

    /**
     * Determine if current user is Super Admin, Manager, or Cashier.
     */
    protected function canAccessReportsAndMetrics(): bool
    {
        $roleSlug = strtolower((string)($this->currentRoleSlug ?? session('role_slug') ?? ''));
        $roleId   = (int)(session('role_id') ?? ($this->currentUser['role_id'] ?? 0));

        // Role IDs: 1 = Admin (Super Admin), 2 = Manager, 3 = Cashier
        if (in_array($roleId, [1, 2, 3], true)) {
            return true;
        }

        return in_array($roleSlug, ['admin', 'super_admin', 'manager', 'cashier'], true);
    }

    /**
     * Live Orders Pipeline & Date-wise Filtered Order Search.
     */
    public function index(): string|ResponseInterface
    {
        $this->authorize('orders.view');

        $canAccessReports = $this->canAccessReportsAndMetrics();

        // Check if CSV export requested via query parameter
        if ($this->request->getGet('export') === 'csv') {
            if (!$canAccessReports) {
                return redirect()->to(site_url('admin/orders'))->with('error', 'Access denied. Order reports and CSV downloads are restricted to Super Admin, Manager, and Cashier.');
            }
            $this->exportCsv();
            return '';
        }

        $branchId      = (int)($this->currentBranchId ?: 1);
        $status        = $this->request->getGet('status') ?: 'all';
        $type          = $this->request->getGet('type') ?: 'all';
        $paymentStatus = $this->request->getGet('payment_status') ?: 'all';
        $datePreset    = $this->request->getGet('date_preset') ?: ($canAccessReports ? 'all' : 'today');
        $fromDate      = $this->request->getGet('from_date');
        $toDate        = $this->request->getGet('to_date');
        $search        = trim((string)($this->request->getGet('search') ?? ''));

        // If not authorized for reports (e.g. Waiter / Chef), restrict strictly to today's active orders
        if (!$canAccessReports) {
            $datePreset = 'today';
            $fromDate   = date('Y-m-d');
            $toDate     = date('Y-m-d');
        } else {
            // Resolve date presets for Super Admin, Manager, Cashier
            if ($datePreset === 'today') {
                $fromDate = date('Y-m-d');
                $toDate   = date('Y-m-d');
            } elseif ($datePreset === 'yesterday') {
                $fromDate = date('Y-m-d', strtotime('-1 day'));
                $toDate   = date('Y-m-d', strtotime('-1 day'));
            } elseif ($datePreset === 'last_7_days') {
                $fromDate = date('Y-m-d', strtotime('-6 days'));
                $toDate   = date('Y-m-d');
            } elseif ($datePreset === 'this_month') {
                $fromDate = date('Y-m-01');
                $toDate   = date('Y-m-d');
            } elseif (!empty($fromDate) || !empty($toDate)) {
                if ($datePreset === 'all') {
                    $datePreset = 'custom';
                }
            }
        }

        $orders = $this->orderModel->getLiveOrders(
            $branchId,
            $status,
            $type,
            $fromDate ?: null,
            $toDate ?: null,
            $paymentStatus,
            $search ?: null
        );

        // Status counts reflecting the active date range & filters
        $db = db_connect();
        $countBuilder = $db->table('orders')
            ->select('orders.status, COUNT(DISTINCT orders.id) as cnt')
            ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left')
            ->where('orders.branch_id', $branchId);

        if (!empty($fromDate)) {
            $countBuilder->where('DATE(orders.created_at) >=', $fromDate);
        }
        if (!empty($toDate)) {
            $countBuilder->where('DATE(orders.created_at) <=', $toDate);
        }
        if (!empty($type) && $type !== 'all') {
            $countBuilder->where('orders.order_type', $type);
        }
        if (!empty($paymentStatus) && $paymentStatus !== 'all') {
            $countBuilder->where('orders.payment_status', $paymentStatus);
        }
        if (!empty($search)) {
            $countBuilder->groupStart()
                ->like('orders.order_number', $search)
                ->orLike('orders.customer_name', $search)
                ->orLike('orders.customer_phone', $search)
                ->orLike('restaurant_tables.table_number', $search)
                ->groupEnd();
        }
        $statusCounts = $countBuilder->groupBy('orders.status')->get()->getResultArray();

        $countsMap = [];
        $totalCount = 0;
        foreach ($statusCounts as $sc) {
            $countsMap[$sc['status']] = (int)$sc['cnt'];
            $totalCount += (int)$sc['cnt'];
        }

        $counts = [
            'all'       => $totalCount,
            'confirmed' => $countsMap['confirmed'] ?? 0,
            'preparing' => $countsMap['preparing'] ?? 0,
            'ready'     => $countsMap['ready'] ?? 0,
            'served'    => $countsMap['served'] ?? 0,
            'completed' => $countsMap['completed'] ?? 0,
            'cancelled' => $countsMap['cancelled'] ?? 0,
        ];

        // Summary financial calculations
        $totalRevenue   = 0.0;
        $paidRevenue    = 0.0;
        $pendingRevenue = 0.0;
        foreach ($orders as $o) {
            $amt = (float)($o['final_total'] ?? 0);
            $totalRevenue += $amt;
            if (($o['payment_status'] ?? '') === 'paid') {
                $paidRevenue += $amt;
            } else {
                $pendingRevenue += $amt;
            }
        }

        $paymentMethods = $db->table('payment_methods')->where('is_active', 1)->get()->getResultArray();
        $coupons        = $db->table('coupons')->where('is_active', 1)->get()->getResultArray();

        return $this->renderView('admin/orders/index', [
            'pageTitle'        => 'Live Orders Pipeline & Reports',
            'orders'           => $orders,
            'currentStatus'    => $status,
            'currentType'      => $type,
            'paymentStatus'    => $paymentStatus,
            'datePreset'       => $datePreset,
            'fromDate'         => $fromDate,
            'toDate'           => $toDate,
            'search'           => $search,
            'counts'           => $counts,
            'canAccessReports' => $canAccessReports,
            'metrics'          => [
                'totalOrders'    => count($orders),
                'totalRevenue'   => $totalRevenue,
                'paidRevenue'    => $paidRevenue,
                'pendingRevenue' => $pendingRevenue,
            ],
            'paymentMethods'   => $paymentMethods,
            'coupons'          => $coupons,
        ]);
    }

    /**
     * Download / Export Orders CSV Report based on current filters.
     * Strictly restricted to Super Admin, Manager, and Cashier.
     */
    public function exportCsv(): void
    {
        $this->authorize('orders.view');

        if (!$this->canAccessReportsAndMetrics()) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Access denied. Order reports and CSV downloads are strictly restricted to Super Admin, Manager, and Cashier.';
            exit;
        }

        $branchId      = (int)($this->currentBranchId ?: 1);
        $status        = $this->request->getGet('status') ?: 'all';
        $type          = $this->request->getGet('type') ?: 'all';
        $paymentStatus = $this->request->getGet('payment_status') ?: 'all';
        $datePreset    = $this->request->getGet('date_preset') ?: 'all';
        $fromDate      = $this->request->getGet('from_date');
        $toDate        = $this->request->getGet('to_date');
        $search        = trim((string)($this->request->getGet('search') ?? ''));

        if ($datePreset === 'today') {
            $fromDate = date('Y-m-d');
            $toDate   = date('Y-m-d');
        } elseif ($datePreset === 'yesterday') {
            $fromDate = date('Y-m-d', strtotime('-1 day'));
            $toDate   = date('Y-m-d', strtotime('-1 day'));
        } elseif ($datePreset === 'last_7_days') {
            $fromDate = date('Y-m-d', strtotime('-6 days'));
            $toDate   = date('Y-m-d');
        } elseif ($datePreset === 'this_month') {
            $fromDate = date('Y-m-01');
            $toDate   = date('Y-m-d');
        }

        $orders = $this->orderModel->getLiveOrders(
            $branchId,
            $status,
            $type,
            $fromDate ?: null,
            $toDate ?: null,
            $paymentStatus,
            $search ?: null
        );

        // Fetch ordered items summary for each order
        $orderIds = array_column($orders, 'id');
        $itemsByOrder = [];
        if (!empty($orderIds)) {
            $db = db_connect();
            $items = $db->table('order_items')
                ->select('order_id, item_name, quantity, unit_price, total')
                ->whereIn('order_id', $orderIds)
                ->get()
                ->getResultArray();
            foreach ($items as $it) {
                $qty = (float)$it['quantity'];
                $itemsByOrder[$it['order_id']][] = ($qty == (int)$qty ? (int)$qty : $qty) . 'x ' . $it['item_name'];
            }
        }

        $dateSuffix = '';
        if (!empty($fromDate) && !empty($toDate)) {
            $dateSuffix = '_' . $fromDate . '_to_' . $toDate;
        } elseif (!empty($fromDate)) {
            $dateSuffix = '_from_' . $fromDate;
        } else {
            $dateSuffix = '_all_' . date('Ymd');
        }

        $filename = 'Orders_Report' . $dateSuffix . '_' . date('His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $fp = fopen('php://output', 'w');
        // UTF-8 BOM for Microsoft Excel compatibility
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header Row
        fputcsv($fp, [
            'Order #',
            'Date',
            'Time',
            'Order Type',
            'Table / Location',
            'Customer Name',
            'Customer Phone',
            'Items Count',
            'Items Details',
            'Subtotal (INR)',
            'Tax (INR)',
            'Discount (INR)',
            'Service Charge (INR)',
            'Final Total (INR)',
            'Payment Status',
            'Payment Method',
            'Order Status',
        ]);

        foreach ($orders as $o) {
            $itemsList = !empty($itemsByOrder[$o['id']]) ? implode(' | ', $itemsByOrder[$o['id']]) : 'N/A';
            $tableLocation = !empty($o['table_number'])
                ? $o['table_number'] . (!empty($o['floor_name']) ? ' (' . $o['floor_name'] . ')' : '')
                : 'Counter / Direct';

            fputcsv($fp, [
                $o['order_number'],
                date('Y-m-d', strtotime($o['created_at'])),
                date('H:i:s', strtotime($o['created_at'])),
                ucwords(str_replace('_', ' ', (string)$o['order_type'])),
                $tableLocation,
                $o['customer_name'] ?: 'Guest',
                $o['customer_phone'] ?: 'N/A',
                (int)($o['total_items'] ?? 0),
                $itemsList,
                number_format((float)($o['subtotal'] ?? 0), 2, '.', ''),
                number_format((float)($o['tax_amount'] ?? 0), 2, '.', ''),
                number_format((float)($o['discount_amount'] ?? 0), 2, '.', ''),
                number_format((float)($o['service_charge'] ?? 0), 2, '.', ''),
                number_format((float)($o['final_total'] ?? 0), 2, '.', ''),
                strtoupper((string)($o['payment_status'] ?? 'unpaid')),
                !empty($o['payment_method_name']) ? ucwords($o['payment_method_name']) : 'N/A',
                ucwords((string)($o['status'] ?? '')),
            ]);
        }

        fclose($fp);
        exit;
    }

    /**
     * View Order Details & Items.
     */
    public function show(int $id): string|ResponseInterface
    {
        $this->authorize('orders.view');

        $order = $this->orderModel->getOrderWithDetails($id);
        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        $paymentMethods = db_connect()->table('payment_methods')->where('is_active', 1)->get()->getResultArray();
        $coupons        = db_connect()->table('coupons')->where('is_active', 1)->get()->getResultArray();

        return $this->renderView('admin/orders/show', [
            'pageTitle'      => "Order #{$order['order_number']}",
            'order'          => $order,
            'paymentMethods' => $paymentMethods,
            'coupons'        => $coupons,
        ]);
    }

    /**
     * Transition Order Status (e.g., preparing -> ready -> served -> completed).
     */
    public function updateStatus(int $id): ResponseInterface
    {
        $this->authorize('orders.edit');

        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->jsonError('Order not found.');
        }

        $post = (array)($this->request->getPost() ?? []);
        $json = [];
        try {
            $rawBody = (string)$this->request->getBody();
            if (!empty($rawBody) && (str_starts_with(ltrim($rawBody), '{') || str_starts_with(ltrim($rawBody), '['))) {
                $json = (array)($this->request->getJSON(true) ?? []);
            }
        } catch (\Throwable $e) {
            $json = [];
        }

        $newStatus = $post['status'] ?? $json['status'] ?? '';
        $allowed   = ['confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'];

        if (!in_array($newStatus, $allowed, true)) {
            return $this->jsonError('Invalid order status.');
        }

        $db = db_connect();
        $db->transStart();

        $updateData = ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')];
        if ($newStatus === 'completed') {
            $updateData['payment_status'] = 'paid';
            $methodId = !empty($post['payment_method_id'])
                ? (int)$post['payment_method_id']
                : (!empty($json['payment_method_id'])
                    ? (int)$json['payment_method_id']
                    : (!empty($order['payment_method_id']) ? (int)$order['payment_method_id'] : 1));
            $updateData['payment_method_id'] = $methodId;

            $discountAmount = max(0.0, (float)($post['discount_amount'] ?? $json['discount_amount'] ?? 0));
            $couponCode     = trim((string)($post['coupon_code'] ?? $json['coupon_code'] ?? ''));
            $couponId       = !empty($post['coupon_id']) ? (int)$post['coupon_id'] : (!empty($json['coupon_id']) ? (int)$json['coupon_id'] : null);
            $discountReason = trim((string)($post['discount_reason'] ?? $json['discount_reason'] ?? ''));
            $redeemedPoints = max(0, (int)($post['redeemed_points'] ?? $json['redeemed_points'] ?? 0));

            if (!$couponId && !empty($couponCode)) {
                $cp = $db->table('coupons')->where('code', strtoupper($couponCode))->get()->getRowArray();
                if ($cp) {
                    $couponId = (int)$cp['id'];
                }
            }

            if ($discountAmount > 0) {
                $newFinalTotal = max(0.0, (float)$order['final_total'] - $discountAmount);
                $updateData['discount_amount'] = $discountAmount;
                $updateData['final_total'] = $newFinalTotal;
                if (!empty($discountReason)) {
                    $updateData['discount_reason'] = $discountReason;
                }
                if ($couponId) {
                    $updateData['coupon_id'] = $couponId;
                    (new \App\Models\CouponUsageModel())->recordRedemption($couponId, $id, null, $discountAmount);
                }
                $order['final_total'] = $newFinalTotal;
            }

            // Record payment tender in order_payments if not already present
            $existingCount = $db->table('order_payments')->where('order_id', $id)->countAllResults();
            if ($existingCount === 0) {
                $db->table('order_payments')->insert([
                    'order_id'          => $id,
                    'payment_method_id' => $methodId,
                    'amount'            => (float)$order['final_total'],
                    'reference_number'  => 'ORD-SETTLE-' . time(),
                    'received_by'       => $this->currentUserId ?: 1,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);
                // If cash payment, increment expected_cash on active register
                if ($methodId === 1) {
                    $branchId = (int)($order['branch_id'] ?? ($this->currentBranchId ?: 1));
                    $amount = (float)$order['final_total'];
                    $db->table('cash_registers')
                        ->where('branch_id', $branchId)
                        ->where('status', 'open')
                        ->set('expected_cash', "COALESCE(expected_cash, opening_float) + {$amount}", false)
                        ->update();
                }
            }

            // Sync CRM Customer Profile & Loyalty Points (Redemption + Accrual)
            if (!empty($order['customer_phone'])) {
                $customerPhone = trim((string)$order['customer_phone']);
                $finalBill     = (float)$order['final_total'];
                $pointsEarned  = (int)floor($finalBill / 10); // 1 pt per ₹10 spent
                $cust          = $db->table('customers')->where('phone', $customerPhone)->get()->getRowArray();

                if ($cust) {
                    $customerId         = (int)$cust['id'];
                    $currentPoints      = (int)$cust['loyalty_points'];
                    $actualRedeemed     = min($currentPoints, $redeemedPoints);
                    $balanceAfterRedeem = max(0, $currentPoints - $actualRedeemed);
                    $finalPointsBalance = $balanceAfterRedeem + $pointsEarned;
                    $newLifetimeSpend   = (float)$cust['lifetime_spend'] + $finalBill;

                    $db->table('customers')->where('id', $customerId)->update([
                        'loyalty_points' => $finalPointsBalance,
                        'total_visits'   => (int)$cust['total_visits'] + 1,
                        'lifetime_spend' => $newLifetimeSpend,
                        'updated_at'     => date('Y-m-d H:i:s')
                    ]);

                    // Record redemption transaction log if points redeemed
                    if ($actualRedeemed > 0) {
                        $db->table('loyalty_transactions')->insert([
                            'customer_id'     => $customerId,
                            'order_id'        => $id,
                            'points_earned'   => 0,
                            'points_redeemed' => $actualRedeemed,
                            'balance_after'   => $balanceAfterRedeem,
                            'description'     => "Redeemed {$actualRedeemed} pts (₹" . number_format((float)$actualRedeemed, 2) . ") for Order #{$order['order_number']}",
                            'created_at'      => date('Y-m-d H:i:s'),
                        ]);
                    }

                    // Record points earned transaction log
                    if ($pointsEarned > 0) {
                        $db->table('loyalty_transactions')->insert([
                            'customer_id'     => $customerId,
                            'order_id'        => $id,
                            'points_earned'   => $pointsEarned,
                            'points_redeemed' => 0,
                            'balance_after'   => $finalPointsBalance,
                            'description'     => "Dining reward for Order #{$order['order_number']}",
                            'created_at'      => date('Y-m-d H:i:s')
                        ]);
                    }
                } else {
                    $custName = (!empty($order['customer_name']) && $order['customer_name'] !== 'Walk-in Guest')
                        ? trim((string)$order['customer_name'])
                        : 'Customer ' . substr($customerPhone, -4);
                    $db->table('customers')->insert([
                        'restaurant_id'  => (int)($order['restaurant_id'] ?? 1),
                        'name'           => $custName,
                        'phone'          => $customerPhone,
                        'loyalty_points' => $pointsEarned,
                        'total_visits'   => 1,
                        'lifetime_spend' => $finalBill,
                        'vip_status'     => 0,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);
                    $newCustId = (int)$db->insertID();
                    if ($pointsEarned > 0 && $newCustId > 0) {
                        $db->table('loyalty_transactions')->insert([
                            'customer_id'     => $newCustId,
                            'order_id'        => $id,
                            'points_earned'   => $pointsEarned,
                            'points_redeemed' => 0,
                            'balance_after'   => $pointsEarned,
                            'description'     => "Welcome dining reward for Order #{$order['order_number']}",
                            'created_at'      => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        $this->orderModel->update($id, $updateData);

        // If completed or cancelled and table attached, free table
        if (in_array($newStatus, ['completed', 'cancelled'], true) && !empty($order['table_id'])) {
            $tableStatus = $newStatus === 'completed' ? 'dirty' : 'available';
            $this->tableModel->updateStatus((int)$order['table_id'], $tableStatus, null);
        }

        $db->transComplete();

        $this->orderModel->writeAudit('orders', 'update_status', 'orders', $id, [
            'old_status' => $order['status']
        ], [
            'new_status' => $newStatus
        ], "Order {$order['order_number']} status changed to {$newStatus}");

        // Push in-app notification
        try {
            (new \App\Models\NotificationModel())->createNotification(
                null,
                'order_status',
                "Order #{$order['order_number']} " . ucfirst($newStatus),
                "Order status changed from {$order['status']} to {$newStatus}.",
                ['order_id' => $id, 'order_number' => $order['order_number'], 'status' => $newStatus],
                (int)($order['branch_id'] ?? ($this->currentBranchId ?: 1))
            );
        } catch (\Throwable $ne) {
            log_message('error', 'Notification dispatch failed: ' . $ne->getMessage());
        }

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess(['new_status' => $newStatus], "Order status updated to {$newStatus}");
        }

        return redirect()->back()->with('success', "Order #{$order['order_number']} updated to {$newStatus}.");
    }

    /**
     * Thermal Receipt Print Layout (80mm).
     */
    public function printReceipt(int $id): string|ResponseInterface
    {
        $this->authorize('orders.view');

        $order = $this->orderModel->getOrderWithDetails($id);
        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        $branchId = !empty($order['branch_id']) ? (int) $order['branch_id'] : (int) ($this->currentBranchId ?: 1);
        $restaurantId = !empty($order['restaurant_id']) ? (int) $order['restaurant_id'] : (int) ($this->currentRestaurantId ?: 1);

        $branch = db_connect()->table('branches')->where('id', $branchId)->get()->getRowArray() ?: [];
        $restaurant = db_connect()->table('restaurants')->where('id', $restaurantId)->get()->getRowArray() ?: [];

        // Dynamic Restaurant Brand Name from System Settings
        $settingsModel = new \App\Models\SettingsModel();
        $configuredRestName = $settingsModel->getValue('restaurant_name', $restaurantId, $branchId)
                           ?: $settingsModel->getValue('restaurant_name', $restaurantId, null);

        if (!empty($configuredRestName)) {
            $restaurant['name'] = $configuredRestName;
            // Keep restaurants table in sync
            try {
                db_connect()->table('restaurants')->where('id', $restaurantId)->update(['name' => $configuredRestName]);
            } catch (\Throwable $e) {
                // Non-blocking
            }
        }

        // Dynamic receipt footer note from System Settings
        $footerNote = $settingsModel->getValue('print_footer_note', $restaurantId, $branchId)
                   ?: $settingsModel->getValue('print_footer_note', $restaurantId, null);

        return view('admin/orders/receipt', [
            'order'      => $order,
            'branch'     => $branch,
            'restaurant' => $restaurant,
            'footerNote' => $footerNote,
        ]);
    }
}
