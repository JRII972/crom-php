# Dossier `/App` - Application Core

Le dossier `/App` contient le cœur de l'application avec une architecture MVC bien structurée.

## Structure

```
App/
├── Api/                    # API REST pour chaque entité
├── controllers/            # Contrôleurs MVC
├── Database/              # Configuration et accès aux données
├── templates/             # Templates Blade
├── views/                 # Vues PHP traditionnelles
├── Utils/                 # Classes utilitaires
└── cache/                 # Cache de l'application
```

## `/App/Api` - Couche API REST

Contient les classes d'API pour chaque entité métier du système :

- `ActivitesApi.php` : Gestion des activités
- `EvenementsApi.php` : Gestion des événements
- `GenresApi.php` : Gestion des genres/catégories
- `JeuxApi.php` : Gestion des jeux
- `LieuxApi.php` : Gestion des lieux
- `PaiementsApi.php` : Gestion des paiements
- `SessionsApi.php` : Gestion des sessions utilisateur
- `UtilisateursApi.php` : Gestion des utilisateurs
- `APIHandler.php` : Gestionnaire principal des API
- `Exception.php` : Gestion des exceptions API

**Responsabilités** :
- Exposition des endpoints REST
- Validation des données
- Sérialisation/désérialisation JSON
- Gestion des erreurs API

## `/App/controllers` - Contrôleurs MVC

Contient les contrôleurs qui orchestrent la logique métier entre les modèles et les vues.

**Responsabilités** :
- Traitement des requêtes HTTP
- Coordination entre modèles et vues
- Gestion du flux de données
- Rendu des templates

## `/App/Database` - Couche d'Accès aux Données

Gestion de la persistance et de la configuration de la base de données.

- `config.php` : Configuration de connexion à la base de données

**Responsabilités** :
- Configuration des connexions
- Requêtes SQL
- Mapping objet-relationnel
- Gestion des transactions

## `/App/templates` - Templates Blade

Système de templates utilisant le moteur Blade de Laravel.

```
templates/
├── components/            # Composants réutilisables
├── layouts/              # Layouts de base
└── pages/                # Pages complètes
```

**Avantages de Blade** :
- Syntaxe claire et expressive
- Héritage de templates
- Composants réutilisables
- Protection XSS automatique
- Directives personnalisées

## `/App/views` - Vues PHP Traditionnelles

Vues PHP classiques pour les parties de l'application qui n'utilisent pas Blade.

**Utilisation** :
- Pages legacy
- Vues simples sans logique complexe
- Compatibilité avec du code existant

## `/App/Utils` - Classes Utilitaires

Classes d'aide et fonctions transversales utilisées dans toute l'application.

**Types d'utilitaires** :
- Helpers de formatage
- Utilitaires de validation
- Classes de configuration
- Fonctions de sécurité

## `/App/cache` - Cache de l'Application

Stockage des fichiers de cache pour optimiser les performances.

**Contenu** :
- Templates compilés
- Données mises en cache
- Configuration en cache
- Assets optimisés

**Gestion** :
- Invalidation automatique
- TTL configurable
- Stratégies de cache multiples
