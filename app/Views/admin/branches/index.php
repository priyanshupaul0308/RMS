<div class="page-header">
  <div>
    <h1 class="page-title">Branch Management</h1>
    <p class="page-subtitle">Configure multi-branch locations, contact info, and tax profiles</p>
  </div>
  <div class="page-actions">
    <a href="<?= site_url('admin/branches/create') ?>" class="btn btn-primary">
      <span>+</span> Add Branch
    </a>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Restaurant Locations (<?= count($branches) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Branch Name</th>
            <th>Code</th>
            <th>Contact</th>
            <th>Location</th>
            <th>Status</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($branches)): ?>
            <tr>
              <td colspan="6" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                No branches registered yet. Click &ldquo;Add Branch&rdquo; to set up your primary outlet.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($branches as $b): ?>
              <tr>
                <td class="td-bold">
                  <div class="d-flex align-center gap-1">
                    <span style="font-size: 1.25rem;">&#127968;</span>
                    <div>
                      <div><?= esc($b['name']) ?></div>
                      <div class="text-xs text-muted"><?= esc($b['address'] ?? '') ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <code><?= esc($b['code'] ?? '—') ?></code>
                </td>
                <td>
                  <div class="text-small"><?= esc($b['phone'] ?? '—') ?></div>
                  <div class="text-xs text-muted"><?= esc($b['email'] ?? '') ?></div>
                </td>
                <td>
                  <?= esc(trim(($b['city'] ?? '') . ' ' . ($b['state'] ?? ''))) ?: '—' ?>
                </td>
                <td>
                  <?php if ((int)$b['is_active'] === 1): ?>
                    <span class="status-badge status-active">Operational</span>
                  <?php else: ?>
                    <span class="status-badge status-inactive">Closed</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <div class="td-actions" style="justify-content: flex-end;">
                    <a href="<?= site_url('admin/branches/edit/' . $b['id']) ?>" class="btn btn-secondary btn-sm">
                      Edit
                    </a>
                    <form action="<?= site_url('admin/branches/toggle/' . $b['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Toggle operational status for this branch?');">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-ghost btn-sm <?= (int)$b['is_active'] === 1 ? 'text-danger' : 'text-success' ?>">
                        <?= (int)$b['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                      </button>
                    </form>
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
