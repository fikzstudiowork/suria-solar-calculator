<?php
function adminHeader(string $title, string $active = 'dashboard'): void
{
    $nav = [
        'dashboard' => ['Leads Dashboard', '/admin/dashboard.php'],
        'settings' => ['Site Settings', '/admin/settings.php'],
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> — Suria Admin</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Montserrat, sans-serif; background: #FAF8F5; color: #0C2637; }
    .header { background: #0C2637; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
    .header h1 { font-size: 17px; font-weight: 800; }
    .header h1 span { color: #F47421; }
    .nav { display: flex; gap: 8px; align-items: center; }
    .nav a { color: rgba(255,255,255,0.75); text-decoration: none; font-size: 13px; font-weight: 600; padding: 8px 14px; border-radius: 8px; }
    .nav a.active, .nav a:hover { background: rgba(244,116,33,0.2); color: #F47421; }
    .nav .logout { color: #F47421; }
    .container { max-width: 1200px; margin: 0 auto; padding: 24px; }
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: #fff; border: 1px solid #ECECEC; border-radius: 14px; padding: 20px; }
    .stat-card .val { font-size: 28px; font-weight: 800; color: #F47421; }
    .stat-card .lbl { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #9AA3AC; margin-top: 4px; }
    .card { background: #fff; border: 1px solid #ECECEC; border-radius: 14px; padding: 24px; margin-bottom: 20px; }
    .card h2 { font-size: 18px; font-weight: 800; margin-bottom: 16px; }
    label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #ECECEC; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
    input:focus, textarea:focus { outline: none; border-color: #F47421; box-shadow: 0 0 0 3px rgba(244,116,33,0.12); }
    .btn { display: inline-block; padding: 10px 20px; background: #F47421; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; text-decoration: none; font-size: 14px; }
    .btn:hover { background: #D9611A; }
    .btn-secondary { background: #0C2637; }
    .btn-secondary:hover { background: #1a3a4f; }
    .btn-outline { background: transparent; color: #0C2637; border: 2px solid #0C2637; }
    .success { background: rgba(46,158,91,0.1); color: #2E9E5B; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
    .error { background: rgba(217,48,37,0.1); color: #D93025; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .grid-2 { grid-template-columns: 1fr; } }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #ECECEC; }
    th { background: #FAF8F5; font-weight: 700; font-size: 11px; text-transform: uppercase; color: #9AA3AC; }
    .filters { display: flex; flex-wrap: wrap; gap: 12px; align-items: end; margin-bottom: 20px; }
    .filters label { margin-bottom: 4px; }
    .filters input, .filters select { margin-bottom: 0; width: auto; min-width: 140px; }
    .logo-preview { max-height: 60px; margin-bottom: 12px; border-radius: 8px; }
  </style>
</head>
<body>
  <div class="header">
    <h1>Suria <span>Admin</span></h1>
    <nav class="nav">
      <?php foreach ($nav as $key => [$label, $url]): ?>
        <a href="<?= e($url) ?>" class="<?= $active === $key ? 'active' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="/admin/logout.php" class="logout">Logout</a>
    </nav>
  </div>
  <div class="container">
    <?php
}

function adminFooter(): void
{
    ?>
  </div>
</body>
</html>
    <?php
}
