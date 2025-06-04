<?php
// Test simple pour vérifier Xdebug
echo "Début du script de test Xdebug\n";

$variable1 = "Test de débogage";
$variable2 = 42;
$array_test = [
    'nom' => 'Test',
    'valeur' => 123,
    'actif' => true
];

echo "Variable 1: " . $variable1 . "\n";
echo "Variable 2: " . $variable2 . "\n";
echo "Array test: " . print_r($array_test, true) . "\n";

function test_function($param) {
    $result = $param * 2;
    return $result;
}

$resultat = test_function(10);
echo "Résultat de la fonction: " . $resultat . "\n";

echo "Fin du script de test\n";
?>
