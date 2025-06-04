<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Xdebug - Interface Web</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .debug-info { background: #f0f0f0; padding: 20px; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .code { background: #f8f8f8; padding: 10px; border-left: 3px solid #007acc; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test de débogage Xdebug</h1>
        
        <div class="debug-info">
            <h2>Informations de débogage</h2>
            
            <?php
            // Variables de test pour le débogage
            $users = [
                ['id' => 1, 'nom' => 'Dupont', 'email' => 'dupont@example.com'],
                ['id' => 2, 'nom' => 'Martin', 'email' => 'martin@example.com'],
                ['id' => 3, 'nom' => 'Bernard', 'email' => 'bernard@example.com']
            ];
            
            $session_info = [
                'date' => date('Y-m-d H:i:s'),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A'
            ];
            
            // Point d'arrêt recommandé ici
            echo "<h3>Extension Xdebug:</h3>";
            if (extension_loaded('xdebug')) {
                echo "<p class='success'>✓ Xdebug est chargé</p>";
                echo "<div class='code'>";
                echo "Version: " . phpversion('xdebug') . "<br>";
                echo "Mode: " . ini_get('xdebug.mode') . "<br>";
                echo "Port: " . ini_get('xdebug.client_port') . "<br>";
                echo "Host: " . ini_get('xdebug.client_host') . "<br>";
                echo "</div>";
            } else {
                echo "<p class='error'>✗ Xdebug n'est pas chargé</p>";
            }
            
            // Fonction de test pour le débogage
            function processUser($user) {
                $processed = [
                    'id' => $user['id'],
                    'nom_complet' => strtoupper($user['nom']),
                    'email_domain' => explode('@', $user['email'])[1] ?? '',
                    'timestamp' => time()
                ];
                
                // Point d'arrêt recommandé ici
                return $processed;
            }
            
            echo "<h3>Données de test:</h3>";
            echo "<div class='code'>";
            echo "<strong>Utilisateurs:</strong><br>";
            foreach ($users as $user) {
                $processed = processUser($user);
                echo "ID: {$processed['id']} - {$processed['nom_complet']} ({$processed['email_domain']})<br>";
            }
            echo "</div>";
            
            echo "<h3>Informations de session:</h3>";
            echo "<div class='code'>";
            foreach ($session_info as $key => $value) {
                echo "<strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($value) . "<br>";
            }
            echo "</div>";
            ?>
        </div>
        
        <h2>Instructions pour le débogage</h2>
        <ol>
            <li>Ouvrez ce fichier dans VS Code</li>
            <li>Placez des points d'arrêt sur les lignes marquées comme "Point d'arrêt recommandé"</li>
            <li>Lancez la configuration "Listen for Xdebug" dans l'onglet Debug</li>
            <li>Rechargez cette page dans votre navigateur</li>
            <li>Le débogueur devrait s'arrêter aux points d'arrêt</li>
        </ol>
        
        <p><strong>URL de test:</strong> 
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?debug=1&test=value">
            <?php echo $_SERVER['PHP_SELF']; ?>?debug=1&test=value
        </a></p>
    </div>
</body>
</html>
