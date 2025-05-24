# Utilisateurs API Endpoints

This document describes the endpoints for managing users and their time slots in the role-playing game system.

## Base Path
`/api/utilisateurs`

## Endpoints

### Register a User
- **Method**: POST
- **Path**: `/api/utilisateurs/inscription`
- **Permissions**: None (public access)
- **Request Body**:
  ```json
  {
    "prenom": "John",
    "nom": "Doe",
    "login": "johndoe",
    "mot_de_passe": "password123",
    "sexe": "M",
    "email": "john.doe@example.com",
    "date_de_naissance": "1990-01-01",
    "id_discord": "john#1234",
    "pseudonyme": "JDoe",
    "type_utilisateur": "Inscrit"
  }
  ```
  - `prenom` (string, required): First name
  - `nom` (string, required): Last name
  - `login` (string, required): Username
  - `mot_de_passe` (string, required): Password
  - `sexe` (string, required): Gender (M, F, A)
  - `email` (string, optional): Email address
  - `date_de_naissance` (string, optional): Birth date (YYYY-MM-DD)
  - `id_discord` (string, optional): Discord ID
  - `pseudonyme` (string, optional): Nickname
  - `type_utilisateur` (string, optional): User type (default: Inscrit)
- **Response**:
  - **201 Created**: User registered successfully
    ```json
    {
      "status": "success",
      "data": {
        "token": "jwt_token",
        "utilisateur": {
          "id": "uuid",
          "prenom": "John",
          "nom": "Doe",
          ...
        }
      },
      "message": "Utilisateur créé avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Prénom, nom, login, mot de passe et sexe sont requis"
    }
    ```
  - **409 Conflict**: Username, email, or Discord ID already in use
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Login, email ou ID Discord déjà utilisé"
    }
    ```

### Login
- **Method**: POST
- **Path**: `/api/utilisateurs/connexion`
- **Permissions**: None (public access)
- **Request Body**:
  ```json
  {
    "login": "johndoe",
    "mot_de_passe": "password123",
    "keep_logged_in": true
  }
  ```
  - `login` (string, required): Username
  - `mot_de_passe` (string, required): Password
  - `keep_logged_in` (boolean, optional): Generate refresh token
- **Response**:
  - **200 Success**: Login successful
    ```json
    {
      "status": "success",
      "data": {
        "token": "jwt_token",
        "refresh_token": "refresh_token",
        "utilisateur": {
          "id": "uuid",
          "prenom": "John",
          ...
        }
      },
      "message": "Connexion réussie"
    }
    ```
  - **401 Unauthorized**: Invalid credentials
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Identifiants invalides"
    }
    ```

### Refresh Token
- **Method**: POST
- **Path**: `/api/utilisateurs/refresh`
- **Permissions**: None (public access)
- **Request Body**:
  ```json
  {
    "refresh_token": "refresh_token"
  }
  ```
  - `refresh_token` (string, required): Refresh token
- **Response**:
  - **200 Success**: Token refreshed
    ```json
    {
      "status": "success",
      "data": {
        "token": "new_jwt_token",
        "utilisateur": {
          "id": "uuid",
          ...
        }
      },
      "message": "Token renouvelé"
    }
    ```
  - **401 Unauthorized**: Invalid or expired refresh token
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Refresh token invalide ou expiré"
    }
    ```

### Get User by ID
- **Method**: GET
- **Path**: `/api/utilisateurs/{id}`
- **Permissions**: Requires `UtilisateurApi:read` (self or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (string, required): UUID of the user
- **Response**:
  - **200 Success**: Returns user details
    ```json
    {
      "status": "success",
      "data": {
        "id": "uuid",
        "prenom": "John",
        "nom": "Doe",
        ...
      },
      "message": null
    }
    ```
  - **403 Forbidden**: Access denied
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Vous ne pouvez accéder qu'à vos propres informations"
    }
    ```
  - **404 Not Found**: User not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Utilisateur non trouvé: ..."
    }
    ```

### Update a User
- **Method**: PUT
- **Path**: `/api/utilisateurs/{id}`
- **Permissions**: Requires `UtilisateurApi:write` (self or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (string, required): UUID of the user
- **Request Body**:
  ```json
  {
    "prenom": "Jane",
    "nom": "Doe",
    "email": "jane.doe@example.com",
    ...
  }
  ```
  - All fields are optional; only provided fields are updated
- **Response**:
  - **200 Success**: User updated successfully
    ```json
    {
      "status": "success",
      "data": {
        "id": "uuid",
        "prenom": "Jane",
        ...
      },
      "message": "Utilisateur mis à jour avec succès"
    }
    ```
  - **403 Forbidden**: Access denied
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Vous ne pouvez modifier qu'à votre propre profil"
    }
    ```
  - **404 Not Found**: User not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Utilisateur non trouvé: ..."
    }
    ```

### Delete a User
- **Method**: DELETE
- **Path**: `/api/utilisateurs/{id}`
- **Permissions**: Requires `UtilisateurApi:delete` (ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (string, required): UUID of the user
- **Response**:
  - **204 No Content**: User deleted successfully
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Utilisateur supprimé avec succès"
    }
    ```
  - **404 Not Found**: User not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Utilisateur non trouvé: ..."
    }
    ```

### Get User Time Slots
- **Method**: GET
- **Path**: `/api/utilisateurs/{userId}/creneaux`
- **Permissions**: Requires `UtilisateurApi:read` (self or ADMINISTRATEUR)
- **Path Parameters**:
  - `userId` (string, required): UUID of the user
- **Response**:
  - **200 Success**: Returns user time slots
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "type_creneau": "DISPONIBLE",
          "date_heure_debut": "2025-06-01 14:00:00",
          "date_heure_fin": "2025-06-01 18:00:00",
          "est_recurrant": false
        },
        ...
      ],
      "message": null
    }
    ```
  - **403 Forbidden**: Access denied
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Vous ne pouvez accéder qu'à vos propres créneaux"
    }
    ```

### Add a Time Slot
- **Method**: POST
- **Path**: `/api/utilisateurs/{userId}/creneaux`
- **Permissions**: Requires `UtilisateurApi:write` (self or ADMINISTRATEUR)
- **Path Parameters**:
  - `userId` (string, required): UUID of the user
- **Request Body**:
  ```json
  {
    "type_creneau": "DISPONIBLE",
    "date_heure_debut": "2025-06-01 14:00:00",
    "date_heure_fin": "2025-06-01 18:00:00",
    "est_recurrant": false,
    "regle_recurrence": null
  }
  ```
  - `type_creneau` (string, required): Slot type (e.g., DISPONIBLE)
  - `date_heure_debut` (string, required): Start date and time
  - `date_heure_fin` (string, required): End date and time
  - `est_recurrant` (boolean, required): Is recurring
  - `regle_recurrence` (string, optional): Recurrence rule
- **Response**:
  - **201 Created**: Time slot added
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "type_creneau": "DISPONIBLE",
        ...
      },
      "message": "Créneau ajouté avec succès"
    }
    ```
  - **403 Forbidden**: Access denied
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Vous ne pouvez ajouter des créneaux qu'à votre propre profil"
    }
    ```

### Update a Time Slot
- **Method**: PATCH
- **Path**: `/api/utilisateurs/{userId}/creneaux`
- **Permissions**: Requires `UtilisateurApi:write` (self or ADMINISTRATEUR)
- **Path Parameters**:
  - `userId` (string, required): UUID of the user
- **Request Body**:
  ```json
  {
    "id_creneau": 1,
    "type_creneau": "NON_DISPONIBLE",
    "date_heure_debut": "2025-06-01 15:00:00",
    ...
  }
  ```
  - `id_creneau` (integer, required): ID of the time slot
  - Other fields are optional
- **Response**:
  - **200 Success**: Time slot updated
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "type_creneau": "NON_DISPONIBLE",
        ...
      },
      "message": "Créneau mis à jour avec succès"
    }
    ```
  - **403 Forbidden**: Slot does not belong to user
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Le créneau n'appartient pas à cet utilisateur"
    }
    ```

### Delete a Time Slot
- **Method**: DELETE
- **Path**: `/api/utilisateurs/{userId}/creneaux/{creneauId}`
- **Permissions**: Requires `UtilisateurApi:delete` (self or ADMINISTRATEUR)
- **Path Parameters**:
  - `userId` (string, required): UUID of the user
  - `creneauId` (integer, required): ID of the time slot
- **Response**:
  - **204 No Content**: Time slot deleted
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Créneau supprimé avec succès"
    }
    ```
  - **403 Forbidden**: Slot does not belong to user
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Le créneau n'appartient pas à cet utilisateur"
    }
    ```