<?php

require_once __DIR__ . '/db.php';

const SITE_SETTING_KEYS = [
    'company_name',
    'company_tagline',
    'contact_email',
    'contact_phone',
    'whatsapp_number',
    'sales_email',
    'logo_url',
    'privacy_policy_url',
    'installations_count',
    'avg_savings_percent',
    'customer_rating',
    'google_place_id',
    'google_reviews_enabled',
    'manual_reviews_json',
    'google_reviews_cache',
];

function getSiteSettings(): array
{
    $defaults = [
        'company_name' => 'Suria Infiniti',
        'company_tagline' => 'Malaysia\'s trusted solar partner',
        'contact_email' => 'taufik@suriainfiniti.com',
        'contact_phone' => '+60 12-707 5391',
        'whatsapp_number' => '60127075391',
        'sales_email' => 'taufik@suriainfiniti.com',
        'logo_url' => '/images/logo-suria.svg',
        'privacy_policy_url' => 'https://suriainfiniti.com/privacy-policy/',
        'installations_count' => '500+',
        'avg_savings_percent' => '80%',
        'customer_rating' => '4.9/5',
        'google_place_id' => '',
        'google_reviews_enabled' => '0',
        'manual_reviews_json' => '',
        'google_reviews_cache' => '',
    ];

    $settings = $defaults;
    try {
        $rows = getDb()->query('SELECT config_key, config_value FROM suria_calc_config')->fetchAll();
        foreach ($rows as $row) {
            if (array_key_exists($row['config_key'], $settings)) {
                $settings[$row['config_key']] = $row['config_value'];
            }
        }
    } catch (Exception $e) {
        // use defaults if DB unavailable
    }

    return $settings;
}

function setSiteSetting(string $key, string $value): void
{
    if (!in_array($key, SITE_SETTING_KEYS, true)) {
        return;
    }
    $stmt = getDb()->prepare(
        'INSERT INTO suria_calc_config (config_key, config_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)'
    );
    $stmt->execute([$key, $value]);
}

function getLeadStats(): array
{
    $db = getDb();
    $total = (int) $db->query('SELECT COUNT(*) FROM suria_calc_leads')->fetchColumn();
    $newToday = (int) $db->query(
        "SELECT COUNT(*) FROM suria_calc_leads WHERE DATE(created_at) = CURDATE()"
    )->fetchColumn();
    $byStatus = $db->query(
        "SELECT status, COUNT(*) AS cnt FROM suria_calc_leads GROUP BY status"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    return [
        'total' => $total,
        'new_today' => $newToday,
        'by_status' => $byStatus ?: [],
    ];
}
