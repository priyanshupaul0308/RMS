<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= esc($pageTitle ?? 'RMS') ?> – Restaurant Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/rms.css') ?>">
</head>
<body class="rms-body" data-role="<?= esc($currentRoleSlug ?? '') ?>">

<!-- ══════════════════════════════════════════════════════════════════════════ -->
<!-- SIDEBAR -->
<!-- ══════════════════════════════════════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">

  <div class="sidebar-brand">
    <div class="brand-logo">&#127860;</div>
    <div class="brand-info">
      <span class="brand-title">RMS</span>
      <span class="brand-sub">Management System</span>
    </div>
    <button class="sidebar-collapse-btn" id="sidebar-toggle" aria-label="Collapse sidebar">&#8249;</button>
  </div>

  <div class="sidebar-user">
    <div class="user-avatar"><?= esc(mb_strtoupper(mb_substr($currentUser['first_name'] ?? 'U', 0, 1))) ?></div>
    <div class="user-info">
      <span class="user-name"><?= esc(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? '')) ?></span>
      <span class="user-role badge badge-<?= esc($currentRoleSlug ?? '') ?>"><?= esc(ucfirst($currentRoleSlug ?? '')) ?></span>
    </div>
  </div>

  <nav class="sidebar-nav">

    <!-- Dashboard -->
    <div class="nav-section">
      <a href="<?= site_url('admin/dashboard') ?>" class="nav-item <?= uri_string() === 'admin/dashboard' ? 'active' : '' ?>" id="nav-dashboard" data-tooltip="Dashboard">
        <span class="nav-icon">&#9768;</span>
        <span class="nav-label">Dashboard</span>
      </a>
    </div>

    <!-- Operations -->
    <div class="nav-section">
      <div class="nav-section-title">Operations</div>

      <?php if (in_array('orders.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/orders') ?>" class="nav-item <?= (uri_string() === 'admin/orders' || strpos(uri_string(), 'admin/orders/') === 0) ? 'active' : '' ?>" id="nav-orders" data-tooltip="Live Orders">
        <span class="nav-icon">&#128203;</span>
        <span class="nav-label">Orders</span>
        <span class="nav-badge" id="live-orders-count" <?= ($liveOrdersCount ?? 0) > 0 ? '' : 'style="display: none;"' ?>><?= (int)($liveOrdersCount ?? 0) ?></span>
      </a>
      <?php endif; ?>

      <?php if (in_array('pos.access', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/pos') ?>" class="nav-item" id="nav-pos" data-tooltip="POS Terminal">
        <span class="nav-icon">&#128185;</span>
        <span class="nav-label">POS Terminal</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('kds.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/kds') ?>" class="nav-item" id="nav-kds" data-tooltip="Kitchen Display (KDS)">
        <span class="nav-icon">&#127859;</span>
        <span class="nav-label">Kitchen Display</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('floors.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/floors') ?>" class="nav-item" id="nav-floors" data-tooltip="Floor & Tables">
        <span class="nav-icon">&#128198;</span>
        <span class="nav-label">Floor & Tables</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('reservations.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/reservations') ?>" class="nav-item" id="nav-reservations" data-tooltip="Reservations">
        <span class="nav-icon">&#128197;</span>
        <span class="nav-label">Reservations</span>
      </a>
      <?php endif; ?>

      <?php if ($currentRoleSlug === 'admin' || $currentRoleSlug === 'cashier' || $currentRoleSlug === 'manager'): ?>
      <a href="<?= site_url('admin/cash-drawer') ?>" class="nav-item" id="nav-cash-drawer" data-tooltip="Cash Drawer">
        <span class="nav-icon">&#128181;</span>
        <span class="nav-label">Cash Drawer</span>
      </a>
      <a href="<?= site_url('admin/billing/transactions') ?>" class="nav-item" id="nav-billing" data-tooltip="Transactions">
        <span class="nav-icon">&#128179;</span>
        <span class="nav-label">Transactions</span>
      </a>
      <?php endif; ?>
    </div>

    <!-- Menu -->
    <div class="nav-section">
      <div class="nav-section-title">Menu & Dishes</div>

      <?php if (in_array('menu_categories.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/menu-categories') ?>" class="nav-item" id="nav-menu-cats" data-tooltip="Menu Categories">
        <span class="nav-icon">&#127857;</span>
        <span class="nav-label">Categories</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('menu_items.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/menu-items') ?>" class="nav-item" id="nav-menu-items" data-tooltip="Menu Items Catalog">
        <span class="nav-icon">&#127869;</span>
        <span class="nav-label">Menu Items</span>
      </a>
      <?php endif; ?>
    </div>

    <!-- Inventory (Phase 6) -->
    <div class="nav-section">
      <div class="nav-section-title">Inventory & Stock</div>

      <?php if (in_array('inventory.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/inventory') ?>" class="nav-item" id="nav-inventory" data-tooltip="Stock & Valuation">
        <span class="nav-icon">&#128230;</span>
        <span class="nav-label">Stock & Valuation</span>
      </a>
      <a href="<?= site_url('admin/inventory/purchase-orders') ?>" class="nav-item" id="nav-po" data-tooltip="Purchase Orders">
        <span class="nav-icon">&#128722;</span>
        <span class="nav-label">Purchase Orders</span>
      </a>
      <a href="<?= site_url('admin/inventory/suppliers') ?>" class="nav-item" id="nav-suppliers" data-tooltip="Suppliers Directory">
        <span class="nav-icon">&#128666;</span>
        <span class="nav-label">Suppliers</span>
      </a>
      <a href="<?= site_url('admin/inventory/waste') ?>" class="nav-item" id="nav-waste" data-tooltip="Wastage & Spoilage">
        <span class="nav-icon">&#9851;</span>
        <span class="nav-label">Wastage & Spoilage</span>
      </a>
      <a href="<?= site_url('admin/inventory/recipes') ?>" class="nav-item" id="nav-recipes" data-tooltip="Recipe Engineering">
        <span class="nav-icon">&#128214;</span>
        <span class="nav-label">Recipes (BOM)</span>
      </a>
      <?php endif; ?>
    </div>

    <!-- CRM (Phase 7) -->
    <div class="nav-section">
      <div class="nav-section-title">CRM & Loyalty</div>

      <?php if (in_array('customers.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/crm') ?>" class="nav-item" id="nav-crm" data-tooltip="Customers & Loyalty">
        <span class="nav-icon">&#128101;</span>
        <span class="nav-label">Customers & Loyalty</span>
      </a>
      <a href="<?= site_url('admin/crm/feedback') ?>" class="nav-item" id="nav-feedback" data-tooltip="Guest Reviews">
        <span class="nav-icon">&#11088;</span>
        <span class="nav-label">Guest Feedback</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('coupons.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/coupons') ?>" class="nav-item" id="nav-coupons" data-tooltip="Discounts & Coupons">
        <span class="nav-icon">&#127991;</span>
        <span class="nav-label">Discounts & Coupons</span>
      </a>
      <?php endif; ?>
    </div>

    <!-- Human Resources & Staff Management -->
    <?php if (in_array('attendance.view', $userPermissions ?? []) || in_array('performance.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
    <div class="nav-section">
      <div class="nav-section-title">Staff & Shifts</div>

      <?php if (in_array('attendance.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/attendance') ?>" class="nav-item" id="nav-attendance" data-tooltip="Attendance & Shifts">
        <span class="nav-icon">&#128197;</span>
        <span class="nav-label">Attendance & Shifts</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('performance.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/performance') ?>" class="nav-item" id="nav-performance" data-tooltip="Staff Performance">
        <span class="nav-icon">&#127942;</span>
        <span class="nav-label">Staff Performance</span>
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Operations, Logistics & Maintenance -->
    <?php if (in_array('delivery.view', $userPermissions ?? []) || in_array('expenses.view', $userPermissions ?? []) || in_array('maintenance.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
    <div class="nav-section">
      <div class="nav-section-title">Operations & Fleet</div>

      <?php if (in_array('delivery.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/delivery') ?>" class="nav-item" id="nav-delivery" data-tooltip="Delivery Partners">
        <span class="nav-icon">&#128692;</span>
        <span class="nav-label">Delivery Partners</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('expenses.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/expenses') ?>" class="nav-item" id="nav-expenses" data-tooltip="Expense Management">
        <span class="nav-icon">&#128181;</span>
        <span class="nav-label">Expense Management</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('maintenance.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/maintenance') ?>" class="nav-item" id="nav-maintenance" data-tooltip="Equipment Maintenance">
        <span class="nav-icon">&#128295;</span>
        <span class="nav-label">Maintenance</span>
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Administration -->
    <?php if ($currentRoleSlug === 'admin' || $currentRoleSlug === 'manager'): ?>
    <div class="nav-section">
      <div class="nav-section-title">Intelligence & Admin</div>

      <a href="<?= site_url('admin/analytics') ?>" class="nav-item" id="nav-analytics" data-tooltip="BI Analytics">
        <span class="nav-icon">&#128200;</span>
        <span class="nav-label">BI Analytics</span>
      </a>

      <?php if (in_array('accounting.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/accounting') ?>" class="nav-item" id="nav-accounting" data-tooltip="Accounting & Finance">
        <span class="nav-icon">&#128176;</span>
        <span class="nav-label">Accounting & Finance</span>
      </a>
      <?php endif; ?>

      <?php if (in_array('communication.view', $userPermissions ?? []) || ($currentRoleSlug === 'admin')): ?>
      <a href="<?= site_url('admin/communication') ?>" class="nav-item" id="nav-communication" data-tooltip="Notifications & Alerts">
        <span class="nav-icon">&#128276;</span>
        <span class="nav-label">Notifications & Comms</span>
      </a>
      <?php endif; ?>

      <a href="<?= site_url('admin/analytics/audit') ?>" class="nav-item" id="nav-audit" data-tooltip="Audit Trail">
        <span class="nav-icon">&#128270;</span>
        <span class="nav-label">Audit Trail</span>
      </a>

      <a href="<?= site_url('admin/users') ?>" class="nav-item" id="nav-users" data-tooltip="Users Management">
        <span class="nav-icon">&#128100;</span>
        <span class="nav-label">Users</span>
      </a>

      <a href="<?= site_url('admin/branches') ?>" class="nav-item" id="nav-branches" data-tooltip="Branches">
        <span class="nav-icon">&#127968;</span>
        <span class="nav-label">Branches</span>
      </a>

      <a href="<?= site_url('admin/roles') ?>" class="nav-item" id="nav-roles" data-tooltip="Roles & Permissions">
        <span class="nav-icon">&#128737;</span>
        <span class="nav-label">Roles & Permissions</span>
      </a>

      <?php if ($currentRoleSlug === 'admin'): ?>
      <a href="<?= site_url('admin/settings') ?>" class="nav-item" id="nav-settings" data-tooltip="System Settings">
        <span class="nav-icon">&#9881;</span>
        <span class="nav-label">Settings</span>
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </nav><!-- /.sidebar-nav -->

  <div class="sidebar-footer">
    <a href="<?= site_url('logout') ?>" class="nav-item nav-item-logout" id="nav-logout" data-tooltip="Sign Out">
      <span class="nav-icon">&#8594;</span>
      <span class="nav-label">Sign Out</span>
    </a>
    <div class="app-version">v<?= esc($appVersion ?? '1.0') ?></div>
  </div>

</aside><!-- /.sidebar -->

<!-- ══════════════════════════════════════════════════════════════════════════ -->
<!-- MAIN CONTENT AREA -->
<!-- ══════════════════════════════════════════════════════════════════════════ -->
<div class="main-wrapper" id="main-wrapper">

  <!-- Top Header Bar -->
  <header class="topbar" role="banner">
    <div class="topbar-left">
      <button class="topbar-toggle-btn" id="desktop-sidebar-toggle" aria-label="Toggle sidebar" title="Toggle Navigation Sidebar">&#9776;</button>
      <button class="mobile-toggle" id="mobile-sidebar-toggle" aria-label="Toggle menu">&#9776;</button>
      <div class="breadcrumb" id="page-breadcrumb">
        <span class="bc-home">&#8962;</span>
        <span class="bc-separator">/</span>
        <span class="bc-current"><?= esc($pageTitle ?? 'Dashboard') ?></span>
      </div>
    </div>

    <div class="topbar-right">
      <!-- Notification bell -->
      <div class="topbar-action" id="notif-wrapper">
        <button class="action-btn" id="notif-btn" aria-label="Notifications">
          &#128276;
          <span class="notif-badge" id="notif-count" hidden>0</span>
        </button>
        <div class="dropdown-panel notif-panel" id="notif-panel" hidden>
          <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem;">
            <span>Notifications</span>
            <button type="button" id="notif-mark-all-btn" style="background: none; border: none; color: var(--accent, #6366f1); font-size: 0.75rem; cursor: pointer; text-decoration: underline; padding: 0;">Mark all read</button>
          </div>
          <div class="panel-body" id="notif-list" style="max-height: 320px; overflow-y: auto;">
            <p class="panel-empty">No new notifications</p>
          </div>
          <div style="padding: 0.5rem 1rem; border-top: 1px solid var(--border); text-align: center; background: rgba(0,0,0,0.15);">
            <a href="<?= site_url('admin/communication') ?>" style="font-size: 0.78rem; color: var(--accent, #6366f1); text-decoration: none; font-weight: 500;">Communication &amp; Alerts &rarr;</a>
          </div>
        </div>
      </div>

      <!-- Clock & Attendance Quick Sign-In Station -->
      <div class="topbar-clock-wrapper" id="topbar-clock-wrapper" style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.03); border: 1px solid var(--border); padding: 0.2rem 0.6rem; border-radius: var(--radius-sm);">
        <div class="topbar-clock" id="live-clock" style="cursor: pointer; font-size: 0.8125rem; font-variant-numeric: tabular-nums;" title="Live Time – Click to Clock In / Out" onclick="openQuickClockModal()"></div>
        <button type="button" class="btn-topbar-clock" id="btn-topbar-clock" onclick="openQuickClockModal()" title="Shift Attendance Sign-In" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.55rem; border-radius: 12px; font-size: 0.72rem; font-weight: 600; cursor: pointer; border: 1px solid rgba(255,255,255,0.1); background: var(--surface-raised); color: var(--text-primary); transition: all 0.2s ease;">
          <span id="topbar-clock-dot" style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #94a3b8;"></span>
          <span id="topbar-clock-text">Clock In</span>
        </button>
      </div>

      <!-- User menu -->
      <div class="topbar-action" id="user-menu-wrapper">
        <button class="user-menu-btn" id="user-menu-btn" aria-label="User menu">
          <span class="user-avatar-sm"><?= esc(mb_strtoupper(mb_substr($currentUser['first_name'] ?? 'U', 0, 1))) ?></span>
          <span class="user-name-sm"><?= esc($currentUser['first_name'] ?? 'User') ?></span>
          <span class="chevron">&#8250;</span>
        </button>
        <div class="dropdown-panel user-panel" id="user-panel" hidden>
          <a href="<?= site_url('admin/users/profile') ?>" class="panel-item">&#128100; My Profile</a>
          <a href="<?= site_url('logout') ?>" class="panel-item panel-item-danger">&#8594; Sign Out</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Flash Messages -->
  <div class="flash-container" id="flash-container">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="flash flash-success" role="alert" data-auto-dismiss="5000">
        <span>&#10003;</span> <?= esc(session()->getFlashdata('success')) ?>
        <button class="flash-close" aria-label="Dismiss">&times;</button>
      </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="flash flash-error" role="alert" data-auto-dismiss="8000">
        <span>&#9888;</span> <?= esc(session()->getFlashdata('error')) ?>
        <button class="flash-close" aria-label="Dismiss">&times;</button>
      </div>
    <?php endif; ?>
  </div>

  <!-- Page Content -->
  <main class="page-content" id="page-content" role="main">
    <?= view($contentView, get_defined_vars()) ?>
  </main>

</div><!-- /.main-wrapper -->

<!-- GLOBAL MODAL: QUICK SHIFT CLOCK IN / CLOCK OUT -->
<div class="pos-modal-overlay" id="quick-clock-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 440px; text-align: center;">
    <div class="pos-modal-header" style="justify-content: space-between;">
      <div class="modal-title" style="display: flex; align-items: center; gap: 0.5rem;">
        <span>⏱️</span>
        <span>Shift Sign-In &amp; Clock</span>
      </div>
      <button type="button" class="btn-close" onclick="closeQuickClockModal()">&times;</button>
    </div>
    
    <div class="pos-modal-body" style="padding: 1.5rem 1.25rem;">
      <!-- Digital Clock Display -->
      <div style="background: rgba(0,0,0,0.35); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem 1rem; margin-bottom: 1.25rem;">
        <div id="modal-clock-time" style="font-size: 2.1rem; font-weight: 800; font-family: monospace; color: var(--accent, #6366f1); letter-spacing: 1px;">
          --:--:--
        </div>
        <div id="modal-clock-date" style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
          <?= date('l, d M Y') ?>
        </div>
      </div>

      <!-- Staff Member & Shift Info -->
      <div style="text-align: left; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.85rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
          <span class="text-muted">Staff Member:</span>
          <strong><?= esc(($currentUser['first_name'] ?? 'Staff') . ' ' . ($currentUser['last_name'] ?? '')) ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
          <span class="text-muted">Role:</span>
          <span class="badge badge-<?= esc($currentRoleSlug ?? '') ?>"><?= esc(ucfirst($currentRoleSlug ?? '')) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
          <span class="text-muted">Today's Shift:</span>
          <strong id="modal-shift-name" style="color: var(--text-primary);">Detecting...</strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span class="text-muted">Status:</span>
          <span id="modal-clock-status-badge" class="badge badge-info">Checking...</span>
        </div>
      </div>

      <!-- Action Section -->
      <div id="modal-clock-action-container">
        <!-- Button dynamically populated by JavaScript based on current status -->
      </div>
    </div>

    <div class="pos-modal-footer" style="justify-content: center; background: rgba(0,0,0,0.15);">
      <button type="button" class="btn btn-secondary btn-sm" onclick="closeQuickClockModal()" style="min-width: 100px;">
        Close
      </button>
    </div>
  </div>
</div>

<!-- CSRF data for AJAX -->
<script>
  window.RMS = {
    baseUrl:   '<?= base_url() ?>',
    csrfName:  '<?= csrf_token() ?>',
    csrfHash:  '<?= csrf_hash() ?>',
    userId:    <?= (int) ($currentUserId ?? 0) ?>,
    roleSlug:  '<?= esc($currentRoleSlug ?? '') ?>',
    branchId:  <?= (int) ($currentBranchId ?? 0) ?>,
  };
</script>
<script src="<?= base_url('assets/js/rms.js') ?>"></script>
</body>
</html>
