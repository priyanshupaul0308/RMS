<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">🛒 Purchase Orders &amp; Procurement</h1>
    <p class="page-subtitle">Draft vendor purchase orders, receive shipments, and automatically update raw ingredient stock</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" onclick="openModal('createPoModal')">
      ➕ Create Purchase Order
    </button>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Procurement Order Ledger</div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>PO Number</th>
          <th>Vendor / Supplier</th>
          <th>Order Date</th>
          <th>Expected Date</th>
          <th>Status</th>
          <th>Total Amount</th>
          <th>Created By</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($orders)): ?>
        <tr>
          <td colspan="8" class="text-center py-4 text-muted">No purchase orders drafted yet.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($orders as $po): 
          $badgeClass = match($po['status']) {
            'draft'    => 'status-pending',
            'sent'     => 'status-active',
            'received' => 'status-success',
            'cancelled'=> 'status-danger',
            default    => 'status-pending'
          };
        ?>
        <tr>
          <td>
            <div class="fw-700 font-mono"><?= esc($po['po_number']) ?></div>
            <div class="text-muted text-xs"><?= esc($po['notes'] ?? 'No notes') ?></div>
          </td>
          <td>
            <div class="fw-600"><?= esc($po['supplier_name']) ?></div>
            <div class="text-muted text-xs"><?= esc($po['supplier_phone']) ?></div>
          </td>
          <td><?= esc(date('M d, Y', strtotime($po['order_date']))) ?></td>
          <td><?= $po['expected_date'] ? esc(date('M d, Y', strtotime($po['expected_date']))) : '—' ?></td>
          <td>
            <span class="status-badge <?= $badgeClass ?>"><?= ucfirst(esc($po['status'])) ?></span>
          </td>
          <td class="fw-700">₹<?= number_format((float)$po['total_amount'], 2) ?></td>
          <td><?= esc(trim(($po['first_name'] ?? '') . ' ' . ($po['last_name'] ?? 'Staff'))) ?></td>
          <td>
            <?php if ($po['status'] === 'sent' || $po['status'] === 'draft'): ?>
            <form action="<?= site_url('admin/inventory/purchase-orders/receive/' . $po['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Confirm receipt of shipment? Stock will be credited automatically.');">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-sm btn-success">
                📥 Receive Shipment (GRN)
              </button>
            </form>
            <?php else: ?>
            <span class="text-muted text-xs">Completed &amp; Stocked</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Create PO -->
<div id="createPoModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 650px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">➕ Draft Purchase Order</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('createPoModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/inventory/purchase-orders/store') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-3">
          <label class="form-label">Vendor / Supplier *</label>
          <select name="supplier_id" class="form-control" required>
            <option value="">-- Choose Supplier --</option>
            <?php foreach ($suppliers as $s): ?>
              <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?> (<?= esc($s['phone']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-2">
          <label class="form-label fw-600">Order Lines</label>
          <div id="poItemsContainer">
            <div class="po-item-row d-flex gap-2 mb-2 align-center">
              <select name="items[]" class="form-control flex-2" required>
                <option value="">-- Select Raw Item --</option>
                <?php foreach ($items as $it): ?>
                  <option value="<?= $it['id'] ?>"><?= esc($it['name']) ?> (₹<?= number_format((float)$it['unit_cost'], 2) ?> / <?= esc($it['unit_code']) ?>)</option>
                <?php endforeach; ?>
              </select>
              <input type="number" step="0.01" name="quantities[]" class="form-control flex-1" placeholder="Quantity" value="10.0" required>
              <input type="number" step="0.01" name="prices[]" class="form-control flex-1" placeholder="Unit Price (₹)" value="10.00" required>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline mt-1" onclick="addPoRow()">➕ Add Another Item</button>
        </div>

        <div class="form-group mb-2 mt-3">
          <label class="form-label">Order Notes / Delivery Instructions</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Deliver before 10 AM to back kitchen loading dock"></textarea>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('createPoModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Dispatch Purchase Order</button>
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
function addPoRow() {
  const container = document.getElementById('poItemsContainer');
  const firstRow = container.querySelector('.po-item-row');
  const clone = firstRow.cloneNode(true);
  clone.querySelector('input[name="quantities[]"]').value = '10.0';
  clone.querySelector('input[name="prices[]"]').value = '10.00';
  container.appendChild(clone);
}
</script>
