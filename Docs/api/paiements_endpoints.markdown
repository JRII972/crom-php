# Paiements API Endpoints

This document describes the endpoints for managing payments and notifications in the role-playing game system.

## Base Path
`/api/paiements`

## Endpoints

### Get a Payment by ID
- **Method**: GET
- **Path**: `/api/paiements/{id}`
- **Permissions**: Requires `PaiementsApi:read` (ADMINISTRATEUR)
- **Path Parameters**:
  - `id` (string, required): The ID of the payment
- **Response**:
  - **200 Success**: Returns payment details
    ```json
    {
      "status": "success",
      "data": {
        "id": "uuid",
        "montant": 50.00,
        "devise": "EUR",
        ...
      },
      "message": null
    }
    ```
  - **403 Forbidden**: Not admin
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Seul un admin peut accéder aux paiements"
    }
    ```
  - **404 Not Found**: Payment not found
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Paiement non trouvé: ..."
    }
    ```

### List Payments
- **Method**: GET
- **Path**: `/api/paiements`
- **Permissions**: Requires `PaiementsApi:read` (ADMINISTRATEUR)
- **Query Parameters**:
  - `utilisateur_id` (string, optional): Filter by user ID
  - `statut` (string, optional): Filter by payment status
- **Response**:
  - **200 Success**: Returns a list of payments
    ```json
    {
      "status": "success",
      "data": [
        {
          "id": "uuid",
          ...
        },
        ...
      ],
      "message": null
    }
    ```
  - **403 Forbidden**: Not admin
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Seul un admin peut accéder aux paiements"
    }
    ```

### Create a Payment Notification
- **Method**: POST
- **Path**: `/api/paiements/notifications/helloasso`
- **Permissions**: None (verified by signature)
- **Request Body**:
  ```json
  {
    "id": "notification_id",
    "type_evenement": "PAYMENT",
    "date_evenement": "2025-06-01T12:00:00Z",
    "donnees": {
      "montant": 50.00,
      "devise": "EUR",
      "id_utilisateur": "uuid",
      ...
    }
  }
  ```
  - `id` (string, required): Notification ID
  - `type_evenement` (string, required): Event type
  - `date_evenement` (string, required): Event date
  - `donnees` (object, required): Payment data
- **Headers**:
  - `X-Helloasso-Signature`: HMAC SHA256 signature
- **Response**:
  - **201 Created**: Notification recorded
    ```json
    {
      "status": "success",
      "data": {
        "id": "notification_id",
        ...
      },
      "message": "Notification enregistrée avec succès"
    }
    ```
  - **401 Unauthorized**: Invalid signature
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Signature invalide"
    }
    ```
  - **409 Conflict**: Notification ID already exists
    ```json
    {
      "status": "error",
      "data": null,
      "message": "Notification avec cet ID existe déjà"
    }
    ```