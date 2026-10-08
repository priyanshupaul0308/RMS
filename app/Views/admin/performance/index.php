<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-title">Staff Performance & Appraisals</h1>
    <p class="page-subtitle">360-degree staff metrics: attendance, punctuality, orders handled, customer feedback, and reviews</p>
  </div>
  <div class="d-flex align-center gap-2 flex-wrap">
    <form method="GET" action="<?= site_url('admin/performance') ?>" class="d-flex align-center gap-1">
      <select name="branch" class="form-control form-control-sm" style="width: auto; padding: 0.35rem 0.65rem; background: var(--surface-raised); font-size: 0.85rem;" onchange="this.form.submit()">
        <option value="all" <?= ($selectedBranch === 'all') ? 'selected' : '' ?>>🌐 All Branches (<?= count($performanceRoster) ?> Staff)</option>
        <?php foreach ($branches as $b): ?>
          <option value="<?= (int)$b['id'] ?>" <?= ((string)$selectedBranch === (string)$b['id']) ? 'selected' : '' ?>>
            📍 <?= esc($b['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
    <button type="button" class="btn btn-primary" onclick="openReviewModalForAny()">
      ⭐ Rate / Review Staff
    </button>
  </div>
</div>

<!-- KPI STATS -->
<div class="stats-grid mb-4">
  <div class="stat-card">
    <div class="stat-label">Active Team Members</div>
    <div class="stat-value text-primary"><?= esc($totalStaff) ?></div>
    <div class="stat-sub"><?= $selectedBranch === 'all' ? 'Across all branches' : 'In this branch' ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Branch Avg Rating</div>
    <div class="stat-value text-warning">&#9733; <?= esc($avgBranchScore) ?> / 5.0</div>
    <div class="stat-sub">Aggregate score</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Top Performer</div>
    <div class="stat-value text-success">
      <?= !empty($performanceRoster) ? esc($performanceRoster[0]['user']['first_name']) : 'N/A' ?>
    </div>
    <div class="stat-sub">
      <?= !empty($performanceRoster) ? 'Score: ' . esc($performanceRoster[0]['score']) . ' / 5.0' : 'Awaiting data' ?>
    </div>
  </div>
</div>

<!-- STAFF SCORECARDS MATRIX -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Employee Performance Matrix & Scorecards (Last 30 Days)</div>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Attendance (30D)</th>
            <th>Punctuality</th>
            <th>Orders Handled</th>
            <th>Sales Attributed</th>
            <th>Feedback Avg</th>
            <th>Overall Rating</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($performanceRoster)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No employees found for this selection.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($performanceRoster as $row): ?>
              <?php 
                $u = $row['user'];
                $m = $row['metrics'];
                $score = $row['score'];
                $latestReviewJson = !empty($m['latest_review']) ? htmlspecialchars(json_encode($m['latest_review']), ENT_QUOTES, 'UTF-8') : 'null';
              ?>
              <tr>
                <td>
                  <strong><?= esc($u['first_name'] . ' ' . $u['last_name']) ?></strong>
                  <div style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($u['email']) ?></div>
                  <div class="d-flex gap-1 align-center mt-1" style="flex-wrap: wrap;">
                    <span class="badge badge-secondary" style="font-size: 10px; font-weight: 600;"><?= esc($u['role_name'] ?? 'Staff') ?></span>
                    <span class="text-xs text-muted" style="font-size: 11px;">&bull; <?= esc($u['branch_name'] ?? 'All Branches / Head Office') ?></span>
                  </div>
                </td>
                <td>
                  <strong><?= esc($m['total_attendance_days']) ?> days</strong>
                  <div style="font-size: 0.75rem; color: #94a3b8;"><?= esc($m['total_hours']) ?> total hrs</div>
                </td>
                <td>
                  <?php if ($m['late_days'] === 0): ?>
                    <span class="badge badge-success">0 Late Days</span>
                  <?php else: ?>
                    <span class="badge badge-warning"><?= esc($m['late_days']) ?> Late</span>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?= esc($m['orders_handled']) ?></strong> orders
                </td>
                <td>
                  &#8377;<?= number_format((float)$m['total_sales'], 2) ?>
                </td>
                <td>
                  <span style="color: #f59e0b; font-weight: 600;">&#9733; <?= esc($m['avg_customer_feedback']) ?></span>
                  <div style="font-size: 0.75rem; color: var(--text-muted);"><?= esc($m['total_feedbacks']) ?> ratings</div>
                </td>
                <td>
                  <div style="display: flex; align-center; gap: 0.5rem;">
                    <span class="badge <?= $score >= 4.5 ? 'badge-success' : ($score >= 3.5 ? 'badge-info' : 'badge-warning') ?>">
                      &#9733; <?= number_format($score, 2) ?>
                    </span>
                  </div>
                  <?php if (!empty($m['latest_review'])): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Rev: <?= esc($m['latest_review']['review_period']) ?></div>
                  <?php else: ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">Baseline default</div>
                  <?php endif; ?>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                  <button type="button" class="btn btn-outline btn-sm" style="margin-right: 0.35rem;" onclick='openHistoryModal(<?= (int)$u['id'] ?>, "<?= esc($u['first_name'] . ' ' . $u['last_name']) ?>")'>
                    History
                  </button>
                  <button type="button" class="btn btn-primary btn-sm" onclick='openReviewModal(<?= (int)$u['id'] ?>, "<?= esc($u['first_name'] . ' ' . $u['last_name']) ?>", <?= $latestReviewJson ?>)'>
                    Review Staff
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

<!-- MODAL: SUBMIT APPRAISAL REVIEW -->
<div class="pos-modal-overlay" id="review-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 600px;">
    <div class="pos-modal-header">
      <div class="modal-title" id="rev-modal-title">Conduct Staff Performance Appraisal</div>
      <button type="button" class="btn-close" onclick="closeModal('review-modal')">&times;</button>
    </div>
    <form id="review-form" onsubmit="submitReview(event)">
      <?= csrf_field() ?>
      <input type="hidden" name="user_id" id="rev-user-id" value="">
      <input type="hidden" name="review_id" id="rev-review-id" value="">
      <div class="pos-modal-body">
        <div class="form-group mb-3">
          <label class="form-label" for="rev-user-select">Staff Member to Review <span class="form-required">*</span></label>
          <select id="rev-user-select" class="form-control" required onchange="onStaffSelectChange(this)">
            <option value="">-- Choose Existing Staff Member --</option>
            <?php foreach ($performanceRoster as $row): ?>
              <?php $uRow = $row['user']; ?>
              <option value="<?= (int)$uRow['id'] ?>" data-name="<?= esc($uRow['first_name'] . ' ' . $uRow['last_name']) ?>">
                <?= esc($uRow['first_name'] . ' ' . $uRow['last_name']) ?> &bull; <?= esc($uRow['role_name'] ?? 'Staff') ?> (<?= esc($uRow['branch_name'] ?? 'All Branches / Head Office') ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="rev-period">Review Period <span class="form-required">*</span></label>
          <input type="text" id="rev-period" name="review_period" class="form-control" required value="<?= date('F Y') ?> Appraisal">
        </div>

        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
          <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 0.75rem;">Competency Ratings (Scale 1 to 5)</div>
          
          <div class="form-row">
            <div class="form-col">
              <div class="form-group mb-3">
                <label class="form-label" for="rev-att">Attendance (25%)</label>
                <select id="rev-att" name="rating_attendance" class="form-control" required onchange="updateLiveScore()">
                  <option value="5">5 - Outstanding</option>
                  <option value="4" selected>4 - Exceeds Expectations</option>
                  <option value="3">3 - Meets Expectations</option>
                  <option value="2">2 - Needs Improvement</option>
                  <option value="1">1 - Unsatisfactory</option>
                </select>
              </div>
            </div>
            <div class="form-col">
              <div class="form-group mb-3">
                <label class="form-label" for="rev-punc">Punctuality (20%)</label>
                <select id="rev-punc" name="rating_punctuality" class="form-control" required onchange="updateLiveScore()">
                  <option value="5">5 - Outstanding</option>
                  <option value="4" selected>4 - Exceeds Expectations</option>
                  <option value="3">3 - Meets Expectations</option>
                  <option value="2">2 - Needs Improvement</option>
                  <option value="1">1 - Unsatisfactory</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-col">
              <div class="form-group mb-3">
                <label class="form-label" for="rev-order">Order Accuracy (30%)</label>
                <select id="rev-order" name="rating_order_accuracy" class="form-control" required onchange="updateLiveScore()">
                  <option value="5" selected>5 - Outstanding</option>
                  <option value="4">4 - Exceeds Expectations</option>
                  <option value="3">3 - Meets Expectations</option>
                  <option value="2">2 - Needs Improvement</option>
                  <option value="1">1 - Unsatisfactory</option>
                </select>
              </div>
            </div>
            <div class="form-col">
              <div class="form-group mb-3">
                <label class="form-label" for="rev-hosp">Hospitality & Teamwork (25%)</label>
                <select id="rev-hosp" name="rating_hospitality" class="form-control" required onchange="updateLiveScore()">
                  <option value="5" selected>5 - Outstanding</option>
                  <option value="4">4 - Exceeds Expectations</option>
                  <option value="3">3 - Meets Expectations</option>
                  <option value="2">2 - Needs Improvement</option>
                  <option value="1">1 - Unsatisfactory</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Live Score Banner -->
          <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 8px; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
              <div style="font-weight: 600; font-size: 0.85rem; color: #f1f5f9;">Calculated Overall Rating:</div>
              <div style="font-size: 0.72rem; color: #94a3b8;">Formula: (Att &times; 0.25) + (Punc &times; 0.20) + (Order &times; 0.30) + (Hosp &times; 0.25)</div>
            </div>
            <div id="live-calculated-score" style="font-size: 1.25rem; font-weight: 800; color: #10b981;">&#9733; 4.55 / 5.0</div>
          </div>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="rev-strengths">Key Strengths</label>
          <textarea id="rev-strengths" name="strengths" class="form-control" rows="2" placeholder="e.g. Excellent guest rapport, rapid POS order punching..."></textarea>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="rev-improv">Areas for Improvement</label>
          <textarea id="rev-improv" name="areas_for_improvement" class="form-control" rows="2" placeholder="e.g. Reduce closing checklist handover delays..."></textarea>
        </div>

        <div class="form-group mb-3">
          <label class="form-label" for="rev-goals">Next Quarter Development Goals</label>
          <input type="text" id="rev-goals" name="goals" class="form-control" placeholder="e.g. Cross-train for Barista station certification">
        </div>
      </div>
      <div class="pos-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('review-modal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="btn-submit-review">Save Appraisal</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: APPRAISAL HISTORY -->
<div class="pos-modal-overlay" id="history-modal" hidden style="display: none;">
  <div class="pos-modal-card" style="max-width: 650px;">
    <div class="pos-modal-header">
      <div class="modal-title" id="hist-modal-title">Appraisal History</div>
      <button type="button" class="btn-close" onclick="closeModal('history-modal')">&times;</button>
    </div>
    <div class="pos-modal-body" id="hist-modal-body" style="max-height: 480px; overflow-y: auto;">
      <div style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading review history...</div>
    </div>
    <div class="pos-modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeModal('history-modal')">Close</button>
    </div>
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

function updateLiveScore() {
  const att   = parseFloat(document.getElementById('rev-att').value) || 0;
  const punc  = parseFloat(document.getElementById('rev-punc').value) || 0;
  const order = parseFloat(document.getElementById('rev-order').value) || 0;
  const hosp  = parseFloat(document.getElementById('rev-hosp').value) || 0;

  const score = (att * 0.25) + (punc * 0.20) + (order * 0.30) + (hosp * 0.25);
  const scoreFormatted = score.toFixed(2);
  const el = document.getElementById('live-calculated-score');
  
  if (el) {
    el.innerHTML = `&#9733; ${scoreFormatted} / 5.0`;
    if (score >= 4.5) {
      el.style.color = '#10b981'; // Green
    } else if (score >= 3.5) {
      el.style.color = '#38bdf8'; // Sky Blue
    } else {
      el.style.color = '#f59e0b'; // Amber
    }
  }
}

// Pre-cache roster review data by staff user ID
const rosterReviewsMap = {};
<?php foreach ($performanceRoster as $row): ?>
  rosterReviewsMap[<?= (int)$row['user']['id'] ?>] = {
    name: <?= json_encode($row['user']['first_name'] . ' ' . $row['user']['last_name']) ?>,
    latestReview: <?= !empty($row['metrics']['latest_review']) ? json_encode($row['metrics']['latest_review']) : 'null' ?>
  };
<?php endforeach; ?>

function openReviewModalForAny() {
  document.getElementById('rev-user-id').value = '';
  const selectEl = document.getElementById('rev-user-select');
  if (selectEl) selectEl.value = '';
  document.getElementById('rev-modal-title').innerText = 'Conduct Staff Performance Appraisal';
  document.getElementById('rev-review-id').value = '';
  document.getElementById('rev-period').value = '<?= date('F Y') ?> Appraisal';
  document.getElementById('rev-att').value = 4;
  document.getElementById('rev-punc').value = 4;
  document.getElementById('rev-order').value = 5;
  document.getElementById('rev-hosp').value = 5;
  document.getElementById('rev-strengths').value = '';
  document.getElementById('rev-improv').value = '';
  document.getElementById('rev-goals').value = '';
  document.getElementById('btn-submit-review').innerText = 'Submit Appraisal';
  updateLiveScore();
  showModal('review-modal');
}

function onStaffSelectChange(selectEl) {
  const userId = selectEl.value;
  if (!userId) {
    document.getElementById('rev-user-id').value = '';
    document.getElementById('rev-modal-title').innerText = 'Conduct Staff Performance Appraisal';
    return;
  }
  document.getElementById('rev-user-id').value = userId;
  const staffData = rosterReviewsMap[userId];
  if (staffData) {
    const name = staffData.name;
    const existingReview = staffData.latestReview;
    if (existingReview) {
      document.getElementById('rev-modal-title').innerText = `Update Appraisal: ${name}`;
      document.getElementById('rev-review-id').value = existingReview.id || '';
      document.getElementById('rev-period').value = existingReview.review_period || '<?= date('F Y') ?> Appraisal';
      document.getElementById('rev-att').value = existingReview.rating_attendance || 4;
      document.getElementById('rev-punc').value = existingReview.rating_punctuality || 4;
      document.getElementById('rev-order').value = existingReview.rating_order_accuracy || 5;
      document.getElementById('rev-hosp').value = existingReview.rating_hospitality || 5;
      document.getElementById('rev-strengths').value = existingReview.strengths || '';
      document.getElementById('rev-improv').value = existingReview.areas_for_improvement || '';
      document.getElementById('rev-goals').value = existingReview.goals || '';
      document.getElementById('btn-submit-review').innerText = 'Update Appraisal';
    } else {
      document.getElementById('rev-modal-title').innerText = `Conduct Staff Performance Appraisal: ${name}`;
      document.getElementById('rev-review-id').value = '';
      document.getElementById('rev-period').value = '<?= date('F Y') ?> Appraisal';
      document.getElementById('rev-att').value = 4;
      document.getElementById('rev-punc').value = 4;
      document.getElementById('rev-order').value = 5;
      document.getElementById('rev-hosp').value = 5;
      document.getElementById('rev-strengths').value = '';
      document.getElementById('rev-improv').value = '';
      document.getElementById('rev-goals').value = '';
      document.getElementById('btn-submit-review').innerText = 'Submit Appraisal';
    }
    updateLiveScore();
  }
}

function openReviewModal(userId, name, existingReview) {
  document.getElementById('rev-user-id').value = userId;
  const selectEl = document.getElementById('rev-user-select');
  if (selectEl) {
    selectEl.value = String(userId);
  }
  
  if (existingReview && typeof existingReview === 'object') {
    document.getElementById('rev-modal-title').innerText = `Update Appraisal: ${name}`;
    document.getElementById('rev-review-id').value = existingReview.id || '';
    document.getElementById('rev-period').value = existingReview.review_period || '<?= date('F Y') ?> Appraisal';
    document.getElementById('rev-att').value = existingReview.rating_attendance || 4;
    document.getElementById('rev-punc').value = existingReview.rating_punctuality || 4;
    document.getElementById('rev-order').value = existingReview.rating_order_accuracy || 5;
    document.getElementById('rev-hosp').value = existingReview.rating_hospitality || 5;
    document.getElementById('rev-strengths').value = existingReview.strengths || '';
    document.getElementById('rev-improv').value = existingReview.areas_for_improvement || '';
    document.getElementById('rev-goals').value = existingReview.goals || '';
    document.getElementById('btn-submit-review').innerText = 'Update Appraisal';
  } else {
    document.getElementById('rev-modal-title').innerText = `Conduct Staff Performance Appraisal: ${name}`;
    document.getElementById('rev-review-id').value = '';
    document.getElementById('rev-period').value = '<?= date('F Y') ?> Appraisal';
    document.getElementById('rev-att').value = 4;
    document.getElementById('rev-punc').value = 4;
    document.getElementById('rev-order').value = 5;
    document.getElementById('rev-hosp').value = 5;
    document.getElementById('rev-strengths').value = '';
    document.getElementById('rev-improv').value = '';
    document.getElementById('rev-goals').value = '';
    document.getElementById('btn-submit-review').innerText = 'Submit Appraisal';
  }

  updateLiveScore();
  showModal('review-modal');
}

async function openHistoryModal(userId, name) {
  document.getElementById('hist-modal-title').innerText = `Appraisal History: ${name}`;
  const body = document.getElementById('hist-modal-body');
  body.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--text-muted);"><i class="fa fa-spinner fa-spin"></i> Loading review history...</div>';
  showModal('history-modal');

  try {
    const res = await fetch(`<?= site_url('admin/performance/history') ?>/${userId}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.status === 'success' && data.data.reviews && data.data.reviews.length > 0) {
      let html = '<div style="display: flex; flex-direction: column; gap: 1rem;">';
      data.data.reviews.forEach(r => {
        const score = parseFloat(r.overall_score).toFixed(2);
        const scoreBadgeClass = score >= 4.5 ? 'badge-success' : (score >= 3.5 ? 'badge-info' : 'badge-warning');
        const reviewer = (r.reviewer_fname ? (r.reviewer_fname + ' ' + (r.reviewer_lname || '')) : 'Manager');
        html += `
          <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
              <div>
                <strong style="font-size: 1rem; color: #f8fafc;">${escHtml(r.review_period)}</strong>
                <div style="font-size: 0.8rem; color: var(--text-muted);">Reviewed by ${escHtml(reviewer)} on ${r.review_date}</div>
              </div>
              <span class="badge ${scoreBadgeClass}" style="font-size: 0.95rem; font-weight: 700;">&#9733; ${score} / 5.0</span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; background: rgba(0,0,0,0.2); padding: 0.5rem; border-radius: 6px; font-size: 0.8rem; margin: 0.5rem 0;">
              <div><span style="color:#94a3b8;">Att:</span> <strong>${r.rating_attendance}/5</strong></div>
              <div><span style="color:#94a3b8;">Punc:</span> <strong>${r.rating_punctuality}/5</strong></div>
              <div><span style="color:#94a3b8;">Accuracy:</span> <strong>${r.rating_order_accuracy}/5</strong></div>
              <div><span style="color:#94a3b8;">Hosp:</span> <strong>${r.rating_hospitality}/5</strong></div>
            </div>
            ${r.strengths ? `<div style="font-size: 0.82rem; margin-top: 0.4rem;"><strong>Strengths:</strong> <span style="color:#cbd5e1;">${escHtml(r.strengths)}</span></div>` : ''}
            ${r.areas_for_improvement ? `<div style="font-size: 0.82rem; margin-top: 0.3rem;"><strong>Improvements:</strong> <span style="color:#cbd5e1;">${escHtml(r.areas_for_improvement)}</span></div>` : ''}
            ${r.goals ? `<div style="font-size: 0.82rem; margin-top: 0.3rem;"><strong>Goals:</strong> <span style="color:#cbd5e1;">${escHtml(r.goals)}</span></div>` : ''}
          </div>
        `;
      });
      html += '</div>';
      body.innerHTML = html;
    } else {
      body.innerHTML = `<div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">No appraisal history found for this employee yet.</div>`;
    }
  } catch(err) {
    body.innerHTML = `<div style="text-align: center; padding: 2rem; color: #ef4444;">Failed to load appraisal history.</div>`;
  }
}

function escHtml(str) {
  if (!str) return '';
  return String(str).replace(/[&<>"']/g, function(m) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
  });
}

async function submitReview(e) {
  e.preventDefault();
  const form = document.getElementById('review-form');
  const btn = document.getElementById('btn-submit-review');
  const origText = btn.innerText;
  btn.disabled = true;
  btn.innerText = 'Saving Appraisal...';

  try {
    const res = await fetch('<?= site_url('admin/performance/review/store') ?>', {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    const data = await res.json();
    if (data.status === 'success') {
      alert(`Appraisal saved successfully! Overall rating updated to: ★ ${parseFloat(data.data.overall_score).toFixed(2)} / 5.0`);
      window.location.reload();
    } else {
      const msg = data.message || (data.errors ? Object.values(data.errors).join('\n') : 'Error saving appraisal review.');
      alert(msg);
      btn.disabled = false;
      btn.innerText = origText;
    }
  } catch(err) {
    console.error('Submit review error:', err);
    alert('Network error while saving appraisal. Please try again.');
    btn.disabled = false;
    btn.innerText = origText;
  }
}
</script>
