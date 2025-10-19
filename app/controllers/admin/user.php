<?php
// Load database connection
require_once __DIR__ . '/../../../config/db_connect.php';
ob_clean(); // Clear connection messages

/**
 * Fetch all users from database
 */
function fetchUsers() {
    global $conn;
    
    $sql = "SELECT id, fullname, email, role, created_at FROM users ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $users = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    
    return $users;
}

/**
 * Get total users count
 */
function getTotalUsersCount() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as total FROM users";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    
    return 0;
}
?>
