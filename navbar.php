<!-- Dynamic Navigation with Auth -->
<?php
session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $isLoggedIn ? $_SESSION['full_name'] ?? 'User' : '';
$userNameShort = $isLoggedIn ? explode(' ', $userName)[0] : '';
?>
<nav id="navbar" class="fixed w-full z-50 transition-all duration-300">
  <div class="container mx-auto px-4 py-4">
    <div class="flex items-center justify-between">
      <div class="text-white text-2xl font-bold">
        <i class="fas fa-mountain text-green-400"></i> CeylonEcoTrails
      </div>
      <div class="hidden md:flex items-center space-x-8">
        <a href="index.php" class="text-white hover:text-green-400 transition">Home</a>
        <a href="about.php" class="text-white hover:text-green-400 transition">About Us</a>
        <a href="tours.php" class="text-white hover:text-green-400 transition">Tours</a>
        <a href="events.php" class="text-white hover:text-green-400 transition">Events</a>
        <a href="gallery.php" class="text-white hover:text-green-400 transition">Gallery</a>
        <a href="contact.php" class="text-white hover:text-green-400 transition">Contact</a>
       
        <?php if ($isLoggedIn): ?>
          <!-- Logged In Menu -->
          <div class="relative group">
            <button class="flex items-center gap-2 text-white hover:text-green-400 transition">
              <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                <span class="text-white font-semibold text-sm"><?php echo strtoupper(substr($userNameShort, 0, 1)); ?></span>
              </div>
              <span><?php echo htmlspecialchars($userNameShort); ?></span>
              <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <!-- Dropdown -->
            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
              <a href="dashboard.php" class="block px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition rounded-t-lg">
                <i class="fas fa-th-large mr-2"></i>Dashboard
              </a>
              <a href="booking.html" class="block px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                <i class="fas fa-calendar-plus mr-2"></i>Book Tour
              </a>
              <a href="logout.php" class="block px-4 py-3 text-red-600 hover:bg-red-50 transition rounded-b-lg">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
              </a>
            </div>
          </div>
        <?php else: ?>
          <!-- Not Logged In -->
          <a href="login.html" class="text-white hover:text-green-400 transition flex items-center gap-2">
            <i class="fas fa-user"></i> Login
          </a>
          <a href="booking.html" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition">Book Now</a>
        <?php endif; ?>
      </div>
      <button id="mobile-menu-btn" class="md:hidden text-white text-2xl">
        <i class="fas fa-bars"></i>
      </button>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden mt-4 space-y-4">
      <a href="index.php" class="block text-white hover:text-green-400 transition">Home</a>
      <a href="about.php" class="block text-white hover:text-green-400 transition">About Us</a>
      <a href="tours.php" class="block text-white hover:text-green-400 transition">Tours</a>
      <a href="events.php" class="block text-white hover:text-green-400 transition">Events</a>
      <a href="gallery.php" class="block text-white hover:text-green-400 transition">Gallery</a>
      <a href="contact.php" class="block text-white hover:text-green-400 transition">Contact</a>
      
      <?php if ($isLoggedIn): ?>
        <a href="dashboard.php" class="block text-white hover:text-green-400 transition">
          <i class="fas fa-th-large mr-2"></i>Dashboard
        </a>
        <a href="logout.php" class="block bg-red-600 text-white px-6 py-2 rounded-full hover:bg-red-700 transition text-center">
          Logout
        </a>
      <?php else: ?>
        <a href="login.html" class="block text-white hover:text-green-400 transition">
          <i class="fas fa-user mr-2"></i>Login
        </a>
        <a href="booking.html" class="block bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition text-center">Book Now</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
