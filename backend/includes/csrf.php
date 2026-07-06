<?php

require_once __DIR__ . '/bootstrap.php';

function generateCsrfToken(): string
{
    $config = loadConfig()['csrf'];
    $payload = json_encode([
        'exp' => time() + $config['ttl_seconds'],
        'nonce' => bin2hex(random_bytes(16)),
    ]);
    $sig = hash_hmac('sha256', $payload, $config['secret']);
    return base64_encode($payload) . '.' . $sig;
}

function verifyCsrfToken(string $token): bool
{
    $config = loadConfig()['csrf'];
    $parts = explode('.', $token, 2);
    if (count($parts) !== 2) {
        return false;
    }

    [$encoded, $sig] = $parts;
    $payload = base64_decode($encoded, true);
    if ($payload === false) {
        return false;
    }

    $expected = hash_hmac('sha256', $payload, $config['secret']);
    if (!hash_equals($expected, $sig)) {
        return false;
    }

    $data = json_decode($payload, true);
    if (!$data || !isset($data['exp']) || $data['exp'] < time()) {
        return false;
    }

    return true;
}
