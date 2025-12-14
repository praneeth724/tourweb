<?php
/**
 * Contact Form Handler
 * CeylonEcoTrails Contact System
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
    $requiredFields = ['name', 'email', 'subject', 'message'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Sanitize and validate input data
    $name = sanitizeInput($data['name']);
    $email = sanitizeInput($data['email']);
    $phone = isset($data['phone']) ? sanitizeInput($data['phone']) : '';
    $subject = sanitizeInput($data['subject']);
    $message = sanitizeInput($data['message']);

    // Validate name
    if (strlen($name) < 2 || strlen($name) > 100) {
        throw new Exception('Name must be between 2 and 100 characters');
    }

    // Validate email
    if (!isValidEmail($email)) {
        throw new Exception('Invalid email address');
    }

    // Validate subject
    if (strlen($subject) < 5 || strlen($subject) > 200) {
        throw new Exception('Subject must be between 5 and 200 characters');
    }

    // Validate message
    if (strlen($message) < 10) {
        throw new Exception('Message must be at least 10 characters long');
    }

    // Get database connection
    $conn = getDatabaseConnection();

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Insert contact submission
    $sql = "INSERT INTO contact_submissions (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        $submissionId = $stmt->insert_id;

        // Success response
        $response = [
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.',
            'submissionId' => $submissionId
        ];

        // Optional: Send email notification to admin
        // mail('admin@ceylonecotrails.lk', 'New Contact Form Submission', $message);

        echo json_encode($response);

    } else {
        throw new Exception('Failed to save contact submission: ' . $stmt->error);
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
