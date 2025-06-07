# Activite

## Description

La classe `Activite` représente une activité ludique dans la base de données. Elle constitue l'entité centrale du système de gestion d'activités, pouvant être une campagne de jeu de rôle, un oneshot, un jeu de société ou un événement. Chaque activité est associée à un jeu, dirigée par un maître de jeu, et peut accueillir plusieurs sessions avec des participants.

## Fonctionnalités principales

### Types d'activités supportés
- **Campagne** : Activité récurrente avec sessions multiples (ouverte ou fermée)
- **Oneshot** : Activité ponctuelle, une seule session
- **Jeu de société** : Activité autour de jeux de plateau
- **Événement** : Activité spéciale ou événementielle

### Gestion des participants
- Limitation du nombre maximum de joueurs par activité
- Limitation du nombre de joueurs par session
- Système de verrouillage pour contrôler les inscriptions
- Support des campagnes fermées avec whitelist

### Système de cache
- Cache APCu pour optimiser les recherches fréquentes
- Invalidation automatique lors des modifications
- Configuration TTL ajustable (5 minutes par défaut)

## Énumérations associées

### TypeActivite
```php
enum TypeActivite: string
{
    case Campagne = 'CAMPAGNE';
    case Oneshot = 'ONESHOT';
    case JeuDeSociete = 'JEU_DE_SOCIETE';
    case Evenement = 'EVENEMENT';
}
```

### TypeCampagne
```php
enum TypeCampagne: string
{
    case Ouverte = 'OUVERTE';    // Inscription libre
    case Fermee = 'FERMEE';      // Inscription sur whitelist
}
```

### EtatActivite
```php
enum EtatActivite: string
{
    case Active = 'ACTIVE';      // Activité en cours
    case Fermer = 'FERMER';      // Fermée aux inscriptions
    case Terminer = 'TERMINER';  // Activité terminée
    case Annuler = 'ANNULER';    // Activité annulée
    case Supprimer = 'SUPPRIMER'; // Marquée pour suppression
}
```

## Structure de la classe

### Propriétés principales
```php
protected ?int $idJeu;                      // ID du jeu associé
private ?string $nom;                       // Nom de l'activité
private ?Jeu $jeu = null;                   // Objet jeu (lazy loading)
protected ?string $idMaitreJeu;             // ID du maître de jeu
private ?Utilisateur $maitreJeu = null;     // Objet utilisateur (lazy loading)
private EtatActivite $etat;                 // État de l'activité
private TypeActivite $typeActivite;         // Type d'activité
private ?TypeCampagne $typeCampagne = null; // Type de campagne (si applicable)
private ?string $descriptionCourte = null;  // Description courte
private ?string $description = null;        // Description complète
private int $nombreMaxJoueurs = 0;          // Limite max de joueurs
private int $maxJoueursSession = 5;         // Limite par session
private bool $verrouille = false;           // Verrouillage des inscriptions
private ?Image $image = null;               // Image de l'activité
private ?string $texteAltImage = null;      // Texte alternatif
private string $dateCreation;               // Date de création
```

### Configuration du cache
```php
private static $cacheEnabled = false;      // Activation du cache
private static $cacheTTL = 300;            // TTL en secondes (5 min)
private static $cachePrefix = 'activite_search_'; // Préfixe des clés
```

## Constructeur

### Mode chargement depuis la base
```php
new Activite($id)
```

### Mode création d'une nouvelle activité
```php
new Activite(
    null,                    // id
    $nom,                    // Nom (requis)
    $jeuOuId,               // Jeu ou ID (requis)
    $maitreJeuOuId,         // Maître de jeu ou ID (requis)
    $typeActivite,          // Type d'activité (requis)
    $typeCampagne,          // Type de campagne (optionnel)
    $descriptionCourte,     // Description courte (optionnel)
    $description,           // Description complète (optionnel)
    $nombreMaxJoueurs,      // Nombre max joueurs (optionnel)
    $maxJoueursSession,     // Max par session (optionnel)
    $image,                 // Image (optionnel)
    $texteAltImage,         // Texte alternatif (optionnel)
    $dateCreation           // Date de création (optionnel)
)
```

## Méthodes principales

### Opérations CRUD
- **`save()`** : Sauvegarde (insertion ou mise à jour) l'activité
- **`delete()`** : Supprime l'activité et invalide le cache
- **`loadFromDatabase($id)`** : Charge une activité existante

### Recherche avancée
```php
public static function search(
    PDO $pdo, 
    string $keyword = '',           // Mot-clé dans nom/description
    int $idJeu = 0,                // Filtre par jeu
    string $idMaitreJeu = '',      // Filtre par maître de jeu
    string $typeActivite = '',     // Filtre par type d'activité
    ?array $categories = null,     // Filtre par genres/catégories
    ?array $jours = null           // Filtre par jours des sessions
): array
```

### Getters avec lazy loading
- `getId()`, `getNom()`, `getTypeActivite()`, `getTypeCampagne()`
- `getJeu()` : Charge automatiquement l'objet Jeu
- `getMaitreJeu()` : Charge automatiquement l'objet Utilisateur
- `getEtat()`, `getDescriptionCourte()`, `getDescription()`
- `getNombreMaxJoueurs()`, `getMaxJoueursSession()`, `getVerrouille()`
- `getImage()` : Avec validation automatique de l'image
- `getTexteAltImage()`, `getDateCreation()`

### Setters avec validation
- `setNom($nom)` : Valide la longueur et les caractères
- `setJeu($jeu)` : Accepte objet Jeu ou ID
- `setMaitreJeu($utilisateur)` : Accepte objet Utilisateur ou ID
- `setTypeActivite($type)`, `setTypeCampagne($type)`
- `setEtat($etat)` : Gestion des transitions d'état
- `setNombreMaxJoueurs($nombre)`, `setMaxJoueursSession($nombre)`
- `setVerrouille($verrouille)` : Contrôle des inscriptions

## Relations avec d'autres entités

### Jeu (obligatoire)
- **Relation** : N:1 vers `Jeu`
- **Champ** : `id_jeu`
- **Lazy loading** : L'objet jeu est chargé à la demande
- **Cascade** : Gestion des erreurs si le jeu n'existe plus

### Maître de jeu (obligatoire)
- **Relation** : N:1 vers `Utilisateur`
- **Champ** : `id_maitre_jeu`
- **Lazy loading** : L'objet utilisateur est chargé à la demande
- **Rôle** : Responsable de l'animation de l'activité

### Sessions (1:N)
- Chaque activité peut avoir plusieurs sessions
- Filtrage possible par jours de la semaine des sessions
- Contrôle du nombre de participants par session

### Genres (N:N via Jeu)
- Filtrage des activités par genres/catégories via le jeu associé
- Jointures automatiques pour les recherches par catégorie

## Fonctionnalités de recherche

### Filtres disponibles
- **Mot-clé** : Recherche dans nom et description
- **Jeu** : Filtre par jeu spécifique
- **Maître de jeu** : Filtre par animateur
- **Type d'activité** : Campagne, oneshot, jeu de société, événement
- **Catégories** : Filtre par genres du jeu associé
- **Jours** : Filtre par jours de sessions planifiées

### Jointures intelligentes
- Jointures conditionnelles selon les filtres utilisés
- Optimisation des requêtes avec `DISTINCT`
- Support des filtres multiples avec paramètres nommés

### Système de cache
- Clé de cache générée à partir des paramètres de recherche
- Cache APCu avec TTL configurable
- Invalidation automatique lors des modifications

## Validation des données

### Contraintes appliquées
- **Nom** : Non vide, longueur appropriée
- **Type d'activité** : Valeur d'énumération valide
- **Type de campagne** : Requis pour les campagnes
- **Nombres** : Valeurs positives pour les limites de joueurs
- **Image** : Validation via la classe Image
- **Relations** : Vérification de l'existence des entités liées

### Gestion des erreurs
- `InvalidArgumentException` pour les paramètres invalides
- `PDOException` pour les erreurs de base de données
- Gestion gracieuse des entités liées inexistantes

## Utilisation typique

### Création d'une campagne ouverte
```php
$activite = new Activite(
    null,
    'Campagne Pathfinder - Les Royaumes Oubliés',
    $jeuPathfinder,
    $maitreDuJeu,
    TypeActivite::Campagne,
    TypeCampagne::Ouverte,
    'Une campagne épique dans l\'univers D&D',
    'Description complète de la campagne...',
    6,  // Max 6 joueurs
    4,  // Max 4 par session
    $image,
    'Illustration de la campagne'
);
$activite->save();
```

### Recherche d'activités par critères
```php
$activites = Activite::search(
    $pdo,
    'pathfinder',              // Mot-clé
    0,                        // Tous les jeux
    '',                       // Tous les maîtres de jeu
    'CAMPAGNE',               // Seulement les campagnes
    [$genreRoleplay->getId()], // Genre jeu de rôle
    ['vendredi', 'samedi']    // Sessions weekend
);
```

### Gestion des états
```php
$activite = new Activite($activiteId);
$activite->setEtat(EtatActivite::Fermer);  // Fermer aux inscriptions
$activite->setVerrouille(true);            // Verrouiller
$activite->save();
```

## Table de base de données

### Structure de `activites`
- `id` : INT AUTO_INCREMENT, clé primaire
- `nom` : VARCHAR, nom de l'activité
- `etat` : ENUM, état de l'activité
- `id_jeu` : INT, clé étrangère vers `jeux`
- `id_maitre_jeu` : VARCHAR, clé étrangère vers `utilisateurs`
- `type_activite` : ENUM, type d'activité
- `type_campagne` : ENUM, type de campagne (optionnel)
- `description_courte` : TEXT, description résumée
- `description` : TEXT, description complète
- `nombre_max_joueurs` : INT, limite totale de joueurs
- `max_joueurs_session` : INT, limite par session
- `verrouille` : BOOLEAN, statut de verrouillage
- `image` : VARCHAR, chemin de l'image
- `texte_alt_image` : VARCHAR, texte alternatif
- `date_creation` : DATETIME, timestamp de création

### Index recommandés
- `id_jeu, type_activite` pour les recherches par jeu et type
- `id_maitre_jeu` pour les recherches par maître de jeu
- `etat` pour les filtres par état
- `date_creation` pour les tris chronologiques

## Sérialisation JSON

La classe implémente `JsonSerializable` et retourne toutes les propriétés de l'objet, avec les objets liés sérialisés via leurs propres méthodes `jsonSerialize()`.

## Notes techniques

### Performance
- Cache APCu pour les recherches fréquentes
- Lazy loading des objets liés pour éviter les requêtes inutiles
- Jointures conditionnelles selon les filtres utilisés

### Extensibilité
- Système d'énumérations pour maintenir la cohérence
- Support des images via la classe Image
- Architecture modulaire pour ajouter de nouveaux types

### Intégration
- Point central du système d'activités
- Liaison avec les sessions, inscriptions et notifications
- Support des workflows de gestion d'événements
