<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!validateCsrfToken($csrf_token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$user_id = $input['id'] ?? '';
$updates = $input['updates'] ?? [];

if (empty($user_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

$allowed_fields = [
    'first_name' => 'string',
    'last_name' => 'string',
    'birth_date' => 'string',
    'sex' => 'string',
    'discord_id' => 'string',
    'pseudonym' => 'string',
    'email' => 'string',
    'username' => 'string',
    'password' => 'string',
    'user_type' => 'string'
];
$update_fields = [];
$values = [];

foreach ($updates as $key => $value) {
    if (!array_key_exists($key, $allowed_fields)) {
        http_response_code(400);
        echo json_encode(['error' => "Invalid field: $key"]);
        exit;
    }
    if ($key === 'sex' && !in_array($value, ['M', 'F', 'Other'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid sex value']);
        exit;
    }
    if ($key === 'user_type' && !in_array($value, ['NON_REGISTERED', 'REGISTERED', 'ADMINISTRATOR'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user type']);
        exit;
    }
    if ($key === 'password') {
        $salt = bin2hex(random_bytes(16));
        $value = password_hash($value . $salt, PASSWORD_BCRYPT);
        $update_fields[] = "password_hash = ?";
        $update_fields[] = "password_salt = ?";
        $values[] = $value;
        $values[] = $salt;
    } else {
        $update_fields[] = "$key = ?";
        $values[] = $value;
    }
}

if (empty($update_fields)) {
    http_response_code(400);
    echo json_encode(['error' => 'No fields to update']);
    exit;
}

try {
    // Check if username or email is taken (if updated)
    if (isset($updates['username']) || isset($updates['email'])) {
        $conditions = [];
        $check_values = [];
        if (isset($updates['username'])) {
            $conditions[] = "username = ?";
            $check_values[] = $updates['username'];
        }
        if (isset($updates['email'])) {
            $conditions[] = "email = ?";
            $check_values[] = $updates['email'];
        }
        $check_values[] = $user_id;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (" . implode(' OR ', $conditions) . ") AND id != ?");
        $stmt->execute($check_values);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'Username or email already exists']);
            exit;
        }
    }

    $values[] = $user_id;
    $query = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute($values);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    echo json_encode(['message' => 'User updated']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>