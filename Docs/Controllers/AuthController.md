# AuthController - Contrôleur d'Authentification

Le `AuthController` gère l'ensemble du processus d'authentification de l'application, incluant la connexion, la déconnexion et la gestion des sessions utilisateur.

## Responsabilités

### 1. **Affichage de la Page de Connexion**
- Présentation du formulaire de connexion
- Gestion des redirections après authentification
- Vérification du statut de connexion existant

### 2. **Processus d'Authentification**
- Validation des identifiants
- Création des sessions utilisateur
- Gestion des tokens de sécurité

### 3. **Sécurité et Sessions**
- Protection contre les accès non autorisés
- Gestion des redirections sécurisées
- Nettoyage des sessions

## Méthodes Principales

### `index(): string`
Affiche la page de connexion avec gestion intelligente des redirections.

```php
public function index(): string {
    // Vérification si l'utilisateur est déjà connecté
    if (isset($_SESSION['user_id'])) {
        // Gestion de la redirection après connexion
        if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
            header('Location: ' . $_GET['redirect']);
        } else {
            header('Location: /');
        }
        exit;
    }
    
    // Affichage du formulaire de connexion
    return $this->render('pages.login-modular', [
        'pageTitle' => 'Connexion'
    ]);
}
```

## Logique de Redirection

### Redirection Intelligente
Le contrôleur gère les redirections post-connexion de manière sécurisée :

```php
// URL de redirection passée en paramètre
if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
    $redirectUrl = $_GET['redirect'];
    
    // Validation de l'URL pour éviter les redirections malveillantes
    if ($this->isValidRedirectUrl($redirectUrl)) {
        header('Location: ' . $redirectUrl);
    } else {
        header('Location: /');
    }
} else {
    // Redirection par défaut vers l'accueil
    header('Location: /');
}
```

### Cas d'Usage de Redirection
1. **Accès à une page protégée** : `/login?redirect=/profile`
2. **Après déconnexion** : `/login?redirect=/`
3. **Connexion directe** : `/login` (redirection vers accueil)

## Gestion de Session

### Vérification du Statut
```php
if (isset($_SESSION['user_id'])) {
    // Utilisateur déjà authentifié
    // Redirection immédiate
}
```

**Avantages** :
- Évite l'affichage inutile du formulaire
- Améliore l'expérience utilisateur
- Optimise les performances

### Données de Session
```php
// Structure typique de session après authentification
$_SESSION = [
    'user_id' => 123,
    'username' => 'john_doe',
    'role' => 'user',
    'last_activity' => time(),
    'csrf_token' => 'abc123...'
];
```

## Template Associé

### `pages.login-modular`
Template modulaire pour l'authentification :

```blade
@extends('layouts.auth')

@section('title', $pageTitle)

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="card w-96 bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title justify-center">{{ $pageTitle }}</h2>
            
            {{-- Formulaire de connexion --}}
            <form method="POST" action="/auth/login" class="space-y-4">
                @csrf
                
                {{-- Champ email --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" name="email" class="input input-bordered" required />
                </div>
                
                {{-- Champ mot de passe --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Mot de passe</span>
                    </label>
                    <input type="password" name="password" class="input input-bordered" required />
                </div>
                
                {{-- Bouton de connexion --}}
                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </div>
            </form>
            
            {{-- Liens utiles --}}
            <div class="text-center mt-4">
                <a href="/forgot-password" class="link link-primary">Mot de passe oublié ?</a>
            </div>
        </div>
    </div>
</div>
@endsection
```

## Sécurité Implementée

### 1. **Protection CSRF**
```php
// Génération et validation de tokens CSRF
if (!$this->validateCSRFToken($_POST['csrf_token'])) {
    throw new SecurityException('Token CSRF invalide');
}
```

### 2. **Validation des URLs de Redirection**
```php
private function isValidRedirectUrl(string $url): bool {
    // Vérification que l'URL appartient au domaine
    $parsedUrl = parse_url($url);
    
    // Rejeter les URLs externes
    if (isset($parsedUrl['host']) && $parsedUrl['host'] !== $_SERVER['HTTP_HOST']) {
        return false;
    }
    
    // Autoriser seulement les URLs relatives
    return strpos($url, '/') === 0 && strpos($url, '//') !== 0;
}
```

### 3. **Limitation des Tentatives**
```php
// Protection contre les attaques par force brute
if ($this->tooManyLoginAttempts($email)) {
    return $this->render('pages.login-modular', [
        'error' => 'Trop de tentatives. Réessayez dans 15 minutes.',
        'pageTitle' => 'Connexion'
    ]);
}
```

## Méthodes Étendues (non visibles dans l'extrait)

### `login(): string`
Traite la soumission du formulaire de connexion.

```php
public function login(): string {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $this->index();
    }
    
    // Validation des données
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (!$email || empty($password)) {
        return $this->render('pages.login-modular', [
            'error' => 'Email ou mot de passe invalide',
            'pageTitle' => 'Connexion'
        ]);
    }
    
    // Authentification
    $user = $this->authenticateUser($email, $password);
    
    if ($user) {
        $this->createUserSession($user);
        return $this->redirectAfterLogin();
    } else {
        return $this->render('pages.login-modular', [
            'error' => 'Identifiants incorrects',
            'pageTitle' => 'Connexion'
        ]);
    }
}
```

### `logout(): void`
Gère la déconnexion de l'utilisateur.

```php
public function logout(): void {
    // Destruction de la session
    session_destroy();
    
    // Redirection vers la page de connexion
    header('Location: /login');
    exit;
}
```

## Flow d'Authentification

### 1. **Accès Initial**
```
Utilisateur → /login → AuthController::index()
```

### 2. **Soumission du Formulaire**
```
POST /auth/login → AuthController::login() → Validation → Session → Redirection
```

### 3. **Accès Protégé**
```
Page protégée → Middleware → Redirection /login?redirect=/page-protegee
```

## Évolutions Possibles

### 1. **Authentification Étendue**
- Connexion via réseaux sociaux (OAuth)
- Authentification à deux facteurs (2FA)
- Connexion par token d'API

### 2. **Sécurité Renforcée**
- Captcha après plusieurs tentatives
- Notification de connexion par email
- Historique des connexions

### 3. **Expérience Utilisateur**
- Connexion persistante ("Se souvenir de moi")
- Connexion par nom d'utilisateur ou email
- Récupération de mot de passe

Le `AuthController` constitue la pierre angulaire de la sécurité de l'application, gérant l'authentification de manière robuste et sécurisée.
