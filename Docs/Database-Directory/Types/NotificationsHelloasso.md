# NotificationsHelloasso

## Description

La classe `NotificationsHelloasso` gère les notifications webhook reçues de la plateforme HelloAsso dans la base de données. Cette classe permet de stocker, traiter et suivre l'état des événements transmis par HelloAsso via leur système de webhooks.

## Fonctionnalités principales

### Gestion des notifications webhook
- **Stockage des événements** : Enregistre les notifications reçues d'HelloAsso avec leur type, date et données JSON
- **Suivi du traitement** : Marque les notifications comme traitées ou non traitées
- **Horodatage automatique** : Enregistre automatiquement la date de réception des notifications

### Types d'événements supportés
- Tous types d'événements HelloAsso (paiements, inscriptions, annulations, etc.)
- Stockage flexible via JSON pour s'adapter à différents formats d'événements
- Validation du format JSON des données reçues

## Structure de la classe

### Propriétés privées
```php
private string $typeEvenement;     // Type d'événement HelloAsso
private string $dateEvenement;     // Date de l'événement (Y-m-d H:i:s)
private string $donnees;           // Données JSON de l'événement
private string $dateReception;     // Date de réception (Y-m-d H:i:s)
private bool $traite;              // Statut de traitement
```

### Constructeur
Le constructeur supporte deux modes d'utilisation :

#### Mode chargement depuis la base
```php
new NotificationsHelloasso($id)
```

#### Mode création d'une nouvelle notification
```php
new NotificationsHelloasso(
    null,                    // id
    $typeEvenement,         // Type d'événement (requis)
    $dateEvenement,         // Date de l'événement (requis)
    $donnees,               // Données JSON (requis)
    $dateReception,         // Date de réception (optionnel)
    $traite                 // Statut traité (optionnel, défaut false)
)
```

## Méthodes principales

### Opérations CRUD
- **`save()`** : Sauvegarde (insertion ou mise à jour) la notification
- **`delete()`** : Supprime la notification de la base de données
- **`loadFromDatabase($id)`** : Charge une notification existante

### Recherche et filtrage
- **`search(PDO $pdo, string $typeEvenement = '', ?bool $traite = null)`** : Recherche des notifications avec filtres optionnels

### Getters
- `getId()`, `getTypeEvenement()`, `getDateEvenement()`
- `getDonnees()`, `getDateReception()`, `getTraite()`

### Setters avec validation
- `setTypeEvenement($typeEvenement)` : Valide la longueur (max 100 caractères)
- `setDateEvenement($dateEvenement)` : Valide le format datetime
- `setDonnees($donnees)` : Valide le format JSON
- `setDateReception($dateReception)` : Valide le format datetime
- `setTraite($traite)` : Définit le statut de traitement

## Validation des données

### Contraintes appliquées
- **Type d'événement** : Non vide, maximum 100 caractères
- **Dates** : Format Y-m-d H:i:s obligatoire
- **Données** : JSON valide obligatoire
- **ID** : UUID non vide requis

### Gestion des erreurs
- `InvalidArgumentException` pour les paramètres invalides
- `PDOException` pour les erreurs de base de données
- Validation des formats datetime et JSON

## Utilisation typique

### Création d'une nouvelle notification
```php
$notification = new NotificationsHelloasso(
    null,
    'payment.completed',
    '2024-01-15 14:30:00',
    '{"amount": 25.00, "currency": "EUR", "order_id": "123"}',
    null,  // Date de réception automatique
    false  // Non traité par défaut
);
$notification->save();
```

### Chargement et traitement d'une notification
```php
$notification = new NotificationsHelloasso($notificationId);
// Traiter la notification...
$notification->setTraite(true);
$notification->save();
```

### Recherche de notifications non traitées
```php
$notificationsNonTraitees = NotificationsHelloasso::search(
    $pdo,
    '',     // Tous types d'événements
    false   // Non traitées uniquement
);
```

## Table de base de données

### Structure de `notifications_helloasso`
- `id` : VARCHAR, identifiant unique UUID
- `type_evenement` : VARCHAR(100), type d'événement HelloAsso
- `date_evenement` : DATETIME, date de l'événement
- `donnees` : TEXT, données JSON de l'événement
- `date_reception` : DATETIME, date de réception de la notification
- `traite` : BOOLEAN, statut de traitement

### Relations
Cette table fonctionne de manière autonome mais peut être référencée par d'autres entités :
- Référencée par `PaiementsHelloasso` via `id_notification`

## Sérialisation JSON

La classe implémente `JsonSerializable` et retourne :
```json
{
    "id": "uuid",
    "type_evenement": "payment.completed",
    "date_evenement": "2024-01-15 14:30:00",
    "donnees": "{...}",
    "date_reception": "2024-01-15 14:30:05",
    "traite": false
}
```

## Notes techniques

### Gestion des webhooks
- La classe est conçue pour recevoir et stocker les notifications webhook d'HelloAsso
- Le champ `donnees` stocke le payload JSON complet pour une flexibilité maximale
- Le statut `traite` permet de suivre le processus de traitement des notifications

### Performance
- Index recommandés sur `type_evenement` et `traite` pour les recherches
- Possibilité de filtrer efficacement les notifications non traitées
- Gestion automatique des timestamps de réception
