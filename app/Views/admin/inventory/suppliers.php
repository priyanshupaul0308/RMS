<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">🚚 Suppliers &amp; Vendors</h1>
    <p class="page-subtitle">Purveyor contacts, payment credit terms, tax registration, and order history</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" onclick="openModal('addSupplierModal')">
      ➕ Register Supplier
    </button>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Approved Restaurant Suppliers</div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Supplier Name</th>
          <th>Contact Person</th>
          <th>Phone &amp; Email</th>
          <th>Payment Terms</th>
          <th>Tax / GST No</th>
          <th>Active Raw Items</th>
          <th>Lifetime POs</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($suppliers)): ?>
        <tr>
          <td colspan="7" class="text-center py-4 text-muted">No suppliers registered yet.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($suppliers as $s): ?>
        <tr>
          <td>
            <div class="fw-700"><?= esc($s['name']) ?></div>
            <div class="text-muted text-xs"><?= esc($s['address'] ?? 'No address provided') ?></div>
          </td>
          <td><?= esc($s['contact_person'] ?? 'N/A') ?></td>
          <td>
            <div><?= esc($s['phone']) ?></div>
            <div class="text-muted text-xs"><?= esc($s['email'] ?? 'N/A') ?></div>
          </td>
          <td><span class="badge badge-secondary"><?= esc($s['payment_terms']) ?></span></td>
          <td class="font-mono text-xs"><?= esc($s['tax_number'] ?? 'N/A') ?></td>
          <td><span class="status-badge status-active"><?= (int)$s['item_count'] ?> items</span></td>
          <td class="fw-600"><?= (int)$s['total_pos'] ?> orders</td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Add Supplier -->
<div id="addSupplierModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 520px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">➕ Register Vendor / Supplier</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('addSupplierModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/inventory/suppliers/store') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-group mb-2">
          <label class="form-label">Company / Vendor Name *</label>
          <input type="text" name="name" class="form-control" placeholder="e.g. Golden Valley Farms" required>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Contact Person</label>
            <input type="text" name="contact_person" class="form-control" placeholder="e.g. Sarah Jenkins">
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Phone Number *</label>
            <input type="text" name="phone" class="form-control" placeholder="+1 555-0199" required>
          </div>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="orders@vendor.com">
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Payment Terms</label>
            <select name="payment_terms" class="form-control">
              <option value="Cash on Delivery">Cash on Delivery (COD)</option>
              <option value="Net 15" selected>Net 15 Days</option>
              <option value="Net 30">Net 30 Days</option>
              <option value="Weekly Cycle">Weekly Cycle</option>
            </select>
          </div>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Tax / GST Identification Number</label>
          <input type="text" name="tax_number" class="form-control" placeholder="e.g. GSTIN12345678">
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Warehouse Address</label>
          <textarea name="address" class="form-control" rows="2" placeholder="Full delivery/billing address"></textarea>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('addSupplierModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Register Supplier</button>
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
