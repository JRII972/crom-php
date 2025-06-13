<?php

use App\Controllers\HomeController;
// Charger l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';


$controller = new HomeController();
echo $controller->index();