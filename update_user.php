<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/App/Database/config.php';
require_once __DIR__ . '/src/App/Database/Database.php';
require_once __DIR__ . '/src/App/Database/Types/Utilisateur.php';
require_once __DIR__ . '/src/App/Utils/Image.php';

use App\Database\Types\Utilisateur;
use App\Database\Types\Sexe;
use App\Database\Types\TypeUtilisateur;
use App\Utils\Image;

header('Content-Type: application/json');

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Jeton CSRF invalide']);
    exit;
}

try {
    $user = new Utilisateur($_POST['id']);
    $data = [
        'prenom' => $_POST['prenom'],
        'nom' => $_POST['nom'],
        'nomUtilisateur' => $_POST['login'],
        'sexe' => $_POST['sexe'],
        'email' => $_POST['email'] ?: null,
        'dateDeNaissance' => !empty($_POST['dateDeNaissance']) ? $_POST['dateDeNaissance'] : null,
        'idDiscord' => $_POST['idDiscord'] ?: null,
        'pseudonyme' => $_POST['pseudonyme'] ?: null,
        'typeUtilisateur' => $_POST['typeUtilisateur']
    ];
    if (!empty($_POST['motDePasse'])) {
        $data['motDePasse'] = $_POST['motDePasse'];
    }
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $data['image'] = $_FILES['image'];
    }
    $user->update($data);
    $user->save();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}