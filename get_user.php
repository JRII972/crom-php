<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/App/Database/Database.php';
require_once __DIR__ . '/src/App/Database/Types/Utilisateur.php';
use App\Database\Types\Utilisateur;

header('Content-Type: application/json');
try {
    $id = $_GET['id'] ?? '';
    $user = new Utilisateur($id);
    echo json_encode($user->jsonSerialize());
} catch (Exception $e) {
    http_response_code(404);
    echo json_encode(['error' => 'Utilisateur non trouvé']);
}