# JoueursSession.php

## Description
Classe représentant la table `joueurs_session` qui gère les inscriptions des utilisateurs aux sessions de jeu (relation N:N entre utilisateurs et sessions).

## Propriétés
- **`idSession`** : ID de la session (clé étrangère)
- **`session`** : Objet Session associé (chargé automatiquement)
- **`idUtilisateur`** : ID de l'utilisateur (clé étrangère)
- **`utilisateur`** : Objet Utilisateur associé (chargé automatiquement)
- **`dateInscription`** : Date/heure d'inscription (horodatage automatique)

## Constructeur
```php
public function __construct(
    ?int $idSession = null,
    ?string $idUtilisateur = null,
    Session|int|null $sessionOuId = null,
    Utilisateur|string|null $utilisateurOuId = null,
    ?string $dateInscription = null
)
```

### Modes d'utilisation
1. **Chargement depuis base** : `new JoueursSession($idSession, $idUtilisateur)` - Charge une inscription existante
2. **Création nouvelle** : `new JoueursSession(null, null, $session, $utilisateur)` - Prépare une inscription

## Méthodes principales

### Recherche et récupération
- **`getBySessionAndUtilisateur(int $idSession, string $idUtilisateur)`** : Récupère une inscription spécifique
- **`getBySession(int $idSession)`** : Liste tous les inscrits à une session
- **`getByUtilisateur(string $idUtilisateur)`** : Liste toutes les sessions d'un utilisateur
- **`getInscriptionsRecentess(int $limite)`** : Dernières inscriptions

### CRUD
- **`save()`** : Sauvegarde l'inscription (insertion uniquement)
- **`delete()`** : Suppression de l'inscription (désistement)
- **`exists()`** : Vérifie si l'inscription existe

### Gestion des inscriptions
- **`inscrire(Session $session, Utilisateur $utilisateur)`** : Inscription directe
- **`desinscrire(Session $session, Utilisateur $utilisateur)`** : Désinscription
- **`estInscrit(Session $session, Utilisateur $utilisateur)`** : Vérification d'inscription
- **`compterInscrits(Session $session)`** : Nombre d'inscrits à une session

### Accesseurs
- **`getSession()`** : Récupère l'objet Session associé
- **`getUtilisateur()`** : Récupère l'objet Utilisateur associé
- **`getDateInscription()`** : Date d'inscription au format DateTime
- **`getIdSession()`**, **`getIdUtilisateur()`** : IDs des entités liées

## Validations

### Contraintes d'inscription
- **Session existante** : Vérification que la session existe
- **Utilisateur existant** : Vérification que l'utilisateur existe
- **Pas de doublon** : Une seule inscription par (utilisateur, session)
- **Places disponibles** : Vérification du nombre maximum de participants
- **Session ouverte** : Vérification de l'état de la session

### Règles métier
- **Maître de jeu** : Le MJ ne peut pas s'inscrire comme joueur à sa propre session
- **Activité fermée** : Vérification de l'autorisation pour les campagnes fermées
- **Conflit d'horaire** : Optionnel, vérification des créneaux utilisateur

## Méthodes statiques utilitaires

### Statistiques
```php
// Nombre d'inscrits par session
$nbInscrits = JoueursSession::compterInscrits($session);

// Sessions les plus populaires
$populaires = JoueursSession::getSessionsPopulaires(10);

// Utilisateurs les plus actifs
$actifs = JoueursSession::getUtilisateursActifs(10);
```

### Gestion de groupe
```php
// Inscription multiple
JoueursSession::inscrireGroupe($session, [$user1, $user2, $user3]);

// Désinscription de tous les participants
JoueursSession::viderSession($session);
```

## Relations base de données
- **Session** : Relation N:1 (une inscription appartient à une session)
- **Utilisateur** : Relation N:1 (une inscription appartient à un utilisateur)
- **Clé composite** : Primary key sur (`id_session`, `id_utilisateur`)

## Utilisation dans le projet
- **Système d'inscription** : Gestion des inscriptions aux sessions
- **Planning utilisateur** : Affichage des sessions d'un utilisateur
- **Gestion des sessions** : Liste des participants pour les MJ
- **Statistiques** : Analyse de fréquentation et participation

## Exemple d'utilisation
```php
// Inscription d'un utilisateur à une session
$session = new Session(123);
$utilisateur = new Utilisateur('user-uuid-123');

$inscription = new JoueursSession(null, null, $session, $utilisateur);
$inscription->save(); // Inscription en base

// Vérification d'inscription
$estInscrit = JoueursSession::estInscrit($session, $utilisateur);

// Liste des inscrits à une session
$inscrits = JoueursSession::getBySession(123);
foreach ($inscrits as $inscription) {
    echo $inscription->getUtilisateur()->getPrenom();
}

// Désinscription
$inscription->delete();
```
