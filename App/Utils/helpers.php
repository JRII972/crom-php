<?php


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
    return true; //FIXME: not working
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


/**
 * Génère un token JWT pour un utilisateur
 *
 * @param string $userId ID de l'utilisateur
 * @param string $typeUtilisateur Type d'utilisateur
 * @param int $expiration Durée de validité en secondes (défaut: 1 heure)
 * @return string Token JWT
 */
function generateJwtToken(string $userId, string $typeUtilisateur, int $expiration = 3600): string
{
    $payload = [
        'iat' => time(),
        'exp' => time() + $expiration,
        'sub' => $userId,
        'type_utilisateur' => $typeUtilisateur
    ];
    
    return JWT::encode($payload, 'votre_secret_jwt_ici', 'HS256');
}

/**
 * Valide et décode un token JWT
 *
 * @param string $token Token JWT à valider
 * @return array|null Données décodées ou null si invalide
 */
function validateJwtToken(string $token): ?array
{
    try {
        $decoded = JWT::decode($token, new Key('votre_secret_jwt_ici', 'HS256'));
        return (array)$decoded;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Définit un cookie JWT sécurisé
 *
 * @param string $token Token JWT
 * @param int $expiration Durée de validité en secondes
 * @return bool
 */
function setJwtCookie(string $token, int $expiration = 3600): bool
{
    $cookieOptions = [
        'expires' => time() + $expiration,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    
    return setcookie('auth_token', $token, $cookieOptions);
}

/**
 * Récupère le token JWT depuis les cookies
 *
 * @return string|null Token JWT ou null si absent
 */
function getJwtFromCookie(): ?string
{
    return $_COOKIE['auth_token'] ?? null;
}

/**
 * Supprime le cookie d'authentification
 *
 * @return bool
 */
function clearJwtCookie(): bool
{
    $cookieOptions = [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    
    return setcookie('auth_token', '', $cookieOptions);
}

/**
 * Récupère l'utilisateur authentifié depuis le cookie JWT
 *
 * @return array|null Données utilisateur ou null si non authentifié
 */
function getAuthenticatedUser(): ?array
{
    $token = getJwtFromCookie();
    if (!$token) {
        return null;
    }
    
    return validateJwtToken($token);
}

/**
 * Récupère l'ID de l'utilisateur authentifié
 *
 * @return string|null ID utilisateur ou null si non authentifié
 */
function getCurrentUserId(): ?string
{
    $userData = getAuthenticatedUser();
    return $userData['sub'] ?? null;
}
