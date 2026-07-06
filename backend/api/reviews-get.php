<?php

require_once dirname(__DIR__) . '/includes/cors.php';
require_once dirname(__DIR__) . '/includes/google-reviews.php';

applyCors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Method not allowed', 405);
}

jsonResponse(['reviews' => getAllReviews()]);
