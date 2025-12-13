<?php
/**
 * Admin Page - View All Bookings
 * CeylonEcoTrails Booking System
 *
 * This page displays all bookings from the database
 */

// Include database configuration
require_once 'db_config.php';

// Get database connection
$conn = getDatabaseConnection();

if (!$conn) {
    die("Database connection failed. Please check your configuration.");
}

// Get filter and sort parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at DESC';

// Build SQL query based on filter
$sql = "SELECT * FROM bookings";

if ($filter === 'today') {
    $sql .= " WHERE DATE(created_at) = CURDATE()";
} elseif ($filter === 'week') {
    $sql .= " WHERE YEARWEEK(created_at) = YEARWEEK(NOW())";
} elseif ($filter === 'month') {
    $sql .= " WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
}

$sql .= " ORDER BY " . $sort;

// Execute query
$result = $conn->query($sql);

// Get booking statistics
$statsQuery = "SELECT * FROM booking_stats LIMIT 1";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - CeylonEcoTrails Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-green-800 text-white py-6 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold"><i class="fas fa-mountain text-green-400"></i> CeylonEcoTrails</h1>
                    <p class="text-green-200 mt-1">Booking Management System</p>
                </div>
                <a href="booking.html" class="bg-green-600 hover:bg-green-700 px-6 py-3 rounded-full font-semibold transition">
                    <i class="fas fa-plus mr-2"></i> New Booking
                </a>
            </div>
        </div>
    </header>

    <!-- Statistics Dashboard -->
    <section class="py-8 bg-white shadow-sm">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-green-500 to-green-700 text-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm uppercase tracking-wide">Total Bookings</p>
                            <h2 class="text-4xl font-bold mt-2"><?php echo $stats['total_bookings'] ?? 0; ?></h2>
                        </div>
                        <i class="fas fa-calendar-check text-5xl opacity-30"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm uppercase tracking-wide">Total Revenue</p>
                            <h2 class="text-4xl font-bold mt-2">$<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h2>
                        </div>
                        <i class="fas fa-dollar-sign text-5xl opacity-30"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-700 text-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm uppercase tracking-wide">Today's Bookings</p>
                            <h2 class="text-4xl font-bold mt-2">
                                <?php
                                $todayQuery = "SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = CURDATE()";
                                $todayResult = $conn->query($todayQuery);
                                $todayCount = $todayResult->fetch_assoc();
                                echo $todayCount['count'] ?? 0;
                                ?>
                            </h2>
                        </div>
                        <i class="fas fa-calendar-day text-5xl opacity-30"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters and Search -->
    <section class="py-6 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">All Bookings</h2>
                        <p class="text-gray-600">Manage and view all tour bookings</p>
                    </div>
                    <div class="flex gap-3">
                        <select onchange="window.location.href='?filter=' + this.value + '&sort=<?php echo $sort; ?>'" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none">
                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Time</option>
                            <option value="today" <?php echo $filter === 'today' ? 'selected' : ''; ?>>Today</option>
                            <option value="week" <?php echo $filter === 'week' ? 'selected' : ''; ?>>This Week</option>
                            <option value="month" <?php echo $filter === 'month' ? 'selected' : ''; ?>>This Month</option>
                        </select>
                        <select onchange="window.location.href='?filter=<?php echo $filter; ?>&sort=' + this.value" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 outline-none">
                            <option value="created_at DESC" <?php echo $sort === 'created_at DESC' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="created_at ASC" <?php echo $sort === 'created_at ASC' ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="total_amount DESC" <?php echo $sort === 'total_amount DESC' ? 'selected' : ''; ?>>Highest Amount</option>
                            <option value="total_amount ASC" <?php echo $sort === 'total_amount ASC' ? 'selected' : ''; ?>>Lowest Amount</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bookings Table -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Booking ID</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Reference</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Customer</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Tour</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Date</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">People</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Amount</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $statusColor = $row['booking_status'] === 'confirmed' ? 'green' : ($row['booking_status'] === 'cancelled' ? 'red' : 'yellow');
                                    echo "<tr class='hover:bg-gray-50 transition'>";
                                    echo "<td class='px-6 py-4 text-sm font-semibold text-gray-800'>#" . $row['id'] . "</td>";
                                    echo "<td class='px-6 py-4 text-sm font-mono text-green-600'>" . htmlspecialchars($row['booking_reference']) . "</td>";
                                    echo "<td class='px-6 py-4'>";
                                    echo "<div class='font-semibold text-gray-800'>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</div>";
                                    echo "<div class='text-sm text-gray-600'>" . htmlspecialchars($row['email']) . "</div>";
                                    echo "<div class='text-sm text-gray-600'>" . htmlspecialchars($row['phone']) . "</div>";
                                    echo "</td>";
                                    echo "<td class='px-6 py-4 text-sm text-gray-800'>" . htmlspecialchars($row['tour_name']) . "</td>";
                                    echo "<td class='px-6 py-4 text-sm text-gray-800'>" . date('M d, Y', strtotime($row['preferred_date'])) . "</td>";
                                    echo "<td class='px-6 py-4 text-sm text-gray-800 text-center'>" . $row['number_of_people'] . "</td>";
                                    echo "<td class='px-6 py-4 text-sm font-bold text-gray-800'>" . $row['currency'] . " " . number_format($row['total_amount'], 2) . "</td>";
                                    echo "<td class='px-6 py-4'>";
                                    echo "<span class='px-3 py-1 rounded-full text-xs font-semibold bg-{$statusColor}-100 text-{$statusColor}-800'>" . ucfirst($row['booking_status']) . "</span>";
                                    echo "</td>";
                                    echo "<td class='px-6 py-4 text-sm text-gray-600'>" . date('M d, Y H:i', strtotime($row['created_at'])) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='9' class='px-6 py-12 text-center text-gray-500'>";
                                echo "<i class='fas fa-inbox text-6xl text-gray-300 mb-4'></i>";
                                echo "<p class='text-lg font-semibold'>No bookings found</p>";
                                echo "<p class='text-sm'>Start by creating your first booking</p>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; 2024 CeylonEcoTrails. Booking Management System.</p>
        </div>
    </footer>
</body>
</html>

<?php
// Close database connection
closeDatabaseConnection($conn);
?>
