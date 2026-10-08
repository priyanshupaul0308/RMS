<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">⭐ Guest Feedback &amp; Reviews</h1>
    <p class="page-subtitle">Diner satisfaction ratings, culinary scores, service speed, and guest comments</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" onclick="openModal('addFeedbackModal')">
      ➕ Log Guest Review
    </button>
  </div>
</div>

<!-- Scorecards -->
<div class="kpi-grid mb-3">
  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">⭐</div>
    <div class="kpi-body">
      <div class="kpi-label">Overall Experience</div>
      <div class="kpi-value text-warning"><?= number_format($metrics['overall'], 1) ?> / 5.0</div>
      <div class="kpi-trend text-muted text-xs">Based on <?= $metrics['total_reviews'] ?> guest reviews</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">🍕</div>
    <div class="kpi-body">
      <div class="kpi-label">Food &amp; Taste</div>
      <div class="kpi-value"><?= number_format($metrics['food'], 1) ?> / 5.0</div>
      <div class="kpi-trend text-muted text-xs">Flavor, temperature &amp; presentation</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">⚡</div>
    <div class="kpi-body">
      <div class="kpi-label">Service Speed</div>
      <div class="kpi-value"><?= number_format($metrics['service'], 1) ?> / 5.0</div>
      <div class="kpi-trend text-muted text-xs">Staff attentiveness &amp; warmth</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">🕯️</div>
    <div class="kpi-body">
      <div class="kpi-label">Dining Ambience</div>
      <div class="kpi-value"><?= number_format($metrics['ambience'], 1) ?> / 5.0</div>
      <div class="kpi-trend text-muted text-xs">Music, lighting &amp; cleanliness</div>
    </div>
  </div>
</div>

<!-- Reviews Table -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Guest Feedback Timeline</div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Diner</th>
          <th>Overall Rating</th>
          <th>Food</th>
          <th>Service</th>
          <th>Ambience</th>
          <th>Comments</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($feedbackList)): ?>
        <tr>
          <td colspan="7" class="text-center py-4 text-muted">No guest reviews recorded yet.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($feedbackList as $fb): ?>
        <tr>
          <td><?= esc(date('M d, Y', strtotime($fb['created_at']))) ?></td>
          <td>
            <div class="fw-700"><?= esc($fb['customer_name']) ?></div>
            <div class="text-muted text-xs"><?= esc($fb['customer_phone'] ?? '') ?></div>
          </td>
          <td>
            <span class="status-badge status-warning fw-700">
              ⭐ <?= (int)$fb['rating'] ?> / 5
            </span>
          </td>
          <td><?= (int)$fb['food_rating'] ?> / 5</td>
          <td><?= (int)$fb['service_rating'] ?> / 5</td>
          <td><?= (int)$fb['ambience_rating'] ?> / 5</td>
          <td class="text-muted text-small" style="max-width: 320px;">
            <?= esc($fb['comments'] ?? 'No written comment') ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: Add Feedback -->
<div id="addFeedbackModal" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
  <div class="card" style="width: 500px; max-width: 95%;">
    <div class="card-header d-flex justify-between align-center">
      <div class="card-title">➕ Log Guest Feedback</div>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('addFeedbackModal')">&times;</button>
    </div>
    <form action="<?= site_url('admin/crm/feedback/store') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="card-body">
        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Customer Name *</label>
            <input type="text" name="customer_name" class="form-control" placeholder="e.g. Elena Rostova" required>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Phone</label>
            <input type="text" name="customer_phone" class="form-control" placeholder="+1 555-0188">
          </div>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Overall Rating *</label>
            <select name="rating" class="form-control" required>
              <option value="5">⭐⭐⭐⭐⭐ (5 - Exceptional)</option>
              <option value="4" selected>⭐⭐⭐⭐ (4 - Great)</option>
              <option value="3">⭐⭐⭐ (3 - Average)</option>
              <option value="2">⭐⭐ (2 - Below Expectations)</option>
              <option value="1">⭐ (1 - Unsatisfactory)</option>
            </select>
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Food Quality (1-5)</label>
            <input type="number" min="1" max="5" name="food_rating" class="form-control" value="5">
          </div>
        </div>

        <div class="form-row d-flex gap-2 mb-2">
          <div class="form-group flex-1">
            <label class="form-label">Service Speed (1-5)</label>
            <input type="number" min="1" max="5" name="service_rating" class="form-control" value="5">
          </div>
          <div class="form-group flex-1">
            <label class="form-label">Ambience (1-5)</label>
            <input type="number" min="1" max="5" name="ambience_rating" class="form-control" value="5">
          </div>
        </div>

        <div class="form-group mb-2">
          <label class="form-label">Guest Comments &amp; Review</label>
          <textarea name="comments" class="form-control" rows="2" placeholder="Guest remarks about dish taste, service, or dining atmosphere"></textarea>
        </div>
      </div>
      <div class="card-footer d-flex justify-end gap-2">
        <button type="button" class="btn btn-outline" onclick="closeModal('addFeedbackModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Review</button>
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
