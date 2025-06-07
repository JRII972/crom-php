# Genre.php

## Description
Classe représentant la table `genres` qui gère les catégories utilisées pour classifier les jeux (Fantastique, Horreur, Science-fiction, etc.).

## Propriétés
- **`nom`** : Nom du genre (obligatoire, unique)

## Constructeur
```php
public function __construct(?int $id = null, ?string $nom = null)
```

### Modes d'utilisation
1. **Chargement depuis base** : `new Genre($id)` - Charge un genre existant
2. **Création nouveau** : `new Genre(null, $nom)` - Prépare un nouveau genre

## Méthodes principales

### Recherche et récupération
- **`getById(int $id)`** : Récupère un genre par son ID
- **`getByNom(string $nom)`** : Récupère un genre par son nom
- **`getAllGenres()`** : Liste tous les genres disponibles

### CRUD
- **`save()`** : Sauvegarde en base (insertion si nouveau, update si existant)
- **`delete()`** : Suppression du genre
- **`exists()`** : Vérifie si le genre existe en base

### Accesseurs
- **`getNom()`** : Retourne le nom du genre
- **`setNom(string $nom)`** : Modifie le nom du genre

## Validations
- **Nom obligatoire** : Doit être fourni pour créer un genre
- **Nom unique** : Vérifié en base de données lors de la sauvegarde
- **Nom non vide** : Contrôle de la chaîne fournie

## Fonctionnalités

### Chargement depuis base
```php
$genre = new Genre(1); // Charge le genre avec ID=1
echo $genre->getNom(); // Affiche le nom
```

### Création nouveau genre
```php
$genre = new Genre(null, 'Steampunk');
$genre->save(); // Insertion en base
```

### Gestion des associations
Les genres sont liés aux jeux via la table de liaison `jeux_genres` (relation N:N).

## Données prédéfinies
La base contient des genres de base :
- **Fantastique**
- **Horreur** 
- **Exploration**
- **Science-fiction**
- **Historique**

## Relations base de données
- **Jeux** : Relation N:N via table `jeux_genres`
- Un genre peut être associé à plusieurs jeux
- Un jeu peut avoir plusieurs genres

## Utilisation dans le projet
- **Filtrage** : Recherche de jeux par genre
- **Classification** : Organisation du catalogue de jeux
- **Interface** : Sélection de genres lors de la création/modification de jeux
- **Administration** : Gestion de la liste des genres disponibles

## Exemple d'utilisation
```php
// Récupération tous les genres
$genres = Genre::getAllGenres();

// Recherche par nom
$fantastique = Genre::getByNom('Fantastique');

// Création nouveau genre
$nouveau = new Genre(null, 'Cyberpunk');
$nouveau->save();
```
