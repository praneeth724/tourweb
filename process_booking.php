<?php
/**
 * Booking Submission Handler
 * CeylonEcoTrails Booking System
 *
 * This file processes booking form submissions and saves them to the database
 */

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production, 1 for debugging

// Set JSON response header
header('Content-Type: application/json');

// Allow CORS (adjust for production)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once 'db_config.php';

// Function to generate unique booking reference
function generateBookingReference() {
    return 'CET' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
}

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

// Function to validate date
function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Main processing
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
    $requiredFields = [
        'tour', 'tourPrice', 'firstName', 'lastName', 'email',
        'phone', 'country', 'numberOfPeople', 'preferredDate',
        'paymentMethod', 'currency', 'totalAmount'
    ];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Sanitize and validate input data
    $tour = sanitizeInput($data['tour']);
    $tourPrice = floatval($data['tourPrice']);
    $firstName = sanitizeInput($data['firstName']);
    $lastName = sanitizeInput($data['lastName']);
    $email = sanitizeInput($data['email']);
    $phone = sanitizeInput($data['phone']);
    $country = sanitizeInput($data['country']);
    $numberOfPeople = intval($data['numberOfPeople']);
    $preferredDate = sanitizeInput($data['preferredDate']);
    $specialRequirements = isset($data['specialRequirements']) ? sanitizeInput($data['specialRequirements']) : '';
    $paymentMethod = sanitizeInput($data['paymentMethod']);
    $currency = sanitizeInput($data['currency']);
    $totalAmount = floatval($data['totalAmount']);

    // Validate email
    if (!isValidEmail($email)) {
        throw new Exception('Invalid email address');
    }

    // Validate date
    if (!isValidDate($preferredDate)) {
        throw new Exception('Invalid date format');
    }

    // Validate number of people
    if ($numberOfPeople < 1 || $numberOfPeople > 15) {
        throw new Exception('Number of people must be between 1 and 15');
    }

    // Check if date is in the future
    $today = new DateTime();
    $bookingDate = new DateTime($preferredDate);
    if ($bookingDate < $today) {
        throw new Exception('Booking date must be in the future');
    }

    // Get tour name mapping
    $tourNames = [
        'sinharaja' => 'Sinharaja Rainforest Expedition',
        'knuckles' => 'Knuckles Mountain Waterfalls',
        'cultural' => 'Cultural Heritage Trail',
        'yala' => 'Yala Wildlife Safari Trek',
        'horton' => "Horton Plains & World's End"
    ];

    $tourName = isset($tourNames[$tour]) ? $tourNames[$tour] : $tour;

    // Generate booking reference
    $bookingReference = generateBookingReference();

    // Get database connection
    $conn = getDatabaseConnection();

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Prepare SQL statement
    $sql = "INSERT INTO bookings (
        tour_name, tour_price, first_name, last_name, email, phone,
        country, number_of_people, preferred_date, special_requirements,
        payment_method, currency, total_amount, booking_reference
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        "sdsssssisssds",
        $tourName,
        $tourPrice,
        $firstName,
        $lastName,
        $email,
        $phone,
        $country,
        $numberOfPeople,
        $preferredDate,
        $specialRequirements,
        $paymentMethod,
        $currency,
        $totalAmount,
        $bookingReference
    );

    // Execute statement
    if ($stmt->execute()) {
        $bookingId = $stmt->insert_id;

        // Update booking stats (optional)
        $updateStats = "UPDATE booking_stats SET
                        total_bookings = total_bookings + 1,
                        total_revenue = total_revenue + ?";
        $statsStmt = $conn->prepare($updateStats);
        $statsStmt->bind_param("d", $totalAmount);
        $statsStmt->execute();
        $statsStmt->close();

        // Success response
        $response = [
            'success' => true,
            'message' => 'Booking confirmed successfully!',
            'bookingId' => $bookingId,
            'bookingReference' => $bookingReference,
            'customerName' => $firstName . ' ' . $lastName,
            'email' => $email,
            'tourName' => $tourName,
            'totalAmount' => $totalAmount,
            'currency' => $currency
        ];

        // Optional: Send confirmation email here
        // sendConfirmationEmail($email, $bookingReference, $tourName);

        echo json_encode($response);

    } else {
        throw new Exception('Failed to save booking: ' . $stmt->error);
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
