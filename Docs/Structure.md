# Structure du projet - Architecture modulaire

!!! info "Organisation"
    Le projet suit une architecture modulaire claire qui sépare les responsabilités entre les différentes couches de l'application.

## Vue d'ensemble de l'architecture

```
projet/
├── App/                    # Code principal de l'application
├── api/                    # Points d'entrée API
├── config/                 # Configuration de l'application
├── public/                 # Assets publics et points d'entrée web
├── src/                    # Sources CSS/JS
├── tests/                  # Tests unitaires
├── vendor/                 # Dépendances Composer
├── node_modules/           # Dépendances NPM
├── dist/                   # Assets compilés par Vite
└── Docs/                   # Documentation
```

## Modules principaux

!!! example "Architecture en couches"
    L'application est organisée en modules spécialisés avec des responsabilités bien définies.

### `/App` - Application Core

!!! abstract "Cœur de l'application"
    **Rôle** : Contient le code métier principal de l'application
- `Api/` : Classes d'API pour chaque entité métier
- `controllers/` : Contrôleurs MVC
- `Database/` : Configuration et utilitaires base de données
- `templates/` : Templates Blade (layouts, components, pages)
- `views/` : Vues PHP traditionnelles
- `Utils/` : Classes utilitaires
- `cache/` : Cache de l'application

### `/api` - Points d'Entrée API
**Rôle** : Endpoints REST accessibles publiquement
- Points d'entrée pour les requêtes AJAX/fetch
- Gestion des tokens CSRF
- Interface entre frontend et backend

### `/public` - Assets Publics
**Rôle** : Fichiers accessibles directement par le navigateur
- Pages PHP principales (index.php, login.php, etc.)
- Assets statiques (images, fonts, CSS compilé)
- Point d'entrée principal de l'application

### `/src` - Sources Frontend
**Rôle** : Code source CSS/JS avant compilation
- Fichiers CSS principaux
- Sources Tailwind/DaisyUI
- Assets non compilés

### `/config` - Configuration
**Rôle** : Fichiers de configuration de l'application
- Configuration du cache
- Paramètres généraux

### `/tests` - Tests
**Rôle** : Tests unitaires et d'intégration
- Tests PHPUnit
- Validation du code métier

### `/vendor` et `/node_modules`
**Rôle** : Dépendances externes
- `vendor/` : Packages PHP gérés par Composer
- `node_modules/` : Packages NPM pour le build frontend

### `/dist` - Build Output
**Rôle** : Assets compilés par Vite
- CSS optimisé et minifié
- Assets finaux pour la production

## Flux de Données

1. **Requête HTTP** → `/public` (point d'entrée)
2. **Routage** → Contrôleurs dans `/App/controllers`
3. **Logique Métier** → Classes dans `/App`
4. **Données** → `/App/Database`
5. **Rendu** → Templates Blade `/App/templates`
6. **Assets** → CSS compilé depuis `/src` vers `/dist`

Cette structure permet une séparation claire des responsabilités et facilite la maintenance et l'évolution du projet.
