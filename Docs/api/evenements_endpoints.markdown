# Evenements API Endpoints

This document describes the endpoints for managing events in the role-playing game system.

## Base Path
`/api/evenements`

## Endpoints

### Get an Event by ID
- **Method**: GET
- **Path**: `/api/evenements/{id}`
- **Permissions**: None (public access)
- **Path Parameters**:
  - `id` (integer, required): The ID of the event
- **Response**:
  - **200 Success**: Returns event details
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "nom": "Game Convention",
        ...
      },
      "message": null
    }
    ```
  - **404 Not Found**: Event not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Événement non trouvé: ..."
    }
    ```

### List Events
- **Method**: GET
- **Path**: `/api/evenements`
- **Permissions**: None (public access)
- **Query Parameters**:
  - `date_debut` (string, optional): Filter by start date
- **Response**:
  - **200 Success**: Returns a list of events
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

### Create an Event
- **Method**: POST
- **Path**: `/api/evenements`
- **Permissions**: Requires `EvenementsApi:write` (ADMINISTRATEUR)
- **Request Body**:
  ```json
  {
    "nom": "Game Convention",
    "date_debut": "2025-06-01",
    "date_fin": "2025-06-03",
    "id_lieu": 1,
    ...
  }
  ```
  - `nom` (string, required): Event name
  - `date_debut` (string, required): Start date
  - `date_fin` (string, required): End date
  - `id_lieu` (integer, optional): Location ID
- **Response**:
  - **201 Created**: Event created
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Événement créé avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "nom, date_debut, date_fin requis"
    }
    ```
  - **403 Forbidden**: Not admin
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Seul un admin peut créer un événement"
    }
    ```

### Update an Event
- **Method**: PUT
- **Path**: `/api/evenements/{id}`
- **Permissions**: Requires `EvenementsApi:write` (ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the event
- **Request Body**:
  ```json
  {
    "nom": "Updated Convention",
    ...
  }
  ```
  - All fields are optional
- **Response**:
  - **200 Success**: Event updated
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Événement mis à jour avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "ID de l’événement requis"
    }
    ```
  - **403 Forbidden**: Not admin
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Seul un admin peut modifier un événement"
    }
    ```

### Delete an Event
- **Method**: DELETE
- **Path**: `/api/evenements/{id}`
- **Permissions**: Requires `EvenementsApi:delete` (ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (integer, required): The ID of the event
- **Response**:
  - **204 No Content**: Event deleted
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Événement supprimé avec succès"
    }
    ```
  - **400 Bad Request**: Invalid ID
    ```json
    {
      "status": "error",
      "data": null,
      "message": "ID de l’événement requis"
    }
    ```