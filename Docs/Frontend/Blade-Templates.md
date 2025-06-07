# Blade - Moteur de Templates

## Configuration dans Notre Projet

Notre projet utilise **Blade**, le moteur de templates de Laravel, via le package `illuminate/view`. Cette intégration nous permet d'utiliser la puissance de Blade dans une application PHP personnalisée.

### Installation et Configuration

#### Dépendances Composer
```json
{
    "require": {
        "illuminate/view": "^10.48",
        "illuminate/filesystem": "^10.48"
    }
}
```

#### Configuration dans BaseController
```php
// App/controllers/BaseController.php
use Illuminate\View\Factory;
use Illuminate\View\Compilers\BladeCompiler;

abstract class BaseController {
    protected $viewFactory;
    
    public function __construct() {
        $this->setupBladeEngine();
    }
    
    private function setupBladeEngine() {
        // Configuration du moteur Blade
        // Définition des chemins de templates et cache
    }
}
```

## Structure des Templates

### Organisation des Fichiers
```
App/templates/
├── layouts/                 # Templates de base
│   ├── app.blade.php       # Layout principal
│   ├── auth.blade.php      # Layout d'authentification
│   ├── base.blade.php      # Layout minimal
│   └── full.blade.php      # Layout pleine page
├── pages/                  # Pages complètes
│   ├── home.blade.php      # Page d'accueil
│   ├── activite.blade.php  # Page d'activité
│   └── profile.blade.php   # Page de profil
├── components/             # Composants réutilisables
│   ├── navigation/         # Composants de navigation
│   ├── auth/              # Composants d'authentification
│   ├── activite/          # Composants d'activités
│   └── breadcrumb.blade.php # Fil d'Ariane
└── cache/                  # Templates compilés (auto-générés)
```

## Fonctionnalités Blade Utilisées

### 1. **Héritage de Templates**

#### Layout Principal (`layouts/app.blade.php`)
```blade
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page_title ?? 'Mon Application' }}</title>
    <link rel="stylesheet" href="/assets/css/module.css">
</head>
<body>
    <header>
        @include('components.navigation.header')
    </header>
    
    <main>
        @yield('content')
    </main>
    
    <footer>
        @include('components.navigation.footer')
    </footer>
    
    @stack('scripts')
</body>
</html>
```

#### Page qui Hérite (`pages/activite.blade.php`)
```blade
@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold">{{ $activite->getNom() }}</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                @include('components.activite.details', ['activite' => $activite])
            </div>
            <div>
                @include('components.activite.sessions', ['sessions' => $sessions])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/assets/js/activite-detail.js"></script>
@endpush
```

### 2. **Composants Réutilisables**

#### Fil d'Ariane (`components/breadcrumb.blade.php`)
```blade
@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
    <nav class="breadcrumbs text-sm breadcrumbs">
        <ul>
            <li><a href="/">Accueil</a></li>
            @foreach($breadcrumbs as $breadcrumb)
                @if($loop->last)
                    <li>{{ $breadcrumb }}</li>
                @else
                    <li><a href="#">{{ $breadcrumb }}</a></li>
                @endif
            @endforeach
        </ul>
    </nav>
@endif
```

#### Composant d'Activité (`components/activite/card.blade.php`)
```blade
<div class="card w-full bg-base-100 shadow-xl">
    <figure>
        @if($activite->getImage())
            <img src="{{ $activite->getImage() }}" alt="{{ $activite->getNom() }}" />
        @endif
    </figure>
    
    <div class="card-body">
        <h2 class="card-title">
            {{ $activite->getNom() }}
            @if($activite->isNew())
                <div class="badge badge-secondary">Nouveau</div>
            @endif
        </h2>
        
        <p>{{ Str::limit($activite->getDescription(), 100) }}</p>
        
        <div class="card-actions justify-end">
            @if($activite->isActive())
                <button class="btn btn-primary">S'inscrire</button>
            @else
                <button class="btn btn-disabled">Complet</button>
            @endif
        </div>
    </div>
</div>
```

### 3. **Directives Conditionnelles et Boucles**

```blade
{{-- Vérification d'authentification --}}
@auth
    <p>Bienvenue, {{ Auth::user()->getName() }}!</p>
@else
    <a href="/login" class="btn btn-outline">Se connecter</a>
@endauth

{{-- Boucles avec données --}}
@foreach($activites as $activite)
    @include('components.activite.card', ['activite' => $activite])
    
    @if($loop->iteration % 3 == 0)
        <div class="w-full"></div> {{-- Retour à la ligne tous les 3 éléments --}}
    @endif
@endforeach

{{-- Gestion des listes vides --}}
@forelse($sessions as $session)
    <div class="session-item">{{ $session->getDate() }}</div>
@empty
    <p class="text-gray-500">Aucune session programmée</p>
@endforelse
```

### 4. **Échappement et Données Brutes**

```blade
{{-- Échappement automatique (sécurisé) --}}
<p>{{ $user->getBio() }}</p>

{{-- Affichage de HTML brut (attention aux failles XSS) --}}
<div>{!! $activite->getDescriptionHtml() !!}</div>

{{-- Valeurs par défaut --}}
<title>{{ $page_title ?? 'Titre par défaut' }}</title>
```

### 5. **Inclusion et Composition**

```blade
{{-- Inclusion simple --}}
@include('components.navigation.menu')

{{-- Inclusion avec données --}}
@include('components.user.profile', ['user' => $currentUser])

{{-- Inclusion conditionnelle --}}
@includeWhen($user->isAdmin(), 'components.admin.panel')

{{-- Inclusion avec fallback --}}
@includeFirst(['custom.header', 'components.navigation.header'])
```

## Intégration avec les Contrôleurs

### Rendu dans BaseController
```php
abstract class BaseController {
    protected function render(string $template, array $data = []): string {
        // Ajout de données globales
        $data['currentUser'] = $this->getCurrentUser();
        $data['breadcrumbs'] = $this->getBreadcrumbs();
        
        // Rendu du template Blade
        return $this->viewFactory->make($template, $data)->render();
    }
}
```

### Utilisation dans un Contrôleur
```php
class ActiviteController extends BaseController {
    public function index(int $id): string {
        $activite = ActiviteDisplay::createSafe($id);
        
        $data = [
            'page_title' => 'Activité - ' . $activite->getNom(),
            'activite' => $activite,
            'sessions' => $activite->getSessions(),
            'scripts' => ['activite-detail.js']
        ];
        
        return $this->render('pages.activite', $data);
    }
}
```

## Cache et Performance

### Compilation Automatique
- Templates Blade compilés automatiquement en PHP pur
- Stockage dans `/App/cache/` avec noms hashés
- Recompilation automatique si template modifié

### Exemple de Template Compilé
```php
// Template Blade original
{{ $user->getName() }}

// Devient après compilation
<?php echo e($user->getName()); ?>
```

## Avantages dans Notre Architecture

### 1. **Sécurité**
- Protection XSS automatique avec `{{ }}`
- Validation des données avant affichage
- Échappement contextuel

### 2. **Maintenabilité**
- Séparation claire logique/présentation
- Composants réutilisables
- Héritage de layouts

### 3. **Performance**
- Templates compilés en PHP natif
- Cache intelligent
- Optimisations automatiques

### 4. **DX (Developer Experience)**
- Syntaxe intuitive et expressive
- Debugging facilité
- Intégration IDE possible

Blade transforme l'écriture de templates en une expérience fluide et sécurisée, parfaitement adaptée à notre architecture PHP moderne !
