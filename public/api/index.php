<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json');
// header('Access-Control-Allow-Origin: http://localhost:3000');
// header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
// header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');
// header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../App/Utils/helpers.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    // Vérification du token JWT depuis les headers ou les cookies
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $user = null;

    // Vérifier d'abord les headers Authorization (priorité aux API calls)
    if (preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
        $jwt = $matches[1];
        $user = validateJwtToken($jwt);
        if (!$user) {
            throw new Exception('Token JWT invalide', 401);
        }
    } 
    // Sinon, vérifier les cookies pour les requêtes web
    else {
        $cookieToken = getJwtFromCookie();
        if ($cookieToken) {
            $user = validateJwtToken($cookieToken);
            if (!$user) {
                // Cookie invalide, le supprimer
                clearJwtCookie();
            }
        }
    }

    // Vérification CSRF pour les méthodes non-GET
    $method = $_SERVER['REQUEST_METHOD'];
    if (!in_array($method, ['GET', 'OPTIONS'])) {
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!$csrfToken) {
            throw new Exception('Jeton CSRF absent', 403);
        }
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

    $resource = ucfirst($segments[1] ?? '');
    $id = $segments[2] ?? null;

    if ($segments[1] == 'csrf_token') {
        echo json_encode(['csrf_token' => generateCsrfToken()]);
        return ;
    }

    $class = "App\\Api\\{$resource}Api";
    if (!class_exists($class)) {
        throw new Exception("Ressource '$resource' non trouvée", 404);
    }

    $handler = new $class();
    $handler->setUser($user);
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