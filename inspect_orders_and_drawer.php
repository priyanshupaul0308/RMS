<?php
declare(strict_types=1);

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=rms_db;charset=utf8mb4', 'root', 'root');

echo "=== Active Cash Registers ===\n";
$stmt = $pdo->query("SELECT * FROM cash_registers ORDER BY id DESC LIMIT 5");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "Register #{$r['id']}: branch={$r['branch_id']}, user={$r['user_id']}, status={$r['status']}, float=₹{$r['opening_float']}, expected=₹{$r['expected_cash']}, opened_at={$r['opened_at']}, closed_at={$r['closed_at']}\n";
}

echo "\n=== Recent Orders ===\n";
$stmt = $pdo->query("SELECT id, order_number, order_type, branch_id, payment_method_id, payment_status, status, final_total, created_at, updated_at FROM orders ORDER BY id DESC LIMIT 10");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $o) {
    echo "Order #{$o['id']} ({$o['order_number']}): status={$o['status']}, payment_status={$o['payment_status']}, method={$o['payment_method_id']}, total=₹{$o['final_total']}, created_at={$o['created_at']}, updated_at={$o['updated_at']}\n";
}

echo "\n=== Order Payments ===\n";
$stmt = $pdo->query("SELECT op.*, pm.name as method_name FROM order_payments op LEFT JOIN payment_methods pm ON op.payment_method_id = pm.id ORDER BY op.id DESC LIMIT 10");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
    echo "Payment #{$p['id']}: order={$p['order_id']}, method={$p['method_name']} ({$p['payment_method_id']}), amount=₹{$p['amount']}, created_at={$p['created_at']}\n";
}
