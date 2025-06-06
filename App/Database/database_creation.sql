-- Active: 1747739094266@@db@3306@mydb
-- === Création de la base et encodage ===
CREATE DATABASE IF NOT EXISTS lbdr_db
  DEFAULT CHARACTER SET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
USE lbdr_db;

-- === Utilisateurs ===
CREATE TABLE utilisateurs (
  id                      VARCHAR(36) PRIMARY KEY,
  prenom                  VARCHAR(255) NOT NULL,
  nom                     VARCHAR(255) NOT NULL,
  login                   VARCHAR(255) NOT NULL UNIQUE,
  date_de_naissance       DATE NULL,
  sexe                    ENUM('M','F','Autre') NOT NULL,
  id_discord              VARCHAR(255) NULL UNIQUE,
  pseudonyme              VARCHAR(255) NULL,
  email                   VARCHAR(255) NULL UNIQUE,
  mot_de_passe            VARCHAR(255) NOT NULL,
  image                   VARCHAR(512) NULL,
  type_utilisateur        ENUM('NON_INSCRIT','INSCRIT','ADMINISTRATEUR') NOT NULL DEFAULT 'INSCRIT',
  date_inscription        DATE,
  age                     INT AS (
                             TIMESTAMPDIFF(YEAR, date_de_naissance, CURDATE())
                           ) VIRTUAL,
  annees_anciennete       INT AS (
                             IF(date_inscription IS NOT NULL,
                                TIMESTAMPDIFF(YEAR, date_inscription, CURDATE()),
                                0)
                           ) VIRTUAL,
  ancien_utilisateur       BOOLEAN   NOT NULL DEFAULT FALSE,
  premiere_connexion       BOOLEAN   NOT NULL DEFAULT TRUE,
  date_creation           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE refresh_tokens (
    id VARCHAR(36) PRIMARY KEY,
    id_utilisateur VARCHAR(36) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    date_expiration DATETIME NOT NULL,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Catalogue des jeux ===
CREATE TABLE jeux (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  nom              VARCHAR(255) NOT NULL UNIQUE,
  description      TEXT,
  image            VARCHAR(512),
  icon             VARCHAR(512),
  type_jeu         ENUM('JDR','JEU_DE_SOCIETE','AUTRE') NOT NULL DEFAULT 'AUTRE'
) ENGINE=InnoDB;

-- === Genres (catégories pour filtrer les jeux) ===
CREATE TABLE genres (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  nom      VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO genres(nom)
VALUES ('Fantastique'),('Horreur'),('Exploration'),('Science-fiction'),('Historique');

-- === Liaison jeux ↔ genres ===
CREATE TABLE jeux_genres (
  id_jeu       INT NOT NULL,
  id_genre     INT NOT NULL,
  PRIMARY KEY (id_jeu, id_genre),
  FOREIGN KEY (id_jeu)   REFERENCES jeux(id)   ON DELETE CASCADE,
  FOREIGN KEY (id_genre) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Lieux de jeu ===
CREATE TABLE lieux (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  nom              VARCHAR(255) NOT NULL,
  short_nom        VARCHAR(5),
  adresse          VARCHAR(255),
  latitude         DECIMAL(10,8),
  longitude        DECIMAL(11,8),
  description      TEXT
) ENGINE=InnoDB;

-- === Événements de l'association ===
CREATE TABLE evenements (
  id                   INT AUTO_INCREMENT PRIMARY KEY,
  nom                  VARCHAR(255) NOT NULL,
  description          TEXT,
  date_debut           DATE        NOT NULL,
  date_fin             DATE        NOT NULL,
  id_lieu              INT,
  regle_recurrence     JSON,
  exceptions           JSON,
  date_creation        TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_lieu) REFERENCES lieux(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- === Activites proposées ===
CREATE TABLE activites (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  nom                   VARCHAR(255) NOT NULL,
  etat                  ENUM('ACTIVE','FERMER','TERMINER','ANNULER', 'SUPPRIMER') NOT NULL DEFAULT 'ACTIVE',
  id_jeu                INT         NOT NULL,
  id_maitre_jeu         VARCHAR(36) NOT NULL,
  type_activite           ENUM('CAMPAGNE','ONESHOT','JEU_DE_SOCIETE','EVENEMENT') NOT NULL,
  type_campagne         ENUM('OUVERTE','FERMEE') DEFAULT NULL,
  description_courte    VARCHAR(255),
  description           TEXT,
  nombre_max_joueurs    INT    DEFAULT 0,
  max_joueurs_session   INT    DEFAULT 5,
  verrouille            BOOLEAN NOT NULL DEFAULT FALSE,
  image                 VARCHAR(512),
  texte_alt_image       VARCHAR(255),
  date_creation         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_jeu)        REFERENCES jeux(id)        ON DELETE CASCADE,
  FOREIGN KEY (id_maitre_jeu) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Whitelist pour campagnes fermées ===
CREATE TABLE membres_activite (
  id_activite    INT        NOT NULL,
  id_utilisateur VARCHAR(36) NOT NULL,
  PRIMARY KEY (id_activite, id_utilisateur),
  FOREIGN KEY (id_activite)     REFERENCES activites(id)       ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Sessions (RDV de jeu) ===
CREATE TABLE sessions (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  nom                   VARCHAR(255) NOT NULL,
  etat                  ENUM('OUVERTE','FERMER', 'COMPLETE', 'ANNULER', 'SUPPRIMER') NOT NULL DEFAULT 'OUVERTE',
  id_activite             INT         NOT NULL,
  id_lieu               INT         NOT NULL,
  date_session          DATE        NOT NULL,
  nombre_max_joueurs    INT         NOT NULL DEFAULT 5,
  heure_debut           TIME        NOT NULL,
  heure_fin             TIME        NOT NULL,
  id_maitre_jeu         VARCHAR(36) NOT NULL,
  FOREIGN KEY (id_activite)     REFERENCES activites(id)       ON DELETE CASCADE,
  FOREIGN KEY (id_lieu)       REFERENCES lieux(id)         ON DELETE CASCADE,
  FOREIGN KEY (id_maitre_jeu) REFERENCES utilisateurs(id)   ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Inscriptions aux sessions ===
CREATE TABLE joueurs_session (
  id_session       INT        NOT NULL,
  id_utilisateur   VARCHAR(36) NOT NULL,
  date_inscription TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_session, id_utilisateur),
  FOREIGN KEY (id_session)    REFERENCES sessions(id)      ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Horaires et récurrences de lieux ===
CREATE TABLE horaires_lieu (
  id                   INT AUTO_INCREMENT PRIMARY KEY,
  id_lieu              INT         NOT NULL,
  heure_debut          TIME        NOT NULL,
  heure_fin            TIME        NOT NULL,
  type_recurrence      ENUM('AUCUNE','QUOTIDIENNE','HEBDOMADAIRE','MENSUELLE','ANNUELLE')
                         NOT NULL DEFAULT 'AUCUNE',
  regle_recurrence     JSON,
  exceptions           JSON,
  id_evenement         INT,
  FOREIGN KEY (id_lieu)      REFERENCES lieux(id)       ON DELETE CASCADE,
  FOREIGN KEY (id_evenement) REFERENCES evenements(id)  ON DELETE SET NULL
) ENGINE=InnoDB;

-- === Disponibilités / Indisponibilités utilisateurs ===
CREATE TABLE creneaux_utilisateur (
  id                   INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur       VARCHAR(36) NOT NULL,
  type_creneau         ENUM('DISPONIBILITE','INDISPONIBILITE') NOT NULL,
  date_heure_debut     DATETIME    NOT NULL,
  date_heure_fin       DATETIME    NOT NULL,
  est_recurrant        BOOLEAN     NOT NULL DEFAULT FALSE,
  regle_recurrence     TEXT,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- === Vues pour colonnes calculées ===

-- Âge et ancienneté
CREATE VIEW statistiques_utilisateur AS
SELECT
  id,
  prenom,
  nom,
  age,
  annees_anciennete
FROM utilisateurs;

-- Nombre d’inscrits par session
CREATE VIEW compte_inscriptions_session AS
SELECT
  s.id                   AS id_session,
  COUNT(sp.id_utilisateur) AS nombre_joueurs_inscrits
FROM sessions s
LEFT JOIN joueurs_session sp ON sp.id_session = s.id
GROUP BY s.id;

-- === Intégration Helloasso ===
-- Table brute des notifications reçues
CREATE TABLE notifications_helloasso (
  id                VARCHAR(100) PRIMARY KEY,
  type_evenement    VARCHAR(100) NOT NULL,
  date_evenement    DATETIME     NOT NULL,
  donnees           JSON         NOT NULL,
  date_reception    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  traite            BOOLEAN      NOT NULL DEFAULT FALSE
) ENGINE=InnoDB;

-- Table détaillée des paiements et statuts
CREATE TABLE paiements_helloasso (
  id                   VARCHAR(100) PRIMARY KEY,
  id_notification      VARCHAR(100),
  id_utilisateur       VARCHAR(36),
  type_paiement        VARCHAR(100),
  nom                  VARCHAR(255),
  montant              DECIMAL(10,2) NOT NULL,
  devise               VARCHAR(10)    NOT NULL,
  date_echeance        DATE,
  statut               VARCHAR(50),
  metadonnees          JSON,
  date_creation        TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_notification) REFERENCES notifications_helloasso(id),
  FOREIGN KEY (id_utilisateur)   REFERENCES utilisateurs(id)
) ENGINE=InnoDB;

