<?php
$profiling_dir = '/var/www/html/profiling';
$files = glob($profiling_dir . '/cachegrind.out.*');

if (empty($files)) {
    echo "<h1>Aucun fichier de profiling trouvé</h1>";
    echo "<p>Lancez d'abord un script avec le profiling activé.</p>";
    exit;
}

// Trier par date de modification (plus récent en premier)
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$selected_file = $_GET['file'] ?? $files[0];

echo "<h1>Visualiseur de Profiling Xdebug</h1>";

// Liste des fichiers
echo "<h2>Fichiers de profiling disponibles :</h2>";
echo "<ul>";
foreach ($files as $file) {
    $basename = basename($file);
    $size = number_format(filesize($file) / 1024, 2);
    $time = date('Y-m-d H:i:s', filemtime($file));
    $selected = ($file === $selected_file) ? ' (sélectionné)' : '';
    echo "<li><a href='?file=" . urlencode($file) . "'>$basename</a> - {$size} KB - $time$selected</li>";
}
echo "</ul>";

if (!file_exists($selected_file)) {
    echo "<p style='color: red;'>Fichier non trouvé : $selected_file</p>";
    exit;
}

// Analyser le fichier de profiling
echo "<h2>Analyse du fichier : " . basename($selected_file) . "</h2>";

$content = file_get_contents($selected_file);
if ($content === false) {
    echo "<p style='color: red;'>Impossible de lire le fichier</p>";
    echo "<p>Vérifiez les permissions : <code>chmod 644 $selected_file</code></p>";
    exit;
}

// Parser basique du format cachegrind
$lines = explode("\n", $content);
$functions = [];
$current_function = null;

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    
    if (strpos($line, 'fl=') === 0) {
        $file = substr($line, 3);
    } elseif (strpos($line, 'fn=') === 0) {
        $current_function = substr($line, 3);
        if (!isset($functions[$current_function])) {
            $functions[$current_function] = ['calls' => 0, 'time' => 0, 'file' => $file ?? 'unknown'];
        }
    } elseif (is_numeric(substr($line, 0, 1)) && $current_function) {
        $parts = explode(' ', $line);
        if (count($parts) >= 2) {
            $functions[$current_function]['calls']++;
            $functions[$current_function]['time'] += intval($parts[1]);
        }
    }
}

// Trier par temps d'exécution
uasort($functions, function($a, $b) {
    return $b['time'] - $a['time'];
});

echo "<h3>Top 20 des fonctions les plus coûteuses :</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Fonction</th><th>Temps (µs)</th><th>Appels</th><th>Temps moyen</th><th>Fichier</th></tr>";

$count = 0;
foreach ($functions as $name => $data) {
    if ($count++ >= 20) break;
    
    $avg_time = $data['calls'] > 0 ? round($data['time'] / $data['calls'], 2) : 0;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($name) . "</td>";
    echo "<td>" . number_format($data['time']) . "</td>";
    echo "<td>" . number_format($data['calls']) . "</td>";
    echo "<td>" . number_format($avg_time, 2) . "</td>";
    echo "<td>" . htmlspecialchars(basename($data['file'])) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Informations sur le fichier :</h3>";
echo "<ul>";
echo "<li>Taille : " . number_format(strlen($content)) . " octets</li>";
echo "<li>Lignes : " . count($lines) . "</li>";
echo "<li>Fonctions analysées : " . count($functions) . "</li>";
echo "</ul>";

echo "<h3>Contenu brut (premiers 2000 caractères) :</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;'>";
echo htmlspecialchars(substr($content, 0, 2000));
if (strlen($content) > 2000) {
    echo "\n... (tronqué)";
}
echo "</pre>";
?>
