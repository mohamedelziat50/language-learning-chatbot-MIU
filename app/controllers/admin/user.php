<?php
// Load database connection
require_once __DIR__ . '/../../../config/db_connect.php';
ob_clean(); // Clear connection messages

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
