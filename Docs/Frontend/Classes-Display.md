# Classes Display - Extension pour l'Affichage

## Concept et Philosophie

Les **classes Display** sont des extensions spécialisées des classes de base de données qui ajoutent des fonctionnalités dédiées à l'affichage et au rendu graphique. Elles respectent le principe de **séparation des responsabilités** en évitant de surcharger les classes métier avec du code de présentation.

### Principe de Conception

#### Séparation des Responsabilités
```
Classes de Base (Database/Types/)     Classes Display (Controllers/Class/)
├── Logique métier                    ├── Logique d'affichage
├── Validation des données            ├── Formatage pour l'interface
├── Persistance en base               ├── Gestion des images
└── Règles business                   └── Méthodes d'aide au rendu
```

#### Avantages de cette Architecture

1. **Évite la Surcharge** : Les classes métier restent focalisées sur leur responsabilité principale
2. **Flexibilité** : Différentes représentations visuelles sans modifier le modèle de données
3. **Réutilisabilité** : Classes de base utilisables dans d'autres contextes (API, CLI, etc.)
4. **Maintenabilité** : Modifications d'affichage isolées des règles métier
5. **Testabilité** : Tests séparés pour la logique métier et l'affichage

## Classes Display Disponibles

### Structure du Namespace
```
App\Controllers\Class\
├── ActiviteDisplay    # Extension de Activite
├── SessionDisplay     # Extension de Session
├── JeuDisplay         # Extension de Jeu
└── UtilisateurDisplay # Extension de Utilisateur
```

## ActiviteDisplay - Gestion d'Affichage des Activités

### Fonctionnalités Ajoutées

#### 1. **Gestion d'Image avec Fallback**
```php
class ActiviteDisplay extends Activite {
    private ?Image $displayImage = null;
    
    public function __construct(...) {
        parent::__construct(...);
        
        // Logique de fallback pour l'image
        $activiteImage = parent::getImage();
        if ($activiteImage !== null) {
            $this->displayImage = $activiteImage;
        } else {
            // Fallback vers l'image du jeu
            $jeu = $this->getJeu();
            if ($jeu && $jeu->getImage()) {
                $this->displayImage = $jeu->getImage();
            }
        }
    }
}
```

**Avantages** :
- Affichage cohérent même sans image spécifique
- Réutilisation intelligente des assets existants
- Amélioration de l'expérience utilisateur

#### 2. **Création Sécurisée**
```php
public static function createSafe(int $id): ?ActiviteDisplay {
    try {
        return new self($id);
    } catch (PDOException $e) {
        return null;
    }
}
```

**Utilisation** :
```php
// Au lieu de gérer les exceptions partout
$activite = ActiviteDisplay::createSafe($id);
if ($activite === null) {
    // Gestion de l'erreur
    return $this->render('pages.error');
}

// Utilisation normale
echo $activite->getNom();
```

#### 3. **Vérification d'Existence**
```php
public static function exists(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM activite WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() > 0;
}
```

## SessionDisplay - Sessions Enrichies

### Fonctionnalités Spécialisées

#### 1. **Image d'Affichage Intelligente**
```php
class SessionDisplay extends Session {
    private Image $displayImage;
    private JeuDisplay $jeuDisplay;
    
    // Logique pour déterminer la meilleure image à afficher
    // (session > activité > jeu > image par défaut)
}
```

#### 2. **Intégration avec JeuDisplay**
```php
private JeuDisplay $jeuDisplay;

// Accès aux fonctionnalités d'affichage enrichies du jeu
public function getJeuForDisplay(): JeuDisplay {
    return $this->jeuDisplay;
}
```

## Exemples d'Utilisation dans les Contrôleurs

### Dans ActiviteController
```php
class ActiviteController extends BaseController {
    public function index(int $id): string {
        // Utilisation de la classe Display
        $activite = ActiviteDisplay::createSafe($id);
        
        if ($activite === null) {
            header('Location: /');
            exit;
        }
        
        $data = [
            'activite' => $activite,
            'displayImage' => $activite->getDisplayImage(), // Méthode spécifique Display
            'formattedDescription' => $activite->getFormattedDescription(), // Formatage pour HTML
        ];
        
        return $this->render('pages.activite', $data);
    }
}
```

### Dans les Templates Blade
```blade
@foreach($activites as $activite)
    <div class="card">
        {{-- Utilisation de l'image avec fallback --}}
        <img src="{{ $activite->getDisplayImage()->getUrl() }}" 
             alt="{{ $activite->getNom() }}" />
        
        <h3>{{ $activite->getNom() }}</h3>
        
        {{-- Méthodes d'affichage enrichies --}}
        <p>{{ $activite->getShortDescription() }}</p>
        <span class="badge">{{ $activite->getFormattedStatus() }}</span>
    </div>
@endforeach
```

## Avantages par Rapport aux Classes de Base

### Classe de Base (Activite)
```php
class Activite {
    // Logique métier pure
    public function getNom(): string { /* ... */ }
    public function getDescription(): string { /* ... */ }
    public function isActive(): bool { /* ... */ }
    
    // Pas de logique d'affichage
}
```

### Classe Display (ActiviteDisplay)
```php
class ActiviteDisplay extends Activite {
    // Hérite de toute la logique métier
    
    // PLUS : Méthodes d'affichage
    public function getDisplayImage(): Image { /* fallback intelligent */ }
    public function getShortDescription(int $length = 100): string { /* troncature */ }
    public function getFormattedStatus(): string { /* badge coloré */ }
    public function getBackgroundClass(): string { /* classe CSS */ }
}
```

## Pattern d'Utilisation Recommandé

### 1. **Dans les API** (Classes de Base)
```php
// API REST - Données pures
class ActivitesApi {
    public function getAll(): array {
        return Activite::findAll(); // Classe de base
    }
}
```

### 2. **Dans les Contrôleurs Web** (Classes Display)
```php
// Contrôleurs web - Données enrichies pour l'affichage
class ActiviteController {
    public function index(): string {
        return ActiviteDisplay::createSafe($id); // Classe Display
    }
}
```

### 3. **Dans les Scripts CLI** (Classes de Base)
```php
// Scripts en ligne de commande - Logique métier pure
$activites = Activite::findExpired();
foreach ($activites as $activite) {
    $activite->archive(); // Pas besoin de logique d'affichage
}
```

## Extensibilité Future

### Nouvelles Classes Display
```php
// Exemple d'extension future
class ActiviteDisplayMobile extends ActiviteDisplay {
    // Spécialisations pour mobile
    public function getCompactView(): array { /* ... */ }
    public function getThumbnailImage(): Image { /* ... */ }
}

class ActiviteDisplayAdmin extends ActiviteDisplay {
    // Spécialisations pour admin
    public function getDetailedStats(): array { /* ... */ }
    public function getManagementActions(): array { /* ... */ }
}
```

### Interfaces Spécialisées
```php
interface DisplayableInterface {
    public function getDisplayImage(): Image;
    public function getFormattedTitle(): string;
    public function getCSSClasses(): array;
}

class ActiviteDisplay extends Activite implements DisplayableInterface {
    // Implémentation garantie des méthodes d'affichage
}
```

Cette architecture garantit une séparation claire entre la logique métier et la logique de présentation, tout en offrant une grande flexibilité pour l'évolution future de l'interface utilisateur.
