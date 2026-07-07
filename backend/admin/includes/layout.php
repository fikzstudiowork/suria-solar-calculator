<?php
function adminHeader(string $title, string $active = 'dashboard'): void
{
    $nav = [
        'dashboard' => ['Leads Dashboard', '/admin/dashboard.php', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        'settings' => ['Site Settings', '/admin/settings.php', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> — Suria Admin</title>
  <style>
    :root {
      --si-navy: #0C2637;
      --si-navy-light: #163A50;
      --si-orange: #F47421;
      --si-orange-dark: #D9611A;
      --si-bg: #FAF8F5;
      --si-border: #ECECEC;
      --si-muted: #9AA3AC;
      --si-success: #2E9E5B;
      --si-error: #D93025;
      --si-radius: 14px;
      --si-radius-sm: 8px;
      --si-shadow: 0 1px 2px rgba(12,38,55,0.04), 0 4px 16px rgba(12,38,55,0.06);
      --si-sidebar-w: 240px;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', Montserrat, sans-serif;
      background: var(--si-bg);
      color: var(--si-navy);
      -webkit-font-smoothing: antialiased;
    }

    /* Sidebar */
    .sidebar-toggle { display: none; }
    .sidebar {
      position: fixed; top: 0; left: 0; bottom: 0; width: var(--si-sidebar-w);
      background: var(--si-navy); color: #fff; display: flex; flex-direction: column;
      z-index: 40; transition: transform 0.25s ease;
    }
    .sidebar .brand { padding: 22px 22px 18px; display: flex; align-items: center; gap: 10px; }
    .sidebar .brand .dot { width: 9px; height: 9px; border-radius: 50%; background: var(--si-orange); box-shadow: 0 0 0 4px rgba(244,116,33,0.2); }
    .sidebar .brand h1 { font-size: 16px; font-weight: 800; letter-spacing: 0.01em; }
    .sidebar .brand h1 span { color: var(--si-orange); }
    .sidebar nav { flex: 1; padding: 10px 12px; display: flex; flex-direction: column; gap: 3px; }
    .sidebar nav a {
      display: flex; align-items: center; gap: 11px; color: rgba(255,255,255,0.68);
      text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 14px;
      border-radius: var(--si-radius-sm); transition: background 0.15s, color 0.15s;
    }
    .sidebar nav a svg { width: 17px; height: 17px; flex-shrink: 0; opacity: 0.85; }
    .sidebar nav a.active { background: var(--si-orange); color: #fff; }
    .sidebar nav a.active svg { opacity: 1; }
    .sidebar nav a:not(.active):hover { background: rgba(255,255,255,0.06); color: #fff; }
    .sidebar .foot { padding: 14px 12px 18px; border-top: 1px solid rgba(255,255,255,0.08); }
    .sidebar .foot a {
      display: flex; align-items: center; gap: 11px; color: rgba(255,255,255,0.55);
      text-decoration: none; font-size: 13px; font-weight: 600; padding: 9px 14px; border-radius: var(--si-radius-sm);
    }
    .sidebar .foot a:hover { color: var(--si-orange); background: rgba(255,255,255,0.06); }
    .sidebar .foot a svg { width: 16px; height: 16px; }

    /* Mobile top bar */
    .mobile-bar { display: none; }

    .main { margin-left: var(--si-sidebar-w); min-height: 100vh; }
    .container { max-width: 1200px; margin: 0 auto; padding: 32px 28px 60px; }

    @media (max-width: 860px) {
      .sidebar { transform: translateX(-100%); box-shadow: 0 0 40px rgba(0,0,0,0.25); }
      .sidebar-toggle:checked ~ .sidebar { transform: translateX(0); }
      .sidebar-toggle:checked ~ .backdrop { opacity: 1; pointer-events: auto; }
      .backdrop {
        position: fixed; inset: 0; background: rgba(12,38,55,0.5); z-index: 30;
        opacity: 0; pointer-events: none; transition: opacity 0.2s;
      }
      .main { margin-left: 0; }
      .mobile-bar {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        background: var(--si-navy); color: #fff; padding: 14px 18px; position: sticky; top: 0; z-index: 20;
      }
      .mobile-bar h1 { font-size: 15px; font-weight: 800; }
      .mobile-bar h1 span { color: var(--si-orange); }
      .mobile-bar label { display: flex; flex-direction: column; gap: 4px; cursor: pointer; padding: 6px; }
      .mobile-bar label i { display: block; width: 20px; height: 2px; background: #fff; border-radius: 2px; }
      .container { padding: 20px 16px 40px; }
    }

    /* Content */
    .page-head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 22px; }
    .page-head h2 { font-size: 22px; font-weight: 800; letter-spacing: -0.01em; }

    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 26px; }
    .stat-card {
      background: #fff; border: 1px solid var(--si-border); border-radius: var(--si-radius);
      padding: 20px; box-shadow: var(--si-shadow); display: flex; flex-direction: column; gap: 10px;
    }
    .stat-card .icon {
      width: 34px; height: 34px; border-radius: 10px; background: rgba(244,116,33,0.1);
      display: flex; align-items: center; justify-content: center; color: var(--si-orange);
    }
    .stat-card .icon svg { width: 18px; height: 18px; }
    .stat-card .val { font-size: 26px; font-weight: 800; color: var(--si-navy); }
    .stat-card .lbl { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--si-muted); }

    .card { background: #fff; border: 1px solid var(--si-border); border-radius: var(--si-radius); padding: 24px; margin-bottom: 20px; box-shadow: var(--si-shadow); }
    .card h2 { font-size: 17px; font-weight: 800; margin-bottom: 16px; }

    label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    input, select, textarea {
      width: 100%; padding: 10px 12px; border: 1px solid var(--si-border); border-radius: var(--si-radius-sm);
      font-size: 14px; margin-bottom: 16px; transition: border-color 0.15s, box-shadow 0.15s; font-family: inherit;
    }
    input:focus, textarea:focus, select:focus { outline: none; border-color: var(--si-orange); box-shadow: 0 0 0 3px rgba(244,116,33,0.12); }

    .btn {
      display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: var(--si-orange); color: #fff;
      border: none; border-radius: var(--si-radius-sm); font-weight: 700; cursor: pointer; text-decoration: none;
      font-size: 14px; transition: background 0.15s, transform 0.1s;
    }
    .btn:hover { background: var(--si-orange-dark); }
    .btn:active { transform: scale(0.98); }
    .btn-secondary { background: var(--si-navy); }
    .btn-secondary:hover { background: var(--si-navy-light); }
    .btn-outline { background: transparent; color: var(--si-navy); border: 2px solid var(--si-border); }
    .btn-outline:hover { border-color: var(--si-navy); background: transparent; }

    .success { background: rgba(46,158,91,0.1); color: var(--si-success); padding: 12px 16px; border-radius: var(--si-radius-sm); margin-bottom: 16px; font-size: 14px; font-weight: 600; }
    .error { background: rgba(217,48,37,0.1); color: var(--si-error); padding: 12px 16px; border-radius: var(--si-radius-sm); margin-bottom: 16px; font-size: 14px; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .grid-2 { grid-template-columns: 1fr; } }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 720px; }
    th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--si-border); }
    th { background: var(--si-bg); font-weight: 700; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--si-muted); }
    tbody tr { transition: background 0.12s; }
    tbody tr:hover { background: rgba(244,116,33,0.035); }

    .pill { display: inline-block; padding: 4px 11px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: capitalize; }
    .pill-new { background: rgba(244,116,33,0.12); color: var(--si-orange-dark); }
    .pill-contacted { background: rgba(58,125,217,0.12); color: #2E6BD9; }
    .pill-quoted { background: rgba(155,89,224,0.12); color: #8B3FE0; }
    .pill-closed { background: rgba(46,158,91,0.12); color: var(--si-success); }

    .filters { display: flex; flex-wrap: wrap; gap: 12px; align-items: end; margin-bottom: 20px; }
    .filters label { margin-bottom: 4px; }
    .filters input, .filters select { margin-bottom: 0; width: auto; min-width: 140px; }
    .logo-preview { max-height: 60px; margin-bottom: 12px; border-radius: 8px; background: #fff; padding: 6px; border: 1px solid var(--si-border); }
  </style>
</head>
<body>
  <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">

  <div class="mobile-bar">
    <h1>Suria <span>Admin</span></h1>
    <label for="sidebar-toggle"><i></i><i></i><i></i></label>
  </div>

  <label for="sidebar-toggle" class="backdrop"></label>

  <aside class="sidebar">
    <div class="brand">
      <span class="dot"></span>
      <h1>Suria <span>Admin</span></h1>
    </div>
    <nav>
      <?php foreach ($nav as $key => [$label, $url, $icon]): ?>
        <a href="<?= e($url) ?>" class="<?= $active === $key ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="<?= $icon ?>"/></svg>
          <?= e($label) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="foot">
      <a href="/admin/logout.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Logout
      </a>
    </div>
  </aside>

  <div class="main">
    <div class="container">
      <?php
}

function adminFooter(): void
{
    ?>
    </div>
  </div>
</body>
</html>
    <?php
}
