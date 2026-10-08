<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AccountingModel;
use App\Models\ChartOfAccountsModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AccountingController - Handles sales records, purchases, expenses, revenue, P&L, taxes, and financial reports
 */
class AccountingController extends BaseController
{
    protected AccountingModel $accountingModel;
    protected ChartOfAccountsModel $accountsModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->accountingModel = new AccountingModel();
        $this->accountsModel   = new ChartOfAccountsModel();
    }

    /**
     * Financial Overview & P&L Dashboard
     */
    public function index(): string
    {
        $this->authorize('accounting.view');

        $branchId = $this->currentBranchId ?? 1;
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));

        $pnl = $this->accountingModel->getProfitLossStatement($branchId, $startDate, $endDate);
        $accounts = $this->accountsModel->getAccounts($this->currentRestaurantId ?? 1);

        return $this->renderView('admin/accounting/index', [
            'pageTitle'  => 'Accounting & Financial Reports',
            'pnl'        => $pnl,
            'accounts'   => $accounts,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ]);
    }

    /**
     * Sales Ledger
     */
    public function sales(): string
    {
        $this->authorize('accounting.view');

        $branchId = $this->currentBranchId ?? 1;
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));

        $sales = $this->accountingModel->getSalesSummary($branchId, $startDate, $endDate);

        if ($this->request->getGet('export') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="sales_ledger_' . $startDate . '_to_' . $endDate . '.csv"');
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ['Order #', 'Date', 'Type', 'Customer', 'Subtotal', 'Tax', 'Discount', 'Final Total', 'Payment Status']);
            
            foreach ($sales['orders'] as $o) {
                fputcsv($fp, [
                    $o['order_number'],
                    $o['created_at'],
                    strtoupper($o['order_type'] ?? 'DINE_IN'),
                    $o['customer_name'] ?? 'Walk-in',
                    $o['subtotal'] ?? '0.00',
                    $o['tax_amount'] ?? '0.00',
                    $o['discount_amount'] ?? '0.00',
                    $o['final_total'],
                    strtoupper($o['payment_status'] ?? 'PAID'),
                ]);
            }
            fclose($fp);
            exit;
        }

        return $this->renderView('admin/accounting/sales', [
            'pageTitle' => 'Sales Ledger & Revenue',
            'sales'     => $sales,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    /**
     * Purchase Procurement Records
     */
    public function purchases(): string
    {
        $this->authorize('accounting.view');

        $branchId = $this->currentBranchId ?? 1;
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));

        $purchases = $this->accountingModel->getPurchasesSummary($branchId, $startDate, $endDate);

        if ($this->request->getGet('export') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="purchase_records_' . $startDate . '_to_' . $endDate . '.csv"');
            
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ['PO Number', 'Date', 'Supplier', 'Items Count', 'Total Cost', 'Status']);
            
            foreach ($purchases['records'] as $p) {
                fputcsv($fp, [
                    $p['po_number'] ?? ('PO-' . $p['id']),
                    $p['created_at'],
                    $p['supplier_name'] ?? 'Unknown Vendor',
                    $p['total_items'] ?? 'N/A',
                    $p['total_amount'] ?? '0.00',
                    strtoupper($p['status'] ?? 'RECEIVED'),
                ]);
            }
            fclose($fp);
            exit;
        }

        return $this->renderView('admin/accounting/purchases', [
            'pageTitle' => 'Purchase & Procurement Records',
            'purchases' => $purchases,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    /**
     * Tax Records & GST/VAT Filing Summary
     */
    public function taxes(): string
    {
        $this->authorize('accounting.view');

        $branchId = $this->currentBranchId ?? 1;
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));

        $taxData = $this->accountingModel->getTaxRecords($branchId, $startDate, $endDate);

        return $this->renderView('admin/accounting/taxes', [
            'pageTitle' => 'Tax Records & Liability',
            'taxData'   => $taxData,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }
}
