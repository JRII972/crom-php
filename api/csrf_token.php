<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/App/Database/config.php';

echo json_encode(['csrf_token' => generateCsrfToken()]);
