<div class="page-header">
  <div>
    <h1 class="page-title">Billing &amp; Payment Transactions</h1>
    <p class="page-subtitle">Itemized audit ledger of tender payments, card settlements, cash receipts, and split bills</p>
  </div>
  <div class="page-actions">
    <a href="<?= site_url('admin/orders') ?>" class="btn btn-secondary">
      Live Orders Pipeline
    </a>
    <a href="<?= site_url('admin/cash-drawer') ?>" class="btn btn-primary">
      Cash Drawer &amp; Shifts
    </a>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Processed Transactions (<?= count($payments) ?>)</div>
    <button class="btn btn-secondary btn-sm" onclick="location.reload()">&#8635; Refresh</button>
  </div>
  <div class="card-body" style="padding: 0;">
    <div class="table-wrapper">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Tx ID</th>
            <th>Order #</th>
            <th>Guest Name</th>
            <th>Tender Method</th>
            <th>Amount Paid</th>
            <th>Reference / Details</th>
            <th>Cashier</th>
            <th>Date &amp; Time</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($payments)): ?>
            <tr>
              <td colspan="8" class="text-center text-muted" style="padding: 3rem 1rem;">
                No payment transactions recorded yet. Settle bills in the POS to record payments.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($payments as $p): ?>
              <tr>
                <td class="td-bold">#<?= (int)$p['id'] ?></td>
                <td>
                  <a href="<?= site_url('admin/orders/show/' . $p['order_id']) ?>" class="text-primary td-bold" style="text-decoration:none;">
                    <?= esc($p['order_number'] ?? 'ORD-' . $p['order_id']) ?>
                  </a>
                </td>
                <td><?= esc($p['customer_name'] ?: 'Guest') ?></td>
                <td>
                  <span class="badge badge-info">
                    <?= esc($p['method_name'] ?? 'Cash') ?>
                  </span>
                </td>
                <td class="td-bold" style="color: var(--accent); font-size: 1rem;">
                  ₹<?= esc(number_format((float)$p['amount'], 2)) ?>
                </td>
                <td>
                  <code class="text-xs"><?= esc($p['reference_number'] ?: 'Direct POS Tender') ?></code>
                </td>
                <td><?= esc($p['cashier_name'] ?? 'Staff') ?></td>
                <td><?= esc(date('M d, Y H:i', strtotime($p['created_at']))) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
