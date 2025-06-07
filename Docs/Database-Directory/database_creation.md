# database_creation.sql

## Description
Script SQL complet pour la création de la base de données `lbdr_db` avec toutes ses tables, contraintes et données initiales.

## Structure du fichier

### 1. Création de la base
```sql
CREATE DATABASE IF NOT EXISTS lbdr_db
  DEFAULT CHARACTER SET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;
```
- **Encodage** : UTF8MB4 pour support Unicode complet (emojis, caractères spéciaux)
- **Collation** : unicode_ci pour tri insensible à la casse

### 2. Tables principales (ordre de création)

#### Tables indépendantes
- **`utilisateurs`** - Membres de l'association avec colonnes virtuelles (`age`, `annees_anciennete`)
- **`refresh_tokens`** - Tokens JWT pour authentification sécurisée
- **`jeux`** - Catalogue des jeux (JdR, jeux de société, autres)
- **`genres`** - Catégories de jeux (Fantastique, Horreur, etc.)
- **`lieux`** - Lieux de jeu avec coordonnées GPS

#### Tables de liaison
- **`jeux_genres`** - Relation N:N entre jeux et genres
- **`activites`** - Propositions de campagnes/sessions
- **`membres_activite`** - Whitelist pour campagnes fermées
- **`sessions`** - Rendez-vous concrets liés aux activités
- **`joueurs_session`** - Inscriptions aux sessions

#### Tables de gestion
- **`evenements`** - Événements de l'association
- **`horaires_lieu`** - Disponibilités des lieux avec récurrence
- **`creneaux_utilisateur`** - Disponibilités/indisponibilités des membres

#### Intégration Helloasso
- **`notifications_helloasso`** - Webhooks bruts reçus
- **`paiements_helloasso`** - Paiements traités

### 3. Contraintes et index
- **Clés étrangères** : Intégrité référentielle avec CASCADE/SET NULL
- **Index uniques** : Login, email utilisateur ; nom des jeux, genres, lieux
- **Colonnes virtuelles** : Calculs automatiques (âge, ancienneté)

### 4. Vues SQL
- **`statistiques_utilisateur`** - Informations utilisateur avec colonnes calculées
- **`compte_inscriptions_session`** - Nombre d'inscrits par session

### 5. Données initiales
- **Genres prédéfinis** : Fantastique, Horreur, Exploration, Science-fiction, Historique
- **Lieux de base** : Configuration pour l'association

## Fonctionnalités spéciales

### Colonnes virtuelles (MySQL 5.7+)
```sql
age INT AS (TIMESTAMPDIFF(YEAR, date_de_naissance, CURDATE())) VIRTUAL
```
Calculs automatiques sans stockage physique.

### Support récurrence (JSON)
```sql
regle_recurrence JSON
exceptions JSON
```
Stockage des règles de récurrence et exceptions pour horaires et événements.

### Gestion des états
```sql
ENUM('ACTIVE','FERMER','TERMINER','ANNULER','SUPPRIMER')
```
États explicites pour activités et sessions.

## Utilisation
Exécuté lors de l'initialisation du projet ou pour remettre à zéro la base de données de développement.
