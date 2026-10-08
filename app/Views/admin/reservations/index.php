<div class="page-header">
  <div>
    <h1 class="page-title">Table Reservations</h1>
    <p class="page-subtitle">Manage guest bookings, seating schedules, and special party requests</p>
  </div>
  <div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="showBookingModal()">
      <span>+</span> Book a Table
    </button>
  </div>
</div>

<!-- DATE & STATUS FILTER -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="<?= site_url('admin/reservations') ?>" class="d-flex align-center gap-2 flex-wrap">
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="res-date">Date:</label>
        <input type="date" id="res-date" name="date" class="form-control pos-input-sm" value="<?= esc($currentDate) ?>" onchange="this.form.submit()">
      </div>
      <div class="d-flex align-center gap-1">
        <label class="form-label" style="margin-bottom:0;" for="res-status">Status:</label>
        <select id="res-status" name="status" class="form-control pos-input-sm" onchange="this.form.submit()">
          <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Statuses</option>
          <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
          <option value="seated" <?= $status === 'seated' ? 'selected' : '' ?>>Seated</option>
          <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
          <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
      </div>
      <a href="<?= site_url('admin/reservations?date=' . date('Y-m-d')) ?>" class="btn btn-secondary btn-sm">Today</a>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Bookings for <?= esc(date('D, M d, Y', strtotime($currentDate))) ?> (<?= count($reservations) ?>)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Guest Name</th>
            <th>Contact</th>
            <th>Party Size</th>
            <th>Assigned Table</th>
            <th>Special Requests</th>
            <th>Status</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reservations)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No reservations scheduled for this date. Click &ldquo;Book a Table&rdquo; to create a new reservation.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($reservations as $r): ?>
              <tr>
                <td class="td-bold" style="color: var(--primary);">
                  <?= esc(date('h:i A', strtotime($r['reservation_time']))) ?>
                </td>
                <td class="td-bold">
                  <?= esc($r['customer_name']) ?>
                </td>
                <td>
                  <div class="text-small"><?= esc($r['customer_phone']) ?></div>
                  <div class="text-xs text-muted"><?= esc($r['customer_email'] ?: '') ?></div>
                </td>
                <td>
                  <span class="badge badge-secondary">👥 <?= (int)$r['guest_count'] ?> Guests</span>
                </td>
                <td>
                  <?php if (!empty($r['table_number'])): ?>
                    <strong><?= esc($r['table_number']) ?></strong>
                    <div class="text-xs text-muted"><?= esc($r['floor_name'] ?? '') ?></div>
                  <?php else: ?>
                    <span class="text-muted text-xs">Unassigned</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?= esc($r['special_requests'] ?: '—') ?>
                </td>
                <td>
                  <?php
                    $sClass = 'status-pending';
                    if ($r['status'] === 'seated') $sClass = 'status-active';
                    elseif ($r['status'] === 'confirmed') $sClass = 'status-info';
                    elseif ($r['status'] === 'cancelled') $sClass = 'status-danger';
                  ?>
                  <span class="status-badge <?= $sClass ?>">
                    <?= esc(ucfirst($r['status'])) ?>
                  </span>
                </td>
                <td style="text-align: right;">
                  <div class="td-actions" style="justify-content: flex-end;">
                    <?php if ($r['status'] === 'confirmed'): ?>
                      <form action="<?= site_url('admin/reservations/seat/' . $r['id']) ?>" method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success btn-sm" title="Seat guest and open POS">
                          ✨ Seat Guest
                        </button>
                      </form>
                      <form action="<?= site_url('admin/reservations/cancel/' . $r['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Cancel this reservation?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-ghost btn-sm text-danger" title="Cancel booking">
                          Cancel
                        </button>
                      </form>
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

<!-- BOOKING MODAL -->
<div class="pos-modal-overlay" id="booking-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 600px;">
    <div class="pos-modal-header">
      <div class="modal-title">Book Table Reservation</div>
      <button type="button" class="btn-close" onclick="closeBookingModal()">&times;</button>
    </div>
    <form action="<?= site_url('admin/reservations/store') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="pos-modal-body">
        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="customer_name">Guest Full Name <span class="form-required">*</span></label>
              <input type="text" name="customer_name" id="customer_name" class="form-control" required placeholder="e.g. Vikram Malhotra">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="customer_phone">Phone Number <span class="form-required">*</span></label>
              <input type="tel" name="customer_phone" id="customer_phone" class="form-control" required placeholder="+91 9876543210">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="customer_email">Email Address</label>
              <input type="email" name="customer_email" id="customer_email" class="form-control" placeholder="guest@example.com">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="guest_count">Party Size / Guests <span class="form-required">*</span></label>
              <input type="number" name="guest_count" id="guest_count" class="form-control" min="1" max="50" value="2" required>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="reservation_date">Date <span class="form-required">*</span></label>
              <input type="date" name="reservation_date" id="reservation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="reservation_time">Time Slot <span class="form-required">*</span></label>
              <input type="time" name="reservation_time" id="reservation_time" class="form-control" value="19:30" required>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="table_id">Assign Table</label>
          <select name="table_id" id="table_id" class="form-control">
            <option value="">Auto-Assign / At Door</option>
            <?php foreach ($tables as $t): ?>
              <option value="<?= (int)$t['id'] ?>">
                <?= esc($t['table_number']) ?> (<?= (int)$t['seating_capacity'] ?> Seats, <?= esc(ucfirst($t['status'])) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="special_requests">Special Requests / Occasion</label>
          <textarea name="special_requests" id="special_requests" class="form-control" rows="2" placeholder="Birthday, anniversary, high chair, window booth..."></textarea>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeBookingModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Confirm Reservation</button>
      </div>
    </form>
  </div>
</div>

<script>
function showBookingModal() {
  var m = document.getElementById('booking-modal');
  m.hidden = false;
  m.style.setProperty('display', 'flex', 'important');
}
function closeBookingModal() {
  var m = document.getElementById('booking-modal');
  m.hidden = true;
  m.style.setProperty('display', 'none', 'important');
}
</script>
