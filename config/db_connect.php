<?php
/**
 * Database Connection Configuration
 * All database settings are configured here directly
 */

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT');


// Create database connection
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);
 
// Check if connection is successful, otherwise it will show an error message in the browser
if (!$conn) {
    echo "❌ Could not connect to the database!<br>";
    echo "Error: " . mysqli_connect_error() . "<br>";
}
?>