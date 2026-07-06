<?php

require_once dirname(__DIR__) . '/includes/cors.php';
require_once dirname(__DIR__) . '/includes/csrf.php';

applyCors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Method not allowed', 405);
}

jsonResponse(['token' => generateCsrfToken()]);
