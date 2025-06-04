<?php
// Fichier d'entrée pour la page de profil
// filepath: /var/www/html/public/profile.php

require_once __DIR__ . '/../App/controllers/PartieController.php';

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID utilisateur depuis l'URL si disponible
$partieId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Instancier le contrôleur et afficher la page
$controller = new PartieController();
echo $controller->index($partieId);
