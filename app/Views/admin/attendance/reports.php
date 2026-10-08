<div class="page-header">
  <div>
    <h1 class="page-title">Attendance & Overtime Reports</h1>
    <p class="page-subtitle">Historical staff logs, hours worked, and overtime tracking</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <a href="<?= site_url('admin/attendance') ?>" class="btn btn-secondary">
      &larr; Back to Attendance
    </a>
    <a href="<?= site_url('admin/attendance/reports?start_date=' . esc($startDate) . '&end_date=' . esc($endDate) . '&export=csv') ?>" class="btn btn-primary">
      Export CSV
    </a>
  </div>
</div>

<!-- DATE FILTER -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/attendance/reports') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date" class="form-control pos-input-sm" value="<?= esc($startDate) ?>">
      </div>
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date" class="form-control pos-input-sm" value="<?= esc($endDate) ?>">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      <a href="<?= site_url('admin/attendance/reports?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-ghost btn-sm">This Month</a>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Attendance History (<?= count($records) ?> logs)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Employee</th>
            <th>Shift</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Total Hours</th>
            <th>Overtime</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($records)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No attendance logs found for the selected date range.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($records as $r): ?>
              <tr>
                <td><strong><?= esc($r['date']) ?></strong></td>
                <td>
                  <strong><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></strong>
                  <div style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($r['email']) ?></div>
                </td>
                <td><?= esc($r['shift_name'] ?? 'Regular') ?></td>
                <td><?= esc(date('h:i A', strtotime($r['check_in']))) ?></td>
                <td><?= !empty($r['check_out']) ? esc(date('h:i A', strtotime($r['check_out']))) : '<span class="badge badge-info">On Duty</span>' ?></td>
                <td><strong><?= esc($r['total_hours']) ?>h</strong></td>
                <td><?= (float)$r['overtime_hours'] > 0 ? '<span class="badge badge-warning">+' . esc($r['overtime_hours']) . 'h</span>' : '<span class="text-muted">0h</span>' ?></td>
                <td>
                  <?php if ($r['status'] === 'present'): ?>
                    <span class="badge badge-success">Present</span>
                  <?php elseif ($r['status'] === 'late'): ?>
                    <span class="badge badge-warning">Late</span>
                  <?php else: ?>
                    <span class="badge badge-info"><?= esc(ucfirst($r['status'])) ?></span>
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
