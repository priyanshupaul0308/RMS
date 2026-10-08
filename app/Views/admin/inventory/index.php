<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">📦 Inventory &amp; Raw Material Stock</h1>
    <p class="page-subtitle">Ingredient levels, reorder thresholds, unit costs, and inventory valuation</p>
  </div>
  <div class="d-flex gap-2">
    <button type="button" class="btn btn-outline" onclick="openModal('adjustModal')">
      ⚖️ Quick Stock Adjustment
    </button>
    <button type="button" class="btn btn-primary" onclick="openModal('addItemModal')">
      ➕ Add Raw Material
    </button>
  </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid mb-3">
  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">📦</div>
    <div class="kpi-body">
      <div class="kpi-label">Active Stock Items</div>
      <div class="kpi-value"><?= count($items) ?></div>
      <div class="kpi-trend text-muted text-xs">Tracked across all categories</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">💵</div>
    <div class="kpi-body">
      <div class="kpi-label">Total Stock Valuation</div>
      <div class="kpi-value">₹<?= number_format($totalValuation, 2) ?></div>
      <div class="kpi-trend text-muted text-xs">Based on latest purchase unit costs</div>
    </div>
  </div>

  <div class="kpi-card" style="<?= ($lowStockCount > 0) ? 'border-color: rgba(239, 68, 68, 0.4);' : '' ?>">
    <div class="kpi-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">⚠️</div>
    <div class="kpi-body">
      <div class="kpi-label">Low Stock Alerts</div>
      <div class="kpi-value" style="<?= ($lowStockCount > 0) ? 'color: #ef4444;' : '' ?>"><?= $lowStockCount ?></div>
      <div class="kpi-trend text-muted text-xs">Items below reorder threshold</div>
    </div>
  </div>
</div>

<?php if ($lowStockCount > 0): ?>
<div class="alert alert-warning mb-3 d-flex align-center justify-between">
  <div>
    <strong>⚠️ Low Stock Notice:</strong> <?= $lowStockCount ?> ingredient(s) have dropped below their minimum safety threshold and require reordering.
  </div>
  <a href="<?= site_url('admin/inventory/purchase-orders') ?>" class="btn btn-sm btn-dark">Create Purchase Order</a>
</div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="card mb-3">
  <div class="card-body p-2 d-flex justify-between align-center flex-wrap gap-2">
    <div class="d-flex align-center gap-1 flex-wrap">
      <a href="<?= site_url('admin/inventory') ?>" class="btn btn-sm <?= ($selectedCat === null && !$onlyLowStock) ? 'btn-primary' : 'btn-outline' ?>">All Categories</a>
      <?php foreach ($categories as $cat): ?>
        <a href="<?= site_url('admin/inventory?category_id=' . $cat['id']) ?>" class="btn btn-sm <?= ($selectedCat === (int)$cat['id']) ? 'btn-primary' : 'btn-outline' ?>">
          <?= esc($cat['name']) ?>
        </a>
      <?php endforeach; ?>
    </div>
    <div>
      <a href="<?= site_url('admin/inventory?low_stock=1') ?>" class="btn btn-sm <?= $onlyLowStock ? 'btn-danger' : 'btn-outline' ?>">
        🚨 Show Low Stock Only (<?= $lowStockCount ?>)
      </a>
    </div>
  </div>
</div>

<!-- Stock Table -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Live Raw Materials Inventory</div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Item &amp; SKU</th>
          <th>Category</th>
          <th>Stock Level</th>
          <th>Min Safety Threshold</th>
          <th>Unit Cost</th>
          <th>Asset Value</th>
          <th>Default Supplier</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
        <tr>
          <td colspan="8" class="text-center py-4 text-muted">No raw material items found.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($items as $item): 
          $isLow = ((float)$item['current_stock'] <= (float)$item['min_stock_level']);
          $itemValuation = (float)$item['current_stock'] * (float)$item['unit_cost'];
        ?>
        <tr style="<?= $isLow ? 'background: rgba(239, 68, 68, 0.05);' : '' ?>">
          <td>
            <div class="fw-700"><?= esc($item['name']) ?></div>
            <div class="text-muted text-xs font-mono"><?= esc($item['sku'] ?? 'N/A') ?></div>
          </td>
          <td><span class="badge badge-secondary"><?= esc($item['category_name'] ?? 'General') ?></span></td>
          <td>
            <div class="d-flex align-center gap-1">
              <span class="fw-700 <?= $isLow ? 'text-danger' : 'text-success' ?>" style="font-size: 1.05rem;">
                <?= number_format((float)$item['current_stock'], 2) ?> <?= esc($item['unit_code']) ?>
              </span>
              <?php if ($isLow): ?>
                <span class="status-badge status-danger text-xs">Low Stock</span>
              <?php endif; ?>
            </div>
          </td>
          <td><?= number_format((float)$item['min_stock_level'], 2) ?> <?= esc($item['unit_code']) ?></td>
          <td>₹<?= number_format((float)$item['unit_cost'], 2) ?></td>
          <td class="fw-600">₹<?= number_format($itemValuation, 2) ?></td>
          <td><?= esc($item['supplier_name'] ?? 'Unassigned') ?></td>
          <td>
            <div class="d-flex gap-1">
              <button type="button" class="btn btn-sm btn-outline" onclick="openEditItemModal(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)">
                ✏️ Edit Price
              </button>
              <button type="button" class="btn btn-sm btn-outline" onclick="openAdjustFor(<?= (int)$item['id'] ?>, '<?= esc($item['name']) ?>', '<?= esc($item['unit_code']) ?>')">
                ⚖️ Adjust
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

<!-- Modal: Add Item -->
<div id="addItemModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 540px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">➕ Add Raw Material Item</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('addItemModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/inventory/store-item') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-2">
          <label class="form-label">Item Name *</label>
          <input type="text" name="name" class="form-control" placeholder="e.g. Fresh Mozzarella Cheese" required>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-control">
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Unit of Measure *</label>
            <select name="unit_id" class="form-control" required>
              <?php foreach ($units as $u): ?>
                <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['short_code']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Opening Stock *</label>
            <input type="number" step="0.01" name="current_stock" class="form-control" value="10.00" required>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Min Safety Threshold *</label>
            <input type="number" step="0.01" name="min_stock_level" class="form-control" value="5.00" required>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Unit Cost (₹) *</label>
            <input type="number" step="0.01" name="unit_cost" class="form-control" value="50.00" required>
          </div>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Preferred Supplier</label>
          <select name="supplier_id" class="form-control">
            <option value="">-- None / Market Purchase --</option>
            <?php foreach ($suppliers as $s): ?>
              <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('addItemModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Item</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Adjust Stock -->
<div id="adjustModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 480px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">⚖️ Stock Adjustment</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('adjustModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/inventory/adjust') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-2">
          <label class="form-label">Select Raw Material *</label>
          <select name="item_id" id="adjust_item_id" class="form-control" required>
            <?php foreach ($items as $it): ?>
              <option value="<?= $it['id'] ?>"><?= esc($it['name']) ?> (Current: <?= number_format((float)$it['current_stock'], 2) ?> <?= esc($it['unit_code']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Adjustment Type *</label>
            <select name="type" class="form-control" required>
              <option value="in">➕ Stock In (Found / Restocked)</option>
              <option value="out">➖ Stock Out (Consumed / Discarded)</option>
              <option value="reconciliation">🔄 Reconciliation Audit</option>
            </select>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Quantity *</label>
            <input type="number" step="0.01" name="quantity" class="form-control" placeholder="e.g. 5.0" required>
          </div>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Reason / Notes *</label>
          <input type="text" name="reason" class="form-control" placeholder="e.g. Physical inventory count discrepancy" required>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('adjustModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Apply Adjustment</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Edit Raw Material Item -->
<div id="editItemModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 540px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">✏️ Edit Raw Material &amp; Price</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('editItemModal')">&times;</button>
    </div>
    <form id="editItemForm" action="" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-2">
          <label class="form-label">Item Name *</label>
          <input type="text" name="name" id="edit_item_name" class="form-control" required>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Category</label>
            <select name="category_id" id="edit_item_category" class="form-control">
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Unit of Measure *</label>
            <select name="unit_id" id="edit_item_unit" class="form-control" required>
              <?php foreach ($units as $u): ?>
                <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['short_code']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label" style="font-weight: 600;">Unit Cost / Purchase Price (₹) *</label>
            <input type="number" step="0.01" min="0" name="unit_cost" id="edit_item_cost" class="form-control" placeholder="e.g. 220.00" required>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Min Safety Threshold *</label>
            <input type="number" step="0.01" min="0" name="min_stock_level" id="edit_item_min_stock" class="form-control" required>
          </div>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Preferred Supplier</label>
          <select name="supplier_id" id="edit_item_supplier" class="form-control">
            <option value="">-- None / Market Purchase --</option>
            <?php foreach ($suppliers as $s): ?>
              <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('editItemModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Item &amp; Price</button>
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
function openAdjustFor(id, name, unit) {
  const sel = document.getElementById('adjust_item_id');
  if (sel) sel.value = id;
  openModal('adjustModal');
}
function openEditItemModal(item) {
  document.getElementById('editItemForm').action = '<?= site_url('admin/inventory/update-item') ?>/' + item.id;
  document.getElementById('edit_item_name').value = item.name || '';
  document.getElementById('edit_item_category').value = item.category_id || '';
  document.getElementById('edit_item_unit').value = item.unit_id || '';
  document.getElementById('edit_item_cost').value = parseFloat(item.unit_cost || 0).toFixed(2);
  document.getElementById('edit_item_min_stock').value = parseFloat(item.min_stock_level || 0).toFixed(2);
  document.getElementById('edit_item_supplier').value = item.supplier_id || '';
  openModal('editItemModal');
}
</script>
