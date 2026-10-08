<?php
  // Helper to preserve active query parameters when navigating tabs
  $buildTabUrl = function(string $statusOverride) use ($currentType, $paymentStatus, $datePreset, $fromDate, $toDate, $search) {
    $params = [
      'status'         => $statusOverride,
      'type'           => $currentType !== 'all' ? $currentType : null,
      'payment_status' => $paymentStatus !== 'all' ? $paymentStatus : null,
      'date_preset'    => $datePreset !== 'all' ? $datePreset : null,
      'from_date'      => $fromDate ?: null,
      'to_date'        => $toDate ?: null,
      'search'         => $search ?: null,
    ];
    $filtered = array_filter($params, fn($v) => $v !== null && $v !== '');
    return site_url('admin/orders' . (!empty($filtered) ? '?' . http_build_query($filtered) : ''));
  };
?>

<div class="page-header d-flex justify-between align-center mb-3">
  <div>
    <h1 class="page-title">Live Orders Pipeline</h1>
    <p class="page-subtitle">Real-time order progression<?= !empty($canAccessReports) ? ', date-wise reports, and kitchen dispatch tracking' : ' and kitchen dispatch tracking' ?></p>
  </div>
  <div class="page-actions d-flex gap-2">
    <?php if (!empty($canAccessReports)): ?>
      <button type="button" class="btn btn-outline d-flex align-center gap-1" onclick="exportFilteredCsv()" title="Download filtered orders report as CSV">
        <span>📥</span> Export CSV Report
      </button>
    <?php endif; ?>
    <a href="<?= site_url('admin/pos') ?>" class="btn btn-primary d-flex align-center gap-1">
      <span>+</span> Open POS Terminal
    </a>
  </div>
</div>

<?php if (!empty($canAccessReports)): ?>
<!-- OPERATIONAL & FINANCIAL PULSE STRIP (Super Admin, Manager, Cashier Only) -->
<div class="metrics-row mb-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
  <div class="stat-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1rem 1.25rem;">
    <div class="text-xs text-muted" style="text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Filtered Orders</div>
    <div style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">
      <?= (int)($metrics['totalOrders'] ?? count($orders)) ?>
    </div>
    <div class="text-xs text-muted mt-1">
      <?= $datePreset === 'today' ? 'Placed today' : ($datePreset === 'yesterday' ? 'Placed yesterday' : ($datePreset === 'all' ? 'All records' : 'In selected date range')) ?>
    </div>
  </div>

  <div class="stat-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1rem 1.25rem;">
    <div class="text-xs text-muted" style="text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Gross Total Volume</div>
    <div style="font-size: 1.6rem; font-weight: 800; color: #6366f1; margin-top: 0.25rem;">
      ₹<?= number_format((float)($metrics['totalRevenue'] ?? 0), 2) ?>
    </div>
    <div class="text-xs text-muted mt-1">Sum of filtered bills</div>
  </div>

  <div class="stat-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1rem 1.25rem;">
    <div class="text-xs text-muted" style="text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Settled / Paid</div>
    <div style="font-size: 1.6rem; font-weight: 800; color: #10b981; margin-top: 0.25rem;">
      ₹<?= number_format((float)($metrics['paidRevenue'] ?? 0), 2) ?>
    </div>
    <div class="text-xs text-muted mt-1">Completed tenders</div>
  </div>

  <div class="stat-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1rem 1.25rem;">
    <div class="text-xs text-muted" style="text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Pending / Unpaid</div>
    <div style="font-size: 1.6rem; font-weight: 800; color: #ef4444; margin-top: 0.25rem;">
      ₹<?= number_format((float)($metrics['pendingRevenue'] ?? 0), 2) ?>
    </div>
    <div class="text-xs text-muted mt-1">Awaiting settlement</div>
  </div>
</div>

<!-- DATE & FILTER TOOLBAR (Super Admin, Manager, Cashier Only) -->
<div class="card mb-3" style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1rem 1.25rem;">
  <form method="GET" action="<?= site_url('admin/orders') ?>" id="ordersFilterForm" class="d-flex flex-wrap align-end gap-2">
    <input type="hidden" name="status" value="<?= esc($currentStatus) ?>">

    <!-- Date Preset -->
    <div style="flex: 1; min-width: 140px;">
      <label class="form-label text-xs text-muted mb-1" style="font-weight: 600; text-transform: uppercase;">📅 Date Filter</label>
      <select name="date_preset" id="filter-date-preset" class="form-control pos-input-sm" onchange="handleDatePresetChange(this.value)">
        <option value="all" <?= $datePreset === 'all' ? 'selected' : '' ?>>All Time</option>
        <option value="today" <?= $datePreset === 'today' ? 'selected' : '' ?>>Today</option>
        <option value="yesterday" <?= $datePreset === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
        <option value="last_7_days" <?= $datePreset === 'last_7_days' ? 'selected' : '' ?>>Last 7 Days</option>
        <option value="this_month" <?= $datePreset === 'this_month' ? 'selected' : '' ?>>This Month</option>
        <option value="custom" <?= $datePreset === 'custom' ? 'selected' : '' ?>>Custom Range</option>
      </select>
    </div>

    <!-- From Date -->
    <div id="from-date-container" style="flex: 1; min-width: 135px; <?= ($datePreset === 'all') ? 'opacity: 0.7;' : '' ?>">
      <label class="form-label text-xs text-muted mb-1" style="font-weight: 600; text-transform: uppercase;">From Date</label>
      <input type="date" name="from_date" id="filter-from-date" class="form-control pos-input-sm" value="<?= esc($fromDate ?? '') ?>" onchange="markCustomPreset()">
    </div>

    <!-- To Date -->
    <div id="to-date-container" style="flex: 1; min-width: 135px; <?= ($datePreset === 'all') ? 'opacity: 0.7;' : '' ?>">
      <label class="form-label text-xs text-muted mb-1" style="font-weight: 600; text-transform: uppercase;">To Date</label>
      <input type="date" name="to_date" id="filter-to-date" class="form-control pos-input-sm" value="<?= esc($toDate ?? '') ?>" onchange="markCustomPreset()">
    </div>

    <!-- Order Type -->
    <div style="flex: 1; min-width: 125px;">
      <label class="form-label text-xs text-muted mb-1" style="font-weight: 600; text-transform: uppercase;">Order Type</label>
      <select name="type" class="form-control pos-input-sm">
        <option value="all" <?= $currentType === 'all' ? 'selected' : '' ?>>All Types</option>
        <option value="dine_in" <?= $currentType === 'dine_in' ? 'selected' : '' ?>>🍽️ Dine-In</option>
        <option value="takeaway" <?= $currentType === 'takeaway' ? 'selected' : '' ?>>🥡 Takeaway</option>
        <option value="delivery" <?= $currentType === 'delivery' ? 'selected' : '' ?>>🛵 Delivery</option>
      </select>
    </div>

    <!-- Payment Status -->
    <div style="flex: 1; min-width: 125px;">
      <label class="form-label text-xs text-muted mb-1" style="font-weight: 600; text-transform: uppercase;">Payment</label>
      <select name="payment_status" class="form-control pos-input-sm">
        <option value="all" <?= $paymentStatus === 'all' ? 'selected' : '' ?>>All Payments</option>
        <option value="paid" <?= $paymentStatus === 'paid' ? 'selected' : '' ?>>🟢 Paid</option>
        <option value="unpaid" <?= $paymentStatus === 'unpaid' ? 'selected' : '' ?>>🔴 Unpaid / Due</option>
      </select>
    </div>

    <!-- Keyword Search -->
    <div style="flex: 1.5; min-width: 160px;">
      <label class="form-label text-xs text-muted mb-1" style="font-weight: 600; text-transform: uppercase;">Search</label>
      <input type="text" name="search" class="form-control pos-input-sm" placeholder="Order #, Guest, Phone..." value="<?= esc($search ?? '') ?>">
    </div>

    <!-- Filter Action Buttons -->
    <div class="d-flex align-center gap-1" style="padding-bottom: 2px;">
      <button type="submit" class="btn btn-primary btn-sm" style="height: 36px; padding: 0 1rem;">
        🔍 Filter
      </button>
      <a href="<?= site_url('admin/orders') ?>" class="btn btn-secondary btn-sm" style="height: 36px; display: inline-flex; align-items: center; padding: 0 0.75rem;" title="Reset all filters">
        🔄 Reset
      </a>
      <button type="button" class="btn btn-success btn-sm" onclick="exportFilteredCsv()" style="height: 36px; display: inline-flex; align-items: center; gap: 4px; padding: 0 0.85rem;" title="Download filtered report as CSV">
        📥 CSV
      </button>
    </div>
  </form>
</div>
<?php else: ?>
<!-- OPERATIONAL QUICK FILTER (Waiters, Chefs, Staff) -->
<div class="card mb-3" style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 0.75rem 1rem;">
  <form method="GET" action="<?= site_url('admin/orders') ?>" class="d-flex flex-wrap align-end gap-2">
    <input type="hidden" name="status" value="<?= esc($currentStatus) ?>">
    <div style="flex: 1; min-width: 140px;">
      <label class="form-label text-xs text-muted mb-1" style="font-weight: 600; text-transform: uppercase;">Order Type</label>
      <select name="type" class="form-control pos-input-sm" onchange="this.form.submit()">
        <option value="all" <?= $currentType === 'all' ? 'selected' : '' ?>>All Types</option>
        <option value="dine_in" <?= $currentType === 'dine_in' ? 'selected' : '' ?>>🍽️ Dine-In</option>
        <option value="takeaway" <?= $currentType === 'takeaway' ? 'selected' : '' ?>>🥡 Takeaway</option>
        <option value="delivery" <?= $currentType === 'delivery' ? 'selected' : '' ?>>🛵 Delivery</option>
      </select>
    </div>
    <div style="flex: 2; min-width: 180px;">
      <label class="form-label text-xs text-muted mb-1" style="font-weight: 600; text-transform: uppercase;">Search Table or Order</label>
      <input type="text" name="search" class="form-control pos-input-sm" placeholder="Table #, Order #, Guest..." value="<?= esc($search ?? '') ?>">
    </div>
    <div class="d-flex align-center gap-1" style="padding-bottom: 2px;">
      <button type="submit" class="btn btn-primary btn-sm" style="height: 36px; padding: 0 1rem;">🔍 Filter</button>
      <a href="<?= site_url('admin/orders') ?>" class="btn btn-secondary btn-sm" style="height: 36px; display: inline-flex; align-items: center; padding: 0 0.75rem;">🔄 Reset</a>
    </div>
  </form>
</div>
<?php endif; ?>

<!-- STATUS PIPELINE TABS -->
<div class="pipeline-tabs mb-3">
  <a href="<?= $buildTabUrl('all') ?>" class="tab-chip <?= $currentStatus === 'all' ? 'active' : '' ?>">
    All Orders <span class="tab-count"><?= (int)($counts['all'] ?? 0) ?></span>
  </a>
  <a href="<?= $buildTabUrl('confirmed') ?>" class="tab-chip <?= $currentStatus === 'confirmed' ? 'active' : '' ?>">
    Confirmed <span class="tab-count"><?= (int)($counts['confirmed'] ?? 0) ?></span>
  </a>
  <a href="<?= $buildTabUrl('preparing') ?>" class="tab-chip <?= $currentStatus === 'preparing' ? 'active' : '' ?>">
    In Kitchen <span class="tab-count"><?= (int)($counts['preparing'] ?? 0) ?></span>
  </a>
  <a href="<?= $buildTabUrl('ready') ?>" class="tab-chip <?= $currentStatus === 'ready' ? 'active' : '' ?>">
    Ready to Serve <span class="tab-count"><?= (int)($counts['ready'] ?? 0) ?></span>
  </a>
  <a href="<?= $buildTabUrl('served') ?>" class="tab-chip <?= $currentStatus === 'served' ? 'active' : '' ?>">
    Served <span class="tab-count"><?= (int)($counts['served'] ?? 0) ?></span>
  </a>
  <a href="<?= $buildTabUrl('completed') ?>" class="tab-chip <?= $currentStatus === 'completed' ? 'active' : '' ?>">
    Completed <span class="tab-count"><?= (int)($counts['completed'] ?? 0) ?></span>
  </a>
  <a href="<?= $buildTabUrl('cancelled') ?>" class="tab-chip <?= $currentStatus === 'cancelled' ? 'active' : '' ?>">
    Cancelled <span class="tab-count"><?= (int)($counts['cancelled'] ?? 0) ?></span>
  </a>
</div>

<div class="card">
  <div class="card-header d-flex justify-between align-center">
    <div class="card-title d-flex align-center gap-2">
      <span>Orders Pipeline</span>
      <span class="badge badge-secondary"><?= count($orders) ?> displayed</span>
      <?php if (!empty($fromDate) || !empty($toDate)): ?>
        <span class="badge badge-info" style="font-size: 0.75rem;">
          📅 <?= esc($fromDate ?: '...') ?> &rarr; <?= esc($toDate ?: '...') ?>
        </span>
      <?php endif; ?>
    </div>
    <div class="d-flex align-center gap-1">
      <button class="btn btn-secondary btn-sm" onclick="location.reload()">&#8635; Refresh</button>
      <?php if (!empty($canAccessReports)): ?>
        <button class="btn btn-success btn-sm" onclick="exportFilteredCsv()" title="Export currently shown orders">📥 Export CSV</button>
      <?php endif; ?>
    </div>
  </div>

  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Type</th>
            <th>Table / Location</th>
            <th>Guest Info</th>
            <th>Items</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Placed On</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="9" class="text-center text-muted" style="padding: 3rem 1rem;">
                No orders found matching the selected filters.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr>
                <td class="td-bold">
                  <a href="<?= site_url('admin/orders/show/' . $o['id']) ?>" class="text-primary" style="text-decoration:none; font-weight: 700;">
                    <?= esc($o['order_number']) ?>
                  </a>
                </td>
                <td>
                  <?php if ($o['order_type'] === 'dine_in'): ?>
                    <span class="badge badge-primary">🍽️ Dine-In</span>
                  <?php elseif ($o['order_type'] === 'takeaway'): ?>
                    <span class="badge badge-warning">🥡 Takeaway</span>
                  <?php else: ?>
                    <span class="badge badge-info">🛵 Delivery</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($o['table_number'])): ?>
                    <strong><?= esc($o['table_number']) ?></strong>
                    <div class="text-xs text-muted"><?= esc($o['floor_name'] ?? '') ?></div>
                  <?php else: ?>
                    <span class="text-muted text-xs">Counter / Direct</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="text-small" style="font-weight: 500;"><?= esc($o['customer_name'] ?: 'Guest') ?></div>
                  <div class="text-xs text-muted"><?= esc($o['customer_phone'] ?: '—') ?></div>
                </td>
                <td>
                  <span class="badge badge-secondary"><?= (int)$o['total_items'] ?> items</span>
                </td>
                <td class="td-bold" style="color: var(--accent);">
                  ₹<?= esc(number_format((float)$o['final_total'], 2)) ?>
                  <div class="text-xs <?= $o['payment_status'] === 'paid' ? 'text-success' : 'text-danger' ?>" style="font-weight: 600;">
                    <?= esc(ucfirst($o['payment_status'])) ?>
                    <?php if (!empty($o['payment_method_name'])): ?>
                      <span class="text-muted" style="font-weight: normal;">(<?= esc(ucfirst($o['payment_method_name'])) ?>)</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <?php
                    $statusClass = 'status-pending';
                    if ($o['status'] === 'completed') $statusClass = 'status-active';
                    elseif ($o['status'] === 'preparing') $statusClass = 'status-warning';
                    elseif ($o['status'] === 'ready') $statusClass = 'status-info';
                    elseif ($o['status'] === 'cancelled') $statusClass = 'status-danger';
                  ?>
                  <span class="status-badge <?= $statusClass ?>">
                    <?= esc(ucfirst($o['status'])) ?>
                  </span>
                </td>
                <td>
                  <div>
                    <span class="text-xs" style="font-weight: 600;"><?= esc(date('d M Y', strtotime($o['created_at']))) ?></span>
                    <div class="text-xs text-secondary"><?= esc(date('h:i:s A', strtotime($o['created_at']))) ?></div>
                  </div>
                </td>
                <td style="text-align: right;">
                  <div class="td-actions" style="justify-content: flex-end;">
                    <?php if ($o['status'] === 'confirmed'): ?>
                      <button type="button" class="btn btn-warning btn-sm" onclick="changeStatus(<?= (int)$o['id'] ?>, 'preparing')">
                        🍳 Start Prep
                      </button>
                    <?php elseif ($o['status'] === 'preparing'): ?>
                      <button type="button" class="btn btn-info btn-sm" onclick="changeStatus(<?= (int)$o['id'] ?>, 'ready')">
                        🔔 Mark Ready
                      </button>
                    <?php elseif ($o['status'] === 'ready'): ?>
                      <button type="button" class="btn btn-primary btn-sm" onclick="changeStatus(<?= (int)$o['id'] ?>, 'served')">
                        🍽️ Mark Served
                      </button>
                    <?php endif; ?>

                    <?php if ($o['payment_status'] !== 'paid' && $o['status'] !== 'cancelled'): ?>
                      <button type="button" class="btn btn-success btn-sm" onclick='openOrderSettleModal(<?= json_encode($o, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' title="Settle payment and close bill">
                        💳 Settle Bill
                      </button>
                    <?php endif; ?>

                    <a href="<?= site_url('admin/orders/show/' . $o['id']) ?>" class="btn btn-secondary btn-sm" title="View Details">
                      Details
                    </a>
                    <a href="<?= site_url('admin/orders/receipt/' . $o['id']) ?>" target="_blank" class="btn btn-ghost btn-sm" title="Print Bill">
                      🖨️
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>



<!-- ========================================== -->
<!-- MODAL: SETTLE ORDER PAYMENT                -->
<!-- ========================================== -->
<div class="modal-backdrop" id="orderSettleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div class="modal-card" style="background: var(--surface, #1e293b); border: 1px solid var(--border, rgba(255,255,255,0.1)); border-radius: 12px; width: 100%; max-width: 540px; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); max-height: 90vh; overflow-y: auto;">
    <div class="d-flex justify-between align-center mb-3 pb-2" style="border-bottom: 1px solid var(--border, rgba(255,255,255,0.08));">
      <div>
        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-primary, #f8fafc);">💳 Settle Order Payment</h3>
        <p class="text-xs text-muted" id="settle-order-subtitle" style="margin: 0.2rem 0 0 0;">Record tender and close the bill</p>
      </div>
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeOrderSettleModal()" style="font-size: 1.4rem; line-height: 1;">&times;</button>
    </div>

    <!-- LIVE PRICE BREAKDOWN CARD -->
    <div class="settle-breakdown-card p-3 mb-3">
      <div class="d-flex justify-between text-muted text-small mb-1">
        <span>Bill Subtotal &amp; Taxes:</span>
        <span class="fw-600" id="settle-original-amt">₹0.00</span>
      </div>
      <div class="d-flex justify-between text-success text-small mb-1" id="settle-discount-row" style="display: none;">
        <span id="settle-discount-label">Discount Applied:</span>
        <span class="fw-700" id="settle-discount-amt">-₹0.00</span>
      </div>
      <div class="d-flex justify-between align-center pt-2 settle-net-row">
        <div>
          <div class="text-xs text-muted" style="text-transform: uppercase; letter-spacing: 0.5px;">Net Payable Amount</div>
          <div class="text-xs text-muted fw-600" id="settle-order-ref"></div>
        </div>
        <div class="text-success settle-net-val" id="settle-bill-amount">₹0.00</div>
      </div>
    </div>

    <!-- DISCOUNT & COUPON SECTION -->
    <div class="discount-box mb-3">
      <div class="d-flex justify-between align-center mb-2">
        <div class="fw-700 text-small d-flex align-center gap-1">
          <span>🎟️</span> <span>Discounts &amp; Offers</span>
        </div>
        <div class="discount-nav-tabs">
          <button type="button" class="discount-tab-btn active" id="tab-btn-coupon" onclick="switchDiscountTab('coupon')">🏷️ Coupon Code</button>
          <button type="button" class="discount-tab-btn" id="tab-btn-manual" onclick="switchDiscountTab('manual')">✂️ Custom Discount</button>
          <button type="button" class="discount-tab-btn" id="tab-btn-loyalty" onclick="switchDiscountTab('loyalty')">🎁 Redeem Points</button>
        </div>
      </div>

      <!-- PANE 1: PROMOTIONAL COUPON -->
      <div id="pane-coupon" class="discount-pane">
        <div class="d-flex gap-2">
          <div style="flex: 1;">
            <input type="text" id="coupon-code-input" class="form-control pos-input-sm" placeholder="ENTER COUPON CODE (E.G. WELCOME20)" style="text-transform: uppercase; font-family: monospace; font-weight: 700; letter-spacing: 1px; width: 100%;">
          </div>
          <button type="button" class="btn btn-primary btn-sm" id="btn-apply-coupon" onclick="applyCouponCode()">Apply Code</button>
        </div>

        <!-- Active Available Coupons Quick Select -->
        <?php if (!empty($coupons)): ?>
          <div class="mt-2">
            <div class="text-xs text-muted mb-1">Available Offers (Click to Apply):</div>
            <div class="coupon-chips-list">
              <?php foreach ($coupons as $ac): ?>
                <div class="coupon-chip" onclick="selectCouponChip('<?= esc($ac['code']) ?>')">
                  <span class="coupon-chip-code"><?= esc($ac['code']) ?></span>
                  <span class="coupon-chip-desc"><?= $ac['type'] === 'percentage' ? ((float)$ac['value'] . '% Off') : ('₹' . (float)$ac['value'] . ' Off') ?></span>
                  <?php if ((float)$ac['min_order_amount'] > 0): ?>
                    <span class="coupon-chip-min">Min ₹<?= (float)$ac['min_order_amount'] ?></span>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- PANE 2: CUSTOM MANUAL DISCOUNT -->
      <div id="pane-manual" class="discount-pane" style="display: none;">
        <div class="d-flex gap-2 align-center mb-2">
          <div class="d-flex gap-1">
            <button type="button" class="btn btn-xs btn-primary" id="btn-disc-type-pct" onclick="setManualDiscType('percentage')">% Percent</button>
            <button type="button" class="btn btn-xs btn-secondary" id="btn-disc-type-fixed" onclick="setManualDiscType('fixed')">₹ Fixed</button>
          </div>
          <div style="flex: 1;">
            <input type="number" step="0.01" min="0" id="manual-disc-val" class="form-control pos-input-sm" placeholder="Percent % (e.g. 10)" style="width: 100%;">
          </div>
          <button type="button" class="btn btn-primary btn-sm" onclick="applyManualDiscount()">Apply</button>
        </div>
        <div class="d-flex gap-2 align-center">
          <label class="text-xs text-muted" style="white-space: nowrap;">Reason:</label>
          <select id="manual-disc-reason" class="form-control pos-input-sm" style="font-size: .8rem; width: 100%;">
            <option value="Manager Discretion">Manager Discretion</option>
            <option value="Staff Privilege Discount">Staff Privilege Discount</option>
            <option value="Customer Goodwill / Delay">Customer Goodwill / Delay</option>
            <option value="Special Festival Offer">Special Festival Offer</option>
            <option value="VIP Regular Diner">VIP Regular Diner</option>
            <option value="Other Discretionary">Other Discretionary</option>
          </select>
        </div>
      </div>

      <!-- PANE 3: LOYALTY REWARD POINTS REDEMPTION -->
      <div id="pane-loyalty" class="discount-pane" style="display: none;">
        <!-- State A: Phone lookup -->
        <div id="loyalty-no-customer" style="display: none;">
          <div class="text-xs text-muted mb-2">
            Enter customer's registered phone number to check their reward points balance:
          </div>
          <div class="d-flex gap-2">
            <input type="tel" id="modal-cust-phone" class="form-control pos-input-sm" placeholder="Enter phone (e.g. 9876543210)" style="flex: 1;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="lookupCustomerFromModal()">Check Points</button>
          </div>
        </div>

        <!-- State B: Customer info with points -->
        <div id="loyalty-customer-info" style="display: none;">
          <div class="p-2 mb-2" style="background: rgba(99, 102, 241, 0.08); border-radius: 6px; border: 1px solid rgba(99, 102, 241, 0.25);">
            <div class="d-flex justify-between align-center">
              <div>
                <div class="fw-700" id="loyalty-cust-name" style="color: var(--text-primary); font-size: 0.85rem;">Customer Name</div>
                <div class="text-xs text-muted" id="loyalty-cust-phone">+91 0000000000</div>
              </div>
              <div class="text-right">
                <div class="badge" id="loyalty-balance-badge" style="font-size: 0.8rem; font-weight: 700; background: #f59e0b; color: #000; padding: 3px 8px; border-radius: 4px;">
                  ⭐ <span id="loyalty-avail-points">0</span> Points
                </div>
                <div class="text-xs text-muted mt-1" id="loyalty-worth-text">Worth ₹0.00 (1 pt = ₹1)</div>
              </div>
            </div>
          </div>

          <div id="loyalty-redeem-controls">
            <div class="d-flex gap-2 align-center mb-1">
              <div style="flex: 1;">
                <label class="text-xs text-muted mb-1 d-block">Points to Redeem (Max: <strong id="loyalty-max-allowed" class="text-primary">0</strong>):</label>
                <div class="d-flex gap-1">
                  <input type="number" step="1" min="1" id="loyalty-redeem-input" class="form-control pos-input-sm" placeholder="Points (e.g. 50)" style="flex: 1;">
                  <button type="button" class="btn btn-secondary btn-xs" onclick="setLoyaltyRedeemMax()">Max</button>
                </div>
              </div>
              <div style="align-self: flex-end;">
                <button type="button" class="btn btn-success btn-sm" id="btn-apply-loyalty" onclick="applyLoyaltyPointsDiscount()">
                  Apply Points
                </button>
              </div>
            </div>
            <div class="text-xs text-muted mt-1">
              💡 1 point equals ₹1 instant bill discount.
            </div>
          </div>

          <div id="loyalty-zero-points-msg" class="text-xs text-muted p-2" style="display: none; background: rgba(255,255,255,0.03); border-radius: 4px; border: 1px dashed var(--border);">
            ⭐ This customer currently has 0 points. They will earn 1 pt per ₹10 on this order upon payment.
          </div>
        </div>

        <!-- State C: New customer message -->
        <div id="loyalty-new-customer-msg" class="text-xs text-muted p-2" style="display: none; background: rgba(59, 130, 246, 0.08); border-radius: 4px; border: 1px dashed rgba(59, 130, 246, 0.3);">
          ✨ New Customer • No existing reward points yet. This customer will earn reward points (1 pt per ₹10) on this order!
        </div>
      </div>

      <!-- APPLIED DISCOUNT STATUS BANNER -->
      <div id="applied-discount-banner" class="discount-applied-banner" style="display: none;">
        <div class="d-flex align-center gap-1">
          <span id="applied-discount-title">✅ Discount Applied</span>
          <span class="text-xs" id="applied-discount-desc"></span>
        </div>
        <button type="button" class="btn btn-xs btn-danger" onclick="removeDiscount()">Remove &times;</button>
      </div>
      <div id="coupon-error-msg" class="text-xs text-danger mt-1" style="display: none;"></div>
    </div>

    <form id="orderSettleForm" onsubmit="submitOrderSettle(event)">
      <input type="hidden" id="settle-order-id" value="">

      <!-- Payment Tender Selection -->
      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem;">Select Payment Tender</label>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.5rem;">
          <?php foreach ($paymentMethods ?? [] as $idx => $pm): ?>
            <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 0.75rem; border: 1px solid var(--border, rgba(255,255,255,0.1)); border-radius: 8px; cursor: pointer; background: rgba(255,255,255,0.02); transition: all 0.15s;" class="pm-opt-label">
              <input type="radio" name="order_payment_method" value="<?= (int)$pm['id'] ?>" <?= $idx === 0 ? 'checked' : '' ?> style="accent-color: var(--accent, #6366f1);">
              <span style="font-weight: 500; font-size: 0.9rem;">
                <?= esc($pm['name'] === 'cash' ? '💵 Cash' : ($pm['name'] === 'card' ? '💳 Card' : ($pm['name'] === 'upi' ? '📱 UPI' : esc(ucfirst($pm['name']))))) ?>
              </span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="d-flex justify-end gap-2 pt-2" style="border-top: 1px solid var(--border, rgba(255,255,255,0.08));">
        <button type="button" class="btn btn-secondary" onclick="closeOrderSettleModal()">Cancel</button>
        <button type="submit" class="btn btn-success" id="btn-confirm-settle" style="font-weight: 600;">
          ✔ Confirm &amp; Complete Bill
        </button>
      </div>
    </form>
  </div>
</div>

<style>
.pipeline-tabs {
  display: flex;
  gap: .5rem;
  overflow-x: auto;
  white-space: nowrap;
}
.tab-chip {
  background: var(--surface-raised);
  border: 1px solid var(--border);
  color: var(--text-secondary);
  padding: .45rem 1rem;
  border-radius: var(--radius-sm);
  text-decoration: none;
  font-size: .85rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  transition: all .2s;
}
.tab-chip.active, .tab-chip:hover {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
}
.tab-count {
  background: rgba(0,0,0,0.3);
  padding: 1px 6px;
  border-radius: 10px;
  font-size: .75rem;
  font-weight: 700;
}
.pm-opt-label:hover {
  background: rgba(99, 102, 241, 0.08) !important;
  border-color: var(--accent, #6366f1) !important;
}

/* Settle Breakdown & Discount Styles matching POS */
.settle-breakdown-card {
  background: rgba(15, 23, 42, 0.55);
  border: 1px solid var(--border, rgba(255, 255, 255, 0.1));
  border-radius: 8px;
  padding: 0.9rem;
}
.settle-net-row {
  border-top: 1px dashed var(--border, rgba(255, 255, 255, 0.12));
  margin-top: .4rem;
  padding-top: .4rem;
}
.settle-net-val {
  font-size: 1.85rem;
  font-weight: 800;
  letter-spacing: -0.5px;
}
.discount-box {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid var(--border, rgba(255, 255, 255, 0.1));
  border-radius: 8px;
  padding: .75rem;
}
.discount-nav-tabs {
  display: flex;
  gap: 4px;
  background: var(--surface-raised, rgba(0, 0, 0, 0.3));
  padding: 2px;
  border-radius: 6px;
}
.discount-tab-btn {
  background: none;
  border: none;
  font-size: .75rem;
  font-weight: 600;
  color: var(--text-muted, #94a3b8);
  padding: .25rem .55rem;
  border-radius: 4px;
  cursor: pointer;
  transition: all .15s;
}
.discount-tab-btn.active {
  background: var(--primary, #6366f1);
  color: #fff;
}
.coupon-chips-list {
  display: flex;
  flex-wrap: wrap;
  gap: .35rem;
}
.coupon-chip {
  background: rgba(255, 255, 255, 0.04);
  border: 1px dashed var(--border, rgba(255, 255, 255, 0.2));
  border-radius: 5px;
  padding: .25rem .55rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  font-size: .75rem;
  transition: all .15s;
}
.coupon-chip:hover {
  border-color: var(--primary, #6366f1);
  background: rgba(99, 102, 241, 0.15);
}
.coupon-chip-code {
  font-weight: 700;
  color: #818cf8;
  font-family: monospace;
}
.coupon-chip-desc {
  color: var(--text-secondary, #cbd5e1);
}
.coupon-chip-min {
  background: rgba(0,0,0,0.3);
  color: var(--text-muted, #94a3b8);
  font-size: 10px;
  padding: 1px 4px;
  border-radius: 3px;
  border: 1px solid rgba(255,255,255,0.1);
}
.discount-applied-banner {
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid #10b981;
  border-radius: 6px;
  padding: .4rem .75rem;
  margin-top: .6rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #34d399;
}
.btn-xs {
  padding: 0.2rem 0.5rem;
  font-size: 0.75rem;
  line-height: 1.2;
}
</style>

<script>
function handleDatePresetChange(val) {
  const fromEl = document.getElementById('filter-from-date');
  const toEl = document.getElementById('filter-to-date');
  const fromCont = document.getElementById('from-date-container');
  const toCont = document.getElementById('to-date-container');

  const today = new Date();
  const pad = (n) => String(n).padStart(2, '0');
  const formatDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

  if (val === 'today') {
    fromEl.value = formatDate(today);
    toEl.value = formatDate(today);
    fromCont.style.opacity = '1';
    toCont.style.opacity = '1';
  } else if (val === 'yesterday') {
    const yest = new Date();
    yest.setDate(today.getDate() - 1);
    fromEl.value = formatDate(yest);
    toEl.value = formatDate(yest);
    fromCont.style.opacity = '1';
    toCont.style.opacity = '1';
  } else if (val === 'last_7_days') {
    const l7 = new Date();
    l7.setDate(today.getDate() - 6);
    fromEl.value = formatDate(l7);
    toEl.value = formatDate(today);
    fromCont.style.opacity = '1';
    toCont.style.opacity = '1';
  } else if (val === 'this_month') {
    const mStart = new Date(today.getFullYear(), today.getMonth(), 1);
    fromEl.value = formatDate(mStart);
    toEl.value = formatDate(today);
    fromCont.style.opacity = '1';
    toCont.style.opacity = '1';
  } else if (val === 'all') {
    fromEl.value = '';
    toEl.value = '';
    fromCont.style.opacity = '0.7';
    toCont.style.opacity = '0.7';
  } else {
    fromCont.style.opacity = '1';
    toCont.style.opacity = '1';
  }
}

function markCustomPreset() {
  const presetSel = document.getElementById('filter-date-preset');
  if (presetSel && presetSel.value !== 'custom') {
    presetSel.value = 'custom';
  }
  const fromCont = document.getElementById('from-date-container');
  const toCont = document.getElementById('to-date-container');
  if (fromCont) fromCont.style.opacity = '1';
  if (toCont) toCont.style.opacity = '1';
}

function exportFilteredCsv() {
  const form = document.getElementById('ordersFilterForm');
  if (!form) {
    window.location.href = '<?= site_url('admin/orders/export') ?>';
    return;
  }
  const formData = new FormData(form);
  const params = new URLSearchParams(formData);
  params.set('export', 'csv');
  window.location.href = '<?= site_url('admin/orders') ?>?' + params.toString();
}

function changeStatus(orderId, nextStatus) {
  if (!confirm('Advance order to ' + nextStatus.toUpperCase() + '?')) return;
  window.rmsPost('<?= site_url('admin/orders/status') ?>/' + orderId, { status: nextStatus })
    .then(function(res) {
      if (res.success) {
        location.reload();
      } else {
        alert(res.message || 'Error updating order');
      }
    })
    .catch(function(err) {
      alert('Request failed');
    });
}

var currentOriginalBill = 0.0;
var currentDiscount = {
  type: null,
  amount: 0.0,
  couponId: null,
  couponCode: '',
  redeemedPoints: 0,
  reason: '',
  label: ''
};
var manualDiscType = 'percentage';
var activeCustomer = null;

function switchDiscountTab(tab) {
  var btnCp = document.getElementById('tab-btn-coupon');
  var btnMan = document.getElementById('tab-btn-manual');
  var btnLoyalty = document.getElementById('tab-btn-loyalty');
  var paneCp = document.getElementById('pane-coupon');
  var paneMan = document.getElementById('pane-manual');
  var paneLoyalty = document.getElementById('pane-loyalty');

  if (btnCp) btnCp.classList.toggle('active', tab === 'coupon');
  if (btnMan) btnMan.classList.toggle('active', tab === 'manual');
  if (btnLoyalty) btnLoyalty.classList.toggle('active', tab === 'loyalty');

  if (paneCp) paneCp.style.display = (tab === 'coupon') ? 'block' : 'none';
  if (paneMan) paneMan.style.display = (tab === 'manual') ? 'block' : 'none';
  if (paneLoyalty) {
    paneLoyalty.style.display = (tab === 'loyalty') ? 'block' : 'none';
    if (tab === 'loyalty') {
      prepareLoyaltyPane();
    }
  }
}

function setManualDiscType(type) {
  manualDiscType = type;
  var btnPct = document.getElementById('btn-disc-type-pct');
  var btnFix = document.getElementById('btn-disc-type-fixed');
  var inputEl = document.getElementById('manual-disc-val');
  if (type === 'percentage') {
    if (btnPct) btnPct.className = 'btn btn-xs btn-primary';
    if (btnFix) btnFix.className = 'btn btn-xs btn-secondary';
    if (inputEl) inputEl.placeholder = 'Percent % (e.g. 10)';
  } else {
    if (btnFix) btnFix.className = 'btn btn-xs btn-primary';
    if (btnPct) btnPct.className = 'btn btn-xs btn-secondary';
    if (inputEl) inputEl.placeholder = 'Amount ₹ (e.g. 50)';
  }
}

function selectCouponChip(code) {
  var codeIn = document.getElementById('coupon-code-input');
  if (codeIn) codeIn.value = code;
  applyCouponCode();
}

function applyCouponCode() {
  var code = (document.getElementById('coupon-code-input')?.value || '').trim();
  var errEl = document.getElementById('coupon-error-msg');
  if (errEl) { errEl.style.display = 'none'; errEl.textContent = ''; }

  if (!code) {
    if (errEl) {
      errEl.textContent = 'Please enter or select a coupon code.';
      errEl.style.display = 'block';
    }
    return;
  }

  var btn = document.getElementById('btn-apply-coupon');
  if (btn) {
    btn.disabled = true;
    btn.textContent = 'Checking...';
  }

  var custPhone = (activeCustomer && activeCustomer.phone) ? activeCustomer.phone : '';

  window.rmsPost('<?= site_url('admin/pos/validate-coupon') ?>', {
    code: code,
    order_amount: currentOriginalBill,
    customer_phone: custPhone
  }).then(function(res) {
    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Apply Code';
    }

    if (res.success && res.data) {
      currentDiscount = {
        type: 'coupon',
        amount: parseFloat(res.data.discount_amount || 0),
        couponId: res.data.coupon_id,
        couponCode: res.data.code,
        redeemedPoints: 0,
        reason: 'Coupon: ' + res.data.code,
        label: res.data.code + ' (' + res.data.name + ')'
      };
      renderDiscountState();
    } else {
      if (errEl) {
        errEl.textContent = res.message || 'Invalid or expired coupon code.';
        errEl.style.display = 'block';
      }
    }
  }).catch(function(err) {
    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Apply Code';
    }
    if (errEl) {
      errEl.textContent = 'Error verifying coupon. Please check network.';
      errEl.style.display = 'block';
    }
  });
}

function applyManualDiscount() {
  var val = parseFloat(document.getElementById('manual-disc-val')?.value || 0);
  var reason = document.getElementById('manual-disc-reason')?.value || 'Manual Discount';
  var errEl = document.getElementById('coupon-error-msg');
  if (errEl) errEl.style.display = 'none';

  if (isNaN(val) || val <= 0) {
    if (errEl) {
      errEl.textContent = 'Please enter a valid discount value greater than 0.';
      errEl.style.display = 'block';
    }
    return;
  }

  var discAmt = 0;
  if (manualDiscType === 'percentage') {
    if (val > 100) {
      if (errEl) {
        errEl.textContent = 'Percentage discount cannot exceed 100%.';
        errEl.style.display = 'block';
      }
      return;
    }
    discAmt = (currentOriginalBill * val) / 100;
  } else {
    discAmt = Math.min(val, currentOriginalBill);
  }

  currentDiscount = {
    type: 'manual',
    amount: discAmt,
    couponId: null,
    couponCode: '',
    redeemedPoints: 0,
    reason: reason,
    label: (manualDiscType === 'percentage' ? val + '% ' : '₹' + val.toFixed(2) + ' ') + reason
  };

  renderDiscountState();
}

function performCustomerLookup(phone, callback) {
  phone = (phone || '').trim();
  if (!phone || phone.length < 5) {
    activeCustomer = null;
    if (typeof callback === 'function') callback(null);
    return;
  }

  window.rmsGet('<?= site_url('admin/pos/lookup-customer') ?>?phone=' + encodeURIComponent(phone))
    .then(function(res) {
      if (res.success && res.data && res.data.exists && res.data.customer) {
        activeCustomer = res.data.customer;
      } else {
        activeCustomer = { exists: false, phone: phone };
      }
      if (typeof callback === 'function') callback(activeCustomer);
    }).catch(function() {
      if (typeof callback === 'function') callback(null);
    });
}

function lookupCustomerFromModal() {
  var modalPhone = (document.getElementById('modal-cust-phone')?.value || '').trim();
  if (!modalPhone) {
    alert('Please enter a customer phone number.');
    return;
  }
  performCustomerLookup(modalPhone, function() {
    prepareLoyaltyPane();
  });
}

function prepareLoyaltyPane() {
  var noCustBox   = document.getElementById('loyalty-no-customer');
  var infoBox     = document.getElementById('loyalty-customer-info');
  var newCustBox  = document.getElementById('loyalty-new-customer-msg');
  var zeroMsg     = document.getElementById('loyalty-zero-points-msg');
  var redeemCtrls = document.getElementById('loyalty-redeem-controls');

  if (activeCustomer && activeCustomer.id) {
    // Registered returning customer
    if (noCustBox) noCustBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'none';
    if (infoBox) infoBox.style.display = 'block';

    var nameEl = document.getElementById('loyalty-cust-name');
    if (nameEl) nameEl.textContent = activeCustomer.name;
    var phoneEl = document.getElementById('loyalty-cust-phone');
    if (phoneEl) phoneEl.textContent = activeCustomer.phone;
    var availEl = document.getElementById('loyalty-avail-points');
    if (availEl) availEl.textContent = activeCustomer.loyalty_points;
    var worthEl = document.getElementById('loyalty-worth-text');
    if (worthEl) worthEl.textContent = 'Worth ₹' + (activeCustomer.points_value || activeCustomer.loyalty_points).toFixed(2) + ' (1 pt = ₹1)';

    var maxPts = Math.min(activeCustomer.loyalty_points, Math.floor(currentOriginalBill));
    var maxEl = document.getElementById('loyalty-max-allowed');
    if (maxEl) maxEl.textContent = maxPts;

    if (activeCustomer.loyalty_points > 0 && maxPts > 0) {
      if (redeemCtrls) redeemCtrls.style.display = 'block';
      if (zeroMsg) zeroMsg.style.display = 'none';
      var inputEl = document.getElementById('loyalty-redeem-input');
      if (inputEl && !inputEl.value) {
        inputEl.value = maxPts;
      }
    } else {
      if (redeemCtrls) redeemCtrls.style.display = 'none';
      if (zeroMsg) zeroMsg.style.display = 'block';
    }
  } else if (activeCustomer && activeCustomer.exists === false) {
    if (noCustBox) noCustBox.style.display = 'none';
    if (infoBox) infoBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'block';
  } else {
    if (infoBox) infoBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'none';
    if (noCustBox) noCustBox.style.display = 'block';
  }
}

function setLoyaltyRedeemMax() {
  var maxPts = parseInt(document.getElementById('loyalty-max-allowed')?.textContent || '0', 10);
  var inputEl = document.getElementById('loyalty-redeem-input');
  if (inputEl) inputEl.value = maxPts;
}

function applyLoyaltyPointsDiscount() {
  var errEl = document.getElementById('coupon-error-msg');
  if (errEl) errEl.style.display = 'none';

  if (!activeCustomer || !activeCustomer.id) {
    alert('No registered customer selected.');
    return;
  }

  var inputEl = document.getElementById('loyalty-redeem-input');
  var pts = parseInt(inputEl ? inputEl.value : '0', 10);

  if (isNaN(pts) || pts <= 0) {
    if (errEl) {
      errEl.textContent = 'Please enter a valid number of points to redeem (greater than 0).';
      errEl.style.display = 'block';
    }
    return;
  }

  if (pts > activeCustomer.loyalty_points) {
    if (errEl) {
      errEl.textContent = 'Cannot redeem more than available balance (' + activeCustomer.loyalty_points + ' points).';
      errEl.style.display = 'block';
    }
    return;
  }

  if (pts > currentOriginalBill) {
    if (errEl) {
      errEl.textContent = 'Points discount cannot exceed the total bill amount (₹' + currentOriginalBill.toFixed(2) + ').';
      errEl.style.display = 'block';
    }
    return;
  }

  var discountAmt = pts * 1.0; // 1 point = ₹1.00

  currentDiscount = {
    type: 'loyalty',
    amount: discountAmt,
    couponId: null,
    couponCode: '',
    redeemedPoints: pts,
    reason: 'Loyalty Points (' + pts + ' pts)',
    label: pts + ' Points (₹' + discountAmt.toFixed(2) + ')'
  };

  renderDiscountState();
}

function removeDiscount() {
  currentDiscount = {
    type: null,
    amount: 0.0,
    couponId: null,
    couponCode: '',
    redeemedPoints: 0,
    reason: '',
    label: ''
  };
  var codeIn = document.getElementById('coupon-code-input');
  if (codeIn) codeIn.value = '';
  var valIn = document.getElementById('manual-disc-val');
  if (valIn) valIn.value = '';
  var loyaltyIn = document.getElementById('loyalty-redeem-input');
  if (loyaltyIn) loyaltyIn.value = '';
  var errEl = document.getElementById('coupon-error-msg');
  if (errEl) errEl.style.display = 'none';
  renderDiscountState();
}

function renderDiscountState() {
  var origEl = document.getElementById('settle-original-amt');
  var discRow = document.getElementById('settle-discount-row');
  var discLabel = document.getElementById('settle-discount-label');
  var discAmtEl = document.getElementById('settle-discount-amt');
  var netDisplay = document.getElementById('settle-bill-amount');
  var banner = document.getElementById('applied-discount-banner');
  var bannerTitle = document.getElementById('applied-discount-title');
  var bannerDesc = document.getElementById('applied-discount-desc');
  var confirmBtn = document.getElementById('btn-confirm-settle');

  if (origEl) origEl.textContent = '₹' + currentOriginalBill.toFixed(2);

  var netPayable = Math.max(0, currentOriginalBill - currentDiscount.amount);

  if (discRow && banner) {
    if (currentDiscount.amount > 0) {
      discRow.style.setProperty('display', 'flex', 'important');
      if (currentDiscount.type === 'loyalty') {
        discLabel.textContent = 'Points Redeemed (' + currentDiscount.redeemedPoints + ' pts):';
        bannerTitle.textContent = '🎁 Loyalty Points Applied';
      } else if (currentDiscount.type === 'coupon') {
        discLabel.textContent = 'Coupon (' + currentDiscount.couponCode + '):';
        bannerTitle.textContent = '🏷️ Coupon Applied';
      } else {
        discLabel.textContent = 'Discount (' + currentDiscount.reason + '):';
        bannerTitle.textContent = '✂️ Discount Applied';
      }
      discAmtEl.textContent = '-₹' + currentDiscount.amount.toFixed(2);

      banner.style.setProperty('display', 'flex', 'important');
      bannerDesc.textContent = ' (' + currentDiscount.label + ' • -₹' + currentDiscount.amount.toFixed(2) + ')';
    } else {
      discRow.style.setProperty('display', 'none', 'important');
      banner.style.setProperty('display', 'none', 'important');
    }
  }

  if (netDisplay) netDisplay.textContent = '₹' + netPayable.toFixed(2);
  if (confirmBtn) confirmBtn.textContent = '✔ Confirm Settlement • ₹' + netPayable.toFixed(2);
}

function openOrderSettleModal(order) {
  document.getElementById('settle-order-id').value = order.id;
  document.getElementById('settle-order-subtitle').textContent = 'Order #' + order.order_number + ' • ' + (order.customer_name || 'Walk-in Guest') + (order.table_number ? ' (Table ' + order.table_number + ')' : '');
  document.getElementById('settle-order-ref').textContent = 'Order #' + order.order_number + ' • ' + (order.customer_name || 'Walk-in Guest');

  currentOriginalBill = parseFloat(order.final_total || 0);
  activeCustomer = null;

  removeDiscount();
  switchDiscountTab('coupon');

  var phone = (order.customer_phone || '').trim();
  var phoneIn = document.getElementById('modal-cust-phone');
  if (phoneIn) phoneIn.value = phone;

  if (phone) {
    performCustomerLookup(phone, function() {
      // Prepared customer for points tab
    });
  }

  renderDiscountState();

  const m = document.getElementById('orderSettleModal');
  if (m) m.style.display = 'flex';
}

function closeOrderSettleModal() {
  const m = document.getElementById('orderSettleModal');
  if (m) m.style.display = 'none';
}

function submitOrderSettle(e) {
  e.preventDefault();
  const orderId = document.getElementById('settle-order-id').value;
  if (!orderId) return;

  const btn = document.getElementById('btn-confirm-settle');
  btn.disabled = true;
  btn.textContent = 'Processing...';

  const pmRadio = document.querySelector('input[name="order_payment_method"]:checked');
  const pmId = pmRadio ? pmRadio.value : 1;

  var payload = {
    status: 'completed',
    payment_method_id: pmId,
    discount_amount: currentDiscount.amount,
    coupon_id: currentDiscount.couponId,
    coupon_code: currentDiscount.couponCode,
    discount_reason: currentDiscount.reason,
    redeemed_points: currentDiscount.redeemedPoints
  };

  window.rmsPost('<?= site_url('admin/orders/status') ?>/' + orderId, payload)
  .then(function(res) {
    if (res.success) {
      alert('Payment settled successfully! Order marked as Completed.');
      location.reload();
    } else {
      alert(res.message || 'Error settling payment.');
      btn.disabled = false;
      renderDiscountState();
    }
  }).catch(function(err) {
    alert('Request failed while settling payment.');
    btn.disabled = false;
    renderDiscountState();
  });
}

window.addEventListener('click', function(e) {
  const m = document.getElementById('orderSettleModal');
  if (e.target === m) closeOrderSettleModal();
});

// Immediate sync of sidebar badge on orders pipeline load
(function() {
  var activeCnt = <?= (int)(($counts['confirmed'] ?? 0) + ($counts['preparing'] ?? 0) + ($counts['ready'] ?? 0)) ?>;
  var badge = document.getElementById('live-orders-count');
  if (badge) {
    badge.textContent = activeCnt;
    badge.style.display = activeCnt > 0 ? 'inline-block' : 'none';
  }
})();
</script>
