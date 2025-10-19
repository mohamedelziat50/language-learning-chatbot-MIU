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
if (mysqli_query($conn, $sql)) {
    echo "✅ Database setup completed successfully!";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}

// Step 4: Close the database connection
mysqli_close($conn);
?>

