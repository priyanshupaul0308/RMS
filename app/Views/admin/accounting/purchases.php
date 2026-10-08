<div class="page-header">
  <div>
    <h1 class="page-title">Purchase & Procurement Records</h1>
    <p class="page-subtitle">Track raw food supplies, ingredient purchase orders, and supplier expenditures</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <a href="<?= site_url('admin/accounting') ?>" class="btn btn-secondary">
      &larr; Financial Overview
    </a>
    <a href="<?= site_url('admin/accounting/purchases?start_date=' . esc($startDate) . '&end_date=' . esc($endDate) . '&export=csv') ?>" class="btn btn-primary">
      Export CSV
    </a>
  </div>
</div>

<!-- DATE FILTER -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/accounting/purchases') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date" class="form-control pos-input-sm" value="<?= esc($startDate) ?>">
      </div>
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date" class="form-control pos-input-sm" value="<?= esc($endDate) ?>">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="<?= site_url('admin/accounting/purchases?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-ghost btn-sm">This Month</a>
    </form>
  </div>
</div>

<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Total Purchase Orders</div>
    <div class="stat-value text-primary"><?= esc($purchases['count']) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Procurement Spend (COGS)</div>
    <div class="stat-value text-warning">&#8377;<?= number_format((float)$purchases['total_purchases'], 2) ?></div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Procurement Log (<?= count($purchases['records']) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>PO Number</th>
            <th>Date Created</th>
            <th>Supplier Name</th>
            <th>Status</th>
            <th style="text-align: right;">Total Amount</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($purchases['records'])): ?>
            <tr>
              <td colspan="5" class="text-center text-muted" style="padding: 3rem 1rem;">
                No purchase orders recorded for this period.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($purchases['records'] as $p): ?>
              <tr>
                <td><strong><?= esc($p['po_number'] ?? ('PO-' . $p['id'])) ?></strong></td>
                <td><?= esc(date('M d, Y', strtotime($p['created_at']))) ?></td>
                <td><strong><?= esc($p['supplier_name'] ?? 'Direct Vendor') ?></strong></td>
                <td><span class="badge badge-success"><?= esc(ucfirst($p['status'] ?? 'Received')) ?></span></td>
                <td style="text-align: right;">
                  <strong style="color: #f59e0b;">&#8377;<?= number_format((float)($p['total_amount'] ?? 0), 2) ?></strong>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
