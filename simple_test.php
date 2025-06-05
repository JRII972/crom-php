<?php
echo "<h1>Test Simple de Profiling</h1>";
echo "<p>Ce script génère un profil Xdebug simple et lisible.</p>";

// Fonction simple à profiler
function test_function() {
    $sum = 0;
    for ($i = 0; $i < 1000; $i++) {
        $sum += $i * 2;
    }
    return $sum;
}

// Autre fonction
function another_function() {
    return array_sum(range(1, 100));
}

$result1 = test_function();
$result2 = another_function();

echo "<p>Résultat 1 : $result1</p>";
echo "<p>Résultat 2 : $result2</p>";

echo "<p><a href='profile_viewer.php'>Voir les profils générés</a></p>";
?>
