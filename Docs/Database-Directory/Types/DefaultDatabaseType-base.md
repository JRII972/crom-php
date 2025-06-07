# DefaultDatabaseType

## Description

La classe `DefaultDatabaseType` est la classe de base abstraite pour tous les types d'entités de la base de données dans l'application. Elle fournit les fonctionnalités communes à toutes les classes qui représentent des tables de base de données, établissant un pattern ORM simplifié.

## Fonctionnalités principales

### Infrastructure de base
- **Connexion PDO** : Gestion automatique de la connexion à la base de données
- **Sérialisation JSON** : Implémentation de `JsonSerializable` pour toutes les entités
- **Opérations CRUD** : Méthodes communes pour les opérations de base de données
- **Validation** : Framework de validation via les setters

### Pattern ORM simplifié
- Chaque propriété correspond à un champ de table
- Utilisation de setters pour la validation des données
- Méthodes standardisées pour les opérations de base

## Structure de la classe

### Propriétés protégées
```php
protected PDO $pdo;           // Connexion à la base de données
protected int|string $id;     // Identifiant de l'entité
protected string $table;      // Nom de la table associée
```

### Implémentation d'interfaces
- **`JsonSerializable`** : Permet la sérialisation automatique en JSON

## Méthodes principales

### Constructeur
```php
public function __construct()
```
- Initialise automatiquement la connexion PDO via `Database::getConnection()`
- Doit être appelé par toutes les classes filles

### Mise à jour en lot
```php
public function update(array $data): self
```
- Met à jour plusieurs champs à la fois à partir d'un tableau associatif
- Utilise les setters existants pour la validation
- Ignore les valeurs nulles
- Nettoie automatiquement les chaînes (trim)
- Lève une exception si tentative de modification d'un champ protégé

#### Exemple d'utilisation
```php
$entite->update([
    'nom' => 'Nouveau nom',
    'email' => 'nouveau@email.com',
    'statut' => 'actif'
]);
```

### Sauvegarde abstraite
```php
public function save(): void
```
- Méthode abstraite devant être implémentée par chaque classe fille
- Gère l'insertion ou la mise à jour selon l'existence de l'entité

### Suppression générique
```php
public function delete(): bool
```
- Supprime l'entité de la base de données
- Utilise l'ID et le nom de table définis dans la classe
- Validation automatique de la présence de l'ID et du nom de table
- Limite la suppression à un seul enregistrement

### Formatage des champs
```php
protected function formatFieldForQuery($value)
```
- Formate automatiquement les valeurs pour les requêtes SQL
- Gère les types spéciaux :
  - `DateTime` → format 'Y-m-d H:i:s'
  - Enums (`Sexe`, `TypeUtilisateur`) → valeur de l'enum
  - `Image` → chemin du fichier
  - `bool` → conversion en entier (0/1)

### Sérialisation JSON
```php
public function jsonSerialize(): mixed
```
- Retourne toutes les propriétés de l'objet via `get_object_vars()`
- Permet la sérialisation automatique avec `json_encode()`

## Utilisation dans les classes filles

### Héritage standard
```php
class MonEntite extends DefaultDatabaseType
{
    private string $nom;
    private string $email;
    
    public function __construct($id = null, $nom = null, $email = null)
    {
        parent::__construct();  // Initialise PDO
        $this->table = 'mon_entite';
        
        if ($id !== null && $nom === null && $email === null) {
            $this->loadFromDatabase($id);
        } elseif ($id === null && $nom !== null && $email !== null) {
            $this->setId(generateUUID());
            $this->setNom($nom);
            $this->setEmail($email);
        }
    }
    
    public function save(): void
    {
        // Implémentation spécifique de la sauvegarde
    }
}
```

### Avantages du pattern
1. **Consistance** : Toutes les entités partagent la même interface de base
2. **Réutilisation** : Code commun centralisé dans la classe de base
3. **Maintenance** : Modifications globales possibles via la classe de base
4. **Validation** : Framework de validation uniforme

## Gestion des erreurs

### Exceptions levées
- **`RuntimeException`** : Pour les erreurs de configuration (table manquante, ID manquant)
- **`InvalidArgumentException`** : Pour les tentatives de modification de champs protégés
- **`PDOException`** : Propagées depuis les opérations de base de données

### Validation dans update()
- Vérification de l'existence des setters avant utilisation
- Protection contre la modification directe des propriétés
- Gestion gracieuse des valeurs nulles

## Notes techniques

### Connection PDO
- Utilise le singleton `Database::getConnection()`
- Connexion partagée entre toutes les instances
- Gestion automatique de la configuration

### Performance
- Connexion unique réutilisée
- Lazy loading possible dans les classes filles
- Méthodes optimisées pour les opérations courantes

### Extensibilité
- Classe ouverte à l'extension via l'héritage
- Méthodes protégées disponibles pour les classes filles
- Pattern permettant l'ajout de nouvelles fonctionnalités

## Intégration avec l'application

### Classes filles existantes
Toutes les entités de l'application héritent de cette classe :
- `Utilisateur`
- `Activite`
- `Session`
- `Lieu`
- `Evenement`
- `Jeu`
- `Genre`
- Et toutes les autres entités du système

### Pattern de conception
- **Active Record** : Chaque instance représente un enregistrement
- **Data Mapper** : Séparation entre l'objet et la persistence
- **Template Method** : Méthodes communes avec implémentation spécifique
