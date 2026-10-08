<div class="page-header">
  <div>
    <h1 class="page-title">Accounting & Finance</h1>
    <p class="page-subtitle">Profit & Loss statement, revenue metrics, expense tracking, and chart of accounts</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
    <a href="<?= site_url('admin/accounting/sales') ?>" class="btn btn-secondary">
      Sales Records
    </a>
    <a href="<?= site_url('admin/accounting/purchases') ?>" class="btn btn-secondary">
      Purchase Records
    </a>
    <a href="<?= site_url('admin/accounting/taxes') ?>" class="btn btn-secondary">
      Tax Records
    </a>
    <a href="<?= site_url('admin/expenses') ?>" class="btn btn-secondary">
      Expenses Ledger
    </a>
  </div>
</div>

<!-- DATE FILTER -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/accounting') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date" class="form-control pos-input-sm" value="<?= esc($startDate) ?>">
      </div>
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date" class="form-control pos-input-sm" value="<?= esc($endDate) ?>">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate P&amp;L</button>
      <a href="<?= site_url('admin/accounting?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-ghost btn-sm">This Month</a>
      <a href="<?= site_url('admin/accounting?start_date=' . date('Y-01-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-ghost btn-sm">Year to Date</a>
    </form>
  </div>
</div>

<!-- FINANCIAL KPI STATS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Gross Revenue</div>
    <div class="stat-value text-primary">&#8377;<?= number_format((float)$pnl['gross_revenue'], 2) ?></div>
    <div class="stat-sub">Before discounts &amp; COGS</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Cost of Goods (COGS)</div>
    <div class="stat-value text-warning">&#8377;<?= number_format((float)$pnl['cogs'], 2) ?></div>
    <div class="stat-sub">Raw ingredients &amp; purchases</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Operating Expenses</div>
    <div class="stat-value text-danger">&#8377;<?= number_format((float)$pnl['operating_expenses'], 2) ?></div>
    <div class="stat-sub">Utilities, rent, maintenance</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Net Operating Profit</div>
    <div class="stat-value <?= (float)$pnl['net_profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
      &#8377;<?= number_format((float)$pnl['net_profit'], 2) ?>
    </div>
    <div class="stat-sub">Margin: <?= esc($pnl['net_margin_percent']) ?>%</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
  <!-- PROFIT & LOSS INCOME STATEMENT -->
  <div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="card-title">Profit &amp; Loss Statement (<?= esc(date('M d, Y', strtotime($startDate))) ?> - <?= esc(date('M d, Y', strtotime($endDate))) ?>)</div>
      <button type="button" class="btn btn-ghost btn-sm" onclick="window.print()">Print Statement</button>
    </div>
    <div class="card-body" style="padding: 0;">
      <div class="table-wrapper">
        <table class="rms-table">
          <thead>
            <tr>
              <th>Financial Line Item</th>
              <th style="text-align: right;">Amount (&#8377;)</th>
            </tr>
          </thead>
          <tbody>
            <!-- REVENUE -->
            <tr style="background: rgba(255,255,255,0.02); font-weight: 700;">
              <td>1. REVENUE FROM OPERATIONS</td>
              <td style="text-align: right;"></td>
            </tr>
            <tr>
              <td style="padding-left: 2rem;">Gross Customer Food &amp; Beverage Sales</td>
              <td style="text-align: right;">&#8377;<?= number_format((float)$pnl['gross_revenue'], 2) ?></td>
            </tr>
            <tr>
              <td style="padding-left: 2rem; color: #ef4444;">Less: Promotional Discounts Given</td>
              <td style="text-align: right; color: #ef4444;">-&#8377;<?= number_format((float)$pnl['discounts'], 2) ?></td>
            </tr>
            <tr style="border-top: 1px dashed rgba(255,255,255,0.1); font-weight: 600;">
              <td style="padding-left: 1.5rem;">Net Revenue</td>
              <td style="text-align: right; color: #10b981;">&#8377;<?= number_format((float)$pnl['net_revenue'], 2) ?></td>
            </tr>

            <!-- COGS -->
            <tr style="background: rgba(255,255,255,0.02); font-weight: 700;">
              <td>2. COST OF GOODS SOLD (COGS)</td>
              <td style="text-align: right;"></td>
            </tr>
            <tr>
              <td style="padding-left: 2rem;">Food Supplies &amp; Ingredient Procurement (POs)</td>
              <td style="text-align: right; color: #f59e0b;">&#8377;<?= number_format((float)$pnl['cogs'], 2) ?></td>
            </tr>
            <tr style="border-top: 1px dashed rgba(255,255,255,0.1); font-weight: 600;">
              <td style="padding-left: 1.5rem;">Gross Profit</td>
              <td style="text-align: right;">
                &#8377;<?= number_format((float)$pnl['gross_profit'], 2) ?>
                <span style="font-size: 0.8rem; color: var(--text-muted);">(<?= esc($pnl['gross_margin_percent']) ?>%)</span>
              </td>
            </tr>

            <!-- OPERATING EXPENSES -->
            <tr style="background: rgba(255,255,255,0.02); font-weight: 700;">
              <td>3. OPERATING EXPENDITURES (OPEX)</td>
              <td style="text-align: right;"></td>
            </tr>
            <?php if (empty($pnl['category_breakdown'])): ?>
              <tr>
                <td style="padding-left: 2rem;" class="text-muted">No operational expenses logged in this period.</td>
                <td style="text-align: right;">&#8377;0.00</td>
              </tr>
            <?php else: ?>
              <?php foreach ($pnl['category_breakdown'] as $catName => $catAmount): ?>
                <tr>
                  <td style="padding-left: 2rem;"><?= esc($catName) ?></td>
                  <td style="text-align: right; color: #ef4444;">&#8377;<?= number_format((float)$catAmount, 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            <tr style="border-top: 1px dashed rgba(255,255,255,0.1); font-weight: 600;">
              <td style="padding-left: 1.5rem;">Total Operating Expenses</td>
              <td style="text-align: right; color: #ef4444;">-&#8377;<?= number_format((float)$pnl['operating_expenses'], 2) ?></td>
            </tr>

            <!-- NET PROFIT -->
            <tr style="background: rgba(59,130,246,0.1); font-weight: 800; font-size: 1.1rem; border-top: 2px solid rgba(255,255,255,0.2);">
              <td>NET OPERATING PROFIT / (LOSS)</td>
              <td style="text-align: right; color: <?= (float)$pnl['net_profit'] >= 0 ? '#10b981' : '#ef4444' ?>;">
                &#8377;<?= number_format((float)$pnl['net_profit'], 2) ?>
              </td>
            </tr>

            <!-- TAX SUMMARY -->
            <tr style="border-top: 1px solid rgba(255,255,255,0.1);">
              <td style="color: var(--text-muted);">GST / VAT Collected (Liability Payable)</td>
              <td style="text-align: right; color: #60a5fa;">&#8377;<?= number_format((float)$pnl['tax_collected'], 2) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- CHART OF ACCOUNTS SUMMARY -->
  <div>
    <div class="card mb-4">
      <div class="card-header">
        <div class="card-title">Chart of Accounts Balances</div>
      </div>
      <div class="card-body" style="padding: 0;">
        <div class="table-wrapper">
          <table class="rms-table">
            <thead>
              <tr>
                <th>Code</th>
                <th>Account</th>
                <th style="text-align: right;">Balance</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($accounts as $acc): ?>
                <tr>
                  <td><span style="font-family: monospace; color: #94a3b8;"><?= esc($acc['code']) ?></span></td>
                  <td>
                    <strong><?= esc($acc['name']) ?></strong>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);"><?= esc($acc['type']) ?></div>
                  </td>
                  <td style="text-align: right;">
                    <strong>&#8377;<?= number_format((float)$acc['current_balance'], 2) ?></strong>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- SALES CHANNEL MIX -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">Sales by Channel</div>
      </div>
      <div class="card-body">
        <?php foreach ($pnl['sales_type_breakdown'] as $channel => $amt): ?>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <span style="text-transform: capitalize;"><?= esc(str_replace('_', ' ', $channel)) ?></span>
            <strong>&#8377;<?= number_format((float)$amt, 2) ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
