<div class="page-header">
  <div>
    <h1 class="page-title">Sales Records & Revenue Ledger</h1>
    <p class="page-subtitle">Granular food, beverage, takeaway and delivery order transactions</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <a href="<?= site_url('admin/accounting') ?>" class="btn btn-secondary">
      &larr; Financial Overview
    </a>
    <a href="<?= site_url('admin/accounting/sales?start_date=' . esc($startDate) . '&end_date=' . esc($endDate) . '&export=csv') ?>" class="btn btn-primary">
      Export CSV
    </a>
  </div>
</div>

<!-- DATE FILTER -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/accounting/sales') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date" class="form-control pos-input-sm" value="<?= esc($startDate) ?>">
      </div>
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date" class="form-control pos-input-sm" value="<?= esc($endDate) ?>">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="<?= site_url('admin/accounting/sales?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-ghost btn-sm">This Month</a>
    </form>
  </div>
</div>

<!-- KPI SUMMARY -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Total Completed Orders</div>
    <div class="stat-value text-primary"><?= esc($sales['count']) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Gross Revenue</div>
    <div class="stat-value text-success">&#8377;<?= number_format((float)$sales['gross_sales'], 2) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Tax Collected</div>
    <div class="stat-value text-info">&#8377;<?= number_format((float)$sales['total_tax'], 2) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Discounts Deducted</div>
    <div class="stat-value text-warning">&#8377;<?= number_format((float)$sales['total_discounts'], 2) ?></div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Completed Sales Journal (<?= count($sales['orders']) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Date &amp; Time</th>
            <th>Type</th>
            <th>Customer</th>
            <th>Subtotal</th>
            <th>Tax</th>
            <th>Discount</th>
            <th>Final Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($sales['orders'])): ?>
            <tr>
              <td colspan="9" class="text-center text-muted" style="padding: 3rem 1rem;">
                No completed sales orders found for this date range.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($sales['orders'] as $o): ?>
              <tr>
                <td><strong><?= esc($o['order_number']) ?></strong></td>
                <td><?= esc(date('M d, Y h:i A', strtotime($o['created_at']))) ?></td>
                <td>
                  <span class="badge badge-info" style="text-transform: capitalize;">
                    <?= esc(str_replace('_', ' ', $o['order_type'] ?? 'dine_in')) ?>
                  </span>
                </td>
                <td><?= esc($o['customer_name'] ?? 'Walk-in Guest') ?></td>
                <td>&#8377;<?= number_format((float)($o['subtotal'] ?? 0), 2) ?></td>
                <td>&#8377;<?= number_format((float)($o['tax_amount'] ?? 0), 2) ?></td>
                <td>
                  <?= (float)($o['discount_amount'] ?? 0) > 0 ? '<span style="color: #ef4444;">-&#8377;' . number_format((float)$o['discount_amount'], 2) . '</span>' : '<span class="text-muted">&#8377;0.00</span>' ?>
                </td>
                <td><strong>&#8377;<?= number_format((float)$o['final_total'], 2) ?></strong></td>
                <td><span class="badge badge-success">Completed</span></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
