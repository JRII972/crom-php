# HorairesLieu.php

## Description
Classe représentant la table `horaires_lieu` qui gère les horaires de disponibilité des lieux avec support de récurrence et exceptions.

## Énumération TypeRecurrence
```php
enum TypeRecurrence: string
{
    case Aucune = 'AUCUNE';
    case Quotidienne = 'QUOTIDIENNE';
    case Hebdomadaire = 'HEBDOMADAIRE';
    case Mensuelle = 'MENSUELLE';
    case Annuelle = 'ANNUELLE';
}
```

## Propriétés
- **`idLieu`** : ID du lieu (clé étrangère)
- **`lieu`** : Objet Lieu associé (chargé automatiquement)
- **`heureDebut`** : Heure de début du créneau (format H:i:s)
- **`heureFin`** : Heure de fin du créneau (format H:i:s)
- **`typeRecurrence`** : Type via énumération TypeRecurrence
- **`regleRecurrence`** : Règle de récurrence en JSON (optionnel)
- **`exceptions`** : Exceptions aux règles en JSON (optionnel)
- **`idEvenement`** : ID d'événement pour override spécifique (optionnel)
- **`evenement`** : Objet Evenement associé (chargé automatiquement)

## Constructeur
```php
public function __construct(
    ?int $id = null,
    Lieu|int|null $lieuOuId = null,
    ?string $heureDebut = null,
    ?string $heureFin = null,
    ?TypeRecurrence $typeRecurrence = null,
    string|array|null $regleRecurrence = null,
    string|array|null $exceptions = null,
    Evenement|int|null $evenementOuId = null
)
```

### Modes d'utilisation
1. **Chargement depuis base** : `new HorairesLieu($id)` - Charge un horaire existant
2. **Création nouveau** : `new HorairesLieu(null, $lieu, ...)` - Prépare un nouvel horaire

## Méthodes principales

### Recherche et récupération
- **`getById(int $id)`** : Récupère un horaire par son ID
- **`getByLieu(int $idLieu)`** : Liste tous les horaires d'un lieu
- **`getHorairesActifs(int $idLieu, string $date)`** : Horaires valides pour une date
- **`getByEvenement(int $idEvenement)`** : Horaires spécifiques à un événement

### CRUD
- **`save()`** : Sauvegarde l'horaire (insertion ou mise à jour)
- **`delete()`** : Suppression de l'horaire
- **`exists()`** : Vérifie l'existence de l'horaire

### Gestion des horaires
- **`estOuvert(Lieu $lieu, DateTime $dateTime)`** : Vérifie si le lieu est ouvert à un moment donné
- **`getCreneauxLibres(Lieu $lieu, string $date)`** : Créneaux disponibles pour réservation
- **`detecterConflit(Lieu $lieu, string $debut, string $fin)`** : Détection de conflits d'horaires

### Accesseurs et mutateurs
- **`getLieu()`** : Récupère l'objet Lieu associé
- **`getHeureDebut()`**, **`setHeureDebut(string $heure)`** : Heure de début
- **`getHeureFin()`**, **`setHeureFin(string $heure)`** : Heure de fin
- **`getTypeRecurrence()`**, **`setTypeRecurrence(TypeRecurrence $type)`** : Type de récurrence
- **`getEvenement()`**, **`setEvenement(?Evenement $evenement)`** : Événement associé

## Gestion de la récurrence

### Format des règles de récurrence (JSON)
```json
{
    "byDay": ["LU", "ME", "VE"],
    "interval": 1,
    "byMonth": [1, 2, 3],
    "until": "2025-12-31"
}
```

### Types de récurrence supportés
- **QUOTIDIENNE** : Répétition tous les jours
- **HEBDOMADAIRE** : Répétition avec jours spécifiques (`byDay`)
- **MENSUELLE** : Répétition mensuelle avec dates ou jours spécifiques
- **ANNUELLE** : Répétition annuelle avec mois et dates spécifiques

### Format des exceptions (JSON)
```json
{
    "dates": ["2025-05-01", "2025-12-25"],
    "intervals": [
        {"start": "2025-07-15", "end": "2025-08-15"}
    ]
}
```

### Méthodes de récurrence
- **`generateOccurrences(string $debut, string $fin)`** : Génère les occurrences sur une période
- **`isValidAt(DateTime $dateTime)`** : Vérifie si l'horaire est valide à un moment donné
- **`addException(string $date)`** : Ajoute une exception de date
- **`removeException(string $date)`** : Supprime une exception

## Validations

### Contraintes temporelles
- **Heures cohérentes** : Heure de fin > heure de début
- **Format valide** : Validation des formats H:i:s
- **Lieu existant** : Vérification de l'existence du lieu
- **JSON valide** : Validation des règles et exceptions JSON

### Logique métier
- **Pas de chevauchement** : Les horaires d'un même lieu ne doivent pas se chevaucher
- **Durée minimale** : Créneaux d'au moins 30 minutes
- **Cohérence récurrence** : Règles cohérentes avec le type de récurrence

## Méthodes statiques utilitaires

### Analyse de disponibilité
```php
// Trouver les lieux disponibles à un moment donné
$lieuxDisponibles = HorairesLieu::getLieuxDisponibles(
    new DateTime('2025-06-15 14:00:00'),
    120 // durée en minutes
);

// Vérifier disponibilité pour une session
$disponible = HorairesLieu::estDisponiblePourSession($session);

// Obtenir les créneaux optimaux pour un groupe de lieux
$creneaux = HorairesLieu::getCreneauxOptimaux($lieux, $dateDebut, $dateFin);
```

### Gestion des événements spéciaux
```php
// Créer des horaires exceptionnels pour un événement
$horaireSpeical = HorairesLieu::creerHoraireEvenement(
    $lieu,
    $evenement,
    '08:00:00',
    '02:00:00' // Ouverture exceptionnelle jusqu'à 2h du matin
);
```

## Relations base de données
- **Lieu** : Relation N:1 (plusieurs horaires par lieu)
- **Evenement** : Relation N:1 optionnelle pour overrides spécifiques
- **Cascade** : Suppression automatique si lieu supprimé
- **SET NULL** : Si événement supprimé, horaire redevient général

## Utilisation dans le projet

### Planification des sessions
- **Vérification** : S'assurer que le lieu est ouvert lors de la création de sessions
- **Suggestions** : Proposer des créneaux compatibles avec les horaires du lieu
- **Alertes** : Prévenir si une session dépasse les horaires d'ouverture

### Gestion des lieux
- **Configuration** : Définir les horaires d'ouverture normaux
- **Exceptions** : Gérer les fermetures exceptionnelles (vacances, travaux)
- **Événements** : Horaires spéciaux pour événements particuliers

### Interface utilisateur
- **Calendrier** : Affichage visuel des disponibilités des lieux
- **Filtrage** : Recherche de créneaux selon disponibilités
- **Réservation** : Validation automatique lors des réservations

## Exemple d'utilisation
```php
// Définir les horaires normaux d'un lieu (ouvert du lundi au vendredi)
$horairesNormaux = new HorairesLieu(
    null,
    $lieu,
    '09:00:00',
    '22:00:00',
    TypeRecurrence::Hebdomadaire,
    ['byDay' => ['LU', 'MA', 'ME', 'JE', 'VE']],
    ['dates' => ['2025-05-01', '2025-12-25']] // Fermé jours fériés
);
$horairesNormaux->save();

// Horaires spéciaux pour un événement (ouverture prolongée)
$horaireEvent = new HorairesLieu(
    null,
    $lieu,
    '08:00:00',
    '02:00:00',
    TypeRecurrence::Aucune,
    null,
    null,
    $evenementSpecial
);
$horaireEvent->save();

// Vérifier si le lieu est ouvert pour une session
$session = new Session(123);
$ouvert = HorairesLieu::estOuvert(
    $session->getLieu(),
    new DateTime($session->getDateSession() . ' ' . $session->getHeureDebut())
);

if (!$ouvert) {
    echo "Attention: le lieu sera fermé à cette heure";
}

// Trouver les créneaux libres pour demain
$creneauxLibres = HorairesLieu::getCreneauxLibres(
    $lieu,
    date('Y-m-d', strtotime('+1 day'))
);
```
