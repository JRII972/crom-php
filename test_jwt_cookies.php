<?php
// Test des fonctions JWT et cookies
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/App/Utils/helpers.php';

// Test de génération de token
echo "=== Test des fonctions JWT ===\n";

$userId = 'test-user-id';
$typeUtilisateur = 'ADMINISTRATEUR';

// Générer un token
$token = generateJwtToken($userId, $typeUtilisateur);
echo "Token généré : " . $token . "\n";

// Valider le token
$payload = validateJwtToken($token);
if ($payload) {
    echo "Token valide !\n";
    echo "User ID : " . $payload['sub'] . "\n";
    echo "Type utilisateur : " . $payload['type_utilisateur'] . "\n";
    echo "Émis à : " . date('Y-m-d H:i:s', $payload['iat']) . "\n";
    echo "Expire à : " . date('Y-m-d H:i:s', $payload['exp']) . "\n";
} else {
    echo "Token invalide !\n";
}

// Test de token expiré (avec une durée négative)
$expiredToken = generateJwtToken($userId, $typeUtilisateur, -3600);
echo "\nTest token expiré :\n";
$expiredPayload = validateJwtToken($expiredToken);
if ($expiredPayload) {
    echo "Token expiré considéré comme valide (ERREUR)\n";
} else {
    echo "Token expiré correctement rejeté\n";
}

echo "\n=== Test des fonctions Cookie ===\n";

// Simuler un environnement web pour les tests de cookies
$_SERVER['HTTPS'] = 'on'; // Simuler HTTPS

// Test de définition de cookie
if (setJwtCookie($token)) {
    echo "Cookie JWT défini avec succès\n";
} else {
    echo "Erreur lors de la définition du cookie\n";
}

// Note : Dans un environnement CLI, les cookies ne sont pas réellement définis
// mais la fonction ne devrait pas générer d'erreur

echo "\n=== Test d'authentification ===\n";

// Simuler un cookie dans $_COOKIE pour le test
$_COOKIE['auth_token'] = $token;

$authUser = getAuthenticatedUser();
if ($authUser) {
    echo "Utilisateur authentifié détecté :\n";
    echo "ID : " . $authUser['sub'] . "\n";
    echo "Type : " . $authUser['type_utilisateur'] . "\n";
} else {
    echo "Aucun utilisateur authentifié\n";
}

echo "\nTest terminé !\n";
?>
