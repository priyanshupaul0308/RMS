<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">👥 Customer Relationship Management (CRM)</h1>
    <p class="page-subtitle">Guest dining histories, VIP preferences, lifetime spend, and loyalty points rewards</p>
  </div>
  <div class="d-flex gap-2">
    <button type="button" class="btn btn-outline" onclick="openModal('pointsModal')">
      ⭐ Reward / Redeem Points
    </button>
    <button type="button" class="btn btn-primary" onclick="openModal('addCustomerModal')">
      ➕ Add Customer Profile
    </button>
  </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid mb-3">
  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">👥</div>
    <div class="kpi-body">
      <div class="kpi-label">Registered Guests</div>
      <div class="kpi-value"><?= $totalCustomers ?></div>
      <div class="kpi-trend text-muted text-xs">Customer profiles in database</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">👑</div>
    <div class="kpi-body">
      <div class="kpi-label">VIP Regulars</div>
      <div class="kpi-value text-warning"><?= $totalVip ?></div>
      <div class="kpi-trend text-muted text-xs">High-frequency &amp; high-spend diners</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">💎</div>
    <div class="kpi-body">
      <div class="kpi-label">Total Guest Lifetime Spend</div>
      <div class="kpi-value">₹<?= number_format($totalSpend, 2) ?></div>
      <div class="kpi-trend text-muted text-xs">Cumulative dining revenue</div>
    </div>
  </div>
</div>

<!-- Search Bar -->
<div class="card mb-3">
  <div class="card-body p-2">
    <form action="<?= site_url('admin/crm') ?>" method="GET" class="d-flex gap-2">
      <input type="text" name="search" class="form-control flex-1" placeholder="Search by name, phone number, or email..." value="<?= esc($search ?? '') ?>">
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if (!empty($search)): ?>
        <a href="<?= site_url('admin/crm') ?>" class="btn btn-outline">Clear</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- Customer Table -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Customer Directory</div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Guest Details</th>
          <th>Contact</th>
          <th>Status</th>
          <th>Loyalty Balance</th>
          <th>Visits</th>
          <th>Lifetime Spend</th>
          <th>Dining Preferences / Notes</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($customers)): ?>
        <tr>
          <td colspan="8" class="text-center py-4 text-muted">No customer profiles found.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($customers as $c): ?>
        <tr>
          <td>
            <div class="fw-700"><?= esc($c['name']) ?></div>
            <div class="text-muted text-xs">Customer ID: #<?= (int)$c['id'] ?></div>
          </td>
          <td>
            <div><?= esc($c['phone']) ?></div>
            <div class="text-muted text-xs"><?= esc($c['email'] ?? '—') ?></div>
          </td>
          <td>
            <?php if (!empty($c['vip_status'])): ?>
              <span class="status-badge status-warning">👑 VIP Guest</span>
            <?php else: ?>
              <span class="badge badge-secondary">Regular</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="fw-700 text-warning" style="font-size: 1.05rem;">
              ⭐ <?= number_format((int)$c['loyalty_points']) ?> pts
            </div>
          </td>
          <td class="fw-600"><?= (int)$c['total_visits'] ?> visits</td>
          <td class="fw-700">₹<?= number_format((float)$c['lifetime_spend'], 2) ?></td>
          <td class="text-muted text-xs" style="max-width: 250px;">
            <?= esc($c['notes'] ?? 'None recorded') ?>
          </td>
          <td>
            <button type="button" class="btn btn-sm btn-outline" onclick="openPointsFor(<?= (int)$c['id'] ?>, '<?= esc($c['name']) ?>')">
              ⭐ Points
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Add Customer -->
<div id="addCustomerModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 500px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">➕ Register Customer Profile</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('addCustomerModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/crm/store') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-2">
          <label class="form-label">Full Name *</label>
          <input type="text" name="name" class="form-control" placeholder="e.g. Arthur Pendelton" required>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Phone Number *</label>
            <input type="text" name="phone" class="form-control" placeholder="+1 555-0921" required>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="guest@example.com">
          </div>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Welcome Bonus Points</label>
            <input type="number" name="loyalty_points" class="form-control" value="50">
          </div>
          <div class="form-group flex-1 d-flex align-center mt-3">
            <label class="d-flex align-center gap-1 cursor-pointer">
              <input type="checkbox" name="vip_status" value="1">
              <span class="fw-600">👑 VIP Regular Diner</span>
            </label>
          </div>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Guest Preferences &amp; Allergies</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Table 2 booth preference, likes sparkling water with lemon, gluten sensitive"></textarea>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('addCustomerModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Profile</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Adjust Loyalty Points -->
<div id="pointsModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 480px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">⭐ Adjust Loyalty Points</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('pointsModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/crm/adjust-points') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-2">
          <label class="form-label">Customer *</label>
          <select name="customer_id" id="points_customer_id" class="form-control" required>
            <?php foreach ($customers as $c): ?>
              <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?> (Current: <?= (int)$c['loyalty_points'] ?> pts)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Points Change (+ to reward, - to redeem) *</label>
          <input type="number" name="points_delta" class="form-control" placeholder="e.g. 100 or -50" required>
          <div class="form-hint">Enter positive value to add points, negative to redeem points.</div>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Reason / Campaign *</label>
          <input type="text" name="reason" class="form-control" placeholder="e.g. Birthday dining reward or ₹100 bill discount redemption" required>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('pointsModal')">Cancel</button>
        <button type="submit" class="btn btn-warning">Apply Points</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id) {
  const el = document.getElementById(id);
  if (el) el.style.display = 'flex';
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) el.style.display = 'none';
}
function openPointsFor(id, name) {
  const sel = document.getElementById('points_customer_id');
  if (sel) sel.value = id;
  openModal('pointsModal');
}
</script>
