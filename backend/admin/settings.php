<?php

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/settings.php';
require_once dirname(__DIR__) . '/includes/google-reviews.php';
require_once __DIR__ . '/includes/layout.php';

requireAdmin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sync_google_reviews'])) {
        $result = syncGoogleReviewsNow();
        if ($result['ok']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } else {
        foreach (SITE_SETTING_KEYS as $key) {
            if ($key === 'google_reviews_enabled') {
                continue;
            }
            if ($key === 'google_reviews_cache') {
                continue;
            }
            if (isset($_POST[$key])) {
                setSiteSetting($key, trim($_POST[$key]));
            }
        }
        setSiteSetting('google_reviews_enabled', isset($_POST['google_reviews_enabled']) ? '1' : '0');

        if (!empty($_FILES['logo_file']['tmp_name']) && is_uploaded_file($_FILES['logo_file']['tmp_name'])) {
            $uploadDir = dirname(__DIR__) . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'], true)) {
                $dest = $uploadDir . 'logo.' . $ext;
                if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $dest)) {
                    setSiteSetting('logo_url', '/uploads/logo.' . $ext . '?v=' . time());
                } else {
                    $error = 'Logo upload failed — check that the uploads/ folder is writable (chmod 755).';
                }
            } else {
                $error = 'Logo must be PNG, JPG, WEBP, or SVG.';
            }
        }

        if ($error === '') {
            $message = 'Settings saved successfully.';
        }

$settings = getSiteSettings();
$manualReviewsPretty = $settings['manual_reviews_json'];
if ($manualReviewsPretty === '') {
    $manualReviewsPretty = json_encode(getDefaultManualReviews(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
adminHeader('Site Settings', 'settings');
?>

<?php if ($message): ?>
  <div class="success"><?= e($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<div class="card">
  <h2>Company Profile & Branding</h2>
  <p style="color:#9AA3AC;font-size:14px;margin-bottom:20px">
    Superadmin — update contact info, WhatsApp, logo, and stats shown on the calculator.
  </p>

  <form method="post" enctype="multipart/form-data">
    <div class="grid-2">
      <div>
        <label for="company_name">Company Name</label>
        <input type="text" id="company_name" name="company_name" value="<?= e($settings['company_name']) ?>">
      </div>
      <div>
        <label for="company_tagline">Tagline</label>
        <input type="text" id="company_tagline" name="company_tagline" value="<?= e($settings['company_tagline']) ?>">
      </div>
    </div>

    <label for="logo_file">Logo Upload (PNG/JPG/SVG)</label>
    <?php if (!empty($settings['logo_url'])): ?>
      <img src="<?= e($settings['logo_url']) ?>" alt="Logo" class="logo-preview" onerror="this.style.display='none'">
    <?php endif; ?>
    <input type="file" id="logo_file" name="logo_file" accept="image/*">

    <label for="logo_url">Logo URL (or path after upload)</label>
    <input type="text" id="logo_url" name="logo_url" value="<?= e($settings['logo_url']) ?>" placeholder="/uploads/logo.png">

    <h2 style="margin-top:24px">Contact & WhatsApp</h2>
    <div class="grid-2">
      <div>
        <label for="contact_email">Public Contact Email</label>
        <input type="email" id="contact_email" name="contact_email" value="<?= e($settings['contact_email']) ?>">
      </div>
      <div>
        <label for="contact_phone">Public Phone (display)</label>
        <input type="text" id="contact_phone" name="contact_phone" value="<?= e($settings['contact_phone']) ?>">
      </div>
      <div>
        <label for="whatsapp_number">WhatsApp Number (digits only, e.g. 60361505399)</label>
        <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?= e($settings['whatsapp_number']) ?>">
      </div>
      <div>
        <label for="whatsapp_prefill_text">WhatsApp Prefill Message (header & floating button)</label>
        <input type="text" id="whatsapp_prefill_text" name="whatsapp_prefill_text" value="<?= e($settings['whatsapp_prefill_text'] ?? 'solarenergy') ?>">
      </div>
      <div>
        <label for="sales_email">Lead Notification Email</label>
        <input type="email" id="sales_email" name="sales_email" value="<?= e($settings['sales_email']) ?>">
      </div>
    </div>

    <h2 style="margin-top:24px">Trust Stats (shown on calculator)</h2>
    <div class="grid-2">
      <div>
        <label for="installations_count">Installations</label>
        <input type="text" id="installations_count" name="installations_count" value="<?= e($settings['installations_count']) ?>">
      </div>
      <div>
        <label for="avg_savings_percent">Avg. Savings</label>
        <input type="text" id="avg_savings_percent" name="avg_savings_percent" value="<?= e($settings['avg_savings_percent']) ?>">
      </div>
      <div>
        <label for="customer_rating">Customer Rating</label>
        <input type="text" id="customer_rating" name="customer_rating" value="<?= e($settings['customer_rating']) ?>">
      </div>
      <div>
        <label for="privacy_policy_url">Privacy Policy URL</label>
        <input type="url" id="privacy_policy_url" name="privacy_policy_url" value="<?= e($settings['privacy_policy_url']) ?>">
      </div>
    </div>

    <h2 style="margin-top:24px">Google Reviews Carousel</h2>
    <p style="color:#9AA3AC;font-size:14px;margin-bottom:16px">
      Live reviews from Google Business Profile. API key goes in <code>config.php</code> (server-side only).
      Google returns up to 5 recent reviews; cached 24 hours.
    </p>

    <label>
      <input type="checkbox" name="google_reviews_enabled" value="1" <?= $settings['google_reviews_enabled'] === '1' ? 'checked' : '' ?>>
      Enable live Google reviews
    </label>

    <label for="google_place_id">Google Place ID</label>
    <input type="text" id="google_place_id" name="google_place_id" value="<?= e($settings['google_place_id']) ?>" placeholder="ChIJ...">

    <label for="manual_reviews_json">Fallback reviews (JSON array — used when Google is off or unavailable)</label>
    <textarea id="manual_reviews_json" name="manual_reviews_json" rows="8" style="font-family:monospace;font-size:13px"><?= e($manualReviewsPretty) ?></textarea>

    <div style="display:flex;gap:12px;margin-top:16px;flex-wrap:wrap">
      <button type="submit" class="btn">Save Settings</button>
      <button type="submit" name="sync_google_reviews" value="1" class="btn btn-secondary">Sync from Google now</button>
    </div>
  </form>
</div>

<div class="card" style="margin-top:24px">
  <h2>Setup: Google Places API</h2>
  <ol style="color:#9AA3AC;font-size:14px;line-height:1.7">
    <li>Go to <a href="https://console.cloud.google.com/" target="_blank" rel="noopener">Google Cloud Console</a> → enable <strong>Places API</strong>.</li>
    <li>Create an API key and restrict it to Places API + your server IP.</li>
    <li>Add to <code>backend/config.php</code>: <code>'google' => ['places_api_key' => 'YOUR_KEY']</code></li>
    <li>Find your Place ID via <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank" rel="noopener">Place ID Finder</a>.</li>
    <li>Enable the toggle above, save, then click <strong>Sync from Google now</strong>.</li>
  </ol>
</div>

<?php adminFooter(); ?>
