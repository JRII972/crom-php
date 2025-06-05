<?php
$profiling_dir = '/var/www/html/profiling';

// Créer le répertoire s'il n'existe pas
if (!is_dir($profiling_dir)) {
    mkdir($profiling_dir, 0777, true);
}

$files = glob($profiling_dir . '/cachegrind.out.*');

echo "<h1>Visualiseur de Fichiers de Profiling Xdebug</h1>";

// Diagnostic des fichiers
if (!empty($files)) {
    echo "<h2>Diagnostic des fichiers :</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>Fichier</th><th>Taille</th><th>Type</th><th>Statut</th><th>Actions</th></tr>";
    
    foreach ($files as $file) {
        $basename = basename($file);
        $size = number_format(filesize($file) / 1024, 2) . ' KB';
        $content = file_get_contents($file, false, null, 0, 1000); // Lire les 1000 premiers caractères
        
        // Détecter le type de fichier
        $is_binary = !mb_check_encoding($content, 'UTF-8') || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $content);
        $is_cachegrind = strpos($content, 'version:') !== false || strpos($content, 'fl=') !== false;
        $is_compressed = substr($content, 0, 2) === "\x1f\x8b"; // gzip magic number
        
        $type = 'Inconnu';
        $status = '<span style="color: red;">Corrompu</span>';
        $actions = '<a href="?delete=' . urlencode($basename) . '">Supprimer</a>';
        
        if ($is_compressed) {
            $type = 'Compressé (gzip)';
            $status = '<span style="color: orange;">Nécessite décompression</span>';
            $actions = '<a href="?decompress=' . urlencode($basename) . '">Décompresser</a> | ' . $actions;
        } elseif ($is_cachegrind && !$is_binary) {
            $type = 'Cachegrind valide';
            $status = '<span style="color: green;">OK</span>';
            $actions = '<a href="?view=' . urlencode($basename) . '">Voir</a> | <a href="?download=' . urlencode($basename) . '">Télécharger</a>';
        } elseif ($is_binary) {
            $type = 'Binaire/Corrompu';
            $status = '<span style="color: red;">Format invalide</span>';
        }
        
        echo "<tr>";
        echo "<td>$basename</td>";
        echo "<td>$size</td>";
        echo "<td>$type</td>";
        echo "<td>$status</td>";
        echo "<td>$actions</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Gérer les actions
if (isset($_GET['delete'])) {
    $file = $profiling_dir . '/' . basename($_GET['delete']);
    if (file_exists($file)) {
        unlink($file);
        echo "<div style='background: #e6ffe6; padding: 10px; border: 1px solid #00cc00; margin: 10px 0;'>";
        echo "Fichier supprimé : " . htmlspecialchars(basename($file));
        echo "</div>";
        echo "<script>setTimeout(() => window.location.href = window.location.pathname, 2000);</script>";
    }
}

if (isset($_GET['decompress'])) {
    $file = $profiling_dir . '/' . basename($_GET['decompress']);
    if (file_exists($file)) {
        $compressed = file_get_contents($file);
        $decompressed = gzdecode($compressed);
        
        if ($decompressed !== false) {
            $new_file = $file . '.decompressed';
            file_put_contents($new_file, $decompressed);
            echo "<div style='background: #e6ffe6; padding: 10px; border: 1px solid #00cc00; margin: 10px 0;'>";
            echo "Fichier décompressé : " . htmlspecialchars(basename($new_file));
            echo "</div>";
        } else {
            echo "<div style='background: #ffe6e6; padding: 10px; border: 1px solid #ff9999; margin: 10px 0;'>";
            echo "Erreur lors de la décompression";
            echo "</div>";
        }
        echo "<script>setTimeout(() => window.location.href = window.location.pathname, 2000);</script>";
    }
}

if (isset($_GET['download'])) {
    $file = $profiling_dir . '/' . basename($_GET['download']);
    if (file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
        exit;
    }
}

if (isset($_GET['convert'])) {
    $file = $profiling_dir . '/' . basename($_GET['convert']);
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $data = parseCachegrind($content);
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . basename($file) . '.json"');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}

if (isset($_GET['view'])) {
    $file = $profiling_dir . '/' . basename($_GET['view']);
    if (file_exists($file)) {
        echo "<h2>Analyse du fichier : " . htmlspecialchars(basename($file)) . "</h2>";
        
        $content = file_get_contents($file);
        
        // Vérifier si le fichier est lisible
        if (!mb_check_encoding($content, 'UTF-8') || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', substr($content, 0, 100))) {
            echo "<div style='background: #ffe6e6; padding: 15px; border: 1px solid #ff9999; margin: 10px 0;'>";
            echo "<h3>⚠️ Fichier corrompu ou binaire détecté</h3>";
            echo "<p>Le fichier semble être dans un format binaire ou corrompu.</p>";
            echo "<p><strong>Solutions possibles :</strong></p>";
            echo "<ul>";
            echo "<li>Le fichier pourrait être compressé - essayez de le décompresser</li>";
            echo "<li>Régénérez le profil avec une configuration Xdebug correcte</li>";
            echo "<li>Vérifiez que le répertoire de sortie a les bonnes permissions</li>";
            echo "</ul>";
            
            // Afficher l'hexdump des premiers bytes
            echo "<h4>Hexdump (32 premiers octets) :</h4>";
            echo "<pre style='background: #f0f0f0; padding: 10px; font-family: monospace;'>";
            $hex = bin2hex(substr($content, 0, 32));
            echo chunk_split($hex, 2, ' ');
            echo "</pre>";
            
            // Essayer de détecter le type de fichier
            $magic = substr($content, 0, 4);
            if ($magic === "\x1f\x8b\x08\x00") {
                echo "<p style='color: orange;'>🔍 Détecté : Fichier compressé gzip</p>";
            } elseif (substr($content, 0, 2) === 'PK') {
                echo "<p style='color: orange;'>🔍 Détecté : Archive ZIP</p>";
            }
            echo "</div>";
            return;
        }
        
        $data = parseCachegrind($content);
        
        if (!empty($data['functions'])) {
            echo "<h3>Top 20 des fonctions les plus coûteuses :</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'><th>Fonction</th><th>Temps inclusif</th><th>Temps exclusif</th><th>Appels</th></tr>";
            
            $count = 0;
            foreach ($data['functions'] as $func => $info) {
                if ($count++ >= 20) break;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($func) . "</td>";
                echo "<td>" . number_format($info['inclusive_time']) . " µs</td>";
                echo "<td>" . number_format($info['exclusive_time']) . " µs</td>";
                echo "<td>" . number_format($info['call_count']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "<h3>Contenu brut (500 premiers caractères) :</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto; max-height: 200px; font-size: 12px;'>";
        echo htmlspecialchars(substr($content, 0, 500));
        if (strlen($content) > 500) echo "\n... (tronqué)";
        echo "</pre>";
        
        echo "<h3>Instructions d'utilisation :</h3>";
        echo "<div style='background: #e6f3ff; padding: 15px; border: 1px solid #0066cc; margin: 10px 0;'>";
        echo "<p><strong>Ce fichier est au format Cachegrind, pas une archive !</strong></p>";
        echo "<p>Pour l'analyser avec des outils externes :</p>";
        echo "<ul>";
        echo "<li><strong>KCacheGrind</strong> (Linux) : <code>kcachegrind " . basename($file) . "</code></li>";
        echo "<li><strong>QCacheGrind</strong> (Windows/Mac) : Ouvrir le fichier directement</li>";
        echo "<li><strong>Webgrind</strong> : Interface web pour analyser les profils</li>";
        echo "<li><strong>PhpStorm</strong> : Tools → Analyze Xdebug Profiler Snapshot</li>";
        echo "</ul>";
        echo "</div>";
    }
}

function parseCachegrind($content) {
    $lines = explode("\n", $content);
    $functions = [];
    $current_file = '';
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        if (strpos($line, 'fl=') === 0) {
            $current_file = substr($line, 3);
        } elseif (strpos($line, 'fn=') === 0) {
            $function = substr($line, 3);
            if (!isset($functions[$function])) {
                $functions[$function] = [
                    'inclusive_time' => 0,
                    'exclusive_time' => 0,
                    'call_count' => 0,
                    'file' => $current_file
                ];
            }
        } elseif (preg_match('/^(\d+)\s+(\d+)/', $line, $matches)) {
            if (!empty($function)) {
                $functions[$function]['exclusive_time'] += intval($matches[2]);
                $functions[$function]['call_count']++;
            }
        }
    }
    
    // Trier par temps exclusif
    uasort($functions, function($a, $b) {
        return $b['exclusive_time'] - $a['exclusive_time'];
    });
    
    return [
        'summary' => [
            'total_functions' => count($functions),
            'file_size' => strlen($content)
        ],
        'functions' => $functions
    ];
}

echo "<h2>Configuration et Diagnostic</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #0066cc; margin: 10px 0;'>";
echo "<h3>Pour corriger les fichiers corrompus :</h3>";
echo "<ol>";
echo "<li><strong>Vérifiez la configuration Xdebug :</strong><br>";
echo "<code>php -i | grep xdebug</code></li>";
echo "<li><strong>Nettoyez les anciens fichiers :</strong><br>";
echo "<code>rm -f $profiling_dir/cachegrind.out.*</code></li>";
echo "<li><strong>Testez avec un profil simple :</strong><br>";
echo "<a href='simple_test.php?XDEBUG_PROFILE=1'>simple_test.php?XDEBUG_PROFILE=1</a></li>";
echo "</ol>";
echo "</div>";
?>
