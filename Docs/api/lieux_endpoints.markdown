# Lieux API Endpoints

This document describes the endpoints for managing locations and their schedules in the role-playing game system.

## Base Path
`/api/lieux`

## Endpoints

### List Locations
- **Method**: GET
- **Path**: `/api/lieux`
- **Permissions**: None (public access)
- **Query Parameters**:
  - `latitude` (float, optional): Latitude for geographic search
  - `longitude` (float, optional): Longitude for geographic search
  - `rayon` (float, optional): Radius in kilometers
  - `keyword` (string, optional): Search term for name or address
- **Response**:
  - **200 Success**: Returns a list of locations
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "nom": "Community Center",
          "adresse": "123 Main St",
          ...
        },
        ...
      ],
      "message": null
    }
    ```
  - **400 Bad Request**: Invalid coordinates or radius
    ```json
    {
      "status": "error",
      "data": null,
      "message": "La latitude doit être comprise entre -90 et 90"
    }
    ```

### Get a Location by ID
- **Method**: GET
- **Path**: `/api/lieux/{id}`
- **Permissions**: None (public access)
- **Path Parameters**:
  - `id` (integer, required): The ID of the location
- **Response**:
  - **200 Success**: Returns location details
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        "nom": "Community Center",
        ...
      },
      "message": null
    }
    ```
  - **404 Not Found**: Location not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Lieu non trouvé: ..."
    }
    ```

### Create a Location
- **Method**: POST
- **Path**: `/api/lieux`
- **Permissions**: Requires `LieuApi:write`
- **Request Body**:
  ```json
  {
    "nom": "New Location",
    "adresse": "456 Elm St",
    "latitude": 48.8566,
    "longitude": 2.3522,
    "description": "A new venue"
  }
  ```
  - `nom` (string, required): Location name
  - `adresse` (string, optional): Address
  - `latitude` (float, optional): Latitude
  - `longitude` (float, optional): Longitude
  - `description` (string, optional): Description
- **Response**:
  - **201 Created**: Location created
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Lieu créé avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Le nom du lieu est requis et ne peut pas être vide"
    }
    ```

### Update a Location
- **Method**: PUT
- **Path**: `/api/lieux/{id}`
- **Permissions**: Requires `LieuApi:write`
- **Path Parameters**:
  - `id` (integer, required): The ID of the location
- **Request Body**: Same as POST
- **Response**:
  - **200 Success**: Location updated
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Lieu mis à jour avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Le nom du lieu est requis et ne peut pas être vide"
    }
    ```

### Delete a Location
- **Method**: DELETE
- **Path**: `/api/lieux/{id}`
- **Permissions**: Requires `LieuApi:delete`
- **Path Parameters**:
  - `id` (integer, required): The ID of the location
- **Response**:
  - **204 No Content**: Location deleted
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Lieu supprimé avec succès"
    }
    ```
  - **404 Not Found**: Location not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Lieu non trouvé: ..."
    }
    ```

### Get Location Schedules
- **Method**: GET
- **Path**: `/api/lieux/{id}/horaires`
- **Permissions**: None (public access)
- **Path Parameters**:
  - `id` (integer, required): The ID of the location
- **Response**:
  - **200 Success**: Returns list of schedules
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": 1,
          "heure_debut": "09:00:00",
          "heure_fin": "17:00:00",
          "type_recurrence": "HEBDOMADAIRE",
          ...
        },
        ...
      ],
      "message": null
    }
    ```
  - **500 Server Error**: Error retrieving schedules
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Erreur lors de la récupération des horaires: ..."
    }
    ```

### Add a Schedule to a Location
- **Method**: POST
- **Path**: `/api/lieux/{id}/horaires`
- **Permissions**: Requires `LieuApi:write`
- **Path Parameters**:
  - `id` (integer, required): The ID of the location
- **Request Body**:
  ```json
  {
    "heure_debut": "09:00:00",
    "heure_fin": "17:00:00",
    "type_recurrence": "HEBDOMADAIRE",
    "regle_recurrence": "FREQ=WEEKLY;BYDAY=MO",
    "exceptions": ["2025-12-25"]
  }
  ```
  - `heure_debut` (string, required): Start time
  - `heure_fin` (string, required): End time
  - `type_recurrence` (string, required): Recurrence type
  - `regle_recurrence` (string, optional): Recurrence rule
  - `exceptions` (array, optional): Exception dates
- **Response**:
  - **201 Created**: Schedule added
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Horaire ajouté avec succès"
    }
    ```
  - **400 Bad Request**: Invalid input
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Heure de début, heure de fin et type de récurrence requis"
    }
    ```

### Update a Schedule
- **Method**: PATCH
- **Path**: `/api/lieux/{id}/horaires`
- **Permissions**: Requires `LieuApi:write`
- **Path Parameters**:
  - `id` (integer, required): The ID of the location
- **Request Body**:
  ```json
  {
    "id_horaire": 1,
    "heure_debut": "10:00:00",
    ...
  }
  ```
  - `id_horaire` (integer, required): ID of the schedule
  - Other fields are optional
- **Response**:
  - **200 Success**: Schedule updated
    ```json
    {
      "status": "success",
      "data": {
        "id": 1,
        ...
      },
      "message": "Horaire mis à jour avec succès"
    }
    ```
  - **403 Forbidden**: Schedule does not belong to location
    ```json
    {
      "status": "error",
      "data": null,
      "message": "L'horaire n'apactivitent pas à ce lieu"
    }
    ```

### Delete a Schedule
- **Method**: DELETE
- **Path**: `/api/lieux/{id}/horaires`
- **Permissions**: Requires `LieuApi:delete`
- **Path Parameters**:
  - `id` (integer, required): The ID of the location
- **Request Body**:
  ```json
  {
    "id_horaire": 1
  }
  ```
  - `id_horaire` (integer, required): ID of the schedule
- **Response**:
  - **204 No Content**: Schedule deleted
    ```json
    {
      "status": "success",
      "data": null,
      "message": "Horaire supprimé avec succès"
    }
    ```
  - **403 Forbidden**: Schedule does not belong to location
    ```json
    {
      "status": "error",
      "data": null,
      "message": "L'horaire n'apactivitent pas à ce lieu"
    }
    ```