<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">📈 Executive BI Analytics &amp; Reporting</h1>
    <p class="page-subtitle">Revenue intelligence, top-selling dishes, kitchen performance, and data exports</p>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= site_url('admin/analytics/export-csv') ?>" class="btn btn-outline">
      📊 Export Sales (CSV)
    </a>
    <a href="<?= site_url('admin/analytics/export-backup') ?>" class="btn btn-primary">
      💾 Download Database SQL Backup
    </a>
  </div>
</div>

<!-- Primary Financial KPIs -->
<div class="kpi-grid mb-3">
  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">💰</div>
    <div class="kpi-body">
      <div class="kpi-label">Total Realized Revenue</div>
      <div class="kpi-value text-success">₹<?= number_format((float)($salesMetrics['total_sales'] ?? 0), 2) ?></div>
      <div class="kpi-trend text-muted text-xs"><?= (int)($salesMetrics['total_orders'] ?? 0) ?> orders settled &amp; paid</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">🏛️</div>
    <div class="kpi-body">
      <div class="kpi-label">Tax Collected (GST)</div>
      <div class="kpi-value">₹<?= number_format((float)($salesMetrics['total_tax'] ?? 0), 2) ?></div>
      <div class="kpi-trend text-muted text-xs">Total government tax liability</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">⏱️</div>
    <div class="kpi-body">
      <div class="kpi-label">Average Kitchen Speed</div>
      <div class="kpi-value"><?= number_format((float)($kotMetrics['avg_prep_time'] ?? 14.5), 1) ?> min</div>
      <div class="kpi-trend text-muted text-xs">KOT order-to-table turnaround</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">📉</div>
    <div class="kpi-body">
      <div class="kpi-label">Wastage Loss Impact</div>
      <div class="kpi-value text-danger">₹<?= number_format((float)($wasteMetrics['total_loss'] ?? 0), 2) ?></div>
      <div class="kpi-trend text-muted text-xs"><?= (int)($wasteMetrics['total_incidents'] ?? 0) ?> logged incidents</div>
    </div>
  </div>
</div>

<div class="form-row d-flex gap-3 mb-3">
  <!-- Payment Method Breakdown -->
  <div class="card flex-1">
    <div class="card-header">
      <div class="card-title">💳 Sales by Payment Tender</div>
    </div>
    <div class="card-body">
      <?php if (empty($tenderBreakdown)): ?>
        <p class="text-muted text-small">No tender transaction data available yet.</p>
      <?php else: ?>
        <div class="d-flex flex-column gap-2">
          <?php foreach ($tenderBreakdown as $tb): 
            $tenderTotal = (float)($salesMetrics['total_sales'] > 0 ? $salesMetrics['total_sales'] : 1);
            $pct = round(((float)$tb['total_collected'] / $tenderTotal) * 100, 1);
          ?>
          <div>
            <div class="d-flex justify-between align-center mb-1">
              <span class="fw-700 text-small"><?= strtoupper(esc($tb['payment_method'])) ?></span>
              <span class="fw-600 text-small">₹<?= number_format((float)$tb['total_collected'], 2) ?> (<?= $pct ?>%)</span>
            </div>
            <div style="background: rgba(255,255,255,0.08); height: 8px; border-radius: 4px; overflow: hidden;">
              <div style="background: var(--primary); height: 100%; width: <?= min(100, $pct) ?>%;"></div>
            </div>
            <div class="text-muted text-xs mt-1"><?= (int)$tb['txn_count'] ?> transaction(s)</div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Top Revenue Menu Items -->
  <div class="card flex-1">
    <div class="card-header">
      <div class="card-title">🏆 Top Selling Dishes</div>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Dish Name</th>
            <th>Qty Sold</th>
            <th>Gross Revenue</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($topDishes)): ?>
          <tr>
            <td colspan="3" class="text-center py-3 text-muted">No sales history recorded yet.</td>
          </tr>
          <?php else: ?>
          <?php foreach ($topDishes as $td): ?>
          <tr>
            <td class="fw-700"><?= esc($td['item_name']) ?></td>
            <td class="fw-600 text-primary"><?= (int)$td['total_qty'] ?> portions</td>
            <td class="fw-700">₹<?= number_format((float)$td['total_revenue'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Quick Export Card -->
<div class="card">
  <div class="card-header">
    <div class="card-title">📦 Data Export &amp; Backup Utilities</div>
  </div>
  <div class="card-body d-flex justify-between align-center flex-wrap gap-2">
    <div>
      <div class="fw-600">Enterprise Database Archive</div>
      <div class="text-muted text-xs">Download an instantaneous, complete SQL dump of all 40 database tables including foreign keys and data records.</div>
    </div>
    <a href="<?= site_url('admin/analytics/export-backup') ?>" class="btn btn-primary">
      💾 Download Full .sql Dump
    </a>
  </div>
</div>
