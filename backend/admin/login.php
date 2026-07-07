<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

startAdminSession();

if (isAdminLoggedIn()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        if ($username && $password && attemptLogin($username, $password)) {
            header('Location: /admin/dashboard.php');
            exit;
        }
        $error = 'Invalid username or password.';
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        if (stripos($msg, 'Access denied') !== false || stripos($msg, 'Unknown database') !== false) {
            $error = 'Database not configured. Open /setup-config-web.php first, import schema.sql, then try again.';
        } else {
            $error = 'Server error: ' . $msg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Suria Solar Calculator</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif; color: #0C2637; min-height: 100vh;
      display: flex; align-items: center; justify-content: center; padding: 20px;
      background: linear-gradient(155deg, #0C2637 0%, #163A50 55%, #0C2637 100%);
    }
    .card {
      background: #fff; border-radius: 20px; padding: 44px 40px; width: 100%; max-width: 400px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .brand { display: flex; align-items: center; gap: 9px; margin-bottom: 22px; }
    .brand .dot { width: 9px; height: 9px; border-radius: 50%; background: #F47421; box-shadow: 0 0 0 4px rgba(244,116,33,0.15); }
    .brand span { font-size: 14px; font-weight: 800; letter-spacing: 0.02em; color: #0C2637; }
    .brand span em { color: #F47421; font-style: normal; }
    h1 { font-size: 22px; margin-bottom: 6px; font-weight: 800; }
    p { color: #9AA3AC; font-size: 14px; margin-bottom: 26px; }
    label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    input { width: 100%; padding: 12px 14px; border: 1px solid #ECECEC; border-radius: 10px; font-size: 15px; margin-bottom: 16px; transition: border-color .15s, box-shadow .15s; }
    input:focus { outline: none; border-color: #F47421; box-shadow: 0 0 0 3px rgba(244,116,33,0.15); }
    button {
      width: 100%; padding: 13px; background: #F47421; color: #fff; border: none; border-radius: 10px;
      font-size: 15px; font-weight: 700; cursor: pointer; transition: background .15s, transform .1s;
    }
    button:hover { background: #D9611A; }
    button:active { transform: scale(0.98); }
    .error { background: rgba(217,48,37,0.08); color: #D93025; font-size: 13px; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; }
  </style>
</head>
<body>
  <div class="card">
    <div class="brand"><span class="dot"></span><span>Suria <em>Admin</em></span></div>
    <h1>Welcome back</h1>
    <p>Sign in to manage leads and site settings.</p>
    <?php if ($error): ?>
      <p class="error"><?= e($error) ?></p>
    <?php endif; ?>
    <form method="post">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required autocomplete="username">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autocomplete="current-password">
      <button type="submit">Sign In</button>
    </form>
  </div>
</body>
</html>
