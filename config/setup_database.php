<?php
/**
 * Database Setup Script for Language Learning Chatbot
 * Run this file once to automatically create all required tables
 * Access via: http://localhost/language-learning-chatbot-MIU/config/setup_database.php
 */

require_once __DIR__ . '/db_connect.php';

// Check connection
if (!$conn) {
    die("❌ Database connection failed.");
}

// Step 1: Locate the SQL schema file
$sql_file = __DIR__ . '/../database/schema.sql';

// Step 2: Read the SQL file contents
$sql = file_get_contents($sql_file);

// Step 3: Execute the SQL to create tables
if (!mysqli_query($conn, $sql)) {
    die("❌ Error: " . mysqli_error($conn));
}

// Step 4: Insert default admin user from .env
$admin_name = getenv('ADMIN_NAME');
$admin_email = getenv('ADMIN_EMAIL');
$admin_password = password_hash(getenv('ADMIN_PASSWORD'), PASSWORD_DEFAULT);
$admin_role = getenv('ADMIN_ROLE');

$insert_admin = "INSERT IGNORE INTO users (fullname, email, password, role) 
                 VALUES ('$admin_name', '$admin_email', '$admin_password', '$admin_role')";
mysqli_query($conn, $insert_admin);

// Step 5: Close connection
mysqli_close($conn);
echo "✅ Setup completed! Login: $admin_email / " . getenv('ADMIN_PASSWORD');
?>

