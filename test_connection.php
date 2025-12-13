<?php
/**
 * Database Connection Test
 * Use this file to diagnose connection issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>MAMP Database Connection Test</h2>";
echo "<hr>";

// Test 1: Check PHP and MySQL extension
echo "<h3>Test 1: PHP Configuration</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "MySQL Extension Loaded: " . (extension_loaded('mysqli') ? 'Yes ✓' : 'No ✗') . "<br>";
echo "<hr>";

// Database credentials
$host = 'localhost';
$port = '3306'; // MAMP default is usually 3306 or 8889
$user = 'root';
$pass = 'root';
$dbname = 'ceylonecotrails';

// Test 2: Try connection without port
echo "<h3>Test 2: Connection to localhost (no port specified)</h3>";
$conn1 = @new mysqli($host, $user, $pass);
if ($conn1->connect_error) {
    echo "Failed ✗: " . $conn1->connect_error . "<br>";
} else {
    echo "Success ✓: Connected to MySQL<br>";
    echo "MySQL Version: " . $conn1->server_info . "<br>";
    $conn1->close();
}
echo "<hr>";

// Test 3: Try connection with port 3306
echo "<h3>Test 3: Connection to localhost:3306</h3>";
$conn2 = @new mysqli($host, $user, $pass, '', $port);
if ($conn2->connect_error) {
    echo "Failed ✗: " . $conn2->connect_error . "<br>";
} else {
    echo "Success ✓: Connected to MySQL on port 3306<br>";
    echo "MySQL Version: " . $conn2->server_info . "<br>";
    $conn2->close();
}
echo "<hr>";

// Test 4: Try connection with port 8889 (common MAMP port)
echo "<h3>Test 4: Connection to localhost:8889</h3>";
$conn3 = @new mysqli($host, $user, $pass, '', '8889');
if ($conn3->connect_error) {
    echo "Failed ✗: " . $conn3->connect_error . "<br>";
} else {
    echo "Success ✓: Connected to MySQL on port 8889<br>";
    echo "MySQL Version: " . $conn3->server_info . "<br>";
    $conn3->close();
}
echo "<hr>";

// Test 5: Try with 127.0.0.1 instead of localhost
echo "<h3>Test 5: Connection to 127.0.0.1:3306</h3>";
$conn4 = @new mysqli('127.0.0.1', $user, $pass, '', $port);
if ($conn4->connect_error) {
    echo "Failed ✗: " . $conn4->connect_error . "<br>";
} else {
    echo "Success ✓: Connected to MySQL using IP address<br>";
    echo "MySQL Version: " . $conn4->server_info . "<br>";
    $conn4->close();
}
echo "<hr>";

// Test 6: Try with socket path (common for MAMP)
echo "<h3>Test 6: Connection with MAMP socket</h3>";
$socket = '/Applications/MAMP/tmp/mysql/mysql.sock'; // Mac
if (!file_exists($socket)) {
    $socket = 'C:/MAMP/tmp/mysql/mysql.sock'; // Windows
}
if (file_exists($socket)) {
    echo "Socket found: $socket<br>";
    $conn5 = @new mysqli($host, $user, $pass, '', null, $socket);
    if ($conn5->connect_error) {
        echo "Failed ✗: " . $conn5->connect_error . "<br>";
    } else {
        echo "Success ✓: Connected via socket<br>";
        echo "MySQL Version: " . $conn5->server_info . "<br>";
        $conn5->close();
    }
} else {
    echo "Socket not found at: $socket<br>";
}
echo "<hr>";

// Test 7: Check if database exists (using successful connection)
echo "<h3>Test 7: Check if database exists</h3>";

// Try different connection methods
$test_conn = null;
$connection_methods = [
    ['host' => $host, 'port' => $port],
    ['host' => '127.0.0.1', 'port' => $port],
    ['host' => $host, 'port' => '8889'],
];

foreach ($connection_methods as $method) {
    $test_conn = @new mysqli($method['host'], $user, $pass, '', $method['port']);
    if (!$test_conn->connect_error) {
        echo "Connected using {$method['host']}:{$method['port']}<br>";
        break;
    }
}

if ($test_conn && !$test_conn->connect_error) {
    // Check if database exists
    $result = $test_conn->query("SHOW DATABASES LIKE '$dbname'");
    if ($result && $result->num_rows > 0) {
        echo "Database '$dbname' exists ✓<br>";

        // Try to connect to the database
        $db_conn = @new mysqli($method['host'], $user, $pass, $dbname, $method['port']);
        if ($db_conn->connect_error) {
            echo "Cannot connect to database '$dbname' ✗: " . $db_conn->connect_error . "<br>";
        } else {
            echo "Successfully connected to database '$dbname' ✓<br>";

            // Check if bookings table exists
            $table_check = $db_conn->query("SHOW TABLES LIKE 'bookings'");
            if ($table_check && $table_check->num_rows > 0) {
                echo "Table 'bookings' exists ✓<br>";
            } else {
                echo "Table 'bookings' does NOT exist ✗<br>";
                echo "<strong>Action needed: Run database_setup.sql</strong><br>";
            }
            $db_conn->close();
        }
    } else {
        echo "Database '$dbname' does NOT exist ✗<br>";
        echo "<strong>Action needed: Create database '$dbname' in phpMyAdmin</strong><br>";
    }
    $test_conn->close();
} else {
    echo "Could not connect to MySQL to check database ✗<br>";
}
echo "<hr>";

// Summary and recommendations
echo "<h3>Recommendations:</h3>";
echo "<ul>";
echo "<li>Make sure MAMP is running (both Apache and MySQL)</li>";
echo "<li>Check MAMP's MySQL port in MAMP preferences</li>";
echo "<li>Create database 'ceylonecotrails' if it doesn't exist</li>";
echo "<li>Run database_setup.sql to create tables</li>";
echo "<li>Update db_config.php with the correct host and port</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Next step:</strong> Based on the successful connection above, update your db_config.php file accordingly.</p>";
?>
