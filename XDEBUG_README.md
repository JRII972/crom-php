# Configuration Xdebug pour VS Code

## Statut de la configuration

✅ **Xdebug est correctement configuré et prêt à l'usage !**

### Informations de configuration
- **Version Xdebug**: 3.4.3
- **Mode**: debug
- **Port**: 9003
- **Host**: host.docker.internal
- **Start with request**: Activé

## Fichiers de configuration créés

### 1. `.vscode/launch.json`
Contient 3 configurations de débogage :
- **Listen for Xdebug**: Pour écouter les connexions Xdebug (recommandé pour le web)
- **Launch currently open script**: Pour déboguer le script PHP actuellement ouvert
- **Debug Web Application**: Configuration avancée pour les applications web

### 2. `.vscode/settings.json`
Mis à jour avec les paramètres PHP optimaux pour VS Code.

### 3. Fichiers de test
- `test_xdebug.php`: Script CLI pour tester Xdebug
- `debug_test.php`: Page web de test avec exemples de débogage

## Instructions d'utilisation

### Pour déboguer un script PHP (CLI)
1. Ouvrez le script PHP dans VS Code
2. Placez des points d'arrêt (cliquez dans la marge à gauche des numéros de ligne)
3. Appuyez sur `F5` ou allez dans le menu Debug > Start Debugging
4. Sélectionnez "Launch currently open script"

### Pour déboguer une application web
1. Placez des points d'arrêt dans votre code PHP
2. Appuyez sur `F5` ou allez dans le menu Debug > Start Debugging
3. Sélectionnez "Listen for Xdebug"
4. Rechargez votre page web dans le navigateur
5. Le débogueur s'arrêtera aux points d'arrêt

### Test rapide
1. Ouvrez `debug_test.php` dans VS Code
2. Placez un point d'arrêt sur la ligne avec le commentaire "Point d'arrêt recommandé"
3. Lancez "Listen for Xdebug"
4. Visitez `http://localhost/debug_test.php` dans votre navigateur

## Raccourcis clavier utiles
- `F5`: Démarrer le débogage
- `F9`: Basculer un point d'arrêt
- `F10`: Passer à la ligne suivante (step over)
- `F11`: Entrer dans la fonction (step into)
- `Shift+F11`: Sortir de la fonction (step out)
- `Shift+F5`: Arrêter le débogage

## Dépannage

### Si le débogage ne fonctionne pas
1. Vérifiez que l'extension PHP Debug est installée dans VS Code
2. Vérifiez que Xdebug est en mode debug : `php -i | grep xdebug.mode`
3. Vérifiez les logs Xdebug : `tail -f /tmp/xdebug.log`
4. Assurez-vous que le port 9003 n'est pas bloqué

### Extensions VS Code recommandées
- PHP Debug (felixfbecker.php-debug)
- PHP Intelephense (bmewburn.vscode-intelephense-client)
- PHP CS Fixer (junstyle.php-cs-fixer)

## Commandes utiles

```bash
# Vérifier l'installation Xdebug
php -m | grep xdebug

# Voir la configuration Xdebug
php -i | grep xdebug

# Tester un script PHP
php test_xdebug.php

# Voir les logs Xdebug
tail -f /tmp/xdebug.log
```

---

**Remarque**: Cette configuration est optimisée pour un environnement Docker avec PHP-FPM et Apache.
