<?php

require_once __DIR__ . '/bootstrap.php';

function getDb(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $cfg = loadConfig()['db'];
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $cfg['host'],
        $cfg['name'],
        $cfg['charset']
    );
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function getConfigValue(string $key, ?string $default = null): ?string
{
    $stmt = getDb()->prepare('SELECT config_value FROM suria_calc_config WHERE config_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['config_value'] : $default;
}

function getAllCalcConfig(): array
{
    return [
        'tariffRate' => (float) getConfigValue('tariff_rate', '0.571'),
        'costPerKwp' => (float) getConfigValue('cost_per_kwp', '4200'),
        'sunHours' => (float) getConfigValue('sun_hours', '4.5'),
        'derate' => (float) getConfigValue('derate', '0.85'),
        'offsetPercent' => (float) getConfigValue('offset_percent', '0.90'),
        'exposureFactors' => [
            'Excellent' => (float) getConfigValue('exposure_excellent', '1.0'),
            'Good' => (float) getConfigValue('exposure_good', '1.15'),
            'Moderate' => (float) getConfigValue('exposure_moderate', '1.3'),
        ],
    ];
}
