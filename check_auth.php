<?php
/**
 * Authentication Check Helper
 * Include this file at the top of protected pages
 */

session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to require login (redirect if not logged in)
function requireLogin($redirectTo = 'login.html') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirectTo);
        exit();
    }
}

// Function to get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'fullName' => $_SESSION['full_name'] ?? null
    ];
}
?>
