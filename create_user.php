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
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = new Image($_FILES['image']); // Assuming Image class handles file upload
    }

    $dateDeNaissance = !empty($_POST['dateDeNaissance']) ? new DateTime($_POST['dateDeNaissance']) : null;
    $typeUtilisateur = !empty($_POST['typeUtilisateur']) ? TypeUtilisateur::from($_POST['typeUtilisateur']) : TypeUtilisateur::Inscrit;
    $user = new Utilisateur(
        prenom: $_POST['prenom'],
        nom: $_POST['nom'],
        nomUtilisateur: $_POST['login'],
        motDePasse: $_POST['motDePasse'],
        sexe: Sexe::from($_POST['sexe']),
        email: $_POST['email'] ?? null,
        dateDeNaissance: $dateDeNaissance,
        idDiscord: $_POST['idDiscord'] ?? null,
        pseudonyme: $_POST['pseudonyme'] ?? null,
        image: $image,
        typeUtilisateur: $typeUtilisateur
    );
    $user->save();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}