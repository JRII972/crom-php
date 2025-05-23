<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/App/Database/Database.php';
require_once __DIR__ . '/src/App/Database/Types/Utilisateur.php';
use App\Database\Database;
use App\Database\Types\Utilisateur;

header('Content-Type: application/json');
try {
    $pdo = Database::getConnection();
    $stmt = $pdo->query('SELECT * FROM utilisateurs');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = array_map(function($user) {
        return [
            'id' => $user['id'],
            'prenom' => $user['prenom'],
            'nom' => $user['nom'],
            'nomUtilisateur' => $user['login'],
            'email' => $user['email'],
            'sexe' => $user['sexe'],
            'idDiscord' => $user['id_discord'],
            'pseudonyme' => $user['pseudonyme'],
            'typeUtilisateur' => $user['type_utilisateur'],
            'dateDeNaissance' => $user['date_de_naissance']
        ];
    }, $users);
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}