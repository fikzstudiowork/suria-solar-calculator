<?php

require_once dirname(__DIR__) . '/includes/auth.php';

logoutAdmin();
header('Location: /admin/login.php');
exit;
