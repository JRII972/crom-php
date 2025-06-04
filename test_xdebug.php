<?php
// Test Xdebug
echo "=== Test de configuration Xdebug ===\n";

// Vérifier si Xdebug est chargé
if (extension_loaded('xdebug')) {
    echo "✓ Extension Xdebug chargée\n";
    
    // Afficher la version
    echo "Version Xdebug: " . phpversion('xdebug') . "\n";
    
    // Vérifier le mode
    $mode = ini_get('xdebug.mode');
    echo "Mode Xdebug: " . $mode . "\n";
    
    // Vérifier le port
    $port = ini_get('xdebug.client_port');
    echo "Port client: " . $port . "\n";
    
    // Vérifier l'hôte
    $host = ini_get('xdebug.client_host');
    echo "Hôte client: " . $host . "\n";
    
    // Vérifier start_with_request
    $start = ini_get('xdebug.start_with_request');
    echo "Start with request: " . $start . "\n";
    
    // Point d'arrêt pour tester le debugger
    $test_var = "Test de débogage";
    $array_test = [1, 2, 3, 'debug', 'test'];
    
    echo "✓ Configuration Xdebug correcte pour le débogage\n";
    echo "Placez un point d'arrêt sur cette ligne et lancez le script pour tester le débogage.\n";
    
} else {
    echo "✗ Extension Xdebug non chargée\n";
}

echo "=== Fin du test ===\n";
?>
