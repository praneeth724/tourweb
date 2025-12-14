<?php
/**
 * User Login Handler
 * CeylonEcoTrails Authentication System
 */

// Start session
session_start();

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
    if (!isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Email and password are required');
    }

    $email = sanitizeInput($data['email']);
    $password = $data['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        throw new Exception('Email and password cannot be empty');
    }

    // Get database connection
    $conn = getDatabaseConnection();

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Query to find user by email
    $sql = "SELECT id, username, email, password_hash, full_name, is_active FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Invalid email or password');
    }

    $user = $result->fetch_assoc();

    // Check if account is active
    if ($user['is_active'] != 1) {
        throw new Exception('Your account has been deactivated. Please contact support.');
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        throw new Exception('Invalid email or password');
    }

    // Update last login time
    $updateLogin = "UPDATE users SET last_login = NOW() WHERE id = ?";
    $updateStmt = $conn->prepare($updateLogin);
    $updateStmt->bind_param("i", $user['id']);
    $updateStmt->execute();
    $updateStmt->close();

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['logged_in'] = true;

    // Success response
    $response = [
        'success' => true,
        'message' => 'Login successful!',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'fullName' => $user['full_name']
        ],
        'redirect' => 'index.html'
    ];

    echo json_encode($response);

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
