# Jeux API Endpoints

This document describes the endpoints for managing games in the role-playing game system.

## Base Path
`/api/jeux`

## Endpoints

### Get a Game by ID
- **Method**: GET
- **Path**: `/api/jeux/{id}`
- **Permissions**: None (public access)
- **Path Parameters**:
  - `id` (integer, required): The ID of the game
- **Response**:
  - **200 Success**: Returns the game details
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "nom": "Dungeons & Dragons",
        "description": "A fantasy role-playing game",
        "type_jeu": "JDR"
      },
      "message": null
    }
    ```
  - **404 Not Found**: Game not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Jeu non trouvé: ..."
    }
    ```

### List Games
- **Method**: GET
- **Path**: `/api/jeux`
- **Permissions**: None (public access)
- **Query Parameters**:
  - `keyword` (string, optional): Search term for game name or description
  - `type_jeu` (string, optional): Filter by game type (e.g., JDR, Plateau)
  - `genres` (string, optional): Comma-separated list of genre IDs
- **Response**:
  - **200 Success**: Returns a list of games
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "nom": "Dungeons & Dragons",
          "description": "A fantasy role-playing game",
          "type_jeu": "JDR"
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

### Create a Game
- **Method**: POST
- **Path**: `/api/jeux`
- **Permissions**: Requires `JeuApi:write` (ADMINISTRATEUR)
- **Request Body**:
  ```json
  {
    "nom": "Game Name",
    "description": "Game description",
    "type_jeu": "JDR",
    "genres": [1, 2]
  }
  ```
  - `nom` (string, required): Game name
  - `description` (string, optional): Game description
  - `type_jeu` (string, optional): Game type (default: Autre)
  - `genres` (array, optional): List of genre IDs
- **Response**:
  - **201 Created**: Game created successfully
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "nom": "Game Name",
        "description": "Game description",
        "type_jeu": "JDR"
      },
      "message": "Jeu créé avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Le nom du jeu est requis et ne peut pas être vide"
    }
    ```
  - **409 Conflict**: Game name already exists
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Un jeu avec ce nom existe déjà"
    }
    ```

### Update a Game (PUT)
- **Method**: PUT
- **Path**: `/api/jeux/{id}`
- **Permissions**: Requires `JeuApi:write` (ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the game
- **Request Body**:
  ```json
  {
    "nom": "Updated Game Name",
    "description": "Updated description",
    "type_jeu": "Plateau",
    "genres": [1, 3]
  }
  ```
  - `nom` (string, required): Updated game name
  - `description` (string, optional): Updated description
  - `type_jeu` (string, optional): Updated game type
  - `genres` (array, optional): Updated list of genre IDs
- **Response**:
  - **200 Success**: Game updated successfully
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "nom": "Updated Game Name",
        "description": "Updated description",
        "type_jeu": "Plateau"
      },
      "message": "Jeu mis à jour avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Le nom du jeu est requis et ne peut pas être vide"
    }
    ```
  - **404 Not Found**: Game not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Jeu non trouvé: ..."
    }
    ```

### Update a Game (PATCH)
- **Method**: PATCH
- **Path**: `/api/jeux/{id}`
- **Permissions**: Requires `JeuApi:write` (ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the game
- **Request Body**: Same as PUT
- **Response**: Same as PUT

### Delete a Game
- **Method**: DELETE
- **Path**: `/api/jeux/{id}`
- **Permissions**: Requires `JeuApi:delete` (ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the game
- **Response**:
  - **204 No Content**: Game deleted successfully
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Jeu supprimé avec succès"
    }
    ```
  - **404 Not Found**: Game not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Jeu non trouvé: ..."
    }
    ```