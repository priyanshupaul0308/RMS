<div class="page-header">
  <div>
    <h1 class="page-title">Delivery Partner Management</h1>
    <p class="page-subtitle">Manage delivery riders, assign takeout/delivery orders, track live statuses and performance</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <button type="button" class="btn btn-secondary" onclick="showAssignModal()">
      + Assign Delivery
    </button>
    <button type="button" class="btn btn-primary" onclick="showPartnerModal()">
      + Add Delivery Rider
    </button>
  </div>
</div>

<!-- KPI STATS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Available Riders</div>
    <div class="stat-value text-success"><?= esc($availableRidersCount) ?></div>
    <div class="stat-sub">Ready for assignment</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Out On Delivery</div>
    <div class="stat-value text-warning"><?= esc($onDeliveryRidersCount) ?></div>
    <div class="stat-sub">Currently en route</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Pending Orders</div>
    <div class="stat-value text-primary"><?= count($unassignedOrders) ?></div>
    <div class="stat-sub">Awaiting rider dispatch</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Fleet</div>
    <div class="stat-value text-info"><?= count($partners) ?></div>
    <div class="stat-sub">Registered drivers</div>
  </div>
</div>

<!-- LIVE DISPATCH & ACTIVE ORDERS -->
<div class="card mb-4">
  <div class="card-header">
    <div class="card-title">Active Delivery Orders & Dispatch Board</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer & Address</th>
            <th>Assigned Rider</th>
            <th>Vehicle</th>
            <th>Fee / Total</th>
            <th>Status</th>
            <th style="text-align: right;">Progress Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($activeDeliveries)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                No active delivery orders currently dispatched.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($activeDeliveries as $d): ?>
              <tr>
                <td>
                  <strong><?= esc($d['order_number']) ?></strong>
                  <div style="font-size: 0.75rem; color: var(--text-muted);">
                    Assigned: <?= esc(date('h:i A', strtotime($d['assigned_at']))) ?>
                  </div>
                </td>
                <td>
                  <div><strong><?= esc($d['customer_name'] ?? 'Walk-in Delivery') ?></strong> (<?= esc($d['customer_phone'] ?? 'N/A') ?>)</div>
                  <div style="font-size: 0.8rem; color: var(--text-muted); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= esc($d['delivery_address'] ?? 'Counter Dispatch') ?>
                  </div>
                </td>
                <td>
                  <strong><?= esc($d['partner_name']) ?></strong>
                  <div style="font-size: 0.75rem; color: var(--text-muted);"><?= esc($d['partner_phone']) ?></div>
                </td>
                <td>
                  <span style="text-transform: capitalize;"><?= esc($d['vehicle_type']) ?></span>
                  <div style="font-size: 0.75rem; font-family: monospace; color: #94a3b8;"><?= esc($d['vehicle_number']) ?></div>
                </td>
                <td>
                  <div>&#8377;<?= number_format((float)($d['total_amount'] ?? $d['final_total'] ?? 0), 2) ?></div>
                  <div style="font-size: 0.75rem; color: #10b981;">Fee: &#8377;<?= number_format((float)($d['delivery_fee'] ?? 0), 2) ?></div>
                </td>
                <td>
                  <?php if ($d['status'] === 'assigned'): ?>
                    <span class="badge badge-warning">Assigned</span>
                  <?php elseif ($d['status'] === 'picked_up'): ?>
                    <span class="badge badge-info">Picked Up</span>
                  <?php elseif ($d['status'] === 'in_transit'): ?>
                    <span class="badge badge-primary">In Transit</span>
                  <?php elseif ($d['status'] === 'delivered'): ?>
                    <span class="badge badge-success">Delivered</span>
                  <?php else: ?>
                    <span class="badge badge-danger"><?= esc(ucfirst($d['status'])) ?></span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.35rem;">
                    <?php if ($d['status'] === 'assigned'): ?>
                      <button type="button" class="btn btn-outline btn-sm" onclick="updateDelivery(<?= (int)$d['id'] ?>, 'picked_up')">
                        Pick Up
                      </button>
                    <?php elseif ($d['status'] === 'picked_up'): ?>
                      <button type="button" class="btn btn-outline btn-sm" onclick="updateDelivery(<?= (int)$d['id'] ?>, 'in_transit')">
                        In Transit
                      </button>
                    <?php elseif ($d['status'] === 'in_transit'): ?>
                      <button type="button" class="btn btn-primary btn-sm" onclick="updateDelivery(<?= (int)$d['id'] ?>, 'delivered')">
                        Complete
                      </button>
                    <?php else: ?>
                      <span class="text-muted" style="font-size: 0.85rem;">Done</span>
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

<!-- FLEET LIST -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Delivery Partners Fleet (<?= count($partners) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Rider Name</th>
            <th>Phone / Contact</th>
            <th>Vehicle Details</th>
            <th>Commission Rate</th>
            <th>Rating</th>
            <th>Total Deliveries</th>
            <th>Availability</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($partners)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No delivery partners added yet. Click "+ Add Delivery Rider" to onboard drivers.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($partners as $p): ?>
              <tr>
                <td>
                  <strong><?= esc($p['name']) ?></strong>
                </td>
                <td>
                  <?= esc($p['phone']) ?>
                  <?php if (!empty($p['email'])): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= esc($p['email']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <span style="text-transform: capitalize;"><?= esc($p['vehicle_type']) ?></span>
                  <div style="font-family: monospace; font-size: 0.8rem; color: #94a3b8;"><?= esc($p['vehicle_number']) ?></div>
                </td>
                <td><?= esc($p['commission_rate']) ?>%</td>
                <td>
                  <span style="color: #f59e0b; font-weight: 600;">&#9733; <?= number_format((float)$p['rating'], 1) ?></span>
                </td>
                <td><strong><?= esc($p['total_deliveries']) ?></strong> trips</td>
                <td>
                  <?php if ($p['availability_status'] === 'available'): ?>
                    <span class="badge badge-success">Available</span>
                  <?php elseif ($p['availability_status'] === 'on_delivery'): ?>
                    <span class="badge badge-warning">On Delivery</span>
                  <?php elseif ($p['availability_status'] === 'on_break'): ?>
                    <span class="badge badge-info">On Break</span>
                  <?php else: ?>
                    <span class="badge badge-danger">Offline</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <button type="button" class="btn btn-ghost btn-sm" onclick='editPartner(<?= json_encode($p) ?>)'>
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

<!-- MODAL: ADD / EDIT PARTNER -->
<div class="pos-modal-overlay" id="partner-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 540px;">
    <div class="pos-modal-header">
      <div class="modal-title" id="pm-title">Add Delivery Partner</div>
      <button type="button" class="btn-close" onclick="closeModal('partner-modal')">&times;</button>
    </div>
    <form id="partner-form" onsubmit="submitPartner(event)">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="pm-id" value="">
      <div class="pos-modal-body">
        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="pm-name">Full Name <span class="form-required">*</span></label>
              <input type="text" id="pm-name" name="name" class="form-control" required placeholder="e.g. Rajesh Kumar">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="pm-phone">Phone Number <span class="form-required">*</span></label>
              <input type="tel" id="pm-phone" name="phone" class="form-control" required placeholder="+91 9811223344">
            </div>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="pm-email">Email Address</label>
          <input type="email" id="pm-email" name="email" class="form-control" placeholder="rider@example.com">
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="pm-vtype">Vehicle Type <span class="form-required">*</span></label>
              <select id="pm-vtype" name="vehicle_type" class="form-control" required>
                <option value="bike">Motorbike</option>
                <option value="scooter">Scooter</option>
                <option value="car">Car</option>
                <option value="van">Delivery Van</option>
              </select>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="pm-vnum">Vehicle Number Plate <span class="form-required">*</span></label>
              <input type="text" id="pm-vnum" name="vehicle_number" class="form-control" required placeholder="DL-01-AB-1234" style="text-transform: uppercase;">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="pm-comm">Commission Rate (%)</label>
              <input type="number" step="0.1" id="pm-comm" name="commission_rate" class="form-control" value="15.0">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="pm-avail">Availability Status</label>
              <select id="pm-avail" name="availability_status" class="form-control">
                <option value="available">Available</option>
                <option value="on_delivery">On Delivery</option>
                <option value="on_break">On Break</option>
                <option value="offline">Offline</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('partner-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-partner">Save Partner</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: ASSIGN DELIVERY -->
<div class="pos-modal-overlay" id="assign-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 500px;">
    <div class="pos-modal-header">
      <div class="modal-title">Assign Order to Delivery Rider</div>
      <button type="button" class="btn-close" onclick="closeModal('assign-modal')">&times;</button>
    </div>
    <form id="assign-form" onsubmit="submitAssign(event)">
      <?= csrf_field() ?>
      <div class="pos-modal-body">
        <div class="form-group mb-3">
          <label class="form-label" for="as-order">Select Order <span class="form-required">*</span></label>
          <select id="as-order" name="order_id" class="form-control" required>
            <option value="">-- Choose Order to Dispatch --</option>
            <?php foreach ($unassignedOrders as $o): ?>
              <option value="<?= (int)$o['id'] ?>">#<?= esc($o['order_number']) ?> - &#8377;<?= number_format((float)($o['final_total'] ?? $o['total_amount'] ?? 0), 2) ?> (<?= esc($o['customer_name'] ?? 'Takeaway') ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="as-partner">Select Rider <span class="form-required">*</span></label>
          <select id="as-partner" name="partner_id" class="form-control" required>
            <option value="">-- Choose Delivery Partner --</option>
            <?php foreach ($partners as $p): ?>
              <option value="<?= (int)$p['id'] ?>">
                <?= esc($p['name']) ?> (<?= esc($p['vehicle_type']) ?> - <?= esc($p['availability_status']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="as-fee">Delivery Fee (&#8377;)</label>
          <input type="number" step="0.01" id="as-fee" name="delivery_fee" class="form-control" value="40.00">
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="as-notes">Dispatch Notes</label>
          <input type="text" id="as-notes" name="delivery_notes" class="form-control" placeholder="e.g. Fragile beverage packing, call on arrival">
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('assign-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-assign">Dispatch Order</button>
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

function showPartnerModal() {
  document.getElementById('pm-title').innerText = 'Add Delivery Partner';
  document.getElementById('pm-id').value = '';
  document.getElementById('partner-form').reset();
  showModal('partner-modal');
}

function editPartner(p) {
  document.getElementById('pm-title').innerText = 'Edit Delivery Partner';
  document.getElementById('pm-id').value = p.id;
  document.getElementById('pm-name').value = p.name;
  document.getElementById('pm-phone').value = p.phone;
  document.getElementById('pm-email').value = p.email || '';
  document.getElementById('pm-vtype').value = p.vehicle_type;
  document.getElementById('pm-vnum').value = p.vehicle_number;
  document.getElementById('pm-comm').value = p.commission_rate;
  document.getElementById('pm-avail').value = p.availability_status;
  showModal('partner-modal');
}

function showAssignModal() { showModal('assign-modal'); }

async function submitPartner(e) {
  e.preventDefault();
  const form = document.getElementById('partner-form');
  const btn = document.getElementById('btn-submit-partner');
  btn.disabled = true;
  btn.innerText = 'Saving...';

  try {
    const res = await fetch('<?= site_url('admin/delivery/partner/save') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error saving partner');
      btn.disabled = false;
      btn.innerText = 'Save Partner';
    }
  } catch(err) {
    alert('Network error.');
    btn.disabled = false;
    btn.innerText = 'Save Partner';
  }
}

async function submitAssign(e) {
  e.preventDefault();
  const form = document.getElementById('assign-form');
  const btn = document.getElementById('btn-submit-assign');
  btn.disabled = true;
  btn.innerText = 'Dispatching...';

  try {
    const res = await fetch('<?= site_url('admin/delivery/assign') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error dispatching order');
      btn.disabled = false;
      btn.innerText = 'Dispatch Order';
    }
  } catch(err) {
    alert('Network error.');
    btn.disabled = false;
    btn.innerText = 'Dispatch Order';
  }
}

async function updateDelivery(id, status) {
  try {
    const fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    fd.append('status', status);

    const res = await fetch(`<?= site_url('admin/delivery/status') ?>/${id}`, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error updating status');
    }
  } catch(err) {
    alert('Network error.');
  }
}
</script>
