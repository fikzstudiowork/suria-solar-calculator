<?php
/**
 * Browser setup for cPanel (one-time). DELETE after use.
 * Open: https://calculator.suriainfiniti.com/setup-web.php
 */
require_once __DIR__ . '/includes/bootstrap.php';

$configPath = __DIR__ . '/config.php';
$message = '';
$error = '';

if (!file_exists($configPath)) {
    if (file_exists(__DIR__ . '/config.example.php')) {
        copy(__DIR__ . '/config.example.php', $configPath);
        $message = 'config.php created. Edit DB credentials in File Manager first if setup fails.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? 'admin');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) {
        $error = 'Username and password required.';
    } else {
        try {
            require_once __DIR__ . '/includes/db.php';
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $db = getDb();
            $stmt = $db->prepare(
                'INSERT INTO admin_users (username, password_hash) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)'
            );
            $stmt->execute([$username, $hash]);
            $message = "Admin user '$username' saved. DELETE setup-web.php and setup.php now.";
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            if (stripos($msg, 'could not find driver') !== false) {
                $error = 'PHP extension pdo_mysql belum aktif. cPanel → MultiPHP Manager → PHP 8.1/8.2 → enable pdo_mysql + pdo. Then reload this page.';
            } elseif (stripos($msg, 'Access denied') !== false || stripos($msg, 'Unknown database') !== false) {
                $error = 'Database config salah: edit config.php (db name/user/pass cPanel), import schema.sql via phpMyAdmin.';
            } else {
                $error = 'Database error: ' . $msg;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suria Calculator Setup</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #FAF8F5; color: #0C2637; max-width: 420px; margin: 40px auto; padding: 24px; }
    .card { background: #fff; border: 1px solid #ECECEC; border-radius: 12px; padding: 24px; }
    h1 { font-size: 20px; margin-bottom: 8px; }
    p { font-size: 14px; color: #666; margin-bottom: 16px; }
    label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; }
    input { width: 100%; padding: 10px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 8px; }
    button { width: 100%; padding: 12px; background: #F47421; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; }
    .ok { background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 8px; margin-bottom: 12px; font-size: 14px; }
    .err { background: #fdecea; color: #c62828; padding: 10px; border-radius: 8px; margin-bottom: 12px; font-size: 14px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Suria Calculator Setup</h1>
    <p>Create admin login. Import <code>schema.sql</code> and edit <code>config.php</code> DB settings first.</p>
    <?php if ($message): ?><div class="ok"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <label>Admin username</label>
      <input name="username" value="admin" required>
      <label>Admin password</label>
      <input type="password" name="password" required>
      <button type="submit">Create Admin User</button>
    </form>
  </div>
</body>
</html>
