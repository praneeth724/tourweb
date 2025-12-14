<?php
/**
 * Database Configuration File
 * CeylonEcoTrails Booking System
 *
 * IMPORTANT: Update these credentials with your actual database details
 */

// Database configuration
define('DB_HOST', '127.0.0.1');      // Database host (using 127.0.0.1 for MAMP TCP/IP connection)
define('DB_PORT', '3306');            // Database port (3306 for standard, 8889 for MAMP)
define('DB_USER', 'root');            // Database username
define('DB_PASS', 'root');            // Database password
define('DB_NAME', 'ceylonecotrails'); // Database name

// Create database connection
function getDatabaseConnection() {
    try {
        // Create connection with port support for MAMP
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Set charset to utf8mb4 for proper emoji and international character support
        $conn->set_charset("utf8mb4");

        return $conn;

    } catch (Exception $e) {
        // Log error (in production, use proper logging)
        error_log("Database Connection Error: " . $e->getMessage());

        // Return null on failure
        return null;
    }
}

// Function to close database connection
function closeDatabaseConnection($conn) {
    if ($conn && !$conn->connect_error) {
        $conn->close();
    }
}

// Test connection (optional - comment out in production)
// $test_conn = getDatabaseConnection();
// if ($test_conn) {
//     echo "Database connection successful!";
//     closeDatabaseConnection($test_conn);
// } else {
//     echo "Database connection failed!";
// }
?>
