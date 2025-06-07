# Documentation du répertoire Types

## Vue d'ensemble

Le répertoire `/var/www/html/App/Database/Types/` contient toutes les classes d'entités de l'application qui représentent les tables de la base de données. Ces classes héritent toutes de `DefaultDatabaseType` et implémentent un pattern ORM (Object-Relational Mapping) personnalisé.

## Architecture

### Classe de base
- **[DefaultDatabaseType-base.md](DefaultDatabaseType-base.md)** - Classe abstraite de base pour tous les types de base de données

### Classes d'entités principales

#### Gestion des utilisateurs et activités
- **[Utilisateur-user.md](Utilisateur-user.md)** - Gestion des utilisateurs, authentification et profils
- **[Activite-main.md](Activite-main.md)** - Gestion des activités de jeu (campagnes, événements)
- **[Session-core.md](Session-core.md)** - Sessions de jeu avec gestion des inscriptions

#### Gestion du contenu ludique
- **[Jeu.md](Jeu.md)** - Catalogue des jeux avec métadonnées complètes
- **[Genre.md](Genre.md)** - Classification des genres de jeux

#### Gestion géographique et temporelle
- **[Lieu.md](Lieu.md)** - Lieux avec coordonnées GPS et informations pratiques
- **[Evenement.md](Evenement.md)** - Événements avec récurrence et exceptions
- **[HorairesLieu.md](HorairesLieu.md)** - Horaires d'ouverture des lieux avec récurrence

#### Relations et associations
- **[JoueursSession.md](JoueursSession.md)** - Association utilisateurs-sessions (relation N:N)
- **[MembreActivite.md](MembreActivite.md)** - Whitelist pour activités fermées
- **[CreneauxUtilisateur.md](CreneauxUtilisateur.md)** - Disponibilités des utilisateurs

#### Intégration externe
- **[PaiementsHelloasso.md](PaiementsHelloasso.md)** - Gestion des paiements HelloAsso
- **[NotificationsHelloasso.md](NotificationsHelloasso.md)** - Webhooks HelloAsso

## Caractéristiques communes

### Héritage de DefaultDatabaseType
Toutes les classes héritent de `DefaultDatabaseType` qui fournit :
- Singleton de connexion PDO
- Méthodes CRUD de base (`save()`, `delete()`)
- Gestion automatique des UUIDs
- Validation des données d'entrée
- Sérialisation JSON

### Énumérations typées
L'application utilise intensivement les enums PHP 8.1+ pour :
- Type safety au niveau du code
- Validation automatique des valeurs
- Documentation auto-générée des valeurs possibles
- Prévention des erreurs de saisie

Exemples d'enums utilisés :
- `TypeJeu`, `TypeActivite`, `EtatActivite`
- `TypeCreneau`, `TypeRecurrence`
- `Sexe`, `TypeUtilisateur`
- `TypePaiement`, `EtatPaiement`

### Pattern de construction
Les constructeurs supportent généralement deux modes :
1. **Mode chargement** : `new Class($id)` - Charge depuis la base
2. **Mode création** : `new Class(param1, param2, ...)` - Crée une nouvelle instance

### Caching intégré
Certaines classes utilisent `CacheableTrait` pour :
- Cache APCu pour les requêtes fréquentes
- Invalidation automatique lors des modifications
- Performance optimisée pour les données référentielles

### Sérialisation JSON
Toutes les classes implémentent `JsonSerializable` avec :
- Sérialisation publique (APIs externes)
- Sérialisation privée (administration)
- Gestion des relations et objets complexes

## Relations entre entités

### Relations principales
- **Utilisateur** ↔ **Session** (via JoueursSession)
- **Activite** → **Session** (1:N)
- **Lieu** → **Session** (1:N)
- **Jeu** → **Activite** (1:N)
- **Utilisateur** → **CreneauxUtilisateur** (1:N)
- **Lieu** → **HorairesLieu** (1:N)

### Relations de gestion
- **Activite** → **MembreActivite** (whitelist)
- **Utilisateur** → **PaiementsHelloasso** (1:N)
- **HelloAsso** → **NotificationsHelloasso** (webhooks)

## Fonctionnalités avancées

### Gestion temporelle
- Récurrences avec exceptions JSON
- Fuseaux horaires et dates locales
- Calculs d'âge et d'ancienneté automatiques

### Géolocalisation
- Coordonnées GPS avec validation
- Calculs de distance (prévu)
- Adresses formatées

### Recherche et filtrage
- Recherches multi-critères optimisées
- Filtres par état, type, date
- Pagination et tri automatiques

### Intégrations externes
- HelloAsso pour les paiements
- Discord pour l'identification
- Upload d'images avec redimensionnement

Ce répertoire Types constitue le cœur métier de l'application, avec une architecture ORM robuste supportant toutes les fonctionnalités de gestion d'activités ludiques, d'utilisateurs et de paiements.