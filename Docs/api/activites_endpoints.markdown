# API Activités - Endpoints

Cette documentation décrit les endpoints pour la gestion des activités de jeu et de leurs membres dans le système de jeu de rôle.

!!! info "API REST"
    L'API suit les conventions REST avec des réponses JSON standardisées.

## Chemin de base
`/api/activites`

!!! tip "Authentification"
    La plupart des endpoints nécessitent une authentification JWT via l'en-tête `Authorization: Bearer <token>`.

## Endpoints disponibles

### Récupérer une activité par ID

!!! example "GET /api/activites/{id}"
    **Méthode**: GET  
    **Chemin**: `/api/activites/{id}`  
    **Permissions**: Aucune (accès public)

**Paramètres de chemin**:
- `id` (integer, requis): L'ID de l'activité
- **Response**:
  - **200 Success**: Returns party details
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "nom": "Epic Quest",
        "id_jeu": 1,
        ...
      },
      "message": null
    }
    ```
  - **404 Not Found**: Party not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Activite non trouvée: ..."
    }
    ```

### List Activites
- **Method**: GET
- **Path**: `/api/activites`
- **Permissions**: None (public access)
- **Query Parameters**:
  - `q` (string, optional): Search term for party name
  - `type_activite` (string, optional): Filter by party type
  - `jeu_id` (integer, optional): Filter by game ID
  - `mj` (string, optional): Filter by game master ID
  - `place_restante` (boolean, optional): Filter activites with available slots
  - `verrouille` (boolean, optional): Filter locked activites
  - `order` (string, optional): Sort by `date_creation` or `prochaine_session`
- **Response**:
  - **200 Success**: Returns a list of activites
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
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

### Create a Party
- **Method**: POST
- **Path**: `/api/activites`
- **Permissions**: Requires `ActiviteApi:write` (game master or ADMINISTRATEUR)
- **Request Body**:
  ```json
  {
    "id_jeu": 1,
    "id_maitre_jeu": "uuid",
    "type_activite": "Campagne",
    "nom": "Epic Quest",
    "type_campagne": "LONGUE",
    ...
  }
  ```
  - `id_jeu` (integer, required): Game ID
  - `id_maitre_jeu` (string, required): Game master UUID
  - `type_activite` (string, required): Party type
  - `nom` (string, required): Party name
  - `type_campagne` (string, optional): Campaign type (required for Campagne)
- **Response**:
  - **201 Created**: Party created
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Activite créée avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "id_jeu, id_maitre_jeu, type_activite et nom requis"
    }
    ```

### Update a Party (PUT)
- **Method**: PUT
- **Path**: `/api/activites/{id}`
- **Permissions**: Requires `ActiviteApi:write` (game master or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the party
- **Request Body**:
  ```json
  {
    "nom": "Updated Quest",
    ...
  }
  ```
  - `nom` (string, required): Updated party name
  - Other fields are optional
- **Response**:
  - **200 Success**: Party updated
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Activite mise à jour avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Nom requis"
    }
    ```

### Update a Party (PATCH)
- **Method**: PATCH
- **Path**: `/api/activites/{id}`
- **Permissions**: Requires `ActiviteApi:write` (game master or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the party
- **Request Body**: Same as PUT
- **Response**: Same as PUT

### Delete a Party
- **Method**: DELETE
- **Path**: `/api/activites/{id}`
- **Permissions**: Requires `ActiviteApi:delete` (game master or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the party
- **Response**:
  - **204 No Content**: Party deleted
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Activite supprimée avec succès"
    }
    ```
  - **400 Bad Request**: Invalid ID
    ```json
    {
      "status": "error",
      "data": null,
      "message": "ID de la activite requis"
    }
    ```

### Add a Member to a Party
- **Method**: POST
- **Path**: `/api/activites/{id}/membres`
- **Permissions**: Requires `ActiviteApi:write` (game master or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the party
- **Request Body**:
  ```json
  {
    "id_utilisateur": "uuid"
  }
  ```
  - `id_utilisateur` (string, required): UUID of the user
- **Response**:
  - **201 Created**: Member added
    ```json
    {
      "status": "success",
      "data": {
        "id_utilisateur": "uuid",
        ...
      },
      "message": "Membre ajouté avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input or not a campaign
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Les membres ne peuvent être ajoutés qu’à une campagne"
    }
    ```

### Remove a Member from a Party
- **Method**: DELETE
- **Path**: `/api/activites/{id}/membres/{userId}`
- **Permissions**: Requires `ActiviteApi:delete` (game master or ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the party
  - `userId` (string, required): UUID of the user
- **Response**:
  - **204 No Content**: Member removed
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Membre retiré avec succès"
    }
    ```
  - **400 Bad Request**: Invalid IDs
    ```json
    {
      "status": "error",
      "data": null,
      "message": "ID de la activite et ID de l’utilisateur requis"
    }
    ```