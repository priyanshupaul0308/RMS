<div class="page-header">
  <div>
    <h1 class="page-title">Coupon Usage & Discount Reports</h1>
    <p class="page-subtitle">Track customer promotional redemptions, order totals, and discounts applied</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <a href="<?= site_url('admin/coupons') ?>" class="btn btn-secondary">
      &larr; Back to Coupons
    </a>
    <a href="<?= site_url('admin/coupons/reports?start_date=' . esc($startDate) . '&end_date=' . esc($endDate) . '&export=csv') ?>" class="btn btn-primary">
      Export CSV
    </a>
  </div>
</div>

<!-- DATE FILTER -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/coupons/reports') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date" class="form-control pos-input-sm" value="<?= esc($startDate) ?>">
      </div>
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date" class="form-control pos-input-sm" value="<?= esc($endDate) ?>">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="<?= site_url('admin/coupons/reports?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-ghost btn-sm">This Month</a>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Redemption Logs (<?= count($usages) ?> events)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Used At</th>
            <th>Coupon Code</th>
            <th>Campaign Title</th>
            <th>Order #</th>
            <th>Order Value</th>
            <th>Discount Granted</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($usages)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted" style="padding: 3rem 1rem;">
                No redemptions found for this date range.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($usages as $u): ?>
              <tr>
                <td><?= esc(date('M d, Y h:i A', strtotime($u['used_at']))) ?></td>
                <td>
                  <span style="font-family: monospace; font-weight: 700; background: rgba(59,130,246,0.15); color: #60a5fa; padding: 2px 6px; border-radius: 4px;">
                    <?= esc($u['code']) ?>
                  </span>
                </td>
                <td><strong><?= esc($u['coupon_name']) ?></strong></td>
                <td><?= esc($u['order_number'] ?? 'N/A') ?></td>
                <td>&#8377;<?= number_format((float)($u['order_total'] ?? 0), 2) ?></td>
                <td><strong style="color: #10b981;">-&#8377;<?= number_format((float)$u['discount_amount'], 2) ?></strong></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
