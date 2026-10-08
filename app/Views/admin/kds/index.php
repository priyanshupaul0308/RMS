<div class="kds-container">
  
  <!-- KDS TOPBAR -->
  <div class="kds-topbar">
    <div class="d-flex align-center gap-2">
      <div class="kds-title">
        🍳 Kitchen Display System <span class="badge badge-primary" id="kds-live-count"><?= count($activeKots) ?> Active</span>
      </div>

      <!-- Station Filter Chips -->
      <div class="kds-station-chips">
        <button type="button" class="station-chip active" data-station="all" onclick="filterStation('all', this)">
          All Stations
        </button>
        <?php foreach ($stations as $st): ?>
          <button type="button" class="station-chip" data-station="<?= esc($st['code']) ?>" onclick="filterStation('<?= esc($st['code']) ?>', this)">
            <span class="station-dot" style="background: <?= esc($st['color_hex']) ?>;"></span>
            <?= esc($st['name']) ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="d-flex align-center gap-2">
      <div class="kds-status-indicator">
        <span class="pulse-dot"></span> <span class="text-xs">Live Kitchen Polling (5s)</span>
      </div>
      <button type="button" class="btn btn-secondary btn-sm" id="btn-audio-toggle" onclick="toggleAudio()">
        🔊 Chime: On
      </button>
      <button type="button" class="btn btn-secondary btn-sm" onclick="fetchKdsFeed()">
        &#8635; Refresh
      </button>
    </div>
  </div>

  <!-- KDS TICKET CARDS GRID -->
  <div class="kds-grid" id="kds-grid">
    <?php if (empty($activeKots)): ?>
      <div class="kds-empty-state" id="kds-empty-state">
        <div style="font-size: 3.5rem; margin-bottom: .75rem;">🧑‍🍳</div>
        <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">All Caught Up!</div>
        <div class="text-muted text-small mt-1">No pending orders in the kitchen queue. Incoming tickets will chime automatically.</div>
      </div>
    <?php else: ?>
      <?php foreach ($activeKots as $kot): ?>
        <div class="kds-card urgency-<?= esc($kot['urgency']) ?>" id="kds-card-<?= (int)$kot['id'] ?>" data-station="<?= esc($kot['station']) ?>">
          
          <div class="kds-card-header">
            <div>
              <div class="kds-order-type">
                <?php if ($kot['order_type'] === 'dine_in'): ?>
                  🍽️ Dine-In &bull; Table <?= esc($kot['table_number'] ?? 'N/A') ?>
                <?php elseif ($kot['order_type'] === 'takeaway'): ?>
                  🥡 Takeaway
                <?php else: ?>
                  🛵 Delivery
                <?php endif; ?>
              </div>
              <div class="kds-ticket-num">
                <?= esc($kot['kot_number']) ?> <span class="text-muted text-xs">(#<?= esc($kot['order_number']) ?>)</span>
              </div>
            </div>

            <!-- Elapsed Cooking Timer -->
            <div class="kds-timer badge-urgency-<?= esc($kot['urgency']) ?>" data-created="<?= esc($kot['created_at']) ?>" id="timer-<?= (int)$kot['id'] ?>">
              ⏱️ <?= esc($kot['elapsed_display']) ?>
            </div>
          </div>

          <!-- Items Checklist -->
          <div class="kds-card-body">
            <div class="kds-items-list">
              <?php foreach ($kot['items'] as $item): ?>
                <div class="kds-item-row <?= $item['status'] === 'ready' ? 'item-ready' : '' ?>" id="kds-item-<?= (int)$item['id'] ?>" onclick="toggleKdsItem(<?= (int)$item['id'] ?>, this)">
                  <div class="kds-item-qty"><?= (int)$item['quantity'] ?>x</div>
                  <div class="kds-item-name-block">
                    <div class="kds-item-name"><?= esc($item['item_name']) ?></div>
                    <?php if (!empty($item['special_notes'])): ?>
                      <div class="kds-item-notes">⚠️ <?= esc($item['special_notes']) ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="kds-item-check">&#10003;</div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Ticket Action Buttons -->
          <div class="kds-card-footer">
            <?php if ($kot['status'] === 'sent'): ?>
              <button type="button" class="btn btn-warning btn-block" onclick="startCooking(<?= (int)$kot['id'] ?>, this)">
                🍳 Start Cooking
              </button>
            <?php else: ?>
              <button type="button" class="btn btn-success btn-block btn-lg" onclick="bumpTicket(<?= (int)$kot['id'] ?>, this)">
                🔔 Bump / Mark Ready
              </button>
            <?php endif; ?>
          </div>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div><!-- /.kds-container -->

<style>
.kds-container {
  display: flex;
  flex-direction: column;
  height: calc(100vh - 90px);
  gap: .75rem;
}
.kds-topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--surface-raised);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: .65rem 1.25rem;
  gap: 1rem;
  flex-wrap: wrap;
}
.kds-title {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: .5rem;
}
.kds-station-chips {
  display: flex;
  gap: .4rem;
  overflow-x: auto;
}
.station-chip {
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--text-secondary);
  padding: .35rem .75rem;
  border-radius: 20px;
  font-size: .8rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  transition: all .2s;
}
.station-chip.active, .station-chip:hover {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
}
.station-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}
.kds-status-indicator {
  display: flex;
  align-items: center;
  gap: .4rem;
  color: var(--text-muted);
}
.pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
  animation: pulse 1.8s infinite;
}
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
  70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
  100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* KDS CARDS GRID */
.kds-grid {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1rem;
  overflow-y: auto;
  padding: .25rem;
}
.kds-empty-state {
  grid-column: 1 / -1;
  margin: auto;
  text-align: center;
  padding: 4rem 1rem;
}
.kds-card {
  background: var(--surface-raised);
  border: 2px solid var(--border);
  border-radius: var(--radius);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
  transition: transform .2s, border-color .2s;
}
.kds-card:hover {
  transform: translateY(-2px);
}
.kds-card.urgency-normal  { border-top: 4px solid var(--success); }
.kds-card.urgency-warning { border-top: 4px solid var(--warning); border-color: var(--warning); }
.kds-card.urgency-urgent  { border-top: 4px solid var(--danger); border-color: var(--danger); animation: glowUrgent 2s infinite; }

@keyframes glowUrgent {
  0%, 100% { box-shadow: 0 0 8px rgba(239, 68, 68, 0.3); }
  50% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.6); }
}

.kds-card-header {
  padding: .75rem 1rem;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.kds-order-type {
  font-size: .85rem;
  font-weight: 700;
  color: var(--text-primary);
}
.kds-ticket-num {
  font-size: .75rem;
  color: var(--text-muted);
}
.kds-timer {
  font-size: .85rem;
  font-weight: 800;
  padding: .25rem .5rem;
  border-radius: var(--radius-sm);
  background: var(--surface-high);
}
.badge-urgency-normal  { color: #22c55e; }
.badge-urgency-warning { color: #f59e0b; background: rgba(245, 158, 11, 0.15); }
.badge-urgency-urgent  { color: #ef4444; background: rgba(239, 68, 68, 0.2); }

.kds-card-body {
  flex: 1;
  padding: .75rem;
  overflow-y: auto;
}
.kds-items-list {
  display: flex;
  flex-direction: column;
  gap: .5rem;
}
.kds-item-row {
  display: flex;
  align-items: flex-start;
  gap: .65rem;
  padding: .5rem;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all .2s;
  user-select: none;
}
.kds-item-row:hover {
  background: var(--surface-high);
}
.kds-item-row.item-ready {
  opacity: .5;
  text-decoration: line-through;
  background: rgba(34, 197, 94, 0.1);
  border-color: rgba(34, 197, 94, 0.3);
}
.kds-item-row.item-ready .kds-item-check {
  color: #22c55e;
  font-weight: 800;
}
.kds-item-qty {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--primary);
  min-width: 28px;
}
.kds-item-name-block {
  flex: 1;
}
.kds-item-name {
  font-size: .925rem;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1.25;
}
.kds-item-notes {
  font-size: .75rem;
  color: #f59e0b;
  font-weight: 600;
  margin-top: .15rem;
}
.kds-item-check {
  color: var(--text-muted);
  font-size: 1rem;
}
.kds-card-footer {
  padding: .75rem;
  background: var(--surface);
  border-top: 1px solid var(--border);
}
</style>

<!-- KDS JAVASCRIPT & AUDIO ENGINE -->
<script>
var audioEnabled = true;
var currentStation = 'all';
var knownTicketIds = new Set();

// Initialize known tickets
<?php foreach ($activeKots as $k): ?>
knownTicketIds.add(<?= (int)$k['id'] ?>);
<?php endforeach; ?>

// Play synthetic web audio bell chime on new incoming tickets
function playKitchenChime() {
  if (!audioEnabled) return;
  try {
    var ctx = new (window.AudioContext || window.webkitAudioContext)();
    var osc1 = ctx.createOscillator();
    var osc2 = ctx.createOscillator();
    var gain = ctx.createGain();

    osc1.type = 'sine';
    osc1.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
    osc2.type = 'triangle';
    osc2.frequency.setValueAtTime(880.00, ctx.currentTime); // A5

    gain.gain.setValueAtTime(0.3, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 1.2);

    osc1.connect(gain);
    osc2.connect(gain);
    gain.connect(ctx.destination);

    osc1.start();
    osc2.start();
    osc1.stop(ctx.currentTime + 1.2);
    osc2.stop(ctx.currentTime + 1.2);
  } catch(e) {}
}

function toggleAudio() {
  audioEnabled = !audioEnabled;
  var btn = document.getElementById('btn-audio-toggle');
  btn.textContent = audioEnabled ? '🔊 Chime: On' : '🔇 Chime: Muted';
}

function filterStation(stationCode, btn) {
  currentStation = stationCode;
  document.querySelectorAll('.station-chip').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');

  var cards = document.querySelectorAll('.kds-card');
  cards.forEach(function(c) {
    var st = c.getAttribute('data-station');
    if (stationCode === 'all' || st === stationCode) {
      c.style.display = 'flex';
    } else {
      c.style.display = 'none';
    }
  });
}

function startCooking(kotId, btn) {
  btn.disabled = true;
  btn.textContent = 'Starting...';

  window.rmsPost('<?= site_url('admin/kds/start') ?>/' + kotId, {})
    .then(function(res) {
      if (res.success) {
        fetchKdsFeed();
      } else {
        alert(res.message || 'Error starting cooking');
        btn.disabled = false;
      }
    });
}

function bumpTicket(kotId, btn) {
  btn.disabled = true;
  btn.textContent = 'Bumping...';

  var card = document.getElementById('kds-card-' + kotId);
  if (card) {
    card.style.opacity = '0.3';
  }

  window.rmsPost('<?= site_url('admin/kds/bump') ?>/' + kotId, {})
    .then(function(res) {
      if (res.success) {
        if (card) card.remove();
        fetchKdsFeed();
      } else {
        alert(res.message || 'Error bumping ticket');
        btn.disabled = false;
        if (card) card.style.opacity = '1';
      }
    });
}

function toggleKdsItem(itemId, row) {
  row.classList.toggle('item-ready');
  window.rmsPost('<?= site_url('admin/kds/toggle-item') ?>/' + itemId, {});
}

// Live Polling
function fetchKdsFeed() {
  window.rmsGet('<?= site_url('admin/kds/feed') ?>?station=' + currentStation)
    .then(function(res) {
      if (!res || !res.success || !res.data) return;
      var tickets = res.data.tickets || [];
      document.getElementById('kds-live-count').textContent = res.data.active_count + ' Active';

      // Check for new incoming tickets
      var hasNew = false;
      tickets.forEach(function(t) {
        if (!knownTicketIds.has(t.id)) {
          hasNew = true;
          knownTicketIds.add(t.id);
        }
      });

      if (hasNew) {
        playKitchenChime();
      }

      renderKdsTickets(tickets);
    });
}

function renderKdsTickets(tickets) {
  var grid = document.getElementById('kds-grid');
  if (tickets.length === 0) {
    grid.innerHTML = '<div class="kds-empty-state"><div style="font-size: 3.5rem; margin-bottom: .75rem;">🧑‍🍳</div><div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">All Caught Up!</div><div class="text-muted text-small mt-1">No pending orders in the kitchen queue. Incoming tickets will chime automatically.</div></div>';
    return;
  }

  var html = '';
  tickets.forEach(function(kot) {
    var typeStr = (kot.order_type === 'dine_in') ? ('🍽️ Dine-In &bull; Table ' + (kot.table_number || 'N/A')) : (kot.order_type === 'takeaway' ? '🥡 Takeaway' : '🛵 Delivery');

    html += '<div class="kds-card urgency-' + kot.urgency + '" id="kds-card-' + kot.id + '" data-station="' + kot.station + '">' +
      '<div class="kds-card-header">' +
        '<div>' +
          '<div class="kds-order-type">' + typeStr + '</div>' +
          '<div class="kds-ticket-num">' + kot.kot_number + ' <span class="text-muted text-xs">(#' + kot.order_number + ')</span></div>' +
        '</div>' +
        '<div class="kds-timer badge-urgency-' + kot.urgency + '" id="timer-' + kot.id + '">' +
          '⏱️ ' + kot.elapsed_display +
        '</div>' +
      '</div>' +
      '<div class="kds-card-body">' +
        '<div class="kds-items-list">';
    
    (kot.items || []).forEach(function(it) {
      var readyClass = (it.status === 'ready') ? ' item-ready' : '';
      var noteHtml = it.special_notes ? ('<div class="kds-item-notes">⚠️ ' + it.special_notes + '</div>') : '';
      html += '<div class="kds-item-row' + readyClass + '" id="kds-item-' + it.id + '" onclick="toggleKdsItem(' + it.id + ', this)">' +
        '<div class="kds-item-qty">' + parseInt(it.quantity) + 'x</div>' +
        '<div class="kds-item-name-block">' +
          '<div class="kds-item-name">' + it.item_name + '</div>' +
          noteHtml +
        '</div>' +
        '<div class="kds-item-check">&#10003;</div>' +
      '</div>';
    });

    html += '</div></div>' +
      '<div class="kds-card-footer">';
    if (kot.status === 'sent') {
      html += '<button type="button" class="btn btn-warning btn-block" onclick="startCooking(' + kot.id + ', this)">🍳 Start Cooking</button>';
    } else {
      html += '<button type="button" class="btn btn-success btn-block btn-lg" onclick="bumpTicket(' + kot.id + ', this)">🔔 Bump / Mark Ready</button>';
    }
    html += '</div></div>';
  });

  grid.innerHTML = html;
}

// Poll every 5 seconds
setInterval(fetchKdsFeed, 5000);
</script>
