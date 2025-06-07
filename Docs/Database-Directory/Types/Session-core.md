# Session

## Description

La classe `Session` représente une session de jeu dans la base de données. Une session constitue une occurrence spécifique d'une activité, planifiée à une date, heure et lieu précis, avec un nombre limité de participants. Elle fait le lien entre les activités abstraites et leurs réalisations concrètes.

## Fonctionnalités principales

### Planification temporelle
- **Date et horaires** : Gestion précise des créneaux de jeu
- **Validation des horaires** : Cohérence entre heure de début et fin
- **Filtrage par jours** : Recherche par jours de la semaine

### Gestion des participants
- **Limite de joueurs** : Contrôle du nombre maximum de participants
- **Héritage des limites** : Récupération automatique depuis l'activité parente
- **Flexibilité** : Possibilité de surcharger les limites par session

### Système de cache
- Cache APCu pour optimiser les recherches fréquentes
- Invalidation automatique lors des modifications
- Configuration TTL ajustable (5 minutes par défaut)

## Énumération associée

### EtatSession
```php
enum EtatSession: string
{
    case Ouverte = 'OUVERTE';     // Session ouverte aux inscriptions
    case Fermer = 'FERMER';       // Session fermée aux inscriptions
    case Annuler = 'ANNULER';     // Session annulée
    case Supprimer = 'SUPPRIMER'; // Session marquée pour suppression
    case Complete = 'COMPLETE';   // Session terminée/complète
}
```

## Structure de la classe

### Propriétés principales
```php
private ?int $idActivite;                    // ID de l'activité parente
private ?Activite $activite = null;         // Objet activité (lazy loading)
private ?int $idLieu;                        // ID du lieu de la session
private ?Lieu $lieu = null;                 // Objet lieu (lazy loading)
private string $nom;                         // Nom de la session
private EtatSession $etat;                   // État de la session
private string $dateSession;                 // Date de la session (Y-m-d)
private string $heureDebut;                  // Heure de début (H:i:s)
private string $heureFin;                    // Heure de fin (H:i:s)
private ?string $idMaitreJeu;                // ID du maître de jeu
private ?Utilisateur $maitreJeu = null;     // Objet utilisateur (lazy loading)
private ?int $maxJoueurs = null;             // Limite max de joueurs (hérité)
private int $maxJoueursSession = 5;          // Limite spécifique à la session
```

### Configuration du cache
```php
private static $cacheEnabled = false;       // Activation du cache
private static $cacheTTL = 300;             // TTL en secondes (5 min)
private static $cachePrefix = 'session_search_'; // Préfixe des clés
```

## Constructeur

### Mode chargement depuis la base
```php
new Session($id)
```

### Mode création d'une nouvelle session
```php
new Session(
    null,                    // id
    $activiteOuId,          // Activité ou ID (requis)
    $lieuOuId,              // Lieu ou ID (requis)
    $nom,                   // Nom (optionnel, défaut "Session")
    $dateSession,           // Date (Y-m-d, requis)
    $heureDebut,            // Heure début (H:i:s, requis)
    $heureFin,              // Heure fin (H:i:s, requis)
    $maitreJeuOuId,         // Maître de jeu ou ID (requis)
    $maxJoueurs,            // Max joueurs (optionnel, hérité)
    $maxJoueursSession      // Max par session (optionnel)
)
```

### Logique d'initialisation
- **Nom par défaut** : "Session" si non spécifié
- **Limites héritées** : `maxJoueurs` récupéré depuis l'activité si non fourni
- **Adaptation automatique** : `maxJoueursSession` ajusté selon `maxJoueurs`

## Méthodes principales

### Opérations CRUD
- **`save()`** : Sauvegarde (insertion ou mise à jour) la session
- **`delete()`** : Supprime la session et invalide le cache
- **`loadFromDatabase($id)`** : Charge une session existante

### Recherche avancée
```php
public static function search(
    PDO $pdo,
    ?int $activiteId = 0,       // Filtre par activité
    ?int $lieuId = 0,           // Filtre par lieu
    ?string $dateDebut = '',    // Date de début (Y-m-d)
    ?string $dateFin = '',      // Date de fin (Y-m-d)
    ?int $maxJoueurs = null,    // Limite de joueurs
    ?array $categories = null,  // Filtre par genres
    ?array $jours = null,       // Filtre par jours
    ?bool $serialize = true     // Sérialisation JSON
): array
```

### Options de filtrage
```php
public static function getFilterOptions(PDO $pdo): array
```
- Récupère les lieux disponibles
- Détermine les plages de dates existantes
- Calcule les limites de joueurs disponibles

### Getters avec lazy loading
- `getId()`, `getNom()`, `getEtat()`, `getDateSession()`
- `getActivite()` : Charge automatiquement l'objet Activité
- `getLieu()` : Charge automatiquement l'objet Lieu
- `getMaitreJeu()` : Charge automatiquement l'objet Utilisateur
- `getHeureDebut()`, `getHeureFin()`, `getMaxJoueurs()`, `getMaxJoueursSession()`

### Setters avec validation
- `setNom($nom)` : Valide la longueur du nom
- `setActivite($activite)` : Accepte objet Activité ou ID
- `setLieu($lieu)` : Accepte objet Lieu ou ID
- `setMaitreJeu($utilisateur)` : Accepte objet Utilisateur ou ID
- `setDateSession($date)` : Valide le format de date (Y-m-d)
- `setHeureDebut($heure)`, `setHeureFin($heure)` : Valident les formats d'heure
- `setEtat($etat)` : Gestion des transitions d'état
- `setMaxJoueurs($max)`, `setMaxJoueursSession($max)` : Validation des nombres

## Relations avec d'autres entités

### Activité (obligatoire)
- **Relation** : N:1 vers `Activite`
- **Champ** : `id_activite`
- **Lazy loading** : L'objet activité est chargé à la demande
- **Héritage** : Récupération des limites de joueurs depuis l'activité

### Lieu (obligatoire)
- **Relation** : N:1 vers `Lieu`
- **Champ** : `id_lieu`
- **Lazy loading** : L'objet lieu est chargé à la demande
- **Contraintes** : Vérification de la disponibilité du lieu

### Maître de jeu (obligatoire)
- **Relation** : N:1 vers `Utilisateur`
- **Champ** : `id_maitre_jeu`
- **Lazy loading** : L'objet utilisateur est chargé à la demande
- **Rôle** : Responsable de l'animation de la session

### Inscriptions (1:N)
- Chaque session peut avoir plusieurs inscriptions via `JoueursSession`
- Contrôle du nombre de participants selon les limites définies

## Fonctionnalités de recherche

### Filtres disponibles
- **Activité** : Sessions d'une activité spécifique
- **Lieu** : Sessions dans un lieu donné
- **Période** : Plage de dates avec validation
- **Capacité** : Nombre maximum de joueurs
- **Catégories** : Filtrage par genres via l'activité
- **Jours** : Jours de la semaine (nom français/anglais ou numérique)

### Jointures intelligentes
- Jointures conditionnelles selon les filtres utilisés
- Support des filtres par genres via les relations activité→jeu→genres
- Optimisation avec `DISTINCT` pour éviter les doublons

### Conversion des jours
```php
$jourMap = [
    'dimanche' => 1, 'lundi' => 2, 'mardi' => 3, 'mercredi' => 4,
    'jeudi' => 5, 'vendredi' => 6, 'samedi' => 7,
    'sunday' => 1, 'monday' => 2, // ... support anglais
    '0' => 1, '1' => 2, // ... support numérique
];
```

## Validation des données

### Contraintes appliquées
- **Date** : Format Y-m-d obligatoire et valide
- **Heures** : Format H:i:s avec cohérence début < fin
- **Nom** : Non vide, longueur appropriée
- **Limites** : Valeurs positives pour les nombres de joueurs
- **État** : Valeur d'énumération valide
- **Relations** : Vérification de l'existence des entités liées

### Gestion des erreurs
- `InvalidArgumentException` pour les paramètres invalides
- `PDOException` pour les erreurs de base de données
- Gestion gracieuse des entités liées inexistantes

## Utilisation typique

### Création d'une session
```php
$session = new Session(
    null,
    $activiteId,            // ID de l'activité
    $lieuId,                // ID du lieu
    'Session découverte',   // Nom spécifique
    '2024-02-15',          // Date
    '19:00:00',            // Heure début
    '23:00:00',            // Heure fin
    $maitreJeuId,          // ID du maître de jeu
    4,                     // Max 4 joueurs pour cette session
    4                      // Limite de la session
);
$session->save();
```

### Recherche de sessions par critères
```php
$sessions = Session::search(
    $pdo,
    $activiteId,           // Sessions d'une activité
    0,                     // Tous les lieux
    '2024-02-01',         // À partir du 1er février
    '2024-02-29',         // Jusqu'à fin février
    null,                  // Toutes capacités
    [$genreRPG->getId()],  // Jeux de rôle uniquement
    ['vendredi', 'samedi'] // Weekends seulement
);
```

### Récupération des options de filtre
```php
$options = Session::getFilterOptions($pdo);
// Retourne: ['lieux' => [...], 'date_debut' => '...', 'date_fin' => '...', 'max_joueurs' => ...]
```

## Table de base de données

### Structure de `sessions`
- `id` : INT AUTO_INCREMENT, clé primaire
- `id_activite` : INT, clé étrangère vers `activites`
- `id_lieu` : INT, clé étrangère vers `lieux`
- `nom` : VARCHAR, nom de la session
- `etat` : ENUM, état de la session
- `date_session` : DATE, date de la session
- `heure_debut` : TIME, heure de début
- `heure_fin` : TIME, heure de fin
- `id_maitre_jeu` : VARCHAR, clé étrangère vers `utilisateurs`
- `nombre_max_joueurs` : INT, limite héritée de l'activité
- `max_joueurs_session` : INT, limite spécifique à la session

### Index recommandés
- `id_activite, date_session` pour les recherches par activité et date
- `id_lieu, date_session` pour les recherches par lieu et date
- `date_session` pour les tris chronologiques
- `etat` pour les filtres par état

## Sérialisation JSON

La classe implémente `JsonSerializable` et retourne toutes les propriétés, avec option de désactiver la sérialisation pour récupérer les objets bruts.

## Notes techniques

### Performance
- Cache APCu pour les recherches fréquentes
- Lazy loading des objets liés pour éviter les requêtes inutiles
- Jointures conditionnelles selon les filtres utilisés

### Héritage des limites
- Récupération automatique des limites depuis l'activité parente
- Possibilité de surcharger pour des besoins spécifiques
- Adaptation intelligente selon la capacité disponible

### Gestion des créneaux
- Validation de la cohérence temporelle (début < fin)
- Support des formats d'heure flexibles
- Intégration avec les horaires des lieux

### Intégration
- Liaison directe avec le système d'inscriptions
- Point de convergence entre activités, lieux et participants
- Support des workflows de planification d'événements
