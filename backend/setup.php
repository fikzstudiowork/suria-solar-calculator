<?php
/**
 * One-time setup: creates config.php and admin user.
 * Run: php setup.php
 * DELETE THIS FILE after running on production.
 */

echo "Suria Solar Calculator — Setup\n\n";

$configPath = __DIR__ . '/config.php';
if (!file_exists($configPath)) {
    copy(__DIR__ . '/config.example.php', $configPath);
    echo "Created config.php from config.example.php\n";
    echo "IMPORTANT: Edit config.php with your DB credentials and secrets.\n\n";
} else {
    echo "config.php already exists.\n\n";
}

require_once __DIR__ . '/includes/db.php';

$username = readline('Admin username [admin]: ') ?: 'admin';
$password = readline('Admin password: ');
if (!$password) {
    echo "Password required.\n";
    exit(1);
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$db = getDb();
$stmt = $db->prepare(
    'INSERT INTO admin_users (username, password_hash) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)'
);
$stmt->execute([$username, $hash]);

echo "\nAdmin user '$username' saved.\n";
echo "Setup complete. Delete setup.php before going live.\n";
