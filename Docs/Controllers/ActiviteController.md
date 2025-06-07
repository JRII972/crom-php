# ActiviteController - Contrôleur des Activités

Le `ActiviteController` gère l'affichage et la manipulation des activités de jeu. Il centralise toute la logique liée aux activités individuelles et leur présentation détaillée.

## Responsabilités

### 1. **Affichage Détaillé d'une Activité**
- Présentation complète d'une activité spécifique
- Affichage des sessions associées
- Information sur les participants et maître de jeu

### 2. **Création d'Activités**
- Formulaire de création d'activité
- Validation des données
- Sauvegarde en base de données

### 3. **Gestion de la Navigation**
- Fil d'Ariane contextuel
- Intégration des scripts spécifiques
- Gestion des erreurs et redirections

## Méthodes Principales

### `index(int $id): string`
Affiche le détail d'une activité spécifique.

```php
public function index(int $id): string {
    // Récupération sécurisée de l'activité
    $activite = ActiviteDisplay::createSafe($id);
    
    // Vérification d'existence
    if ($activite === null) {
        header('Location: /');
        exit;
    }
    
    // Préparation des données
    $data = [
        'page_title' => 'Activité - ' . $activite->getNom(),
        'activite' => $activite,
        'jeu' => $activite->getJeu(),
        'maitre_de_jeu' => $activite->getMaitreJeu(),
        'sessions' => $activite->getSessions(),
        'nextSessions' => $activite->getNextSessions(),
        'joueurs' => $activite->getJoueursInscrits(),
        'activeTab' => 'description',
        'scripts' => ['activite-detail.js']
    ];
    
    // Configuration du fil d'Ariane
    $this->addBreadcrumb($activite->getNom());
    
    return $this->render('pages.activite', $data);
}
```

### `create(): string`
Affiche le formulaire de création d'activité.

```php
public function create(): string {
    return $this->render('components.activite.form', [
        'title' => 'Créer une nouvelle activité',
    ]);
}
```

## Utilisation d'ActiviteDisplay

### Récupération Sécurisée
```php
$activite = ActiviteDisplay::createSafe($id);
```

**Avantages** :
- Protection contre les ID invalides
- Gestion automatique des erreurs
- Retour `null` si activité introuvable

### Données Enrichies
L'objet `ActiviteDisplay` fournit des méthodes d'affichage spécialisées :

```php
$activite->getNom()              // Nom de l'activité
$activite->getJeu()              // Jeu associé
$activite->getMaitreJeu()        // Maître de jeu
$activite->getSessions()         // Toutes les sessions
$activite->getNextSessions()     // Prochaines sessions
$activite->getJoueursInscrits()  // Participants
```

## Structure des Données de Vue

### Données Principales
```php
$data = [
    'page_title' => 'Activité - ' . $activite->getNom(),
    'activite' => $activite,                    // Objet principal
    'jeu' => $activite->getJeu(),               // Informations du jeu
    'maitre_de_jeu' => $activite->getMaitreJeu(), // MJ responsable
    'sessions' => $activite->getSessions(),      // Sessions planifiées
    'nextSessions' => $activite->getNextSessions(), // Sessions à venir
    'joueurs' => $activite->getJoueursInscrits(), // Participants
    'activeTab' => 'description',               // Onglet actif par défaut
    'scripts' => ['activite-detail.js']         // Scripts spécifiques
];
```

### Organisation par Sections
Les données sont organisées pour supporter un affichage en onglets :
- **Description** : Détails de l'activité et du jeu
- **Sessions** : Planning et inscriptions
- **Participants** : Liste des joueurs inscrits

## Template Associé

### `pages.activite`
Template principal structuré en sections :

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    {{-- En-tête de l'activité --}}
    <div class="hero bg-base-200 rounded-lg mb-6">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <h1 class="text-5xl font-bold">{{ $activite->getNom() }}</h1>
                <p class="py-6">{{ $activite->getDescription() }}</p>
            </div>
        </div>
    </div>
    
    {{-- Navigation par onglets --}}
    <div class="tabs tabs-lifted">
        <input type="radio" name="activite_tabs" class="tab" aria-label="Description" checked />
        <div class="tab-content">
            @include('components.activite.description', ['activite' => $activite])
        </div>
        
        <input type="radio" name="activite_tabs" class="tab" aria-label="Sessions" />
        <div class="tab-content">
            @include('components.activite.sessions', ['sessions' => $sessions])
        </div>
        
        <input type="radio" name="activite_tabs" class="tab" aria-label="Participants" />
        <div class="tab-content">
            @include('components.activite.participants', ['joueurs' => $joueurs])
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="/assets/js/activite-detail.js"></script>
@endpush
```

## Gestion des Erreurs

### Validation des Paramètres
```php
if ($activite === null) {
    header('Location: /');
    exit;
}
```

**Stratégie** :
- Redirection immédiate si activité introuvable
- Pas d'affichage d'erreur explicite (sécurité)
- Retour vers page d'accueil

### Logging et Debug
```php
// En cas d'erreur lors de la récupération
try {
    $activite = ActiviteDisplay::createSafe($id);
} catch (\Exception $e) {
    error_log("Erreur récupération activité $id: " . $e->getMessage());
    return $this->handleError($e, 'Activité introuvable');
}
```

## Intégration JavaScript

### Scripts Spécifiques
```php
'scripts' => ['activite-detail.js']
```

**Fonctionnalités JavaScript** :
- Gestion des onglets dynamiques
- AJAX pour les inscriptions/désinscriptions
- Mise à jour en temps réel des places disponibles
- Validation côté client des formulaires

## Fil d'Ariane

### Configuration Automatique
```php
$this->addBreadcrumb($activite->getNom());
```

**Résultat** :
```
Accueil > Activité Specifique
```