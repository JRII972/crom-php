# Contrôleurs - Architecture MVC

## Rôle des Contrôleurs

Dans l'architecture **MVC (Model-View-Controller)**, les contrôleurs servent d'intermédiaires entre les modèles (données) et les vues (templates). Ils orchestrent la logique métier et préparent les données pour l'affichage.

### Responsabilités des Contrôleurs

#### 1. **Traitement des Requêtes HTTP**
- Analyse des paramètres de requête
- Validation des données d'entrée
- Gestion des sessions utilisateur

#### 2. **Coordination Métier**
- Appel aux modèles pour récupérer/modifier les données
- Application de la logique métier
- Validation des permissions

#### 3. **Préparation des Vues**
- Formatage des données pour l'affichage
- Sélection du template approprié
- Injection des données dans les templates

#### 4. **Gestion des Réponses**
- Génération du HTML final
- Gestion des redirections
- Retour des codes de statut appropriés

## Architecture des Contrôleurs dans Notre Projet

### Hiérarchie des Classes
```
BaseController (abstract)
├── HomeController
├── AuthController
├── ActiviteController
├── ProfileController
└── ContactController
```

### Structure des Fichiers
```
App/controllers/
├── BaseController.php        # Contrôleur de base
├── HomeController.php        # Page d'accueil
├── AuthController.php        # Authentification
├── ActiviteController.php    # Gestion des activités
├── ProfileController.php     # Profil utilisateur
├── ContactController.php     # Page de contact
└── class/                    # Classes d'aide
    ├── SessionDisplay.php    # Affichage des sessions
    └── ActiviteDisplay.php   # Affichage des activités
```

## BaseController - Fondation

Le `BaseController` fournit les fonctionnalités communes à tous les contrôleurs :

### Fonctionnalités Principales

#### 1. **Configuration Blade**
```php
abstract class BaseController {
    protected $viewFactory;
    protected PDO $pdo;
    
    public function __construct() {
        $this->setupBladeEngine();
        $this->setupDatabase();
    }
    
    private function setupBladeEngine() {
        // Configuration du moteur Blade
        // Définition des chemins et du cache
    }
}
```

#### 2. **Méthode de Rendu**
```php
protected function render(string $template, array $data = []): string {
    // Ajout de données globales
    $data['currentUser'] = $this->getCurrentUser();
    $data['breadcrumbs'] = $this->getBreadcrumbs();
    
    return $this->viewFactory->make($template, $data)->render();
}
```

#### 3. **Gestion de l'Authentification**
```php
protected function getCurrentUser(): ?Utilisateur {
    // Récupération de l'utilisateur connecté
}

protected function requireAuth(): void {
    if (!$this->isAuthenticated()) {
        $this->redirect('/login');
    }
}
```

#### 4. **Navigation et Fil d'Ariane**
```php
private array $breadcrumbs = [];

protected function addBreadcrumb(string $label, string $url = ''): void {
    $this->breadcrumbs[] = ['label' => $label, 'url' => $url];
}

protected function getBreadcrumbs(): array {
    return $this->breadcrumbs;
}
```

#### 5. **Gestion des Erreurs**
```php
protected function handleError(\Exception $e, string $defaultMessage = 'Une erreur est survenue'): string {
    // Log de l'erreur
    error_log($e->getMessage());
    
    // Affichage d'une page d'erreur conviviale
    return $this->render('pages.error', [
        'message' => $defaultMessage,
        'debug' => $this->isDebugMode() ? $e->getMessage() : null
    ]);
}
```

## Flux Typique d'un Contrôleur

### 1. **Réception de la Requête**
```php
public function index(int $id): string {
    try {
        // Validation des paramètres
        if ($id <= 0) {
            throw new InvalidArgumentException('ID invalide');
        }
```

### 2. **Récupération des Données**
```php
        // Utilisation des classes Display pour récupérer les données
        $activite = ActiviteDisplay::createSafe($id);
        
        if ($activite === null) {
            $this->redirect('/');
            return '';
        }
```

### 3. **Préparation des Données pour la Vue**
```php
        $data = [
            'page_title' => 'Activité - ' . $activite->getNom(),
            'activite' => $activite,
            'jeu' => $activite->getJeu(),
            'sessions' => $activite->getSessions(),
            'scripts' => ['activite-detail.js']
        ];
```

### 4. **Gestion du Fil d'Ariane**
```php
        $this->addBreadcrumb('Activités', '/activites');
        $this->addBreadcrumb($activite->getNom());
```

### 5. **Rendu de la Vue**
```php
        return $this->render('pages.activite', $data);
    } catch (\Exception $e) {
        return $this->handleError($e, 'Impossible de charger l\'activité');
    }
}
```

## Avantages de cette Architecture

### 1. **Réutilisabilité**
- Fonctionnalités communes dans `BaseController`
- Classes Display réutilisables
- Composants Blade modulaires

### 2. **Maintenabilité**
- Séparation claire des responsabilités
- Code organisé et prévisible
- Gestion centralisée des erreurs

### 3. **Extensibilité**
- Ajout facile de nouveaux contrôleurs
- Héritage des fonctionnalités de base
- Surcharge possible des méthodes

### 4. **Testabilité**
- Logique isolée dans des méthodes spécifiques
- Dépendances injectables
- Mocking facilité pour les tests

## Bonnes Pratiques Appliquées

### 1. **Single Responsibility**
Chaque contrôleur gère une entité ou fonctionnalité spécifique.

### 2. **DRY (Don't Repeat Yourself)**
Code commun factorisé dans `BaseController`.

### 3. **Fail Fast**
Validation précoce des paramètres et redirection immédiate si nécessaire.

### 4. **Error Handling**
Gestion centralisée et uniforme des erreurs.

### 5. **Security First**
Vérification des permissions et validation des données.

Cette architecture de contrôleurs offre une base solide pour le développement d'une application web maintenable et évolutive !
