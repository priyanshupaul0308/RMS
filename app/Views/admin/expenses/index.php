<div class="page-header">
  <div>
    <h1 class="page-title">Expense Management</h1>
    <p class="page-subtitle">Record daily restaurant expenses, track receipts, invoices, and manager approvals</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <a href="<?= site_url('admin/expenses/reports') ?>" class="btn btn-outline">
      Expense Analytics
    </a>
    <button type="button" class="btn btn-primary" onclick="showExpenseModal()">
      + Record Expense
    </button>
  </div>
</div>

<!-- KPI STATS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Today's Expenses</div>
    <div class="stat-value text-primary">&#8377;<?= number_format($todayTotal, 2) ?></div>
    <div class="stat-sub">Logged on <?= esc(date('M d, Y', strtotime($today))) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Month-to-Date Spend</div>
    <div class="stat-value text-danger">&#8377;<?= number_format($monthTotal, 2) ?></div>
    <div class="stat-sub">Total approved expenses</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Pending Approval</div>
    <div class="stat-value text-warning"><?= esc($pendingCount) ?></div>
    <div class="stat-sub">Requires manager review</div>
  </div>
</div>

<!-- EXPENSES TABLE -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Recent Operating Expenses (<?= count($expenses) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Vendor / Invoice</th>
            <th>Payment Method</th>
            <th>Amount</th>
            <th>Receipt</th>
            <th>Status</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($expenses)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No expenses logged for this month. Click "+ Record Expense" to log daily operational costs.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($expenses as $e): ?>
              <tr>
                <td>
                  <strong><?= esc(date('M d, Y', strtotime($e['expense_date']))) ?></strong>
                </td>
                <td>
                  <span class="badge badge-info"><?= esc($e['category_name']) ?></span>
                </td>
                <td>
                  <strong><?= esc($e['vendor_name'] ?? 'Direct Purchase') ?></strong>
                  <?php if (!empty($e['invoice_receipt_no'])): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Inv: <?= esc($e['invoice_receipt_no']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <span style="text-transform: capitalize;"><?= esc(str_replace('_', ' ', $e['payment_method'])) ?></span>
                </td>
                <td>
                  <strong style="color: #ef4444; font-size: 1.05rem;">&#8377;<?= number_format((float)$e['amount'], 2) ?></strong>
                </td>
                <td>
                  <?php if (!empty($e['receipt_file'])): ?>
                    <a href="<?= base_url(esc($e['receipt_file'])) ?>" target="_blank" class="btn btn-ghost btn-sm" style="color: #60a5fa;">
                      &#128196; View File
                    </a>
                  <?php else: ?>
                    <span class="text-muted" style="font-size: 0.8rem;">No receipt</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($e['status'] === 'approved'): ?>
                    <span class="badge badge-success">Approved</span>
                  <?php elseif ($e['status'] === 'pending'): ?>
                    <span class="badge badge-warning">Pending</span>
                  <?php else: ?>
                    <span class="badge badge-danger">Rejected</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <?php if ($e['status'] === 'pending'): ?>
                    <div style="display: inline-flex; gap: 0.25rem;">
                      <button type="button" class="btn btn-outline btn-sm" onclick="approveExpense(<?= (int)$e['id'] ?>, 'approved')">
                        Approve
                      </button>
                      <button type="button" class="btn btn-ghost btn-sm text-danger" onclick="approveExpense(<?= (int)$e['id'] ?>, 'rejected')">
                        Reject
                      </button>
                    </div>
                  <?php else: ?>
                    <span class="text-muted" style="font-size: 0.85rem;">Reviewed</span>
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

<!-- MODAL: RECORD EXPENSE -->
<div class="pos-modal-overlay" id="expense-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 540px;">
    <div class="pos-modal-header">
      <div class="modal-title">Record Operating Expense</div>
      <button type="button" class="btn-close" onclick="closeModal('expense-modal')">&times;</button>
    </div>
    <form id="expense-form" onsubmit="submitExpense(event)" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div class="pos-modal-body">
        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="ex-cat">Category <span class="form-required">*</span></label>
              <select id="ex-cat" name="category_id" class="form-control" required>
                <option value="">-- Choose Category --</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="ex-amt">Amount (&#8377;) <span class="form-required">*</span></label>
              <input type="number" step="0.01" id="ex-amt" name="amount" class="form-control" required placeholder="0.00">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="ex-date">Expense Date <span class="form-required">*</span></label>
              <input type="date" id="ex-date" name="expense_date" class="form-control" value="<?= esc($today) ?>" required>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="ex-method">Payment Method <span class="form-required">*</span></label>
              <select id="ex-method" name="payment_method" class="form-control" required>
                <option value="cash">Cash in Drawer</option>
                <option value="bank_transfer">Bank Transfer / NEFT</option>
                <option value="company_card">Company Debit / Credit Card</option>
                <option value="cheque">Cheque</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="ex-vendor">Vendor / Supplier Name</label>
              <input type="text" id="ex-vendor" name="vendor_name" class="form-control" placeholder="e.g. City Gas Agency, Metro Mart">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="ex-inv">Bill / Invoice Number</label>
              <input type="text" id="ex-inv" name="invoice_receipt_no" class="form-control" placeholder="e.g. INV-2026-9921">
            </div>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="ex-file">Attach Receipt or Bill Photo</label>
          <input type="file" id="ex-file" name="receipt_file" class="form-control" accept="image/*,application/pdf">
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="ex-notes">Expense Details / Notes</label>
          <textarea id="ex-notes" name="notes" class="form-control" rows="2" placeholder="Specific items purchased, reason, or breakdown..."></textarea>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('expense-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-expense">Save Expense</button>
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

function showExpenseModal() { showModal('expense-modal'); }

async function submitExpense(e) {
  e.preventDefault();
  const form = document.getElementById('expense-form');
  const btn = document.getElementById('btn-submit-expense');
  btn.disabled = true;
  btn.innerText = 'Saving...';

  try {
    const res = await fetch('<?= site_url('admin/expenses/store') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error recording expense');
      btn.disabled = false;
      btn.innerText = 'Save Expense';
    }
  } catch(err) {
    alert('Network error.');
    btn.disabled = false;
    btn.innerText = 'Save Expense';
  }
}

async function approveExpense(id, status) {
  try {
    const fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    fd.append('status', status);

    const res = await fetch(`<?= site_url('admin/expenses/approve') ?>/${id}`, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error processing request');
    }
  } catch(err) {
    alert('Network error.');
  }
}
</script>
