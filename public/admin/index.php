<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AdminController;

try {
    // Créer une instance du contrôleur
    $controller = new AdminController();
    
    // Pour l'instant, nous affichons seulement la page principale d'administration
    echo $controller->index();
    
} catch (Exception $e) {
    error_log('Erreur dans admin/index.php: ' . $e->getMessage());
    http_response_code(500);
    
    // Afficher une page d'erreur simple
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Erreur - Administration</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Erreur</h1>
    <p>Une erreur est survenue lors du chargement de la page d\'administration.</p>
    <p>Veuillez réessayer plus tard ou contacter l\'administrateur.</p>
</body>
</html>';
}
