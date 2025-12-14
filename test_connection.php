<?php
/**
 * Database Connection Diagnostics
 * Uses the same credentials defined in db_config.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=UTF-8');

echo '<h2>CeylonEcoTrails - Database Connection Test</h2>';
echo '<hr>';

echo '<p>PHP version: ' . phpversion() . '</p>';
echo '<p>MySQLi extension loaded: ' . (extension_loaded('mysqli') ? 'Yes' : 'No') . '</p>';
echo '<hr>';

require_once __DIR__ . '/db_config.php';

/**
 * Attempt a mysqli connection and return the instance on success.
 */
function attemptConnection(string $host, int $port, string $user, string $pass, ?string $db, string $label)
{
    echo '<h3>' . htmlspecialchars($label) . '</h3>';

    $dbNameForConnect = $db ?? '';
    $mysqli = @new mysqli($host, $user, $pass, $dbNameForConnect, $port);

    if ($mysqli->connect_error) {
        echo '<p style="color:#b91c1c;">Failed: ' . htmlspecialchars($mysqli->connect_error) . '</p>';
        return null;
    }

    echo '<p style="color:#15803d;">Success. MySQL server version: ' . htmlspecialchars($mysqli->server_info) . '</p>';
    return $mysqli;
}

$primaryLabel = sprintf(
    'Attempt 1: %s:%s (database: %s) as configured in db_config.php',
    DB_HOST,
    DB_PORT,
    DB_NAME
);

$connection = attemptConnection(DB_HOST, (int) DB_PORT, DB_USER, DB_PASS, DB_NAME, $primaryLabel);

if (!$connection) {
    echo '<p>Trying common fallback combinations...</p>';

    $fallbacks = [
        ['host' => 'localhost', 'port' => 3306],
        ['host' => 'localhost', 'port' => 8889],
        ['host' => '127.0.0.1', 'port' => 3306],
        ['host' => '127.0.0.1', 'port' => 8889],
    ];

    foreach ($fallbacks as $index => $combo) {
        $connection = attemptConnection(
            $combo['host'],
            $combo['port'],
            DB_USER,
            DB_PASS,
            DB_NAME,
            sprintf('Attempt %d: %s:%d', $index + 2, $combo['host'], $combo['port'])
        );

        if ($connection) {
            break;
        }
    }
}

if ($connection) {
    echo '<hr>';
    echo '<h3>Database Checks</h3>';

    // Confirm database exists
    $result = $connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $connection->real_escape_string(DB_NAME) . "'");
    if ($result && $result->num_rows) {
        echo '<p style="color:#15803d;">Database "' . htmlspecialchars(DB_NAME) . '" exists.</p>';

        // Confirm bookings table exists
        $tables = $connection->query("SHOW TABLES LIKE 'bookings'");
        if ($tables && $tables->num_rows) {
            echo '<p style="color:#15803d;">Table "bookings" found.</p>';
        } else {
            echo '<p style="color:#b45309;">Table "bookings" not found. Run database_setup.sql.</p>';
        }
    } else {
        echo '<p style="color:#b91c1c;">Database "' . htmlspecialchars(DB_NAME) . '" does not exist. Create it in phpMyAdmin and import database_setup.sql.</p>';
    }

    $connection->close();
} else {
    echo '<hr>';
    echo '<p style="color:#b91c1c;">Unable to establish any MySQL connection. Verify that MySQL is running in MAMP, confirm the port in MAMP preferences, and update db_config.php.</p>';
}

echo '<hr>';
echo '<p><strong>Next steps:</strong> Use the successful combination above in db_config.php. Then retry the booking form.</p>';
?>
