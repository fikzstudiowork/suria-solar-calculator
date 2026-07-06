<?php

require_once dirname(__DIR__) . '/includes/cors.php';
require_once dirname(__DIR__) . '/includes/db.php';

applyCors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Method not allowed', 405);
}

try {
    jsonResponse(getAllCalcConfig());
} catch (Exception $e) {
    jsonResponse([
        'tariffRate' => 0.571,
        'costPerKwp' => 4200,
        'sunHours' => 4.5,
        'derate' => 0.85,
        'offsetPercent' => 0.90,
        'exposureFactors' => [
            'Excellent' => 1.0,
            'Good' => 1.15,
            'Moderate' => 1.3,
        ],
    ]);
}
