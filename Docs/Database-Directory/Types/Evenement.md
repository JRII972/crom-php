# Evenement.php

## Description
Classe représentant la table `evenements` qui gère les événements de l'association indépendants des sessions de jeu.

## Propriétés
- **`nom`** : Nom de l'événement (obligatoire)
- **`description`** : Description détaillée (optionnel)
- **`dateDebut`** : Date de début (format Y-m-d)
- **`dateFin`** : Date de fin (format Y-m-d)
- **`idLieu`** : ID du lieu (optionnel)
- **`lieu`** : Objet Lieu associé (chargé automatiquement)
- **`regleRecurrence`** : Règle de récurrence en JSON (optionnel)
- **`exceptions`** : Exceptions aux règles en JSON (optionnel)
- **`dateCreation`** : Horodatage de création

## Constructeur
```php
public function __construct(
    ?int $id = null,
    ?string $nom = null,
    ?string $description = null,
    ?string $dateDebut = null,
    ?string $dateFin = null,
    int|Lieu|null $idLieu = null,
    ?string $regleRecurrence = null,
    ?string $exceptions = null,
    ?string $dateCreation = null
)
```

### Modes d'utilisation
1. **Chargement depuis base** : `new Evenement($id)` - Charge un événement existant
2. **Création nouveau** : `new Evenement(null, $nom, ...)` - Prépare un nouvel événement

## Méthodes principales

### Recherche et récupération
- **`getById(int $id)`** : Récupère un événement par son ID
- **`getAllEvenements()`** : Liste tous les événements
- **`getEvenementsByPeriode(string $debut, string $fin)`** : Événements sur une période
- **`getEvenementsRecurrents()`** : Événements avec récurrence

### CRUD
- **`save()`** : Sauvegarde en base (insertion ou mise à jour)
- **`delete()`** : Suppression de l'événement
- **`exists()`** : Vérifie l'existence de l'événement

### Accesseurs et mutateurs
- **`getNom()`**, **`setNom(string $nom)`** : Nom de l'événement
- **`getDescription()`**, **`setDescription(?string $description)`** : Description
- **`getDateDebut()`**, **`setDateDebut(string $date)`** : Date de début
- **`getDateFin()`**, **`setDateFin(string $date)`** : Date de fin
- **`getLieu()`**, **`setLieu(?Lieu $lieu)`** : Lieu associé

## Gestion de la récurrence

### Règles de récurrence (JSON)
Format basé sur iCalendar RRULE :
```json
{
    "byDay": ["LU", "ME", "VE"],
    "interval": 2,
    "frequency": "WEEKLY"
}
```

### Exceptions (JSON)
Dates ou plages à exclure :
```json
{
    "dates": ["2025-05-01", "2025-12-25"],
    "intervals": [
        {"start": "2025-07-15", "end": "2025-08-15"}
    ]
}
```

### Méthodes de récurrence
- **`setRegleRecurrence(array $regle)`** : Définit une règle de récurrence
- **`getRegleRecurrence()`** : Récupère la règle parsée
- **`addException(string $date)`** : Ajoute une exception de date
- **`getOccurrences(string $debut, string $fin)`** : Génère les occurrences

## Validations
- **Nom obligatoire** : Doit être fourni pour créer un événement
- **Dates cohérentes** : Date de fin >= date de début
- **Format dates** : Validation des formats Y-m-d et Y-m-d H:i:s
- **JSON valide** : Validation des règles de récurrence et exceptions
- **Lieu existant** : Vérification si lieu spécifié

## Relations base de données
- **Lieu** : Relation N:1 (plusieurs événements peuvent avoir lieu au même endroit)
- **Horaires** : Relation 1:N via `horaires_lieu` pour overrides spécifiques

## Utilisation dans le projet
- **Calendrier** : Affichage des événements de l'association
- **Planification** : Éviter les conflits avec les sessions
- **Récurrence** : Gestion d'événements répétitifs (réunions, nettoyage, etc.)
- **Administration** : Gestion des événements de l'association

## Exemple d'utilisation
```php
// Création d'un événement simple
$evenement = new Evenement(
    null,
    'Assemblée Générale 2025',
    'AG annuelle de l\'association',
    '2025-06-15',
    '2025-06-15',
    $lieu_principal
);
$evenement->save();

// Événement récurrent (réunion mensuelle)
$reunion = new Evenement(null, 'Réunion bureau', null, '2025-01-15', '2025-12-15');
$reunion->setRegleRecurrence([
    'frequency' => 'MONTHLY',
    'byDay' => ['LU'],
    'interval' => 1
]);
$reunion->addException('2025-08-15'); // Pas de réunion en août
$reunion->save();
```
