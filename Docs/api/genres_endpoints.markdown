# Genres API Endpoints

This document describes the endpoints for managing genres in the role-playing game system.

## Base Path
`/api/genres`

## Endpoints

### Get a Genre by ID
- **Method**: GET
- **Path**: `/api/genres/{id}`
- **Permissions**: None (public access)
- **Path Parameters**:
  - `id` (integer, required): The ID of the genre
- **Response**:
  - **200 Success**: Returns the genre details
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "nom": "Fantasy"
      },
      "message": null
    }
    ```
  - **404 Not Found**: Genre not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Genre non trouvé: ..."
    }
    ```

### List Genres
- **Method**: GET
- **Path**: `/api/genres`
- **Permissions**: None (public access)
- **Query Parameters**:
  - `q` (string, optional): Search term for genre name
- **Response**:
  - **200 Success**: Returns a list of genres
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "nom": "Fantasy"
        },
        {
          "id": 2,
          "nom": "Science-Fiction"
        }
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

### Create a Genre
- **Method**: POST
- **Path**: `/api/genres`
- **Permissions**: None (assumes admin-only in practice)
- **Request Body**:
  ```json
  {
    "nom": "New Genre"
  }
  ```
  - `nom` (string, required): Genre name (max 100 characters)
- **Response**:
  - **201 Created**: Genre created successfully
    ```json
    {
      "status": "success",
      "data": {
        "id": 3,
        "nom": "New Genre"
      },
      "message": "Genre créé avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Le nom du genre est requis et ne peut pas être vide"
    }
    ```
  - **409 Conflict**: Genre name already exists
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Un genre avec ce nom existe déjà"
    }
    ```

### Update a Genre
- **Method**: PUT or PATCH
- **Path**: `/api/genres/{id}`
- **Permissions**: None (assumes admin-only in practice)
- **Path Parameters**:
  - `id` (integer, required): The ID of the genre
- **Request Body**:
  ```json
  {
    "nom": "Updated Genre"
  }
  ```
  - `nom` (string, required): Updated genre name (max 100 characters)
- **Response**:
  - **200 Success**: Genre updated successfully
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "nom": "Updated Genre"
      },
      "message": "Genre mis à jour avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Le nom du genre est requis et ne peut pas être vide"
    }
    ```
  - **404 Not Found**: Genre not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Genre non trouvé: ..."
    }
    ```

### Delete a Genre
- **Method**: DELETE
- **Path**: `/api/genres/{id}`
- **Permissions**: None (assumes admin-only in practice)
- **Path Parameters**:
  - `id` (integer, required): The ID of the genre
- **Response**:
  - **204 No Content**: Genre deleted successfully
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Genre supprimé avec succès"
    }
    ```
  - **404 Not Found**: Genre not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Genre non trouvé: ..."
    }
    ```