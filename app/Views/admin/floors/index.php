<div class="page-header d-flex justify-between align-center mb-3">
  <div>
    <h1 class="page-title">Floor Plan &amp; Dining Tables</h1>
    <p class="page-subtitle">Real-time table occupancy status, seating capacity, and floor management</p>
  </div>
  <div class="page-actions d-flex gap-2">
    <button type="button" class="btn btn-primary d-flex align-center gap-1" onclick="openAddTableModal()">
      <span>+</span> Add Table
    </button>
    <a href="<?= site_url('admin/pos') ?>" class="btn btn-outline d-flex align-center gap-1">
      Open POS Terminal
    </a>
  </div>
</div>

<div class="d-flex align-center gap-2 mb-3">
  <div class="d-flex align-center gap-1">
    <span class="legend-dot status-available" style="width:12px; height:12px; border-radius:50%; background:var(--success); display:inline-block;"></span>
    <span class="text-small">Available</span>
  </div>
  <div class="d-flex align-center gap-1">
    <span class="legend-dot status-occupied" style="width:12px; height:12px; border-radius:50%; background:var(--danger); display:inline-block;"></span>
    <span class="text-small">Occupied</span>
  </div>
  <div class="d-flex align-center gap-1">
    <span class="legend-dot status-reserved" style="width:12px; height:12px; border-radius:50%; background:var(--warning); display:inline-block;"></span>
    <span class="text-small">Reserved</span>
  </div>
  <div class="d-flex align-center gap-1">
    <span class="legend-dot status-dirty" style="width:12px; height:12px; border-radius:50%; background:var(--text-muted); display:inline-block;"></span>
    <span class="text-small">Dirty / Cleanup</span>
  </div>
</div>

<?php foreach ($floors as $floor): ?>
  <div class="card mb-3">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title d-flex align-center gap-2">
        <span>📍 <?= esc($floor['name']) ?> (Floor <?= (int)$floor['floor_number'] ?>)</span>
        <span class="badge badge-secondary"><?= count($floor['tables'] ?? []) ?> Tables</span>
      </div>
      <button type="button" class="btn btn-outline btn-xs d-flex align-center gap-1" onclick="openAddTableModal(<?= (int)$floor['id'] ?>)">
        <span>+</span> Add Table
      </button>
    </div>
    <div class="card-body">
      <?php if (empty($floor['tables'])): ?>
        <div class="text-center text-muted py-4" style="padding: 2rem 1rem;">
          <p class="mb-2">No tables added to this floor yet.</p>
          <button type="button" class="btn btn-primary btn-sm" onclick="openAddTableModal(<?= (int)$floor['id'] ?>)">
            + Add First Table
          </button>
        </div>
      <?php else: ?>
        <div class="grid-4" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
          <?php foreach ($floor['tables'] as $tbl): ?>
            <div class="card table-card" style="position: relative; border: 2px solid <?= $tbl['status'] === 'available' ? 'var(--success)' : ($tbl['status'] === 'occupied' ? 'var(--danger)' : ($tbl['status'] === 'reserved' ? 'var(--warning)' : 'var(--border)')) ?>; padding: 1rem; text-align: center; border-radius: 12px; background: var(--surface);">
              
              <!-- Top Card Actions: Edit & Remove -->
              <div style="position: absolute; top: 8px; right: 8px; display: flex; gap: 4px;">
                <button type="button" 
                        class="btn-icon-subtle" 
                        onclick='openEditTableModal(<?= json_encode($tbl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                        title="Edit Table">
                  ✏️
                </button>
                <?php if ($tbl['status'] !== 'occupied' && empty($tbl['current_order_id'])): ?>
                  <button type="button" 
                          class="btn-icon-subtle btn-icon-danger" 
                          onclick="confirmDeleteTable(<?= (int)$tbl['id'] ?>, '<?= esc($tbl['table_number'], 'js') ?>')" 
                          title="Remove Table">
                    🗑️
                  </button>
                <?php else: ?>
                  <span class="btn-icon-subtle disabled" title="Cannot remove occupied table" style="opacity: 0.35; cursor: not-allowed;">
                    🔒
                  </span>
                <?php endif; ?>
              </div>

              <!-- Table Number & Shape -->
              <div style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">
                <?= esc($tbl['table_number']) ?>
              </div>
              <div class="text-xs text-muted mt-1">
                👥 <?= (int)$tbl['seating_capacity'] ?> Seats &bull; <?= esc(ucfirst($tbl['shape'])) ?>
              </div>
              
              <!-- Status Badge -->
              <div class="mt-2">
                <span class="status-badge status-<?= esc($tbl['status']) ?>">
                  <?= esc(ucfirst($tbl['status'])) ?>
                </span>
              </div>

              <?php if (!empty($tbl['final_total'])): ?>
                <div class="mt-2 text-small fw-bold" style="color: var(--accent);">
                  Running: ₹<?= esc(number_format((float)$tbl['final_total'], 2)) ?>
                </div>
              <?php endif; ?>

              <!-- Status Action Buttons -->
              <div class="mt-2 d-flex justify-center gap-1" style="justify-content: center;">
                <?php if ($tbl['status'] === 'dirty'): ?>
                  <button type="button" class="btn btn-success btn-xs" onclick="setTableState(<?= (int)$tbl['id'] ?>, 'available')">
                    ✨ Mark Clean
                  </button>
                <?php elseif ($tbl['status'] === 'available'): ?>
                  <button type="button" class="btn btn-warning btn-xs" onclick="setTableState(<?= (int)$tbl['id'] ?>, 'reserved')">
                    Book Table
                  </button>
                <?php elseif ($tbl['status'] === 'reserved'): ?>
                  <button type="button" class="btn btn-secondary btn-xs" onclick="setTableState(<?= (int)$tbl['id'] ?>, 'available')">
                    Cancel Booking
                  </button>
                <?php elseif ($tbl['status'] === 'occupied'): ?>
                  <span class="text-xs text-danger" style="font-weight: 600;">Currently In Dining</span>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

<!-- ========================================== -->
<!-- MODAL: ADD NEW TABLE                      -->
<!-- ========================================== -->
<div class="modal-backdrop" id="addTableModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div class="modal-card" style="background: var(--surface, #1e293b); border: 1px solid var(--border, rgba(255,255,255,0.1)); border-radius: 12px; width: 100%; max-width: 450px; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);">
    <div class="d-flex justify-between align-center mb-3 pb-2" style="border-bottom: 1px solid var(--border, rgba(255,255,255,0.08));">
      <div>
        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-primary);">➕ Add Dining Table</h3>
        <p class="text-xs text-muted" style="margin: 0.2rem 0 0 0;">Add a new table to the restaurant floor plan</p>
      </div>
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('addTableModal')" style="font-size: 1.4rem; line-height: 1;">&times;</button>
    </div>

    <form method="POST" action="<?= site_url('admin/floors/table/store') ?>">
      <?= csrf_field() ?>

      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 600;">Floor Section *</label>
        <select name="floor_id" id="add-table-floor-id" class="form-control" required>
          <?php foreach ($floors as $f): ?>
            <option value="<?= (int)$f['id'] ?>">📍 <?= esc($f['name']) ?> (Floor <?= (int)$f['floor_number'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 600;">Table Number / Name *</label>
        <input type="text" name="table_number" id="add-table-number" class="form-control" placeholder="e.g. T-07, R-05, VIP-3" required maxlength="50">
        <div class="text-xs text-muted mt-1">Unique identifier shown on POS and KDS</div>
      </div>

      <div class="form-row d-flex gap-2 mb-3">
        <div class="form-group flex-1">
          <label class="form-label" style="font-weight: 600;">Seating Capacity *</label>
          <input type="number" name="seating_capacity" id="add-table-capacity" class="form-control" value="4" min="1" max="50" required>
        </div>
        <div class="form-group flex-1">
          <label class="form-label" style="font-weight: 600;">Table Shape</label>
          <select name="shape" id="add-table-shape" class="form-control">
            <option value="square">Square</option>
            <option value="rectangle">Rectangle</option>
            <option value="round">Round</option>
          </select>
        </div>
      </div>

      <div class="d-flex justify-end gap-2 pt-2" style="border-top: 1px solid var(--border, rgba(255,255,255,0.08));">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addTableModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" style="font-weight: 600;">Save Table</button>
      </div>
    </form>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT TABLE                         -->
<!-- ========================================== -->
<div class="modal-backdrop" id="editTableModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div class="modal-card" style="background: var(--surface, #1e293b); border: 1px solid var(--border, rgba(255,255,255,0.1)); border-radius: 12px; width: 100%; max-width: 450px; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);">
    <div class="d-flex justify-between align-center mb-3 pb-2" style="border-bottom: 1px solid var(--border, rgba(255,255,255,0.08));">
      <div>
        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; color: var(--text-primary);">✏️ Edit Dining Table</h3>
        <p class="text-xs text-muted" style="margin: 0.2rem 0 0 0;">Update table details and layout properties</p>
      </div>
      <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('editTableModal')" style="font-size: 1.4rem; line-height: 1;">&times;</button>
    </div>

    <form method="POST" id="editTableForm" action="">
      <?= csrf_field() ?>

      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 600;">Floor Section *</label>
        <select name="floor_id" id="edit-table-floor-id" class="form-control" required>
          <?php foreach ($floors as $f): ?>
            <option value="<?= (int)$f['id'] ?>">📍 <?= esc($f['name']) ?> (Floor <?= (int)$f['floor_number'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group mb-3">
        <label class="form-label" style="font-weight: 600;">Table Number / Name *</label>
        <input type="text" name="table_number" id="edit-table-number" class="form-control" required maxlength="50">
      </div>

      <div class="form-row d-flex gap-2 mb-3">
        <div class="form-group flex-1">
          <label class="form-label" style="font-weight: 600;">Seating Capacity *</label>
          <input type="number" name="seating_capacity" id="edit-table-capacity" class="form-control" min="1" max="50" required>
        </div>
        <div class="form-group flex-1">
          <label class="form-label" style="font-weight: 600;">Table Shape</label>
          <select name="shape" id="edit-table-shape" class="form-control">
            <option value="square">Square</option>
            <option value="rectangle">Rectangle</option>
            <option value="round">Round</option>
          </select>
        </div>
      </div>

      <div class="d-flex justify-end gap-2 pt-2" style="border-top: 1px solid var(--border, rgba(255,255,255,0.08));">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editTableModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" style="font-weight: 600;">Update Table</button>
      </div>
    </form>
  </div>
</div>

<!-- HIDDEN DELETE FORM -->
<form id="deleteTableForm" method="POST" action="" style="display: none;">
  <?= csrf_field() ?>
</form>

<style>
.btn-icon-subtle {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: var(--text-secondary);
  border-radius: 6px;
  width: 28px;
  height: 28px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.82rem;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-icon-subtle:hover {
  background: rgba(255, 255, 255, 0.15);
  color: #fff;
}

.btn-icon-danger:hover {
  background: rgba(239, 68, 68, 0.2);
  border-color: #ef4444;
}

.table-card:hover {
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  transform: translateY(-2px);
  transition: all 0.2s ease;
}
</style>

<script>
function openModal(id) {
  const m = document.getElementById(id);
  if (m) m.style.display = 'flex';
}

function closeModal(id) {
  const m = document.getElementById(id);
  if (m) m.style.display = 'none';
}

function openAddTableModal(floorId) {
  if (floorId) {
    const sel = document.getElementById('add-table-floor-id');
    if (sel) sel.value = floorId;
  }
  document.getElementById('add-table-number').value = '';
  document.getElementById('add-table-capacity').value = '4';
  openModal('addTableModal');
}

function openEditTableModal(tbl) {
  document.getElementById('editTableForm').action = '<?= site_url('admin/floors/table/update') ?>/' + tbl.id;
  document.getElementById('edit-table-floor-id').value = tbl.floor_id;
  document.getElementById('edit-table-number').value = tbl.table_number;
  document.getElementById('edit-table-capacity').value = tbl.seating_capacity;
  document.getElementById('edit-table-shape').value = tbl.shape || 'square';
  openModal('editTableModal');
}

function confirmDeleteTable(tableId, tableNumber) {
  if (confirm('Are you sure you want to remove Table "' + tableNumber + '" from the floor plan?')) {
    const form = document.getElementById('deleteTableForm');
    form.action = '<?= site_url('admin/floors/table/delete') ?>/' + tableId;
    form.submit();
  }
}

function setTableState(tableId, newStatus) {
  window.rmsPost('<?= site_url('admin/floors/table/status') ?>/' + tableId, { status: newStatus })
    .then(function(res) {
      if (res.success) {
        location.reload();
      } else {
        alert(res.message || 'Error updating status');
      }
    })
    .catch(function() {
      alert('Request failed');
    });
}

window.addEventListener('click', function(e) {
  if (e.target.classList.contains('modal-backdrop')) {
    e.target.style.display = 'none';
  }
});
</script>
