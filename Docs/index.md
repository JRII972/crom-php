# Documentation du Projet Crom PHP

Bienvenue dans la documentation complète du projet Crom PHP. Cette documentation couvre tous les aspects du développement, de la structure et du déploiement de l'application de gestion d'activités ludiques.

!!! info "À propos de cette documentation"
    Cette documentation est générée avec MkDocs Material et couvre l'architecture complète de l'application, de la base de données aux interfaces utilisateur.

## 🚀 Démarrage rapide

### [Contexte Général](README.md)
Vue d'ensemble du projet, technologies utilisées et architecture générale.

### [Structure du Projet](Structure.md)
Organisation des dossiers et flux de données de l'application.

## 📁 Documentation des modules

!!! note "Structure modulaire"
    L'application est organisée en modules spécialisés, chacun avec sa documentation dédiée.

### [Dossier `/App`](App-Directory.md)
Cœur de l'application : API, contrôleurs, templates Blade, et logique métier.

### [Dossier `/public`](Public-Directory.md)
Assets publics, points d'entrée web et configuration serveur.

### [API REST](Api-Directory.md)
Points d'entrée API REST, sécurité et format des réponses.

### [Base de données](Database-Directory/)
Architecture ORM complète, entités métier et gestion des données.

### [Templates](Templates-Directory/)
Système de templating Blade avec composants modulaires.

### [Tests](Tests-Directory.md)
Suite de tests automatisés, PHPUnit et stratégies de test.

## 🛠 Guides techniques

!!! tip "Technologies utilisées"
    Le projet utilise PHP 8.1+, Blade templates, TailwindCSS + DaisyUI, et une architecture ORM personnalisée.

### [Programmation Orientée Objet](Programmation-Orientee-Objet.md)
Concepts POO, architecture du projet et exemples d'implémentation.

### [Build et Assets Frontend](Frontend-Build.md)
Configuration Vite, Tailwind CSS, DaisyUI et workflow de développement.

### [Moteur de templates](Template-Engines.md)
Architecture Blade et système de composants.

### [Framework CSS](DaisyUI-Tailwind.md)
Guide d'utilisation de DaisyUI avec Tailwind CSS.

### [Architecture des contrôleurs](Controleurs.md)
Pattern MVC et gestion des routes.

## 📋 Références spécialisées

### [Affichage des classes](Classes-Display.md)
Documentation des classes d'affichage et utilitaires.

### [Gestion iCalendar](iCalendat%20Rrule.md)
Intégration des récurrences d'événements.

## 🗄 Archives

!!! warning "Documentation historique"
    Ces fichiers sont conservés pour référence mais peuvent être obsolètes.

- [Database (ancienne version)](Database-Old.md)
- [Database (version mise à jour)](Database-Updated.md)

## 💻 Installation et configuration

!!! example "Prérequis système"
    - PHP 8.1+ avec extensions PDO, APCu
    - Composer pour les dépendances PHP
    - Node.js 18+ et npm pour le frontend
    - Base de données MariaDB/MySQL
    - Serveur web (Apache/Nginx)

### Installation rapide

```bash
# Cloner le projet
git clone <repository-url>
cd crom-php

# Dépendances PHP
composer install

# Dépendances Node.js
npm install

# Build des assets
npm run build

# Configuration de la base de données
cp App/Database/config.php.example App/Database/config.php
# Éditer config.php avec vos paramètres

# Initialiser la base de données
mysql -u user -p database < App/Database/database_creation.sql
```

!!! success "Prêt à commencer !"
    Consultez la [structure du projet](Structure.md) pour comprendre l'organisation du code.
npm install

# Build des assets CSS
npm run build:css

# Serveur de développement
npm run dev
```

### Configuration
1. Configurer la base de données dans `/App/Database/config.php`
2. Configurer le serveur web avec `/public` comme DocumentRoot
3. Vérifier les permissions sur `/App/cache/`

## 📖 Ressources Additionnelles

### Technologies Utilisées
- [PHP](https://www.php.net/) - Langage backend
- [Composer](https://getcomposer.org/) - Gestionnaire de dépendances PHP
- [Blade](https://laravel.com/docs/blade) - Moteur de templates
- [Tailwind CSS](https://tailwindcss.com/) - Framework CSS
- [DaisyUI](https://daisyui.com/) - Composants UI
- [Vite](https://vitejs.dev/) - Outil de build
- [PHPUnit](https://phpunit.de/) - Framework de tests

### Conventions de Code
- PSR-4 pour l'autoloading
- PSR-12 pour le style de code
- Nommage explicite des classes et méthodes
- Documentation des fonctions publiques

## 🔧 Maintenance

### Mise à Jour des Dépendances
```bash
# PHP
composer update

# Node.js
npm update
```

### Nettoyage du Cache
```bash
# Cache de l'application
rm -rf App/cache/*

# Cache de build
rm -rf dist/ node_modules/.vite/
```

---

*Cette documentation est maintenue à jour avec l'évolution du projet. N'hésitez pas à contribuer en ajoutant ou corrigeant les informations.*
