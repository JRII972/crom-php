# Sessions API Endpoints

This document describes the endpoints for managing game sessions in the role-playing game system.

## Base Path
`/api/sessions`

## Endpoints

### Get a Session by ID
- **Method**: GET
- **Path**: `/api/sessions/{id}`
- **Permissions**: None (public access)
- **Path Parameters**:
  - `id` (integer, required): The ID of the session
- **Response**:
  - **200 Success**: Returns session details
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "id_partie": 1,
        "id_lieu": 1,
        "date_session": "2025-06-01",
        "heure_debut": "14:00:00",
        "heure_fin": "18:00:00",
        ...
      },
      "message": null
    }
    ```
  - **404 Not Found**: Session not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Session non trouvée: ..."
    }
    ```

### List Sessions
- **Method**: GET
- **Path**: `/api/sessions`
- **Permissions**: None (public access)
- **Query Parameters**:
  - `partie_id` (integer, optional): Filter by party ID
  - `lieu_id` (integer, optional): Filter by location ID
  - `date_debut` (string, optional): Filter by start date
  - `max_joueurs` (integer, optional): Filter by maximum players
- **Response**:
  - **200 Success**: Returns a list of sessions
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "id_partie": 1,
          ...
        },
        ...
      ],
      "message": null
    }
    ```
  - **500 Server Error**: Error during search
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Erreur lors de la recherche: ..."
    }
    ```

### Create a Session
- **Method**: POST
- **Path**: `/api/sessions`
- **Permissions**: Requires `SessionsApi:write` (game master or ADMINISTRATEUR)
- **Request Body**:
  ```json
  {
    "id_partie": 1,
    "id_lieu": 1,
    "date_session": "2025-06-01",
    "heure_debut": "14:00:00",
    "heure_fin": "18:00:00",
    "id_maitre_jeu": "uuid",
    "nombre_max_joueurs": 6
  }
  ```
  - `id_partie` (integer, required): Party ID
  - `id_lieu` (integer, required): Location ID
  - `date_session` (string, required): Session date
  - `heure_debut` (string, required): Start time
  - `heure_fin` (string, required): End time
  - `id_maitre_jeu` (string, required): Game master UUID
  - `nombre_max_joueurs` (integer, optional): Maximum players
- **Response**:
  - **201 Created**: Session created
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Session créée avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "id_partie, id_lieu, date_session, heure_debut, heure_fin, id_maitre_jeu requis"
    }
    ```
  - **403 Forbidden**: Not game master or admin
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Seul le maître du jeu ou un admin peut créer une session"
    }
    ```

### Update a Session
- **Method**: PUT
- **Path**: `/api/sessions/{id}`
- **Permissions**: Requires `SessionsApi:write` (game master or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the session
- **Request Body**:
  ```json
  {
    "id_partie": 1,
    "id_lieu": 2,
    ...
  }
  ```
  - All fields are optional; only provided fields are updated
- **Response**:
  - **200 Success**: Session updated
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Session mise à jour avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "ID de la session requis"
    }
    ```
  - **403 Forbidden**: Not game master or admin
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Seul le maître du jeu ou un admin peut effectuer cette action"
    }
    ```

### Delete a Session
- **Method**: DELETE
- **Path**: `/api/sessions/{id}`
- **Permissions**: Requires `SessionsApi:delete` (game master or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the session
- **Response**:
  - **204 No Content**: Session deleted
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Session supprimée avec succès"
    }
    ```
  - **400 Bad Request**: Invalid ID
    ```json
    {
      "status": "error",
      "data": null,
      "message": "ID de la session requis"
    }
    ```

### Get Session Players
- **Method**: GET
- **Path**: `/api/sessions/{id}/joueurs`
- **Permissions**: Requires `SessionsApi:read`
- **Path Parameters**:
  - `id` (integer, required): The ID of the session
- **Response**:
  - **200 Success**: Returns list of players
    ```json
    {
      "status": "success",
      "data": [
        {
          "id_utilisateur": "uuid",
          ...
        },
        ...
      ],
      "message": null
    }
    ```
  - **400 Bad Request**: Invalid ID
    ```json
    {
      "status": "error",
      "data": null,
      "message": "ID de la session requis"
    }
    ```

### Add a Player to a Session
- **Method**: POST
- **Path**: `/api/sessions/{id}/joueurs`
- **Permissions**: Requires `SessionsApi:write` (self or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the session
- **Request Body**:
  ```json
  {
    "id_utilisateur": "uuid"
  }
  ```
  - `id_utilisateur` (string, required): UUID of the user
- **Response**:
  - **201 Created**: Player added
    ```json
    {
      "status": "success",
      "data": {
        "id_utilisateur": "uuid",
        ...
      },
      "message": "Joueur inscrit avec succès"
    }
    ```
  - **403 Forbidden**: Not self or admin
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Seul l’utilisateur ou un admin peut s’inscrire"
    }
    ```

### Remove a Player from a Session
- **Method**: DELETE
- **Path**: `/api/sessions/{id}/joueurs/{userId}`
- **Permissions**: Requires `SessionsApi:delete` (self or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the session
  - `userId` (string, required): UUID of the user
- **Response**:
  - **204 No Content**: Player removed
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Joueur désinscrit avec succès"
    }
    ```
  - **403 Forbidden**: Not self or admin
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Seul l’utilisateur ou un admin peut se désinscrire"
    }
    ```