<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * DashboardController – Main admin dashboard.
 */
class DashboardController extends BaseController
{
    public function index(): string|ResponseInterface
    {
        $this->authorize('dashboard.view');

        $db = db_connect();

        // ── Quick KPI aggregates ──────────────────────────────────────────────
        $restaurantId = $this->currentRestaurantId;
        $branchId     = $this->currentBranchId;

        $today = date('Y-m-d');

        // Live aggregates
        $todayOrders = $db->table('orders')
            ->where('DATE(created_at)', $today)
            ->countAllResults();

        $revRow = $db->table('orders')
            ->selectSum('final_total', 'revenue')
            ->where('DATE(created_at)', $today)
            ->where('payment_status', 'paid')
            ->get()->getRowArray();
        $todayRevenue = (float)($revRow['revenue'] ?? 0);

        $tablesOccupied = $db->table('restaurant_tables')
            ->where('status', 'occupied')
            ->countAllResults();

        $kpis = [
            'total_users'     => $db->table('users')
                ->where('restaurant_id', $restaurantId)
                ->where('is_active', 1)
                ->countAllResults(),
            'total_branches'  => $db->table('branches')
                ->where('restaurant_id', $restaurantId)
                ->where('is_active', 1)
                ->countAllResults(),
            'today_orders'    => $todayOrders,
            'today_revenue'   => number_format($todayRevenue, 2),
            'tables_occupied' => $tablesOccupied,
        ];

        return $this->renderView('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'kpis'      => $kpis,
        ]);
    }
}
