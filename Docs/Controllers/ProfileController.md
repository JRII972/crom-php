# ProfileController - Gestion des Profils

Le `ProfileController` gère l'affichage et la modification des profils utilisateur. Il centralise toutes les fonctionnalités liées à la gestion des comptes utilisateur et leurs données personnelles.

## Responsabilités

### 1. **Affichage des Profils**
- Page de profil personnelle (utilisateur connecté)
- Consultation des profils publics d'autres utilisateurs
- Gestion des onglets et sections du profil

### 2. **Données Utilisateur**
- Informations personnelles
- Statistiques d'activité
- Historique des participations
- Préférences et paramètres

### 3. **Navigation et Tabs**
- Interface à onglets pour organiser l'information
- Navigation contextuelle
- Gestion des états actifs

## Méthodes Principales

### `show(?int $userId = null, ?string $activeTab = 'activites'): string`
Affiche la page de profil avec gestion flexible de l'utilisateur et de l'onglet actif.

```php
public function show(?int $userId = null, ?string $activeTab = 'activites'): string {
    // Détermination de l'utilisateur à afficher
    if (!$userId) {
        // Utilise l'utilisateur connecté par défaut
        $userId = $_SESSION['user_id'] ?? 1;
    }
    
    // Récupération des données utilisateur
    $user = $this->getUserData($userId);
    $stats = $this->getUserStats($userId);
    $activites = $this->getUserActivites($userId);
    
    // Préparation des données pour la vue
    $data = [
        'page_title' => 'Profil - ' . $user['name'],
        'user' => $user,
        'stats' => $stats,
        'activites' => $activites,
        'activeTab' => $activeTab,
        'isOwnProfile' => $userId === ($_SESSION['user_id'] ?? null)
    ];
    
    return $this->render('pages.profile', $data);
}
```

## Architecture des Données

### Données Utilisateur
```php
private function getUserData(int $userId): array {
    return [
        'id' => $userId,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'avatar' => '/assets/images/avatars/default.png',
        'bio' => 'Passionné de jeux de rôle...',
        'dateInscription' => '2023-01-15',
        'dernièreConnexion' => '2025-06-07 10:30:00',
        'isPublic' => true
    ];
}
```

### Statistiques d'Activité
```php
private function getUserStats(int $userId): array {
    return [
        'totalActivites' => 42,        // Nombre total d'activités
        'activitesEnCours' => 3,       // Activités actives
        'sessionsJouees' => 156,       // Sessions auxquelles il a participé
        'heuresDeJeu' => 312,          // Temps total de jeu
        'genresPreferes' => [          // Genres favoris
            'Fantasy' => 35,
            'Sci-Fi' => 20,
            'Horreur' => 15
        ],
        'rang' => 'Joueur Expérimenté'
    ];
}
```

### Activités Utilisateur
```php
private function getUserActivites(int $userId): array {
    return [
        'enCours' => [
            // Activités auxquelles l'utilisateur participe actuellement
        ],
        'historique' => [
            // Activités passées
        ],
        'favorites' => [
            // Activités mises en favoris
        ],
        'organisees' => [
            // Activités que l'utilisateur organise (MJ)
        ]
    ];
}
```

## Interface à Onglets

### Structure des Onglets
```php
$availableTabs = [
    'activites' => 'Mes Activités',
    'stats' => 'Statistiques', 
    'preferences' => 'Préférences',
    'historique' => 'Historique'
];
```

### Gestion de l'Onglet Actif
```php
// Validation de l'onglet demandé
if (!in_array($activeTab, array_keys($availableTabs))) {
    $activeTab = 'activites'; // Fallback par défaut
}
```

## Template Associé

### `pages.profile`
Template principal avec interface à onglets :

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    {{-- En-tête du profil --}}
    <div class="profile-header bg-base-200 rounded-lg p-6 mb-6">
        <div class="flex items-center space-x-6">
            <div class="avatar">
                <div class="w-24 rounded-full">
                    <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" />
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-bold">{{ $user['name'] }}</h1>
                <p class="text-gray-600">{{ $user['bio'] }}</p>
                <div class="badges mt-2">
                    <span class="badge badge-primary">{{ $stats['rang'] }}</span>
                    @if($isOwnProfile)
                        <button class="btn btn-sm btn-outline">Modifier le profil</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- Navigation par onglets --}}
    <div class="tabs tabs-boxed mb-6">
        @foreach(['activites', 'stats', 'preferences', 'historique'] as $tab)
            <a class="tab {{ $activeTab === $tab ? 'tab-active' : '' }}" 
               href="/profile/{{ $user['id'] }}/{{ $tab }}">
                {{ ucfirst($tab) }}
            </a>
        @endforeach
    </div>
    
    {{-- Contenu de l'onglet actif --}}
    <div class="tab-content">
        @if($activeTab === 'activites')
            @include('components.profile.activites', ['activites' => $activites])
        @elseif($activeTab === 'stats')
            @include('components.profile.stats', ['stats' => $stats])
        @elseif($activeTab === 'preferences')
            @include('components.profile.preferences', ['user' => $user])
        @elseif($activeTab === 'historique')
            @include('components.profile.historique', ['historique' => $activites['historique']])
        @endif
    </div>
</div>
@endsection
```

## Composants de Profil

### Onglet Activités (`components.profile.activites`)
```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Activités en cours --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">Activités en cours</h2>
            @forelse($activites['enCours'] as $activite)
                @include('components.activite.mini-card', ['activite' => $activite])
            @empty
                <p class="text-gray-500">Aucune activité en cours</p>
            @endforelse
        </div>
    </div>
    
    {{-- Activités organisées --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">Activités organisées</h2>
            @forelse($activites['organisees'] as $activite)
                @include('components.activite.mini-card', ['activite' => $activite])
            @empty
                <p class="text-gray-500">Aucune activité organisée</p>
            @endforelse
        </div>
    </div>
</div>
```

### Onglet Statistiques (`components.profile.stats`)
```blade
<div class="stats stats-vertical lg:stats-horizontal shadow">
    <div class="stat">
        <div class="stat-title">Activités totales</div>
        <div class="stat-value">{{ $stats['totalActivites'] }}</div>
    </div>
    
    <div class="stat">
        <div class="stat-title">Sessions jouées</div>
        <div class="stat-value">{{ $stats['sessionsJouees'] }}</div>
    </div>
    
    <div class="stat">
        <div class="stat-title">Heures de jeu</div>
        <div class="stat-value">{{ $stats['heuresDeJeu'] }}h</div>
    </div>
</div>

{{-- Graphique des genres préférés --}}
<div class="mt-6">
    <h3 class="text-xl font-bold mb-4">Genres préférés</h3>
    @foreach($stats['genresPreferes'] as $genre => $count)
        <div class="flex justify-between items-center mb-2">
            <span>{{ $genre }}</span>
            <progress class="progress progress-primary w-56" value="{{ $count }}" max="50"></progress>
            <span class="text-sm">{{ $count }}</span>
        </div>
    @endforeach
</div>
```

## Gestion des Permissions

### Profil Personnel vs Profil Public
```php
$isOwnProfile = $userId === ($_SESSION['user_id'] ?? null);

if ($isOwnProfile) {
    // Affichage complet avec données privées
    $data['showPrivateData'] = true;
    $data['canEdit'] = true;
} else {
    // Vérification de la visibilité publique
    if (!$user['isPublic']) {
        return $this->render('pages.error', [
            'message' => 'Ce profil est privé'
        ]);
    }
    $data['showPrivateData'] = false;
    $data['canEdit'] = false;
}
```

## Évolutions Possibles

### 1. **Fonctionnalités Sociales**
- Système d'amis/suiveurs
- Messages privés
- Partage d'activités

### 2. **Personnalisation Avancée**
- Thèmes personnalisés
- Widgets configurables
- Badges et achievements

### 3. **Analytiques**
- Graphiques d'activité
- Tendances de participation
- Recommandations personnalisées

Le `ProfileController` offre une interface complète et modulaire pour la gestion des profils utilisateur, favorisant l'engagement et la personnalisation de l'expérience.
