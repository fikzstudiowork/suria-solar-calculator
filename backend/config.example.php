<?php
/**
 * Suria Solar Calculator — Configuration
 * Copy to config.php and fill in live values. Never commit config.php.
 */

return [
    'db' => [
        'host' => 'localhost',
        'name' => 'suria_calc',
        'user' => 'suria_calc_user',
        'pass' => 'CHANGE_ME',
        'charset' => 'utf8mb4',
    ],

    'app' => [
        'env' => 'development',
        'allowed_origins' => [
            'http://localhost:3000',
            'https://calculator.suriainfiniti.com',
            'https://suriainfiniti.com',
        ],
        'privacy_policy_version' => '1.0',
    ],

    'csrf' => [
        'secret' => 'CHANGE_ME_TO_RANDOM_64_CHAR_STRING',
        'ttl_seconds' => 3600,
    ],

    'turnstile' => [
        'secret_key' => '1x0000000000000000000000000000000AA',
    ],

    'mail' => [
        'enabled' => true,
        'to' => 'info@suriainfiniti.com',
        'from' => 'noreply@suriainfiniti.com',
        'from_name' => 'Suria Solar Calculator',
        'smtp_host' => 'localhost',
        'smtp_port' => 587,
        'smtp_user' => '',
        'smtp_pass' => '',
        'smtp_secure' => 'tls',
    ],

    'rate_limit' => [
        'max_requests' => 5,
        'window_seconds' => 3600,
    ],

    'google' => [
        'places_api_key' => '', // Google Cloud → Places API key (server-side only)
    ],
];
