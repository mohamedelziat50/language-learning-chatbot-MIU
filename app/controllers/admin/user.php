<?php
// Load database connection
require_once __DIR__ . '/../../../config/db_connect.php';
require_once __DIR__ . '/../../models/User.php';
ob_clean(); // Clear connection messages

/**
 * Fetch all users from database
 */
function fetchUsers() {
    global $conn;
    
    $userModel = new User($conn);
    return $userModel->getAll();
}

/**
 * Get total users count
 */
function getTotalUsersCount() {
    global $conn;
    
    $userModel = new User($conn);
    return $userModel->getTotalCount();
}
?>
