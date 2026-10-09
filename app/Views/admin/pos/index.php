<div class="pos-container">
  
  <!-- POS TOP BAR -->
  <div class="pos-topbar">
    <div class="d-flex align-center gap-2">
      <div class="order-type-tabs" id="order-type-tabs">
        <button type="button" class="pos-tab-btn active" data-type="dine_in" onclick="setOrderType('dine_in')">
          🍽️ Dine-In
        </button>
        <button type="button" class="pos-tab-btn" data-type="takeaway" onclick="setOrderType('takeaway')">
          🥡 Takeaway
        </button>
        <button type="button" class="pos-tab-btn" data-type="delivery" onclick="setOrderType('delivery')">
          🛵 Delivery
        </button>
      </div>

      <div id="selected-table-badge" class="selected-table-badge">
        <span class="text-muted">Table:</span> 
        <strong id="current-table-label" class="text-primary">None Selected</strong>
        <button type="button" class="btn btn-ghost btn-xs text-muted" onclick="showTableModal()" title="Change Table">&#9998; Change</button>
      </div>
    </div>

    <div class="d-flex align-center gap-2">
      <div class="pos-search-wrapper">
        <input type="text" id="pos-item-search" class="form-control pos-search-input" placeholder="🔍 Search dishes, codes (e.g. Biryani, APP-01)..." oninput="filterMenuItems()">
      </div>
      <button type="button" class="btn btn-secondary btn-sm" onclick="showTableModal()">
        🗺️ Table Layout
      </button>
    </div>
  </div>

  <!-- MOBILE POS VIEW SWITCHER (Visible on tablets and phones <= 992px) -->
  <div class="pos-mobile-nav" id="pos-mobile-nav">
    <button type="button" class="pos-mobile-tab-btn active" id="btn-pos-menu" onclick="switchPosMobileView('menu')">
      🍽️ Menu Catalog
    </button>
    <button type="button" class="pos-mobile-tab-btn" id="btn-pos-cart" onclick="switchPosMobileView('cart')">
      🛒 Order Cart <span class="mobile-cart-badge" id="mobile-cart-count" style="display:none;">0</span>
    </button>
  </div>

  <!-- MAIN POS LAYOUT -->
  <div class="pos-body">
    
    <!-- LEFT: MENU SECTION -->
    <div class="pos-menu-section">
      <!-- Category Tabs -->
      <div class="pos-category-bar" id="pos-category-bar">
        <button type="button" class="category-chip active" data-cat="all" onclick="filterCategory('all', this)">
          🌟 All Items
        </button>
        <?php foreach ($categories as $cat): ?>
          <button type="button" class="category-chip" data-cat="<?= (int)$cat['id'] ?>" onclick="filterCategory(<?= (int)$cat['id'] ?>, this)">
            <?= esc($cat['icon']) ?> <?= esc($cat['name']) ?> (<?= (int)$cat['item_count'] ?>)
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Menu Items Grid -->
      <div class="pos-items-grid" id="pos-items-grid">
        <?php foreach ($menuItems as $item): ?>
          <div class="pos-item-card" 
               data-id="<?= (int)$item['id'] ?>" 
               data-cat="<?= (int)$item['category_id'] ?>" 
               data-name="<?= esc(strtolower($item['name'])) ?>" 
               data-code="<?= esc(strtolower($item['code'] ?? '')) ?>"
               onclick="addToCartById(<?= (int)$item['id'] ?>)">
            
            <div class="pos-item-top">
              <span class="diet-indicator <?= !empty($item['is_veg']) ? 'veg' : 'non-veg' ?>" title="<?= !empty($item['is_veg']) ? 'Vegetarian' : 'Non-Veg' ?>"></span>
              <?php if (!empty($item['code'])): ?>
                <span class="pos-item-code"><?= esc($item['code']) ?></span>
              <?php endif; ?>
            </div>

            <div class="pos-item-name"><?= esc($item['name']) ?></div>
            <div class="pos-item-desc"><?= esc($item['description'] ?? '') ?></div>

            <div class="pos-item-footer">
              <div class="pos-item-price">₹<?= esc(number_format((float)$item['price'], 2)) ?></div>
              <button type="button" class="pos-add-btn">+ Add</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- RIGHT: ORDER CART & BILLING -->
    <div class="pos-cart-section">
      <div class="pos-cart-header">
        <div>
          <div class="cart-title">Current Order</div>
          <div class="text-xs text-muted" id="cart-order-info">Dine-In &bull; No Table</div>
        </div>
        <button type="button" class="btn btn-ghost btn-sm text-danger" onclick="clearCart()" title="Clear cart">
          Clear
        </button>
      </div>

      <!-- Customer Info & Loyalty Recognition -->
      <div class="pos-customer-bar">
        <div class="pos-customer-inputs">
          <input type="text" id="cust-name" class="form-control pos-input-sm" placeholder="Guest Name (Optional)">
          <div style="position: relative; display: flex; align-items: center; width: 100%;">
            <input type="tel" id="cust-phone" class="form-control pos-input-sm" placeholder="Phone No." autocomplete="off" style="width: 100%; padding-right: 24px;">
            <span id="cust-lookup-spinner" style="position: absolute; right: 6px; font-size: 11px; display: none;" title="Checking customer...">⏳</span>
          </div>
        </div>
        <!-- Live Customer Loyalty Recognition Badge -->
        <div id="customer-loyalty-status" class="customer-loyalty-card" style="display: none;"></div>
      </div>

      <!-- Cart Line Items -->
      <div class="pos-cart-items" id="pos-cart-items">
        <div class="cart-empty-state" id="cart-empty-state">
          <div style="font-size: 2.5rem; margin-bottom: .5rem;">🛒</div>
          <div class="text-secondary fw-600">Your cart is empty</div>
          <div class="text-xs text-muted mt-1">Click on menu items to begin building the order</div>
        </div>
      </div>

      <!-- Cart Pricing Summary -->
      <div class="pos-cart-summary">
        <div class="summary-line">
          <span>Subtotal</span>
          <span id="sum-subtotal">₹0.00</span>
        </div>
        <div class="summary-line text-muted">
          <span>Taxes (GST 5%)</span>
          <span id="sum-tax">₹0.00</span>
        </div>
        <div class="summary-line total-line">
          <span>Grand Total</span>
          <span id="sum-total">₹0.00</span>
        </div>

        <!-- POS Action Buttons -->
        <div class="pos-actions-grid mt-2">
          <button type="button" class="btn btn-primary btn-block btn-lg" id="btn-place-order" onclick="submitOrder('kot')" style="font-size: 0.95rem; font-weight: 600; padding: 0.65rem 1rem;">
            🍳 Send to Kitchen (KOT)
          </button>
          <button type="button" class="btn btn-success btn-block btn-lg" id="btn-settle-order" onclick="openSettleModal()" style="font-size: 0.95rem; font-weight: 600; padding: 0.65rem 1rem;">
            💳 Settle &amp; Pay Bill
          </button>
        </div>
      </div>

    </div><!-- /.pos-cart-section -->

  </div><!-- /.pos-body -->
</div><!-- /.pos-container -->

<!-- MODAL: FLOOR & TABLE SELECTION -->
<div class="pos-modal-overlay" id="table-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 850px;">
    <div class="pos-modal-header">
      <div class="modal-title">Select Table for Dine-In</div>
      <button type="button" class="btn-close" onclick="closeTableModal()">&times;</button>
    </div>
    <div class="pos-modal-body">
      <!-- Floor selector tabs -->
      <div class="floor-tabs mb-2">
        <?php foreach ($floors as $idx => $f): ?>
          <button type="button" class="floor-tab-btn <?= $idx === 0 ? 'active' : '' ?>" onclick="switchFloorTab(<?= (int)$f['id'] ?>, this)">
            <?= esc($f['name']) ?>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Floor table views -->
      <?php foreach ($floors as $idx => $f): ?>
        <div class="floor-table-view <?= $idx === 0 ? 'active' : '' ?>" id="floor-view-<?= (int)$f['id'] ?>">
          <div class="table-card-grid">
            <?php if (empty($f['tables'])): ?>
              <p class="text-muted text-small">No tables configured on this floor.</p>
            <?php else: ?>
              <?php foreach ($f['tables'] as $tbl): ?>
                <div class="table-pos-card status-<?= esc($tbl['status']) ?>" 
                     onclick="selectTable(<?= (int)$tbl['id'] ?>, '<?= esc($tbl['table_number']) ?>', '<?= esc($tbl['status']) ?>')">
                  <div class="table-num"><?= esc($tbl['table_number']) ?></div>
                  <div class="table-cap text-xs">👥 <?= (int)$tbl['seating_capacity'] ?> Seats</div>
                  <div class="table-badge-status text-xs"><?= esc(ucfirst($tbl['status'])) ?></div>
                  <?php if (!empty($tbl['final_total'])): ?>
                    <div class="table-bill-amt text-xs fw-bold">₹<?= esc(number_format((float)$tbl['final_total'], 2)) ?></div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="pos-modal-footer">
      <div class="d-flex align-center gap-2">
        <span class="legend-dot status-available"></span> <span class="text-xs">Available</span>
        <span class="legend-dot status-occupied"></span> <span class="text-xs">Occupied</span>
        <span class="legend-dot status-reserved"></span> <span class="text-xs">Reserved</span>
        <span class="legend-dot status-dirty"></span> <span class="text-xs">Dirty</span>
      </div>
      <button type="button" class="btn btn-secondary" onclick="closeTableModal()">Close</button>
    </div>
  </div>
</div>

<!-- MODAL: SETTLE / BILL PAYMENT WITH DISCOUNT & COUPONS -->
<div class="pos-modal-overlay" id="settle-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 540px;">
    <div class="pos-modal-header">
      <div class="modal-title d-flex align-center gap-2">
        <span>💳</span> <span>Settle &amp; Close Bill</span>
      </div>
      <button type="button" class="btn-close" onclick="closeSettleModal()">&times;</button>
    </div>
    <div class="pos-modal-body">
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
            <div class="text-xs text-muted">Net Payable Amount</div>
            <div class="text-xs text-muted fw-600" id="settle-order-ref"></div>
          </div>
          <div class="text-success settle-net-val" id="settle-amount-display">₹0.00</div>
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
              <input type="text" id="coupon-code-input" class="form-control pos-input-sm" placeholder="Enter coupon code (e.g. WELCOME20)" style="text-transform: uppercase; font-family: monospace; font-weight: 700; letter-spacing: 1px;">
            </div>
            <button type="button" class="btn btn-primary btn-sm" id="btn-apply-coupon" onclick="applyCouponCode()">Apply Code</button>
          </div>

          <!-- Active Available Coupons Quick Select -->
          <?php if (!empty($activeCoupons)): ?>
            <div class="mt-2">
              <div class="text-xs text-muted mb-1">Available Offers (Click to Apply):</div>
              <div class="coupon-chips-list">
                <?php foreach ($activeCoupons as $ac): ?>
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
              <input type="number" step="0.01" min="0" id="manual-disc-val" class="form-control pos-input-sm" placeholder="Value (e.g. 10)">
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="applyManualDiscount()">Apply</button>
          </div>
          <div class="d-flex gap-2 align-center">
            <label class="text-xs text-muted" style="white-space: nowrap;">Reason:</label>
            <select id="manual-disc-reason" class="form-control pos-input-sm" style="font-size: .8rem;">
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
          <!-- State A: No phone number entered yet -->
          <div id="loyalty-no-customer" style="display: none;">
            <div class="text-xs text-muted mb-2">
              Enter customer's registered phone number to check their available reward balance:
            </div>
            <div class="d-flex gap-2">
              <input type="tel" id="modal-cust-phone" class="form-control pos-input-sm" placeholder="Enter phone number (e.g. 9876543210)">
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
                    <input type="number" step="1" min="1" id="loyalty-redeem-input" class="form-control pos-input-sm" placeholder="Points (e.g. 50)">
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
            ✨ New Customer • No existing reward points yet. This customer will be registered and will earn reward points on this order!
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

      <!-- PAYMENT METHOD TENDER -->
      <div class="form-group mb-0">
        <label class="form-label mb-1">Payment Tender Method</label>
        <div class="payment-methods-grid">
          <?php foreach ($paymentMethods as $idx => $pm): ?>
            <label class="pm-radio-card <?= $idx === 0 ? 'active' : '' ?>">
              <input type="radio" name="payment_method_id" value="<?= (int)$pm['id'] ?>" <?= $idx === 0 ? 'checked' : '' ?> onchange="selectPaymentMethod(this)">
              <div class="pm-title"><?= esc($pm['name']) ?></div>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="pos-modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeSettleModal()">Cancel</button>
      <button type="button" class="btn btn-success btn-lg" id="btn-confirm-settle" onclick="executeSettle()">
        Confirm Settlement
      </button>
    </div>
  </div>
</div>

<!-- POS STYLES -->
<style>
.pos-container {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 90px);
  gap: .75rem;
}
.pos-topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--surface-raised);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: .6rem 1rem;
  gap: 1rem;
}
.order-type-tabs {
  display: flex;
  background: var(--surface);
  border-radius: var(--radius-sm);
  padding: 3px;
  gap: 4px;
}
.pos-tab-btn {
  background: transparent;
  border: none;
  color: var(--text-secondary);
  padding: .35rem .75rem;
  border-radius: 4px;
  font-size: .8125rem;
  font-weight: 500;
  cursor: pointer;
  transition: all .15s;
}
.pos-tab-btn.active {
  background: var(--primary);
  color: #fff;
  font-weight: 600;
}
.selected-table-badge {
  display: flex;
  align-items: center;
  gap: .4rem;
  background: var(--surface);
  border: 1px solid var(--border);
  padding: .3rem .6rem;
  border-radius: var(--radius-sm);
  font-size: .8125rem;
}
.pos-search-wrapper {
  position: relative;
  min-width: 260px;
}
.pos-search-input {
  width: 100%;
  padding: .4rem .75rem;
  font-size: .8125rem;
}
.pos-body {
  display: flex;
  flex: 1;
  gap: .75rem;
  min-height: 0;
}
.pos-menu-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: var(--surface-raised);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
}
.pos-category-bar {
  display: flex;
  gap: .5rem;
  padding: .75rem;
  border-bottom: 1px solid var(--border);
  overflow-x: auto;
  white-space: nowrap;
  background: var(--surface);
}
.category-chip {
  background: var(--surface-raised);
  border: 1px solid var(--border);
  color: var(--text-secondary);
  padding: .4rem .85rem;
  border-radius: 20px;
  font-size: .8125rem;
  font-weight: 500;
  cursor: pointer;
  transition: all .2s;
}
.category-chip.active, .category-chip:hover {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
}
.pos-items-grid {
  flex: 1;
  padding: .85rem;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: .75rem;
  overflow-y: auto;
}
.pos-item-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: .75rem;
  display: flex;
  flex-direction: column;
  cursor: pointer;
  transition: all .2s;
  user-select: none;
}
.pos-item-card:hover {
  border-color: var(--primary);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.pos-item-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: .35rem;
}
.diet-indicator {
  width: 14px;
  height: 14px;
  border: 1.5px solid;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.diet-indicator.veg { border-color: #22c55e; }
.diet-indicator.veg::after {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #22c55e;
}
.diet-indicator.non-veg { border-color: #ef4444; }
.diet-indicator.non-veg::after {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #ef4444;
}
.pos-item-code {
  font-size: .65rem;
  font-family: monospace;
  background: var(--surface-high);
  color: var(--text-muted);
  padding: 1px 4px;
  border-radius: 3px;
}
.pos-item-name {
  font-size: .875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: .25rem;
  line-height: 1.25;
}
.pos-item-desc {
  font-size: .7rem;
  color: var(--text-muted);
  line-height: 1.3;
  margin-bottom: .5rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}
.pos-item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}
.pos-item-price {
  font-weight: 700;
  font-size: .9375rem;
  color: var(--accent);
}
.pos-add-btn {
  background: var(--surface-high);
  border: 1px solid var(--border);
  color: var(--text-primary);
  font-size: .75rem;
  font-weight: 600;
  padding: .2rem .5rem;
  border-radius: 4px;
  cursor: pointer;
  transition: all .15s;
}
.pos-item-card:hover .pos-add-btn {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
}
.pos-cart-section {
  width: 380px;
  display: flex;
  flex-direction: column;
  background: var(--surface-raised);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
}
.pos-cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: .75rem 1rem;
  border-bottom: 1px solid var(--border);
  background: var(--surface);
}
.cart-title {
  font-weight: 700;
  font-size: .9375rem;
}
.pos-customer-bar {
  display: flex;
  flex-direction: column;
  gap: .45rem;
  padding: .5rem .75rem;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
}
.pos-customer-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: .5rem;
}
.customer-loyalty-card {
  background: var(--surface-raised);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: .45rem .6rem;
  font-size: .75rem;
  transition: all .2s;
}
.customer-loyalty-card.is-returning {
  border-color: rgba(16, 185, 129, 0.4);
  background: rgba(16, 185, 129, 0.06);
}
.customer-loyalty-card.is-vip {
  border-color: rgba(245, 158, 11, 0.45);
  background: rgba(245, 158, 11, 0.08);
}
.customer-loyalty-card.is-new {
  border-color: rgba(59, 130, 246, 0.35);
  background: rgba(59, 130, 246, 0.06);
}
.cust-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: .25rem;
}
.cust-badge {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 2px 6px;
  border-radius: 3px;
}
.cust-loyalty-pills {
  display: flex;
  align-items: center;
  gap: .35rem;
  flex-wrap: wrap;
  margin-top: .3rem;
}
.cust-pill {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 2px 6px;
  font-size: 11px;
}
.cust-pill.points-pill {
  color: #f59e0b;
  border-color: rgba(245, 158, 11, 0.4);
  background: rgba(245, 158, 11, 0.12);
  font-weight: 700;
}
.btn-cust-clear {
  background: none;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  font-size: 1.1rem;
  line-height: 1;
  padding: 0 4px;
}
.btn-cust-clear:hover {
  color: var(--danger);
}
.pos-input-sm {
  padding: .35rem .5rem;
  font-size: .75rem;
}
.pos-cart-items {
  flex: 1;
  overflow-y: auto;
  padding: .5rem .75rem;
  display: flex;
  flex-direction: column;
  gap: .5rem;
}
.cart-empty-state {
  margin: auto;
  text-align: center;
  padding: 2rem 1rem;
}
.cart-item-row {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: .5rem .75rem;
  display: flex;
  flex-direction: column;
  gap: .3rem;
}
.cart-item-main {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.cart-item-title {
  font-size: .85rem;
  font-weight: 600;
  color: var(--text-primary);
}
.cart-item-stepper {
  display: flex;
  align-items: center;
  gap: .35rem;
}
.step-btn {
  background: var(--surface-high);
  border: 1px solid var(--border);
  color: var(--text-primary);
  width: 24px;
  height: 24px;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-weight: 700;
}
.step-qty {
  font-size: .85rem;
  font-weight: 700;
  min-width: 20px;
  text-align: center;
}
.pos-cart-summary {
  padding: .75rem 1rem;
  border-top: 1px solid var(--border);
  background: var(--surface);
}
.summary-line {
  display: flex;
  justify-content: space-between;
  font-size: .85rem;
  margin-bottom: .25rem;
}
.summary-line.total-line {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--text-primary);
  border-top: 1px dashed var(--border);
  padding-top: .4rem;
  margin-top: .4rem;
}
.pos-actions-grid {
  display: flex;
  flex-direction: column;
  gap: .5rem;
}

/* MODAL OVERLAYS: Strictly hidden by default! */
.pos-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.75);
  display: none !important;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(4px);
}
.pos-modal-overlay.active,
.pos-modal-overlay[style*="display: flex"] {
  display: flex !important;
}
.pos-modal-overlay[hidden],
[hidden].pos-modal-overlay {
  display: none !important;
}

.pos-modal-card {
  background: var(--surface-raised);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  width: 90%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  max-height: 85vh;
  box-shadow: 0 10px 40px rgba(0,0,0,0.6);
}
.pos-modal-header {
  padding: 1rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-title { font-weight: 700; font-size: 1.1rem; }
.btn-close {
  background: none;
  border: none;
  color: var(--text-muted);
  font-size: 1.5rem;
  cursor: pointer;
  line-height: 1;
}
.pos-modal-body {
  padding: 1rem;
  overflow-y: auto;
}
.pos-modal-footer {
  padding: .75rem 1rem;
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--surface);
}
/* Table cards */
.table-card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
  gap: .75rem;
}
.table-pos-card {
  background: var(--surface);
  border: 2px solid var(--border);
  border-radius: var(--radius-sm);
  padding: .75rem;
  text-align: center;
  cursor: pointer;
  transition: all .2s;
}
.table-pos-card:hover { transform: scale(1.03); }
.table-pos-card.status-available { border-color: var(--success); }
.table-pos-card.status-occupied  { border-color: var(--danger); background: var(--danger-light); }
.table-pos-card.status-reserved  { border-color: var(--warning); background: var(--warning-light); }
.table-pos-card.status-dirty     { border-color: var(--text-muted); opacity: .7; }
.table-num { font-size: 1.1rem; font-weight: 700; }
.legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
.legend-dot.status-available { background: var(--success); }
.legend-dot.status-occupied  { background: var(--danger); }
.legend-dot.status-reserved  { background: var(--warning); }
.legend-dot.status-dirty     { background: var(--text-muted); }
.floor-tabs { display: flex; gap: .5rem; }
.floor-tab-btn {
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--text-secondary);
  padding: .4rem .85rem;
  border-radius: var(--radius-sm);
  cursor: pointer;
}
.floor-tab-btn.active {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
}
.floor-table-view { display: none; }
.floor-table-view.active { display: block; }
.payment-methods-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: .5rem;
}
.pm-radio-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: .75rem;
  display: flex;
  align-items: center;
  gap: .5rem;
  cursor: pointer;
}
.pm-radio-card.active {
  border-color: var(--success);
  background: var(--success-light);
}
/* Settle Breakdown & Discount Styles */
.settle-breakdown-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
}
.settle-net-row {
  border-top: 1px dashed var(--border);
  margin-top: .4rem;
}
.settle-net-val {
  font-size: 1.85rem;
  font-weight: 800;
  letter-spacing: -0.5px;
}
.discount-box {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  padding: .75rem;
}
.discount-nav-tabs {
  display: flex;
  gap: 4px;
  background: var(--surface-raised);
  padding: 2px;
  border-radius: 4px;
}
.discount-tab-btn {
  background: none;
  border: none;
  font-size: .75rem;
  font-weight: 600;
  color: var(--text-muted);
  padding: .25rem .5rem;
  border-radius: 3px;
  cursor: pointer;
  transition: all .15s;
}
.discount-tab-btn.active {
  background: var(--primary);
  color: #fff;
}
.coupon-chips-list {
  display: flex;
  flex-wrap: wrap;
  gap: .35rem;
}
.coupon-chip {
  background: var(--surface-raised);
  border: 1px dashed var(--border);
  border-radius: 4px;
  padding: .2rem .5rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  font-size: .75rem;
  transition: all .15s;
}
.coupon-chip:hover {
  border-color: var(--primary);
  background: rgba(99, 102, 241, 0.1);
}
.coupon-chip-code {
  font-weight: 700;
  color: var(--primary);
  font-family: monospace;
}
.coupon-chip-desc {
  color: var(--text-secondary);
}
.coupon-chip-min {
  background: var(--surface);
  color: var(--text-muted);
  font-size: 9px;
  padding: 1px 4px;
  border-radius: 2px;
  border: 1px solid var(--border);
}
.discount-applied-banner {
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid var(--success);
  border-radius: var(--radius-sm);
  padding: .4rem .75rem;
  margin-top: .6rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--success);
}
</style>

<!-- POS JAVASCRIPT ENGINE -->
<script>
// Cache all menu items for fast lookups
window.POS_MENU_ITEMS = <?= json_encode($menuItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
window.POS_MENU_MAP = {};
if (Array.isArray(window.POS_MENU_ITEMS)) {
  window.POS_MENU_ITEMS.forEach(function(it) {
    window.POS_MENU_MAP[it.id] = it;
  });
}

var currentOrderType  = 'dine_in';
var selectedTableId   = null;
var selectedTableNum  = null;
var activeOrderId     = null;
var activeOrderNumber = null;
var activeOrderTotal  = 0.0;
var cart = []; // items: { menu_item_id, item_name, unit_price, quantity, special_notes }

function setOrderType(type) {
  currentOrderType = type;
  document.querySelectorAll('.pos-tab-btn').forEach(function(b) {
    b.classList.toggle('active', b.getAttribute('data-type') === type);
  });
  
  var tblBadge = document.getElementById('selected-table-badge');
  if (type === 'dine_in') {
    tblBadge.style.display = 'flex';
  } else {
    tblBadge.style.display = 'none';
    selectedTableId = null;
    selectedTableNum = null;
    document.getElementById('current-table-label').textContent = 'None Selected';
  }
  updateCartDisplay();
}

function showTableModal() {
  var m = document.getElementById('table-modal');
  m.hidden = false;
  m.style.setProperty('display', 'flex', 'important');
}

function closeTableModal() {
  var m = document.getElementById('table-modal');
  m.hidden = true;
  m.style.setProperty('display', 'none', 'important');
}

function switchFloorTab(floorId, btn) {
  document.querySelectorAll('.floor-tab-btn').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  document.querySelectorAll('.floor-table-view').forEach(function(v) { v.classList.remove('active'); });
  var target = document.getElementById('floor-view-' + floorId);
  if (target) target.classList.add('active');
}

function selectTable(id, num, status) {
  selectedTableId = id;
  selectedTableNum = num;
  document.getElementById('current-table-label').textContent = num;
  closeTableModal();
  updateCartDisplay();

  // Fetch active order or active reservation for this table
  window.rmsGet('<?= site_url('admin/pos/table-order') ?>/' + id)
    .then(function(res) {
      if (res.success && res.data) {
        if (res.data.active && res.data.order) {
          activeOrderId     = res.data.order.id;
          activeOrderNumber = res.data.order.order_number;
          activeOrderTotal  = parseFloat(res.data.order.final_total || 0);
          if (res.data.order.customer_name && res.data.order.customer_name !== 'Walk-in Guest') {
            var custName = document.getElementById('cust-name');
            if (custName) custName.value = res.data.order.customer_name;
          }
          if (res.data.order.customer_phone) {
            var custPhone = document.getElementById('cust-phone');
            if (custPhone) {
              custPhone.value = res.data.order.customer_phone;
              performCustomerLookup(res.data.order.customer_phone);
            }
          }
          updateCartDisplay();
        } else {
          activeOrderId     = null;
          activeOrderNumber = null;
          activeOrderTotal  = 0.0;
        }

        // Auto-fill from reservation if present
        if (res.data.reservation) {
          var custName = document.getElementById('cust-name');
          var custPhone = document.getElementById('cust-phone');
          if (custName && (!custName.value || custName.value === 'Walk-in Guest')) {
            custName.value = res.data.reservation.customer_name || '';
          }
          if (custPhone && !custPhone.value && res.data.reservation.customer_phone) {
            custPhone.value = res.data.reservation.customer_phone;
            performCustomerLookup(custPhone.value);
          }
        }
      }
    });
}

function filterCategory(catId, btn) {
  document.querySelectorAll('.category-chip').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  
  var cards = document.querySelectorAll('.pos-item-card');
  cards.forEach(function(card) {
    var c = card.getAttribute('data-cat');
    if (catId === 'all' || c === String(catId)) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

function filterMenuItems() {
  var q = document.getElementById('pos-item-search').value.toLowerCase().trim();
  var cards = document.querySelectorAll('.pos-item-card');
  cards.forEach(function(card) {
    var name = card.getAttribute('data-name');
    var code = card.getAttribute('data-code');
    if (!q || name.indexOf(q) !== -1 || code.indexOf(q) !== -1) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

function addToCartById(itemId) {
  var item = window.POS_MENU_MAP[itemId];
  if (!item) return;
  
  var existing = cart.find(function(it) { return it.menu_item_id === item.id; });
  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({
      menu_item_id: item.id,
      item_name: item.name,
      unit_price: parseFloat(item.price),
      quantity: 1,
      special_notes: ''
    });
  }
  updateCartDisplay();
}

function updateQty(idx, delta) {
  if (!cart[idx]) return;
  cart[idx].quantity += delta;
  if (cart[idx].quantity <= 0) {
    cart.splice(idx, 1);
  }
  updateCartDisplay();
}

function updateNotes(idx, notes) {
  if (cart[idx]) cart[idx].special_notes = notes;
}

function clearCart() {
  if (cart.length > 0 && !confirm('Clear all items from current cart?')) return;
  cart = [];
  updateCartDisplay();
}

function calculateTotals() {
  var subtotal = 0;
  cart.forEach(function(it) {
    subtotal += (it.unit_price * it.quantity);
  });
  var tax = subtotal * 0.05;
  var grandTotal = subtotal + tax;
  return { subtotal: subtotal, tax: tax, grandTotal: grandTotal };
}

function updateCartDisplay() {
  var container = document.getElementById('pos-cart-items');
  var orderInfo = document.getElementById('cart-order-info');
  
  var typeLabel = currentOrderType === 'dine_in' ? '🍽️ Dine-In' : (currentOrderType === 'takeaway' ? '🥡 Takeaway' : '🛵 Delivery');
  var tableInfo = (currentOrderType === 'dine_in' && selectedTableNum) ? (' &bull; Table: ' + selectedTableNum) : '';
  var orderBadge = activeOrderId ? (' &bull; <span class="badge badge-warning">Active Order #' + activeOrderNumber + ' (₹' + activeOrderTotal.toFixed(2) + ')</span>') : '';
  orderInfo.innerHTML = typeLabel + tableInfo + orderBadge;

  var totals = calculateTotals();

  if (cart.length === 0) {
    if (activeOrderId) {
      container.innerHTML = '<div class="cart-empty-state"><div style="font-size: 2.25rem; margin-bottom: .5rem;">📋</div><div class="text-secondary fw-600">Active Order #' + activeOrderNumber + '</div><div class="text-xs text-muted mt-1">Ready for settlement (₹' + activeOrderTotal.toFixed(2) + ') or add new dishes to append.</div></div>';
      document.getElementById('sum-subtotal').textContent = '₹' + activeOrderTotal.toFixed(2);
      document.getElementById('sum-tax').textContent = '₹0.00';
      document.getElementById('sum-total').textContent = '₹' + activeOrderTotal.toFixed(2);
      document.getElementById('settle-amount-display').textContent = '₹' + activeOrderTotal.toFixed(2);
    } else {
      container.innerHTML = '<div class="cart-empty-state"><div style="font-size: 2.5rem; margin-bottom: .5rem;">🛒</div><div class="text-secondary fw-600">Your cart is empty</div><div class="text-xs text-muted mt-1">Click on menu items to begin building the order</div></div>';
      document.getElementById('sum-subtotal').textContent = '₹0.00';
      document.getElementById('sum-tax').textContent = '₹0.00';
      document.getElementById('sum-total').textContent = '₹0.00';
      document.getElementById('settle-amount-display').textContent = '₹0.00';
    }
    var mb = document.getElementById('mobile-cart-count');
    if (mb) { mb.textContent = '0'; mb.style.display = 'none'; }
    return;
  }

  var mb = document.getElementById('mobile-cart-count');
  if (mb) {
    var totalQty = cart.reduce(function(acc, i) { return acc + i.quantity; }, 0);
    mb.textContent = totalQty;
    mb.style.display = totalQty > 0 ? 'inline-block' : 'none';
  }

  var html = '';
  cart.forEach(function(it, idx) {
    var itemTotal = it.unit_price * it.quantity;
    html += '<div class="cart-item-row">' +
      '<div class="cart-item-main">' +
        '<div>' +
          '<div class="cart-item-title">' + it.item_name + '</div>' +
          '<div class="text-xs text-muted">₹' + it.unit_price.toFixed(2) + ' each</div>' +
        '</div>' +
        '<div class="cart-item-stepper">' +
          '<button type="button" class="step-btn" onclick="updateQty(' + idx + ', -1)">&minus;</button>' +
          '<span class="step-qty">' + it.quantity + '</span>' +
          '<button type="button" class="step-btn" onclick="updateQty(' + idx + ', 1)">&plus;</button>' +
          '<span class="td-bold text-small" style="min-width: 60px; text-align: right;">₹' + itemTotal.toFixed(2) + '</span>' +
        '</div>' +
      '</div>' +
      '<input type="text" class="form-control pos-input-sm" placeholder="Cooking instruction / note" value="' + (it.special_notes || '') + '" onchange="updateNotes(' + idx + ', this.value)">' +
    '</div>';
  });

  container.innerHTML = html;

  document.getElementById('sum-subtotal').textContent = '₹' + totals.subtotal.toFixed(2);
  document.getElementById('sum-tax').textContent = '₹' + totals.tax.toFixed(2);
  document.getElementById('sum-total').textContent = '₹' + totals.grandTotal.toFixed(2);
  document.getElementById('settle-amount-display').textContent = '₹' + totals.grandTotal.toFixed(2);
}

function submitOrder(action, callback) {
  if (cart.length === 0) {
    alert('Cart is empty. Please add items to place an order.');
    return;
  }

  if (currentOrderType === 'dine_in' && !selectedTableId) {
    alert('Please select a dining table before sending order.');
    showTableModal();
    return;
  }

  var btn = document.getElementById('btn-place-order');
  btn.disabled = true;
  btn.textContent = 'Sending to Kitchen...';

  var payload = {
    order_type: currentOrderType,
    table_id: selectedTableId,
    customer_name: document.getElementById('cust-name').value,
    customer_phone: document.getElementById('cust-phone').value,
    items: cart
  };

  window.rmsPost('<?= site_url('admin/pos/order') ?>', payload)
    .then(function(res) {
      btn.disabled = false;
      btn.textContent = '🍳 Send to Kitchen (KOT)';
      if (res.success) {
        activeOrderId     = res.data.order_id;
        activeOrderNumber = res.data.order_number;
        activeOrderTotal  = parseFloat(res.data.final_total || 0);
        cart = [];
        updateCartDisplay();
        
        if (typeof callback === 'function') {
          callback(res.data);
        } else {
          alert('Order #' + res.data.order_number + ' created and dispatched to kitchen! (KOT: ' + res.data.kot_number + ')');
        }
      } else {
        alert(res.message || 'Failed to place order.');
      }
    })
    .catch(function(err) {
      btn.disabled = false;
      btn.textContent = '🍳 Send to Kitchen (KOT)';
      alert('Request error placing order.');
    });
}

var currentDiscount = {
  type: null, // 'coupon' or 'manual'
  amount: 0.0,
  couponId: null,
  couponCode: '',
  reason: '',
  label: ''
};
var currentOriginalBill = 0.0;
var manualDiscType = 'percentage';

function openSettleModal() {
  var totals = calculateTotals();
  var amountToSettle = 0;

  if (cart.length > 0) {
    amountToSettle = totals.grandTotal;
    document.getElementById('settle-order-ref').textContent = 'New Order (' + cart.length + ' item' + (cart.length > 1 ? 's' : '') + ')';
  } else if (activeOrderId && activeOrderTotal > 0) {
    amountToSettle = activeOrderTotal;
    document.getElementById('settle-order-ref').textContent = 'Active Order #' + activeOrderNumber;
  } else {
    if (confirm('Your cart is currently empty. Would you like to select an occupied table to settle its bill?')) {
      showTableModal();
    }
    return;
  }

  currentOriginalBill = amountToSettle;
  removeDiscount(); // Clear any previous discount session
  renderDiscountState();

  var m = document.getElementById('settle-modal');
  if (m) {
    m.removeAttribute('hidden');
    m.classList.add('active');
    m.style.setProperty('display', 'flex', 'important');
  }
}

function closeSettleModal() {
  var m = document.getElementById('settle-modal');
  if (m) {
    m.setAttribute('hidden', '');
    m.classList.remove('active');
    m.style.setProperty('display', 'none', 'important');
  }
}

// Customer & Loyalty State
var activeCustomer = null;
var custLookupTimer = null;

function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function performCustomerLookup(phone, callback) {
  phone = (phone || '').trim();
  var card = document.getElementById('customer-loyalty-status');
  var spinner = document.getElementById('cust-lookup-spinner');

  if (!phone || phone.length < 5) {
    if (card) {
      card.style.display = 'none';
      card.innerHTML = '';
    }
    activeCustomer = null;
    if (typeof callback === 'function') callback(null);
    return;
  }

  if (spinner) spinner.style.display = 'inline';

  window.rmsGet('<?= site_url('admin/pos/lookup-customer') ?>?phone=' + encodeURIComponent(phone))
    .then(function(res) {
      if (spinner) spinner.style.display = 'none';

      if (res.success && res.data && res.data.exists && res.data.customer) {
        activeCustomer = res.data.customer;

        // Auto-fill customer name if empty or generic
        var nameInput = document.getElementById('cust-name');
        if (nameInput && (!nameInput.value.trim() || nameInput.value === 'Walk-in Guest')) {
          nameInput.value = activeCustomer.name;
        }

        if (card) {
          card.className = 'customer-loyalty-card ' + (activeCustomer.vip_status ? 'is-vip' : 'is-returning');
          card.style.display = 'block';
          card.innerHTML = 
            '<div class="cust-card-header">' +
              '<span class="badge ' + (activeCustomer.vip_status ? 'badge-warning' : 'badge-success') + ' cust-badge">' +
                (activeCustomer.vip_status ? '👑 VIP Diner' : '⭐ Returning Customer') +
              '</span>' +
              '<button type="button" class="btn-cust-clear" onclick="clearCustomerInfo()" title="Clear customer">&times;</button>' +
            '</div>' +
            '<div class="cust-card-body">' +
              '<div style="font-weight: 700; color: var(--text-primary); font-size: 0.85rem;">' + escapeHtml(activeCustomer.name) + '</div>' +
              '<div class="cust-loyalty-pills">' +
                '<span class="cust-pill points-pill" title="1 Point = ₹1.00 Discount">' +
                  '⭐ <strong>' + activeCustomer.loyalty_points + ' Points</strong> (₹' + activeCustomer.points_value.toFixed(2) + ')' +
                '</span>' +
                '<span class="cust-pill">🔄 ' + activeCustomer.total_visits + ' visits</span>' +
                '<span class="cust-pill">💰 ₹' + activeCustomer.lifetime_spend.toFixed(2) + ' spent</span>' +
              '</div>' +
            '</div>';
        }
      } else {
        // Customer does not exist yet (New Customer)
        activeCustomer = { exists: false, phone: phone };

        if (card) {
          card.className = 'customer-loyalty-card is-new';
          card.style.display = 'block';
          card.innerHTML = 
            '<div class="cust-card-header">' +
              '<span class="badge badge-info cust-badge">✨ New Customer</span>' +
              '<button type="button" class="btn-cust-clear" onclick="clearCustomerInfo()" title="Clear customer">&times;</button>' +
            '</div>' +
            '<div class="text-xs text-muted mt-1">' +
              'First visit! Customer profile will be auto-created and earn <strong>1 pt per ₹10</strong> on this bill.' +
            '</div>';
        }
      }

      // If Settle Modal is currently open on loyalty tab, refresh it
      var paneLoyalty = document.getElementById('pane-loyalty');
      if (paneLoyalty && paneLoyalty.style.display !== 'none') {
        prepareLoyaltyPane();
      }

      if (typeof callback === 'function') callback(activeCustomer);
    })
    .catch(function(err) {
      if (spinner) spinner.style.display = 'none';
      if (typeof callback === 'function') callback(null);
    });
}

function clearCustomerInfo() {
  activeCustomer = null;
  var nameIn = document.getElementById('cust-name');
  var phoneIn = document.getElementById('cust-phone');
  var card = document.getElementById('customer-loyalty-status');

  if (nameIn) nameIn.value = '';
  if (phoneIn) phoneIn.value = '';
  if (card) {
    card.style.display = 'none';
    card.innerHTML = '';
  }

  // If loyalty discount was applied, remove it
  if (currentDiscount && currentDiscount.type === 'loyalty') {
    removeDiscount();
  }

  var paneLoyalty = document.getElementById('pane-loyalty');
  if (paneLoyalty && paneLoyalty.style.display !== 'none') {
    prepareLoyaltyPane();
  }
}

// Attach live auto-lookup listeners to customer phone field
document.addEventListener('DOMContentLoaded', function() {
  var phoneInput = document.getElementById('cust-phone');
  if (phoneInput) {
    phoneInput.addEventListener('input', function() {
      var val = this.value.trim();
      clearTimeout(custLookupTimer);
      if (val.length >= 10) {
        custLookupTimer = setTimeout(function() {
          performCustomerLookup(val);
        }, 350);
      } else if (val.length === 0) {
        clearCustomerInfo();
      }
    });

    phoneInput.addEventListener('blur', function() {
      var val = this.value.trim();
      if (val.length >= 5) {
        performCustomerLookup(val);
      } else if (!val) {
        clearCustomerInfo();
      }
    });
  }
});

function selectPaymentMethod(radio) {
  document.querySelectorAll('.pm-radio-card').forEach(function(c) { c.classList.remove('active'); });
  radio.closest('.pm-radio-card').classList.add('active');
}

function switchDiscountTab(tab) {
  var btnCp = document.getElementById('tab-btn-coupon');
  var btnMan = document.getElementById('tab-btn-manual');
  var btnLoyalty = document.getElementById('tab-btn-loyalty');
  var paneCp = document.getElementById('pane-coupon');
  var paneMan = document.getElementById('pane-manual');
  var paneLoyalty = document.getElementById('pane-loyalty');

  btnCp.classList.toggle('active', tab === 'coupon');
  btnMan.classList.toggle('active', tab === 'manual');
  if (btnLoyalty) btnLoyalty.classList.toggle('active', tab === 'loyalty');

  paneCp.style.display = (tab === 'coupon') ? 'block' : 'none';
  paneMan.style.display = (tab === 'manual') ? 'block' : 'none';
  if (paneLoyalty) {
    paneLoyalty.style.display = (tab === 'loyalty') ? 'block' : 'none';
    if (tab === 'loyalty') {
      prepareLoyaltyPane();
    }
  }
}

function prepareLoyaltyPane() {
  var noCustBox   = document.getElementById('loyalty-no-customer');
  var infoBox     = document.getElementById('loyalty-customer-info');
  var newCustBox  = document.getElementById('loyalty-new-customer-msg');
  var zeroMsg     = document.getElementById('loyalty-zero-points-msg');
  var redeemCtrls = document.getElementById('loyalty-redeem-controls');

  var phone = (document.getElementById('cust-phone')?.value || '').trim();

  if (activeCustomer && activeCustomer.id) {
    // Registered Returning Customer
    if (noCustBox) noCustBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'none';
    if (infoBox) infoBox.style.display = 'block';

    document.getElementById('loyalty-cust-name').textContent = activeCustomer.name;
    document.getElementById('loyalty-cust-phone').textContent = activeCustomer.phone;
    document.getElementById('loyalty-avail-points').textContent = activeCustomer.loyalty_points;
    document.getElementById('loyalty-worth-text').textContent = 'Worth ₹' + (activeCustomer.points_value || activeCustomer.loyalty_points).toFixed(2) + ' (1 pt = ₹1)';

    var maxPts = Math.min(activeCustomer.loyalty_points, Math.floor(currentOriginalBill));
    document.getElementById('loyalty-max-allowed').textContent = maxPts;

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
    // New Customer
    if (noCustBox) noCustBox.style.display = 'none';
    if (infoBox) infoBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'block';
  } else if (phone && phone.length >= 5) {
    // Phone was typed but lookup not finished yet
    performCustomerLookup(phone, function() {
      prepareLoyaltyPane();
    });
  } else {
    // No phone number provided
    if (infoBox) infoBox.style.display = 'none';
    if (newCustBox) newCustBox.style.display = 'none';
    if (noCustBox) noCustBox.style.display = 'block';
    var modalPhoneInput = document.getElementById('modal-cust-phone');
    if (modalPhoneInput) modalPhoneInput.value = '';
  }
}

function lookupCustomerFromModal() {
  var modalPhone = (document.getElementById('modal-cust-phone')?.value || '').trim();
  if (!modalPhone) {
    alert('Please enter a customer phone number.');
    return;
  }
  var mainPhone = document.getElementById('cust-phone');
  if (mainPhone) mainPhone.value = modalPhone;

  performCustomerLookup(modalPhone, function() {
    prepareLoyaltyPane();
  });
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

function setManualDiscType(type) {
  manualDiscType = type;
  var btnPct = document.getElementById('btn-disc-type-pct');
  var btnFix = document.getElementById('btn-disc-type-fixed');
  if (type === 'percentage') {
    btnPct.className = 'btn btn-xs btn-primary';
    btnFix.className = 'btn btn-xs btn-secondary';
    document.getElementById('manual-disc-val').placeholder = 'Percent % (e.g. 10)';
  } else {
    btnFix.className = 'btn btn-xs btn-primary';
    btnPct.className = 'btn btn-xs btn-secondary';
    document.getElementById('manual-disc-val').placeholder = 'Amount ₹ (e.g. 50)';
  }
}

function selectCouponChip(code) {
  document.getElementById('coupon-code-input').value = code;
  applyCouponCode();
}

function applyCouponCode() {
  var code = (document.getElementById('coupon-code-input').value || '').trim();
  var errEl = document.getElementById('coupon-error-msg');
  errEl.style.display = 'none';
  errEl.textContent = '';

  if (!code) {
    errEl.textContent = 'Please enter or select a coupon code.';
    errEl.style.display = 'block';
    return;
  }

  var btn = document.getElementById('btn-apply-coupon');
  btn.disabled = true;
  btn.textContent = 'Checking...';

  var custPhone = (document.getElementById('cust-phone') ? document.getElementById('cust-phone').value : '');

  window.rmsPost('<?= site_url('admin/pos/validate-coupon') ?>', {
    code: code,
    order_amount: currentOriginalBill,
    customer_phone: custPhone
  }).then(function(res) {
    btn.disabled = false;
    btn.textContent = 'Apply Code';

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
      errEl.textContent = res.message || 'Invalid or expired coupon code.';
      errEl.style.display = 'block';
    }
  }).catch(function(err) {
    btn.disabled = false;
    btn.textContent = 'Apply Code';
    errEl.textContent = 'Error verifying coupon. Please check network.';
    errEl.style.display = 'block';
  });
}

function applyManualDiscount() {
  var val = parseFloat(document.getElementById('manual-disc-val').value || 0);
  var reason = document.getElementById('manual-disc-reason').value || 'Manual Discount';
  var errEl = document.getElementById('coupon-error-msg');
  errEl.style.display = 'none';

  if (isNaN(val) || val <= 0) {
    errEl.textContent = 'Please enter a valid discount value greater than 0.';
    errEl.style.display = 'block';
    return;
  }

  var discAmt = 0;
  if (manualDiscType === 'percentage') {
    if (val > 100) {
      errEl.textContent = 'Percentage discount cannot exceed 100%.';
      errEl.style.display = 'block';
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
  var netDisplay = document.getElementById('settle-amount-display');
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
  if (confirmBtn) confirmBtn.textContent = 'Confirm Settlement • ₹' + netPayable.toFixed(2);
}

function executeSettle() {
  var checkedRadio = document.querySelector('input[name="payment_method_id"]:checked');
  var pmId = checkedRadio ? checkedRadio.value : 1;
  var btn = document.getElementById('btn-confirm-settle');

  var settlePayload = {
    payment_method_id: pmId,
    discount_amount: currentDiscount.amount,
    coupon_id: currentDiscount.couponId,
    coupon_code: currentDiscount.couponCode,
    discount_reason: currentDiscount.reason,
    redeemed_points: (currentDiscount.type === 'loyalty' ? (currentDiscount.redeemedPoints || 0) : 0)
  };

  // If cart has items and order not sent yet, atomically place then settle
  if (cart.length > 0 && !activeOrderId) {
    btn.disabled = true;
    btn.textContent = 'Placing & Settling...';

    submitOrder('settle', function(orderData) {
      var newOrderId = orderData.order_id;
      window.rmsPost('<?= site_url('admin/pos/complete-order') ?>/' + newOrderId, settlePayload)
        .then(function(res) {
          btn.disabled = false;
          btn.textContent = 'Confirm Settlement';
          closeSettleModal();
          if (res.success) {
            alert(res.message || ('Order #' + orderData.order_number + ' placed and settled successfully!'));
            resetPosState();
          } else {
            alert(res.message || 'Settlement error');
          }
        }).catch(function(err) {
          btn.disabled = false;
          btn.textContent = 'Confirm Settlement';
          alert('Settlement request failed.');
        });
    });
    return;
  }

  if (!activeOrderId) {
    alert('No active order to settle.');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Settling...';

  window.rmsPost('<?= site_url('admin/pos/complete-order') ?>/' + activeOrderId, settlePayload)
    .then(function(res) {
      btn.disabled = false;
      btn.textContent = 'Confirm Settlement';
      closeSettleModal();
      if (res.success) {
        alert(res.message || 'Order Settled Successfully!');
        resetPosState();
      } else {
        alert(res.message || 'Settlement error');
      }
    }).catch(function(err) {
      btn.disabled = false;
      btn.textContent = 'Confirm Settlement';
      alert('Request failed');
    });
}

function resetPosState() {
  activeOrderId     = null;
  activeOrderNumber = null;
  activeOrderTotal  = 0.0;
  selectedTableId   = null;
  selectedTableNum  = null;
  currentOriginalBill = 0.0;
  removeDiscount();
  document.getElementById('current-table-label').textContent = 'None Selected';
  clearCustomerInfo();
  cart = [];
  updateCartDisplay();
}

function switchPosMobileView(view) {
  var menuSec = document.querySelector('.pos-menu-section');
  var cartSec = document.querySelector('.pos-cart-section');
  var btnMenu = document.getElementById('btn-pos-menu');
  var btnCart = document.getElementById('btn-pos-cart');
  if (!menuSec || !cartSec) return;
  if (view === 'cart') {
    menuSec.classList.add('mobile-hidden');
    cartSec.classList.remove('mobile-hidden');
    if (btnMenu) btnMenu.classList.remove('active');
    if (btnCart) btnCart.classList.add('active');
  } else {
    menuSec.classList.remove('mobile-hidden');
    cartSec.classList.add('mobile-hidden');
    if (btnMenu) btnMenu.classList.add('active');
    if (btnCart) btnCart.classList.remove('active');
  }
}

// Close modals when clicking backdrop or pressing Escape
window.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeTableModal();
    closeSettleModal();
  }
});

document.getElementById('table-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeTableModal();
});
document.getElementById('settle-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeSettleModal();
});
</script>
