<?php

require_once dirname(__DIR__) . '/includes/cors.php';
require_once dirname(__DIR__) . '/includes/csrf.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

applyCors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    jsonError('Invalid JSON payload');
}

// Honeypot check
if (!empty($data['website'])) {
    jsonError('Submission rejected', 403);
}

// CSRF
if (empty($data['csrfToken']) || !verifyCsrfToken($data['csrfToken'])) {
    jsonError('Invalid or expired security token', 403);
}

// Turnstile
if (empty($data['turnstileToken']) || !verifyTurnstile($data['turnstileToken'])) {
    jsonError('Security verification failed', 403);
}

// Rate limit
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$ipHash = hashIp($ip);
if (!checkRateLimit($ipHash)) {
    jsonError('Too many submissions. Please try again later.', 429);
}

// Validate fields
$fullName = sanitizeString($data['fullName'] ?? '', 120);
$email = sanitizeString($data['email'] ?? '', 191);
$phone = sanitizeString($data['phone'] ?? '', 30);
$state = sanitizeString($data['state'] ?? '', 60);
$propertyType = sanitizeString($data['propertyType'] ?? '', 60);
$roofExposure = sanitizeString($data['roofExposure'] ?? '', 30);

if (!$fullName || !$email || !$phone || !$state) {
    jsonError('Missing required fields');
}

if (!validateEmail($email)) {
    jsonError('Invalid email address');
}

if (!validatePhone($phone)) {
    jsonError('Invalid phone number');
}

$monthlyBill = (float) ($data['monthlyBill'] ?? 0);
if ($monthlyBill < 100 || $monthlyBill > 5000) {
    jsonError('Monthly bill must be between RM 100 and RM 5,000');
}

$allowedExposure = ['Excellent', 'Good', 'Moderate'];
if (!in_array($roofExposure, $allowedExposure, true)) {
    jsonError('Invalid roof exposure value');
}

if (empty($data['consent'])) {
    jsonError('Consent is required');
}

// Server-side recalculation
$config = getAllCalcConfig();
$computed = calculateServerSide($monthlyBill, $roofExposure, $config);

$clientKwp = (float) ($data['recommendedKwp'] ?? 0);
$clientMonthly = (float) ($data['estMonthlySavings'] ?? 0);
$clientAnnual = (float) ($data['estAnnualSavings'] ?? 0);
$clientPayback = (float) ($data['paybackYears'] ?? 0);

if (
    !valuesMatch($clientKwp, $computed['recommendedKwp']) ||
    !valuesMatch($clientMonthly, $computed['estMonthlySavings'], 1.0) ||
    !valuesMatch($clientAnnual, $computed['estAnnualSavings'], 2.0) ||
    !valuesMatch($clientPayback, $computed['paybackYears'], 0.5)
) {
    jsonError('Estimate values mismatch. Please recalculate.', 400);
}

$now = date('Y-m-d H:i:s');
$privacyVersion = loadConfig()['app']['privacy_policy_version'];

try {
    $db = getDb();
    $stmt = $db->prepare(
        'INSERT INTO suria_calc_leads (
            created_at, full_name, email, phone, state, property_type,
            monthly_bill, roof_exposure, recommended_kwp, est_monthly_savings,
            est_annual_savings, payback_years, consent_at, privacy_policy_version,
            utm_source, utm_campaign, status, ip_hash
        ) VALUES (
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?
        )'
    );

    $stmt->execute([
        $now,
        $fullName,
        $email,
        $phone,
        $state,
        $propertyType,
        $monthlyBill,
        $roofExposure,
        $computed['recommendedKwp'],
        $computed['estMonthlySavings'],
        $computed['estAnnualSavings'],
        $computed['paybackYears'],
        $now,
        $privacyVersion,
        sanitizeString($data['utmSource'] ?? '', 100) ?: null,
        sanitizeString($data['utmCampaign'] ?? '', 100) ?: null,
        'new',
        $ipHash,
    ]);

    $lead = [
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'state' => $state,
        'property_type' => $propertyType,
        'monthly_bill' => $monthlyBill,
        'roof_exposure' => $roofExposure,
        'recommended_kwp' => $computed['recommendedKwp'],
        'est_monthly_savings' => $computed['estMonthlySavings'],
        'est_annual_savings' => $computed['estAnnualSavings'],
        'payback_years' => $computed['paybackYears'],
        'created_at' => $now,
    ];

    $emailSent = sendLeadEmail($lead);
    if (!$emailSent) {
        error_log('Suria Calc: email failed for lead ' . $email);
    }

    jsonResponse([
        'success' => true,
        'message' => 'Thank you! We will contact you shortly.',
    ]);
} catch (Exception $e) {
    error_log('Suria Calc lead error: ' . $e->getMessage());
    jsonError('Unable to save submission. Please try again.', 500);
}
