<div class="page-header">
  <div>
    <h1 class="page-title">Expense Analytics & Breakdown</h1>
    <p class="page-subtitle">Track categorical spending, historical expense trends, and financial reports</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <a href="<?= site_url('admin/expenses') ?>" class="btn btn-secondary">
      &larr; Back to Expenses
    </a>
    <a href="<?= site_url('admin/expenses/reports?start_date=' . esc($startDate) . '&end_date=' . esc($endDate) . '&export=csv') ?>" class="btn btn-primary">
      Export CSV
    </a>
  </div>
</div>

<!-- DATE FILTER -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/expenses/reports') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date" class="form-control pos-input-sm" value="<?= esc($startDate) ?>">
      </div>
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date" class="form-control pos-input-sm" value="<?= esc($endDate) ?>">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="<?= site_url('admin/expenses/reports?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-ghost btn-sm">This Month</a>
    </form>
  </div>
</div>

<!-- CATEGORY AGGREGATION CARDS -->
<div class="card mb-4">
  <div class="card-header">
    <div class="card-title">Spending by Category (Approved)</div>
  </div>
  <div class="card-body">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem;">
      <?php if (empty($categoryTotals)): ?>
        <p class="text-muted">No approved expenses logged in this timeframe.</p>
      <?php else: ?>
        <?php foreach ($categoryTotals as $cat): ?>
          <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 1rem;">
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.25rem;"><?= esc($cat['category_name']) ?></div>
            <div style="font-size: 1.3rem; font-weight: 700; color: #ef4444;">&#8377;<?= number_format((float)$cat['total_amount'], 2) ?></div>
            <div style="font-size: 0.8rem; color: #94a3b8;"><?= esc($cat['count']) ?> receipts processed</div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- EXPENSE LOGS -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Detailed Ledger (<?= count($expenses) ?> entries)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Vendor / Payee</th>
            <th>Invoice #</th>
            <th>Payment Method</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Recorded By</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($expenses)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No ledger entries found for this date range.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($expenses as $e): ?>
              <tr>
                <td><?= esc($e['expense_date']) ?></td>
                <td><span class="badge badge-info"><?= esc($e['category_name']) ?></span></td>
                <td><?= esc($e['vendor_name'] ?? 'Direct Cash') ?></td>
                <td><?= esc($e['invoice_receipt_no'] ?? '-') ?></td>
                <td style="text-transform: capitalize;"><?= esc(str_replace('_', ' ', $e['payment_method'])) ?></td>
                <td><strong>&#8377;<?= number_format((float)$e['amount'], 2) ?></strong></td>
                <td>
                  <?php if ($e['status'] === 'approved'): ?>
                    <span class="badge badge-success">Approved</span>
                  <?php elseif ($e['status'] === 'pending'): ?>
                    <span class="badge badge-warning">Pending</span>
                  <?php else: ?>
                    <span class="badge badge-danger">Rejected</span>
                  <?php endif; ?>
                </td>
                <td><?= esc(($e['recorded_by_fname'] ?? '') . ' ' . ($e['recorded_by_lname'] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
