<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">📖 Recipe &amp; Bill of Materials (BOM)</h1>
    <p class="page-subtitle">Map menu dishes to raw ingredient portions and manage manual unit costs for accurate food costing &amp; stock deduction</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" onclick="openNewRecipeModal()">
      ➕ Map Ingredient Portion
    </button>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Dish Ingredient Mappings</div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Menu Dish</th>
          <th>Selling Price</th>
          <th>Raw Material Component</th>
          <th>Portion Required</th>
          <th>Raw Material Price (₹)</th>
          <th>Component Food Cost</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recipes)): ?>
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">No recipe mappings configured yet. Click "Map Ingredient
              Portion" to link dishes to raw ingredients.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($recipes as $r):
            $componentCost = (float) $r['quantity_required'] * (float) $r['unit_cost'];
            $costPct = (float)$r['menu_item_price'] > 0 ? round(($componentCost / (float)$r['menu_item_price']) * 100, 1) : 0;
            ?>
            <tr>
              <td>
                <div class="fw-700"><?= esc($r['menu_item_name']) ?></div>
              </td>
              <td>₹<?= number_format((float) $r['menu_item_price'], 2) ?></td>
              <td>
                <div class="fw-600"><?= esc($r['raw_item_name']) ?></div>
              </td>
              <td class="fw-700 text-primary">
                <?= number_format((float) $r['quantity_required'], 3) ?> <?= esc($r['unit_code']) ?>
              </td>
              <td>
                <div class="fw-600">₹<?= number_format((float) $r['unit_cost'], 2) ?> <span class="text-muted text-xs">/ <?= esc($r['unit_code']) ?></span></div>
              </td>
              <td>
                <div class="fw-700" style="color: #10b981;">₹<?= number_format($componentCost, 2) ?></div>
                <div class="text-xs text-muted"><?= $costPct ?>% of dish price</div>
              </td>
              <td style="text-align: right;">
                <div class="d-flex justify-end gap-1">
                  <button type="button" class="btn btn-sm btn-outline" onclick='editRecipeRow(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                    ✏️ Edit Price / Qty
                  </button>
                  <form action="<?= site_url('admin/inventory/recipes/delete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Remove this ingredient mapping?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="menu_item_id" value="<?= (int)$r['menu_item_id'] ?>">
                    <input type="hidden" name="inventory_item_id" value="<?= (int)$r['inventory_item_id'] ?>">
                    <button type="submit" class="btn btn-sm btn-ghost text-danger">
                      🗑️
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

<!-- Modal: Map Recipe -->
<div id="mapRecipeModal" class="modal-backdrop"
  style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 540px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title" id="modal-recipe-title">➕ Map Recipe Ingredient</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('mapRecipeModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/inventory/recipes/store') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-3">
          <label class="form-label" style="font-weight: 600;">Menu Dish *</label>
          <select name="menu_item_id" id="recipe-menu-id" class="form-control" required>
            <option value="">-- Choose Menu Item --</option>
            <?php foreach ($menuItems as $m): ?>
              <option value="<?= $m['id'] ?>"><?= esc($m['name']) ?> (₹<?= number_format((float) $m['price'], 2) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" style="font-weight: 600;">Raw Material Component *</label>
          <select name="inventory_item_id" id="recipe-inv-id" class="form-control" onchange="onRawIngredientChange()" required>
            <option value="">-- Choose Raw Ingredient --</option>
            <?php foreach ($rawItems as $it): ?>
              <option value="<?= $it['id'] ?>" 
                      data-cost="<?= (float)$it['unit_cost'] ?>" 
                      data-unit-id="<?= (int)$it['unit_id'] ?>" 
                      data-unit-code="<?= esc($it['unit_code']) ?>">
                <?= esc($it['name']) ?> (Current Cost: ₹<?= number_format((float) $it['unit_cost'], 2) ?> / <?= esc($it['unit_code']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row d-flex gap-2 mb-3">
          <div class="form-group flex-1">
            <label class="form-label" style="font-weight: 600;">
              Raw Material Price / Unit Cost (₹) *
              <span class="text-xs text-muted" style="font-weight: normal;">(Manual price)</span>
            </label>
            <div style="position: relative;">
              <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-weight: 600;">₹</span>
              <input type="number" step="0.01" min="0" name="unit_cost" id="recipe-unit-cost" class="form-control" style="padding-left: 26px;" placeholder="e.g. 220.00" oninput="calculateLiveCost()" required>
            </div>
            <div class="text-xs text-muted mt-1" id="cost-unit-hint">Set the purchase/market price for this ingredient.</div>
          </div>
          <div class="form-group flex-1">
            <label class="form-label" style="font-weight: 600;">Unit of Measure *</label>
            <select name="unit_id" id="recipe-unit-id" class="form-control" onchange="onUnitChange()" required>
              <?php foreach ($units as $u): ?>
                <option value="<?= $u['id'] ?>" data-code="<?= esc($u['short_code']) ?>"><?= esc($u['name']) ?> (<?= esc($u['short_code']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label" style="font-weight: 600;">Portion Quantity Required *</label>
            <input type="number" step="0.001" min="0.0001" name="quantity_required" id="recipe-qty" class="form-control" placeholder="e.g. 0.250" oninput="calculateLiveCost()" required>
          </div>
          <div class="form-group flex-1">
            <label class="form-label" style="font-weight: 600;">Component Food Cost</label>
            <div id="recipe-cost-preview" class="form-control d-flex align-center" style="background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.3); color: #10b981; font-weight: 700; height: 38px;">
              ₹0.00
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('mapRecipeModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-recipe">Save Recipe Component</button>
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

  function openNewRecipeModal() {
    document.getElementById('modal-recipe-title').textContent = '➕ Map Recipe Ingredient';
    document.getElementById('recipe-menu-id').value = '';
    document.getElementById('recipe-inv-id').value = '';
    document.getElementById('recipe-unit-cost').value = '';
    document.getElementById('recipe-qty').value = '';
    document.getElementById('recipe-cost-preview').textContent = '₹0.00';
    document.getElementById('cost-unit-hint').textContent = 'Set the purchase/market price for this ingredient.';
    openModal('mapRecipeModal');
  }

  function onRawIngredientChange() {
    const sel = document.getElementById('recipe-inv-id');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) {
      const cost = opt.getAttribute('data-cost');
      const unitId = opt.getAttribute('data-unit-id');
      const unitCode = opt.getAttribute('data-unit-code');

      // Pre-fill unit cost with default, user can freely edit
      const costInput = document.getElementById('recipe-unit-cost');
      if (cost !== null && cost !== undefined) {
        costInput.value = parseFloat(cost).toFixed(2);
      }
      if (unitId) {
        document.getElementById('recipe-unit-id').value = unitId;
      }
      if (unitCode) {
        document.getElementById('cost-unit-hint').textContent = 'Price per ' + unitCode + ' (editable manually)';
      }
    }
    calculateLiveCost();
  }

  function onUnitChange() {
    const uSel = document.getElementById('recipe-unit-id');
    const uOpt = uSel.options[uSel.selectedIndex];
    if (uOpt) {
      const code = uOpt.getAttribute('data-code');
      document.getElementById('cost-unit-hint').textContent = 'Price per ' + code + ' (editable manually)';
    }
    calculateLiveCost();
  }

  function calculateLiveCost() {
    const qty = parseFloat(document.getElementById('recipe-qty').value) || 0;
    const cost = parseFloat(document.getElementById('recipe-unit-cost').value) || 0;
    const total = qty * cost;
    document.getElementById('recipe-cost-preview').textContent = '₹' + total.toFixed(2);
  }

  function editRecipeRow(r) {
    document.getElementById('modal-recipe-title').textContent = '✏️ Edit Recipe Component';
    document.getElementById('recipe-menu-id').value = r.menu_item_id;
    document.getElementById('recipe-inv-id').value = r.inventory_item_id;
    document.getElementById('recipe-unit-cost').value = parseFloat(r.unit_cost || 0).toFixed(2);
    document.getElementById('recipe-unit-id').value = r.unit_id;
    document.getElementById('recipe-qty').value = parseFloat(r.quantity_required || 0);
    document.getElementById('cost-unit-hint').textContent = 'Price per ' + (r.unit_code || 'unit') + ' (editable manually)';
    calculateLiveCost();
    openModal('mapRecipeModal');
  }
</script>