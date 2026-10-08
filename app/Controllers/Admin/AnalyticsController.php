<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AnalyticsController
 * 
 * Phase 7: Business Intelligence Analytics, Audit Trail, and Database Backup / Data Export.
 */
class AnalyticsController extends BaseController
{
    protected AuditLogModel $auditModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->auditModel = new AuditLogModel();
    }

    /**
     * Executive BI Analytics Dashboard
     */
    public function index(): string
    {
        $this->authorize('reports.view');

        $db       = \Config\Database::connect();
        $branchId = $this->currentBranchId;

        // 1. Total revenue and order counts (scoped by branch if set)
        $salesQuery = $db->table('orders')
            ->select('COUNT(id) as total_orders, COALESCE(SUM(final_total), 0) as total_sales, COALESCE(SUM(tax_amount), 0) as total_tax')
            ->where('payment_status', 'paid');
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }
        $salesMetrics = $salesQuery->get()->getRowArray();

        // 2. Sales by Payment Tender breakdown
        $tenderQuery = $db->table('order_payments op')
            ->select('COALESCE(pm.name, "Cash") as payment_method, COUNT(op.id) as txn_count, COALESCE(SUM(op.amount), 0) as total_collected')
            ->join('payment_methods pm', 'pm.id = op.payment_method_id', 'left')
            ->join('orders o', 'o.id = op.order_id', 'left');
        if ($branchId) {
            $tenderQuery->where('o.branch_id', $branchId);
        }
        $tenderBreakdown = $tenderQuery->groupBy('op.payment_method_id')->get()->getResultArray();

        // 3. Top selling menu dishes
        $topDishesQuery = $db->table('order_items oi')
            ->select('oi.item_name, SUM(oi.quantity) as total_qty, SUM(oi.total) as total_revenue')
            ->join('orders o', 'o.id = oi.order_id')
            ->where('o.payment_status', 'paid');
        if ($branchId) {
            $topDishesQuery->where('o.branch_id', $branchId);
        }
        $topDishes = $topDishesQuery->groupBy('oi.item_name')
            ->orderBy('total_qty', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // 4. Kitchen Prep Time Metrics (from KOTs)
        $kotQuery = $db->table('kots')
            ->select('COUNT(id) as total_kots, COALESCE(AVG(TIMESTAMPDIFF(MINUTE, created_at, ready_at)), 14.5) as avg_prep_time')
            ->where('status', 'completed');
        if ($branchId) {
            $kotQuery->where('branch_id', $branchId);
        }
        $kotMetrics = $kotQuery->get()->getRowArray();

        // 5. Spoilage Loss
        $wasteQuery = $db->table('waste_logs')
            ->select('COALESCE(SUM(cost_impact), 0) as total_loss, COUNT(id) as total_incidents');
        if ($branchId) {
            $wasteQuery->where('branch_id', $branchId);
        }
        $wasteMetrics = $wasteQuery->get()->getRowArray();

        return $this->renderView('admin/analytics/index', [
            'pageTitle'       => 'Executive Analytics & BI Intelligence',
            'title'           => 'Executive Analytics & BI Intelligence',
            'active_menu'     => 'analytics',
            'salesMetrics'    => $salesMetrics,
            'tenderBreakdown' => $tenderBreakdown,
            'topDishes'       => $topDishes,
            'kotMetrics'      => $kotMetrics,
            'wasteMetrics'    => $wasteMetrics
        ]);
    }

    /**
     * Granular System Audit Trail Viewer
     */
    public function audit(): string
    {
        $this->authorize('reports.view');

        $module = $this->request->getGet('module') ? (string)$this->request->getGet('module') : null;
        $userId = $this->request->getGet('user_id') ? (int)$this->request->getGet('user_id') : null;

        $logs = $this->auditModel->getLogs($module, $userId, 100);

        $db      = \Config\Database::connect();
        $modules = $db->table('audit_logs')->select('DISTINCT(module) as mod_name')->get()->getResultArray();
        $users   = $db->table('users')->select('id, first_name, last_name')->get()->getResultArray();

        return $this->renderView('admin/analytics/audit', [
            'pageTitle'      => 'System Audit Trail & Security Logs',
            'title'          => 'System Audit Trail & Security Logs',
            'active_menu'    => 'audit',
            'logs'           => $logs,
            'modules'        => $modules,
            'users'          => $users,
            'selectedModule' => $module,
            'selectedUser'   => $userId
        ]);
    }

    /**
     * Database SQL Backup Utility
     */
    public function exportBackup(): ResponseInterface
    {
        $this->authorize('reports.view');

        $db     = \Config\Database::connect();
        $tables = $db->listTables();

        $dump  = "-- ==========================================================\n";
        $dump .= "-- RMS Database Backup\n";
        $dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $dump .= "-- Database: " . $db->getDatabase() . "\n";
        $dump .= "-- ==========================================================\n\n";
        $dump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $tbl) {
            $createRes = $db->query("SHOW CREATE TABLE `{$tbl}`")->getRowArray();
            $dump .= "-- Table structure for `{$tbl}`\n";
            $dump .= "DROP TABLE IF EXISTS `{$tbl}`;\n";
            $dump .= ($createRes['Create Table'] ?? '') . ";\n\n";

            // Export up to 500 rows per table
            $rows = $db->table($tbl)->limit(500)->get()->getResultArray();
            if (!empty($rows)) {
                $dump .= "-- Dumping data for `{$tbl}`\n";
                foreach ($rows as $row) {
                    $keys   = array_map(fn($k) => "`{$k}`", array_keys($row));
                    $values = array_map(function($v) use ($db) {
                        return ($v === null) ? 'NULL' : $db->escape($v);
                    }, array_values($row));

                    $dump .= "INSERT INTO `{$tbl}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
                $dump .= "\n";
            }
        }

        $dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $filename = 'rms_db_backup_' . date('Y_m_d_His') . '.sql';

        $this->auditModel->recordEvent('backup', 'export_sql', "Generated database backup: {$filename}", $this->currentUserId);

        return $this->response
            ->setHeader('Content-Type', 'application/sql')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dump);
    }

    /**
     * Export Sales Ledger to CSV
     */
    public function exportCsv(): ResponseInterface
    {
        $this->authorize('reports.view');

        $db       = \Config\Database::connect();
        $branchId = $this->currentBranchId;

        $ordersQuery = $db->table('orders o')
            ->select('o.order_number, o.order_type, o.subtotal, o.tax_amount, o.discount_amount, o.final_total, o.status as order_status, o.payment_status, o.customer_name, o.created_at, t.table_number, u.first_name, u.last_name')
            ->join('restaurant_tables t', 't.id = o.table_id', 'left')
            ->join('users u', 'u.id = o.waiter_id', 'left');

        if ($branchId) {
            $ordersQuery->where('o.branch_id', $branchId);
        }

        $orders = $ordersQuery->orderBy('o.id', 'DESC')->get()->getResultArray();

        $filename = 'rms_sales_export_' . date('Y_m_d') . '.csv';

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Order Number', 'Type', 'Table', 'Customer', 'Subtotal', 'Tax', 'Discount', 'Total Amount', 'Order Status', 'Payment Status', 'Server', 'Date & Time']);

        foreach ($orders as $o) {
            fputcsv($output, [
                $o['order_number'],
                strtoupper($o['order_type']),
                $o['table_number'] ?? 'N/A',
                $o['customer_name'] ?? 'Walk-in Guest',
                number_format((float)$o['subtotal'], 2),
                number_format((float)$o['tax_amount'], 2),
                number_format((float)$o['discount_amount'], 2),
                number_format((float)$o['final_total'], 2),
                $o['order_status'],
                $o['payment_status'],
                trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? '')),
                $o['created_at']
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        $this->auditModel->recordEvent('analytics', 'export_csv', "Exported sales ledger CSV: {$filename}", $this->currentUserId);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csvContent);
    }
}
