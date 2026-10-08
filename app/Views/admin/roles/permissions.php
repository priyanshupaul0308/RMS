<div class="page-header">
  <div>
    <h1 class="page-title"><?= esc($pageTitle) ?></h1>
    <p class="page-subtitle">Configure module permissions for <strong><?= esc($role['name']) ?></strong></p>
  </div>
  <div class="page-actions d-flex gap-1 align-center">
    <a href="<?= site_url('admin/roles') ?>" class="btn btn-secondary">&larr; Back to Roles</a>
    <button type="button" class="btn btn-ghost btn-sm" onclick="selectAllPermissions(true)">Select All</button>
    <button type="button" class="btn btn-ghost btn-sm" onclick="selectAllPermissions(false)">Deselect All</button>
    <button type="button" class="btn btn-primary btn-save-perm" onclick="savePermissions()">
      Save Permissions
    </button>
  </div>
</div>

<div id="flash-container"></div>

<form id="permissions-form">
  <?= csrf_field() ?>

  <div class="grid-2">
    <?php foreach ($permissionMatrix as $module => $perms): ?>
      <div class="card mb-2">
        <div class="card-header d-flex justify-between align-center">
          <div class="card-title text-capitalize">
            <?= esc(str_replace('_', ' ', $module)) ?> Module
          </div>
          <button type="button" class="btn btn-ghost btn-sm" onclick="toggleModuleGroup('<?= esc($module) ?>')">
            Toggle Module
          </button>
        </div>
        <div class="card-body">
          <div class="d-flex flex-column gap-1">
            <?php foreach ($perms as $p): ?>
              <?php $checked = in_array((int)$p['id'], $rolePermissions ?? [], true); ?>
              <label class="d-flex align-center gap-1" style="cursor: pointer; padding: .35rem 0;">
                <input type="checkbox" 
                       name="permissions[]" 
                       value="<?= (int)$p['id'] ?>" 
                       class="perm-checkbox perm-group-<?= esc($module) ?>"
                       <?= $checked ? 'checked' : '' ?>
                       style="width: 18px; height: 18px; accent-color: var(--primary);">
                <div>
                  <div class="text-small td-bold"><?= esc(ucwords(str_replace(['_', '.'], ' ', $p['action'] ?? $p['slug']))) ?></div>
                  <div class="text-xs text-muted"><code><?= esc($p['slug']) ?></code> &bull; <?= esc($p['description'] ?? '') ?></div>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-3 mb-4 d-flex gap-2 align-center">
    <button type="button" class="btn btn-primary btn-lg btn-save-perm" onclick="savePermissions()">
      Save Permissions
    </button>
    <a href="<?= site_url('admin/roles') ?>" class="btn btn-secondary btn-lg">Cancel</a>
  </div>
</form>

<script>
function toggleModuleGroup(module) {
  var boxes = document.querySelectorAll('.perm-group-' + module);
  var anyUnchecked = Array.from(boxes).some(function(b) { return !b.checked; });
  boxes.forEach(function(b) { b.checked = anyUnchecked; });
}

function selectAllPermissions(check) {
  document.querySelectorAll('input[name="permissions[]"]').forEach(function(b) {
    b.checked = check;
  });
}

function savePermissions() {
  var saveBtns = document.querySelectorAll('.btn-save-perm');
  saveBtns.forEach(function(btn) {
    btn.disabled = true;
    btn.textContent = 'Saving...';
  });

  var checked = [];
  document.querySelectorAll('input[name="permissions[]"]:checked').forEach(function(b) {
    checked.push(parseInt(b.value, 10));
  });

  window.rmsPost('<?= site_url('admin/roles/permissions/' . $role['id']) ?>', {
    permissions: checked
  }).then(function(res) {
    saveBtns.forEach(function(btn) {
      btn.disabled = false;
      btn.textContent = 'Save Permissions';
    });

    if (res.status === 'success' || res.success) {
      var msg = res.message || 'Permissions saved successfully! (' + checked.length + ' permissions assigned)';
      if (typeof window.showFlash === 'function') {
        window.showFlash('success', msg);
      }
      alert(msg);
    } else {
      var errMsg = res.message || (res.errors ? JSON.stringify(res.errors) : 'Error saving permissions');
      if (typeof window.showFlash === 'function') {
        window.showFlash('error', errMsg);
      }
      alert(errMsg);
    }
  }).catch(function(err) {
    saveBtns.forEach(function(btn) {
      btn.disabled = false;
      btn.textContent = 'Save Permissions';
    });
    alert('An unexpected error occurred while saving: ' + (err.message || err));
  });
}
</script>
