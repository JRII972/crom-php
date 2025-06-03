<?php
// Point d'entrée pour la page de connexion
// filepath: /var/www/html/public/login.php

session_start();

// Autoloader et configuration
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../App/controllers/AuthController.php';

try {
    $controller = new AuthController();
    
    echo $controller->index();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
