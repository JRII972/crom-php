<?php
// Script de test pour vérifier l'installation de Blade

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/App/controllers/HomeController.php';

try {
    $controller = new HomeController();
    $output = $controller->index();
    echo "Blade fonctionne correctement!\n";
    echo "Sortie HTML:\n";
    echo $output;
} catch (Exception $e) {
    echo "Erreur lors du test de Blade: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
