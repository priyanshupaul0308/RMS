<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/**
 * RMS Route Configuration
 *
 * @var RouteCollection $routes
 */

// ============================================================================
// AUTH ROUTES (no authentication required)
// ============================================================================
$routes->group('', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->get('/',               'Auth\AuthController::login',   ['as' => 'home']);
    $routes->get('login',           'Auth\AuthController::login',   ['as' => 'auth.login']);
    $routes->post('login',          'Auth\AuthController::authenticate');
    $routes->get('logout',          'Auth\AuthController::logout',  ['as' => 'auth.logout']);
    $routes->get('forgot-password', 'Auth\AuthController::forgotPassword', ['as' => 'auth.forgot']);
    $routes->post('forgot-password','Auth\AuthController::sendResetLink');
    $routes->get('reset-password/(:segment)', 'Auth\AuthController::resetPassword/$1', ['as' => 'auth.reset']);
    $routes->post('reset-password', 'Auth\AuthController::processReset');
});

// ============================================================================
// AUTHENTICATED ROUTES  (AuthFilter applied)
// ============================================================================
$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => 'auth',
], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'admin.dashboard']);

    // ── User Management ──────────────────────────────────────────────────────
    $routes->group('users', static function ($routes) {
        $routes->get('/',              'UserController::index',    ['as' => 'admin.users.index']);
        $routes->get('create',         'UserController::create',   ['as' => 'admin.users.create']);
        $routes->post('store',         'UserController::store',    ['as' => 'admin.users.store']);
        $routes->get('edit/(:num)',    'UserController::edit/$1',  ['as' => 'admin.users.edit']);
        $routes->post('update/(:num)', 'UserController::update/$1');
        $routes->post('toggle/(:num)', 'UserController::toggle/$1');
        $routes->get('profile',        'UserController::profile',  ['as' => 'admin.users.profile']);
        $routes->post('change-password','UserController::changePassword');
    });

    // ── Role & Permission Management ─────────────────────────────────────────
    $routes->group('roles', static function ($routes) {
        $routes->get('/',                     'RoleController::index',         ['as' => 'admin.roles.index']);
        $routes->get('permissions/(:num)',     'RoleController::permissions/$1');
        $routes->post('permissions/(:num)',    'RoleController::savePermissions/$1');
    });

    // ── Branch Management ────────────────────────────────────────────────────
    $routes->group('branches', static function ($routes) {
        $routes->get('/',              'BranchController::index',    ['as' => 'admin.branches.index']);
        $routes->get('create',         'BranchController::create',   ['as' => 'admin.branches.create']);
        $routes->post('store',         'BranchController::store');
        $routes->get('edit/(:num)',    'BranchController::edit/$1');
        $routes->post('update/(:num)', 'BranchController::update/$1');
        $routes->post('toggle/(:num)', 'BranchController::toggle/$1');
    });

    // ── POS Terminal ────────────────────────────────────────────────────────
    $routes->get('pos',                        'PosController::index',           ['as' => 'admin.pos']);
    $routes->post('pos/order',                 'PosController::createOrder');
    $routes->get('pos/table-order/(:num)',      'PosController::getTableOrder/$1');
    $routes->post('pos/complete-order/(:num)', 'PosController::completeOrder/$1');
    $routes->post('pos/validate-coupon',       'PosController::validateCoupon');
    $routes->get('pos/lookup-customer',        'PosController::lookupCustomer');

    // ── Live Orders Pipeline ─────────────────────────────────────────────────
    $routes->group('orders', static function ($routes) {
        $routes->get('/',              'OrderController::index',        ['as' => 'admin.orders.index']);
        $routes->get('export',         'OrderController::exportCsv',    ['as' => 'admin.orders.export']);
        $routes->get('show/(:num)',    'OrderController::show/$1',      ['as' => 'admin.orders.show']);
        $routes->post('status/(:num)', 'OrderController::updateStatus/$1');
        $routes->get('receipt/(:num)', 'OrderController::printReceipt/$1');
    });

    // ── Floors & Tables ──────────────────────────────────────────────────────
    $routes->group('floors', static function ($routes) {
        $routes->get('/',                    'FloorController::index',            ['as' => 'admin.floors.index']);
        $routes->post('table/status/(:num)', 'FloorController::updateTableStatus/$1');
        $routes->post('table/store',         'FloorController::storeTable',       ['as' => 'admin.floors.table.store']);
        $routes->post('table/update/(:num)', 'FloorController::updateTable/$1',   ['as' => 'admin.floors.table.update']);
        $routes->post('table/delete/(:num)', 'FloorController::deleteTable/$1',   ['as' => 'admin.floors.table.delete']);
    });

    // ── Menu Engineering ─────────────────────────────────────────────────────
    $routes->get('menu-categories',                  'MenuController::categories',         ['as' => 'admin.menu.categories']);
    $routes->post('menu-categories/store',           'MenuController::storeCategory');
    $routes->post('menu-categories/update/(:num)',   'MenuController::updateCategory/$1');
    $routes->post('menu-categories/delete/(:num)',   'MenuController::deleteCategory/$1');
    $routes->get('menu-items',                       'MenuController::items',              ['as' => 'admin.menu.items']);
    $routes->post('menu-items/store',                'MenuController::storeItem',          ['as' => 'admin.menu.items.store']);
    $routes->post('menu-items/update/(:num)',        'MenuController::updateItem/$1',      ['as' => 'admin.menu.items.update']);
    $routes->post('menu-items/delete/(:num)',        'MenuController::deleteItem/$1',      ['as' => 'admin.menu.items.delete']);
    $routes->post('menu-items/toggle/(:num)',        'MenuController::toggleAvailability/$1');

    // ── Kitchen Display System (KDS) & KOT ───────────────────────────────────
    $routes->get('kds',                          'KdsController::index',               ['as' => 'admin.kds']);
    $routes->get('kds/feed',                     'KdsController::feed');
    $routes->post('kds/start/(:num)',            'KdsController::start/$1');
    $routes->post('kds/bump/(:num)',             'KdsController::bump/$1');
    $routes->post('kds/toggle-item/(:num)',      'KdsController::toggleItem/$1');

    $routes->group('kot', static function ($routes) {
        $routes->get('/',                        'KotController::index',               ['as' => 'admin.kot.index']);
        $routes->get('print/(:num)',             'KotController::print/$1',            ['as' => 'admin.kot.print']);
    });

    // ── Table Reservations ────────────────────────────────────────────────────
    $routes->group('reservations', static function ($routes) {
        $routes->get('/',                        'ReservationController::index',       ['as' => 'admin.reservations.index']);
        $routes->post('store',                   'ReservationController::store',       ['as' => 'admin.reservations.store']);
        $routes->post('seat/(:num)',             'ReservationController::seat/$1');
        $routes->post('cancel/(:num)',           'ReservationController::cancel/$1');
    });

    // ── Cash Drawer & Shift Reconciliation ───────────────────────────────────
    $routes->group('cash-drawer', static function ($routes) {
        $routes->get('/',                        'CashDrawerController::index',        ['as' => 'admin.cash-drawer']);
        $routes->post('open',                    'CashDrawerController::open');
        $routes->post('transaction',             'CashDrawerController::transaction');
        $routes->post('close',                   'CashDrawerController::close');
        $routes->post('update/(:num)',           'CashDrawerController::update/$1');
        $routes->post('delete/(:num)',           'CashDrawerController::delete/$1');
    });

    // ── Billing & Multi-Tender Split Payments ────────────────────────────────
    $routes->group('billing', static function ($routes) {
        $routes->get('transactions',             'BillingController::transactions',    ['as' => 'admin.billing.transactions']);
        $routes->post('split-payment',           'BillingController::splitPayment');
    });

    // ── Settings ─────────────────────────────────────────────────────────────
    $routes->group('settings', static function ($routes) {
        $routes->get('/',       'SettingsController::index',  ['as' => 'admin.settings']);
        $routes->post('save',   'SettingsController::save');
        $routes->get('tax',     'SettingsController::tax');
        $routes->post('tax/save','SettingsController::saveTax');
    });

    // ── Phase 6: Inventory, Procurement, Wastage & Recipes ───────────────────
    $routes->group('inventory', static function ($routes) {
        $routes->get('/',                             'InventoryController::index',           ['as' => 'admin.inventory.index']);
        $routes->post('store-item',                   'InventoryController::storeItem',       ['as' => 'admin.inventory.storeItem']);
        $routes->post('adjust',                       'InventoryController::adjustStock',     ['as' => 'admin.inventory.adjust']);
        $routes->get('suppliers',                     'InventoryController::suppliers',       ['as' => 'admin.inventory.suppliers']);
        $routes->post('suppliers/store',              'InventoryController::storeSupplier',   ['as' => 'admin.inventory.storeSupplier']);
        $routes->get('purchase-orders',               'InventoryController::purchaseOrders',  ['as' => 'admin.inventory.purchaseOrders']);
        $routes->post('purchase-orders/store',        'InventoryController::storePo',         ['as' => 'admin.inventory.storePo']);
        $routes->post('purchase-orders/receive/(:num)','InventoryController::receivePo/$1',   ['as' => 'admin.inventory.receivePo']);
        $routes->get('waste',                         'InventoryController::waste',           ['as' => 'admin.inventory.waste']);
        $routes->post('waste/store',                  'InventoryController::storeWaste',      ['as' => 'admin.inventory.storeWaste']);
        $routes->get('recipes',                       'InventoryController::recipes',         ['as' => 'admin.inventory.recipes']);
        $routes->post('recipes/store',                'InventoryController::storeRecipe',     ['as' => 'admin.inventory.storeRecipe']);
        $routes->post('recipes/delete',               'InventoryController::deleteRecipe',    ['as' => 'admin.inventory.deleteRecipe']);
        $routes->post('update-item/(:num)',           'InventoryController::updateItem/$1',   ['as' => 'admin.inventory.updateItem']);
    });

    // ── Phase 7: CRM, Loyalty & Customer Feedback ────────────────────────────
    $routes->group('crm', static function ($routes) {
        $routes->get('/',                             'CrmController::index',                 ['as' => 'admin.crm.index']);
        $routes->post('store',                        'CrmController::store',                 ['as' => 'admin.crm.store']);
        $routes->post('adjust-points',                'CrmController::adjustPoints',          ['as' => 'admin.crm.adjustPoints']);
        $routes->get('feedback',                      'CrmController::feedback',              ['as' => 'admin.crm.feedback']);
        $routes->post('feedback/store',               'CrmController::storeFeedback',         ['as' => 'admin.crm.storeFeedback']);
    });

    // ── Phase 7: Analytics, Audit Trail & Export ─────────────────────────────
    $routes->group('analytics', static function ($routes) {
        $routes->get('/',                             'AnalyticsController::index',           ['as' => 'admin.analytics.index']);
        $routes->get('audit',                         'AnalyticsController::audit',           ['as' => 'admin.analytics.audit']);
        $routes->get('export-backup',                 'AnalyticsController::exportBackup',    ['as' => 'admin.analytics.exportBackup']);
        $routes->get('export-csv',                    'AnalyticsController::exportCsv',       ['as' => 'admin.analytics.exportCsv']);
    });

    // ── Module 5: Staff Attendance & Shifts ──────────────────────────────────
    $routes->group('attendance', static function ($routes) {
        $routes->get('/',                             'AttendanceController::index',           ['as' => 'admin.attendance.index']);
        $routes->get('my-status',                     'AttendanceController::myStatus',        ['as' => 'admin.attendance.myStatus']);
        $routes->post('check-in',                     'AttendanceController::checkIn',         ['as' => 'admin.attendance.checkIn']);
        $routes->post('check-out/(:num)',             'AttendanceController::checkOut/$1',     ['as' => 'admin.attendance.checkOut']);
        $routes->post('clock-out-self',               'AttendanceController::clockOutSelf',   ['as' => 'admin.attendance.clockOutSelf']);
        $routes->post('shifts/save',                  'AttendanceController::saveShift',       ['as' => 'admin.attendance.saveShift']);
        $routes->post('shifts/assign',                'AttendanceController::assignShift',     ['as' => 'admin.attendance.assignShift']);
        $routes->get('reports',                       'AttendanceController::reports',         ['as' => 'admin.attendance.reports']);
    });

    // ── Module 26: Discount & Coupon Management ──────────────────────────────
    $routes->group('coupons', static function ($routes) {
        $routes->get('/',                             'CouponController::index',               ['as' => 'admin.coupons.index']);
        $routes->post('store',                        'CouponController::store',               ['as' => 'admin.coupons.store']);
        $routes->post('toggle/(:num)',                'CouponController::toggle/$1',           ['as' => 'admin.coupons.toggle']);
        $routes->get('validate',                      'CouponController::validateCode',        ['as' => 'admin.coupons.validate']);
        $routes->post('validate',                     'CouponController::validateCode');
        $routes->get('reports',                       'CouponController::reports',             ['as' => 'admin.coupons.reports']);
    });

    // ── Module 29: Delivery Partner Management ────────────────────────────────
    $routes->group('delivery', static function ($routes) {
        $routes->get('/',                             'DeliveryController::index',             ['as' => 'admin.delivery.index']);
        $routes->post('partner/save',                 'DeliveryController::savePartner',       ['as' => 'admin.delivery.savePartner']);
        $routes->post('assign',                       'DeliveryController::assign',            ['as' => 'admin.delivery.assign']);
        $routes->post('status/(:num)',                'DeliveryController::updateStatus/$1',   ['as' => 'admin.delivery.updateStatus']);
    });

    // ── Module 30: Expense Management ────────────────────────────────────────
    $routes->group('expenses', static function ($routes) {
        $routes->get('/',                             'ExpenseController::index',              ['as' => 'admin.expenses.index']);
        $routes->post('store',                        'ExpenseController::store',              ['as' => 'admin.expenses.store']);
        $routes->post('approve/(:num)',               'ExpenseController::approve/$1',         ['as' => 'admin.expenses.approve']);
        $routes->get('reports',                       'ExpenseController::reports',            ['as' => 'admin.expenses.reports']);
    });

    // ── Module 33: Staff Performance ─────────────────────────────────────────
    $routes->group('performance', static function ($routes) {
        $routes->get('/',                             'StaffPerformanceController::index',     ['as' => 'admin.performance.index']);
        $routes->post('review/store',                 'StaffPerformanceController::storeReview',['as' => 'admin.performance.storeReview']);
        $routes->get('history/(:num)',                'StaffPerformanceController::history/$1', ['as' => 'admin.performance.history']);
    });

    // ── Module 34: Maintenance Management ─────────────────────────────────────
    $routes->group('maintenance', static function ($routes) {
        $routes->get('/',                             'MaintenanceController::index',          ['as' => 'admin.maintenance.index']);
        $routes->post('equipment/save',               'MaintenanceController::saveEquipment',  ['as' => 'admin.maintenance.saveEquipment']);
        $routes->post('request/store',                'MaintenanceController::createRequest',  ['as' => 'admin.maintenance.createRequest']);
        $routes->post('request/status/(:num)',         'MaintenanceController::updateRequestStatus/$1', ['as' => 'admin.maintenance.updateStatus']);
    });

    // ── Module 32: Accounting & Finance ───────────────────────────────────────
    $routes->group('accounting', static function ($routes) {
        $routes->get('/',                             'AccountingController::index',            ['as' => 'admin.accounting.index']);
        $routes->get('sales',                         'AccountingController::sales',            ['as' => 'admin.accounting.sales']);
        $routes->get('purchases',                     'AccountingController::purchases',        ['as' => 'admin.accounting.purchases']);
        $routes->get('taxes',                         'AccountingController::taxes',            ['as' => 'admin.accounting.taxes']);
    });

    // ── Module 35: Notification & Communication ──────────────────────────────
    $routes->group('communication', static function ($routes) {
        $routes->get('/',                             'CommunicationController::index',         ['as' => 'admin.communication.index']);
        $routes->post('send',                         'CommunicationController::send',          ['as' => 'admin.communication.send']);
        $routes->post('mark-all-read',                'CommunicationController::markAllRead',   ['as' => 'admin.communication.markAllRead']);
    });

    // ── API / AJAX Endpoints ─────────────────────────────────────────────────
    $routes->group('api', ['filter' => 'auth'], static function ($routes) {
        $routes->get('notifications',         'Api\NotificationController::index');
        $routes->post('notifications/read',   'Api\NotificationController::markRead');
        $routes->get('permissions/user/(:num)','Api\PermissionController::user/$1');
    });
});

// ============================================================================
// FALLBACK
// ============================================================================
$routes->set404Override('App\Controllers\Errors\PageNotFoundController::index');
