# API Documentation

This repository contains the API documentation for a role-playing game management system. The API provides endpoints to manage various resources such as games, genres, users, sessions, locations, payments, events, and activites. Each resource is documented in its own Markdown file within a dedicated subfolder.

## Base URL
All endpoints are relative to the base URL: `/api`.

## Authentication
Most endpoints require authentication via JWT tokens. Include the token in the `Authorization` header as `Bearer <token>`. Some endpoints are restricted to specific user roles (e.g., ADMINISTRATEUR).

## Structure
The documentation is organized into subfolders, each containing a Markdown file detailing the endpoints for a specific resource:

- **jeux**: Manage games (JeuApi)
- **genres**: Manage genres (GenreApi)
- **utilisateurs**: Manage users and their time slots (UtilisateurApi)
- **sessions**: Manage game sessions (SessionsApi)
- **lieux**: Manage locations and their schedules (LieuApi)
- **paiements**: Manage payments and notifications (PaiementsApi)
- **activites**: Manage game activites and members (ActiviteApi)
- **evenements**: Manage events (EvenementsApi)

Each file includes:
- Endpoint paths and HTTP methods
- Required permissions (if any)
- Query parameters, path parameters, and request body schemas
- Example responses for success and error cases

## Getting Started
To explore the API:
1. Navigate to the relevant subfolder for the resource you're interested in.
2. Review the endpoints, including required parameters and permissions.
3. Use tools like Postman or cURL to test the endpoints with appropriate authentication.

For detailed endpoint information, refer to the individual Markdown files in the subfolders.