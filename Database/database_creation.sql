-- Active: 1745827473457@@db@3306@mydb
-- === Création de la base et encodage ===
CREATE DATABASE IF NOT EXISTS lbdr_db
  DEFAULT CHARACTER SET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
USE lbdr_db;

-- === Utilisateurs (persistés seulement après inscription) ===
CREATE TABLE users (
  id                      VARCHAR(36) PRIMARY KEY,
  first_name              VARCHAR(255) NOT NULL,
  last_name               VARCHAR(255) NOT NULL,
  birth_date              DATE        NOT NULL,
  sex                     ENUM('M','F','Other') NOT NULL,
  discord_id              VARCHAR(255) UNIQUE,
  pseudonym               VARCHAR(255),
  email                   VARCHAR(255) NOT NULL UNIQUE,
  username                VARCHAR(100) NOT NULL UNIQUE,
  password_hash           VARCHAR(255) NOT NULL,
  password_salt           VARCHAR(255) NOT NULL,
  user_type               ENUM('NON_REGISTERED','REGISTERED','ADMINISTRATOR') NOT NULL DEFAULT 'REGISTERED',
  registration_date       DATE,
  age                     INT AS (
                             TIMESTAMPDIFF(YEAR, birth_date, CURDATE())
                           ) VIRTUAL,
  seniority_years         INT AS (
                             IF(registration_date IS NOT NULL,
                                TIMESTAMPDIFF(YEAR, registration_date, CURDATE()),
                                0)
                           ) VIRTUAL,
  old_user                BOOLEAN   NOT NULL DEFAULT FALSE,
  first_connection        BOOLEAN   NOT NULL DEFAULT TRUE,
  created_at              TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- === Catalogue des jeux ===
CREATE TABLE games (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(255) NOT NULL UNIQUE,
  description TEXT,
  type        ENUM('JDR','BOARD_GAME','OTHER') NOT NULL DEFAULT 'JDR'
) ENGINE=InnoDB;

-- === Genres (catégories pour filtrer les jeux) ===
CREATE TABLE genres (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO genres(name)
VALUES ('Fantastique'),('Horreur'),('Exploration'),('Science-fiction'),('Historique');

-- === Liaison jeux ↔ genres ===
CREATE TABLE game_genres (
  game_id  INT NOT NULL,
  genre_id INT NOT NULL,
  PRIMARY KEY (game_id, genre_id),
  FOREIGN KEY (game_id)  REFERENCES games(id)  ON DELETE CASCADE,
  FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Lieux de jeu ===
CREATE TABLE locations (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(255) NOT NULL,
  address     VARCHAR(255),
  latitude    DECIMAL(10,8),
  longitude   DECIMAL(11,8),
  description TEXT
) ENGINE=InnoDB;

-- === Événements de l'association ===
CREATE TABLE events (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  name                VARCHAR(255) NOT NULL,
  description         TEXT,
  start_date          DATE        NOT NULL,
  end_date            DATE        NOT NULL,
  location_id         INT,
  recurrence_pattern  JSON,
  exceptions          JSON,
  created_at          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- === Périodes d'ouverture/fermeture de l'association ===
CREATE TABLE association_periods (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  open_date  DATE NOT NULL,
  close_date DATE NOT NULL
) ENGINE=InnoDB;

-- === Parties proposées ===
CREATE TABLE partie (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  game_id            INT         NOT NULL,
  mj_id        VARCHAR(36) NOT NULL,
  partie_type   ENUM('CAMPAIGN','ONESHOT','BOARD_GAME','EVENT') NOT NULL,
  campaign_type      ENUM('OPEN','CLOSED') DEFAULT NULL,
  short_description  VARCHAR(255),
  description        TEXT,
  max_players        INT    DEFAULT 0,
  locked             BOOLEAN AS ( FALSE ) VIRTUAL,
  image_url          VARCHAR(512),
  image_alt          VARCHAR(255),
  created_at         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (game_id)     REFERENCES games(id) ON DELETE CASCADE,
  FOREIGN KEY (mj_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Whitelist pour campagnes fermées ===
CREATE TABLE partie_members (
  partie_id INT        NOT NULL,
  user_id   VARCHAR(36) NOT NULL,
  PRIMARY KEY (partie_id, user_id),
  FOREIGN KEY (partie_id) REFERENCES partie(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Sessions (RDV de jeu) ===
CREATE TABLE sessions (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  partie_id      INT         NOT NULL,
  location_id    INT         NOT NULL,
  session_date   DATE        NOT NULL,
  start_time     TIME        NOT NULL,
  end_time       TIME        NOT NULL,
  mj_id          VARCHAR(36) NOT NULL,
  FOREIGN KEY (partie_id)   REFERENCES partie(id)    ON DELETE CASCADE,
  FOREIGN KEY (location_id) REFERENCES locations(id)  ON DELETE CASCADE,
  FOREIGN KEY (mj_id)       REFERENCES users(id)      ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Inscriptions aux sessions ===
CREATE TABLE session_players (
  session_id    INT        NOT NULL,
  user_id       VARCHAR(36) NOT NULL,
  registered_at TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (session_id, user_id),
  FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Horaires et récurrences de lieux ===
CREATE TABLE location_schedule (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  location_id         INT         NOT NULL,
  start_time          TIME        NOT NULL,
  end_time            TIME        NOT NULL,
  recurrence_type     ENUM('NONE','DAILY','WEEKLY','MONTHLY','YEARLY')
                        NOT NULL DEFAULT 'NONE',
  recurrence_pattern  JSON,      -- Détails de la règle (e.g. {"byDay":["MO","WE"],"interval":2})
  exceptions          JSON,      -- Liste de dates ou d'intervalles ( {"dates":["2025-05-01"],"intervals":[{"start":"2025-05-10","end":"2025-05-12"}]} )
  event_id            INT,       -- Optionnel, pour override lors d'un événement
  FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id)    REFERENCES events(id)    ON DELETE SET NULL
) ENGINE=InnoDB;

-- === Disponibilités / Indisponibilités utilisateurs ===
CREATE TABLE user_time_slots (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         VARCHAR(36) NOT NULL,
  slot_type       ENUM('AVAILABILITY','UNAVAILABILITY') NOT NULL,
  start_datetime  DATETIME    NOT NULL,
  end_datetime    DATETIME    NOT NULL,
  is_recurring    BOOLEAN     NOT NULL DEFAULT FALSE,
  recurrence_rule TEXT,  -- RRULE iCal si besoin de traitement en base
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Vues pour colonnes calculées ===

-- Âge et ancienneté (remplacées par colonnes virtuelles)
CREATE VIEW user_stats AS
SELECT
  id,
  first_name,
  last_name,
  age,
  seniority_years
FROM users;

-- Nombre d’inscrits par session
CREATE VIEW session_registration_count AS
SELECT
  s.id                   AS session_id,
  COUNT(sp.user_id)      AS number_of_players_registered
FROM sessions s
LEFT JOIN session_players sp ON sp.session_id = s.id
GROUP BY s.id;

-- === Intégration Helloasso ===
-- Table brute des notifications reçues
CREATE TABLE helloasso_notifications (
  id             VARCHAR(100) PRIMARY KEY,
  event_type     VARCHAR(100) NOT NULL,
  occurred_at    DATETIME     NOT NULL,
  payload        JSON         NOT NULL,
  received_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed      BOOLEAN      NOT NULL DEFAULT FALSE
) ENGINE=InnoDB;

-- Table détaillée des paiements et statuts
CREATE TABLE helloasso_payments (
  id                   VARCHAR(100) PRIMARY KEY,
  notification_id      VARCHAR(100),
  user_id              VARCHAR(36),
  type                 VARCHAR(100),
  name                 VARCHAR(255),
  amount               DECIMAL(10,2) NOT NULL,
  currency             VARCHAR(10)    NOT NULL,
  due_date             DATE,
  status               VARCHAR(50),
  metadata             JSON,
  created_at           TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (notification_id) REFERENCES helloasso_notifications(id),
  FOREIGN KEY (user_id)          REFERENCES users(id)
) ENGINE=InnoDB;
