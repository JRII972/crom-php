# Documentation de la base de données `jdr_assoc`

Cette documentation décrit la structure de la base de données MariaDB **jdr_assoc**, utilisée pour gérer :

- Les utilisateurs et leurs statuts
- Le catalogue de jeux et de genres
- Les propositions de parties (campagnes, one-shots, jeux de société, événements)
- Les sessions de jeu (rendez-vous)
- Les lieux et leurs horaires de disponibilité
- Les disponibilités/indisponibilités des utilisateurs
- Les périodes d’ouverture/fermeture de l’association
- Les événements de l’association
- Les intégrations avec l’API Helloasso (notifications et paiements)

---

## 1. Table `users`

Stocke les membres de l’association (NON_REGISTERED, REGISTERED, ADMINISTRATOR).

| Colonne             | Type                                     | Description                                        |
|---------------------|------------------------------------------|----------------------------------------------------|
| `id`                | VARCHAR(36) PRIMARY KEY                  | Identifiant UUID de l’utilisateur                  |
| `first_name`        | VARCHAR(255) NOT NULL                    | Prénom                                             |
| `last_name`         | VARCHAR(255) NOT NULL                    | Nom                                                |
| `birth_date`        | DATE NOT NULL                            | Date de naissance                                  |
| `sex`               | ENUM('M','F','Other') NOT NULL           | Sexe                                               |
| `discord_id`        | VARCHAR(255) UNIQUE                      | Identifiant Discord (facultatif)                   |
| `pseudonym`         | VARCHAR(255)                             | Pseudonyme (facultatif)                            |
| `password_hash`     | VARCHAR(255) NOT NULL                    | Hash du mot de passe                                |
| `password_salt`     | VARCHAR(255)                             | Salt utilisé pour le hachage  |
| `user_type`         | ENUM('NON_REGISTERED','REGISTERED','ADMINISTRATOR') NOT NULL DEFAULT 'REGISTERED' | Statut de l’utilisateur         |
| `registration_date` | DATE                                     | Date d’inscription à l’association                 |
| `age`               | INT VIRTUAL                              | Âge calculé automatiquement                        |
| `seniority_years`   | INT VIRTUAL                              | Ancienneté (années depuis `registration_date`)     |
| `old_user`   | BOOLEAN                              | Indication d'un compte généré depuis l'ancienne plateforme. L'utilisateur doit se connecter pour confirmer sa transition    |
| `first_connection`   | BOOLEAN                              | Indication d'un compte crée, dont l'utilisateur ne c'est jamais connecter     |
| `lifetime_membership`   | BOOLEAN                              | Sera considéré comme n'ayant pas besoin de cotiser     |
| `created_at`        | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Date de création du compte                    |

## 2. Table `games` Table `games`

Catalogue des jeux disponibles pour les parties.

| Colonne     | Type                | Description                            |
|-------------|---------------------|----------------------------------------|
| `id`        | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du jeu       |
| `name`      | VARCHAR(255) NOT NULL UNIQUE    | Nom du jeu               |
| `description` | TEXT              | Description générale                |
| `type` | ENUM('JDR','BOARD_GAME','OTHER')              | Type de jeux                |


## 3. Table `genres`

Liste des catégories (genres) pour filtrer les jeux.

| Colonne    | Type       | Description               |
|------------|------------|---------------------------|
| `id`       | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du genre |
| `name`     | VARCHAR(100) NOT NULL UNIQUE   | Nom du genre         |

Exemples insérés : `Fantastique`, `Horreur`, `Exploration`, `Science-fiction`, `Historique`.


## 4. Table `game_genres`

Relation N–à–N entre `games` et `genres`.
- `game_id` → `games.id`
- `genre_id` → `genres.id`


## 5. Table `locations`

Lieux où peuvent avoir lieu les sessions.

| Colonne      | Type                | Description                       |
|--------------|---------------------|-----------------------------------|
| `id`         | INT AUTO_INCREMENT PRIMARY KEY | Identifiant du lieu |
| `name`       | VARCHAR(255) NOT NULL | Nom du lieu                |
| `address`    | VARCHAR(255)        | Adresse                     |
| `latitude`   | DECIMAL(10,8)       | Latitude GPS                |
| `longitude`  | DECIMAL(11,8)       | Longitude GPS               |
| `description`| TEXT                | Description / remarques      |


## 6. Table `events`

Événements de l’association indépendants des sessions.

| Colonne              | Type                | Description                                                            |
|----------------------|---------------------|------------------------------------------------------------------------|
| `id`                 | INT AUTO_INCREMENT PRIMARY KEY | Identifiant de l’événement                          |
| `name`               | VARCHAR(255) NOT NULL  | Titre de l’événement                                              |
| `description`        | TEXT                  | Détails de l’événement                                             |
| `start_date`         | DATE NOT NULL         | Date de début                                                      |
| `end_date`           | DATE NOT NULL         | Date de fin                                                        |
| `location_id`        | INT                   | Lien vers `locations.id` (facultatif)                              |
| `recurrence_pattern` | JSON                  | Règle de récurrence (ex. `{ "byDay": ["MO","WE"], "interval": 2 }`) |
| `exceptions`         | JSON                  | Dates ou plages à exclure (ex. `{ "dates": ["2025-05-01"], "intervals": [{"start":"2025-05-10","end":"2025-05-12"}] }`) |
| `created_at`         | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de création       |


## 7. Table `association_periods`

Périodes d’ouverture/fermeture de l’association.

| Colonne      | Type       | Description                  |
|--------------|------------|------------------------------|
| `id`         | INT AUTO_INCREMENT PRIMARY KEY | Identifiant de période |
| `open_date`  | DATE NOT NULL | Début de la période        |
| `close_date` | DATE NOT NULL | Fin de la période          |


## 8. Table `partie`

Propositions de parties (scénarios) : campagnes, one-shots, jeux de société, événements.

| Colonne             | Type                                               | Description                                    |
|---------------------|----------------------------------------------------|------------------------------------------------|
| `id`                | INT AUTO_INCREMENT PRIMARY KEY                     | Identifiant de la partie                       |
| `game_id`           | INT NOT NULL                                        | Référence vers `games.id`                      |
| `mj_id`       | VARCHAR(36) NOT NULL                                | Utilisateur maître du scénario (`users.id`)    |
| `partie_type`  | ENUM('CAMPAIGN','ONESHOT','BOARD_GAME','EVENT')     | Type de la proposition                         |
| `campaign_type`     | ENUM('OPEN','CLOSED') DEFAULT NULL                  | Pour CAMPAIGN : ouverture de la campagne       |
| `short_description` | VARCHAR(255)                                        | Résumé court                                   |
| `description`       | TEXT                                                | Description détaillée                          |
| `max_players`       | INT DEFAULT 0                                       | Nombre max de joueurs                          |
| `locked`            | BOOLEAN AS (FALSE) VIRTUAL                          | Colonne virtuelle (ex. à calculer en requête)  |
| `image_url`         | VARCHAR(512)                                        | URL d’image                                     |
| `image_alt`         | VARCHAR(255)                                        | Texte alternatif pour l’image                  |
| `created_at`        | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP        | Date de création                               |


## 9. Table `partie_members`

Whitelist pour campagnes fermées.
- `partie_id` → `partie.id`
- `user_id` → `users.id`


## 10. Table `sessions`

Sessions (rendez-vous) liées à une proposition.

| Colonne        | Type                | Description                                       |
|----------------|---------------------|---------------------------------------------------|
| `id`           | INT AUTO_INCREMENT PRIMARY KEY | Identifiant de session          |
| `partie_id`    | INT NOT NULL        | Référence vers `partie.id`                         |
| `location_id`  | INT NOT NULL        | Référence vers `locations.id`                      |
| `session_date` | DATE NOT NULL       | Date de la session                                 |
| `start_time`   | TIME NOT NULL       | Heure de début                                     |
| `end_time`     | TIME NOT NULL       | Heure de fin                                       |
| `mj_id`        | VARCHAR(36) NOT NULL| Maître de jeu de la session (`users.id`)           |


## 11. Table `session_players`

Inscriptions des joueurs aux sessions.
- `session_id` → `sessions.id`
- `user_id` → `users.id`


## 12. Table `location_schedule`

Horaires de disponibilité des lieux avec récurrence et exceptions.

| Colonne             | Type                                                    | Description                                                     |
|---------------------|---------------------------------------------------------|-----------------------------------------------------------------|
| `id`                | INT AUTO_INCREMENT PRIMARY KEY                          | Identifiant du créneau                                         |
| `location_id`       | INT NOT NULL                                            | Référence vers `locations.id`                                  |
| `start_time`        | TIME NOT NULL                                           | Heure de début du créneau                                      |
| `end_time`          | TIME NOT NULL                                           | Heure de fin du créneau                                        |
| `recurrence_type`   | ENUM('NONE','DAILY','WEEKLY','MONTHLY','YEARLY') NOT NULL DEFAULT 'NONE' | Type de récurrence                   |
| `recurrence_pattern`| JSON                                                    | Détails de la récurrence (idem `events`)                       |
| `exceptions`        | JSON                                                    | Dates/plages à exclure                                          |
| `event_id`          | INT                                                     | Override pour un événement spécifique (référence `events.id`)   |


## 13. Table `user_time_slots`

Disponibilités / Indisponibilités des utilisateurs.

| Colonne         | Type                                        | Description                                                 |
|-----------------|---------------------------------------------|-------------------------------------------------------------|
| `id`            | INT AUTO_INCREMENT PRIMARY KEY              | Identifiant du créneau                                     |
| `user_id`       | VARCHAR(36) NOT NULL                        | Référence vers `users.id`                                   |
| `slot_type`     | ENUM('AVAILABILITY','UNAVAILABILITY') NOT NULL | Type de créneau                                          |
| `start_datetime`| DATETIME NOT NULL                           | Début du créneau                                            |
| `end_datetime`  | DATETIME NOT NULL                           | Fin du créneau                                              |
| `is_recurring`  | BOOLEAN NOT NULL DEFAULT FALSE              | Flag de récurrence                                          |
| `recurrence_rule` | TEXT                                      | Règle iCal RRULE si prise en charge côté base               |


## 14. Vues

- **`user_stats`** : affiche `id`, `first_name`, `last_name`, `age`, `seniority_years` (issues des colonnes virtuelles).
- **`session_registration_count`** : nombre d’inscrits par session.


## 15. Intégration Helloasso

### Table `helloasso_notifications`
- Stocke en brut les webhooks reçus.

| Colonne        | Type                   | Description                                                 |
|----------------|------------------------|-------------------------------------------------------------|
| `id`           | VARCHAR(100) PRIMARY KEY | Identifiant de la notification                            |
| `event_type`   | VARCHAR(100) NOT NULL  | Type d’événement Helloasso                                 |
| `occurred_at`  | DATETIME NOT NULL      | Date/heure de l’événement                                   |
| `payload`      | JSON NOT NULL          | Données brutes                                              |
| `received_at`  | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de réception  |
| `processed`    | BOOLEAN NOT NULL DEFAULT FALSE | Flag de traitement                                  |

### Table `helloasso_payments`
- Détails des paiements et échéances extraits des notifications.

| Colonne           | Type                   | Description                                                 |
|-------------------|------------------------|-------------------------------------------------------------|
| `id`              | VARCHAR(100) PRIMARY KEY | Identifiant du paiement                                   |
| `notification_id` | VARCHAR(100)           | Référence vers `helloasso_notifications.id`                 |
| `user_id`         | VARCHAR(36)            | Référence vers `users.id`                                   |
| `type`            | VARCHAR(100)           | Type de paiement Helloasso (ex. `Payment`, `Refund`)       |
| `name`            | VARCHAR(255)           | Nom ou description du paiement                             |
| `amount`          | DECIMAL(10,2) NOT NULL | Montant                                                     |
| `currency`        | VARCHAR(10) NOT NULL   | Devise (ex. `EUR`)                                          |
| `due_date`        | DATE                   | Date d’échéance ou de paiement                              |
| `status`          | VARCHAR(50)            | Statut (`PENDING`, `FAILED`, `COMPLETED`, …)               |
| `metadata`        | JSON                   | Données complémentaires                                     |
| `created_at`      | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | Horodatage de création          |

---

*Fin de la documentation*

