<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>KOT Ticket #<?= esc($kot['kot_number']) ?></title>
  <style>
    @media print {
      @page { margin: 0; }
      body { margin: 0.5cm; }
      .no-print { display: none !important; }
    }
    body {
      font-family: 'Courier New', Courier, monospace;
      font-size: 14px;
      color: #000;
      width: 80mm;
      max-width: 80mm;
      margin: 15px auto;
      padding: 10px;
      background: #fff;
    }
    .text-center { text-align: center; }
    .bold { font-weight: bold; }
    .divider { border-top: 2px dashed #000; margin: 8px 0; }
    .d-flex { display: flex; justify-content: space-between; }
    table { width: 100%; border-collapse: collapse; margin: 8px 0; }
    th { border-bottom: 2px solid #000; padding: 4px 0; text-align: left; font-size: 14px; }
    td { padding: 6px 0; vertical-align: top; border-bottom: 1px dotted #ccc; }
    .qty-cell { font-size: 18px; font-weight: bold; width: 45px; }
    .item-cell { font-size: 15px; font-weight: bold; }
    .note-cell { font-size: 12px; font-weight: normal; margin-top: 2px; }
    .btn-print {
      display: block; width: 100%; padding: 10px; background: #000; color: #fff;
      border: none; cursor: pointer; font-size: 14px; margin-bottom: 15px; border-radius: 4px;
    }
  </style>
</head>
<body>
  <div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Print KOT (80mm Thermal)</button>
  </div>

  <div class="text-center bold" style="font-size: 18px;">
    *** KITCHEN ORDER TICKET ***
  </div>
  <?php if ((int)$kot['reprint_count'] > 1): ?>
    <div class="text-center bold" style="color: red; font-size: 14px;">
      ** REPRINT #<?= (int)$kot['reprint_count'] ?> **
    </div>
  <?php endif; ?>

  <div class="divider"></div>

  <div class="d-flex bold" style="font-size: 16px;">
    <span>KOT: <?= esc($kot['kot_number']) ?></span>
    <span><?= esc(strtoupper($kot['order_type'])) ?></span>
  </div>
  <div class="d-flex" style="font-size: 15px; margin-top: 3px;">
    <span class="bold">TABLE: <?= esc($kot['table_number'] ?? 'COUNTER') ?></span>
    <span>Floor: <?= esc($kot['floor_name'] ?? 'Main') ?></span>
  </div>
  <div class="d-flex" style="font-size: 12px; margin-top: 3px;">
    <span>Order: #<?= esc($kot['order_number']) ?></span>
    <span>Server: <?= esc($kot['waiter_name'] ?? 'Staff') ?></span>
  </div>
  <div class="d-flex" style="font-size: 12px; margin-top: 2px;">
    <span>Time: <?= esc(date('d/m/Y H:i:s', strtotime($kot['created_at']))) ?></span>
  </div>

  <div class="divider"></div>

  <table>
    <thead>
      <tr>
        <th style="width: 45px;">QTY</th>
        <th>ITEM DESCRIPTION</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td class="qty-cell"><?= (int)$it['quantity'] ?>x</td>
          <td>
            <div class="item-cell"><?= esc($it['item_name']) ?></div>
            <?php if (!empty($it['special_notes'])): ?>
              <div class="note-cell bold">*** NOTE: <?= esc($it['special_notes']) ?> ***</div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if (!empty($kot['order_notes'])): ?>
    <div class="divider"></div>
    <div class="bold" style="font-size: 13px;">
      SPECIAL ORDER INSTRUCTIONS:<br>
      <?= esc($kot['order_notes']) ?>
    </div>
  <?php endif; ?>

  <div class="divider"></div>
  <div class="text-center bold" style="font-size: 12px;">
    END OF TICKET
  </div>
</body>
</html>
