pip install mkdocs-single-pager-plugin# Programmation Orientée Objet (POO)

## Qu'est-ce que la Programmation Orientée Objet ?

La **Programmation Orientée Objet (POO)** est un paradigme de programmation qui organise le code autour d'objets plutôt que d'actions et de données plutôt que de logique. Un objet peut être défini comme une instance d'une classe qui encapsule des données (propriétés) et des comportements (méthodes).

### Concepts Fondamentaux

#### 1. **Classe**
Une classe est un modèle ou un plan pour créer des objets. Elle définit les propriétés et méthodes que posséderont les objets.

#### 2. **Objet**
Un objet est une instance d'une classe. C'est une entité concrète créée à partir du modèle défini par la classe.

#### 3. **Encapsulation**
L'encapsulation consiste à regrouper les données et les méthodes qui les manipulent dans une même entité (la classe) et à contrôler l'accès à ces données.

#### 4. **Héritage**
L'héritage permet à une classe de hériter des propriétés et méthodes d'une autre classe.

#### 5. **Polymorphisme**
Le polymorphisme permet à des objets de différentes classes d'être traités de manière uniforme.

## POO dans Notre Projet

Notre projet utilise intensivement la programmation orientée objet. Voici comment elle est mise en œuvre :

### Structure des Classes API

```php
// Exemple: ActivitesApi.php
class ActivitesApi extends APIHandler
{
    private $database;
    private $validator;
    
    public function __construct()
    {
        parent::__construct();
        $this->database = new DatabaseConnection();
        $this->validator = new DataValidator();
    }
    
    public function getAll(): array
    {
        // Logique pour récupérer toutes les activités
    }
    
    public function getById(int $id): array
    {
        // Logique pour récupérer une activité par ID
    }
    
    public function create(array $data): array
    {
        // Validation et création d'une nouvelle activité
    }
}
```

### Avantages dans Notre Architecture

#### 1. **Réutilisabilité**
Les classes peuvent être réutilisées dans différentes parties de l'application.

**Exemple** :
```php
// La classe UtilisateursApi peut être utilisée dans plusieurs contextes
$userApi = new UtilisateursApi();

// Dans un contrôleur web
$users = $userApi->getAll();

// Dans une tâche en ligne de commande
$activeUsers = $userApi->getActiveUsers();

// Dans un test unitaire
$testUser = $userApi->create($testData);
```

#### 2. **Maintenabilité**
Le code est organisé en unités logiques faciles à comprendre et à modifier.

**Exemple** :
```php
// Modification de la logique d'authentification
class SessionsApi extends APIHandler
{
    public function authenticate(string $email, string $password): array
    {
        // Toute la logique d'authentification est centralisée ici
        // Facile à modifier ou étendre
    }
    
    public function validateToken(string $token): bool
    {
        // Validation JWT centralisée
    }
}
```

#### 3. **Extensibilité**
Nouvelles fonctionnalités peuvent être ajoutées facilement grâce à l'héritage.

**Exemple** :
```php
// Classe de base pour toutes les API
abstract class APIHandler
{
    protected function validateRequest(): bool { /* ... */ }
    protected function formatResponse(array $data): array { /* ... */ }
    protected function handleError(Exception $e): array { /* ... */ }
}

// Spécialisation pour les jeux
class JeuxApi extends APIHandler
{
    public function getByGenre(int $genreId): array
    {
        // Méthode spécifique aux jeux
    }
    
    public function searchByName(string $name): array
    {
        // Recherche spécialisée pour les jeux
    }
}
```

#### 4. **Encapsulation des Données**
Les données sensibles sont protégées et accessible uniquement via des méthodes contrôlées.

**Exemple** :
```php
class Utilisateur
{
    private $password; // Propriété privée
    private $email;
    
    public function setPassword(string $password): void
    {
        // Validation et hachage avant stockage
        if (strlen($password) < 8) {
            throw new InvalidArgumentException("Mot de passe trop court");
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
    
    // Le mot de passe n'est jamais exposé directement
}
```

## Exemples Concrets dans le Projet

### 1. **Gestion des Activités**

```php
// App/Api/ActivitesApi.php
class ActivitesApi extends APIHandler
{
    private $activiteModel;
    
    public function create(array $data): array
    {
        // Validation des données
        $this->validateActiviteData($data);
        
        // Création de l'objet Activite
        $activite = new Activite($data);
        
        // Sauvegarde via le modèle
        $id = $this->activiteModel->save($activite);
        
        return $this->formatResponse([
            'id' => $id,
            'message' => 'Activité créée avec succès'
        ]);
    }
}
```

### 2. **Gestion des Erreurs**

```php
// App/Api/Exception.php
class APIException extends Exception
{
    private $httpCode;
    private $context;
    
    public function __construct(string $message, int $httpCode = 400, array $context = [])
    {
        parent::__construct($message);
        $this->httpCode = $httpCode;
        $this->context = $context;
    }
    
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
    
    public function getContext(): array
    {
        return $this->context;
    }
}
```

### 3. **Templates Blade avec Objets**

```blade
{{-- Utilisation d'objets dans les templates --}}
@foreach($activites as $activite)
    <div class="card">
        <h3>{{ $activite->getNom() }}</h3>
        <p>{{ $activite->getDescription() }}</p>
        <span class="badge">{{ $activite->getGenre()->getNom() }}</span>
        
        @if($activite->isActive())
            <button class="btn btn-primary">S'inscrire</button>
        @endif
    </div>
@endforeach
```

## Avantages de la POO pour ce Projet

### 1. **Organisation Claire**
- Chaque entité métier (Activité, Utilisateur, Jeu) a sa propre classe
- Responsabilités bien définies et séparées
- Code facile à naviguer et comprendre

### 2. **Facilité de Test**
```php
// Test unitaire d'une classe
class ActivitesApiTest extends PHPUnit\Framework\TestCase
{
    public function testCreateActivite()
    {
        $api = new ActivitesApi();
        $data = ['nom' => 'Test', 'description' => 'Test activity'];
        
        $result = $api->create($data);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('id', $result['data']);
    }
}
```

### 3. **Évolutivité**
- Ajout de nouvelles entités sans modifier l'existant
- Extension des fonctionnalités par héritage
- Modification des comportements par polymorphisme

### 4. **Réduction des Erreurs**
- Encapsulation prévient les accès incorrects aux données
- Typage fort réduit les erreurs de type
- Validation centralisée dans les méthodes appropriées

## Bonnes Pratiques Appliquées

### 1. **Single Responsibility Principle**
Chaque classe a une responsabilité unique et bien définie.

### 2. **Dependency Injection**
Les dépendances sont injectées plutôt que créées directement dans les classes.

### 3. **Interface Segregation**
Les interfaces sont spécifiques et ne forcent pas l'implémentation de méthodes inutiles.

### 4. **Open/Closed Principle**
Les classes sont ouvertes à l'extension mais fermées à la modification.

La programmation orientée objet dans ce projet permet de créer une application robuste, maintenable et évolutive, où chaque composant a sa place et son rôle bien définis.
