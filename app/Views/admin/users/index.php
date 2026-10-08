<div class="page-header">
  <div>
    <h1 class="page-title">User Management</h1>
    <p class="page-subtitle">Manage system staff, branch assignments, and roles</p>
  </div>
  <div class="page-actions">
    <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary" id="btn-create-user">
      <span>+</span> Add New User
    </a>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Staff Members (<?= count($users) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Role</th>
            <th>Branch</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Last Login</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                No users found. Click &ldquo;Add New User&rdquo; to create your first staff member.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <tr>
                <td>
                  <div class="d-flex align-center gap-1">
                    <div class="user-avatar-sm" style="font-weight:600;">
                      <?= esc(mb_strtoupper(mb_substr($u['first_name'] ?? 'U', 0, 1))) ?>
                    </div>
                    <div>
                      <div class="td-bold"><?= esc(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?></div>
                      <div class="text-xs text-muted"><?= esc($u['email']) ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge badge-<?= esc($u['role_slug'] ?? 'secondary') ?>">
                    <?= esc($u['role_name'] ?? 'N/A') ?>
                  </span>
                </td>
                <td>
                  <?= esc($u['branch_name'] ?? 'All Branches / Head Office') ?>
                </td>
                <td>
                  <?= esc($u['phone'] ?: '—') ?>
                </td>
                <td>
                  <?php if ((int)$u['is_active'] === 1): ?>
                    <span class="status-badge status-active">Active</span>
                  <?php else: ?>
                    <span class="status-badge status-inactive">Inactive</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="text-xs text-secondary">
                    <?= !empty($u['last_login_at']) ? esc(date('M d, Y H:i', strtotime($u['last_login_at']))) : 'Never' ?>
                  </span>
                </td>
                <td style="text-align: right;">
                  <div class="td-actions" style="justify-content: flex-end;">
                    <a href="<?= site_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-secondary btn-sm" title="Edit User">
                      Edit
                    </a>
                    <?php if ((int)$u['id'] !== (int)$currentUserId): ?>
                      <button type="button" 
                              class="btn btn-ghost btn-sm <?= (int)$u['is_active'] === 1 ? 'text-danger' : 'text-success' ?>" 
                              onclick="toggleUserStatus(<?= (int)$u['id'] ?>, this)"
                              title="<?= (int)$u['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>">
                        <?= (int)$u['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                      </button>
                    <?php endif; ?>
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

<script>
function toggleUserStatus(userId, btn) {
  if (!confirm('Are you sure you want to change this user\'s active status?')) return;
  btn.disabled = true;
  window.rmsPost('<?= site_url('admin/users/toggle') ?>/' + userId, {})
    .then(function(res) {
      if (res.success) {
        location.reload();
      } else {
        alert(res.message || 'Error toggling user status');
        btn.disabled = false;
      }
    })
    .catch(function(err) {
      alert('Request failed');
      btn.disabled = false;
    });
}
</script>
