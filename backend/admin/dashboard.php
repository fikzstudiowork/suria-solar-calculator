<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/settings.php';
require_once __DIR__ . '/includes/layout.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $leadId = (int) ($_POST['lead_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['new', 'contacted', 'quoted', 'closed'];
    if ($leadId && in_array($status, $allowed, true)) {
        $stmt = getDb()->prepare('UPDATE suria_calc_leads SET status = ? WHERE id = ?');
        $stmt->execute([$status, $leadId]);
    }
    header('Location: /admin/dashboard.php?' . http_build_query($_GET));
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$stateFilter = $_GET['state'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$search = trim($_GET['search'] ?? '');

$where = ['1=1'];
$params = [];

if ($statusFilter) { $where[] = 'status = ?'; $params[] = $statusFilter; }
if ($stateFilter) { $where[] = 'state = ?'; $params[] = $stateFilter; }
if ($dateFrom) { $where[] = 'created_at >= ?'; $params[] = $dateFrom . ' 00:00:00'; }
if ($dateTo) { $where[] = 'created_at <= ?'; $params[] = $dateTo . ' 23:59:59'; }
if ($search) {
    $where[] = '(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}

$dbError = '';
$leads = [];
$states = [];

try {
    $sql = 'SELECT * FROM suria_calc_leads WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC LIMIT 200';
    $stmt = getDb()->prepare($sql);
    $stmt->execute($params);
    $leads = $stmt->fetchAll();
    $states = getDb()->query(
        'SELECT DISTINCT state FROM suria_calc_leads WHERE state IS NOT NULL ORDER BY state'
    )->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

$stats = getLeadStats();
$settings = getSiteSettings();
$waBase = 'https://wa.me/' . preg_replace('/\D/', '', $settings['whatsapp_number']);

adminHeader('Leads Dashboard', 'dashboard');

if ($dbError !== ''):
?>
<div class="error">
  <strong>Database setup incomplete.</strong>
  Leads table missing or not accessible. Import <code>schema.sql</code> via phpMyAdmin, then reload this page.
  <br><small style="opacity:0.8"><?= e($dbError) ?></small>
</div>
<?php
endif;
?>

<div class="page-head">
  <h2>Leads Dashboard</h2>
</div>

<div class="stats">
  <div class="stat-card">
    <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
    <div class="val"><?= (int)$stats['total'] ?></div><div class="lbl">Total Leads</div>
  </div>
  <div class="stat-card">
    <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg></div>
    <div class="val"><?= (int)$stats['new_today'] ?></div><div class="lbl">New Today</div>
  </div>
  <div class="stat-card">
    <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
    <div class="val"><?= (int)($stats['by_status']['new'] ?? 0) ?></div><div class="lbl">Awaiting Contact</div>
  </div>
  <div class="stat-card">
    <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg></div>
    <div class="val"><?= (int)($stats['by_status']['quoted'] ?? 0) ?></div><div class="lbl">Quoted</div>
  </div>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px">
  <p style="color:#9AA3AC;font-size:14px"><?= count($leads) ?> lead(s) shown (max 200)</p>
  <a href="/admin/export.php?<?= e(http_build_query($_GET)) ?>" class="btn">Export CSV</a>
</div>

<form class="filters card" method="get" style="padding:20px">
  <div><label>Search</label><input type="text" name="search" value="<?= e($search) ?>" placeholder="Name, email, phone"></div>
  <div><label>Status</label><select name="status"><option value="">All</option><?php foreach (['new','contacted','quoted','closed'] as $s): ?><option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div>
  <div><label>State</label><select name="state"><option value="">All</option><?php foreach ($states as $st): ?><option value="<?= e($st) ?>" <?= $stateFilter === $st ? 'selected' : '' ?>><?= e($st) ?></option><?php endforeach; ?></select></div>
  <div><label>From</label><input type="date" name="date_from" value="<?= e($dateFrom) ?>"></div>
  <div><label>To</label><input type="date" name="date_to" value="<?= e($dateTo) ?>"></div>
  <button type="submit" class="btn">Filter</button>
  <a href="/admin/dashboard.php" class="btn btn-outline">Reset</a>
</form>

<div class="card" style="padding:0;overflow:hidden">
  <div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Date</th><th>Name</th><th>Contact</th><th>State</th><th>kWp</th><th>Savings/mo</th><th>Status</th><th>WhatsApp</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($leads)): ?>
        <tr><td colspan="8" style="text-align:center;color:#9AA3AC;padding:32px">No leads yet.</td></tr>
      <?php else: ?>
        <?php foreach ($leads as $lead):
          $waText = urlencode("Hi {$lead['full_name']}, this is Suria Infiniti regarding your solar estimate ({$lead['recommended_kwp']} kWp).");
          $waLink = $waBase . '?text=' . $waText;
        ?>
          <tr>
            <td><?= e(date('d M Y H:i', strtotime($lead['created_at']))) ?></td>
            <td><strong><?= e($lead['full_name']) ?></strong><br><small style="color:#9AA3AC"><?= e($lead['property_type'] ?? '') ?></small></td>
            <td><?= e($lead['email']) ?><br><small><?= e($lead['phone']) ?></small></td>
            <td><?= e($lead['state'] ?? '—') ?></td>
            <td><?= e($lead['recommended_kwp']) ?></td>
            <td>RM <?= e(number_format((float)$lead['est_monthly_savings'], 0)) ?></td>
            <td>
              <span class="pill pill-<?= e($lead['status']) ?>"><?= e($lead['status']) ?></span>
              <form method="post" style="margin:6px 0 0">
                <input type="hidden" name="lead_id" value="<?= (int)$lead['id'] ?>">
                <select name="status" onchange="this.form.submit()" style="padding:4px 8px;border:1px solid #ECECEC;border-radius:6px;font-size:12px;margin-bottom:0">
                  <?php foreach (['new','contacted','quoted','closed'] as $s): ?>
                    <option value="<?= $s ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
                <input type="hidden" name="update_status" value="1">
              </form>
            </td>
            <td><a href="<?= e($waLink) ?>" target="_blank" rel="noopener" style="color:#25D366;font-weight:700;font-size:12px">Chat →</a></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>

<?php adminFooter(); ?>
