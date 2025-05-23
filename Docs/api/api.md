Voici la traduction en français des endpoints proposés pour l'API, en conservant la structure et la logique décrites précédemment. Les descriptions, rôles requis et corps/paramètres restent inchangés, seule la terminologie des endpoints est traduite pour correspondre à un contexte francophone.

---

### **1. Utilisateurs**
Gérer les utilisateurs (inscription, connexion, profil, disponibilités).

| Méthode | Endpoint | Description | Rôle requis | Corps/Paramètres |
|---------|------------------------------|-------------------------------------------|------------------|-------------------|
| POST    | `/api/utilisateurs/inscription` | Inscription d’un nouvel utilisateur       | Aucun            | `{ prenom, nom, login, date_de_naissance, sexe, email, mot_de_passe, ... }` |
| POST    | `/api/utilisateurs/connexion`   | Connexion (retourne un token JWT)         | Aucun            | `{ login, mot_de_passe }` |
| GET     | `/api/utilisateurs/:id`        | Récupérer les infos d’un utilisateur      | INSCRIT/ADMIN    | Param: `id` |
| PUT     | `/api/utilisateurs/:id`        | Mettre à jour le profil                   | INSCRIT (soi-même)/ADMIN | `{ prenom, nom, email, ... }` |
| DELETE  | `/api/utilisateurs/:id`        | Supprimer un utilisateur                  | ADMIN            | Param: `id` |
| GET     | `/api/utilisateurs/:id/creneaux` | Liste des disponibilités/indisponibilités | INSCRIT/ADMIN    | Param: `id` |
| POST    | `/api/utilisateurs/:id/creneaux` | Ajouter un créneau (dispo/indispo)       | INSCRIT (soi-même)/ADMIN | `{ type_creneau, date_heure_debut, date_heure_fin, est_recurrant, regle_recurrence }` |
| DELETE  | `/api/utilisateurs/:id/creneaux/:creneauId` | Supprimer un créneau             | INSCRIT (soi-même)/ADMIN | Param: `creneauId` |

---

### **2. Jeux**
Gérer le catalogue des jeux et leurs genres.

| Méthode | Endpoint | Description | Rôle requis | Corps/Paramètres |
|---------|------------------------------|-------------------------------------------|------------------|-------------------|
| GET     | `/api/jeux`                 | Liste des jeux (filtre par genre possible) | Aucun            | Query: `?genre=fantastique` |
| GET     | `/api/jeux/:id`             | Détails d’un jeu                          | Aucun            | Param: `id` |
| POST    | `/api/jeux`                 | Ajouter un jeu                            | ADMIN            | `{ nom, description, image, type_jeu, genres: [id_genre] }` |
| PUT     | `/api/jeux/:id`             | Mettre à jour un jeu                      | ADMIN            | `{ nom, description, ... }` |
| DELETE  | `/api/jeux/:id`             | Supprimer un jeu                          | ADMIN            | Param: `id` |
| GET     | `/api/genres`               | Liste des genres                          | Aucun            | - |

---

### **3. Lieux**
Gérer les lieux où se déroulent les parties/sessions.

| Méthode | Endpoint | Description | Rôle requis | Corps/Paramètres |
|---------|------------------------------|-------------------------------------------|------------------|-------------------|
| GET     | `/api/lieux`                | Liste des lieux (filtre par coordonnées possible) | Aucun            | Query: `?latitude=48.8566&longitude=2.3522&rayon=10` |
| GET     | `/api/lieux/:id`            | Détails d’un lieu                         | Aucun            | Param: `id` |
| POST    | `/api/lieux`                | Ajouter un lieu                           | ADMIN            | `{ nom, adresse, latitude, longitude, description }` |
| PUT     | `/api/lieux/:id`            | Mettre à jour un lieu                     | ADMIN            | `{ nom, adresse, ... }` |
| DELETE  | `/api/lieux/:id`            | Supprimer un lieu                         | ADMIN            | Param: `id` |
| GET     | `/api/lieux/:id/horaires`   | Horaires du lieu                          | Aucun            | Param: `id` |
| POST    | `/api/lieux/:id/horaires`   | Ajouter un horaire                        | ADMIN            | `{ heure_debut, heure_fin, type_recurrence, regle_recurrence }` |
| PATCH    | `/api/lieux/:id/horaires`   | Ajouter un horaire                        | ADMIN            | `{ heure_debut, heure_fin, type_recurrence, regle_recurrence }` |
| DELETE    | `/api/lieux/:id/horaires`   | Ajouter un horaire                        | ADMIN            | `{ heure_debut, heure_fin, type_recurrence, regle_recurrence }` |

---

### **4. Parties (Campagnes)**
Gérer les campagnes ou parties (y compris jeux de société et événements).

| Méthode | Endpoint | Description | Rôle requis | Corps/Paramètres |
|---------|------------------------------|-------------------------------------------|------------------|-------------------|
| GET     | `/api/parties`              | Liste des parties (filtre par type, jeu, q [mot clé : nom, description, mj, nom des joueurs inscrit]..., nombre d'insctit, place restante (bool), lieu, mj, verrouille, order (date_creation, date de la dernière/prochiane session )  ) | Aucun            | Query: `?type_partie=CAMPAGNE&jeu_id=1` |
| GET     | `/api/parties/:id`          | Détails d’une partie                      | Aucun            | Param: `id` |
| POST    | `/api/parties`              | Créer une partie                          | INSCRIT (maître du jeu)/ADMIN | `{ id_jeu, id_maitre_jeu, type_partie, type_campagne, description, nombre_max_joueurs, ... }` |
| PUT     | `/api/parties/:id`          | Mettre à jour une partie                  | INSCRIT (maître du jeu)/ADMIN | `{ description, nombre_max_joueurs, ... }` |
| PATCH     | `/api/parties/:id`          | Mettre à jour une partie                  | INSCRIT (maître du jeu)/ADMIN | `{ description, nombre_max_joueurs, ... }` |
| DELETE  | `/api/parties/:id`          | Supprimer une partie                      | INSCRIT (maître du jeu)/ADMIN | Param: `id` |
| POST    | `/api/parties/:id/membres`  | Ajouter un membre (campagne)       | INSCRIT/ADMIN | `{ id_utilisateur }` |
| DELETE  | `/api/parties/:id/membres/:userId` | Retirer un membre                  | INSCRIT (maître du jeu)/ADMIN | Param: `userId` |

---

### **5. Sessions (RDV de jeu)**
Gérer les sessions associées à une partie.

| Méthode | Endpoint | Description | Rôle requis | Corps/Paramètres |
|---------|------------------------------|-------------------------------------------|------------------|-------------------|
| GET     | `/api/sessions`             | Liste des sessions (filtre par partie, lieu, date) | Aucun            | Query: `?partie_id=1&date_debut=2025-06-01` |
| GET     | `/api/sessions/:id`         | Détails d’une session                     | Aucun            | Param: `id` |
| POST    | `/api/sessions`             | Créer une session                         | INSCRIT (maître du jeu)/ADMIN | `{ id_partie, id_lieu, date_session, heure_debut, heure_fin, nombre_max_joueurs, id_maitre_jeu }` |
| PUT     | `/api/sessions/:id`         | Mettre à jour une session                 | INSCRIT (maître du jeu)/ADMIN | `{ date_session, heure_debut, ... }` |
| DELETE  | `/api/sessions/:id`         | Supprimer une session                     | INSCRIT (maître du jeu)/ADMIN | Param: `id` |
| POST    | `/api/sessions/:id/joueurs` | Inscription à une session                 | INSCRIT          | `{ id_utilisateur }` |
| DELETE  | `/api/sessions/:id/joueurs/:userId` | Désinscription d’une session       | INSCRIT (soi-même)/ADMIN | Param: `userId` |
| GET     | `/api/sessions/:id/joueurs` | Liste des joueurs inscrits                | INSCRIT/ADMIN    | Param: `id` |

---

### **6. Événements**
Gérer les événements de l’association.

| Méthode | Endpoint | Description | Rôle requis | Corps/Paramètres |
|---------|------------------------------|-------------------------------------------|------------------|-------------------|
| GET     | `/api/evenements`           | Liste des événements                      | Aucun            | Query: `?date_debut=2025-06-01` |
| GET     | `/api/evenements/:id`       | Détails d’un événement                    | Aucun            | Param: `id` |
| POST    | `/api/evenements`           | Créer un événement                        | ADMIN            | `{ nom, description, date_debut, date_fin, id_lieu, regle_recurrence }` |
| PUT     | `/api/evenements/:id`       | Mettre à jour un événement                | ADMIN            | `{ nom, description, ... }` |
| DELETE  | `/api/evenements/:id`       | Supprimer un événement                    | ADMIN            | Param: `id` |

---

### **7. Périodes d’ouverture/fermeture**
Gérer les périodes d’activité de l’association.

| Méthode | Endpoint | Description | Rôle requis | Corps/Paramètres |
|---------|------------------------------|-------------------------------------------|------------------|-------------------|
| GET     | `/api/association/periodes` | Liste des périodes                        | Aucun            | - |
| POST    | `/api/association/periodes` | Ajouter une période                       | ADMIN            | `{ date_ouverture, date_fermeture }` |
| DELETE  | `/api/association/periodes/:id` | Supprimer une période                  | ADMIN            | Param: `id` |

---

### **8. Paiements (HelloAsso)**
Gérer les paiements et notifications.

| Méthode | Endpoint | Description | Rôle requis | Corps/Paramètres |
|---------|------------------------------|-------------------------------------------|------------------|-------------------|
| GET     | `/api/paiements`            | Liste des paiements                       | ADMIN            | Query: `?utilisateur_id=uuid` |
| GET     | `/api/paiements/:id`        | Détails d’un paiement                     | ADMIN            | Param: `id` |
| POST    | `/api/notifications/helloasso` | Recevoir une notification HelloAsso    | Aucun (webhook)  | `{ id, type_evenement, date_evenement, donnees }` |

---

### **Notes sur la traduction**
- Les termes comme "utilisateurs", "jeux", "lieux", "parties", "sessions", "événements", "périodes", et "paiements" sont directement tirés de la base de données pour rester cohérents avec le schéma SQL.
- Les mots comme "inscription", "connexion", "creneaux", "membres", et "joueurs" reflètent le vocabulaire francophone adapté au contexte de l’association de JDR.
- Les paramètres de requête (query) comme `rayon` (pour radius) ou `utilisateur_id` sont traduits pour plus de clarté en français.

Si vous souhaitez modifier certains termes (par exemple, utiliser "campagnes" au lieu de "parties" partout) ou adapter davantage le vocabulaire, faites-le-moi savoir !