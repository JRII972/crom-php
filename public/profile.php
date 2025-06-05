<?php
// Fichier d'entrée pour la page de profil
// filepath: /var/www/html/public/profile.php

require_once __DIR__ . '/../App/controllers/ProfileController.php';

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID utilisateur depuis l'URL si disponible
$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Récupérer l'onglet actif depuis l'URL si disponible
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'activites';

// Valider l'onglet actif
$validTabs = ['activites', 'disponibilites', 'historique', 'preference', 'paiements'];
if (!in_array($activeTab, $validTabs)) {
    $activeTab = 'activites';
}

// Instancier le contrôleur et afficher la page
$controller = new ProfileController();
echo $controller->show($userId, $activeTab);
