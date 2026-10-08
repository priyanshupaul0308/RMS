<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">🗑️ Wastage &amp; Spoilage Logs</h1>
    <p class="page-subtitle">Track kitchen prep loss, expired perishables, burnt food, and financial cost impact</p>
  </div>
  <div>
    <button type="button" class="btn btn-danger" onclick="openModal('logWasteModal')">
      ➕ Log Waste Incident
    </button>
  </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid mb-3">
  <div class="kpi-card" style="border-color: rgba(239, 68, 68, 0.4);">
    <div class="kpi-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">💸</div>
    <div class="kpi-body">
      <div class="kpi-label">This Month's Waste Loss</div>
      <div class="kpi-value text-danger">₹<?= number_format($monthlyLoss, 2) ?></div>
      <div class="kpi-trend text-muted text-xs">Direct financial cost of discarded items</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">📋</div>
    <div class="kpi-body">
      <div class="kpi-label">Logged Incidents</div>
      <div class="kpi-value"><?= count($logs) ?></div>
      <div class="kpi-trend text-muted text-xs">Total incidents on record</div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-between align-center">
    <div class="card-title">Waste Incident History</div>
    <div class="d-flex gap-1">
      <a href="<?= site_url('admin/inventory/waste') ?>" class="btn btn-xs <?= ($selectedRsn === null) ? 'btn-primary' : 'btn-outline' ?>">All</a>
      <a href="<?= site_url('admin/inventory/waste?reason=spoilage') ?>" class="btn btn-xs <?= ($selectedRsn === 'spoilage') ? 'btn-primary' : 'btn-outline' ?>">Spoilage</a>
      <a href="<?= site_url('admin/inventory/waste?reason=expired') ?>" class="btn btn-xs <?= ($selectedRsn === 'expired') ? 'btn-primary' : 'btn-outline' ?>">Expired</a>
      <a href="<?= site_url('admin/inventory/waste?reason=burnt') ?>" class="btn btn-xs <?= ($selectedRsn === 'burnt') ? 'btn-primary' : 'btn-outline' ?>">Burnt</a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Date &amp; Time</th>
          <th>Raw Material</th>
          <th>Discarded Quantity</th>
          <th>Financial Cost Impact</th>
          <th>Reason</th>
          <th>Logged By</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($logs)): ?>
        <tr>
          <td colspan="7" class="text-center py-4 text-muted">No waste incidents recorded. Keep up the high kitchen efficiency!</td>
        </tr>
        <?php else: ?>
        <?php foreach ($logs as $l): ?>
        <tr>
          <td><?= esc(date('M d, Y h:i A', strtotime($l['created_at']))) ?></td>
          <td>
            <div class="fw-700"><?= esc($l['item_name']) ?></div>
            <div class="text-muted text-xs font-mono"><?= esc($l['sku']) ?></div>
          </td>
          <td class="fw-600 text-danger"><?= number_format((float)$l['quantity'], 2) ?> <?= esc($l['unit_code']) ?></td>
          <td class="fw-700 text-danger">₹<?= number_format((float)$l['cost_impact'], 2) ?></td>
          <td>
            <span class="badge badge-warning"><?= strtoupper(esc($l['waste_reason'])) ?></span>
          </td>
          <td><?= esc(trim(($l['first_name'] ?? '') . ' ' . ($l['last_name'] ?? 'Kitchen Staff'))) ?></td>
          <td class="text-muted text-xs"><?= esc($l['notes'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Log Waste -->
<div id="logWasteModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 480px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">🗑️ Log Waste Incident</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('logWasteModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/inventory/waste/store') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-2">
          <label class="form-label">Ingredient / Raw Material *</label>
          <select name="inventory_item_id" class="form-control" required>
            <option value="">-- Choose Item --</option>
            <?php foreach ($items as $it): ?>
              <option value="<?= $it['id'] ?>"><?= esc($it['name']) ?> (Stock: <?= number_format((float)$it['current_stock'], 2) ?> <?= esc($it['unit_code']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Discarded Quantity *</label>
            <input type="number" step="0.01" name="quantity" class="form-control" placeholder="e.g. 2.5" required>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Primary Reason *</label>
            <select name="waste_reason" class="form-control" required>
              <option value="spoilage">Spoilage (Rotten / Bad)</option>
              <option value="expired">Past Expiry Date</option>
              <option value="burnt">Burnt / Overcooked in Kitchen</option>
              <option value="damaged">Damaged Packaging / Spilled</option>
              <option value="prep_error">Prep / Cutting Waste</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Incident Notes</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Power fluctuation caused walk-in cooler temperature rise"></textarea>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('logWasteModal')">Cancel</button>
        <button type="submit" class="btn btn-danger">Confirm &amp; Deduct Stock</button>
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
</script>
