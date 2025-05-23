<?php


// Generate UUID v4
function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Generate CSRF token
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Valide qu'une chaîne est un UUID valide.
 *
 * @param string $uuid
 * @return bool
 */
function isValidUuid(string $uuid): bool
{
    return preg_match(
        '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
        $uuid
    ) === 1;
}

function isValidDateTime(string $dateTime): bool
{
    $format = 'Y-m-d H:i:s';
    $dateObj = DateTime::createFromFormat($format, $dateTime);
    return $dateObj && $dateObj->format($format) === $dateTime;
}

function isValidDate(string $date): bool
{
    $format = 'Y-m-d';
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

function isValidTime(string $time): bool
{
    $format = 'H:i:s';
    $dateTime = DateTime::createFromFormat($format, $time);
    return $dateTime && $dateTime->format($format) === $time;
}

function isValidJson(string $json): bool
{
    json_decode($json);
    return json_last_error() === JSON_ERROR_NONE;
}
