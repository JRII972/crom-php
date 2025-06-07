# PaiementsHelloasso

## Description

La classe `PaiementsHelloasso` gère les paiements traités via la plateforme HelloAsso dans la base de données. Cette classe permet de stocker et suivre les transactions financières en liaison avec les notifications reçues d'HelloAsso et les utilisateurs de l'application.

## Fonctionnalités principales

### Gestion des paiements
- **Stockage des transactions** : Enregistre les détails des paiements (montant, devise, statut)
- **Liaison avec les notifications** : Associe chaque paiement à une notification HelloAsso
- **Association utilisateur** : Lie les paiements aux utilisateurs de l'application
- **Métadonnées flexibles** : Stockage de données additionnelles au format JSON

### Suivi financier
- Gestion des montants et devises
- Dates d'échéance optionnelles
- Statuts de paiement personnalisables
- Horodatage de création automatique

## Structure de la classe

### Propriétés privées
```php
private ?string $idNotification = null;         // ID de la notification liée
private ?NotificationsHelloasso $notification = null;  // Objet notification (lazy loading)
private ?string $idUtilisateur = null;          // ID de l'utilisateur
private ?Utilisateur $utilisateur = null;       // Objet utilisateur (lazy loading)
private ?string $typePaiement = null;           // Type de paiement
private ?string $nom = null;                    // Nom associé au paiement
private float $montant;                         // Montant du paiement
private string $devise;                         // Devise (EUR, USD, etc.)
private ?string $dateEcheance = null;           // Date d'échéance (Y-m-d)
private ?string $statut = null;                 // Statut du paiement
private ?string $metadonnees = null;            // Métadonnées JSON
private string $dateCreation;                   // Date de création (Y-m-d H:i:s)
```

### Constructeur
Le constructeur supporte deux modes d'utilisation :

#### Mode chargement depuis la base
```php
new PaiementsHelloasso($id)
```

#### Mode création d'un nouveau paiement
```php
new PaiementsHelloasso(
    null,                           // id
    $notificationOuId,             // NotificationsHelloasso ou ID (optionnel)
    $utilisateurOuId,              // Utilisateur ou ID (optionnel)
    $typePaiement,                 // Type de paiement (optionnel)
    $nom,                          // Nom (optionnel)
    $montant,                      // Montant (requis)
    $devise,                       // Devise (requis)
    $dateEcheance,                 // Date d'échéance (optionnel)
    $statut,                       // Statut (optionnel)
    $metadonnees,                  // Métadonnées JSON (optionnel)
    $dateCreation                  // Date de création (optionnel)
)
```

## Méthodes principales

### Opérations CRUD
- **`save()`** : Sauvegarde (insertion ou mise à jour) le paiement
- **`delete()`** : Supprime le paiement de la base de données
- **`loadFromDatabase($id)`** : Charge un paiement existant

### Recherche et filtrage
- **`search(PDO $pdo, string $idUtilisateur = '', string $statut = '', string $dateDebut = '', string $dateFin = '')`** : Recherche des paiements avec filtres multiples

### Getters avec lazy loading
- `getId()`, `getMontant()`, `getDevise()`, `getDateCreation()`
- `getNotification()` : Charge automatiquement l'objet NotificationsHelloasso
- `getUtilisateur()` : Charge automatiquement l'objet Utilisateur
- `getTypePaiement()`, `getNom()`, `getDateEcheance()`, `getStatut()`, `getMetadonnees()`

### Setters avec validation
- `setMontant($montant)` : Valide que le montant n'est pas négatif
- `setDevise($devise)` : Valide la longueur (max 10 caractères)
- `setNotification($notification)` : Accepte objet ou ID
- `setUtilisateur($utilisateur)` : Accepte objet ou UUID
- `setDateEcheance($dateEcheance)` : Valide le format de date (Y-m-d)
- `setMetadonnees($metadonnees)` : Valide le format JSON

## Relations avec d'autres entités

### Notification HelloAsso (optionnelle)
- **Relation** : N:1 vers `NotificationsHelloasso`
- **Champ** : `id_notification`
- **Lazy loading** : L'objet notification est chargé à la demande
- **Gestion des erreurs** : Si la notification n'existe plus, l'ID est mis à `null`

### Utilisateur (optionnel)
- **Relation** : N:1 vers `Utilisateur`
- **Champ** : `id_utilisateur`
- **Lazy loading** : L'objet utilisateur est chargé à la demande
- **Validation** : L'ID utilisateur doit être un UUID valide

## Validation des données

### Contraintes appliquées
- **Montant** : Doit être positif ou nul
- **Devise** : Non vide, maximum 10 caractères
- **Type de paiement** : Maximum 100 caractères (optionnel)
- **Nom** : Maximum 255 caractères (optionnel)
- **Statut** : Maximum 50 caractères (optionnel)
- **Date d'échéance** : Format Y-m-d (optionnel)
- **Métadonnées** : JSON valide (optionnel)
- **Date de création** : Format Y-m-d H:i:s obligatoire

### Gestion des erreurs
- `InvalidArgumentException` pour les paramètres invalides
- `PDOException` pour les erreurs de base de données
- Validation automatique des formats UUID, date et JSON

## Utilisation typique

### Création d'un nouveau paiement
```php
$paiement = new PaiementsHelloasso(
    null,                    // ID auto-généré
    $notificationId,         // ID de la notification HelloAsso
    $utilisateurId,          // ID de l'utilisateur
    'adhésion',              // Type de paiement
    'Adhésion annuelle',     // Nom
    25.00,                   // Montant
    'EUR',                   // Devise
    '2024-12-31',           // Date d'échéance
    'en_attente',           // Statut
    '{"description": "Adhésion 2024"}', // Métadonnées
    null                     // Date de création automatique
);
$paiement->save();
```

### Chargement avec relations
```php
$paiement = new PaiementsHelloasso($paiementId);
$notification = $paiement->getNotification();  // Lazy loading
$utilisateur = $paiement->getUtilisateur();    // Lazy loading
```

### Recherche de paiements par utilisateur
```php
$paiementsUtilisateur = PaiementsHelloasso::search(
    $pdo,
    $utilisateurId,    // Filtre par utilisateur
    'confirme',        // Filtre par statut
    '2024-01-01',      // Date de début
    '2024-12-31'       // Date de fin
);
```

## Table de base de données

### Structure de `paiements_helloasso`
- `id` : VARCHAR, identifiant unique UUID
- `id_notification` : VARCHAR, clé étrangère vers `notifications_helloasso` (optionnel)
- `id_utilisateur` : VARCHAR, clé étrangère vers `utilisateurs` (optionnel)
- `type_paiement` : VARCHAR(100), type de paiement (optionnel)
- `nom` : VARCHAR(255), nom ou description (optionnel)
- `montant` : DECIMAL, montant du paiement
- `devise` : VARCHAR(10), code devise
- `date_echeance` : DATE, date d'échéance (optionnel)
- `statut` : VARCHAR(50), statut du paiement (optionnel)
- `metadonnees` : TEXT, données JSON additionnelles (optionnel)
- `date_creation` : DATETIME, timestamp de création

### Index recommandés
- `id_utilisateur, statut, date_creation` pour les recherches par utilisateur
- `statut` pour les filtres par statut
- `date_creation` pour les tris chronologiques

## Sérialisation JSON

La classe implémente `JsonSerializable` et retourne :
```json
{
    "id": "uuid",
    "id_notification": "uuid",
    "id_utilisateur": "uuid",
    "notification": { /* objet NotificationsHelloasso */ },
    "utilisateur": { /* objet Utilisateur */ },
    "type_paiement": "adhésion",
    "nom": "Adhésion annuelle",
    "montant": 25.00,
    "devise": "EUR",
    "date_echeance": "2024-12-31",
    "statut": "confirme",
    "metadonnees": "{\"description\": \"Adhésion 2024\"}",
    "date_creation": "2024-01-15 14:30:00"
}
```

## Notes techniques

### Gestion des relations
- **Lazy loading** : Les objets liés ne sont chargés qu'en cas de besoin
- **Gestion d'erreur** : Si un objet lié n'existe plus, l'ID est automatiquement mis à `null`
- **Flexibilité** : Les setters acceptent à la fois des objets et des IDs

### Métadonnées
- Stockage flexible de données additionnelles au format JSON
- Validation automatique du format JSON
- Possibilité d'étendre les informations sans modification de schéma

### Performance
- Index sur les champs fréquemment filtrés
- Lazy loading pour éviter les requêtes inutiles
- Méthode de recherche optimisée avec filtres multiples

### Intégration HelloAsso
- Conçu pour s'intégrer avec le système de webhooks HelloAsso
- Liaison optionnelle avec les notifications pour traçabilité
- Support des différents types de paiements HelloAsso
