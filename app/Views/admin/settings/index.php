<div class="page-header">
  <div>
    <h1 class="page-title">System Settings</h1>
    <p class="page-subtitle">Configure restaurant branding, billing preferences, currency, and security</p>
  </div>
  <div class="page-actions">
    <a href="<?= site_url('admin/settings/tax') ?>" class="btn btn-secondary">
      Tax Rates Configuration &rarr;
    </a>
  </div>
</div>

<form action="<?= site_url('admin/settings/save') ?>" method="POST">
  <?= csrf_field() ?>

  <div class="grid-2">
    <!-- General & Branding -->
    <div class="card mb-2">
      <div class="card-header">
        <div class="card-title">&#127970; General &amp; Branding</div>
      </div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label" for="restaurant_name">Restaurant Brand Name</label>
          <input type="text" name="restaurant_name" id="restaurant_name" class="form-control" 
                 value="<?= esc($settings['general']['restaurant_name'] ?? 'RMS Fine Dining') ?>">
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="currency_code">Currency Code</label>
              <input type="text" name="currency_code" id="currency_code" class="form-control" 
                     value="<?= esc($settings['billing']['currency_code'] ?? 'INR') ?>" placeholder="e.g. INR, USD, EUR">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="currency_symbol">Currency Symbol</label>
              <input type="text" name="currency_symbol" id="currency_symbol" class="form-control" 
                     value="<?= esc($settings['billing']['currency_symbol'] ?? '₹') ?>" placeholder="e.g. ₹, $, €">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="print_footer_note">Bill Receipt Footer Note</label>
          <textarea name="print_footer_note" id="print_footer_note" class="form-control" rows="2" 
                    placeholder="Thank you for dining with us!"><?= esc($settings['billing']['print_footer_note'] ?? 'Thank you for dining with us!') ?></textarea>
        </div>
      </div>
    </div>

    <!-- Billing & Orders -->
    <div class="card mb-2">
      <div class="card-header">
        <div class="card-title">&#128181; Billing &amp; Orders</div>
      </div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="bill_prefix">Invoice Prefix</label>
              <input type="text" name="bill_prefix" id="bill_prefix" class="form-control" 
                     value="<?= esc($settings['billing']['bill_prefix'] ?? 'INV-') ?>" placeholder="INV-">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="service_charge_pct">Service Charge (%)</label>
              <input type="number" step="0.1" name="service_charge_pct" id="service_charge_pct" class="form-control" 
                     value="<?= esc($settings['billing']['service_charge_pct'] ?? '0') ?>">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="delivery_charge_flat">Flat Delivery Charge</label>
              <input type="number" step="0.01" name="delivery_charge_flat" id="delivery_charge_flat" class="form-control" 
                     value="<?= esc($settings['order']['delivery_charge_flat'] ?? '0') ?>">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="kot_auto_print">KOT Auto-Print</label>
              <select name="kot_auto_print" id="kot_auto_print" class="form-control">
                <option value="1" <?= ($settings['order']['kot_auto_print'] ?? '1') == '1' ? 'selected' : '' ?>>Enabled</option>
                <option value="0" <?= ($settings['order']['kot_auto_print'] ?? '1') == '0' ? 'selected' : '' ?>>Disabled</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="low_stock_threshold">Low Stock Alert Threshold</label>
          <input type="number" name="low_stock_threshold" id="low_stock_threshold" class="form-control" 
                 value="<?= esc($settings['order']['low_stock_threshold'] ?? '10') ?>">
          <div class="form-hint">Items with inventory below this count trigger dashboard alerts</div>
        </div>
      </div>
    </div>

    <!-- Security & Sessions -->
    <div class="card mb-2">
      <div class="card-header">
        <div class="card-title">&#128274; Security &amp; Sessions</div>
      </div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="max_login_attempts">Max Failed Login Attempts</label>
              <input type="number" name="max_login_attempts" id="max_login_attempts" class="form-control" 
                     value="<?= esc($settings['security']['max_login_attempts'] ?? '5') ?>">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="lockout_minutes">Lockout Duration (Minutes)</label>
              <input type="number" name="lockout_minutes" id="lockout_minutes" class="form-control" 
                     value="<?= esc($settings['security']['lockout_minutes'] ?? '15') ?>">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="session_timeout_min">Inactivity Session Timeout (Minutes)</label>
          <input type="number" name="session_timeout_min" id="session_timeout_min" class="form-control" 
                 value="<?= esc($settings['security']['session_timeout_min'] ?? '60') ?>">
        </div>
      </div>
    </div>

    <!-- Payment Methods Overview -->
    <div class="card mb-2">
      <div class="card-header">
        <div class="card-title">&#128179; Configured Payment Methods</div>
      </div>
      <div class="card-body" style="padding:0;">
        <table class="rms-table">
          <thead>
            <tr>
              <th>Method</th>
              <th>Slug</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($paymentMethods as $pm): ?>
              <tr>
                <td class="td-bold"><?= esc($pm['name']) ?></td>
                <td><code><?= esc($pm['slug']) ?></code></td>
                <td>
                  <?php if (!empty($pm['is_active'])): ?>
                    <span class="status-badge status-active">Active</span>
                  <?php else: ?>
                    <span class="status-badge status-inactive">Disabled</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary btn-lg">
      Save All Settings
    </button>
  </div>
</form>
