<?php
echo "<h1>Test de Profiling Xdebug</h1>";

// Vérifier si le profiling est activé
if (extension_loaded('xdebug')) {
    echo "<p>Xdebug est chargé</p>";
    echo "<p>Mode Xdebug : " . ini_get('xdebug.mode') . "</p>";
    echo "<p>Répertoire de sortie : " . ini_get('xdebug.output_dir') . "</p>";
} else {
    echo "<p style='color: red;'>Xdebug n'est pas chargé !</p>";
}

// Fonction coûteuse pour générer du profiling
function expensive_function() {
    $result = 0;
    for ($i = 0; $i < 1000000; $i++) {
        $result += sqrt($i);
    }
    return $result;
}

// Appeler la fonction
$start = microtime(true);
$result = expensive_function();
$end = microtime(true);

echo "<p>Résultat : " . number_format($result, 2) . "</p>";
echo "<p>Temps d'exécution : " . number_format(($end - $start) * 1000, 2) . " ms</p>";

// Vérifier si des fichiers de profiling ont été générés
$profiling_dir = '/var/www/html/profiling';
if (is_dir($profiling_dir)) {
    $files = glob($profiling_dir . '/cachegrind.out.*');
    echo "<h2>Fichiers de profiling générés :</h2>";
    if (empty($files)) {
        echo "<p style='color: orange;'>Aucun fichier de profiling trouvé</p>";
        echo "<p>Essayez d'accéder à cette page avec : <code>?XDEBUG_PROFILE=1</code></p>";
    } else {
        echo "<ul>";
        foreach ($files as $file) {
            echo "<li>" . basename($file) . " (" . number_format(filesize($file)) . " octets)</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color: red;'>Le répertoire de profiling n'existe pas</p>";
}
?>
