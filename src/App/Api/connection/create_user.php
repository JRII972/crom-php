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
$first_name = $input['first_name'] ?? '';
$last_name = $input['last_name'] ?? '';
$birth_date = $input['birth_date'] ?? '';
$sex = $input['sex'] ?? '';
$discord_id = $input['discord_id'] ?? null;
$pseudonym = $input['pseudonym'] ?? null;
$email = $input['email'] ?? '';
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';
$user_type = $input['user_type'] ?? 'REGISTERED';

$required_fields = ['first_name', 'last_name', 'birth_date', 'sex', 'email', 'username', 'password'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => ucfirst($field) . ' is required']);
        exit;
    }
}

if (!in_array($sex, ['M', 'F', 'Other'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid sex value']);
    exit;
}

if (!in_array($user_type, ['NON_REGISTERED', 'REGISTERED', 'ADMINISTRATOR'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid user type']);
    exit;
}

try {
    // Check if username or email exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Username or email already exists']);
        exit;
    }

    $id = generateUUID();
    $salt = bin2hex(random_bytes(16));
    $password_hash = password_hash($password . $salt, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("
        INSERT INTO users (id, first_name, last_name, birth_date, sex, discord_id, pseudonym, email, username, password_hash, password_salt, user_type, registration_date, old_user, first_connection, lifetime_membership)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), FALSE, TRUE, FALSE)
    ");
    $stmt->execute([
        $id, $first_name, $last_name, $birth_date, $sex, $discord_id, $pseudonym, $email, $username, $password_hash, $salt, $user_type
    ]);

    echo json_encode(['message' => 'User created', 'user_id' => $id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>