# Architecture de la base de données

Ce module contient l'architecture complète de gestion de la base de données du projet, organisée autour d'une approche orientée objet avec un ORM personnalisé.

!!! info "Architecture ORM"
    Le système utilise une approche ORM simplifiée avec une classe par table, toutes héritant de `DefaultDatabaseType` pour un comportement uniforme.

## Structure du module

```
App/Database/
├── Database.php              # Classe principale de connexion
├── config.php               # Configuration de connexion
├── database_creation.sql    # Script de création des tables
├── gen_test_data_v1.sql     # Données de test
├── Traits/
│   └── CacheableTrait.php   # Trait pour mise en cache
└── Types/
    ├── DefaultDatabaseType.php # Classe de base abstraite
    ├── Activite.php           # Gestion des activités
    ├── Session.php            # Gestion des sessions
    ├── Utilisateur.php        # Gestion des utilisateurs
    ├── Jeu.php               # Gestion des jeux
    ├── Genre.php             # Gestion des genres
    ├── Lieu.php              # Gestion des lieux
    ├── Evenement.php         # Gestion des événements
    ├── JoueursSession.php    # Inscriptions aux sessions
    ├── MembreActivite.php    # Membres autorisés pour activités fermées
    ├── CreneauxUtilisateur.php # Disponibilités utilisateurs
    ├── HorairesLieu.php      # Horaires des lieux
    ├── NotificationsHelloasso.php # Notifications Helloasso
    └── PaiementsHelloasso.php     # Paiements Helloasso
```

## Principe de fonctionnement

!!! note "Architecture ORM simplifiée"
    Le système suit les principes suivants :
    
    - **Une classe par table** : Chaque table de la base de données a sa classe PHP correspondante
    - **Héritage commun** : Toutes les classes héritent de `DefaultDatabaseType`
    - **Méthodes standardisées** : CRUD (Create, Read, Update, Delete) uniforme
    - **Mise en cache** : Utilisation du `CacheableTrait` pour optimiser les performances

### Flux de données

```mermaid
graph LR
    A[Requête] --> B[Database.php]
    B --> C[Types/*.php]
    C --> D[CacheableTrait]
    D --> E[Base de données]
    E --> F[Objets PHP]
```

1. **Connexion** : `Database.php` + `config.php` établissent la connexion MariaDB
2. **Requêtes** : Les classes `Types/` exécutent les opérations sur leurs tables respectives
3. **Cache** : `CacheableTrait` stocke les résultats fréquents en mémoire
4. **Retour** : Les données sont retournées sous forme d'objets PHP ou tableaux

### Types de fichiers
- **`.php`** : Classes et logique métier
- **`.sql`** : Scripts de structure et données de test

## Navigation

- [Database.php](Database.md) - Classe principale de connexion
- [config.php](config.md) - Configuration de connexion
- [database_creation.sql](database_creation.md) - Script de création
- [gen_test_data_v1.sql](gen_test_data_v1.md) - Données de test
- [Traits/](Traits/index.md) - Traits réutilisables
- [Types/](Types/index.md) - Classes représentant les tables

---

**Voir aussi :** [Documentation générale de la base de données](../Database.md) pour le schéma relationnel complet.
