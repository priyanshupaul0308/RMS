<div class="page-header">
  <div>
    <h1 class="page-title">Equipment & Maintenance Management</h1>
    <p class="page-subtitle">Track kitchen assets, breakdown work orders, scheduled maintenance, and repair expenses</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <button type="button" class="btn btn-secondary" onclick="showRequestModal()">
      + Log Maintenance Request
    </button>
    <button type="button" class="btn btn-primary" onclick="showEquipmentModal()">
      + Register Equipment
    </button>
  </div>
</div>

<!-- KPI STATS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Operational Assets</div>
    <div class="stat-value text-success"><?= esc($operationalCount) ?></div>
    <div class="stat-sub">Fully functioning</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Under Servicing</div>
    <div class="stat-value text-warning"><?= esc($underMaintenanceCount) ?></div>
    <div class="stat-sub">Active work orders</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Broken / Out of Order</div>
    <div class="stat-value text-danger"><?= esc($brokenCount) ?></div>
    <div class="stat-sub">Requires urgent technician</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Cumulative Repair Cost</div>
    <div class="stat-value text-info">&#8377;<?= number_format($totalRepairCost, 2) ?></div>
    <div class="stat-sub">Logged maintenance spend</div>
  </div>
</div>

<!-- ACTIVE MAINTENANCE REQUESTS -->
<div class="card mb-4">
  <div class="card-header">
    <div class="card-title">Active Maintenance Requests & Repairs (<?= count($requests) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Equipment & Location</th>
            <th>Work Order Title</th>
            <th>Priority</th>
            <th>Assigned Vendor</th>
            <th>Reported At</th>
            <th>Repair Cost</th>
            <th>Status</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($requests)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                No open maintenance requests. All equipment operating smoothly.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td>
                  <strong><?= esc($r['equipment_name']) ?></strong>
                  <div style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($r['location']) ?></div>
                </td>
                <td>
                  <strong><?= esc($r['title']) ?></strong>
                  <div style="font-size: 0.8rem; color: #94a3b8; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= esc($r['description']) ?>
                  </div>
                </td>
                <td>
                  <?php if ($r['priority'] === 'emergency'): ?>
                    <span class="badge badge-danger">EMERGENCY</span>
                  <?php elseif ($r['priority'] === 'high'): ?>
                    <span class="badge badge-warning">High</span>
                  <?php elseif ($r['priority'] === 'medium'): ?>
                    <span class="badge badge-info">Medium</span>
                  <?php else: ?>
                    <span class="badge badge-success">Low</span>
                  <?php endif; ?>
                </td>
                <td><?= esc($r['assigned_to_vendor'] ?? 'In-house staff') ?></td>
                <td><?= esc(date('M d, Y', strtotime($r['reported_at']))) ?></td>
                <td>
                  <?= (float)$r['cost'] > 0 ? '&#8377;' . number_format((float)$r['cost'], 2) : '<span class="text-muted">Pending</span>' ?>
                </td>
                <td>
                  <?php if ($r['status'] === 'reported'): ?>
                    <span class="badge badge-warning">Reported</span>
                  <?php elseif ($r['status'] === 'in_progress'): ?>
                    <span class="badge badge-primary">In Progress</span>
                  <?php elseif ($r['status'] === 'completed'): ?>
                    <span class="badge badge-success">Completed</span>
                  <?php else: ?>
                    <span class="badge badge-danger">Cancelled</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <?php if ($r['status'] !== 'completed' && $r['status'] !== 'cancelled'): ?>
                    <button type="button" class="btn btn-outline btn-sm" onclick='openUpdateModal(<?= (int)$r['id'] ?>, "<?= esc($r['equipment_name']) ?>")'>
                      Update Status
                    </button>
                  <?php else: ?>
                    <span class="text-muted" style="font-size: 0.85rem;">Resolved</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- EQUIPMENT INVENTORY REGISTRY -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Equipment Asset Registry (<?= count($equipmentList) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Asset Name</th>
            <th>Location</th>
            <th>Model / Serial No.</th>
            <th>Next Service Due</th>
            <th>Asset Value</th>
            <th>Operating Status</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($equipmentList)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted" style="padding: 3rem 1rem;">
                No equipment registered yet. Click "+ Register Equipment" to build your inventory.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($equipmentList as $eq): ?>
              <tr>
                <td>
                  <strong><?= esc($eq['name']) ?></strong>
                </td>
                <td><?= esc($eq['location']) ?></td>
                <td>
                  <div style="font-size: 0.85rem; font-family: monospace; color: #94a3b8;">
                    <?= esc($eq['model_number'] ?? 'N/A') ?>
                  </div>
                  <?php if (!empty($eq['serial_number'])): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">SN: <?= esc($eq['serial_number']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <?= !empty($eq['next_service_due']) ? esc(date('M d, Y', strtotime($eq['next_service_due']))) : '<span class="text-muted">Not scheduled</span>' ?>
                </td>
                <td>&#8377;<?= number_format((float)$eq['cost'], 2) ?></td>
                <td>
                  <?php if ($eq['status'] === 'operational'): ?>
                    <span class="badge badge-success">Operational</span>
                  <?php elseif ($eq['status'] === 'under_maintenance'): ?>
                    <span class="badge badge-warning">Maintenance</span>
                  <?php elseif ($eq['status'] === 'broken'): ?>
                    <span class="badge badge-danger">Broken</span>
                  <?php else: ?>
                    <span class="badge badge-info">Retired</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <button type="button" class="btn btn-ghost btn-sm" onclick='editEquipment(<?= json_encode($eq) ?>)'>
                    Edit
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL: ADD / EDIT EQUIPMENT -->
<div class="pos-modal-overlay" id="equipment-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 540px;">
    <div class="pos-modal-header">
      <div class="modal-title" id="eq-modal-title">Register Equipment Asset</div>
      <button type="button" class="btn-close" onclick="closeModal('equipment-modal')">&times;</button>
    </div>
    <form id="equipment-form" onsubmit="submitEquipment(event)">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="eq-id" value="">
      <div class="pos-modal-body">
        <div class="form-group mb-3">
          <label class="form-label" for="eq-name">Equipment Name <span class="form-required">*</span></label>
          <input type="text" id="eq-name" name="name" class="form-control" required placeholder="e.g. Commercial Conveyor Toaster">
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="eq-loc">Station / Location <span class="form-required">*</span></label>
              <input type="text" id="eq-loc" name="location" class="form-control" required placeholder="e.g. Bakery Station, Bar, Main Kitchen">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="eq-status">Operational Status <span class="form-required">*</span></label>
              <select id="eq-status" name="status" class="form-control" required>
                <option value="operational">Operational</option>
                <option value="under_maintenance">Under Maintenance</option>
                <option value="broken">Broken</option>
                <option value="retired">Retired</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="eq-model">Model Number</label>
              <input type="text" id="eq-model" name="model_number" class="form-control" placeholder="e.g. TOAST-900X">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="eq-serial">Serial Number</label>
              <input type="text" id="eq-serial" name="serial_number" class="form-control" placeholder="e.g. SN-882194">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="eq-cost">Purchase Value (&#8377;)</label>
              <input type="number" step="0.01" id="eq-cost" name="cost" class="form-control" placeholder="0.00">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="eq-next-due">Next Service Due Date</label>
              <input type="date" id="eq-next-due" name="next_service_due" class="form-control">
            </div>
          </div>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('equipment-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-eq">Save Equipment</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: NEW MAINTENANCE REQUEST -->
<div class="pos-modal-overlay" id="request-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 540px;">
    <div class="pos-modal-header">
      <div class="modal-title">Log Maintenance Work Order</div>
      <button type="button" class="btn-close" onclick="closeModal('request-modal')">&times;</button>
    </div>
    <form id="request-form" onsubmit="submitRequest(event)">
      <?= csrf_field() ?>
      <div class="pos-modal-body">
        <div class="form-group mb-3">
          <label class="form-label" for="mr-eq">Select Equipment <span class="form-required">*</span></label>
          <select id="mr-eq" name="equipment_id" class="form-control" required>
            <option value="">-- Choose Equipment Asset --</option>
            <?php foreach ($equipmentList as $eq): ?>
              <option value="<?= (int)$eq['id'] ?>"><?= esc($eq['name']) ?> (<?= esc($eq['location']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="mr-title">Issue Summary <span class="form-required">*</span></label>
              <input type="text" id="mr-title" name="title" class="form-control" required placeholder="e.g. Heating element not turning on">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="mr-pri">Priority Level <span class="form-required">*</span></label>
              <select id="mr-pri" name="priority" class="form-control" required>
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
                <option value="emergency">Emergency Breakdown</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="mr-desc">Detailed Issue Description <span class="form-required">*</span></label>
          <textarea id="mr-desc" name="description" class="form-control" rows="3" required placeholder="Describe symptoms, noise, leaks or error codes..."></textarea>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="mr-vendor">Assigned Technician / Vendor</label>
          <input type="text" id="mr-vendor" name="assigned_to_vendor" class="form-control" placeholder="e.g. Apex Kitchen Services Pvt Ltd">
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('request-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-req">Submit Request</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: UPDATE WORK ORDER STATUS -->
<div class="pos-modal-overlay" id="update-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 500px;">
    <div class="pos-modal-header">
      <div class="modal-title" id="um-title">Update Work Order Status</div>
      <button type="button" class="btn-close" onclick="closeModal('update-modal')">&times;</button>
    </div>
    <form id="update-form" onsubmit="submitStatusUpdate(event)">
      <?= csrf_field() ?>
      <input type="hidden" id="um-id" value="">
      <div class="pos-modal-body">
        <div class="form-group mb-3">
          <label class="form-label" for="um-status">Status <span class="form-required">*</span></label>
          <select id="um-status" name="status" class="form-control" required>
            <option value="in_progress">In Progress / Technician Dispatched</option>
            <option value="completed">Completed & Operational</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="um-cost">Repair Cost (&#8377;)</label>
              <input type="number" step="0.01" id="um-cost" name="cost" class="form-control" placeholder="0.00">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="um-inv">Service Invoice #</label>
              <input type="text" id="um-inv" name="invoice_number" class="form-control" placeholder="e.g. INV-9012">
            </div>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="um-notes">Resolution Notes</label>
          <textarea id="um-notes" name="resolution_notes" class="form-control" rows="2" placeholder="Parts replaced, labor details..."></textarea>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('update-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-update">Update Request</button>
      </div>
    </form>
  </div>
</div>

<script>
function showModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.removeAttribute('hidden');
    m.style.setProperty('display', 'flex', 'important');
  }
}

function closeModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.style.setProperty('display', 'none', 'important');
    m.setAttribute('hidden', '');
  }
}

function showEquipmentModal() {
  document.getElementById('eq-modal-title').innerText = 'Register Equipment Asset';
  document.getElementById('eq-id').value = '';
  document.getElementById('equipment-form').reset();
  showModal('equipment-modal');
}

function editEquipment(eq) {
  document.getElementById('eq-modal-title').innerText = 'Edit Equipment Asset';
  document.getElementById('eq-id').value = eq.id;
  document.getElementById('eq-name').value = eq.name;
  document.getElementById('eq-loc').value = eq.location;
  document.getElementById('eq-status').value = eq.status;
  document.getElementById('eq-model').value = eq.model_number || '';
  document.getElementById('eq-serial').value = eq.serial_number || '';
  document.getElementById('eq-cost').value = eq.cost;
  document.getElementById('eq-next-due').value = eq.next_service_due || '';
  showModal('equipment-modal');
}

function showRequestModal() {
  document.getElementById('request-form').reset();
  showModal('request-modal');
}

function openUpdateModal(id, name) {
  document.getElementById('um-id').value = id;
  document.getElementById('um-title').innerText = `Update: ${name}`;
  showModal('update-modal');
}

async function submitEquipment(e) {
  e.preventDefault();
  const form = document.getElementById('equipment-form');
  const btn = document.getElementById('btn-submit-eq');
  btn.disabled = true;
  btn.innerText = 'Saving...';

  try {
    const res = await fetch('<?= site_url('admin/maintenance/equipment/save') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error saving equipment');
      btn.disabled = false;
      btn.innerText = 'Save Equipment';
    }
  } catch(err) {
    alert('Network error.');
    btn.disabled = false;
    btn.innerText = 'Save Equipment';
  }
}

async function submitRequest(e) {
  e.preventDefault();
  const form = document.getElementById('request-form');
  const btn = document.getElementById('btn-submit-req');
  btn.disabled = true;
  btn.innerText = 'Submitting...';

  try {
    const res = await fetch('<?= site_url('admin/maintenance/request/store') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error submitting request');
      btn.disabled = false;
      btn.innerText = 'Submit Request';
    }
  } catch(err) {
    alert('Network error.');
    btn.disabled = false;
    btn.innerText = 'Submit Request';
  }
}

async function submitStatusUpdate(e) {
  e.preventDefault();
  const id = document.getElementById('um-id').value;
  const form = document.getElementById('update-form');
  const btn = document.getElementById('btn-submit-update');
  btn.disabled = true;
  btn.innerText = 'Updating...';

  try {
    const res = await fetch(`<?= site_url('admin/maintenance/request/status') ?>/${id}`, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error updating status');
      btn.disabled = false;
      btn.innerText = 'Update Request';
    }
  } catch(err) {
    alert('Network error.');
    btn.disabled = false;
    btn.innerText = 'Update Request';
  }
}
</script>
