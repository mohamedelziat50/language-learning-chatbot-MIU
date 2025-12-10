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

// Step 3: Execute the SQL to create tables (handle multiple statements)
if (mysqli_multi_query($conn, $sql)) {
    // Process all results
    do {
        // Store first result set
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    
    // Check for errors
    if (mysqli_errno($conn)) {
        die("❌ Error executing SQL: " . mysqli_error($conn));
    }
} else {
    die("❌ Error: " . mysqli_error($conn));
}

// Step 4: Insert default admin user from .env
$admin_name = getenv('ADMIN_NAME');
$admin_email = getenv('ADMIN_EMAIL');
$admin_password = password_hash(getenv('ADMIN_PASSWORD'), PASSWORD_DEFAULT);
$admin_role = getenv('ADMIN_ROLE');

$insert_admin = "INSERT IGNORE INTO users (name, email, password, role) 
                 VALUES ('$admin_name', '$admin_email', '$admin_password', '$admin_role')";
mysqli_query($conn, $insert_admin);

// Step 5: Close connection
mysqli_close($conn);
echo "✅ Setup completed! Login: $admin_email / " . getenv('ADMIN_PASSWORD');
?>

