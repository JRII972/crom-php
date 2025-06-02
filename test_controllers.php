<?php
// Test script for our new controller architecture

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/App/controllers/HomeController.php';
require_once __DIR__ . '/App/controllers/ContactController.php';

try {
    // Test Home Controller
    echo "Testing HomeController:\n";
    $homeController = new HomeController();
    $homeOutput = $homeController->index();
    echo "Home Controller output length: " . strlen($homeOutput) . " characters\n";
    echo "Home Controller output preview: " . substr($homeOutput, 0, 100) . "...\n\n";
    
    // Test Contact Controller
    echo "Testing ContactController:\n";
    $contactController = new ContactController();
    $contactOutput = $contactController->index();
    echo "Contact Controller output length: " . strlen($contactOutput) . " characters\n";
    echo "Contact Controller output preview: " . substr($contactOutput, 0, 100) . "...\n\n";
    
    echo "All controllers initialized and rendered successfully!\n";
} catch (Exception $e) {
    echo "Error testing controllers: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
