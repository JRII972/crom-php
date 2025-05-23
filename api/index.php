<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: http://localhost:3000');
// header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
// header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');
// header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    // Vérification du token JWT ou refresh token
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $user = null;
    if (preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
        $jwt = $matches[1];
        try {
            $decoded = JWT::decode($jwt, new Key('votre_secret_jwt_ici', 'HS256'));
            $user = (array)$decoded;
        } catch (Exception $e) {
            throw new Exception('Token JWT invalide: ' . $e->getMessage(), 401);
        }
    }

    // Vérification CSRF pour les méthodes non-GET
    $method = $_SERVER['REQUEST_METHOD'];
    if (!in_array($method, ['GET', 'OPTIONS'])) {
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
            throw new Exception('Jeton CSRF invalide', 403);
        }
    }

    $uri = $_SERVER['REQUEST_URI'];
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $path = preg_replace("#^$scriptName#", '', $uri);
    $path = parse_url($path, PHP_URL_PATH);
    $segments = array_values(array_filter(explode('/', $path)));

    if (empty($segments)) {
        throw new Exception("Point de terminaison API invalide", 404);
    }

    $resource = ucfirst($segments[0] ?? '');
    $id = $segments[1] ?? null;

    $class = "App\\Api\\{$resource}Api";
    if (!class_exists($class)) {
        throw new Exception("Ressource '$resource' non trouvée", 404);
    }

    $handler = new $class();
    $response = $handler->handle($id);

    echo json_encode($response);
} catch (Throwable $e) {
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    echo json_encode([
        'status' => 'error',
        'data' => null,
        'message' => $e->getMessage()
    ]);
}