# Jeu.php

## Description
Classe représentant la table `jeux` qui gère le catalogue des jeux disponibles pour les activités (jeux de rôle, jeux de société, autres).

## Énumération TypeJeu
```php
enum TypeJeu: string
{
    case JDR = 'JDR';
    case JeuDeSociete = 'JEU_DE_SOCIETE';
    case Autre = 'AUTRE';
}
```

## Propriétés principales
- **`nom`** : Nom du jeu (obligatoire, unique)
- **`description`** : Description optionnelle
- **`typeJeu`** : Type via énumération TypeJeu
- **`image`** : Image principale (objet Image)
- **`icon`** : Icône du jeu (objet Image)

## Configuration cache
- **Activé** : Par défaut désactivé (`$cacheEnabled = false`)
- **TTL** : 5 minutes (300 secondes)
- **Préfixe** : `'jeu_search_'`

## Constructeur
```php
public function __construct(
    ?int $id = null,
    ?string $nom = null,
    ?string $description = null,
    Image|string|array|null $image = null,
    Image|string|array|null $icon = null,
    ?TypeJeu $typeJeu = null
)
```

### Modes d'utilisation
1. **Chargement depuis base** : `new Jeu($id)` - Charge les données existantes
2. **Création nouveau** : `new Jeu(null, $nom, ...)` - Prépare un nouveau jeu

## Méthodes principales

### Recherche et récupération
- **`getById(int $id)`** : Récupère un jeu par son ID
- **`getByNom(string $nom)`** : Récupère un jeu par son nom
- **`getAllJeux()`** : Liste tous les jeux
- **`searchJeux(array $criteres)`** : Recherche avec filtres (nom, type, genre)

### CRUD
- **`save()`** : Sauvegarde en base (insertion ou mise à jour)
- **`delete()`** : Suppression du jeu
- **`exists()`** : Vérifie l'existence du jeu

### Gestion des genres
- **`addGenre(Genre $genre)`** : Associe un genre au jeu
- **`removeGenre(Genre $genre)`** : Dissocie un genre
- **`getGenres()`** : Récupère les genres associés
- **`hasGenre(Genre $genre)`** : Vérifie l'association avec un genre

### Gestion des images
- **`setImage(Image|string|array $image)`** : Définit l'image principale
- **`setIcon(Image|string|array $icon)`** : Définit l'icône
- **Formats supportés** : Objet Image, chaîne URL, ou tableau de métadonnées

## Validations
- **Nom obligatoire** : Doit être fourni pour les nouveaux jeux
- **Nom unique** : Vérifié en base de données
- **Type valide** : Doit correspondre à l'énumération TypeJeu
- **Images valides** : Format et taille contrôlés via classe Image

## Fonctionnalités avancées

### Recherche avec critères
```php
$jeux = Jeu::searchJeux([
    'nom' => 'Donjons',
    'type' => TypeJeu::JDR,
    'genre' => 'Fantastique'
]);
```

### Association avec activités
Les jeux sont référencés par les activités via clé étrangère `id_jeu`.

### Cache intelligent
- Mise en cache des recherches fréquentes
- Invalidation automatique lors des modifications
- Configuration flexible par instance

## Relations base de données
- **Genres** : Relation N:N via table `jeux_genres`
- **Activités** : Relation 1:N (un jeu peut avoir plusieurs activités)

## Utilisation dans le projet
- **Catalogue** : Affichage des jeux disponibles
- **Création d'activités** : Sélection du jeu associé
- **Filtres** : Recherche par type ou genre
- **Administration** : Gestion du catalogue
