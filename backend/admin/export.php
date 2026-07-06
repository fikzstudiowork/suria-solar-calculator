<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

requireAdmin();

$statusFilter = $_GET['status'] ?? '';
$stateFilter = $_GET['state'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$search = trim($_GET['search'] ?? '');

$where = ['1=1'];
$params = [];

if ($statusFilter) { $where[] = 'status = ?'; $params[] = $statusFilter; }
if ($stateFilter) { $where[] = 'state = ?'; $params[] = $stateFilter; }
if ($dateFrom) { $where[] = 'created_at >= ?'; $params[] = $dateFrom . ' 00:00:00'; }
if ($dateTo) { $where[] = 'created_at <= ?'; $params[] = $dateTo . ' 23:59:59'; }
if ($search) {
    $where[] = '(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}

$sql = 'SELECT * FROM suria_calc_leads WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC';
$stmt = getDb()->prepare($sql);
$stmt->execute($params);
$leads = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="suria-leads-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, [
    'ID', 'Created', 'Name', 'Email', 'Phone', 'State', 'Property Type',
    'Monthly Bill', 'Roof Exposure', 'kWp', 'Monthly Savings', 'Annual Savings',
    'Payback Years', 'Status', 'UTM Source', 'UTM Campaign',
]);

foreach ($leads as $lead) {
    fputcsv($out, [
        $lead['id'],
        $lead['created_at'],
        $lead['full_name'],
        $lead['email'],
        $lead['phone'],
        $lead['state'],
        $lead['property_type'],
        $lead['monthly_bill'],
        $lead['roof_exposure'],
        $lead['recommended_kwp'],
        $lead['est_monthly_savings'],
        $lead['est_annual_savings'],
        $lead['payback_years'],
        $lead['status'],
        $lead['utm_source'],
        $lead['utm_campaign'],
    ]);
}

fclose($out);
exit;
