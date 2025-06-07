# HomeController - Contrôleur d'Accueil

Le `HomeController` gère l'affichage de la page d'accueil de l'application. Il centralise l'affichage des activités et sessions mises en avant.

## Responsabilités

### 1. **Page d'Accueil Principale**
- Affichage des activités suggérées par genre
- Présentation des sessions de la semaine
- Mise en avant du contenu principal

### 2. **Curation de Contenu**
- Sélection des activités par catégories populaires
- Filtrage des sessions par période
- Organisation des données pour un affichage optimal

## Méthodes Principales

### `index(): string`
Méthode principale qui génère la page d'accueil.

```php
public function index(): string {
    $data = [
        'page_title' => 'CROM | BDR',
        'suggestion' => [
            'Fantaisie' => SessionDisplay::search(/* critères fantasy */),
            'Enquête' => SessionDisplay::search(/* critères enquête */),
            'Coopératif' => SessionDisplay::search(/* critères coopératif */)
        ],
        'next_week' => [
            'Vendredi' => SessionDisplay::search(/* sessions vendredi */),
            // Autres jours de la semaine...
        ]
    ];
    
    return $this->render('pages.home', $data);
}
```

## Architecture des Données

### Suggestions par Genre
Le contrôleur organise les suggestions en catégories thématiques :

```php
'suggestion' => [
    'Fantaisie' => [...],    // Jeux de fantasy
    'Enquête' => [...],      // Jeux d'enquête/mystère
    'Coopératif' => [...]    // Jeux coopératifs
]
```

**Avantages** :
- Découverte facile pour les utilisateurs
- Diversité du contenu proposé
- Catégorisation intuitive

### Planning Hebdomadaire
Affichage des sessions par jour de la semaine :

```php
'next_week' => [
    'Vendredi' => [...],     // Sessions du vendredi
    'Samedi' => [...],       // Sessions du samedi
    'Dimanche' => [...]      // Sessions du dimanche
]
```

**Avantages** :
- Vision claire du planning
- Planification facilitée pour les joueurs
- Mise en avant des créneaux populaires

## Utilisation des Classes Display

### SessionDisplay
```php
SessionDisplay::search(
    $this->pdo,
    categories: Genre::search($this->pdo, 'fantasy'),
    serialize: false
)
```

**Paramètres** :
- `categories` : Filtrage par genre
- `dateDebut/dateFin` : Période de recherche
- `serialize` : Format de retour des données

### Genre
```php
Genre::search($this->pdo, 'fantasy')
```

Recherche des genres par nom ou critères spécifiques.

## Template Associé

### `pages.home`
Template principal qui structure l'affichage :

```blade
@extends('layouts.app')

@section('content')
    {{-- Section suggestions --}}
    @foreach($suggestion as $genre => $sessions)
        <section class="genre-section">
            <h2>{{ $genre }}</h2>
            @include('components.session.grid', ['sessions' => $sessions])
        </section>
    @endforeach
    
    {{-- Planning de la semaine --}}
    <section class="weekly-schedule">
        @foreach($next_week as $jour => $sessions)
            <div class="day-column">
                <h3>{{ $jour }}</h3>
                @include('components.session.list', ['sessions' => $sessions])
            </div>
        @endforeach
    </section>
@endsection
```

## Logique Métier

### 1. **Curation Automatique**
- Sélection automatique des genres populaires
- Rotation du contenu mis en avant
- Équilibrage des propositions

### 2. **Optimisation Performance**
- Requêtes optimisées via `SessionDisplay`
- Cache potentiel des résultats
- Limitation du nombre d'éléments affichés

### 3. **Expérience Utilisateur**
- Découverte facilitée du contenu
- Navigation intuitive vers les détails
- Appel à l'action clair

## Évolutions Possibles

### 1. **Personnalisation**
- Suggestions basées sur l'historique utilisateur
- Préférences de genres sauvegardées

### 2. **Métriques**
- Tracking des clics sur les suggestions
- Analyse des genres populaires

### 3. **Contenu Dynamique**
- Mise à jour en temps réel
- Intégration d'actualités
- Événements spéciaux

