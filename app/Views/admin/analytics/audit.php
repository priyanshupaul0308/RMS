<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">🔍 System Audit Trail &amp; Security Logs</h1>
    <p class="page-subtitle">Immutable chronological ledger of every mutation, financial action, and user authentication event</p>
  </div>
</div>

<!-- Filter Bar -->
<div class="card mb-3">
  <div class="card-body p-2">
    <form action="<?= site_url('admin/analytics/audit') ?>" method="GET" class="d-flex gap-2 flex-wrap align-center">
      <div class="flex-1">
        <select name="module" class="form-control">
          <option value="">-- All Modules --</option>
          <?php foreach ($modules as $m): ?>
            <option value="<?= esc($m['mod_name']) ?>" <?= ($selectedModule === $m['mod_name']) ? 'selected' : '' ?>>
              <?= ucfirst(esc($m['mod_name'])) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex-1">
        <select name="user_id" class="form-control">
          <option value="">-- All Staff Members --</option>
          <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>" <?= ($selectedUser === (int)$u['id']) ? 'selected' : '' ?>>
              <?= esc($u['first_name'] . ' ' . $u['last_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter Logs</button>
      <?php if ($selectedModule || $selectedUser): ?>
        <a href="<?= site_url('admin/analytics/audit') ?>" class="btn btn-outline">Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Audit Activity Stream</div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Timestamp</th>
          <th>Staff Member</th>
          <th>Module</th>
          <th>Action</th>
          <th>Description</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($logs)): ?>
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">No audit trail records found.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($logs as $l): ?>
        <tr>
          <td class="font-mono text-xs"><?= esc(date('M d, Y H:i:s', strtotime($l['created_at']))) ?></td>
          <td>
            <div class="fw-700"><?= esc(trim(($l['first_name'] ?? '') . ' ' . ($l['last_name'] ?? 'System'))) ?></div>
            <div class="text-muted text-xs"><?= esc($l['role_name'] ?? 'Automated') ?></div>
          </td>
          <td><span class="badge badge-secondary font-mono"><?= strtoupper(esc($l['module'])) ?></span></td>
          <td><span class="fw-600"><?= esc($l['action']) ?></span></td>
          <td class="text-small" style="max-width: 380px;"><?= esc($l['description']) ?></td>
          <td class="font-mono text-xs text-muted"><?= esc($l['ip_address'] ?? '127.0.0.1') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
