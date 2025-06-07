# Dossier `/tests` - Tests et Qualité

Le dossier `/tests` contient la suite de tests automatisés pour assurer la qualité et la fiabilité du code.

## Structure

```
tests/
├── ExampleTest.php        # Exemple de test de base
└── [autres tests]         # Tests spécifiques par fonctionnalité
```

## Configuration PHPUnit

### `phpunit.xml`
Fichier de configuration principal de PHPUnit situé à la racine du projet.

**Configuration typique** :
- Répertoires de tests
- Bootstrap d'autoloading
- Configuration de la base de données de test
- Rapports de couverture de code

## Types de Tests

### Tests Unitaires
- Test des classes individuelles
- Isolation des dépendances
- Validation de la logique métier
- Tests des classes dans `/App/Api/`, `/App/Utils/`, etc.

### Tests d'Intégration
- Test des interactions entre composants
- Validation des flux complets
- Tests de l'API REST
- Tests des contrôleurs avec base de données

### Tests Fonctionnels
- Tests end-to-end des fonctionnalités
- Simulation des interactions utilisateur
- Validation des pages web complètes

## Exemple de Test

```php
<?php
// ExampleTest.php
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }
    
    public function testApiResponse()
    {
        // Test d'un endpoint API
        $api = new \App\Api\ActivitesApi();
        $response = $api->getAll();
        
        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
    }
}
```

## Tests API

### Endpoints Testing
- Validation des réponses JSON
- Test des codes de statut HTTP
- Validation des données de retour
- Tests d'authentification et d'autorisation

### Collection Postman
Le fichier `api_test_collection.postman_collection.json` complète les tests PHPUnit avec :
- Tests automatisés via Postman/Newman
- Validation des contrats API
- Tests de charge et de performance

## Couverture de Code

### Configuration
PHPUnit peut être configuré pour générer des rapports de couverture :
- Identification du code non testé
- Métriques de qualité
- Rapports HTML/XML

### Commandes
```bash
# Exécution des tests
vendor/bin/phpunit

# Tests avec couverture
vendor/bin/phpunit --coverage-html coverage/

# Tests spécifiques
vendor/bin/phpunit tests/ExampleTest.php
```

## Stratégie de Tests

### Classes API
Priority aux tests des classes dans `/App/Api/` :
- `ActivitesApi.php`
- `UtilisateursApi.php`
- `EvenementsApi.php`
- etc.

### Base de Données
- Tests avec base de données de test
- Transactions rollback après chaque test
- Fixtures de données de test

### Authentification
- Tests des tokens JWT
- Validation des sessions
- Tests des permissions et rôles

## CI/CD Integration

### Automatisation
- Exécution automatique lors des commits
- Intégration avec Git hooks
- Validation avant merge/déploiement

### Environnements
- Tests isolés par environnement
- Configuration spécifique aux tests
- Données de test séparées

## Bonnes Pratiques

### Organisation
- Un test par fonctionnalité
- Nommage explicite des méthodes
- Groupement logique des tests

### Données de Test
- Utilisation de factories/fixtures
- Données réalistes mais anonymisées
- Nettoyage après chaque test

### Assertions
- Tests spécifiques et précis
- Messages d'erreur clairs
- Validation complète des résultats
