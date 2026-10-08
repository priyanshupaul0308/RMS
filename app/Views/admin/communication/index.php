<div class="page-header">
  <div>
    <h1 class="page-title">Notification &amp; Communication Center</h1>
    <p class="page-subtitle">Multi-channel guest alerts, order updates, reservation reminders, low-stock warnings, and staff broadcasts</p>
  </div>
  <div class="page-actions" style="display: flex; gap: 0.5rem;">
    <button type="button" class="btn btn-secondary" onclick="markAllNotificationsRead()">
      &#10003; Mark All Read
    </button>
    <button type="button" class="btn btn-primary" onclick="showCommModal()">
      + Dispatch Message
    </button>
  </div>
</div>

<!-- KPI CATEGORY METRICS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Order Updates</div>
    <div class="stat-value text-primary"><?= esc($orderCount) ?></div>
    <div class="stat-sub">Ready, takeaway, delivery alerts</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Reservation Reminders</div>
    <div class="stat-value text-info"><?= esc($resCount) ?></div>
    <div class="stat-sub">Confirmations &amp; table bookings</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Low-Stock Warnings</div>
    <div class="stat-value text-warning"><?= esc($stockCount) ?></div>
    <div class="stat-sub">Inventory below reorder level</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Staff Broadcasts</div>
    <div class="stat-value text-success"><?= esc($staffCount) ?></div>
    <div class="stat-sub">Team memos &amp; operational shifts</div>
  </div>
</div>

<!-- FILTER TABS -->
<div class="card mb-4">
  <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <a href="<?= site_url('admin/communication') ?>" class="btn <?= $category === 'all' ? 'btn-primary' : 'btn-ghost' ?> btn-sm">
        All Categories
      </a>
      <a href="<?= site_url('admin/communication?category=order') ?>" class="btn <?= $category === 'order' ? 'btn-primary' : 'btn-ghost' ?> btn-sm">
        Orders
      </a>
      <a href="<?= site_url('admin/communication?category=reservation') ?>" class="btn <?= $category === 'reservation' ? 'btn-primary' : 'btn-ghost' ?> btn-sm">
        Reservations
      </a>
      <a href="<?= site_url('admin/communication?category=payment') ?>" class="btn <?= $category === 'payment' ? 'btn-primary' : 'btn-ghost' ?> btn-sm">
        Payments
      </a>
      <a href="<?= site_url('admin/communication?category=low_stock') ?>" class="btn <?= $category === 'low_stock' ? 'btn-primary' : 'btn-ghost' ?> btn-sm">
        Low Stock
      </a>
      <a href="<?= site_url('admin/communication?category=announcement') ?>" class="btn <?= $category === 'announcement' ? 'btn-primary' : 'btn-ghost' ?> btn-sm">
        Staff Memos
      </a>
      <a href="<?= site_url('admin/communication?category=promo') ?>" class="btn <?= $category === 'promo' ? 'btn-primary' : 'btn-ghost' ?> btn-sm">
        Promotions
      </a>
    </div>

    <div style="display: flex; align-items: center; gap: 0.5rem;">
      <label class="form-label" style="margin-bottom:0;" for="comm-channel">Channel:</label>
      <select id="comm-channel" class="form-control pos-input-sm" onchange="window.location.href='<?= site_url('admin/communication?category=' . esc($category) . '&channel=') ?>' + this.value">
        <option value="all" <?= $channel === 'all' ? 'selected' : '' ?>>All Channels</option>
        <option value="in_app" <?= $channel === 'in_app' ? 'selected' : '' ?>>In-App</option>
        <option value="whatsapp" <?= $channel === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
        <option value="sms" <?= $channel === 'sms' ? 'selected' : '' ?>>SMS</option>
        <option value="email" <?= $channel === 'email' ? 'selected' : '' ?>>Email</option>
      </select>
    </div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
  <!-- COMMUNICATION DISPATCH LOGS -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Dispatched Communications (<?= count($logs) ?>)</div>
    </div>
    <div class="card-body" style="padding: 0;">
      <div class="table-wrapper">
        <table class="rms-table">
          <thead>
            <tr>
              <th>Timestamp</th>
              <th>Recipient</th>
              <th>Channel</th>
              <th>Category</th>
              <th>Subject &amp; Message</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($logs)): ?>
              <tr>
                <td colspan="6" class="text-center text-muted" style="padding: 3rem 1rem;">
                  No communication logs found for this filter criteria.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($logs as $l): ?>
                <tr>
                  <td style="font-size: 0.8rem; white-space: nowrap;">
                    <?= esc(date('M d, h:i A', strtotime($l['sent_at']))) ?>
                  </td>
                  <td>
                    <strong><?= esc($l['recipient_name']) ?></strong>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= esc($l['recipient_contact']) ?></div>
                  </td>
                  <td>
                    <?php if ($l['channel'] === 'whatsapp'): ?>
                      <span class="badge" style="background: rgba(34,197,94,0.15); color: #22c55e;">WhatsApp</span>
                    <?php elseif ($l['channel'] === 'sms'): ?>
                      <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa;">SMS</span>
                    <?php elseif ($l['channel'] === 'email'): ?>
                      <span class="badge" style="background: rgba(168,85,247,0.15); color: #c084fc;">Email</span>
                    <?php else: ?>
                      <span class="badge badge-info">In-App</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge badge-secondary" style="text-transform: capitalize;">
                      <?= esc(str_replace('_', ' ', $l['category'])) ?>
                    </span>
                  </td>
                  <td>
                    <strong><?= esc($l['subject']) ?></strong>
                    <div style="font-size: 0.8rem; color: #94a3b8; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                      <?= esc($l['message']) ?>
                    </div>
                  </td>
                  <td>
                    <span class="badge badge-success">&#10003; Delivered</span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- IN-APP NOTIFICATIONS DRAWER -->
  <div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="card-title">Recent In-App Alerts</div>
      <?php if ($unreadCount > 0): ?>
        <span class="badge badge-warning"><?= esc($unreadCount) ?> Unread</span>
      <?php else: ?>
        <span class="badge badge-success">All Read</span>
      <?php endif; ?>
    </div>
    <div class="card-body" style="padding: 0;">
      <?php if (empty($inAppNotifs)): ?>
        <div style="padding: 2rem 1rem; text-align: center; color: var(--text-muted);">
          No in-app alerts at the moment.
        </div>
      <?php else: ?>
        <div style="display: flex; flex-direction: column;">
          <?php foreach ($inAppNotifs as $notif): ?>
            <div style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); <?= empty($notif['read_at']) ? 'background: rgba(59,130,246,0.05);' : '' ?>">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                <strong style="font-size: 0.9rem;"><?= esc($notif['title']) ?></strong>
                <span style="font-size: 0.75rem; color: var(--text-muted);"><?= esc(date('h:i A', strtotime($notif['created_at']))) ?></span>
              </div>
              <p style="margin: 0; font-size: 0.8rem; color: #94a3b8; line-height: 1.4;">
                <?= esc($notif['body']) ?>
              </p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- MODAL: DISPATCH COMMUNICATION -->
<div class="pos-modal-overlay" id="comm-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 540px;">
    <div class="pos-modal-header">
      <div class="modal-title">Dispatch Communication / Alert</div>
      <button type="button" class="btn-close" onclick="closeModal('comm-modal')">&times;</button>
    </div>
    <form id="comm-form" onsubmit="submitComm(event)">
      <?= csrf_field() ?>
      <div class="pos-modal-body">
        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cm-rtype">Target Audience <span class="form-required">*</span></label>
              <select id="cm-rtype" name="recipient_type" class="form-control" required>
                <option value="customer">Individual Customer</option>
                <option value="staff">Staff Member</option>
                <option value="broadcast">All Branch Staff (Broadcast)</option>
              </select>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cm-cat">Alert Category <span class="form-required">*</span></label>
              <select id="cm-cat" name="category" class="form-control" required>
                <option value="order">Order Notification</option>
                <option value="reservation">Reservation Notification</option>
                <option value="payment">Payment &amp; Receipt</option>
                <option value="low_stock">Low-Stock Alert</option>
                <option value="announcement">Staff Announcement</option>
                <option value="promo">Promotional Offer</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cm-name">Recipient Name <span class="form-required">*</span></label>
              <input type="text" id="cm-name" name="recipient_name" class="form-control" required placeholder="e.g. Vikram Malhotra / Kitchen Team">
            </div>
          </div>
          <div class="form-col">
            <div class="form-group mb-3">
              <label class="form-label" for="cm-contact">Phone / Email <span class="form-required">*</span></label>
              <input type="text" id="cm-contact" name="recipient_contact" class="form-control" required placeholder="+91 9876543210 or staff@rms.local">
            </div>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="cm-channel-input">Delivery Channel <span class="form-required">*</span></label>
          <select id="cm-channel-input" name="channel" class="form-control" required>
            <option value="whatsapp">WhatsApp Message</option>
            <option value="sms">SMS Text</option>
            <option value="in_app">In-App Notification</option>
            <option value="email">Email</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="cm-sub">Subject / Title <span class="form-required">*</span></label>
          <input type="text" id="cm-sub" name="subject" class="form-control" required placeholder="e.g. Order #ORD-1029 is Ready for Pickup!">
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="cm-msg">Message Content <span class="form-required">*</span></label>
          <textarea id="cm-msg" name="message" class="form-control" rows="3" required placeholder="Enter message text..."></textarea>
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('comm-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-comm">Send Message</button>
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

function showCommModal() {
  document.getElementById('comm-form').reset();
  showModal('comm-modal');
}

async function submitComm(e) {
  e.preventDefault();
  const form = document.getElementById('comm-form');
  const btn = document.getElementById('btn-submit-comm');
  btn.disabled = true;
  btn.innerText = 'Dispatching...';

  try {
    const res = await fetch('<?= site_url('admin/communication/send') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    } else {
      alert(data.message || 'Error dispatching message');
      btn.disabled = false;
      btn.innerText = 'Send Message';
    }
  } catch(err) {
    alert('Network error.');
    btn.disabled = false;
    btn.innerText = 'Send Message';
  }
}

async function markAllNotificationsRead() {
  try {
    const fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res = await fetch('<?= site_url('admin/communication/mark-all-read') ?>', {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success') {
      window.location.reload();
    }
  } catch(err) {
    alert('Network error.');
  }
}
</script>
