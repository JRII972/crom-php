<?php

use App\Controllers\ActiviteController;
// Fichier d'entrée pour la page de profil
// filepath: /var/www/html/public/profile.php

require_once __DIR__ . '/../../App/controllers/ActiviteController.php';

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Instancier le contrôleur et afficher la page
$controller = new ActiviteController();
echo $controller->create();
