# Documentation du Projet

## Contexte Général

Ce projet est une application web développée en **PHP** qui utilise plusieurs technologies modernes pour offrir une architecture robuste et une interface utilisateur attrayante.

### Technologies Principales

#### Backend - PHP
- **PHP** : Langage principal du backend
- **Composer** : Gestionnaire de dépendances avec autoloading PSR-4
- **Blade** : Moteur de templates Laravel (via `illuminate/view`)
- **JWT** : Authentification par tokens (Firebase JWT)
- **UUID** : Génération d'identifiants uniques

#### Frontend - CSS et Build
- **DaisyUI** : Framework de composants UI basé sur Tailwind CSS
- **Tailwind CSS** : Framework CSS utilitaire pour le styling
- **Vite** : Outil de build moderne pour la compilation des assets CSS

#### Base de Données
- Support pour les bases de données relationnelles
- Système de cache intégré

### Architecture

Le projet suit une architecture MVC (Model-View-Controller) avec :
- **API REST** : Endpoints pour les interactions frontend/backend
- **Templates Blade** : Système de vues componentisées
- **Autoloading PSR-4** : Chargement automatique des classes
- **Système de cache** : Optimisation des performances

### Outils de Développement
- **PHPUnit** : Tests unitaires
- **Vite** : Compilation et optimisation des assets
- **Docker** : Containerisation de l'environnement de développement
- **Git** : Contrôle de version

Ce projet combine les meilleures pratiques du développement PHP moderne avec des outils frontend performants pour créer une application web complète et maintenable.
