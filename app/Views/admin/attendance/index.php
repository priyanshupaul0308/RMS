<div class="page-header">
  <div>
    <h1 class="page-title">Staff Attendance &amp; Shifts</h1>
    <p class="page-subtitle">Track daily staff check-ins, shift schedules, overtime hours, and attendance logs</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
    <?php if ($isManagerOrAdmin): ?>
      <a href="<?= site_url('admin/attendance/reports') ?>" class="btn btn-outline">
        <i class="icon-reports"></i> Attendance Reports
      </a>
      <button type="button" class="btn btn-secondary" onclick="showShiftModal()">
        + New Shift
      </button>
      <button type="button" class="btn btn-secondary" onclick="showAssignModal()">
        + Assign Shift
      </button>
      <button type="button" class="btn btn-primary" onclick="showCheckInModal()">
        + Staff Check-In
      </button>
    <?php else: ?>
      <!-- Staff Direct Clock In / Clock Out -->
      <?php if (empty($myAttendance)): ?>
        <button type="button" class="btn btn-success" onclick="openQuickClockModal()" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);">
          🟢 Clock In Now
        </button>
      <?php elseif (empty($myAttendance['check_out'])): ?>
        <button type="button" class="btn btn-danger" onclick="doQuickClockOut()" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);">
          🔴 Clock Out (On Duty)
        </button>
      <?php else: ?>
        <span class="badge badge-success" style="font-size: 0.85rem; padding: 0.45rem 0.85rem;">
          ✔️ Shift Completed (<?= esc($myAttendance['total_hours']) ?>h)
        </span>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- PERSONAL ATTENDANCE & SHIFT BANNER -->
<div class="card mb-4" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95)); border: 1px solid var(--border); box-shadow: var(--shadow);">
  <div class="card-body" style="padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.2px; color: var(--accent, #6366f1); font-weight: 700; margin-bottom: 0.25rem;">
        Personal Work Station &bull; <?= esc(ucfirst($currentRoleSlug ?? 'Staff')) ?>
      </div>
      <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--text-primary);">
        <?= esc(($currentUser['first_name'] ?? 'Staff') . ' ' . ($currentUser['last_name'] ?? '')) ?>
      </h3>
      <div style="color: var(--text-secondary); font-size: 0.88rem; margin-top: 0.35rem; display: flex; gap: 1.25rem; flex-wrap: wrap;">
        <span><strong>Assigned Shift:</strong> <?= !empty($myAssignedShift['shift_name']) ? esc($myAssignedShift['shift_name']) . ' (' . date('h:i A', strtotime('2000-01-01 ' . $myAssignedShift['start_time'])) . ' - ' . date('h:i A', strtotime('2000-01-01 ' . $myAssignedShift['end_time'])) . ')' : '<span class="text-muted">General / Unscheduled</span>' ?></span>
        <span>
          <strong>Today's Status:</strong> 
          <?php if (empty($myAttendance)): ?>
            <span class="badge badge-warning" style="margin-left: 0.25rem;">Not Clocked In</span>
          <?php elseif (empty($myAttendance['check_out'])): ?>
            <span class="badge badge-success" style="margin-left: 0.25rem;">On Duty since <?= esc(date('h:i A', strtotime($myAttendance['check_in']))) ?></span>
          <?php else: ?>
            <span class="badge badge-secondary" style="margin-left: 0.25rem;">Clocked Out at <?= esc(date('h:i A', strtotime($myAttendance['check_out']))) ?></span>
          <?php endif; ?>
        </span>
      </div>
    </div>
    <div>
      <?php if (empty($myAttendance)): ?>
        <button type="button" class="btn btn-success" onclick="openQuickClockModal()" style="font-size: 0.95rem; font-weight: 700; padding: 0.6rem 1.25rem; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);">
          ⏱️ Clock In for Shift
        </button>
      <?php elseif (empty($myAttendance['check_out'])): ?>
        <button type="button" class="btn btn-danger" onclick="doQuickClockOut()" style="font-size: 0.95rem; font-weight: 700; padding: 0.6rem 1.25rem; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);">
          🔴 Clock Out (End Shift)
        </button>
      <?php else: ?>
        <button type="button" class="btn btn-outline" disabled style="opacity: 0.7;">
          ✔️ Shift Completed (<?= esc($myAttendance['total_hours']) ?> hrs)
        </button>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- KPI STATS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Checked In Today</div>
    <div class="stat-value text-primary"><?= esc($checkedInCount) ?></div>
    <div class="stat-sub">Active staff on duty</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Late Arrivals</div>
    <div class="stat-value text-warning"><?= esc($lateCount) ?></div>
    <div class="stat-sub">Past scheduled grace window</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Logged Hours Today</div>
    <div class="stat-value text-success"><?= esc($totalHoursToday) ?> hrs</div>
    <div class="stat-sub">Across all shifts</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Overtime Hours</div>
    <div class="stat-value text-info"><?= esc($totalOtToday) ?> hrs</div>
    <div class="stat-sub">Excess of 8 hrs/shift</div>
  </div>
</div>

<!-- SHIFT TILES -->
<div class="card mb-4">
  <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div class="card-title">Configured Work Shifts</div>
  </div>
  <div class="card-body">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
      <?php if (empty($shifts)): ?>
        <p class="text-muted">No shifts configured yet. <?= $isManagerOrAdmin ? 'Click "+ New Shift" to create your first shift schedule.' : '' ?></p>
      <?php else: ?>
        <?php foreach ($shifts as $s): ?>
          <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-left: 4px solid <?= esc($s['color_code']) ?>; border-radius: 8px; padding: 1rem;">
            <div style="font-weight: 600; font-size: 1.05rem; margin-bottom: 0.25rem;"><?= esc($s['name']) ?></div>
            <div style="color: var(--text-muted, #94a3b8); font-size: 0.9rem; margin-bottom: 0.5rem;">
              <?= esc(date('h:i A', strtotime('2000-01-01 ' . $s['start_time']))) ?> - <?= esc(date('h:i A', strtotime('2000-01-01 ' . $s['end_time']))) ?>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
              <span>Break: <?= esc($s['break_duration_mins']) ?> mins</span>
              <span class="badge badge-success">Active</span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- TODAY'S ATTENDANCE LOG -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Today's Attendance Roster (<?= esc(date('M d, Y', strtotime($today))) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Staff Member</th>
            <th>Shift Assigned</th>
            <th>Check-In Time</th>
            <th>Check-Out Time</th>
            <th>Total Hours</th>
            <th>Overtime</th>
            <th>Status</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($todayAttendance)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No attendance entries logged for today yet. Use Clock In to record your attendance.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($todayAttendance as $att): ?>
              <tr>
                <td>
                  <strong><?= esc($att['first_name'] . ' ' . $att['last_name']) ?></strong>
                  <div style="font-size: 0.8rem; color: var(--text-muted, #94a3b8);"><?= esc($att['email']) ?></div>
                </td>
                <td>
                  <?= esc($att['shift_name'] ?? 'General / Unscheduled') ?>
                </td>
                <td>
                  <?= esc(date('h:i:s A', strtotime($att['check_in']))) ?>
                </td>
                <td>
                  <?= !empty($att['check_out']) ? esc(date('h:i:s A', strtotime($att['check_out']))) : '<span class="badge badge-info">On Duty</span>' ?>
                </td>
                <td>
                  <strong><?= esc($att['total_hours']) ?>h</strong>
                </td>
                <td>
                  <?= (float)$att['overtime_hours'] > 0 ? '<span class="badge badge-warning">+' . esc($att['overtime_hours']) . 'h</span>' : '<span class="text-muted">0h</span>' ?>
                </td>
                <td>
                  <?php if ($att['status'] === 'present'): ?>
                    <span class="badge badge-success">Present</span>
                  <?php elseif ($att['status'] === 'late'): ?>
                    <span class="badge badge-warning">Late Arrival</span>
                  <?php else: ?>
                    <span class="badge badge-info"><?= esc(ucfirst($att['status'])) ?></span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <?php if (empty($att['check_out'])): ?>
                    <?php if ($isManagerOrAdmin || (int)$att['user_id'] === (int)$currentUserId): ?>
                      <button type="button" class="btn btn-outline btn-sm" onclick="performCheckOut(<?= (int)$att['id'] ?>, '<?= esc($att['first_name']) ?>')">
                        Check-Out
                      </button>
                    <?php else: ?>
                      <span class="badge badge-info" style="font-size: 0.75rem;">On Duty</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="text-muted" style="font-size: 0.85rem;">Completed</span>
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

<?php if ($isManagerOrAdmin): ?>
<!-- MODAL: CHECK-IN STAFF -->
<div class="pos-modal-overlay" id="checkin-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 500px;">
    <div class="pos-modal-header">
      <div class="modal-title">Record Staff Check-In</div>
      <button type="button" class="btn-close" onclick="closeModal('checkin-modal')">&times;</button>
    </div>
    <form id="checkin-form" onsubmit="submitCheckIn(event)">
      <?= csrf_field() ?>
      <div class="pos-modal-body">
        <div class="form-group mb-3">
          <label class="form-label" for="ci-user">Select Staff Member <span class="form-required">*</span></label>
          <select id="ci-user" name="user_id" class="form-control" required>
            <option value="">-- Choose Employee --</option>
            <?php foreach ($staffMembers as $u): ?>
              <option value="<?= (int)$u['id'] ?>" <?= ((int)$u['id'] === (int)($currentUserId ?? 0)) ? 'selected' : '' ?>>
                <?= esc($u['first_name'] . ' ' . $u['last_name']) ?> (<?= esc($u['email']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="ci-shift">Assigned Shift (Optional)</label>
          <select id="ci-shift" name="shift_id" class="form-control">
            <option value="">-- Auto-detect / General --</option>
            <?php foreach ($shifts as $s): ?>
              <option value="<?= (int)$s['id'] ?>"><?= esc($s['name']) ?> (<?= esc(date('h:i A', strtotime('2000-01-01 ' . $s['start_time']))) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('checkin-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-checkin">Record Check-In</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: NEW SHIFT -->
<div class="pos-modal-overlay" id="shift-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 520px;">
    <div class="pos-modal-header">
      <div class="modal-title">Create Work Shift</div>
      <button type="button" class="btn-close" onclick="closeModal('shift-modal')">&times;</button>
    </div>
    <form id="shift-form" onsubmit="submitShift(event)">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="sf-id" value="">
      <div class="pos-modal-body">
        <div class="form-group mb-3">
          <label class="form-label" for="sf-name">Shift Name <span class="form-required">*</span></label>
          <input type="text" id="sf-name" name="name" class="form-control" required placeholder="e.g. Lunch Rush, Late Night Closing">
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="sf-start">Start Time <span class="form-required">*</span></label>
              <input type="time" id="sf-start" name="start_time" class="form-control" required>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="sf-end">End Time <span class="form-required">*</span></label>
              <input type="time" id="sf-end" name="end_time" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="sf-break">Break Duration (Mins)</label>
              <input type="number" id="sf-break" name="break_duration_mins" class="form-control" value="30" min="0" max="180">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="sf-color">Color Badge</label>
              <input type="color" id="sf-color" name="color_code" class="form-control" value="#3b82f6" style="height: 42px; padding: 2px;">
            </div>
          </div>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('shift-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-shift">Save Shift</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: ASSIGN SHIFT -->
<div class="pos-modal-overlay" id="assign-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 500px;">
    <div class="pos-modal-header">
      <div class="modal-title">Assign Shift to Staff</div>
      <button type="button" class="btn-close" onclick="closeModal('assign-modal')">&times;</button>
    </div>
    <form id="assign-form" onsubmit="submitAssign(event)">
      <?= csrf_field() ?>
      <div class="pos-modal-body">
        <div class="form-group mb-3">
          <label class="form-label" for="as-user">Staff Member <span class="form-required">*</span></label>
          <select id="as-user" name="user_id" class="form-control" required>
            <option value="">-- Choose Employee --</option>
            <?php foreach ($staffMembers as $u): ?>
              <option value="<?= (int)$u['id'] ?>" <?= ((int)$u['id'] === (int)($currentUserId ?? 0)) ? 'selected' : '' ?>>
                <?= esc($u['first_name'] . ' ' . $u['last_name']) ?> (<?= esc($u['email']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="as-shift">Shift <span class="form-required">*</span></label>
          <select id="as-shift" name="shift_id" class="form-control" required>
            <option value="">-- Choose Shift --</option>
            <?php foreach ($shifts as $s): ?>
              <option value="<?= (int)$s['id'] ?>"><?= esc($s['name']) ?> (<?= esc(date('h:i A', strtotime('2000-01-01 ' . $s['start_time']))) ?> - <?= esc(date('h:i A', strtotime('2000-01-01 ' . $s['end_time']))) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="as-date">Shift Date <span class="form-required">*</span></label>
          <input type="date" id="as-date" name="shift_date" class="form-control" value="<?= esc($today) ?>" required>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="as-notes">Notes / Instructions</label>
          <input type="text" id="as-notes" name="notes" class="form-control" placeholder="Optional notes for employee">
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('assign-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-assign">Assign Shift</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

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

function showCheckInModal() { showModal('checkin-modal'); }
function showShiftModal() { showModal('shift-modal'); }
function showAssignModal() { showModal('assign-modal'); }

async function submitCheckIn(e) {
  e.preventDefault();
  const form = document.getElementById('checkin-form');
  const btn = document.getElementById('btn-submit-checkin');
  const origText = btn.innerText;
  btn.disabled = true;
  btn.innerText = 'Checking In...';

  try {
    const res = await fetch('<?= site_url('admin/attendance/check-in') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (parseErr) {
      alert('Server error: ' + (text ? text.substring(0, 150) : res.statusText));
      btn.disabled = false;
      btn.innerText = origText;
      return;
    }

    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || (data.errors ? Object.values(data.errors).join('\n') : 'Error recording check-in'));
      btn.disabled = false;
      btn.innerText = origText;
    }
  } catch(err) {
    console.error('Check-in error:', err);
    alert('Network error: ' + (err.message || 'Unable to connect to server.'));
    btn.disabled = false;
    btn.innerText = origText;
  }
}

async function performCheckOut(id, name) {
  if (!confirm(`Are you sure you want to check out ${name}?`)) return;

  try {
    const fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res = await fetch(`<?= site_url('admin/attendance/check-out') ?>/${id}`, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (parseErr) {
      alert('Server error: ' + (text ? text.substring(0, 150) : res.statusText));
      return;
    }

    if (data.status === 'success') {
      alert(data.message);
      window.location.reload();
    } else {
      alert(data.message || 'Check-out failed');
    }
  } catch(err) {
    console.error('Check-out error:', err);
    alert('Network error: ' + (err.message || 'Unable to connect to server.'));
  }
}

async function submitShift(e) {
  e.preventDefault();
  const form = document.getElementById('shift-form');
  const btn = document.getElementById('btn-submit-shift');
  const origText = btn.innerText;
  btn.disabled = true;
  btn.innerText = 'Saving...';

  try {
    const res = await fetch('<?= site_url('admin/attendance/shifts/save') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (parseErr) {
      alert('Server error: ' + (text ? text.substring(0, 150) : res.statusText));
      btn.disabled = false;
      btn.innerText = origText;
      return;
    }

    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || (data.errors ? Object.values(data.errors).join('\n') : 'Error saving shift'));
      btn.disabled = false;
      btn.innerText = origText;
    }
  } catch(err) {
    console.error('Save shift error:', err);
    alert('Network error: ' + (err.message || 'Unable to connect to server.'));
    btn.disabled = false;
    btn.innerText = origText;
  }
}

async function submitAssign(e) {
  e.preventDefault();
  const form = document.getElementById('assign-form');
  const btn = document.getElementById('btn-submit-assign');
  const origText = btn.innerText;
  btn.disabled = true;
  btn.innerText = 'Assigning...';

  try {
    const res = await fetch('<?= site_url('admin/attendance/shifts/assign') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch (parseErr) {
      alert('Server error: ' + (text ? text.substring(0, 150) : res.statusText));
      btn.disabled = false;
      btn.innerText = origText;
      return;
    }

    if (data.status === 'success') {
      alert(data.message || 'Shift assigned successfully!');
      window.location.reload();
    } else {
      alert(data.message || (data.errors ? Object.values(data.errors).join('\n') : 'Error assigning shift'));
      btn.disabled = false;
      btn.innerText = origText;
    }
  } catch(err) {
    console.error('Assign shift error:', err);
    alert('Network error: ' + (err.message || 'Unable to connect to server.'));
    btn.disabled = false;
    btn.innerText = origText;
  }
}
</script>
