<?php
declare(strict_types=1);

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=rms_db;charset=utf8mb4', 'root', 'root');
$roles = $pdo->query('SELECT id, name, slug FROM roles')->fetchAll(PDO::FETCH_ASSOC);
foreach ($roles as $r) {
    $stmt = $pdo->prepare('SELECT p.slug FROM role_permissions rp JOIN permissions p ON p.id = rp.permission_id WHERE rp.role_id = ?');
    $stmt->execute([$r['id']]);
    $slugs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Role {$r['id']} ({$r['slug']}): " . count($slugs) . " perms\n";
    $hasDash = in_array('dashboard.view', $slugs, true) ? 'YES' : 'NO';
    echo "  dashboard.view: {$hasDash}\n";
    echo "  all slugs: " . implode(', ', $slugs) . "\n\n";
}
