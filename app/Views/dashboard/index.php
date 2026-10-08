<div class="page-header">
  <div>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Welcome back, <?= esc($currentUser['first_name'] ?? 'User') ?>. Here is your operational
      overview.</p>
  </div>
  <div class="page-actions">
    <span class="text-muted text-small" id="dash-refresh-time"></span>
    <button class="btn btn-secondary btn-sm" onclick="location.reload()">&#8635; Refresh</button>
  </div>
</div>

<!-- KPI Widgets -->
<div class="kpi-grid" id="kpi-grid">

  <div class="kpi-card primary">
    <div class="kpi-icon">&#127968;</div>
    <div class="kpi-value"><?= esc($kpis['total_branches']) ?></div>
    <div class="kpi-label">Active Branches</div>
  </div>

  <div class="kpi-card success">
    <div class="kpi-icon">&#128100;</div>
    <div class="kpi-value"><?= esc($kpis['total_users']) ?></div>
    <div class="kpi-label">System Users</div>
  </div>

  <div class="kpi-card warning">
    <div class="kpi-icon">&#128203;</div>
    <div class="kpi-value" id="kpi-orders"><?= esc($kpis['today_orders'] ?? 0) ?></div>
    <div class="kpi-label">Today&apos;s Orders</div>
  </div>

  <div class="kpi-card accent">
    <div class="kpi-icon">&#128185;</div>
    <div class="kpi-value" id="kpi-revenue">₹<?= esc($kpis['today_revenue'] ?? '0.00') ?></div>
    <div class="kpi-label">Today&apos;s Revenue</div>
  </div>

  <div class="kpi-card info">
    <div class="kpi-icon">&#128198;</div>
    <div class="kpi-value" id="kpi-tables"><?= esc($kpis['tables_occupied'] ?? 0) ?></div>
    <div class="kpi-label">Tables Occupied</div>
  </div>

  <div class="kpi-card danger">
    <div class="kpi-icon">&#128230;</div>
    <div class="kpi-value" id="kpi-lowstock">0</div>
    <div class="kpi-label">Low Stock Alerts</div>
  </div>

</div><!-- /.kpi-grid -->






<!-- Quick links grid -->
<div class="grid-3">

  <div class="card">
    <div class="card-header">
      <span class="card-title">📦 Inventory &amp; Stock (Phase 6)</span>
      <a href="<?= site_url('admin/inventory') ?>" class="btn btn-primary btn-sm">Stock View</a>
    </div>
    <div class="card-body">
      <p class="text-muted text-small">Monitor raw materials, unit costs, low-stock alerts, and wastage logs.</p>
      <div class="d-flex gap-1 mt-2">
        <a href="<?= site_url('admin/inventory/purchase-orders') ?>" class="btn btn-secondary btn-sm">Purchase
          Orders</a>
        <a href="<?= site_url('admin/inventory/recipes') ?>" class="btn btn-secondary btn-sm">Recipe BOM</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">👥 CRM &amp; Loyalty (Phase 7)</span>
      <a href="<?= site_url('admin/crm') ?>" class="btn btn-primary btn-sm">Guest Directory</a>
    </div>
    <div class="card-body">
      <p class="text-muted text-small">Manage guest profiles, lifetime spend, VIP diners, and loyalty rewards.</p>
      <a href="<?= site_url('admin/crm/feedback') ?>" class="btn btn-secondary btn-sm mt-2">⭐ View Reviews</a>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">📈 BI Analytics &amp; Audit (Phase 7)</span>
      <a href="<?= site_url('admin/analytics') ?>" class="btn btn-primary btn-sm">Executive BI</a>
    </div>
    <div class="card-body">
      <p class="text-muted text-small">Real-time revenue intelligence, tender breakdowns, audit trail, and database
        export.</p>
      <div class="d-flex gap-1 mt-2">
        <a href="<?= site_url('admin/analytics/audit') ?>" class="btn btn-secondary btn-sm">Audit Trail</a>
        <a href="<?= site_url('admin/analytics/export-backup') ?>" class="btn btn-secondary btn-sm">💾 Backup</a>
      </div>
    </div>
  </div>

  <?php if ($currentRoleSlug === 'admin' || in_array('users.view', $userPermissions)): ?>
    <div class="card">
      <div class="card-header">
        <span class="card-title">&#128100; User Management</span>
        <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary btn-sm">+ Add User</a>
      </div>
      <div class="card-body">
        <p class="text-muted text-small">Manage system users, roles, and access permissions.</p>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary btn-sm mt-2">View All Users</a>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($currentRoleSlug === 'admin' || in_array('branches.view', $userPermissions)): ?>
    <div class="card">
      <div class="card-header">
        <span class="card-title">&#127968; Branches</span>
        <a href="<?= site_url('admin/branches/create') ?>" class="btn btn-primary btn-sm">+ Add Branch</a>
      </div>
      <div class="card-body">
        <p class="text-muted text-small">Configure restaurant branches and operational settings.</p>
        <a href="<?= site_url('admin/branches') ?>" class="btn btn-secondary btn-sm mt-2">View Branches</a>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($currentRoleSlug === 'admin'): ?>
    <div class="card">
      <div class="card-header">
        <span class="card-title">&#9881; System Settings</span>
      </div>
      <div class="card-body">
        <p class="text-muted text-small">Configure taxes, payment methods, and global preferences.</p>
        <a href="<?= site_url('admin/settings') ?>" class="btn btn-secondary btn-sm mt-2">Open Settings</a>
      </div>
    </div>
  <?php endif; ?>

</div>

<script>
  document.getElementById('dash-refresh-time').textContent =
    'Last updated: ' + new Date().toLocaleTimeString('en-IN');
</script>