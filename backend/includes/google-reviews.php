<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/db.php';

function fetchGoogleReviews(string $placeId, string $apiKey): array
{
    $url = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
        'place_id' => $placeId,
        'fields' => 'reviews,rating,user_ratings_total',
        'key' => $apiKey,
        'language' => 'en',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return [];
    }

    $data = json_decode($response, true);
    if (($data['status'] ?? '') !== 'OK' || empty($data['result']['reviews'])) {
        return [];
    }

    $reviews = [];
    foreach ($data['result']['reviews'] as $r) {
        $reviews[] = [
            'author' => $r['author_name'] ?? 'Google User',
            'rating' => (int) ($r['rating'] ?? 5),
            'text' => $r['text'] ?? '',
            'date' => $r['relative_time_description'] ?? '',
            'source' => 'google',
        ];
    }

    return $reviews;
}

function getManualReviews(): array
{
    $raw = getConfigValue('manual_reviews_json', '[]');
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return getDefaultManualReviews();
    }
    return $decoded;
}

function getDefaultManualReviews(): array
{
    return [
        [
            'author' => 'Suria Infiniti Customer',
            'rating' => 5,
            'text' => 'Professional team, clear savings estimate, and smooth installation process.',
            'date' => 'Recent',
            'source' => 'manual',
        ],
        [
            'author' => 'Homeowner, Selangor',
            'rating' => 5,
            'text' => 'My TNB bill dropped significantly after going solar with Suria Infiniti.',
            'date' => 'Recent',
            'source' => 'manual',
        ],
    ];
}

function getCachedGoogleReviews(): ?array
{
    $cached = getConfigValue('google_reviews_cache', '');
    if (!$cached) {
        return null;
    }
    $data = json_decode($cached, true);
    if (!is_array($data) || empty($data['reviews']) || empty($data['cached_at'])) {
        return null;
    }
    // Cache 24 hours
    if (time() - (int) $data['cached_at'] > 86400) {
        return null;
    }
    return $data['reviews'];
}

function cacheGoogleReviews(array $reviews): void
{
    require_once __DIR__ . '/settings.php';
    setSiteSetting('google_reviews_cache', json_encode([
        'cached_at' => time(),
        'reviews' => $reviews,
    ]));
}

function getAllReviews(): array
{
    $enabled = getConfigValue('google_reviews_enabled', '0') === '1';
    $placeId = getConfigValue('google_place_id', '');
    $config = loadConfig();
    $apiKey = $config['google']['places_api_key'] ?? '';

    $reviews = [];

    if ($enabled && $placeId && $apiKey) {
        $cached = getCachedGoogleReviews();
        if ($cached !== null) {
            $reviews = $cached;
        } else {
            $reviews = fetchGoogleReviews($placeId, $apiKey);
            if (!empty($reviews)) {
                cacheGoogleReviews($reviews);
            }
        }
    }

    if (empty($reviews)) {
        $reviews = getManualReviews();
    }

    return $reviews;
}

/** Force refresh from Google Places API (admin sync). Returns review count or error message. */
function syncGoogleReviewsNow(): array
{
    $placeId = getConfigValue('google_place_id', '');
    $config = loadConfig();
    $apiKey = $config['google']['places_api_key'] ?? '';

    if (!$placeId) {
        return ['ok' => false, 'message' => 'Google Place ID is required.'];
    }
    if (!$apiKey) {
        return ['ok' => false, 'message' => 'Add google.places_api_key in config.php first.'];
    }

    $reviews = fetchGoogleReviews($placeId, $apiKey);
    if (empty($reviews)) {
        return ['ok' => false, 'message' => 'No reviews returned. Check Place ID and API key.'];
    }

    cacheGoogleReviews($reviews);
    return ['ok' => true, 'message' => 'Synced ' . count($reviews) . ' reviews from Google.', 'count' => count($reviews)];
}
