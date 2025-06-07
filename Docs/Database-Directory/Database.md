# Database.php

## Description
Classe principale singleton pour la gestion de la connexion à la base de données MariaDB.

## Fonctionnement

### Pattern Singleton
- **Instance unique** : Une seule connexion PDO partagée dans toute l'application
- **Constructeur privé** : Empêche l'instanciation directe
- **Méthode statique** : `getConnection()` pour accéder à la connexion

### Gestion des connexions
```php
Database::getConnection() // Retourne l'instance PDO
```

### Configuration utilisée
- Utilise les constantes définies dans `config.php`
- **Host** : `DB_HOST`
- **Base** : `DB_NAME` 
- **Utilisateur** : `DB_USER`
- **Mot de passe** : `DB_PASS`

### Paramètres PDO
- **Charset** : UTF8MB4 pour support Unicode complet
- **Mode d'erreur** : `PDO::ERRMODE_EXCEPTION` pour gestion des exceptions
- **Type de base** : MySQL/MariaDB

### Gestion d'erreurs
- **Catch PDOException** : Capture les erreurs de connexion
- **Logging** : Enregistre les erreurs avec `error_log()`
- **Réponse HTTP** : Retourne code 500 et JSON d'erreur
- **Arrêt propre** : `exit` en cas d'échec de connexion

## Utilisation dans le projet
Toutes les classes du dossier `Types/` utilisent cette classe pour obtenir leur connexion à la base de données via la méthode statique `getConnection()`.
