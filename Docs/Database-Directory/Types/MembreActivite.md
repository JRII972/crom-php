# MembreActivite.php

## Description
Classe représentant la table `membres_activite` qui gère la whitelist (liste des membres autorisés) pour les campagnes fermées.

## Propriétés
- **`idActivite`** : ID de l'activité (clé étrangère)
- **`activite`** : Objet Activite associé (chargé automatiquement)
- **`idUtilisateur`** : ID de l'utilisateur (clé étrangère)
- **`utilisateur`** : Objet Utilisateur associé (chargé automatiquement)

## Constructeur
```php
public function __construct(
    ?int $idActivite = null,
    ?string $idUtilisateur = null,
    Activite|int|null $activiteOuId = null,
    Utilisateur|string|null $utilisateurOuId = null
)
```

### Modes d'utilisation
1. **Chargement depuis base** : `new MembreActivite($idActivite, $idUtilisateur)` - Charge une autorisation existante
2. **Création nouvelle** : `new MembreActivite(null, null, $activite, $utilisateur)` - Prépare une autorisation

## Méthodes principales

### Recherche et récupération
- **`getByActiviteAndUtilisateur(int $idActivite, string $idUtilisateur)`** : Récupère une autorisation spécifique
- **`getByActivite(int $idActivite)`** : Liste tous les membres autorisés pour une activité
- **`getByUtilisateur(string $idUtilisateur)`** : Liste toutes les activités fermées autorisées pour un utilisateur

### CRUD
- **`save()`** : Sauvegarde l'autorisation (insertion uniquement)
- **`delete()`** : Suppression de l'autorisation (retrait de la whitelist)
- **`exists()`** : Vérifie si l'autorisation existe

### Gestion des autorisations
- **`autoriser(Activite $activite, Utilisateur $utilisateur)`** : Ajout direct à la whitelist
- **`revoquer(Activite $activite, Utilisateur $utilisateur)`** : Retrait de la whitelist
- **`estAutorise(Activite $activite, Utilisateur $utilisateur)`** : Vérification d'autorisation
- **`compterMembres(Activite $activite)`** : Nombre de membres autorisés

### Accesseurs
- **`getActivite()`** : Récupère l'objet Activite associé
- **`getUtilisateur()`** : Récupère l'objet Utilisateur associé
- **`getIdActivite()`**, **`getIdUtilisateur()`** : IDs des entités liées

## Validations

### Contraintes d'autorisation
- **Activité existante** : Vérification que l'activité existe
- **Utilisateur existant** : Vérification que l'utilisateur existe
- **Activité fermée** : Seules les activités `type_campagne = 'FERMEE'` utilisent cette table
- **Pas de doublon** : Une seule autorisation par (activité, utilisateur)
- **MJ exclu** : Le maître de jeu n'a pas besoin d'être dans la whitelist

### Règles métier
- **Campagnes ouvertes** : N'utilisent pas cette table (accès libre)
- **Invitation uniquement** : Seul le MJ ou admin peut ajouter des membres
- **Révocation possible** : Le MJ peut retirer des membres de la whitelist

## Méthodes statiques utilitaires

### Gestion de groupe
```php
// Autorisation multiple
MembreActivite::autoriserGroupe($activite, [$user1, $user2, $user3]);

// Vidage de la whitelist
MembreActivite::viderWhitelist($activite);

// Import depuis une autre activité
MembreActivite::copierWhitelist($activiteSource, $activiteCible);
```

### Vérifications
```php
// Vérifier si un utilisateur peut s'inscrire
$peutSinscrire = MembreActivite::peutSinscrire($activite, $utilisateur);

// Obtenir les activités accessibles pour un utilisateur
$activitesAccessibles = MembreActivite::getActivitesAccessibles($utilisateur);
```

## Relations base de données
- **Activite** : Relation N:1 (une autorisation appartient à une activité)
- **Utilisateur** : Relation N:1 (une autorisation appartient à un utilisateur)
- **Clé composite** : Primary key sur (`id_activite`, `id_utilisateur`)

## Utilisation dans le projet
- **Contrôle d'accès** : Vérification des droits d'inscription aux campagnes fermées
- **Gestion par MJ** : Interface pour gérer les membres autorisés
- **Invitation système** : Mécanisme d'invitation pour campagnes privées
- **Administration** : Gestion des accès par les administrateurs

## Logique métier

### Campagnes ouvertes vs fermées
```php
// Campagne ouverte - Pas de restriction
if ($activite->getTypeCampagne() === 'OUVERTE') {
    // Inscription libre (dans la limite des places)
}

// Campagne fermée - Vérification whitelist
if ($activite->getTypeCampagne() === 'FERMEE') {
    $autorise = MembreActivite::estAutorise($activite, $utilisateur);
    if (!$autorise) {
        throw new Exception('Accès refusé - Campagne sur invitation');
    }
}
```

### Workflow d'invitation
1. **MJ crée campagne fermée** : `type_campagne = 'FERMEE'`
2. **MJ invite des joueurs** : Ajout dans `membres_activite`
3. **Joueurs voient la campagne** : Filtrée selon leurs autorisations
4. **Inscription conditionnelle** : Vérification avant inscription aux sessions

## Exemple d'utilisation
```php
// Création d'une campagne fermée
$campagne = new Activite(null, 'Campagne secrète', ...);
$campagne->setTypeCampagne('FERMEE');
$campagne->save();

// Invitation de joueurs spécifiques
$alice = new Utilisateur('alice-uuid');
$bob = new Utilisateur('bob-uuid');

MembreActivite::autoriser($campagne, $alice);
MembreActivite::autoriser($campagne, $bob);

// Vérification avant inscription à une session
$session = new Session(...);
$charlie = new Utilisateur('charlie-uuid');

if (!MembreActivite::estAutorise($campagne, $charlie)) {
    echo "Accès refusé - Campagne sur invitation uniquement";
} else {
    // Procéder à l'inscription
    $inscription = new JoueursSession(null, null, $session, $charlie);
    $inscription->save();
}
```
