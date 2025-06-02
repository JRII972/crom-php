<?php
// Charger l'autoloader de Composer
require_once __DIR__ . '/../app/controllers/HomeController.php';

$controller = new HomeController();
echo $controller->index();