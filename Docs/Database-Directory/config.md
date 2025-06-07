# config.php

## Description
Fichier de configuration contenant les paramètres de connexion à la base de données et de sécurité.

## Constantes définies

### Connexion base de données
- **`DB_HOST`** : `'db'` - Hôte de la base de données (conteneur Docker)
- **`DB_NAME`** : `'lbdr_db'` - Nom de la base de données
- **`DB_USER`** : `'user'` - Nom d'utilisateur pour la connexion
- **`DB_PASS`** : `'userpassword'` - Mot de passe de connexion

### Sécurité
- **`PASSWORD_SALT`** : `'lbdr_salt_2024'` - Salt pour le hashage des mots de passe

## Utilisation
Ce fichier est inclus par `Database.php` via `require_once` pour récupérer les paramètres de connexion PDO.

## Environnement
Les valeurs sont configurées pour un environnement de développement avec Docker :
- L'hôte `'db'` correspond au service de base de données dans le `docker-compose.yml`
- Les identifiants sont ceux définis dans la configuration Docker
