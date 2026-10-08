<div class="page-header">
  <div>
    <h1 class="page-title">Kitchen Order Tickets (KOT)</h1>
    <p class="page-subtitle">Historical and live KOT tickets dispatched to kitchen cook stations</p>
  </div>
  <div class="page-actions">
    <a href="<?= site_url('admin/kds') ?>" class="btn btn-primary">
      🍳 Open Kitchen Display (KDS)
    </a>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Recent KOT Tickets (<?= count($kots) ?>)</div>
    <button class="btn btn-secondary btn-sm" onclick="location.reload()">&#8635; Refresh</button>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>KOT #</th>
            <th>Order #</th>
            <th>Type</th>
            <th>Table</th>
            <th>Station</th>
            <th>Items</th>
            <th>Dispatched Time</th>
            <th>Status</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($kots)): ?>
            <tr>
              <td colspan="9" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                No Kitchen Order Tickets found. Place orders in POS to generate KOTs.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($kots as $k): ?>
              <tr>
                <td class="td-bold">
                  <a href="<?= site_url('admin/kot/print/' . $k['id']) ?>" target="_blank" class="text-primary" style="text-decoration:none;">
                    <?= esc($k['kot_number']) ?>
                  </a>
                </td>
                <td>
                  <a href="<?= site_url('admin/orders/show/' . $k['order_id']) ?>" class="text-muted" style="text-decoration:none;">
                    <?= esc($k['order_number'] ?? 'ORD-' . $k['order_id']) ?>
                  </a>
                </td>
                <td>
                  <span class="badge badge-secondary"><?= esc(ucfirst($k['order_type'] ?? 'Dine-In')) ?></span>
                </td>
                <td>
                  <strong><?= esc($k['table_number'] ?: 'Takeaway') ?></strong>
                </td>
                <td>
                  <span class="badge badge-info"><?= esc(ucfirst($k['station'])) ?></span>
                </td>
                <td>
                  <?= (int)$k['item_count'] ?> dishes
                </td>
                <td>
                  <?= esc(date('M d, H:i:s', strtotime($k['created_at']))) ?>
                </td>
                <td>
                  <?php if ($k['status'] === 'completed'): ?>
                    <span class="status-badge status-active">Ready / Bumped</span>
                  <?php elseif ($k['status'] === 'preparing'): ?>
                    <span class="status-badge status-warning">Cooking</span>
                  <?php else: ?>
                    <span class="status-badge status-pending">In Queue</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <a href="<?= site_url('admin/kot/print/' . $k['id']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                    🖨️ Print KOT
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
