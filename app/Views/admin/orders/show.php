<div class="page-header d-flex justify-between align-center mb-3">
  <div>
    <h1 class="page-title">Order #<?= esc($order['order_number']) ?></h1>
    <p class="page-subtitle">Placed on <?= esc(date('M d, Y H:i:s', strtotime($order['created_at']))) ?> &bull; Type: <?= esc(strtoupper(str_replace('_', ' ', $order['order_type']))) ?></p>
  </div>
  <div class="page-actions d-flex gap-2">
    <a href="<?= site_url('admin/orders') ?>" class="btn btn-secondary">&larr; Back to Orders</a>
    <a href="<?= site_url('admin/orders/receipt/' . $order['id']) ?>" target="_blank" class="btn btn-primary d-flex align-center gap-1">
      <span>🖨️</span> Print Receipt
    </a>
  </div>
</div>

<div class="order-show-grid">
  <!-- Left: Order Items Table & Invoice Summary -->
  <div class="card" style="height: fit-content;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">Ordered Items (<?= count($order['items']) ?>)</div>
      <?php
        $statusClass = 'status-pending';
        if ($order['status'] === 'completed') $statusClass = 'status-active';
        elseif ($order['status'] === 'preparing') $statusClass = 'status-warning';
        elseif ($order['status'] === 'ready') $statusClass = 'status-info';
        elseif ($order['status'] === 'cancelled') $statusClass = 'status-danger';
      ?>
      <span class="status-badge <?= $statusClass ?>"><?= esc(ucfirst($order['status'])) ?></span>
    </div>
    
    <div class="card-body" style="padding: 0;">
      <div class="table-wrapper">
        <table class="rms-table">
          <thead>
            <tr>
              <th>Item</th>
              <th style="text-align: center; width: 60px;">Qty</th>
              <th style="text-align: right; width: 100px;">Rate</th>
              <th style="text-align: right; width: 110px;">Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($order['items'] as $item): ?>
              <tr>
                <td>
                  <div class="td-bold" style="font-size: 0.95rem;"><?= esc($item['item_name']) ?></div>
                  <?php if (!empty($item['special_notes'])): ?>
                    <div class="text-xs text-warning mt-1">Note: <?= esc($item['special_notes']) ?></div>
                  <?php endif; ?>
                </td>
                <td style="text-align: center; font-weight: 600;"><?= (float)$item['quantity'] ?></td>
                <td style="text-align: right;">₹<?= esc(number_format((float)$item['unit_price'], 2)) ?></td>
                <td style="text-align: right;" class="td-bold">₹<?= esc(number_format((float)$item['total'], 2)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Structured Clean Bill Calculation Summary -->
    <div class="bill-summary-container">
      <div class="bill-summary-box">
        <div class="bill-summary-row">
          <span class="text-muted">Subtotal:</span>
          <span class="bill-val">₹<?= esc(number_format((float)$order['subtotal'], 2)) ?></span>
        </div>
        
        <div class="bill-summary-row">
          <span class="text-muted">Taxes (GST 5%):</span>
          <span class="bill-val">₹<?= esc(number_format((float)$order['tax_amount'], 2)) ?></span>
        </div>
        
        <div class="bill-summary-row">
          <span class="text-muted">Service Charge:</span>
          <span class="bill-val">₹<?= esc(number_format((float)$order['service_charge'], 2)) ?></span>
        </div>

        <?php if ((float)($order['discount_amount'] ?? 0) > 0): ?>
          <div class="bill-summary-row text-success">
            <span>Discount <?= !empty($order['discount_reason']) ? '(' . esc($order['discount_reason']) . ')' : '' ?>:</span>
            <span class="bill-val">-₹<?= esc(number_format((float)$order['discount_amount'], 2)) ?></span>
          </div>
        <?php endif; ?>

        <div class="bill-summary-row bill-grand-total">
          <span>Grand Total:</span>
          <span class="bill-grand-val">₹<?= esc(number_format((float)$order['final_total'], 2)) ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Guest Info & Workflow Progression Actions -->
  <div class="order-sidebar-col">
    <!-- Order Information Card -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">Order Information</div>
      </div>
      <div class="card-body" style="padding: 1rem 1.25rem;">
        <table class="order-info-table">
          <tbody>
            <tr>
              <td class="info-label">Table</td>
              <td class="info-val">
                <?php if (!empty($order['table_number'])): ?>
                  <strong><?= esc($order['table_number']) ?></strong>
                  <span class="text-xs text-muted">(<?= esc($order['floor_name'] ?? 'Floor') ?>)</span>
                <?php else: ?>
                  <span class="text-muted">Counter / Direct</span>
                <?php endif; ?>
              </td>
            </tr>
            <tr>
              <td class="info-label">Guest Name</td>
              <td class="info-val"><strong><?= esc($order['customer_name'] ?: 'Walk-in Guest') ?></strong></td>
            </tr>
            <tr>
              <td class="info-label">Contact Phone</td>
              <td class="info-val"><?= esc($order['customer_phone'] ?: 'None') ?></td>
            </tr>
            <tr>
              <td class="info-label">Server / Waiter</td>
              <td class="info-val"><?= esc($order['waiter_name'] ?: 'Staff') ?></td>
            </tr>
            <tr>
              <td class="info-label">Payment Status</td>
              <td class="info-val">
                <span class="status-badge <?= $order['payment_status'] === 'paid' ? 'status-active' : 'status-danger' ?>">
                  <?= esc(ucfirst($order['payment_status'])) ?>
                </span>
                <?php if (!empty($order['payment_method_name'])): ?>
                  <span class="text-xs text-muted" style="margin-left: 4px;">(<?= esc(ucfirst($order['payment_method_name'])) ?>)</span>
                <?php endif; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Order Progression Actions Card -->
    <div class="card">
      <div class="card-header d-flex justify-between align-center">
        <div class="card-title">Order Progression Actions</div>
        <?php if ($order['payment_status'] !== 'paid'): ?>
          <button type="button" class="btn btn-sm btn-success d-flex align-center gap-1" onclick="openOrderShowSettleModal()">
            <span>💳</span> Settle Payment
          </button>
        <?php endif; ?>
      </div>
      <div class="card-body" style="padding: 1.25rem;">
        <div class="progression-actions-grid">
          <?php
            $stages = [
              'confirmed' => ['label' => 'Confirm', 'icon' => '✔'],
              'preparing' => ['label' => 'In Kitchen', 'icon' => '🍳'],
              'ready'     => ['label' => 'Ready to Serve', 'icon' => '🔔'],
              'served'    => ['label' => 'Served', 'icon' => '🍽️'],
              'completed' => ['label' => 'Complete & Settle', 'icon' => '✅'],
              'cancelled' => ['label' => 'Cancel Order', 'icon' => '✖'],
            ];
          ?>
          <?php foreach ($stages as $st => $info): ?>
            <form action="<?= site_url('admin/orders/status/' . $order['id']) ?>" method="POST" style="margin: 0;">
              <?= csrf_field() ?>
              <input type="hidden" name="status" value="<?= esc($st) ?>">
              <button type="submit" 
                      class="btn btn-sm progression-btn <?= $order['status'] === $st ? 'btn-primary current-status' : 'btn-secondary' ?>" 
                      <?= $order['status'] === $st ? 'disabled' : '' ?>
                      title="Set order status to <?= esc($info['label']) ?>">
                <span><?= $info['icon'] ?></span> <?= esc($info['label']) ?>
              </button>
            </form>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: SETTLE ORDER PAYMENT                -->
<!-- ========================================== -->
<div class="modal-backdrop" id="showOrderSettleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div class="modal-card" style="background: var(--surface, #1e293b); border: 1px solid var(--border, rgba(255,255,255,0.1)); border-radius: 12px; width: 100%; max-width: 540px; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); max-height: 90vh; overflow-y: auto;">
    <div class="d-flex justify-between align-center mb-3 pb-2" style="border-bottom: 1px solid var(--border, rgba(255,255,255,0.08));">
      <div>
        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-primary, #f8fafc);">💳 Settle Order Payment</h3>
        <p class="text-xs text-muted" style="margin: 0.2rem 0 0 0;">Order #<?= esc($order['order_number']) ?> &bull; <?= esc($order['customer_name'] ?: 'Guest') ?></p>
      </div>
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeOrderShowSettleModal()" style="font-size: 1.4rem; line-height: 1;">&times;</button>
    </div>

    <!-- LIVE PRICE BREAKDOWN CARD -->
    <div class="settle-breakdown-card p-3 mb-3">
      <div class="d-flex justify-between text-muted text-small mb-1">
        <span>Bill Subtotal &amp; Taxes:</span>
        <span class="fw-600" id="show-settle-original-amt">₹<?= esc(number_format((float)$order['final_total'], 2)) ?></span>
      </div>
      <div class="d-flex justify-between text-success text-small mb-1" id="show-settle-discount-row" style="display: none;">
        <span id="show-settle-discount-label">Discount Applied:</span>
        <span class="fw-700" id="show-settle-discount-amt">-₹0.00</span>
      </div>
      <div class="d-flex justify-between align-center pt-2 settle-net-row">
        <div>
          <div class="text-xs text-muted" style="text-transform: uppercase; letter-spacing: 0.5px;">Net Payable Amount</div>
          <div class="text-xs text-muted fw-600">Order #<?= esc($order['order_number']) ?> &bull; <?= esc($order['customer_name'] ?: 'Guest') ?></div>
        </div>
        <div class="text-success settle-net-val" id="show-settle-bill-amount">₹<?= esc(number_format((float)$order['final_total'], 2)) ?></div>
      </div>
    </div>

    <!-- DISCOUNT & COUPON SECTION -->
    <div class="discount-box mb-3">
      <div class="d-flex justify-between align-center mb-2">
        <div class="fw-700 text-small d-flex align-center gap-1">
          <span>🎟️</span> <span>Discounts &amp; Offers</span>
        </div>
        <div class="discount-nav-tabs">
          <button type="button" class="discount-tab-btn active" id="show-tab-btn-coupon" onclick="showSwitchDiscountTab('coupon')">🏷️ Coupon Code</button>
          <button type="button" class="discount-tab-btn" id="show-tab-btn-manual" onclick="showSwitchDiscountTab('manual')">✂️ Custom Discount</button>
          <button type="button" class="discount-tab-btn" id="show-tab-btn-loyalty" onclick="showSwitchDiscountTab('loyalty')">🎁 Redeem Points</button>
        </div>
      </div>

      <!-- PANE 1: PROMOTIONAL COUPON -->
      <div id="show-pane-coupon" class="discount-pane">
        <div class="d-flex gap-2">
          <div style="flex: 1;">
            <input type="text" id="show-coupon-code-input" class="form-control pos-input-sm" placeholder="ENTER COUPON CODE (E.G. WELCOME20)" style="text-transform: uppercase; font-family: monospace; font-weight: 700; letter-spacing: 1px; width: 100%;">
          </div>
          <button type="button" class="btn btn-primary btn-sm" id="show-btn-apply-coupon" onclick="showApplyCouponCode()">Apply Code</button>
        </div>

        <!-- Active Available Coupons Quick Select -->
        <?php if (!empty($coupons)): ?>
          <div class="mt-2">
            <div class="text-xs text-muted mb-1">Available Offers (Click to Apply):</div>
            <div class="coupon-chips-list">
              <?php foreach ($coupons as $ac): ?>
                <div class="coupon-chip" onclick="showSelectCouponChip('<?= esc($ac['code']) ?>')">
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
      <div id="show-pane-manual" class="discount-pane" style="display: none;">
        <div class="d-flex gap-2 align-center mb-2">
          <div class="d-flex gap-1">
            <button type="button" class="btn btn-xs btn-primary" id="show-btn-disc-type-pct" onclick="showSetManualDiscType('percentage')">% Percent</button>
            <button type="button" class="btn btn-xs btn-secondary" id="show-btn-disc-type-fixed" onclick="showSetManualDiscType('fixed')">₹ Fixed</button>
          </div>
          <div style="flex: 1;">
            <input type="number" step="0.01" min="0" id="show-manual-disc-val" class="form-control pos-input-sm" placeholder="Percent % (e.g. 10)" style="width: 100%;">
          </div>
          <button type="button" class="btn btn-primary btn-sm" onclick="showApplyManualDiscount()">Apply</button>
        </div>
        <div class="d-flex gap-2 align-center">
          <label class="text-xs text-muted" style="white-space: nowrap;">Reason:</label>
          <select id="show-manual-disc-reason" class="form-control pos-input-sm" style="font-size: .8rem; width: 100%;">
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
      <div id="show-pane-loyalty" class="discount-pane" style="display: none;">
        <!-- State A: Phone lookup -->
        <div id="show-loyalty-no-customer" style="display: none;">
          <div class="text-xs text-muted mb-2">
            Enter customer's registered phone number to check their reward points balance:
          </div>
          <div class="d-flex gap-2">
            <input type="tel" id="show-modal-cust-phone" class="form-control pos-input-sm" placeholder="Enter phone (e.g. 9876543210)" style="flex: 1;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="showLookupCustomerFromModal()">Check Points</button>
          </div>
        </div>

        <!-- State B: Customer info with points -->
        <div id="show-loyalty-customer-info" style="display: none;">
          <div class="p-2 mb-2" style="background: rgba(99, 102, 241, 0.08); border-radius: 6px; border: 1px solid rgba(99, 102, 241, 0.25);">
            <div class="d-flex justify-between align-center">
              <div>
                <div class="fw-700" id="show-loyalty-cust-name" style="color: var(--text-primary); font-size: 0.85rem;">Customer Name</div>
                <div class="text-xs text-muted" id="show-loyalty-cust-phone">+91 0000000000</div>
              </div>
              <div class="text-right">
                <div class="badge" id="show-loyalty-balance-badge" style="font-size: 0.8rem; font-weight: 700; background: #f59e0b; color: #000; padding: 3px 8px; border-radius: 4px;">
                  ⭐ <span id="show-loyalty-avail-points">0</span> Points
                </div>
                <div class="text-xs text-muted mt-1" id="show-loyalty-worth-text">Worth ₹0.00 (1 pt = ₹1)</div>
              </div>
            </div>
          </div>

          <div id="show-loyalty-redeem-controls">
            <div class="d-flex gap-2 align-center mb-1">
              <div style="flex: 1;">
                <label class="text-xs text-muted mb-1 d-block">Points to Redeem (Max: <strong id="show-loyalty-max-allowed" class="text-primary">0</strong>):</label>
                <div class="d-flex gap-1">
                  <input type="number" step="1" min="1" id="show-loyalty-redeem-input" class="form-control pos-input-sm" placeholder="Points (e.g. 50)" style="flex: 1;">
                  <button type="button" class="btn btn-secondary btn-xs" onclick="showSetLoyaltyRedeemMax()">Max</button>
                </div>
              </div>
              <div style="align-self: flex-end;">
                <button type="button" class="btn btn-success btn-sm" id="show-btn-apply-loyalty" onclick="showApplyLoyaltyPointsDiscount()">
                  Apply Points
                </button>
              </div>
            </div>
            <div class="text-xs text-muted mt-1">
              💡 1 point equals ₹1 instant bill discount.
            </div>
          </div>

          <div id="show-loyalty-zero-points-msg" class="text-xs text-muted p-2" style="display: none; background: rgba(255,255,255,0.03); border-radius: 4px; border: 1px dashed var(--border);">
            ⭐ This customer currently has 0 points. They will earn 1 pt per ₹10 on this order upon payment.
          </div>
        </div>

        <!-- State C: New customer message -->
        <div id="show-loyalty-new-customer-msg" class="text-xs text-muted p-2" style="display: none; background: rgba(59, 130, 246, 0.08); border-radius: 4px; border: 1px dashed rgba(59, 130, 246, 0.3);">
          ✨ New Customer • No existing reward points yet. This customer will earn reward points (1 pt per ₹10) on this order!
        </div>
      </div>

      <!-- APPLIED DISCOUNT STATUS BANNER -->
      <div id="show-applied-discount-banner" class="discount-applied-banner" style="display: none;">
        <div class="d-flex align-center gap-1">
          <span id="show-applied-discount-title">✅ Discount Applied</span>
          <span class="text-xs" id="show-applied-discount-desc"></span>
        </div>
        <button type="button" class="btn btn-xs btn-danger" onclick="showRemoveDiscount()">Remove &times;</button>
      </div>
      <div id="show-coupon-error-msg" class="text-xs text-danger mt-1" style="display: none;"></div>
    </div>

    <form method="POST" action="<?= site_url('admin/orders/status/' . $order['id']) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="status" value="completed">
      <input type="hidden" name="discount_amount" id="show-form-discount-amount" value="0">
      <input type="hidden" name="coupon_id" id="show-form-coupon-id" value="">
      <input type="hidden" name="coupon_code" id="show-form-coupon-code" value="">
      <input type="hidden" name="discount_reason" id="show-form-discount-reason" value="">
      <input type="hidden" name="redeemed_points" id="show-form-redeemed-points" value="0">

      <!-- Payment Tender Selection -->
      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem;">Select Payment Tender</label>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.5rem;">
          <?php foreach ($paymentMethods ?? [] as $idx => $pm): ?>
            <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 0.75rem; border: 1px solid var(--border, rgba(255,255,255,0.1)); border-radius: 8px; cursor: pointer; background: rgba(255,255,255,0.02); transition: all 0.15s;">
              <input type="radio" name="payment_method_id" value="<?= (int)$pm['id'] ?>" <?= $idx === 0 ? 'checked' : '' ?> style="accent-color: var(--accent, #6366f1);">
              <span style="font-weight: 500; font-size: 0.9rem;">
                <?= esc($pm['name'] === 'cash' ? '💵 Cash' : ($pm['name'] === 'card' ? '💳 Card' : ($pm['name'] === 'upi' ? '📱 UPI' : esc(ucfirst($pm['name']))))) ?>
              </span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="d-flex justify-end gap-2 pt-2" style="border-top: 1px solid var(--border, rgba(255,255,255,0.08));">
        <button type="button" class="btn btn-secondary" onclick="closeOrderShowSettleModal()">Cancel</button>
        <button type="submit" class="btn btn-success" id="show-btn-confirm-settle" style="font-weight: 600;">
          ✔ Confirm &amp; Complete Bill
        </button>
      </div>
    </form>
  </div>
</div>

<style>
.order-show-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.45fr) minmax(360px, 1fr);
  gap: 1.5rem;
  align-items: start;
}

.order-sidebar-col {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.bill-summary-container {
  padding: 1.25rem 1.5rem;
  background: rgba(0, 0, 0, 0.15);
  border-top: 1px solid var(--border);
  border-radius: 0 0 var(--radius) var(--radius);
}

.bill-summary-box {
  max-width: 340px;
  margin-left: auto;
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
}

.bill-summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
}

.bill-val {
  font-weight: 600;
  color: var(--text-primary);
}

.bill-grand-total {
  padding-top: 0.75rem;
  margin-top: 0.25rem;
  border-top: 1px dashed var(--border);
  font-size: 1.2rem;
  font-weight: 800;
}

.bill-grand-val {
  color: var(--accent, #6366f1);
  font-size: 1.3rem;
}

.order-info-table {
  width: 100%;
  border-collapse: collapse;
}

.order-info-table td {
  padding: 0.65rem 0.25rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.order-info-table tr:last-child td {
  border-bottom: none;
}

.info-label {
  color: var(--text-muted);
  width: 40%;
  font-size: 0.88rem;
}

.info-val {
  color: var(--text-primary);
  font-size: 0.92rem;
}

.progression-actions-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.65rem;
}

.progression-btn {
  width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.55rem 0.6rem;
  font-size: 0.85rem;
  font-weight: 600;
  border-radius: 8px;
}

.progression-btn.current-status {
  opacity: 0.95;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.4);
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

@media (max-width: 960px) {
  .order-show-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<script>
var showOriginalBill = <?= (float)$order['final_total'] ?>;
var showCustomerPhone = <?= json_encode((string)($order['customer_phone'] ?? '')) ?>;
var showActiveCustomer = null;
var showManualDiscType = 'percentage';
var showCurrentDiscount = {
  type: null,
  amount: 0.0,
  couponId: null,
  couponCode: '',
  redeemedPoints: 0,
  reason: '',
  label: ''
};

function showSwitchDiscountTab(tab) {
  var btnCp = document.getElementById('show-tab-btn-coupon');
  var btnMan = document.getElementById('show-tab-btn-manual');
  var btnLoyalty = document.getElementById('show-tab-btn-loyalty');
  var paneCp = document.getElementById('show-pane-coupon');
  var paneMan = document.getElementById('show-pane-manual');
  var paneLoyalty = document.getElementById('show-pane-loyalty');

  if (btnCp) btnCp.classList.toggle('active', tab === 'coupon');
  if (btnMan) btnMan.classList.toggle('active', tab === 'manual');
  if (btnLoyalty) btnLoyalty.classList.toggle('active', tab === 'loyalty');

  if (paneCp) paneCp.style.display = (tab === 'coupon') ? 'block' : 'none';
  if (paneMan) paneMan.style.display = (tab === 'manual') ? 'block' : 'none';
  if (paneLoyalty) {
    paneLoyalty.style.display = (tab === 'loyalty') ? 'block' : 'none';
    if (tab === 'loyalty') {
      showPrepareLoyaltyPane();
    }
  }
}

function showSetManualDiscType(type) {
  showManualDiscType = type;
  var btnPct = document.getElementById('show-btn-disc-type-pct');
  var btnFix = document.getElementById('show-btn-disc-type-fixed');
  var inputEl = document.getElementById('show-manual-disc-val');
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

function showSelectCouponChip(code) {
  var codeIn = document.getElementById('show-coupon-code-input');
  if (codeIn) codeIn.value = code;
  showApplyCouponCode();
}

function showApplyCouponCode() {
  var code = (document.getElementById('show-coupon-code-input')?.value || '').trim();
  var errEl = document.getElementById('show-coupon-error-msg');
  if (errEl) { errEl.style.display = 'none'; errEl.textContent = ''; }

  if (!code) {
    if (errEl) {
      errEl.textContent = 'Please enter or select a coupon code.';
      errEl.style.display = 'block';
    }
    return;
  }

  var btn = document.getElementById('show-btn-apply-coupon');
  if (btn) {
    btn.disabled = true;
    btn.textContent = 'Checking...';
  }

  var custPhone = showActiveCustomer?.phone || showCustomerPhone || '';

  window.rmsPost('<?= site_url('admin/pos/validate-coupon') ?>', {
    code: code,
    order_amount: showOriginalBill,
    customer_phone: custPhone
  }).then(function(res) {
    if (btn) {
      btn.disabled = false;
      btn.textContent = 'Apply Code';
    }

    if (res.success && res.data) {
      showCurrentDiscount = {
        type: 'coupon',
        amount: parseFloat(res.data.discount_amount || 0),
        couponId: res.data.coupon_id,
        couponCode: res.data.code,
        redeemedPoints: 0,
        reason: 'Coupon: ' + res.data.code,
        label: res.data.code + ' (' + res.data.name + ')'
      };
      showRenderDiscountState();
    } else {
      if (errEl) {
        errEl.textContent = res.message || 'Invalid or expired coupon code.';
        errEl.style.display = 'block';
      }
    }
  }).catch(function() {
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

function showApplyManualDiscount() {
  var val = parseFloat(document.getElementById('show-manual-disc-val')?.value || 0);
  var reason = document.getElementById('show-manual-disc-reason')?.value || 'Manual Discount';
  var errEl = document.getElementById('show-coupon-error-msg');
  if (errEl) errEl.style.display = 'none';

  if (isNaN(val) || val <= 0) {
    if (errEl) {
      errEl.textContent = 'Please enter a valid discount value greater than 0.';
      errEl.style.display = 'block';
    }
    return;
  }

  var discAmt = 0;
  if (showManualDiscType === 'percentage') {
    if (val > 100) {
      if (errEl) {
        errEl.textContent = 'Percentage discount cannot exceed 100%.';
        errEl.style.display = 'block';
      }
      return;
    }
    discAmt = (showOriginalBill * val) / 100;
  } else {
    discAmt = Math.min(val, showOriginalBill);
  }

  showCurrentDiscount = {
    type: 'manual',
    amount: discAmt,
    couponId: null,
    couponCode: '',
    redeemedPoints: 0,
    reason: reason,
    label: (showManualDiscType === 'percentage' ? val + '% ' : '₹' + val.toFixed(2) + ' ') + reason
  };

  showRenderDiscountState();
}

function showPerformCustomerLookup(phone, callback) {
  phone = (phone || '').trim();
  if (!phone || phone.length < 5) {
    showActiveCustomer = null;
    if (typeof callback === 'function') callback(null);
    return;
  }

  window.rmsGet('<?= site_url('admin/pos/lookup-customer') ?>?phone=' + encodeURIComponent(phone))
    .then(function(res) {
      if (res.success && res.data && res.data.exists && res.data.customer) {
        showActiveCustomer = res.data.customer;
      } else {
        showActiveCustomer = { exists: false, phone: phone };
      }
      if (typeof callback === 'function') callback(showActiveCustomer);
    }).catch(function() {
      if (typeof callback === 'function') callback(null);
    });
}

function showLookupCustomerFromModal() {
  var modalPhone = (document.getElementById('show-modal-cust-phone')?.value || '').trim();
  if (!modalPhone) {
    alert('Please enter a customer phone number.');
    return;
  }
  showPerformCustomerLookup(modalPhone, function() {
    showPrepareLoyaltyPane();
  });
}

function showPrepareLoyaltyPane() {
  var noCustBox   = document.getElementById('show-loyalty-no-customer');
  var infoBox     = document.getElementById('show-loyalty-customer-info');
  var newCustBox  = document.getElementById('show-loyalty-new-customer-msg');
  var zeroMsg     = document.getElementById('show-loyalty-zero-points-msg');
  var redeemCtrls = document.getElementById('show-loyalty-redeem-controls');

  if (showActiveCustomer && showActiveCustomer.id) {
    if (noCustBox) noCustBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'none';
    if (infoBox) infoBox.style.display = 'block';

    var nameEl = document.getElementById('show-loyalty-cust-name');
    if (nameEl) nameEl.textContent = showActiveCustomer.name;
    var phoneEl = document.getElementById('show-loyalty-cust-phone');
    if (phoneEl) phoneEl.textContent = showActiveCustomer.phone;
    var availEl = document.getElementById('show-loyalty-avail-points');
    if (availEl) availEl.textContent = showActiveCustomer.loyalty_points;
    var worthEl = document.getElementById('show-loyalty-worth-text');
    if (worthEl) worthEl.textContent = 'Worth ₹' + (showActiveCustomer.points_value || showActiveCustomer.loyalty_points).toFixed(2) + ' (1 pt = ₹1)';

    var maxPts = Math.min(showActiveCustomer.loyalty_points, Math.floor(showOriginalBill));
    var maxEl = document.getElementById('show-loyalty-max-allowed');
    if (maxEl) maxEl.textContent = maxPts;

    if (showActiveCustomer.loyalty_points > 0 && maxPts > 0) {
      if (redeemCtrls) redeemCtrls.style.display = 'block';
      if (zeroMsg) zeroMsg.style.display = 'none';
      var inputEl = document.getElementById('show-loyalty-redeem-input');
      if (inputEl && !inputEl.value) {
        inputEl.value = maxPts;
      }
    } else {
      if (redeemCtrls) redeemCtrls.style.display = 'none';
      if (zeroMsg) zeroMsg.style.display = 'block';
    }
  } else if (showActiveCustomer && showActiveCustomer.exists === false) {
    if (noCustBox) noCustBox.style.display = 'none';
    if (infoBox) infoBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'block';
  } else {
    if (infoBox) infoBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'none';
    if (noCustBox) noCustBox.style.display = 'block';
  }
}

function showSetLoyaltyRedeemMax() {
  var maxPts = parseInt(document.getElementById('show-loyalty-max-allowed')?.textContent || '0', 10);
  var inputEl = document.getElementById('show-loyalty-redeem-input');
  if (inputEl) inputEl.value = maxPts;
}

function showApplyLoyaltyPointsDiscount() {
  var errEl = document.getElementById('show-coupon-error-msg');
  if (errEl) errEl.style.display = 'none';

  if (!showActiveCustomer || !showActiveCustomer.id) {
    alert('No registered customer selected.');
    return;
  }

  var inputEl = document.getElementById('show-loyalty-redeem-input');
  var pts = parseInt(inputEl ? inputEl.value : '0', 10);

  if (isNaN(pts) || pts <= 0) {
    if (errEl) {
      errEl.textContent = 'Please enter a valid number of points to redeem (greater than 0).';
      errEl.style.display = 'block';
    }
    return;
  }

  if (pts > showActiveCustomer.loyalty_points) {
    if (errEl) {
      errEl.textContent = 'Cannot redeem more than available balance (' + showActiveCustomer.loyalty_points + ' points).';
      errEl.style.display = 'block';
    }
    return;
  }

  if (pts > showOriginalBill) {
    if (errEl) {
      errEl.textContent = 'Points discount cannot exceed total bill (₹' + showOriginalBill.toFixed(2) + ').';
      errEl.style.display = 'block';
    }
    return;
  }

  var discountAmt = pts * 1.0;

  showCurrentDiscount = {
    type: 'loyalty',
    amount: discountAmt,
    couponId: null,
    couponCode: '',
    redeemedPoints: pts,
    reason: 'Loyalty Points (' + pts + ' pts)',
    label: pts + ' Points (₹' + discountAmt.toFixed(2) + ')'
  };

  showRenderDiscountState();
}

function showRemoveDiscount() {
  showCurrentDiscount = {
    type: null,
    amount: 0.0,
    couponId: null,
    couponCode: '',
    redeemedPoints: 0,
    reason: '',
    label: ''
  };
  var codeIn = document.getElementById('show-coupon-code-input');
  if (codeIn) codeIn.value = '';
  var valIn = document.getElementById('show-manual-disc-val');
  if (valIn) valIn.value = '';
  var loyaltyIn = document.getElementById('show-loyalty-redeem-input');
  if (loyaltyIn) loyaltyIn.value = '';
  var errEl = document.getElementById('show-coupon-error-msg');
  if (errEl) errEl.style.display = 'none';
  showRenderDiscountState();
}

function showRenderDiscountState() {
  var origEl = document.getElementById('show-settle-original-amt');
  var discRow = document.getElementById('show-settle-discount-row');
  var discLabel = document.getElementById('show-settle-discount-label');
  var discAmtEl = document.getElementById('show-settle-discount-amt');
  var netDisplay = document.getElementById('show-settle-bill-amount');
  var banner = document.getElementById('show-applied-discount-banner');
  var bannerTitle = document.getElementById('show-applied-discount-title');
  var bannerDesc = document.getElementById('show-applied-discount-desc');
  var confirmBtn = document.getElementById('show-btn-confirm-settle');

  if (origEl) origEl.textContent = '₹' + showOriginalBill.toFixed(2);

  var netPayable = Math.max(0, showOriginalBill - showCurrentDiscount.amount);

  if (discRow && banner) {
    if (showCurrentDiscount.amount > 0) {
      discRow.style.setProperty('display', 'flex', 'important');
      if (showCurrentDiscount.type === 'loyalty') {
        discLabel.textContent = 'Points Redeemed (' + showCurrentDiscount.redeemedPoints + ' pts):';
        bannerTitle.textContent = '🎁 Loyalty Points Applied';
      } else if (showCurrentDiscount.type === 'coupon') {
        discLabel.textContent = 'Coupon (' + showCurrentDiscount.couponCode + '):';
        bannerTitle.textContent = '🏷️ Coupon Applied';
      } else {
        discLabel.textContent = 'Discount (' + showCurrentDiscount.reason + '):';
        bannerTitle.textContent = '✂️ Discount Applied';
      }
      discAmtEl.textContent = '-₹' + showCurrentDiscount.amount.toFixed(2);

      banner.style.setProperty('display', 'flex', 'important');
      bannerDesc.textContent = ' (' + showCurrentDiscount.label + ' • -₹' + showCurrentDiscount.amount.toFixed(2) + ')';
    } else {
      discRow.style.setProperty('display', 'none', 'important');
      banner.style.setProperty('display', 'none', 'important');
    }
  }

  // Update hidden form inputs
  var fDisc = document.getElementById('show-form-discount-amount');
  var fCpId = document.getElementById('show-form-coupon-id');
  var fCpCode = document.getElementById('show-form-coupon-code');
  var fReason = document.getElementById('show-form-discount-reason');
  var fPoints = document.getElementById('show-form-redeemed-points');

  if (fDisc) fDisc.value = showCurrentDiscount.amount;
  if (fCpId) fCpId.value = showCurrentDiscount.couponId || '';
  if (fCpCode) fCpCode.value = showCurrentDiscount.couponCode || '';
  if (fReason) fReason.value = showCurrentDiscount.reason || '';
  if (fPoints) fPoints.value = showCurrentDiscount.redeemedPoints || 0;

  if (netDisplay) netDisplay.textContent = '₹' + netPayable.toFixed(2);
  if (confirmBtn) confirmBtn.textContent = '✔ Confirm Settlement • ₹' + netPayable.toFixed(2);
}

function openOrderShowSettleModal() {
  showRemoveDiscount();
  showSwitchDiscountTab('coupon');

  if (showCustomerPhone) {
    var phoneIn = document.getElementById('show-modal-cust-phone');
    if (phoneIn) phoneIn.value = showCustomerPhone;
    showPerformCustomerLookup(showCustomerPhone, function() {
      // Prepared
    });
  }

  showRenderDiscountState();

  const m = document.getElementById('showOrderSettleModal');
  if (m) m.style.display = 'flex';
}

function closeOrderShowSettleModal() {
  const m = document.getElementById('showOrderSettleModal');
  if (m) m.style.display = 'none';
}

window.addEventListener('click', function(e) {
  const m = document.getElementById('showOrderSettleModal');
  if (e.target === m) closeOrderShowSettleModal();
});
</script>
