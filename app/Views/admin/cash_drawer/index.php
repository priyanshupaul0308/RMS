<div class="page-header">
  <div>
    <h1 class="page-title">Cash Drawer &amp; Shifts</h1>
    <p class="page-subtitle">Cash register float tracking, petty cash drops, and end-of-day discrepancy reconciliation</p>
  </div>
</div>

<?php if (!$activeRegister): ?>
  <!-- REGISTER CLOSED STATE -->
  <div class="card mb-3" style="max-width: 600px;">
    <div class="card-header">
      <div class="card-title">🔒 Register is Currently Closed</div>
    </div>
    <div class="card-body">
      <p class="text-muted text-small mb-3">
        No active cash drawer shift is currently open for your account. Please enter your starting float to begin taking cash orders.
      </p>

      <form action="<?= site_url('admin/cash-drawer/open') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label" for="opening_float">Starting Cash Float (₹) <span class="form-required">*</span></label>
          <input type="number" step="0.01" name="opening_float" id="opening_float" class="form-control" placeholder="e.g. 2000.00" value="2000.00" required>
          <div class="form-hint">Physical cash placed in till at shift start for change</div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mt-2">
          🔓 Open Shift Register
        </button>
      </form>
    </div>
  </div>

<?php else: ?>

  <!-- REGISTER OPEN STATE -->
  <div class="card mb-3" style="border-left: 4px solid var(--success);">
    <div class="card-body d-flex justify-between align-center flex-wrap gap-2">
      <div>
        <div class="d-flex align-center gap-1">
          <span class="status-badge status-active">Shift Active</span>
          <span class="fw-700">Opened at <?= esc(date('M d, Y h:i A', strtotime($activeRegister['opened_at']))) ?></span>
        </div>
        <div class="text-muted text-xs mt-1">Cashier: <?= esc($currentUser['first_name'] ?? 'Staff') ?> &bull; Register ID: #<?= (int)$activeRegister['id'] ?></div>
      </div>
      <div>
        <button type="button" class="btn btn-warning btn-sm" onclick="showCashModal()">
          💵 Record Cash In / Out
        </button>
        <button type="button" class="btn btn-danger btn-sm" onclick="showCloseModal()">
          🔒 Close &amp; Reconcile Shift
        </button>
      </div>
    </div>
  </div>

  <!-- LIVE SHIFT KPIS -->
  <div class="kpi-grid">
    <div class="kpi-card info">
      <div class="kpi-icon">💰</div>
      <div class="kpi-value">₹<?= esc(number_format((float)($summary['opening_float'] ?? 0), 2)) ?></div>
      <div class="kpi-label">Opening Float</div>
    </div>

    <div class="kpi-card success">
      <div class="kpi-icon">💵</div>
      <div class="kpi-value">₹<?= esc(number_format((float)($summary['cash_sales'] ?? 0), 2)) ?></div>
      <div class="kpi-label">Cash Sales</div>
    </div>

    <div class="kpi-card accent">
      <div class="kpi-icon">💳</div>
      <div class="kpi-value">₹<?= esc(number_format((float)($summary['digital_sales'] ?? 0), 2)) ?></div>
      <div class="kpi-label">Card / UPI Sales</div>
    </div>

    <div class="kpi-card primary">
      <div class="kpi-icon">📥</div>
      <div class="kpi-value">₹<?= esc(number_format((float)($summary['cash_in'] ?? 0), 2)) ?></div>
      <div class="kpi-label">Cash In (Added)</div>
    </div>

    <div class="kpi-card danger">
      <div class="kpi-icon">📤</div>
      <div class="kpi-value">₹<?= esc(number_format((float)($summary['cash_out'] ?? 0), 2)) ?></div>
      <div class="kpi-label">Cash Out (Petty Cash)</div>
    </div>

    <div class="kpi-card warning" style="border: 2px solid var(--warning);">
      <div class="kpi-icon">⚖️</div>
      <div class="kpi-value" style="color: var(--warning);">₹<?= esc(number_format((float)($summary['expected_cash'] ?? 0), 2)) ?></div>
      <div class="kpi-label">Expected Drawer Cash</div>
    </div>
  </div>

  <!-- TRANSACTIONS LEDGER FOR THIS SHIFT -->
  <div class="card mb-3">
    <div class="card-header">
      <div class="card-title">Drawer Drops &amp; Petty Cash Entries (<?= count($transactions) ?>)</div>
    </div>
    <div class="card-body" style="padding: 0;">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Reason / Purpose</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($transactions)): ?>
            <tr>
              <td colspan="4" class="text-center text-muted" style="padding: 1.5rem 1rem;">
                No manual cash in or cash out transactions recorded during this shift.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($transactions as $t): ?>
              <tr>
                <td><?= esc(date('H:i:s', strtotime($t['created_at']))) ?></td>
                <td>
                  <span class="status-badge <?= $t['type'] === 'cash_in' ? 'status-active' : 'status-danger' ?>">
                    <?= $t['type'] === 'cash_in' ? 'Cash In (Deposit)' : 'Cash Out (Drop / Payout)' ?>
                  </span>
                </td>
                <td class="td-bold">₹<?= esc(number_format((float)$t['amount'], 2)) ?></td>
                <td><?= esc($t['reason']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php endif; ?>

<!-- PAST SHIFTS RECONCILIATION AUDIT -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Recent Closed Shift Audits</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Shift ID</th>
            <th>Cashier</th>
            <th>Opened</th>
            <th>Closed</th>
            <th>Float</th>
            <th>Expected</th>
            <th>Counted</th>
            <th>Discrepancy</th>
            <th>Notes</th>
            <th style="text-align: right; width: 140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($pastShifts)): ?>
            <tr>
              <td colspan="10" class="text-center text-muted" style="padding: 2rem 1rem;">
                No closed shift history available.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($pastShifts as $ps): ?>
              <tr>
                <td class="td-bold">#<?= (int)$ps['id'] ?></td>
                <td><?= esc($ps['cashier_name'] ?? 'Staff') ?></td>
                <td><?= esc(date('d M, H:i', strtotime($ps['opened_at']))) ?></td>
                <td><?= !empty($ps['closed_at']) ? esc(date('d M, H:i', strtotime($ps['closed_at']))) : '—' ?></td>
                <td>₹<?= esc(number_format((float)$ps['opening_float'], 2)) ?></td>
                <td>₹<?= esc(number_format((float)($ps['expected_cash'] ?? 0), 2)) ?></td>
                <td class="td-bold">₹<?= esc(number_format((float)($ps['closing_cash_counted'] ?? 0), 2)) ?></td>
                <td>
                  <?php $disc = (float)($ps['discrepancy'] ?? 0); ?>
                  <?php if ($disc == 0): ?>
                    <span class="status-badge status-active">Balanced (₹0)</span>
                  <?php elseif ($disc > 0): ?>
                    <span class="status-badge status-active">+₹<?= esc(number_format($disc, 2)) ?> Overage</span>
                  <?php else: ?>
                    <span class="status-badge status-danger">-₹<?= esc(number_format(abs($disc), 2)) ?> Shortage</span>
                  <?php endif; ?>
                </td>
                <td class="text-xs" style="max-width: 180px; word-break: break-word;">
                  <?= !empty($ps['notes']) ? esc($ps['notes']) : '<span class="text-muted">—</span>' ?>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                  <button type="button" class="btn btn-ghost btn-sm" onclick='openEditShiftModal(<?= json_encode($ps, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' title="Edit Notes & Shift Audit" style="padding: 0.25rem 0.5rem; margin-right: 0.25rem;">
                    ✏️ Edit
                  </button>
                  <button type="button" class="btn btn-ghost btn-sm text-danger" onclick="deleteShiftAudit(<?= (int)$ps['id'] ?>)" title="Delete Shift Audit Record" style="padding: 0.25rem 0.5rem;">
                    🗑️ Delete
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

<?php if ($activeRegister): ?>
<!-- MODAL: CASH IN / CASH OUT -->
<div class="pos-modal-overlay" id="cash-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 480px;">
    <div class="pos-modal-header">
      <div class="modal-title">Record Cash In / Cash Out</div>
      <button type="button" class="btn-close" onclick="closeCashModal()">&times;</button>
    </div>
    <form action="<?= site_url('admin/cash-drawer/transaction') ?>" method="POST">
      <?= csrf_field() ?>
      <input type="hidden" name="register_id" value="<?= (int)$activeRegister['id'] ?>">

      <div class="pos-modal-body">
        <div class="form-group">
          <label class="form-label" for="tx-type">Transaction Type <span class="form-required">*</span></label>
          <select name="type" id="tx-type" class="form-control" required>
            <option value="cash_in">📥 Cash In (Add Change / Float Top-up)</option>
            <option value="cash_out">📤 Cash Out (Petty Cash / Vendor Payout / Drop)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="tx-amount">Amount (₹) <span class="form-required">*</span></label>
          <input type="number" step="0.01" name="amount" id="tx-amount" class="form-control" required placeholder="e.g. 500.00">
        </div>

        <div class="form-group">
          <label class="form-label" for="tx-reason">Reason / Purpose <span class="form-required">*</span></label>
          <input type="text" name="reason" id="tx-reason" class="form-control" required placeholder="e.g. Paid ice vendor, Bank cash drop...">
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeCashModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Transaction</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: CLOSE SHIFT RECONCILIATION -->
<div class="pos-modal-overlay" id="close-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 500px;">
    <div class="pos-modal-header">
      <div class="modal-title">🔒 Close Shift &amp; Cash Reconciliation</div>
      <button type="button" class="btn-close" onclick="closeCloseModal()">&times;</button>
    </div>
    <form action="<?= site_url('admin/cash-drawer/close') ?>" method="POST">
      <?= csrf_field() ?>
      <input type="hidden" name="register_id" value="<?= (int)$activeRegister['id'] ?>">

      <div class="pos-modal-body">
        <div class="card mb-3" style="background: var(--surface);">
          <div class="card-body">
            <div class="d-flex justify-between text-small mb-1">
              <span class="text-muted">Expected Cash in Drawer:</span>
              <strong style="color: var(--warning); font-size: 1.1rem;">₹<?= esc(number_format((float)($summary['expected_cash'] ?? 0), 2)) ?></strong>
            </div>
            <div class="text-xs text-muted">Includes opening float, cash sales, plus deposits minus payouts</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="closing_cash_counted">Actual Physical Cash Counted (₹) <span class="form-required">*</span></label>
          <input type="number" step="0.01" name="closing_cash_counted" id="closing_cash_counted" class="form-control" required placeholder="Count till bills & coins" style="font-size: 1.25rem; font-weight: 700;">
          <div class="form-hint">Count all physical currency in the drawer before submitting</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="notes">Closing Notes / Discrepancy Reason</label>
          <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Explain any shortage, overage, or shift handoff notes..."></textarea>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeCloseModal()">Cancel</button>
        <button type="submit" class="btn btn-danger" onclick="return confirm('Confirm and close cash drawer shift?');">
          Lock &amp; Close Register
        </button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- MODAL: EDIT SHIFT AUDIT & NOTES -->
<div class="pos-modal-overlay" id="edit-shift-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 520px;">
    <div class="pos-modal-header">
      <div class="modal-title" id="edit-modal-title">✏️ Edit Shift Audit</div>
      <button type="button" class="btn-close" onclick="closeEditShiftModal()">&times;</button>
    </div>
    <form id="edit-shift-form" action="" method="POST">
      <?= csrf_field() ?>

      <div class="pos-modal-body">
        <!-- Shift Context Card -->
        <div class="card mb-3" style="background: var(--surface); padding: 0.85rem; border: 1px solid var(--border);">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.82rem;">
            <div>
              <span class="text-muted">Cashier:</span>
              <strong id="edit-shift-cashier" style="display: block; color: var(--text-primary);">—</strong>
            </div>
            <div>
              <span class="text-muted">Expected Cash:</span>
              <strong id="edit-shift-expected" style="display: block; color: var(--warning);">—</strong>
            </div>
            <div>
              <span class="text-muted">Opened:</span>
              <span id="edit-shift-opened" style="display: block; color: var(--text-secondary);">—</span>
            </div>
            <div>
              <span class="text-muted">Closed:</span>
              <span id="edit-shift-closed" style="display: block; color: var(--text-secondary);">—</span>
            </div>
          </div>
        </div>

        <!-- Notes field (Primary requested item) -->
        <div class="form-group mb-3">
          <label class="form-label" for="edit-shift-notes">
            Audit Notes / Justification
          </label>
          <textarea name="notes" id="edit-shift-notes" class="form-control" rows="3" placeholder="Enter reason for discrepancy, cash drop details, or audit notes..."></textarea>
          <div class="form-hint">Notes will be displayed in the shift reconciliation audit table.</div>
        </div>

        <!-- Counted Cash & Opening Float -->
        <div class="d-flex gap-2 mb-2">
          <div class="form-group" style="flex: 1;">
            <label class="form-label" for="edit-shift-counted">Counted Cash (₹)</label>
            <input type="number" step="0.01" name="closing_cash_counted" id="edit-shift-counted" class="form-control" oninput="calcEditDiscrepancy()">
          </div>
          <div class="form-group" style="flex: 1;">
            <label class="form-label" for="edit-shift-float">Opening Float (₹)</label>
            <input type="number" step="0.01" name="opening_float" id="edit-shift-float" class="form-control">
          </div>
        </div>

        <!-- Live Discrepancy Preview -->
        <div id="edit-shift-disc-preview" style="font-size: 0.82rem; padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); background: var(--surface-raised); border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
          <span class="text-muted">Calculated Discrepancy:</span>
          <strong id="edit-shift-disc-val">—</strong>
        </div>
      </div>

      <div class="pos-modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
        <button type="button" class="btn btn-ghost text-danger" id="edit-modal-delete-btn" onclick="deleteFromEditModal()" style="padding: 0.35rem 0.75rem;">
          🗑️ Delete Shift
        </button>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-secondary" onclick="closeEditShiftModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function showCashModal() {
  var m = document.getElementById('cash-modal');
  if (!m) return;
  m.hidden = false;
  m.style.setProperty('display', 'flex', 'important');
}
function closeCashModal() {
  var m = document.getElementById('cash-modal');
  if (!m) return;
  m.hidden = true;
  m.style.setProperty('display', 'none', 'important');
}
function showCloseModal() {
  var m = document.getElementById('close-modal');
  if (!m) return;
  m.hidden = false;
  m.style.setProperty('display', 'flex', 'important');
}
function closeCloseModal() {
  var m = document.getElementById('close-modal');
  if (!m) return;
  m.hidden = true;
  m.style.setProperty('display', 'none', 'important');
}

// ── Edit & Delete Shift Audits ──
let currentEditingShift = null;

function openEditShiftModal(shift) {
  currentEditingShift = shift;
  var modal = document.getElementById('edit-shift-modal');
  var form = document.getElementById('edit-shift-form');
  
  form.action = '<?= site_url('admin/cash-drawer/update') ?>/' + shift.id;
  document.getElementById('edit-modal-title').textContent = '✏️ Edit Shift Audit #' + shift.id;
  document.getElementById('edit-shift-cashier').textContent = shift.cashier_name || 'Staff';
  document.getElementById('edit-shift-expected').textContent = '₹' + parseFloat(shift.expected_cash || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  
  var openedStr = shift.opened_at ? shift.opened_at : '—';
  var closedStr = shift.closed_at ? shift.closed_at : '—';
  document.getElementById('edit-shift-opened').textContent = openedStr;
  document.getElementById('edit-shift-closed').textContent = closedStr;
  
  document.getElementById('edit-shift-notes').value = shift.notes || '';
  document.getElementById('edit-shift-counted').value = (shift.closing_cash_counted !== null && shift.closing_cash_counted !== undefined) ? parseFloat(shift.closing_cash_counted).toFixed(2) : '';
  document.getElementById('edit-shift-float').value = (shift.opening_float !== null && shift.opening_float !== undefined) ? parseFloat(shift.opening_float).toFixed(2) : '';
  
  calcEditDiscrepancy();

  modal.hidden = false;
  modal.style.setProperty('display', 'flex', 'important');
}

function closeEditShiftModal() {
  var modal = document.getElementById('edit-shift-modal');
  if (!modal) return;
  modal.hidden = true;
  modal.style.setProperty('display', 'none', 'important');
}

function calcEditDiscrepancy() {
  if (!currentEditingShift) return;
  var expected = parseFloat(currentEditingShift.expected_cash || 0);
  var countedInput = document.getElementById('edit-shift-counted');
  var valEl = document.getElementById('edit-shift-disc-val');
  
  if (!countedInput || countedInput.value === '') {
    valEl.textContent = '—';
    valEl.style.color = 'var(--text-muted)';
    return;
  }
  
  var counted = parseFloat(countedInput.value) || 0;
  var diff = counted - expected;
  
  if (Math.abs(diff) < 0.01) {
    valEl.textContent = 'Balanced (₹0.00)';
    valEl.style.color = 'var(--success, #10b981)';
  } else if (diff > 0) {
    valEl.textContent = '+₹' + diff.toFixed(2) + ' Overage';
    valEl.style.color = 'var(--success, #10b981)';
  } else {
    valEl.textContent = '-₹' + Math.abs(diff).toFixed(2) + ' Shortage';
    valEl.style.color = 'var(--danger, #ef4444)';
  }
}

function deleteShiftAudit(shiftId) {
  if (!confirm('Are you sure you want to delete Shift Audit #' + shiftId + '?\nThis will permanently remove this shift audit record and cannot be undone.')) {
    return;
  }
  
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = '<?= site_url('admin/cash-drawer/delete') ?>/' + shiftId;
  
  var csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = (window.RMS && window.RMS.csrfName) ? window.RMS.csrfName : '<?= csrf_token() ?>';
  csrfInput.value = (window.RMS && window.RMS.csrfHash) ? window.RMS.csrfHash : '<?= csrf_hash() ?>';
  form.appendChild(csrfInput);
  
  document.body.appendChild(form);
  form.submit();
}

function deleteFromEditModal() {
  if (!currentEditingShift) return;
  deleteShiftAudit(currentEditingShift.id);
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
  var modal = document.getElementById('edit-shift-modal');
  if (modal && e.target === modal) {
    closeEditShiftModal();
  }
});
</script>
