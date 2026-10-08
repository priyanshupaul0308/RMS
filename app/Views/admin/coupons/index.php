<div class="page-header">
  <div>
    <h1 class="page-title">Discount & Coupon Management</h1>
    <p class="page-subtitle">Create promotional codes, manage percentage/fixed discounts, usage limits, and tracking</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <a href="<?= site_url('admin/coupons/reports') ?>" class="btn btn-outline">
      Redemption Reports
    </a>
    <button type="button" class="btn btn-primary" onclick="showCouponModal()">
      + Create Coupon
    </button>
  </div>
</div>

<!-- KPI STATS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Active Promotions</div>
    <div class="stat-value text-primary"><?= esc($activeCount) ?></div>
    <div class="stat-sub">Valid coupons currently running</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Redemptions</div>
    <div class="stat-value text-success"><?= esc($totalRedemptions) ?></div>
    <div class="stat-sub">Used across customer orders</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Discount Granted</div>
    <div class="stat-value text-warning">&#8377;<?= number_format($totalSavings, 2) ?></div>
    <div class="stat-sub">Savings given to diners</div>
  </div>
</div>

<!-- COUPONS TABLE -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Promotional Discount Coupons (<?= count($coupons) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Coupon Code</th>
            <th>Campaign Name</th>
            <th>Discount Value</th>
            <th>Min. Spend</th>
            <th>Usage / Limit</th>
            <th>Validity Window</th>
            <th>Status</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($coupons)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No discount coupons found. Click "+ Create Coupon" to launch your first promotional code.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($coupons as $c): ?>
              <tr>
                <td>
                  <span style="font-family: monospace; font-weight: 700; background: rgba(59,130,246,0.15); color: #60a5fa; padding: 4px 8px; border-radius: 4px; border: 1px dashed rgba(96,165,250,0.4);">
                    <?= esc($c['code']) ?>
                  </span>
                </td>
                <td>
                  <strong><?= esc($c['name']) ?></strong>
                </td>
                <td>
                  <?php if ($c['type'] === 'percentage'): ?>
                    <span style="color: #10b981; font-weight: 600;"><?= esc((float)$c['value']) ?>% OFF</span>
                    <?php if (!empty($c['max_discount_amount'])): ?>
                      <div style="font-size: 0.75rem; color: var(--text-muted);">Max &#8377;<?= number_format((float)$c['max_discount_amount'], 2) ?></div>
                    <?php endif; ?>
                  <?php else: ?>
                    <span style="color: #10b981; font-weight: 600;">Flat &#8377;<?= number_format((float)$c['value'], 2) ?> OFF</span>
                  <?php endif; ?>
                </td>
                <td>
                  &#8377;<?= number_format((float)$c['min_order_amount'], 2) ?>
                </td>
                <td>
                  <strong><?= esc($c['times_used']) ?></strong> / <?= (int)$c['usage_limit_total'] > 0 ? esc($c['usage_limit_total']) : '&infin;' ?>
                </td>
                <td>
                  <div style="font-size: 0.85rem;">
                    <?= esc(date('M d', strtotime($c['start_date']))) ?> - <?= esc(date('M d, Y', strtotime($c['end_date']))) ?>
                  </div>
                  <?php if ($c['end_date'] < date('Y-m-d')): ?>
                    <span class="badge badge-danger" style="font-size: 0.7rem;">Expired</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ((int)$c['is_active'] === 1 && $c['end_date'] >= date('Y-m-d')): ?>
                    <span class="badge badge-success">Active</span>
                  <?php elseif ((int)$c['is_active'] === 1): ?>
                    <span class="badge badge-warning">Expired</span>
                  <?php else: ?>
                    <span class="badge badge-danger">Inactive</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <button type="button" class="btn btn-ghost btn-sm" onclick="toggleCoupon(<?= (int)$c['id'] ?>)">
                    <?= (int)$c['is_active'] === 1 ? 'Disable' : 'Enable' ?>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL: CREATE / EDIT COUPON -->
<div class="pos-modal-overlay" id="coupon-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 560px;">
    <div class="pos-modal-header">
      <div class="modal-title">Create Discount Coupon</div>
      <button type="button" class="btn-close" onclick="closeModal('coupon-modal')">&times;</button>
    </div>
    <form id="coupon-form" onsubmit="submitCoupon(event)">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="cp-id" value="">
      <div class="pos-modal-body">
        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cp-code">Coupon Code <span class="form-required">*</span></label>
              <input type="text" id="cp-code" name="code" class="form-control" required placeholder="e.g. SUMMER25" style="text-transform: uppercase;">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cp-type">Discount Type <span class="form-required">*</span></label>
              <select id="cp-type" name="type" class="form-control" required onchange="handleTypeChange()">
                <option value="percentage">Percentage (%)</option>
                <option value="fixed">Fixed Amount (&#8377;)</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="cp-name">Campaign Title <span class="form-required">*</span></label>
          <input type="text" id="cp-name" name="name" class="form-control" required placeholder="e.g. Summer Weekend 25% Off Deal">
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cp-value">Discount Value <span class="form-required">*</span></label>
              <input type="number" step="0.01" id="cp-value" name="value" class="form-control" required placeholder="e.g. 20">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3" id="group-max-discount">
              <label class="form-label" for="cp-max-disc">Max Cap (&#8377;)</label>
              <input type="number" step="0.01" id="cp-max-disc" name="max_discount_amount" class="form-control" placeholder="Optional cap">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cp-min-order">Min Order Amount (&#8377;)</label>
              <input type="number" step="0.01" id="cp-min-order" name="min_order_amount" class="form-control" value="0.00">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cp-limit-total">Total Usage Limit</label>
              <input type="number" id="cp-limit-total" name="usage_limit_total" class="form-control" value="100">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cp-start">Valid From <span class="form-required">*</span></label>
              <input type="date" id="cp-start" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cp-end">Valid Until <span class="form-required">*</span></label>
              <input type="date" id="cp-end" name="end_date" class="form-control" value="<?= date('Y-12-31') ?>" required>
            </div>
          </div>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('coupon-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-coupon">Save Coupon</button>
      </div>
    </form>
  </div>
</div>

<script>
function showModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.removeAttribute('hidden');
    m.style.setProperty('display', 'flex', 'important');
  }
}

function closeModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.style.setProperty('display', 'none', 'important');
    m.setAttribute('hidden', '');
  }
}

function showCouponModal() { showModal('coupon-modal'); }

function handleTypeChange() {
  const type = document.getElementById('cp-type').value;
  const maxGroup = document.getElementById('group-max-discount');
  if (type === 'fixed') {
    maxGroup.style.display = 'none';
  } else {
    maxGroup.style.display = 'block';
  }
}

async function submitCoupon(e) {
  e.preventDefault();
  const form = document.getElementById('coupon-form');
  const btn = document.getElementById('btn-submit-coupon');
  btn.disabled = true;
  btn.innerText = 'Saving...';

  try {
    const res = await fetch('<?= site_url('admin/coupons/store') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error saving coupon');
      btn.disabled = false;
      btn.innerText = 'Save Coupon';
    }
  } catch(err) {
    alert('Network error.');
    btn.disabled = false;
    btn.innerText = 'Save Coupon';
  }
}

async function toggleCoupon(id) {
  try {
    const fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res = await fetch(`<?= site_url('admin/coupons/toggle') ?>/${id}`, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error updating status');
    }
  } catch(err) {
    alert('Network error.');
  }
}
</script>
