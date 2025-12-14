<?php
/**
 * User Registration Handler
 * CeylonEcoTrails Authentication System
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON response header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once 'db_config.php';

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate password strength
function isStrongPassword($password) {
    // Minimum 6 characters
    if (strlen($password) < 6) {
        return false;
    }
    return true;
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get JSON data from request body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    // Check if JSON was decoded successfully
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $requiredFields = ['fullName', 'username', 'email', 'password'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Sanitize and validate input data
    $fullName = sanitizeInput($data['fullName']);
    $username = sanitizeInput($data['username']);
    $email = sanitizeInput($data['email']);
    $password = $data['password']; // Don't sanitize password

    // Validate full name
    if (strlen($fullName) < 2 || strlen($fullName) > 100) {
        throw new Exception('Full name must be between 2 and 100 characters');
    }

    // Validate username
    if (strlen($username) < 3 || strlen($username) > 50) {
        throw new Exception('Username must be between 3 and 50 characters');
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('Username can only contain letters, numbers, and underscores');
    }

    // Validate email
    if (!isValidEmail($email)) {
        throw new Exception('Invalid email address');
    }

    // Validate password strength
    if (!isStrongPassword($password)) {
        throw new Exception('Password must be at least 6 characters long');
    }

    // Get database connection
    $conn = getDatabaseConnection();

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Check if username already exists
    $checkUsername = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkUsername);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        throw new Exception('Username already exists');
    }
    $stmt->close();

    // Check if email already exists
    $checkEmail = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        throw new Exception('Email already registered');
    }
    $stmt->close();

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $sql = "INSERT INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("ssss", $username, $email, $passwordHash, $fullName);

    if ($stmt->execute()) {
        $userId = $stmt->insert_id;

        // Success response
        $response = [
            'success' => true,
            'message' => 'Account created successfully!',
            'userId' => $userId,
            'username' => $username
        ];

        echo json_encode($response);

    } else {
        throw new Exception('Failed to create account: ' . $stmt->error);
    }

    // Close statement and connection
    $stmt->close();
    closeDatabaseConnection($conn);

} catch (Exception $e) {
    // Error response
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];

    echo json_encode($response);
}
?>
