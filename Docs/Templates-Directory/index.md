# Documentation du répertoire Templates

## Vue d'ensemble

Le répertoire `/var/www/html/App/templates/` contient l'architecture de templating de l'application basée sur le moteur **Blade** (inspiré de Laravel). Ce système permet une organisation modulaire et hiérarchique des vues avec un système d'héritage et d'inclusion récursive.

## Structure du répertoire

```
App/templates/
├── layouts/                    # Gabarits de base
│   ├── base.blade.php         # Layout principal avec sidebar/navbar
│   ├── app.blade.php          # Layout simple alternatif
│   ├── auth.blade.php         # Layout pour authentification
│   └── full.blade.php         # Layout pleine page
├── components/                 # Composants réutilisables
│   ├── navigation/            # Navigation (sidebar, navbar, menu)
│   ├── activite/              # Composants spécifiques aux activités
│   ├── activites/             # Listes et grilles d'activités
│   ├── auth/                  # Composants d'authentification
│   ├── profile/               # Composants de profil utilisateur
│   ├── user/                  # Composants utilisateur
│   ├── breadcrumb.blade.php   # Fil d'ariane
│   ├── date-formatter.blade.php # Formatage des dates
│   └── scripts.blade.php      # Scripts JavaScript globaux
├── pages/                      # Pages complètes
│   ├── home.blade.php         # Page d'accueil
│   ├── activites.blade.php    # Liste des activités
│   ├── activite.blade.php     # Détail d'une activité
│   ├── profile.blade.php      # Profil utilisateur
│   ├── login.blade.php        # Page de connexion
│   └── form/                  # Formulaires spécifiques
└── test-*.blade.php           # Templates de test
```

## Architecture et fonctionnement

### Système de layouts hiérarchiques

#### Layout principal : `base.blade.php`
Le layout de base définit la structure complète de l'application :
- **Structure HTML5** complète avec head et body
- **Drawer responsive** avec sidebar pour desktop et overlay mobile
- **Inclusion de composants** navigation via `@include`
- **Zones de contenu** définies avec `@yield('content')`
- **CSS et JavaScript** intégrés (Tailwind, DaisyUI, Vite)

#### Layouts alternatifs
- **`app.blade.php`** : Layout simple sans sidebar
- **`auth.blade.php`** : Layout pour pages d'authentification
- **`full.blade.php`** : Layout pleine page sans contraintes

### Système d'importations récursives

#### Héritage avec `@extends`
```php
@extends('layouts.base')    // Hérite du layout de base
```

#### Inclusions de composants avec `@include`
```php
@include('components.navigation.sidebar')
@include('components.navigation.navbar')
@include('components.scripts')
```

#### Inclusions avec paramètres
```php
@include('components.activites.liste-activites', [
    'titre_section' => 'La semaine prochaine',
    'sections' => $next_week
])
```

#### Sections et yield
```php
@section('content')
    // Contenu de la page
@endsection

@yield('content')    // Dans le layout
@yield('head')       // Scripts CSS additionnels
@yield('scripts')    // Scripts JS additionnels
```

### Architecture modulaire par composants

#### Navigation (`components/navigation/`)
- **`sidebar.blade.php`** : Sidebar principal avec menu hiérarchique
- **`navbar.blade.php`** : Barre de navigation top
- **`logo.blade.php`** : Logo et nom de l'association
- **`main-menu.blade.php`** : Menu principal de navigation

#### Utilisateur (`components/user/`)
- **`user-menu.blade.php`** : Menu utilisateur dans sidebar
- **`user-profile.blade.php`** : Profil utilisateur compact

#### Activités (`components/activites/`)
- **`header-search.blade.php`** : En-tête avec recherche
- **`filtres.blade.php`** : Filtres de recherche
- **`hero-banner.blade.php`** : Bannière d'accueil
- **`liste-activites.blade.php`** : Grille d'activités réutilisable
- **`scripts.blade.php`** : JavaScript spécifique aux activités

### Flux d'inclusion récursive

#### Exemple complet : Page activités
```
activites.blade.php
├── @extends('layouts.base')
│   ├── @include('components.navigation.sidebar')
│   │   ├── @include('components.navigation.logo')
│   │   ├── @include('components.navigation.main-menu')
│   │   ├── @include('components.user.user-menu')
│   │   └── @include('components.user.user-profile')
│   ├── @include('components.navigation.navbar')
│   └── @include('components.scripts')
└── @section('content')
    ├── @include('components.activites.header-search')
    ├── @include('components.activites.filtres')
    ├── @include('components.activites.hero-banner')
    └── @include('components.activites.liste-activites')
```

### Fichiers importants dans layouts/

#### `base.blade.php` - Layout principal ⭐
- **Rôle** : Gabarit principal de l'application
- **Structure** : Drawer responsive + sidebar + navbar + main content
- **Intégrations** : Tailwind CSS, DaisyUI, Material Symbols, Vite assets
- **Composants inclus** : Navigation complète, scripts globaux
- **Variables** : `$page_title` pour le titre dynamique

#### `auth.blade.php` - Layout authentification
- **Rôle** : Gabarit pour pages de connexion/inscription
- **Structure** : Layout simplifié sans navigation
- **Usage** : Pages login, register, password reset

## Système de variables et paramètres

### Variables globales
- **`$page_title`** : Titre de la page (avec fallback)
- **`$message`** : Messages système
- **Données métier** : `$activites`, `$suggestion`, `$next_week`

### Paramètres de composants
Les composants reçoivent des paramètres via l'inclusion :
```php
@include('components.activites.liste-activites', [
    'titre_section' => 'Pourrais vous intérésser !',
    'sections' => $suggestion
])
```

## Avantages de cette architecture

### Modularité
- **Composants réutilisables** à travers l'application
- **Séparation des responsabilités** (layout/navigation/contenu)
- **Maintenance facilitée** par la centralisation des composants

### Flexibilité
- **Layouts multiples** selon le contexte
- **Compositions dynamiques** avec paramètres
- **Extensions faciles** via l'héritage

### Performance
- **Cache des templates** compilés (dans `App/cache/`)
- **Inclusion conditionnelle** possible
- **Optimisation CSS/JS** via Vite

### Cohérence
- **Structure HTML** cohérente via layouts
- **Composants standardisés** (navigation, profils)
- **Thème unifié** avec DaisyUI/Tailwind

## Intégration avec le système de routage

Les templates sont appelés depuis les contrôleurs via le moteur de template :
```php
// Dans un contrôleur
return renderTemplate('pages.activites', [
    'page_title' => 'Nos Activités',
    'activites' => $activites,
    'suggestion' => $suggestions
]);
```

Cette architecture de templating offre une approche moderne et modulaire pour la construction d'interfaces utilisateur cohérentes et maintenables, avec un système d'héritage et d'inclusion permettant une réutilisabilité maximale des composants.
