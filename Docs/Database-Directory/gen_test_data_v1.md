# gen_test_data_v1.sql

## Description
Fichier SQL contenant des données de test générées automatiquement pour peupler la base de données en environnement de développement.

## Contenu

### Données utilisateurs (users)
- **30 utilisateurs fictifs** avec profils complets
- **Identifiants UUID** générés aléatoirement
- **Données variées** : âges, sexes, dates d'inscription différentes
- **Mots de passe hashés** avec salts uniques
- **Identifiants Discord** simulés au format `nom#XXXX`

### Structure des insertions
```sql
INSERT INTO users (id, prenom, nom, date_de_naissance, sexe, id_discord, ...)
```

### Types de données générées
- **Prénoms/Noms** : Diversité internationale (Martin, Dupont, Nguyen, Kumar, etc.)
- **Dates de naissance** : Entre 1970 et 2002 pour simulation d'âges variés
- **Sexes** : Distribution entre M, F, Other
- **Emails** : Format `prenom.nom@example.com` 
- **Types d'utilisateurs** : Principalement REGISTERED
- **Dates d'inscription** : Étalées entre 2020 et 2024

### Caractéristiques techniques
- **Mots de passe** : Hashés avec SHA-256 + salt individuel
- **Salts** : Chaînes hexadécimales de 8 caractères
- **Flags** : `old_user=FALSE`, `first_connection=FALSE` pour utilisateurs établis

## Utilisation
Exécuté après `database_creation.sql` pour avoir des données de test permettant de :
- Tester les fonctionnalités d'inscription/connexion
- Simuler des sessions avec plusieurs participants
- Valider les requêtes de recherche et filtrage
- Développer l'interface utilisateur avec des données réalistes

## Note
Ces données sont uniquement pour l'environnement de développement et ne doivent jamais être utilisées en production.
