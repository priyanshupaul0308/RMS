<div class="page-header">
  <div>
    <h1 class="page-title">Roles &amp; Permissions</h1>
    <p class="page-subtitle">Configure granular role-based access control and system privileges</p>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Defined System Roles (<?= count($roles) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Role Name</th>
            <th>Slug</th>
            <th>Description</th>
            <th>Type</th>
            <th style="text-align: right;">Permissions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($roles as $r): ?>
            <tr>
              <td class="td-bold">
                <span class="badge badge-<?= esc($r['slug']) ?>" style="font-size: .85rem; padding: .3rem .6rem;">
                  <?= esc($r['name']) ?>
                </span>
              </td>
              <td><code><?= esc($r['slug']) ?></code></td>
              <td><?= esc($r['description'] ?? 'Standard system role') ?></td>
              <td>
                <?php if (!empty($r['is_system'])): ?>
                  <span class="badge badge-secondary">Core System</span>
                <?php else: ?>
                  <span class="badge badge-info">Custom</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <?php if ($r['slug'] === 'admin'): ?>
                  <span class="text-xs text-muted" style="padding-right: .5rem;">Full SuperAdmin Privileges</span>
                <?php else: ?>
                  <a href="<?= site_url('admin/roles/permissions/' . $r['id']) ?>" class="btn btn-secondary btn-sm">
                    Configure Permissions &rarr;
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
