<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use App\Models\TaxRateModel;
use App\Models\PaymentMethodModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * SettingsController – Global and branch-level system configuration.
 */
class SettingsController extends BaseController
{
    protected SettingsModel $settingsModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->settingsModel = new SettingsModel();
    }

    public function index(): string
    {
        $this->authorize('settings.view');

        $settings = $this->settingsModel->getAllSettingsGrouped($this->currentRestaurantId, $this->currentBranchId);
        $groups = ['general', 'billing', 'order', 'notification', 'security'];
        foreach ($groups as $group) {
            if (!isset($settings[$group])) {
                $settings[$group] = [];
            }
        }

        $taxRates = db_connect()->table('tax_rates')
            ->where('restaurant_id', $this->currentRestaurantId)
            ->where('is_active', 1)
            ->get()->getResultArray();

        $paymentMethods = db_connect()->table('payment_methods')
            ->where('restaurant_id', $this->currentRestaurantId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return $this->renderView('admin/settings/index', [
            'pageTitle'      => 'System Settings',
            'settings'       => $settings,
            'taxRates'       => $taxRates,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function save(): ResponseInterface
    {
        $this->authorize('settings.edit');

        $postData = $this->request->getPost();
        $allowed  = [
            'restaurant_name', 'currency_code', 'currency_symbol',
            'service_charge_pct', 'default_tax_inclusive', 'bill_prefix',
            'print_footer_note', 'kot_auto_print', 'delivery_charge_flat',
            'low_stock_threshold', 'max_login_attempts', 'lockout_minutes',
            'session_timeout_min',
        ];

        $restaurantId = (int) ($this->currentRestaurantId ?: 1);

        foreach ($allowed as $key) {
            if (isset($postData[$key])) {
                $this->settingsModel->setValue(
                    $key,
                    $postData[$key],
                    $restaurantId,
                    $this->currentBranchId,
                    'string',
                    $this->currentUserId
                );
                // Also set at global restaurant level (branch_id = null)
                $this->settingsModel->setValue(
                    $key,
                    $postData[$key],
                    $restaurantId,
                    null,
                    'string',
                    $this->currentUserId
                );
            }
        }

        // Keep restaurants table in sync with configured restaurant name
        if (!empty($postData['restaurant_name'])) {
            $restName = trim((string) $postData['restaurant_name']);
            try {
                db_connect()->table('restaurants')->where('id', $restaurantId)->update(['name' => $restName]);
            } catch (\Throwable $e) {
                // Non-blocking
            }
        }

        $this->settingsModel->writeAudit('settings', 'updated', 'settings', null, null, $postData);

        return redirect()->route('admin.settings')->with('success', 'Settings saved successfully.');
    }

    public function tax(): string
    {
        $this->authorize('settings.view');

        $taxRates = db_connect()->table('tax_rates')
            ->where('restaurant_id', $this->currentRestaurantId)
            ->get()->getResultArray();

        return $this->renderView('admin/settings/tax', [
            'pageTitle' => 'Tax Rate Management',
            'taxRates'  => $taxRates,
        ]);
    }

    public function saveTax(): ResponseInterface
    {
        $this->authorize('settings.edit');

        $rules = [
            'name'   => 'required|max_length[80]',
            'rate'   => 'required|numeric',
            'type'   => 'required|in_list[inclusive,exclusive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'restaurant_id' => $this->currentRestaurantId,
            'branch_id'     => $this->currentBranchId,
            'name'          => $this->request->getPost('name'),
            'rate'          => (float) $this->request->getPost('rate'),
            'type'          => $this->request->getPost('type'),
            'applies_to'    => $this->request->getPost('applies_to') ?? 'all',
            'is_compound'   => (int) (bool) $this->request->getPost('is_compound'),
        ];

        $taxId = $this->request->getPost('id');
        if ($taxId) {
            db_connect()->table('tax_rates')->where('id', (int) $taxId)->update($data);
        } else {
            db_connect()->table('tax_rates')->insert($data);
        }

        return redirect()->route('admin.settings')->with('success', 'Tax rate saved.');
    }
}
