# Lieu.php

## Description
Classe représentant la table `lieux` qui gère les emplacements où peuvent se dérouler les sessions de jeu.

## Propriétés
- **`nom`** : Nom complet du lieu (obligatoire)
- **`short_nom`** : Nom abrégé (5 caractères max, généré automatiquement si non fourni)
- **`adresse`** : Adresse postale (optionnel)
- **`latitude`** : Coordonnée GPS latitude (optionnel)
- **`longitude`** : Coordonnée GPS longitude (optionnel)
- **`description`** : Description ou remarques (optionnel)

## Constructeur
```php
public function __construct(
    ?int $id = null,
    ?string $nom = null,
    ?string $short_nom = null,
    ?string $adresse = null,
    ?float $latitude = null,
    ?float $longitude = null,
    ?string $description = null
)
```

### Modes d'utilisation
1. **Chargement depuis base** : `new Lieu($id)` - Charge un lieu existant
2. **Création nouveau** : `new Lieu(null, $nom, ...)` - Prépare un nouveau lieu

## Méthodes principales

### Recherche et récupération
- **`getById(int $id)`** : Récupère un lieu par son ID
- **`getByNom(string $nom)`** : Récupère un lieu par son nom
- **`getAllLieux()`** : Liste tous les lieux disponibles
- **`searchLieux(array $criteres)`** : Recherche avec filtres

### CRUD
- **`save()`** : Sauvegarde en base (insertion ou mise à jour)
- **`delete()`** : Suppression du lieu
- **`exists()`** : Vérifie l'existence du lieu

### Accesseurs et mutateurs
- **`getNom()`**, **`setNom(string $nom)`** : Nom du lieu
- **`getShortNom()`**, **`setShortNom(string $short_nom)`** : Nom abrégé
- **`getAdresse()`**, **`setAdresse(?string $adresse)`** : Adresse
- **`getLatitude()`**, **`setLatitude(?float $latitude)`** : Coordonnée latitude
- **`getLongitude()`**, **`setLongitude(?float $longitude)`** : Coordonnée longitude
- **`getDescription()`**, **`setDescription(?string $description)`** : Description

## Fonctionnalités spéciales

### Génération automatique du nom court
Si `short_nom` n'est pas fourni, il est généré automatiquement à partir du nom complet :
- Prise des premières lettres
- Suppression des espaces et caractères spéciaux
- Limitation à 5 caractères maximum

### Gestion des coordonnées GPS
- **Validation** : Contrôle des plages latitude (-90 à 90) et longitude (-180 à 180)
- **Précision** : Stockage avec décimales pour localisation précise
- **Optionnel** : Peut être null si coordonnées inconnues

### Recherche géographique
Possibilité de recherche par proximité géographique si coordonnées disponibles.

## Validations
- **Nom obligatoire** : Doit être fourni pour créer un lieu
- **Nom unique** : Vérifié en base de données
- **Short_nom unique** : Également vérifié pour éviter les doublons
- **Coordonnées valides** : Contrôle des plages géographiques

## Relations base de données
- **Sessions** : Relation 1:N (un lieu peut accueillir plusieurs sessions)
- **Horaires** : Relation 1:N via table `horaires_lieu`
- **Événements** : Relation 1:N (un lieu peut accueillir plusieurs événements)

## Utilisation dans le projet
- **Planification** : Sélection du lieu lors de la création de sessions
- **Cartographie** : Affichage sur carte si coordonnées GPS disponibles
- **Disponibilités** : Gestion des horaires d'ouverture via `HorairesLieu`
- **Administration** : Gestion de la liste des lieux disponibles

## Exemple d'utilisation
```php
// Création d'un nouveau lieu
$lieu = new Lieu(
    null,
    'Local associatif central',
    'LAC',
    '123 rue de la Paix, 75001 Paris',
    48.8566,
    2.3522,
    'Local principal avec 3 salles'
);
$lieu->save();

// Chargement d'un lieu existant
$lieu = new Lieu(1);
echo $lieu->getNom(); // Affiche le nom
echo $lieu->getShortNom(); // Affiche l'abréviation
```
