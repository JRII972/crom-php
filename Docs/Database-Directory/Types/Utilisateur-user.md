# Documentation de la classe Utilisateur

## Vue d'ensemble

La classe `Utilisateur` (`/var/www/html/App/Database/Types/Utilisateur.php`) représente les utilisateurs de l'application dans la base de données. Elle hérite de `DefaultDatabaseType` et gère l'authentification, les profils utilisateur, la gestion des images de profil et les différents types d'utilisateurs.

## Énumérations

### Sexe
```php
enum Sexe: string
{
    case Masculin = 'M';
    case Feminin = 'F';
    case Autre = 'Autre';
}
```

### TypeUtilisateur
```php
enum TypeUtilisateur: string
{
    case NonInscrit = 'NON_INSCRIT';
    case Inscrit = 'INSCRIT';
    case Administrateur = 'ADMINISTRATEUR';
}
```

## Propriétés privées

La classe contient les propriétés suivantes :
- `$prenom` : Prénom de l'utilisateur (obligatoire)
- `$nom` : Nom de famille (obligatoire)
- `$email` : Adresse email (optionnelle, avec validation)
- `$nomUtilisateur` : Nom d'utilisateur pour la connexion (obligatoire, unique)
- `$dateDeNaissance` : Date de naissance (optionnelle)
- `$sexe` : Sexe de l'utilisateur (obligatoire)
- `$idDiscord` : Identifiant Discord (optionnel)
- `$pseudonyme` : Pseudonyme d'affichage (optionnel)
- `$motDePasse` : Hash du mot de passe (obligatoire)
- `$image` : Image de profil (optionnelle, instance de la classe Image)
- `$typeUtilisateur` : Type d'utilisateur (par défaut : Inscrit)
- `$dateInscription` : Date d'inscription (optionnelle)
- `$ancienUtilisateur` : Statut d'ancien utilisateur (booléen)
- `$premiereConnexion` : Indicateur de première connexion (booléen)
- `$dateCreation` : Date de création du compte

## Constructeur

Le constructeur supporte deux modes d'utilisation :

### Mode chargement depuis la base
```php
$utilisateur = new Utilisateur($id);
```

### Mode création d'un nouvel utilisateur
```php
$utilisateur = new Utilisateur(
    id: null,
    prenom: 'Jean',
    nom: 'Dupont',
    email: 'jean.dupont@example.com',
    nomUtilisateur: 'jdupont',
    motDePasse: 'motdepassesecurise',
    sexe: Sexe::Masculin
);
```

## Méthodes principales

### Sauvegarde et suppression

#### `save(): void`
Sauvegarde l'utilisateur dans la base de données (insertion ou mise à jour automatique).

**Fonctionnement :**
- Détecte automatiquement si l'utilisateur existe (insertion vs mise à jour)
- Gère la table `utilisateurs` avec tous les champs
- Hache automatiquement les mots de passe avec un salt
- Gère les contraintes d'unicité sur l'email et le nom d'utilisateur

#### `delete(): bool`
Supprime l'utilisateur de la base de données.
- Supprime également l'image de profil associée si elle existe
- Vérifie l'existence de l'utilisateur avant suppression

### Recherche et authentification

#### `search(PDO $pdo, string $email = '', string $nomUtilisateur = '', string $typeUtilisateur = ''): array`
Recherche des utilisateurs avec filtres optionnels.

**Paramètres de recherche :**
- `$email` : Recherche par email exact
- `$nomUtilisateur` : Recherche par nom d'utilisateur exact
- `$typeUtilisateur` : Filtre par type d'utilisateur

**Retour :** Tableau associatif avec les données des utilisateurs (sans mot de passe)

#### `findByLogin(PDO $pdo, $login): ?self`
Trouve un utilisateur par son nom d'utilisateur.
- Retourne une instance `Utilisateur` ou `null` si non trouvé
- Méthode statique pour la recherche rapide

#### `checkLogin(string $login, string $password): ?static`
Vérifie les identifiants de connexion.
- Compare le mot de passe haché avec salt
- Retourne l'utilisateur connecté ou `null` si échec
- Gère les erreurs PDO avec logs

### Getters

La classe fournit des getters pour toutes les propriétés :
- `getId()`, `getPrenom()`, `getNom()`, etc.
- `getAge()` : Calcule l'âge en années depuis la date de naissance
- `getAnneesAnciennete()` : Calcule l'ancienneté depuis la date d'inscription

### Setters avec validation

Tous les setters incluent une validation :

#### `setEmail(?string $email)`
- Validation avec `filter_var(FILTER_VALIDATE_EMAIL)`
- Accepte `null` pour les emails optionnels

#### `setMotDePasse(string $motDePasse)`
- Hachage automatique avec `password_hash()` et salt personnalisé
- Utilise `PASSWORD_BCRYPT` pour la sécurité

#### `setSexe(Sexe|string $sexe)`
- Accepte enum `Sexe` ou string
- Conversion automatique string vers enum avec validation

#### `setImage(Image|string|array $image)`
- Supprime l'ancienne image si elle existe
- Accepte instance `Image`, chemin string ou données array
- Génération automatique du nom de fichier basé sur pseudonyme/nom

#### `setTypeUtilisateur(TypeUtilisateur|string $typeUtilisateur)`
- Conversion automatique string vers enum
- Validation des valeurs autorisées

## Sérialisation JSON

### `jsonSerialize(): array`
Sérialisation publique limitée (pour les API publiques) :
- ID, prénom, nom, pseudonyme, image, ID Discord
- Exclut les données sensibles (email, mot de passe, etc.)

### `private_jsonSerialize(): array`
Sérialisation complète (pour l'administration) :
- Toutes les propriétés incluant email, type d'utilisateur
- Données calculées (âge, ancienneté)
- Format complet pour les interfaces d'administration

## Gestion des images de profil

L'integration avec la classe `Image` permet :
- Upload automatique dans `/ProfilePicture/`
- Nommage basé sur pseudonyme ou nom/prénom
- Suppression automatique lors du changement d'image
- Sérialisation complète de l'image dans `private_jsonSerialize()`

## Sécurité

### Hachage des mots de passe
- Utilisation de `password_hash()` avec `PASSWORD_BCRYPT`
- Salt personnalisé via `PASSWORD_SALT` (constante de configuration)
- Validation lors de la connexion avec `password_verify()`

### Validation des données
- UUIDs validés avec `isValidUuid()`
- Emails validés avec filter PHP
- Noms et prénoms non-vides obligatoires
- Gestion des erreurs avec exceptions typées

## Table de base de données

La classe mappe la table `utilisateurs` avec les colonnes :
- `id` (UUID, clé primaire)
- `prenom`, `nom` (VARCHAR, obligatoires)
- `email` (VARCHAR, unique, optionnel)
- `login` (VARCHAR, unique, obligatoire)
- `date_de_naissance` (DATE, optionnel)
- `sexe` (ENUM: 'M', 'F', 'Autre')
- `id_discord` (VARCHAR, optionnel)
- `pseudonyme` (VARCHAR, optionnel)
- `mot_de_passe` (VARCHAR, hash bcrypt)
- `image` (VARCHAR, chemin fichier)
- `type_utilisateur` (ENUM: 'NON_INSCRIT', 'INSCRIT', 'ADMINISTRATEUR')
- `date_inscription` (DATE, optionnel)
- `ancien_utilisateur` (BOOLEAN)
- `premiere_connexion` (BOOLEAN)
- `date_creation` (DATETIME)

## Cas d'usage

### Création d'un compte utilisateur
```php
$nouvelUtilisateur = new Utilisateur(
    prenom: 'Marie',
    nom: 'Martin',
    email: 'marie.martin@email.com',
    nomUtilisateur: 'mmartin',
    motDePasse: 'motdepasse123',
    sexe: Sexe::Feminin,
    typeUtilisateur: TypeUtilisateur::Inscrit
);
$nouvelUtilisateur->save();
```

### Authentification
```php
$utilisateurConnecte = $utilisateur->checkLogin('mmartin', 'motdepasse123');
if ($utilisateurConnecte) {
    // Connexion réussie
    $_SESSION['user_id'] = $utilisateurConnecte->getId();
}
```

### Recherche d'administrateurs
```php
$admins = Utilisateur::search(
    $pdo, 
    typeUtilisateur: TypeUtilisateur::Administrateur->value
);
```

La classe `Utilisateur` constitue le cœur du système d'authentification et de gestion des profils utilisateur de l'application, avec une architecture robuste supportant différents types d'utilisateurs et une gestion complète des images de profil.
