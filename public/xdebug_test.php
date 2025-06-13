<?php
// Test Xdebug
function testXdebug() {
    // Point d'arrêt pour le débogage
    $testVar = "Xdebug fonctionne !";
    
    // Données pour le profilage
    for ($i = 0; $i < 1000; $i++) {
        $array[] = $i;
    }
    
    // Afficher les informations sur Xdebug
    phpinfo();
}

testXdebug();
