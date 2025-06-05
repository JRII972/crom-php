# Documentation de la base de données `jdr_assoc`

Cette documentation décrit la structure de la base de données MariaDB **jdr_assoc**, utilisée pour gérer :

- Les utilisateurs et leurs statuts
- Le catalogue de jeux et de genres
- Les propositions de activites (campagnes, one-shots, jeux de société, événements)
- Les sessions de jeu (rendez-vous)
- Les lieux et leurs horaires de disponibilité
- Les disponibilités/indisponibilités des utilisateurs
- Les périodes d’ouverture/fermeture de l’association
- Les événements de l’association
- Les intégrations avec l’API Helloasso (notifications et paiements)

---

## 1. Table `utilisateurs`

Stocke les membres de l’association (NON_INSCRIT, INSCRIT, ADMINISTRATEUR).

| Colonne                | Type                                     | Description                                        |
|------------------------|------------------------------------------|----------------------------------------------------|
| `id`                   | VARCHAR(36) PRIMARY KEY                  | Identifiant UUID de l’utilisateur                  |
| `prenom`               | VARCHAR(255) NOT NULL                    | Prénom                                             |
| `nom`                  | VARCHAR(255) NOT NULL                    | Nom                                                |
| `email`                  | VARCHAR(255) NOT NULL                    | Email                                                |
| `login`                  | VARCHAR(255) NOT NULL                    | Nom d'utilisateur                                                |
| `date_de_naissance`    | DATE NOT NULL                            | Date de naissance                                  |
| `sexe`                 | ENUM('M','F','Autre') NOT NULL           | Sexe                                               |
| `id_discord`           | VARCHAR(255) UNIQUE                      | Identifiant Discord (facultatif)                   |
| `pseudonyme`           | VARCHAR(255)                             | Pseudonyme (facultatif)                            |
| `mot_de_passe`         | VARCHAR(255) NOT NULL                    | Hash du mot de passe                               |
| `type_utilisateur`     | ENUM('NON_INSCRIT','INSCRIT','ADMINISTRATEUR') NOT NULL DEFAULT 'INSCRIT' | Statut de l’utilisateur |
| `date_inscription`     | DATE                                     | Date d’inscription à l’association                 |
| `age`                  | INT VIRTUAL                              | Âge calculé automatiquement                        |
| `annees_anciennete`    | INT VIRTUAL                              | Ancienneté (années depuis `date_inscription`)      |
| `ancien_utilisateur`   | BOOLEAN                                  | Indication d'un compte généré depuis l'ancienne plateforme. L'utilisateur doit se connecter pour confirmer sa transition |
| `premiere_connexion`   | BOOLEAN                                  | Indication d'un compte créé, dont l'utilisateur ne s'est jamais connecté |
| `adhesion_a_vie`       | BOOLEAN                                  | Sera considéré comme n'ayant pas besoin de cotiser |
| `date_creation`        | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Date de création du compte                     |

## 2. Table `jeux`

Catalogue des jeux disponibles pour les activites.

| Colonne        | Type                | Description                            |
|----------------|---------------------|----------------------------------------|
| `id`           | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du jeu       |
| `nom`          | VARCHAR(255) NOT NULL UNIQUE    | Nom du jeu               |
| `description`  | TEXT                | Description générale                   |
| `type_jeu`     | ENUM('JDR','JEU_DE_SOCIETE','AUTRE') | Type de jeu                      |

## 3. Table `genres`

Liste des catégories (genres) pour filtrer les jeux.

| Colonne    | Type       | Description               |
|------------|------------|---------------------------|
| `id`       | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du genre |
| `nom`      | VARCHAR(100) NOT NULL UNIQUE   | Nom du genre         |

Exemples insérés : `Fantastique`, `Horreur`, `Exploration`, `Science-fiction`, `Historique`.

## 4. Table `jeux_genres`

Relation N–à–N entre `jeux` et `genres`.
- `id_jeu` → `jeux.id`
- `id_genre` → `genres.id`

## 5. Table `lieux`

Lieux où peuvent avoir lieu les sessions.

| Colonne        | Type                | Description                       |
|----------------|---------------------|-----------------------------------|
| `id`           | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du lieu |
| `nom`          | VARCHAR(255) NOT NULL | Nom du lieu                |
| `adresse`      | VARCHAR(255)        | Adresse                     |
| `latitude`     | DECIMAL(10,8)       | Latitude GPS                |
| `longitude`    | DECIMAL(11,8)       | Longitude GPS               |
| `description`  | TEXT                | Description / remarques      |

## 6. Table `evenements`

Événements de l’association indépendants des sessions.

| Colonne              | Type                | Description                                                            |
|----------------------|---------------------|------------------------------------------------------------------------|
| `id`                 | INT AUTO_INCREMENT PRIMARY KEY | Identifiant de l’événement                          |
| `nom`                | VARCHAR(255) NOT NULL  | Titre de l’événement                                              |
| `description`        | TEXT                  | Détails de l’événement                                             |
| `date_debut`         | DATE NOT NULL         | Date de début                                                      |
| `date_fin`           | DATE NOT NULL         | Date de fin                                                        |
| `id_lieu`            | INT                   | Lien vers `lieux.id` (facultatif)                                  |
| `regle_recurrence`   | JSON                  | Règle de récurrence (ex. `{ "byDay": ["LU","ME"], "interval": 2 }`) |
| `exceptions`         | JSON                  | Dates ou plages à exclure (ex. `{ "dates": ["2025-05-01"], "intervals": [{"start":"2025-05-10","end":"2025-05-12"}] }`) |
| `date_creation`      | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de création       |

## 7. Table `periodes_association`

Périodes d’ouverture/fermeture de l’association.

| Colonne            | Type       | Description                  |
|--------------------|------------|------------------------------|
| `id`               | INT AUTO_INCREMENT PRIMARY KEY | Identifiant de période |
| `date_ouverture`   | DATE NOT NULL | Début de la période        |
| `date_fermeture`   | DATE NOT NULL | Fin de la période          |

## 8. Table `activites`

Propositions de activites (scénarios) : campagnes, one-shots, jeux de société, événements.

| Colonne                  | Type                                               | Description                                    |
|--------------------------|----------------------------------------------------|------------------------------------------------|
| `id`                     | INT AUTO_INCREMENT PRIMARY KEY                     | Identifiant de la activite                       |
| `id_jeu`                 | INT NOT NULL                                       | Référence vers `jeux.id`                       |
| `id_maitre_jeu`          | VARCHAR(36) NOT NULL                               | Utilisateur maître du scénario (`utilisateurs.id`) |
| `type_activite`            | ENUM('CAMPAGNE','ONESHOT','JEU_DE_SOCIETE','EVENEMENT') | Type de la proposition                        |
| `type_campagne`          | ENUM('OUVERTE','FERMEE') DEFAULT NULL              | Pour CAMPAGNE : ouverture de la campagne       |
| `description_courte`     | VARCHAR(255)                                       | Résumé court                                   |
| `description`            | TEXT                                               | Description détaillée                          |
| `nombre_max_joueurs`     | INT DEFAULT 0                                      | Nombre max de joueurs                          |
| `max_joueurs_session`     | INT DEFAULT 5                                      | Nombre max de joueurs dans une session                        |
| `verrouille`             | BOOLEAN AS (FALSE) VIRTUAL                         | Colonne virtuelle (ex. à calculer en requête)  |
| `url_image`              | VARCHAR(512)                                       | URL d’image                                    |
| `texte_alt_image`        | VARCHAR(255)                                       | Texte alternatif pour l’image                  |
| `date_creation`          | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP       | Date de création                               |

## 9. Table `membres_activite`

Whitelist pour campagnes fermées.
- `id_activite` → `activites.id`
- `id_utilisateur` → `utilisateurs.id`

## 10. Table `sessions`

Sessions (rendez-vous) liées à une proposition.

| Colonne            | Type                | Description                                       |
|--------------------|---------------------|---------------------------------------------------|
| `id`               | INT AUTO_INCREMENT PRIMARY KEY | Identifiant de session          |
| `id_activite`        | INT NOT NULL        | Référence vers `activites.id`                        |
| `id_lieu`          | INT NOT NULL        | Référence vers `lieux.id`                          |
| `nombre_max_joueurs`          | INT NOT NULL DEFAULT 5       | Nombre max de joueurs                          |
| `date_session`     | DATE NOT NULL       | Date de la session                                 |
| `heure_debut`      | TIME NOT NULL       | Heure de début                                     |
| `heure_fin`        | TIME NOT NULL       | Heure de fin                                       |
| `id_maitre_jeu`    | VARCHAR(36) NOT NULL| Maître de jeu de la session (`utilisateurs.id`)    |

## 11. Table `joueurs_session`

Inscriptions des joueurs aux sessions.
- `id_session` → `sessions.id`
- `id_utilisateur` → `utilisateurs.id`

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

## 13. Table `creneaux_utilisateur`

Disponibilités / Indisponibilités des utilisateurs.

| Colonne              | Type                                        | Description                                                 |
|----------------------|---------------------------------------------|-------------------------------------------------------------|
| `id`                 | INT AUTO_INCREMENT PRIMARY KEY              | Identifiant du créneau                                     |
| `id_utilisateur`     | VARCHAR(36) NOT NULL                        | Référence vers `utilisateurs.id`                           |
| `type_creneau`       | ENUM('DISPONIBILITE','INDISPONIBILITE') NOT NULL | Type de créneau                                          |
| `date_heure_debut`   | DATETIME BARBARA NOT NULL                           | Début du créneau                                            |
| `date_heure_fin`     | DATETIME NOT NULL                           | Fin du créneau                                              |
| `est_recurrant`      | BOOLEAN NOT NULL DEFAULT FALSE              | Flag de récurrence                                          |
| `regle_recurrence`   | TEXT                                        | Règle iCal RRULE si prise en charge côté base               |

## 14. Vues

- **`statistiques_utilisateur`** : affiche `id`, `prenom`, `nom`, `age`, `annees_anciennete` (issues des colonnes virtuelles).
- **`compte_inscriptions_session`** : nombre d’inscrits par session.

## 15. Intégration Helloasso

### Table `notifications_helloasso`
- Stocke en brut les webhooks reçus.

| Colonne            | Type                   | Description                                                 |
|--------------------|------------------------|-------------------------------------------------------------|
| `id`               | VARCHAR(100) PRIMARY KEY | Identifiant de la notification                            |
| `type_evenement`   | VARCHAR(100) NOT NULL  | Type d’événement Helloasso                                 |
| `date_evenement`   | DATETIME NOT NULL      | Date/heure de l’événement                                   |
| `donnees`          | JSON NOT NULL          | Données brutes                                              |
| `date_reception`   | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de réception  |
| `traite`           | BOOLEAN NOT NULL DEFAULT FALSE | Flag de traitement                                  |

### Table `paiements_helloasso`
- Détails des paiements et échéances extraits des notifications.

| Colonne              | Type                   | Description                                                 |
|----------------------|------------------------|-------------------------------------------------------------|
| `id`                 | VARCHAR(100) PRIMARY KEY | Identifiant du paiement                                   |
| `id_notification`    | VARCHAR(100)           | Référence vers `notifications_helloasso.id`                |
| `id_utilisateur`     | VARCHAR(36)            | Référence vers `utilisateurs.id`                           |
| `type_paiement`      | VARCHAR(100)           | Type de paiement Helloasso (ex. `Paiement`, `Remboursement`) |
| `nom`                | VARCHAR(255)           | Nom ou description du paiement                             |
| `montant`            | DECIMAL(10,2) NOT NULL | Montant                                                    |
| `devise`             | VARCHAR(10) NOT NULL   | Devise (ex. `EUR`)                                         |
| `date_echeance`      | DATE                   | Date d’échéance ou de paiement                             |
| `statut`             | VARCHAR(50)            | Statut (`EN_ATTENTE`, `ECHEC`, `COMPLETE`, …)              |
| `metadonnees`        | JSON                   | Données complémentaires                                    |
| `date_creation`      | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de création          |

---
