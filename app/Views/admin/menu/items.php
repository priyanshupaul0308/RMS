<div class="page-header">
  <div>
    <h1 class="page-title">Menu Items &amp; Dishes</h1>
    <p class="page-subtitle">Dish catalog, pricing, preparation times, and instant 86 (out-of-stock) controls</p>
  </div>
  <div class="page-actions d-flex gap-2">
    <button type="button" class="btn btn-primary" onclick="openAddDishModal()">
      + Add New Dish
    </button>
    <a href="<?= site_url('admin/menu-categories') ?>" class="btn btn-secondary">
      Categories
    </a>
    <a href="<?= site_url('admin/pos') ?>" class="btn btn-secondary">
      POS Terminal
    </a>
  </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success mb-3">
    <?= esc(session()->getFlashdata('success')) ?>
  </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger mb-3">
    <?= esc(session()->getFlashdata('error')) ?>
  </div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/menu-items') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div style="flex: 1; min-width: 200px;">
        <input type="text" name="q" class="form-control" placeholder="Search dish name or code..."
          value="<?= esc($search ?? '') ?>">
      </div>
      <div>
        <select name="category_id" class="form-control" onchange="this.form.submit()">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int) $cat['id'] ?>" <?= (int) ($currentCategoryId ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>>
              <?= esc($cat['icon']) ?>   <?= esc($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary">Filter</button>
      <?php if (!empty($search) || !empty($currentCategoryId)): ?>
        <a href="<?= site_url('admin/menu-items') ?>" class="btn btn-ghost text-muted">Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-between align-center">
    <div class="card-title">Dishes &amp; Beverages (<?= count($items) ?>)</div>
    <button type="button" class="btn btn-sm btn-outline" onclick="openAddDishModal()">
      + Add Dish
    </button>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Type</th>
            <th>Code</th>
            <th>Dish Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Prep Time</th>
            <th>Availability</th>
            <th style="text-align: right; width: 170px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($items)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                No menu items found. Click "+ Add New Dish" to add your first menu item.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($items as $item): ?>
              <tr>
                <td>
                  <span class="diet-indicator <?= !empty($item['is_veg']) ? 'veg' : 'non-veg' ?>"
                    title="<?= !empty($item['is_veg']) ? 'Vegetarian' : 'Non-Veg' ?>"
                    style="width: 16px; height: 16px; border: 1.5px solid <?= !empty($item['is_veg']) ? '#22c55e' : '#ef4444' ?>; display: inline-flex; align-items: center; justify-content: center; border-radius: 3px;">
                    <span
                      style="width: 6px; height: 6px; border-radius: 50%; background: <?= !empty($item['is_veg']) ? '#22c55e' : '#ef4444' ?>;"></span>
                  </span>
                </td>
                <td><code><?= esc($item['code'] ?: '—') ?></code></td>
                <td>
                  <div class="td-bold"><?= esc($item['name']) ?></div>
                  <?php if (!empty($item['description'])): ?>
                    <div class="text-xs text-muted"><?= esc($item['description']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge badge-secondary"><?= esc($item['category_name'] ?? 'General') ?></span>
                </td>
                <td class="td-bold" style="color: var(--accent, #6366f1); font-size: 1rem;">
                  ₹<?= esc(number_format((float) $item['price'], 2)) ?>
                </td>
                <td>
                  <?= (int) $item['preparation_time'] ?> mins
                </td>
                <td>
                  <?php if ((int) $item['is_available'] === 1): ?>
                    <span class="status-badge status-active">In Stock</span>
                  <?php else: ?>
                    <span class="status-badge status-danger"> Out of Stock</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <div class="d-flex justify-end gap-1">
                    <button type="button" class="btn btn-sm btn-ghost"
                      onclick='openEditDishModal(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                      title="Edit dish details">
                      ✏️ Edit
                    </button>
                    <button type="button"
                      class="btn btn-sm <?= (int) $item['is_available'] === 1 ? 'btn-ghost text-danger' : 'btn-success' ?>"
                      onclick="toggleStock(<?= (int) $item['id'] ?>, this)" title="Toggle in-stock / out-of-stock">
                      <?= (int) $item['is_available'] === 1 ? 'Mark Out' : 'Mark In' ?>
                    </button>
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

<!-- ========================================== -->
<!-- MODAL: ADD NEW DISH                        -->
<!-- ========================================== -->
<div class="modal-backdrop" id="addDishModal"
  style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div class="modal-card"
    style="background: var(--surface, #1e293b); border: 1px solid var(--border, rgba(255,255,255,0.1)); border-radius: 12px; width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);">
    <div class="d-flex justify-between align-center mb-3 pb-2"
      style="border-bottom: 1px solid var(--border, rgba(255,255,255,0.08));">
      <div>
        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-primary, #f8fafc);">Add New Menu
          Item</h3>
        <p class="text-xs text-muted" style="margin: 0.2rem 0 0 0;">Create a new dish or beverage for catalog &amp; POS
          ordering</p>
      </div>
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeAddDishModal()"
        style="font-size: 1.4rem; line-height: 1; padding: 0.2rem 0.5rem;">&times;</button>
    </div>

    <form method="POST" action="<?= site_url('admin/menu-items/store') ?>">
      <?= csrf_field() ?>

      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 500;">Dish Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required
          placeholder="e.g. Paneer Butter Masala, Cold Brew Coffee">
      </div>

      <div class="d-flex gap-2 mb-3">
        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Category <span class="text-danger">*</span></label>
          <select name="category_id" class="form-control" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int) $cat['id'] ?>" <?= ((int) ($currentCategoryId ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>>
                <?= esc($cat['icon']) ?>   <?= esc($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group" style="width: 140px;">
          <label class="form-label" style="font-weight: 500;">Item Code</label>
          <input type="text" name="code" class="form-control" placeholder="e.g. MC-06 (Auto)">
        </div>
      </div>

      <div class="d-flex gap-2 mb-3">
        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Selling Price (₹) <span
              class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0" name="price" class="form-control" required placeholder="e.g. 299.00">
        </div>

        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Dietary Type <span class="text-danger">*</span></label>
          <select name="is_veg" class="form-control" required>
            <option value="1">🟢 Vegetarian</option>
            <option value="0">🔴 Non-Vegetarian</option>
          </select>
        </div>
      </div>

      <div class="d-flex gap-2 mb-3">
        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Prep Time (Mins)</label>
          <input type="number" name="preparation_time" class="form-control" value="15" min="1" max="180">
        </div>

        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Tax Rate</label>
          <select name="tax_rate_id" class="form-control">
            <option value="">Standard / None</option>
            <?php if (!empty($taxRates)): ?>
              <?php foreach ($taxRates as $tr): ?>
                <option value="<?= (int) $tr['id'] ?>">
                  <?= esc($tr['name']) ?> (<?= esc($tr['rate']) ?>%)
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div class="form-group" style="width: 100px;">
          <label class="form-label" style="font-weight: 500;">Sort Order</label>
          <input type="number" name="sort_order" class="form-control" value="0" min="0">
        </div>
      </div>

      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 500;">Description / Ingredients</label>
        <textarea name="description" class="form-control" rows="2"
          placeholder="Tender paneer cubes cooked in rich tomato and cashew gravy..."></textarea>
      </div>

      <div class="form-group mb-4">
        <label class="d-flex align-center gap-2" style="cursor: pointer; user-select: none;">
          <input type="checkbox" name="is_available" value="1" checked
            style="width: 18px; height: 18px; accent-color: var(--accent, #6366f1);">
          <span style="font-weight: 500; font-size: 0.95rem;">Immediately available for ordering (In Stock)</span>
        </label>
      </div>

      <div class="d-flex justify-end gap-2 pt-2" style="border-top: 1px solid var(--border, rgba(255,255,255,0.08));">
        <button type="button" class="btn btn-secondary" onclick="closeAddDishModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">+ Create Dish</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT DISH                          -->
<!-- ========================================== -->
<div class="modal-backdrop" id="editDishModal"
  style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div class="modal-card"
    style="background: var(--surface, #1e293b); border: 1px solid var(--border, rgba(255,255,255,0.1)); border-radius: 12px; width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);">
    <div class="d-flex justify-between align-center mb-3 pb-2"
      style="border-bottom: 1px solid var(--border, rgba(255,255,255,0.08));">
      <div>
        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-primary, #f8fafc);">Edit Dish
          Details</h3>
        <p class="text-xs text-muted" style="margin: 0.2rem 0 0 0;">Update dish information, pricing, or availability
        </p>
      </div>
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeEditDishModal()"
        style="font-size: 1.4rem; line-height: 1; padding: 0.2rem 0.5rem;">&times;</button>
    </div>

    <form method="POST" id="editDishForm" action="">
      <?= csrf_field() ?>

      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 500;">Dish Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="edit-dish-name" class="form-control" required>
      </div>

      <div class="d-flex gap-2 mb-3">
        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Category <span class="text-danger">*</span></label>
          <select name="category_id" id="edit-dish-category" class="form-control" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int) $cat['id'] ?>">
                <?= esc($cat['icon']) ?>   <?= esc($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group" style="width: 140px;">
          <label class="form-label" style="font-weight: 500;">Item Code</label>
          <input type="text" name="code" id="edit-dish-code" class="form-control">
        </div>
      </div>

      <div class="d-flex gap-2 mb-3">
        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Selling Price (₹) <span
              class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0" name="price" id="edit-dish-price" class="form-control" required>
        </div>

        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Dietary Type <span class="text-danger">*</span></label>
          <select name="is_veg" id="edit-dish-veg" class="form-control" required>
            <option value="1">🟢 Vegetarian</option>
            <option value="0">🔴 Non-Vegetarian</option>
          </select>
        </div>
      </div>

      <div class="d-flex gap-2 mb-3">
        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Prep Time (Mins)</label>
          <input type="number" name="preparation_time" id="edit-dish-preptime" class="form-control" min="1" max="180">
        </div>

        <div class="form-group" style="flex: 1;">
          <label class="form-label" style="font-weight: 500;">Tax Rate</label>
          <select name="tax_rate_id" id="edit-dish-tax" class="form-control">
            <option value="">Standard / None</option>
            <?php if (!empty($taxRates)): ?>
              <?php foreach ($taxRates as $tr): ?>
                <option value="<?= (int) $tr['id'] ?>">
                  <?= esc($tr['name']) ?> (<?= esc($tr['rate']) ?>%)
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div class="form-group" style="width: 100px;">
          <label class="form-label" style="font-weight: 500;">Sort Order</label>
          <input type="number" name="sort_order" id="edit-dish-sort" class="form-control" min="0">
        </div>
      </div>

      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 500;">Description / Ingredients</label>
        <textarea name="description" id="edit-dish-desc" class="form-control" rows="2"></textarea>
      </div>

      <div class="form-group mb-4">
        <label class="d-flex align-center gap-2" style="cursor: pointer; user-select: none;">
          <input type="checkbox" name="is_available" id="edit-dish-available" value="1"
            style="width: 18px; height: 18px; accent-color: var(--accent, #6366f1);">
          <span style="font-weight: 500; font-size: 0.95rem;">Available for ordering (In Stock)</span>
        </label>
      </div>

      <div class="d-flex justify-between align-center pt-2"
        style="border-top: 1px solid var(--border, rgba(255,255,255,0.08));">
        <button type="button" class="btn btn-ghost text-danger" onclick="deleteCurrentDish()">
          🗑️ Remove Dish
        </button>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-secondary" onclick="closeEditDishModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  let currentEditingDishId = null;

  function openAddDishModal() {
    const modal = document.getElementById('addDishModal');
    if (modal) {
      modal.style.display = 'flex';
    }
  }

  function closeAddDishModal() {
    const modal = document.getElementById('addDishModal');
    if (modal) {
      modal.style.display = 'none';
    }
  }

  function openEditDishModal(item) {
    currentEditingDishId = item.id;
    const form = document.getElementById('editDishForm');
    form.action = '<?= site_url('admin/menu-items/update') ?>/' + item.id;

    document.getElementById('edit-dish-name').value = item.name || '';
    document.getElementById('edit-dish-category').value = item.category_id || '';
    document.getElementById('edit-dish-code').value = item.code || '';
    document.getElementById('edit-dish-price').value = item.price || '';
    document.getElementById('edit-dish-veg').value = (item.is_veg !== undefined && item.is_veg !== null) ? item.is_veg : 1;
    document.getElementById('edit-dish-preptime').value = item.preparation_time || 15;
    document.getElementById('edit-dish-tax').value = item.tax_rate_id || '';
    document.getElementById('edit-dish-sort').value = item.sort_order || 0;
    document.getElementById('edit-dish-desc').value = item.description || '';
    document.getElementById('edit-dish-available').checked = parseInt(item.is_available) === 1;

    const modal = document.getElementById('editDishModal');
    if (modal) {
      modal.style.display = 'flex';
    }
  }

  function closeEditDishModal() {
    const modal = document.getElementById('editDishModal');
    if (modal) {
      modal.style.display = 'none';
    }
  }

  function deleteCurrentDish() {
    if (!currentEditingDishId) return;
    if (!confirm('Are you sure you want to remove this dish from the active catalog?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= site_url('admin/menu-items/delete') ?>/' + currentEditingDishId;

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '<?= csrf_token() ?>';
    csrf.value = '<?= csrf_hash() ?>';
    form.appendChild(csrf);

    document.body.appendChild(form);
    form.submit();
  }

  function toggleStock(itemId, btn) {
    btn.disabled = true;
    window.rmsPost('<?= site_url('admin/menu-items/toggle') ?>/' + itemId, {})
      .then(function (res) {
        if (res.success) {
          location.reload();
        } else {
          alert(res.message || 'Error toggling availability');
          btn.disabled = false;
        }
      })
      .catch(function (err) {
        alert('Request failed');
        btn.disabled = false;
      });
  }

  // Close modals when clicking backdrop
  window.addEventListener('click', function (e) {
    const addModal = document.getElementById('addDishModal');
    const editModal = document.getElementById('editDishModal');
    if (e.target === addModal) closeAddDishModal();
    if (e.target === editModal) closeEditDishModal();
  });
</script>