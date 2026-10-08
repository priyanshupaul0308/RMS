<div class="page-header">
  <div>
    <h1 class="page-title">Tax Records & Liability</h1>
    <p class="page-subtitle">Track GST / VAT collections, taxable turnover, and filing summaries</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <a href="<?= site_url('admin/accounting') ?>" class="btn btn-secondary">
      &larr; Financial Overview
    </a>
  </div>
</div>

<!-- DATE FILTER -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/accounting/taxes') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date" class="form-control pos-input-sm" value="<?= esc($startDate) ?>">
      </div>
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date" class="form-control pos-input-sm" value="<?= esc($endDate) ?>">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="<?= site_url('admin/accounting/taxes?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-ghost btn-sm">This Month</a>
    </form>
  </div>
</div>

<!-- TAX STATS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Taxable Turnover</div>
    <div class="stat-value text-primary">&#8377;<?= number_format((float)$taxData['taxable_turnover'], 2) ?></div>
    <div class="stat-sub">Net assessable sales</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Tax Collected</div>
    <div class="stat-value text-success">&#8377;<?= number_format((float)$taxData['total_tax_collected'], 2) ?></div>
    <div class="stat-sub">Output GST / VAT liability</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Orders Taxed</div>
    <div class="stat-value text-info"><?= esc($taxData['orders_count']) ?></div>
    <div class="stat-sub">Completed transactions</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Configured Tax Rates &amp; Slabs</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Tax Name</th>
            <th>Rate (%)</th>
            <th>Type</th>
            <th>Applies To</th>
            <th>Compound</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($taxData['tax_rates'])): ?>
            <tr>
              <td colspan="6" class="text-center text-muted" style="padding: 3rem 1rem;">
                No tax rates configured.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($taxData['tax_rates'] as $tr): ?>
              <tr>
                <td><strong><?= esc($tr['name']) ?></strong></td>
                <td><strong style="color: #60a5fa;"><?= esc($tr['rate']) ?>%</strong></td>
                <td style="text-transform: capitalize;"><?= esc($tr['type']) ?></td>
                <td style="text-transform: capitalize;"><?= esc($tr['applies_to']) ?></td>
                <td><?= (int)$tr['is_compound'] === 1 ? 'Yes' : 'No' ?></td>
                <td><span class="badge badge-success">Active</span></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
