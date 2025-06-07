# CreneauxUtilisateur.php

## Description
Classe représentant la table `creneaux_utilisateur` qui gère les disponibilités et indisponibilités des utilisateurs pour la planification des sessions.

## Énumération TypeCreneau
```php
enum TypeCreneau: string
{
    case Disponibilite = 'DISPONIBILITE';
    case Indisponibilite = 'INDISPONIBILITE';
}
```

## Propriétés
- **`idUtilisateur`** : ID de l'utilisateur (clé étrangère)
- **`utilisateur`** : Objet Utilisateur associé (chargé automatiquement)
- **`typeCreneau`** : Type via énumération TypeCreneau
- **`dateHeureDebut`** : Date/heure de début (format Y-m-d H:i:s)
- **`dateHeureFin`** : Date/heure de fin (format Y-m-d H:i:s)
- **`estRecurrant`** : Indique si le créneau se répète
- **`regleRecurrence`** : Règle de récurrence en format iCal RRULE (optionnel)

## Constructeur
```php
public function __construct(
    ?int $id = null,
    Utilisateur|string|null $utilisateurOuId = null,
    ?TypeCreneau $typeCreneau = null,
    ?string $dateHeureDebut = null,
    ?string $dateHeureFin = null,
    ?bool $estRecurrant = null,
    ?string $regleRecurrence = null
)
```

### Modes d'utilisation
1. **Chargement depuis base** : `new CreneauxUtilisateur($id)` - Charge un créneau existant
2. **Création nouveau** : `new CreneauxUtilisateur(null, $utilisateur, $type, ...)` - Prépare un nouveau créneau

## Méthodes principales

### Recherche et récupération
- **`getById(int $id)`** : Récupère un créneau par son ID
- **`getByUtilisateur(string $idUtilisateur)`** : Liste tous les créneaux d'un utilisateur
- **`getDisponibilites(string $idUtilisateur)`** : Créneaux de disponibilité uniquement
- **`getIndisponibilites(string $idUtilisateur)`** : Créneaux d'indisponibilité uniquement
- **`getCreneauxPeriode(string $idUtilisateur, string $debut, string $fin)`** : Créneaux sur une période

### CRUD
- **`save()`** : Sauvegarde le créneau (insertion ou mise à jour)
- **`delete()`** : Suppression du créneau
- **`exists()`** : Vérifie l'existence du créneau

### Gestion des créneaux
- **`ajouterDisponibilite(Utilisateur $user, string $debut, string $fin)`** : Ajout rapide de disponibilité
- **`ajouterIndisponibilite(Utilisateur $user, string $debut, string $fin)`** : Ajout rapide d'indisponibilité
- **`estDisponible(Utilisateur $user, string $debut, string $fin)`** : Vérification de disponibilité
- **`getConflits(Utilisateur $user, string $debut, string $fin)`** : Détection de conflits

### Accesseurs et mutateurs
- **`getUtilisateur()`** : Récupère l'objet Utilisateur associé
- **`getTypeCreneau()`**, **`setTypeCreneau(TypeCreneau $type)`** : Type de créneau
- **`getDateHeureDebut()`**, **`setDateHeureDebut(string $date)`** : Date/heure début
- **`getDateHeureFin()`**, **`setDateHeureFin(string $date)`** : Date/heure fin
- **`isRecurrant()`**, **`setRecurrant(bool $recurrant)`** : Récurrence
- **`getRegleRecurrence()`**, **`setRegleRecurrence(?string $regle)`** : Règle de récurrence

## Gestion de la récurrence

### Règles de récurrence (iCal RRULE)
Format standard iCalendar pour définir des répétitions :
```
FREQ=WEEKLY;BYDAY=MO,WE,FR;INTERVAL=1
```

### Types de récurrence supportés
- **FREQ=DAILY** : Récurrence quotidienne
- **FREQ=WEEKLY** : Récurrence hebdomadaire avec jours spécifiques
- **FREQ=MONTHLY** : Récurrence mensuelle
- **BYDAY** : Spécification des jours (MO, TU, WE, TH, FR, SA, SU)
- **INTERVAL** : Intervalle de répétition

### Méthodes de récurrence
- **`generateOccurrences(string $debut, string $fin)`** : Génère les occurrences sur une période
- **`parseRegleRecurrence()`** : Parse la règle RRULE
- **`isActiveAt(DateTime $dateTime)`** : Vérifie si le créneau est actif à un moment donné

## Validations

### Contraintes temporelles
- **Dates cohérentes** : Date de fin > date de début
- **Format valide** : Validation des formats Y-m-d H:i:s
- **Utilisateur existant** : Vérification de l'existence de l'utilisateur
- **Règle RRULE valide** : Validation du format iCalendar si récurrent

### Logique métier
- **Pas de chevauchement** : Les créneaux du même type ne doivent pas se chevaucher
- **Priorité indisponibilité** : Les indisponibilités priment sur les disponibilités
- **Durée minimale** : Créneaux d'au moins 30 minutes

## Méthodes statiques utilitaires

### Analyse de disponibilité
```php
// Trouver les créneaux libres communs à plusieurs utilisateurs
$creneauxLibres = CreneauxUtilisateur::trouverCreneauxCommuns(
    [$user1, $user2, $user3],
    '2025-06-01',
    '2025-06-30',
    120 // durée minimale en minutes
);

// Vérifier conflit pour une session
$conflit = CreneauxUtilisateur::detecterConflitSession($session, $utilisateur);
```

### Import/Export
```php
// Import depuis calendrier iCal
CreneauxUtilisateur::importFromIcal($utilisateur, $icalData);

// Export vers format compatible agenda
$icalData = CreneauxUtilisateur::exportToIcal($utilisateur, $debut, $fin);
```

## Relations base de données
- **Utilisateur** : Relation N:1 (un créneau appartient à un utilisateur)
- **Cascade** : Suppression automatique si utilisateur supprimé

## Utilisation dans le projet

### Planification intelligente
- **Aide MJ** : Trouver des créneaux où tous les joueurs sont disponibles
- **Suggestions** : Proposer des horaires optimaux pour les sessions
- **Alertes** : Prévenir des conflits potentiels

### Interface utilisateur
- **Calendrier personnel** : Gestion des disponibilités via interface graphique
- **Import agenda** : Synchronisation avec calendriers externes (Google, Outlook)
- **Notifications** : Rappels avant sessions basés sur les créneaux

### Optimisation
- **Cache** : Mise en cache des créneaux fréquemment consultés
- **Performance** : Index sur dates pour recherches rapides

## Exemple d'utilisation
```php
// Définir une disponibilité récurrente (tous les mardis soir)
$disponibilite = new CreneauxUtilisateur(
    null,
    $utilisateur,
    TypeCreneau::Disponibilite,
    '2025-06-10 19:00:00',
    '2025-06-10 23:00:00',
    true,
    'FREQ=WEEKLY;BYDAY=TU'
);
$disponibilite->save();

// Ajouter une indisponibilité ponctuelle (vacances)
$indispo = new CreneauxUtilisateur(
    null,
    $utilisateur,
    TypeCreneau::Indisponibilite,
    '2025-08-01 00:00:00',
    '2025-08-31 23:59:59',
    false
);
$indispo->save();

// Vérifier si un utilisateur est disponible pour une session
$session = new Session(123);
$disponible = CreneauxUtilisateur::estDisponible(
    $utilisateur,
    $session->getDateSession() . ' ' . $session->getHeureDebut(),
    $session->getDateSession() . ' ' . $session->getHeureFin()
);

if (!$disponible) {
    echo "Conflit détecté avec vos créneaux d'indisponibilité";
}
```
