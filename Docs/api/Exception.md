### Explanation of `Api\Exception`

- **Purpose**: The `Api\Exception` class standardizes error handling for the API by providing:
  - A user-friendly message (`userMessage`) for API consumers.
  - An HTTP status code (`httpStatusCode`) to set the appropriate response code.
  - An internal message for debugging/logging, inherited from the parent `Exception` class.
  - A method to format the exception as a JSON response (`toJsonResponse`).
- **Properties**:
  - `httpStatusCode`: The HTTP status code (e.g., 400, 404, 409, 500).
  - `userMessage`: A clear message for the end user (e.g., "Jeu non trouvé").
- **Methods**:
  - `getHttpStatusCode()`: Returns the HTTP status code.
  - `getUserMessage()`: Returns the user-friendly message.
  - `toJsonResponse()`: Formats the exception as a JSON array with error details, including file and line for debugging (can be disabled in production).
- **Usage**: The class is thrown in place of direct `sendResponse` calls for errors and caught in a global `try-catch` block to ensure consistent JSON error responses.


### Example Error Response

If a user tries to access a non-existent game (e.g., `GET /api/jeu/999`):

```json
{
    "error": "Jeu non trouvé",
    "code": 0,
    "file": "/path/to/Jeu.php",
    "line": 123
}
```

With HTTP status code 404.

