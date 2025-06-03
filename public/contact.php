<?php
// Charger l'autoloader de Composer
require_once __DIR__ . '/../app/controllers/ContactController.php';

$controller = new ContactController();
echo $controller->index();