<?php

require_once __DIR__ . '/bootstrap.php';

function applyCors(): void
{
    $config = loadConfig();
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $config['app']['allowed_origins'], true)) {
        header("Access-Control-Allow-Origin: $origin");
        header('Access-Control-Allow-Credentials: true');
    }

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function jsonResponse(array $data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function jsonError(string $message, int $code = 400): void
{
    jsonResponse(['success' => false, 'message' => $message], $code);
}
