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

    if ($username && $password && attemptLogin($username, $password)) {
        header('Location: /admin/dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
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
    body { font-family: 'Segoe UI', sans-serif; background: #FAF8F5; color: #0C2637; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .card { background: #fff; border: 1px solid #ECECEC; border-radius: 16px; padding: 40px; width: 100%; max-width: 400px; }
    h1 { font-size: 22px; margin-bottom: 8px; }
    p { color: #9AA3AC; font-size: 14px; margin-bottom: 24px; }
    label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    input { width: 100%; padding: 12px; border: 1px solid #ECECEC; border-radius: 8px; font-size: 15px; margin-bottom: 16px; }
    input:focus { outline: none; border-color: #F47421; box-shadow: 0 0 0 3px rgba(244,116,33,0.15); }
    button { width: 100%; padding: 12px; background: #F47421; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; }
    button:hover { background: #D9611A; }
    .error { color: #E0503A; font-size: 13px; margin-bottom: 16px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Admin Login</h1>
    <p>Suria Solar Calculator — Leads Dashboard</p>
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
