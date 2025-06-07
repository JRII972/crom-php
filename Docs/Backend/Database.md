# Documentation de la base de données `lbdr_db`

Cette documentation décrit la structure de la base de données MariaDB **lbdr_db**, utilisée pour gérer :

- Les utilisateurs et leurs statuts
- Le catalogue de jeux et de genres
- Les propositions d'activités (campagnes, one-shots, jeux de société, événements)
- Les sessions de jeu (rendez-vous)
- Les lieux et leurs horaires de disponibilité
- Les disponibilités/indisponibilités des utilisateurs
- Les événements de l'association
- Les intégrations avec l'API Helloasso (notifications et paiements)
- L'authentification avec tokens de rafraîchissement

---

## Schéma relationnel et interactions entre tables

La base de données est organisée autour de **trois entités centrales** qui interagissent pour gérer l'activité de l'association :

### 🎯 Flux principal : Utilisateur → Activité → Session → Inscription

1. **`utilisateurs`** - Membres de l'association (joueurs, maîtres de jeu, administrateurs)
2. **`activites`** - Propositions de campagnes, one-shots, jeux de société ou événements
3. **`sessions`** - Rendez-vous concrets liés à une activité (date, heure, lieu)
4. **`joueurs_session`** - Inscriptions des membres aux sessions

### 🔄 Relations clés

- **Utilisateur ↔ Activité** : Un utilisateur peut être maître de jeu d'une activité
- **Activité ↔ Session** : Une activité peut avoir plusieurs sessions (relation 1:N)
- **Session ↔ Joueur** : Une session peut accueillir plusieurs joueurs (relation N:N via `joueurs_session`)
- **Jeu ↔ Genre** : Un jeu peut avoir plusieurs genres (relation N:N via `jeux_genres`)
- **Lieu ↔ Session** : Un lieu peut accueillir plusieurs sessions
- **Authentification** : `refresh_tokens` gère les connexions sécurisées

## Relation spéciale : Activité ↔ Session

### Fonctionnement
Une **activité** est une proposition générale (ex: "Campagne D&D", "Soirée Catan"), tandis qu'une **session** est un rendez-vous précis (date, heure, lieu). Cette séparation permet :

- **Flexibilité** : Une campagne peut avoir plusieurs sessions étalées dans le temps
- **Gestion indépendante** : Chaque session a ses propres participants et contraintes
- **Suivi** : États différents pour l'activité globale et chaque session

### Exemples concrets

**Exemple 1 - Campagne JdR :**
```
Activité : "Les Mines de la Moria" (CAMPAGNE, OUVERTE)
├── Session 1 : "Prologue" - 15/06/2025 14h-18h - Local A
├── Session 2 : "L'entrée des mines" - 29/06/2025 14h-18h - Local A  
└── Session 3 : "Combat final" - 13/07/2025 14h-18h - Local B
```

**Exemple 2 - Soirée jeux de société :**
```
Activité : "Soirée Euro-games" (JEU_DE_SOCIETE)
└── Session 1 : "Soirée du vendredi" - 20/06/2025 19h-23h - Local C
```

---

## 1. Table `utilisateurs`

Stocke les membres de l'association (NON_INSCRIT, INSCRIT, ADMINISTRATEUR).

| Colonne                | Type                                     | Description                                        |
|------------------------|------------------------------------------|----------------------------------------------------|
| `id`                   | VARCHAR(36) PRIMARY KEY                  | Identifiant UUID de l'utilisateur                  |
| `prenom`               | VARCHAR(255) NOT NULL                    | Prénom                                             |
| `nom`                  | VARCHAR(255) NOT NULL                    | Nom                                                |
| `login`                | VARCHAR(255) NOT NULL UNIQUE            | Nom d'utilisateur unique                           |
| `date_de_naissance`    | DATE NULL                                | Date de naissance (nullable)                      |
| `sexe`                 | ENUM('M','F','Autre') NOT NULL           | Sexe                                               |
| `id_discord`           | VARCHAR(255) UNIQUE                      | Identifiant Discord (facultatif, unique)          |
| `pseudonyme`           | VARCHAR(255)                             | Pseudonyme (facultatif)                            |
| `email`                | VARCHAR(255) UNIQUE                      | Email unique (nullable)                            |
| `mot_de_passe`         | VARCHAR(255) NOT NULL                    | Hash du mot de passe                               |
| `image`                | VARCHAR(512)                             | URL de l'image de profil                           |
| `type_utilisateur`     | ENUM('NON_INSCRIT','INSCRIT','ADMINISTRATEUR') NOT NULL DEFAULT 'INSCRIT' | Statut de l'utilisateur |
| `date_inscription`     | DATE                                     | Date d'inscription à l'association                 |
| `age`                  | INT VIRTUAL                              | Âge calculé automatiquement                        |
| `annees_anciennete`    | INT VIRTUAL                              | Ancienneté (années depuis `date_inscription`)      |
| `ancien_utilisateur`   | BOOLEAN NOT NULL DEFAULT FALSE           | Indication d'un compte généré depuis l'ancienne plateforme |
| `premiere_connexion`   | BOOLEAN NOT NULL DEFAULT TRUE            | Indication d'un compte créé, dont l'utilisateur ne s'est jamais connecté |
| `date_creation`        | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Date de création du compte                     |

### Colonnes virtuelles calculées automatiquement :
- **`age`** : Calcule l'âge en années à partir de `date_de_naissance`
- **`annees_anciennete`** : Calcule l'ancienneté en années depuis `date_inscription`

## 2. Table `refresh_tokens`

Gestion des tokens de rafraîchissement pour l'authentification JWT.

| Colonne            | Type                | Description                            |
|--------------------|---------------------|----------------------------------------|
| `id`               | VARCHAR(36) PRIMARY KEY | Identifiant du token               |
| `id_utilisateur`   | VARCHAR(36) NOT NULL    | Référence vers `utilisateurs.id`  |
| `token`            | VARCHAR(255) NOT NULL UNIQUE | Token de rafraîchissement unique |
| `date_expiration`  | DATETIME NOT NULL       | Date d'expiration du token         |
| `date_creation`    | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Date de création          |

**Clés étrangères :**
- `id_utilisateur` → `utilisateurs.id` (ON DELETE CASCADE)

## 3. Table `jeux`

Catalogue des jeux disponibles pour les activités.

| Colonne        | Type                | Description                            |
|----------------|---------------------|----------------------------------------|
| `id`           | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du jeu       |
| `nom`          | VARCHAR(255) NOT NULL UNIQUE    | Nom du jeu               |
| `description`  | TEXT                | Description générale                   |
| `image`        | VARCHAR(512)        | URL de l'image du jeu                  |
| `icon`         | VARCHAR(512)        | URL de l'icône du jeu                  |
| `type_jeu`     | ENUM('JDR','JEU_DE_SOCIETE','AUTRE') NOT NULL DEFAULT 'AUTRE' | Type de jeu |

## 4. Table `genres`

Liste des catégories (genres) pour filtrer les jeux.

| Colonne    | Type       | Description               |
|------------|------------|---------------------------|
| `id`       | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du genre |
| `nom`      | VARCHAR(100) NOT NULL UNIQUE   | Nom du genre         |

**Données pré-insérées :** `Fantastique`, `Horreur`, `Exploration`, `Science-fiction`, `Historique`.

## 5. Table `jeux_genres`

Relation N–à–N entre `jeux` et `genres`.

| Colonne      | Type | Description               |
|--------------|------|---------------------------|
| `id_jeu`     | INT NOT NULL | Référence vers `jeux.id`   |
| `id_genre`   | INT NOT NULL | Référence vers `genres.id` |

**Clé primaire composite :** (`id_jeu`, `id_genre`)

## 6. Table `lieux`

Lieux où peuvent avoir lieu les sessions.

| Colonne        | Type                | Description                       |
|----------------|---------------------|-----------------------------------|
| `id`           | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du lieu |
| `nom`          | VARCHAR(255) NOT NULL | Nom du lieu                |
| `short_nom`    | VARCHAR(5)          | Nom court/abrégé                  |
| `adresse`      | VARCHAR(255)        | Adresse                           |
| `latitude`     | DECIMAL(10,8)       | Latitude GPS                      |
| `longitude`    | DECIMAL(11,8)       | Longitude GPS                     |
| `description`  | TEXT                | Description / remarques           |

## 7. Table `evenements`

Événements de l'association indépendants des sessions.

| Colonne              | Type                | Description                                                            |
|----------------------|---------------------|------------------------------------------------------------------------|
| `id`                 | INT AUTO_INCREMENT PRIMARY KEY | Identifiant de l'événement                          |
| `nom`                | VARCHAR(255) NOT NULL  | Titre de l'événement                                              |
| `description`        | TEXT                  | Détails de l'événement                                             |
| `date_debut`         | DATE NOT NULL         | Date de début                                                      |
| `date_fin`           | DATE NOT NULL         | Date de fin                                                        |
| `id_lieu`            | INT                   | Lien vers `lieux.id` (facultatif)                                  |
| `regle_recurrence`   | JSON                  | Règle de récurrence (ex. `{ "byDay": ["LU","ME"], "interval": 2 }`) |
| `exceptions`         | JSON                  | Dates ou plages à exclure (ex. `{ "dates": ["2025-05-01"], "intervals": [{"start":"2025-05-10","end":"2025-05-12"}] }`) |
| `date_creation`      | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de création       |

**Clés étrangères :**
- `id_lieu` → `lieux.id` (ON DELETE SET NULL)

## 8. Table `activites`

Propositions d'activités (scénarios) : campagnes, one-shots, jeux de société, événements.

| Colonne                  | Type                                               | Description                                    |
|--------------------------|----------------------------------------------------|------------------------------------------------|
| `id`                     | INT AUTO_INCREMENT PRIMARY KEY                     | Identifiant de l'activité                     |
| `nom`                    | VARCHAR(255) NOT NULL                              | Nom de l'activité                             |
| `etat`                   | ENUM('ACTIVE','FERMER','TERMINER','ANNULER','SUPPRIMER') NOT NULL DEFAULT 'ACTIVE' | État de l'activité |
| `id_jeu`                 | INT NOT NULL                                       | Référence vers `jeux.id`                       |
| `id_maitre_jeu`          | VARCHAR(36) NOT NULL                               | Utilisateur maître du scénario (`utilisateurs.id`) |
| `type_activite`          | ENUM('CAMPAGNE','ONESHOT','JEU_DE_SOCIETE','EVENEMENT') NOT NULL | Type de la proposition                        |
| `type_campagne`          | ENUM('OUVERTE','FERMEE') DEFAULT NULL              | Pour CAMPAGNE : ouverture de la campagne       |
| `description_courte`     | VARCHAR(255)                                       | Résumé court                                   |
| `description`            | TEXT                                               | Description détaillée                          |
| `nombre_max_joueurs`     | INT DEFAULT 0                                      | Nombre max de joueurs dans l'activité         |
| `max_joueurs_session`    | INT DEFAULT 5                                      | Nombre max de joueurs par session             |
| `verrouille`             | BOOLEAN NOT NULL DEFAULT FALSE                     | Activité verrouillée aux inscriptions         |
| `image`                  | VARCHAR(512)                                       | URL d'image                                    |
| `texte_alt_image`        | VARCHAR(255)                                       | Texte alternatif pour l'image                  |
| `date_creation`          | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP       | Date de création                               |

**Clés étrangères :**
- `id_jeu` → `jeux.id` (ON DELETE CASCADE)
- `id_maitre_jeu` → `utilisateurs.id` (ON DELETE CASCADE)

## 9. Table `membres_activite`

Whitelist pour campagnes fermées.

| Colonne            | Type        | Description                    |
|--------------------|-------------|--------------------------------|
| `id_activite`      | INT NOT NULL| Référence vers `activites.id`  |
| `id_utilisateur`   | VARCHAR(36) NOT NULL | Référence vers `utilisateurs.id` |

**Clé primaire composite :** (`id_activite`, `id_utilisateur`)

## 10. Table `sessions`

Sessions (rendez-vous) liées à une activité.

| Colonne              | Type                | Description                                       |
|----------------------|---------------------|---------------------------------------------------|
| `id`                 | INT AUTO_INCREMENT PRIMARY KEY | Identifiant de session          |
| `nom`                | VARCHAR(255) NOT NULL | Nom de la session                             |
| `etat`               | ENUM('OUVERTE','FERMER','COMPLETE','ANNULER','SUPPRIMER') NOT NULL DEFAULT 'OUVERTE' | État de la session |
| `id_activite`        | INT NOT NULL        | Référence vers `activites.id`                 |
| `id_lieu`            | INT NOT NULL        | Référence vers `lieux.id`                     |
| `date_session`       | DATE NOT NULL       | Date de la session                             |
| `nombre_max_joueurs` | INT NOT NULL DEFAULT 5 | Nombre max de joueurs                       |
| `heure_debut`        | TIME NOT NULL       | Heure de début                                 |
| `heure_fin`          | TIME NOT NULL       | Heure de fin                                   |
| `id_maitre_jeu`      | VARCHAR(36) NOT NULL| Maître de jeu de la session (`utilisateurs.id`) |

**Clés étrangères :**
- `id_activite` → `activites.id` (ON DELETE CASCADE)
- `id_lieu` → `lieux.id` (ON DELETE CASCADE)
- `id_maitre_jeu` → `utilisateurs.id` (ON DELETE CASCADE)

## 11. Table `joueurs_session`

Inscriptions des joueurs aux sessions.

| Colonne              | Type        | Description                    |
|----------------------|-------------|--------------------------------|
| `id_session`         | INT NOT NULL| Référence vers `sessions.id`   |
| `id_utilisateur`     | VARCHAR(36) NOT NULL | Référence vers `utilisateurs.id` |
| `date_inscription`   | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Date d'inscription |

**Clé primaire composite :** (`id_session`, `id_utilisateur`)

## 12. Table `horaires_lieu`

Horaires de disponibilité des lieux avec récurrence et exceptions.

| Colonne              | Type                                                    | Description                                                     |
|----------------------|---------------------------------------------------------|-----------------------------------------------------------------|
| `id`                 | INT AUTO_INCREMENT PRIMARY KEY                          | Identifiant du créneau                                         |
| `id_lieu`            | INT NOT NULL                                            | Référence vers `lieux.id`                                      |
| `heure_debut`        | TIME NOT NULL                                           | Heure de début du créneau                                      |
| `heure_fin`          | TIME NOT NULL                                           | Heure de fin du créneau                                        |
| `type_recurrence`    | ENUM('AUCUNE','QUOTIDIENNE','HEBDOMADAIRE','MENSUELLE','ANNUELLE') NOT NULL DEFAULT 'AUCUNE' | Type de récurrence                   |
| `regle_recurrence`   | JSON                                                    | Détails de la récurrence (idem `evenements`)                   |
| `exceptions`         | JSON                                                    | Dates/plages à exclure                                         |
| `id_evenement`       | INT                                                     | Override pour un événement spécifique (référence `evenements.id`) |

**Clés étrangères :**
- `id_lieu` → `lieux.id` (ON DELETE CASCADE)
- `id_evenement` → `evenements.id` (ON DELETE SET NULL)

## 13. Table `creneaux_utilisateur`

Disponibilités / Indisponibilités des utilisateurs.

| Colonne              | Type                                        | Description                                                 |
|----------------------|---------------------------------------------|-------------------------------------------------------------|
| `id`                 | INT AUTO_INCREMENT PRIMARY KEY              | Identifiant du créneau                                     |
| `id_utilisateur`     | VARCHAR(36) NOT NULL                        | Référence vers `utilisateurs.id`                           |
| `type_creneau`       | ENUM('DISPONIBILITE','INDISPONIBILITE') NOT NULL | Type de créneau                                          |
| `date_heure_debut`   | DATETIME NOT NULL                           | Début du créneau                                            |
| `date_heure_fin`     | DATETIME NOT NULL                           | Fin du créneau                                              |
| `est_recurrant`      | BOOLEAN NOT NULL DEFAULT FALSE              | Flag de récurrence                                          |
| `regle_recurrence`   | TEXT                                        | Règle iCal RRULE si prise en charge côté base               |

**Clés étrangères :**
- `id_utilisateur` → `utilisateurs.id` (ON DELETE CASCADE)

## 14. Vues

### Vue `statistiques_utilisateur`
Affiche les informations de base avec colonnes calculées.

```sql
SELECT id, prenom, nom, age, annees_anciennete
FROM utilisateurs;
```

### Vue `compte_inscriptions_session`
Compte le nombre d'inscrits par session.

```sql
SELECT 
  s.id AS id_session,
  COUNT(sp.id_utilisateur) AS nombre_joueurs_inscrits
FROM sessions s
LEFT JOIN joueurs_session sp ON sp.id_session = s.id
GROUP BY s.id;
```

## 15. Intégration Helloasso

### Table `notifications_helloasso`
Stocke en brut les webhooks reçus de Helloasso.

| Colonne            | Type                   | Description                                                 |
|--------------------|------------------------|-------------------------------------------------------------|
| `id`               | VARCHAR(100) PRIMARY KEY | Identifiant de la notification                            |
| `type_evenement`   | VARCHAR(100) NOT NULL  | Type d'événement Helloasso                                 |
| `date_evenement`   | DATETIME NOT NULL      | Date/heure de l'événement                                   |
| `donnees`          | JSON NOT NULL          | Données brutes du webhook                                   |
| `date_reception`   | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de réception  |
| `traite`           | BOOLEAN NOT NULL DEFAULT FALSE | Flag de traitement                                  |

### Table `paiements_helloasso`
Détails des paiements et échéances extraits des notifications.

| Colonne              | Type                   | Description                                                 |
|----------------------|------------------------|-------------------------------------------------------------|
| `id`                 | VARCHAR(100) PRIMARY KEY | Identifiant du paiement                                   |
| `id_notification`    | VARCHAR(100)           | Référence vers `notifications_helloasso.id`                |
| `id_utilisateur`     | VARCHAR(36)            | Référence vers `utilisateurs.id`                           |
| `type_paiement`      | VARCHAR(100)           | Type de paiement Helloasso (ex. `Paiement`, `Remboursement`) |
| `nom`                | VARCHAR(255)           | Nom ou description du paiement                             |
| `montant`            | DECIMAL(10,2) NOT NULL | Montant                                                    |
| `devise`             | VARCHAR(10) NOT NULL   | Devise (ex. `EUR`)                                         |
| `date_echeance`      | DATE                   | Date d'échéance ou de paiement                             |
| `statut`             | VARCHAR(50)            | Statut (`EN_ATTENTE`, `ECHEC`, `COMPLETE`, …)              |
| `metadonnees`        | JSON                   | Données complémentaires                                    |
| `date_creation`      | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de création          |

**Clés étrangères :**
- `id_notification` → `notifications_helloasso.id`
- `id_utilisateur` → `utilisateurs.id`

---

## Encodage et Configuration

- **Base de données :** `lbdr_db`
- **Encodage :** UTF8MB4 avec collation `utf8mb4_unicode_ci`
- **Moteur :** InnoDB pour toutes les tables
- **Support des transactions et contraintes d'intégrité référentielle**

## Principales Améliorations par rapport à l'ancienne version

1. **Nouvelle table `refresh_tokens`** pour la gestion sécurisée de l'authentification JWT
2. **Champs étendus** dans `utilisateurs` : `image` pour les avatars
3. **Champs étendus** dans `jeux` : `image` et `icon` pour le catalogue visuel
4. **Champs étendus** dans `lieux` : `short_nom` pour les abréviations
5. **Champs étendus** dans `activites` et `sessions` : `nom` et `etat` pour une meilleure gestion des états
6. **Amélioration des contraintes** : unicité renforcée sur `login` et `email`
7. **Gestion des états** : enum pour les statuts des activités et sessions
8. **Suppression de** `periodes_association` (non utilisée dans le code actuel)
9. **Changement de nom** : `url_image` → `image` pour cohérence
