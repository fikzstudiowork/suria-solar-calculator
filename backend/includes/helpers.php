<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/db.php';

function calculateServerSide(
    float $monthlyBill,
    string $roofExposure,
    array $config
): array {
    $exposureFactors = $config['exposureFactors'];
    $exposureFactor = $exposureFactors[$roofExposure] ?? 1.15;

    $kwhMonth = $monthlyBill / $config['tariffRate'];
    $recommendedKwp =
        ($kwhMonth * $exposureFactor) /
        ($config['sunHours'] * 30 * $config['derate']);

    $annualGen =
        $recommendedKwp * $config['sunHours'] * 365 * $config['derate'];
    $generationValueMonthly =
        ($annualGen / 12) * $config['tariffRate'] * $config['offsetPercent'];

    $estMonthlySavings = min($monthlyBill, $generationValueMonthly);
    $estAnnualSavings = $estMonthlySavings * 12;
    $estSystemCost = $recommendedKwp * $config['costPerKwp'];
    $paybackYears = $estAnnualSavings > 0 ? $estSystemCost / $estAnnualSavings : 0;

    return [
        'recommendedKwp' => round($recommendedKwp, 2),
        'estMonthlySavings' => round($estMonthlySavings, 2),
        'estAnnualSavings' => round($estAnnualSavings, 2),
        'paybackYears' => round($paybackYears, 1),
    ];
}

function valuesMatch(float $a, float $b, float $tolerance = 0.05): bool
{
    return abs($a - $b) <= $tolerance;
}

function sanitizeString(string $value, int $maxLen = 255): string
{
    return mb_substr(trim(strip_tags($value)), 0, $maxLen);
}

function validateEmail(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone(string $phone): bool
{
    $clean = preg_replace('/[\s\-\+]/', '', $phone);
    return (bool) preg_match('/^[0-9]{9,15}$/', $clean ?? '');
}

function hashIp(string $ip): string
{
    return hash('sha256', $ip . loadConfig()['csrf']['secret']);
}

function verifyTurnstile(string $token): bool
{
    $secret = loadConfig()['turnstile']['secret_key'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $ip,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return false;
    }

    $data = json_decode($response, true);
    return isset($data['success']) && $data['success'] === true;
}

function checkRateLimit(string $ipHash): bool
{
    $config = loadConfig()['rate_limit'];
    $db = getDb();
    $now = date('Y-m-d H:i:s');

    $stmt = $db->prepare('SELECT request_count, window_start FROM rate_limits WHERE ip_hash = ?');
    $stmt->execute([$ipHash]);
    $row = $stmt->fetch();

    if (!$row) {
        $insert = $db->prepare('INSERT INTO rate_limits (ip_hash, request_count, window_start) VALUES (?, 1, ?)');
        $insert->execute([$ipHash, $now]);
        return true;
    }

    $windowStart = strtotime($row['window_start']);
    if (time() - $windowStart > $config['window_seconds']) {
        $reset = $db->prepare('UPDATE rate_limits SET request_count = 1, window_start = ? WHERE ip_hash = ?');
        $reset->execute([$now, $ipHash]);
        return true;
    }

    if ((int) $row['request_count'] >= $config['max_requests']) {
        return false;
    }

    $update = $db->prepare('UPDATE rate_limits SET request_count = request_count + 1 WHERE ip_hash = ?');
    $update->execute([$ipHash]);
    return true;
}

function sendLeadEmail(array $lead): bool
{
    require_once __DIR__ . '/settings.php';
    $settings = getSiteSettings();
    $mailConfig = loadConfig()['mail'];
    if (!$mailConfig['enabled']) {
        return true;
    }

    $to = $settings['sales_email'] ?: $mailConfig['to'];
    $subject = 'New Solar Calculator Lead — ' . $lead['full_name'];
    $body = "New lead from Suria Solar Calculator\n\n"
        . "Name: {$lead['full_name']}\n"
        . "Email: {$lead['email']}\n"
        . "Phone: {$lead['phone']}\n"
        . "State: {$lead['state']}\n"
        . "Property: {$lead['property_type']}\n"
        . "Monthly Bill: RM {$lead['monthly_bill']}\n"
        . "Roof Exposure: {$lead['roof_exposure']}\n"
        . "Recommended kWp: {$lead['recommended_kwp']}\n"
        . "Est. Monthly Savings: RM {$lead['est_monthly_savings']}\n"
        . "Est. Annual Savings: RM {$lead['est_annual_savings']}\n"
        . "Payback: {$lead['payback_years']} years\n"
        . "Submitted: {$lead['created_at']}\n";

    $headers = [
        'From: ' . $mailConfig['from_name'] . ' <' . $mailConfig['from'] . '>',
        'Reply-To: ' . $lead['email'],
        'Content-Type: text/plain; charset=UTF-8',
    ];

    return @mail($to, $subject, $body, implode("\r\n", $headers));
}
