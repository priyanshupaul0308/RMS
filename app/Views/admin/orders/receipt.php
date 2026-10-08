<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Receipt #<?= esc($order['order_number']) ?></title>
  <style>
    @media print {
      @page { margin: 0; }
      body { margin: 0.5cm; }
      .no-print { display: none !important; }
    }
    body {
      font-family: 'Courier New', Courier, monospace;
      font-size: 13px;
      color: #000;
      width: 80mm;
      max-width: 80mm;
      margin: 15px auto;
      padding: 10px;
      background: #fff;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: bold; }
    .divider { border-top: 1px dashed #000; margin: 8px 0; }
    .d-flex { display: flex; justify-content: space-between; }
    table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 12px; }
    th { border-bottom: 1px dashed #000; padding: 4px 0; text-align: left; }
    td { padding: 4px 0; vertical-align: top; }
    .btn-print {
      display: block; width: 100%; padding: 10px; background: #000; color: #fff;
      border: none; cursor: pointer; font-size: 14px; margin-bottom: 15px; border-radius: 4px;
    }
  </style>
</head>
<body>
  <div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Print Receipt (80mm)</button>
  </div>

  <div class="text-center">
    <div class="bold" style="font-size: 16px; letter-spacing: 0.5px;"><?= esc($restaurant['name'] ?? 'RESTAURANT MANAGEMENT') ?></div>
    <?php if (!empty($branch['name'])): ?>
      <div style="font-size: 13px; font-weight: 600; margin-top: 2px;"><?= esc($branch['name']) ?></div>
    <?php endif; ?>
    <?php if (!empty($branch['address'])): ?>
      <div><?= esc($branch['address']) ?></div>
    <?php endif; ?>
    <?php 
      $loc = trim(($branch['city'] ?? '') . (!empty($branch['city']) && !empty($branch['state']) ? ', ' : '') . ($branch['state'] ?? ''));
      if (!empty($loc)): 
    ?>
      <div><?= esc($loc) ?></div>
    <?php endif; ?>
    <?php if (!empty($branch['phone'])): ?>
      <div>Tel: <?= esc($branch['phone']) ?></div>
    <?php endif; ?>
    <?php if (!empty($branch['tax_number'])): ?>
      <div>GSTIN: <?= esc($branch['tax_number']) ?></div>
    <?php endif; ?>
  </div>

  <div class="divider"></div>

  <div class="d-flex">
    <span>Order: <?= esc($order['order_number']) ?></span>
    <span><?= esc(strtoupper($order['order_type'])) ?></span>
  </div>
  <div class="d-flex">
    <span>Table: <?= esc($order['table_number'] ?? 'Counter') ?></span>
    <span><?= esc(date('d/m/Y H:i', strtotime($order['created_at']))) ?></span>
  </div>
  <?php 
    $guestName = !empty($order['customer_name']) && $order['customer_name'] !== 'Walk-in Guest' ? $order['customer_name'] : '';
    $guestPhone = !empty($order['customer_phone']) ? $order['customer_phone'] : '';

    if (empty($guestName) && !empty($order['table_id'])) {
      $tblRes = db_connect()->table('reservations')
        ->where('table_id', (int)$order['table_id'])
        ->where('reservation_date', date('Y-m-d', strtotime($order['created_at'])))
        ->whereIn('status', ['seated', 'confirmed', 'completed'])
        ->orderBy('id', 'DESC')
        ->get()
        ->getRowArray();
      if ($tblRes) {
        $guestName = $tblRes['customer_name'] ?? '';
        if (empty($guestPhone)) {
          $guestPhone = $tblRes['customer_phone'] ?? '';
        }
      }
    }
  ?>
  <?php if (!empty($guestName)): ?>
    <div class="d-flex" style="margin-top: 3px;">
      <span>Guest: <strong><?= esc($guestName) ?></strong></span>
      <?php if (!empty($guestPhone)): ?>
        <span style="font-size: 11px;"><?= esc($guestPhone) ?></span>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="divider"></div>

  <table>
    <thead>
      <tr>
        <th>Item</th>
        <th class="text-center">Qty</th>
        <th class="text-right">Price</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($order['items'] as $item): ?>
        <tr>
          <td><?= esc($item['item_name']) ?></td>
          <td class="text-center"><?= (float)$item['quantity'] ?></td>
          <td class="text-right"><?= number_format((float)$item['unit_price'], 2) ?></td>
          <td class="text-right"><?= number_format((float)$item['total'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="divider"></div>

  <div class="d-flex">
    <span>Subtotal:</span>
    <span>₹<?= number_format((float)$order['subtotal'], 2) ?></span>
  </div>
  <div class="d-flex">
    <span>CGST (2.5%):</span>
    <span>₹<?= number_format((float)$order['tax_amount'] / 2, 2) ?></span>
  </div>
  <div class="d-flex">
    <span>SGST (2.5%):</span>
    <span>₹<?= number_format((float)$order['tax_amount'] / 2, 2) ?></span>
  </div>
  <?php if ((float)$order['service_charge'] > 0): ?>
    <div class="d-flex">
      <span>Service Charge:</span>
      <span>₹<?= number_format((float)$order['service_charge'], 2) ?></span>
    </div>
  <?php endif; ?>
  <?php if ((float)($order['discount_amount'] ?? 0) > 0): ?>
    <div class="d-flex">
      <span>Discount <?= !empty($order['discount_reason']) ? '(' . esc($order['discount_reason']) . ')' : '' ?>:</span>
      <span>-₹<?= number_format((float)$order['discount_amount'], 2) ?></span>
    </div>
  <?php endif; ?>

  <div class="divider"></div>

  <div class="d-flex bold" style="font-size: 15px;">
    <span>GRAND TOTAL:</span>
    <span>₹<?= number_format((float)$order['final_total'], 2) ?></span>
  </div>

  <div class="divider"></div>

  <div class="text-center" style="font-size: 11px; margin-top: 10px;">
    <?php if (!empty($footerNote)): ?>
      <div>*** <?= esc(strtoupper($footerNote)) ?> ***</div>
    <?php else: ?>
      <div>*** THANK YOU FOR DINING WITH US ***</div>
    <?php endif; ?>
    <div>Please visit again!</div>
  </div>
</body>
</html>
