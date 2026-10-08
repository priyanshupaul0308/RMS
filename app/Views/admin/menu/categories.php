<div class="page-header">
  <div>
    <h1 class="page-title">Menu Categories</h1>
    <p class="page-subtitle">Organize kitchen sections, appetizers, main courses, beverages, and desserts</p>
  </div>
  <div class="page-actions d-flex gap-2">
    <button type="button" class="btn btn-primary" onclick="openAddModal()">
      + Add Category
    </button>
    <a href="<?= site_url('admin/menu-items') ?>" class="btn btn-secondary">
      View All Dishes &rarr;
    </a>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Active Menu Categories (<?= count($categories) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th style="width: 50px;">Icon</th>
            <th>Category Name</th>
            <th>Slug</th>
            <th>Description</th>
            <th>Total Dishes</th>
            <th>Status</th>
            <th style="text-align: right; width: 180px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($categories)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                No categories found. Click "+ Add Category" to create one.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($categories as $cat): ?>
              <tr>
                <td style="font-size: 1.5rem; text-align: center;">
                  <?= esc($cat['icon']) ?>
                </td>
                <td class="td-bold">
                  <?= esc($cat['name']) ?>
                </td>
                <td><code><?= esc($cat['slug']) ?></code></td>
                <td><?= esc($cat['description'] ?? '—') ?></td>
                <td>
                  <span class="badge badge-primary"><?= (int)$cat['item_count'] ?> Items</span>
                </td>
                <td>
                  <span class="status-badge status-active">Active</span>
                </td>
                <td style="text-align: right;">
                  <div class="d-flex justify-end gap-1">
                    <button type="button" class="btn btn-ghost btn-sm" onclick='openEditModal(<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' title="Edit category">
                      ✏️ Edit
                    </button>
                    <a href="<?= site_url('admin/menu-items?category_id=' . $cat['id']) ?>" class="btn btn-secondary btn-sm" title="View dishes">
                      Dishes
                    </a>
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

<!-- ADD CATEGORY MODAL -->
<div class="modal-backdrop" id="addCategoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;">
  <div class="modal-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); width: 100%; max-width: 500px; padding: 1.5rem; box-shadow: var(--shadow-lg);">
    <div class="d-flex justify-between align-center mb-3">
      <h3 style="margin: 0; font-size: 1.15rem; color: var(--text-primary);">Add Menu Category</h3>
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeAddModal()" style="font-size: 1.25rem; line-height: 1;">&times;</button>
    </div>
    <form method="POST" action="<?= site_url('admin/menu-categories/store') ?>">
      <?= csrf_field() ?>
      <div class="form-group mb-2">
        <label class="form-label">Category Name *</label>
        <input type="text" name="name" class="form-control" required placeholder="e.g. Starters & Appetizers">
      </div>
      <div class="d-flex gap-2 mb-2">
        <div class="form-group" style="flex: 1;">
          <label class="form-label">Slug (Optional)</label>
          <input type="text" name="slug" class="form-control" placeholder="e.g. starters">
        </div>
        <div class="form-group" style="width: 110px;">
          <label class="form-label">Icon Emoji</label>
          <input type="text" name="icon" class="form-control text-center" value="🥗" placeholder="🥗">
        </div>
      </div>
      <div class="form-group mb-2">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2" placeholder="Brief description of items in this category"></textarea>
      </div>
      <div class="form-group mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" value="0" min="0">
      </div>
      <div class="d-flex justify-end gap-2">
        <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Category</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT CATEGORY MODAL -->
<div class="modal-backdrop" id="editCategoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;">
  <div class="modal-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); width: 100%; max-width: 500px; padding: 1.5rem; box-shadow: var(--shadow-lg);">
    <div class="d-flex justify-between align-center mb-3">
      <h3 style="margin: 0; font-size: 1.15rem; color: var(--text-primary);">Edit Menu Category</h3>
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeEditModal()" style="font-size: 1.25rem; line-height: 1;">&times;</button>
    </div>
    <form method="POST" id="editCategoryForm" action="">
      <?= csrf_field() ?>
      <div class="form-group mb-2">
        <label class="form-label">Category Name *</label>
        <input type="text" name="name" id="edit-cat-name" class="form-control" required>
      </div>
      <div class="d-flex gap-2 mb-2">
        <div class="form-group" style="flex: 1;">
          <label class="form-label">Slug</label>
          <input type="text" name="slug" id="edit-cat-slug" class="form-control">
        </div>
        <div class="form-group" style="width: 110px;">
          <label class="form-label">Icon Emoji</label>
          <input type="text" name="icon" id="edit-cat-icon" class="form-control text-center">
        </div>
      </div>
      <div class="form-group mb-2">
        <label class="form-label">Description</label>
        <textarea name="description" id="edit-cat-desc" class="form-control" rows="2"></textarea>
      </div>
      <div class="form-group mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" id="edit-cat-sort" class="form-control" min="0">
      </div>
      <div class="d-flex justify-between align-center">
        <button type="button" class="btn btn-ghost text-danger" id="deactivate-cat-btn" onclick="deactivateCategory()">
          Deactivate
        </button>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
let currentEditingId = null;

function openAddModal() {
  const modal = document.getElementById('addCategoryModal');
  modal.style.display = 'flex';
}

function closeAddModal() {
  document.getElementById('addCategoryModal').style.display = 'none';
}

function openEditModal(cat) {
  currentEditingId = cat.id;
  const form = document.getElementById('editCategoryForm');
  form.action = '<?= site_url('admin/menu-categories/update') ?>/' + cat.id;
  
  document.getElementById('edit-cat-name').value = cat.name || '';
  document.getElementById('edit-cat-slug').value = cat.slug || '';
  document.getElementById('edit-cat-icon').value = cat.icon || '🍽️';
  document.getElementById('edit-cat-desc').value = cat.description || '';
  document.getElementById('edit-cat-sort').value = cat.sort_order || 0;
  
  document.getElementById('editCategoryModal').style.display = 'flex';
}

function closeEditModal() {
  document.getElementById('editCategoryModal').style.display = 'none';
}

function deactivateCategory() {
  if (!currentEditingId) return;
  if (confirm('Are you sure you want to deactivate this category?')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= site_url('admin/menu-categories/delete') ?>/' + currentEditingId;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= csrf_token() ?>';
    csrfInput.value = '<?= csrf_hash() ?>';
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
  }
}
</script>
