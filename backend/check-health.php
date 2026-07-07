<?php
/**
 * Health check — DELETE after setup complete.
 * Open: https://calculator.suriainfiniti.com/check-health.php
 */
header('Content-Type: text/html; charset=utf-8');

$checks = [];

$checks[] = ['PHP version', PHP_VERSION, version_compare(PHP_VERSION, '8.0.0', '>=')];

$pdoMysql = extension_loaded('pdo_mysql');
$checks[] = ['PDO MySQL (pdo_mysql)', $pdoMysql ? 'Enabled' : 'MISSING — enable in cPanel MultiPHP', $pdoMysql];

$mysqli = extension_loaded('mysqli');
$checks[] = ['MySQLi', $mysqli ? 'Enabled' : 'Optional', true];

$configPath = __DIR__ . '/config.php';
$configExamplePath = __DIR__ . '/config.example.php';
$configExists = file_exists($configPath);
$configExampleExists = file_exists($configExamplePath);
$configMsg = $configExists
    ? 'Found config.php'
    : ($configExampleExists ? 'Using config.example.php (rename/copy to config.php recommended)' : 'Missing — copy from config.example.php');
$checks[] = ['config.php', $configMsg, $configExists || $configExampleExists];

$dbOk = false;
$dbMsg = 'Not tested';
if ($pdoMysql && ($configExists || $configExampleExists)) {
    try {
        require_once __DIR__ . '/includes/db.php';
        getDb()->query('SELECT 1');
        $dbOk = true;
        $dbMsg = 'Connected OK';
    } catch (Throwable $e) {
        $dbMsg = $e->getMessage();
    }
}
$checks[] = ['Database connection', $dbMsg, $dbOk];

$tablesOk = false;
$leadsTableOk = false;
$tableMsg = 'Fix connection first';
$leadsTableMsg = 'Fix connection first';

if ($dbOk) {
    try {
        getDb()->query('SELECT 1 FROM admin_users LIMIT 1');
        $tablesOk = true;
        $tableMsg = 'admin_users OK';
    } catch (Throwable $e) {
        $tableMsg = 'Import schema.sql via phpMyAdmin (admin_users missing)';
    }

    try {
        getDb()->query('SELECT 1 FROM suria_calc_leads LIMIT 1');
        $leadsTableOk = true;
        $leadsTableMsg = 'suria_calc_leads OK';
    } catch (Throwable $e) {
        $leadsTableMsg = 'Import schema.sql via phpMyAdmin (suria_calc_leads missing)';
    }
}

$checks[] = ['Admin users table', $tableMsg, $tablesOk];
$checks[] = ['Leads table', $leadsTableMsg, $leadsTableOk];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suria Calculator Health Check</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #FAF8F5; color: #0C2637; max-width: 560px; margin: 40px auto; padding: 24px; }
    h1 { font-size: 22px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #ECECEC; font-size: 14px; }
    th { background: #0C2637; color: #fff; }
    .ok { color: #2e7d32; font-weight: 700; }
    .bad { color: #c62828; font-weight: 700; }
    .help { margin-top: 20px; padding: 16px; background: #fff3e0; border-radius: 12px; font-size: 14px; line-height: 1.6; }
  </style>
</head>
<body>
  <h1>Suria Calculator — Health Check</h1>
  <table>
    <tr><th>Check</th><th>Result</th><th>Status</th></tr>
    <?php foreach ($checks as [$label, $result, $pass]): ?>
    <tr>
      <td><?= htmlspecialchars($label) ?></td>
      <td><?= htmlspecialchars((string) $result) ?></td>
      <td class="<?= $pass ? 'ok' : 'bad' ?>"><?= $pass ? 'OK' : 'FIX' ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php if (!$pdoMysql): ?>
  <div class="help">
    <strong>Fix "could not find driver":</strong><br>
    cPanel → <strong>MultiPHP Manager</strong> → pilih subdomain calculator → PHP 8.1 atau 8.2<br>
    cPanel → <strong>Select PHP Version</strong> → tick <strong>pdo_mysql</strong> dan <strong>pdo</strong> → Save
  </div>
  <?php endif; ?>
  <?php if ($pdoMysql && !$dbOk): ?>
  <div class="help">
    <strong>Fix database connection:</strong><br>
    1. cPanel → MySQL Databases → buat database + user<br>
    2. phpMyAdmin → Import <code>schema.sql</code><br>
    3. Edit <code>config.php</code> — isi db name, user, password (format cPanel: <code>suriainfiniti_namadb</code>)
  </div>
  <?php endif; ?>
</body>
</html>
