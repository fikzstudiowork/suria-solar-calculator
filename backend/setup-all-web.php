<?php
/**
 * One-time full setup: config.php + schema + admin user.
 * DELETE this file after setup succeeds.
 * https://calculator.suriainfiniti.com/setup-all-web.php
 */
declare(strict_types=1);

$configPath = __DIR__ . '/config.php';
$schemaPath = __DIR__ . '/schema.sql';
$message = '';
$error = '';
$done = false;

$defaults = [
    'db_host' => 'localhost',
    'db_name' => 'suriainfico_suriacalculator',
    'db_user' => 'suriainfico_usercalculator',
    'admin_user' => 'admin',
];

function importSchema(PDO $pdo, string $schemaPath): void
{
    if (!file_exists($schemaPath)) {
        throw new RuntimeException('schema.sql not found on server.');
    }

    $sql = file_get_contents($schemaPath);
    $sql = preg_replace('/^--.*$/m', '', $sql);
    $sql = preg_replace('/^\s*$/m', '', $sql);

    foreach (explode(';', $sql) as $statement) {
        $statement = trim($statement);
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }
}

function buildConfig(string $host, string $name, string $user, string $pass, string $csrfSecret): string
{
    return sprintf(
        <<<'PHP'
<?php
return [
    'db' => [
        'host' => '%s',
        'name' => '%s',
        'user' => '%s',
        'pass' => '%s',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'env' => 'production',
        'allowed_origins' => [
            'https://calculator.suriainfiniti.com',
            'https://suriainfiniti.com',
            'https://suria-solar-calculator.vercel.app',
        ],
        'privacy_policy_version' => '1.0',
    ],
    'csrf' => [
        'secret' => '%s',
        'ttl_seconds' => 3600,
    ],
    'turnstile' => [
        'secret_key' => '1x0000000000000000000000000000000AA',
    ],
    'mail' => [
        'enabled' => true,
        'to' => 'info@suriainfiniti.com',
        'from' => 'noreply@suriainfiniti.com',
        'from_name' => 'Suria Solar Calculator',
        'smtp_host' => 'localhost',
        'smtp_port' => 587,
        'smtp_user' => '',
        'smtp_pass' => '',
        'smtp_secure' => 'tls',
    ],
    'rate_limit' => [
        'max_requests' => 5,
        'window_seconds' => 3600,
    ],
    'google' => [
        'places_api_key' => '',
    ],
];
PHP,
        addcslashes($host, "\\'"),
        addcslashes($name, "\\'"),
        addcslashes($user, "\\'"),
        addcslashes($pass, "\\'"),
        $csrfSecret
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';
    $adminUser = trim($_POST['admin_user'] ?? 'admin');
    $adminPass = $_POST['admin_pass'] ?? '';

    if (!$dbName || !$dbUser || !$dbPass) {
        $error = 'Database name, user, and password are required.';
    } elseif (!$adminUser || strlen($adminPass) < 8) {
        $error = 'Admin username required. Admin password must be at least 8 characters.';
    } else {
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $dbHost, $dbName);
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            importSchema($pdo, $schemaPath);

            $csrfSecret = bin2hex(random_bytes(32));
            $configContents = buildConfig($dbHost, $dbName, $dbUser, $dbPass, $csrfSecret);
            if (file_put_contents($configPath, $configContents) === false) {
                throw new RuntimeException('Could not write config.php — check folder permissions.');
            }

            $hash = password_hash($adminPass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare(
                'INSERT INTO admin_users (username, password_hash) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)'
            );
            $stmt->execute([$adminUser, $hash]);

            $done = true;
            $message = "Setup complete! Login at /admin/login.php with username \"{$adminUser}\". DELETE setup-all-web.php, setup-config-web.php, setup-web.php, and check-health.php now.";
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            if (stripos($msg, 'Access denied') !== false) {
                $error = 'MySQL access denied — check DB user, password, and ALL PRIVILEGES in cPanel.';
            } elseif (stripos($msg, 'Unknown database') !== false) {
                $error = 'Database not found — create it in cPanel → MySQL Databases first.';
            } else {
                $error = $msg;
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
  <title>Suria Calculator — Full Setup</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #FAF8F5; color: #0C2637; max-width: 520px; margin: 40px auto; padding: 24px; }
    .card { background: #fff; border: 1px solid #ECECEC; border-radius: 12px; padding: 24px; }
    h1 { font-size: 20px; margin-bottom: 8px; }
    p { font-size: 14px; color: #666; line-height: 1.6; }
    h2 { font-size: 15px; margin: 20px 0 8px; }
    label { display: block; font-weight: 600; font-size: 13px; margin: 10px 0 4px; }
    input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
    button { width: 100%; margin-top: 20px; padding: 12px; background: #F47421; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 15px; }
    .ok { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 12px; font-size: 14px; }
    .err { background: #fdecea; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 12px; font-size: 14px; }
    a { color: #F47421; font-weight: 700; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Suria Calculator — Full Setup</h1>
    <p>Creates <code>config.php</code>, imports tables, and creates admin login in one step.</p>
    <?php if ($message): ?><div class="ok"><?= htmlspecialchars($message) ?><br><a href="/admin/login.php">Go to Admin Login →</a></div><?php endif; ?>
    <?php if ($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (!$done): ?>
    <form method="post">
      <h2>MySQL (cPanel)</h2>
      <label>DB Host</label>
      <input name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? $defaults['db_host']) ?>">
      <label>DB Name</label>
      <input name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? $defaults['db_name']) ?>" required>
      <label>DB User</label>
      <input name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? $defaults['db_user']) ?>" required>
      <label>DB Password</label>
      <input type="password" name="db_pass" required placeholder="From cPanel → MySQL Databases">

      <h2>Admin Login</h2>
      <label>Admin Username</label>
      <input name="admin_user" value="<?= htmlspecialchars($_POST['admin_user'] ?? $defaults['admin_user']) ?>" required>
      <label>Admin Password (min 8 chars)</label>
      <input type="password" name="admin_pass" required minlength="8" placeholder="Choose a strong password">

      <button type="submit">Run Full Setup</button>
    </form>
    <?php endif; ?>
  </div>
</body>
</html>
