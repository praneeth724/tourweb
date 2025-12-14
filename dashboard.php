<?php
/**
 * User Dashboard - Show Bookings
 */

require_once 'check_auth.php';
requireLogin(); // Redirect to login if not authenticated

$user =getCurrentUser();

// Get database connection  
require_once 'db_config.php';
$conn = getDatabaseConnection();

if (!$conn) {
    die("Database connection failed. Please check your configuration.");
}

// Debug: Log the email being searched
error_log("Dashboard: Searching bookings for email: " . $user['email']);

// Fetch user's bookings - ONLY for this user's email
$sql = "SELECT * FROM bookings WHERE email = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Dashboard: Failed to prepare statement: " . $conn->error);
    $bookings = [];
} else {
    $stmt->bind_param("s", $user['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    $stmt->close();
    error_log("Dashboard: Found " . count($bookings) . " bookings for " . $user['email']);
}

// Calculate stats
$totalBookings = count($bookings);
$totalAmount = array_sum(array_column($bookings, 'total_amount'));
$upcomingCount = 0;
foreach($bookings as $booking) {
    if (strtotime($booking['preferred_date']) > time()) {
        $upcomingCount++;
    }
}

closeDatabaseConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - CeylonEcoTrails</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15); }
        .stat-card { background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%); border-left: 4px solid #10b981; }
        .nav-scrolled { background-color: rgba(5, 150, 105, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'navbar.php'; ?>

    <!-- Header Spacing -->
    <div class="h-20"></div>

    <!-- Hero Banner -->
    <div class="gradient-bg text-white py-12">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Welcome back, <?php echo htmlspecialchars(explode(' ', $user['fullName'])[0]); ?>! 👋</h1>
                    <p class="text-green-100">Manage your bookings and explore new adventures</p>
                </div>
                <a href="booking.html" class="bg-white text-green-600 px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="fas fa-plus mr-2"></i>New Booking
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <!-- Stats Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-ticket-alt text-green-600 text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo $totalBookings; ?></span>
                </div>
                <p class="text-gray-600 font-semibold">Total Bookings</p>
            </div>

            <div class="stat-card bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-clock text-blue-600 text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800"><?php echo $upcomingCount; ?></span>
                </div>
                <p class="text-gray-600 font-semibold">Upcoming Tours</p>
            </div>

            <div class="stat-card bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-dollar-sign text-purple-600 text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800">$<?php echo number_format($totalAmount, 0); ?></span>
                </div>
                <p class="text-gray-600 font-semibold">Total Spent</p>
            </div>

            <div class="stat-card bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-user-circle text-orange-600 text-2xl"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-800">Active</span>
                </div>
                <p class="text-gray-600 font-semibold">Account Status</p>
            </div>
        </div>

        <!-- My Bookings -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-list text-green-600 mr-2"></i>My Bookings
                </h2>
                <a href="booking.html" class="text-green-600 hover:text-green-700 font-semibold">
                    <i class="fas fa-plus mr-1"></i>Add New
                </a>
            </div>

            <?php if (empty($bookings)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-600 font-semibold mb-2">No bookings yet</p>
                    <p class="text-gray-500 text-sm mb-4">Start your adventure today!</p>
                    <a href="booking.html" class="inline-block px-6 py-3 gradient-bg text-white rounded-xl font-semibold hover:shadow-lg transition">
                        Book Your First Tour
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Booking Ref</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tour</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">People</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Amount</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): 
                                $isUpcoming = strtotime($booking['preferred_date']) > time();
                                $statusClass = $isUpcoming ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600';
                                $statusText = $isUpcoming ? 'Upcoming' : 'Completed';
                            ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-4">
                                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($booking['booking_reference']); ?></span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($booking['tour_name']); ?></div>
                                    <div class="text-sm text-gray-500">$<?php echo number_format($booking['tour_price'], 0); ?> per person</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-gray-800"><?php echo date('M d, Y', strtotime($booking['preferred_date'])); ?></div>
                                    <div class="text-xs text-gray-500">Booked: <?php echo date('M d', strtotime($booking['created_at'])); ?></div>
                                </td>
                                <td class="px-4 py-4 text-gray-800">
                                    <i class="fas fa-users text-gray-400 mr-1"></i><?php echo $booking['number_of_people']; ?>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-bold text-green-600">$<?php echo number_format($booking['total_amount'], 0); ?></span>
                                    <div class="text-xs text-gray-500"><?php echo strtoupper($booking['currency']); ?></div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $statusClass; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <button class="text-blue-600 hover:text-blue-700 mr-3" onclick="viewBooking('<?php echo $booking['booking_reference']; ?>')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-gray-600 hover:text-gray-700">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Account Info -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-circle text-green-600 mr-2"></i>Account Information
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-user text-green-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Full Name</p>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['fullName']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-at text-green-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Username</p>
                            <p class="font-semibold text-gray-800">@<?php echo htmlspecialchars($user['username']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-envelope text-green-600"></i>
                        <div>
                            <p class="text-xs text-gray-600">Email</p>
                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Tours -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-fire text-orange-600 mr-2"></i>Explore More Tours
                </h3>
                <div class="space-y-3">
                    <a href="booking.html?tour=sinharaja" class="card block p-4 border-2 border-gray-200 rounded-xl hover:border-green-500">
                        <div class="flex items-center gap-4">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-tree text-green-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">Sinharaja Rainforest</h4>
                                <p class="text-sm text-gray-600">3-day • $299</p>
                            </div>
                            <i class="fas fa-arrow-right text-gray-400"></i>
                        </div>
                    </a>
                    <a href="booking.html?tour=yala" class="card block p-4 border-2 border-gray-200 rounded-xl hover:border-green-500">
                        <div class="flex items-center gap-4">
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <i class="fas fa-paw text-yellow-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">Yala Wildlife Safari</h4>
                                <p class="text-sm text-gray-600">2-day • $249</p>
                            </div>
                            <i class="fas fa-arrow-right text-gray-400"></i>
                        </div>
                    </a>
                    <a href="tours.html" class="block text-center text-green-600 hover:text-green-700 font-semibold mt-4">
                        View All Tours <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-12 py-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 CeylonEcoTrails. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('nav-scrolled');
            } else {
                navbar.classList.remove('nav-scrolled');
            }
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        function viewBooking(ref) {
            alert('Viewing booking: ' + ref + '\n\nFull booking details would be shown in a modal here.');
        }
    </script>
</body>
</html>
