<?php
/**
 * Test Database Setup Script
 * Automatically creates test database and imports schema
 * 
 * Access via browser: http://localhost/language-learning-chatbot-MIU/tests/setup_test_database.php
 * Or via command line: C:\xampp\php\php.exe tests/setup_test_database.php
 */

// Load .env file (required by project)
require_once __DIR__ . '/../config/load_env.php';

// Use test-specific environment variables (fallback to defaults)
$db_server = getenv('DB_TEST_SERVER') ?: 'localhost';
$db_user = getenv('DB_TEST_USER') ?: 'root';
$db_pass = getenv('DB_TEST_PASS') ?: '';
$db_name = getenv('DB_TEST_NAME') ?: 'test_language_learning_chatbot';

echo "Setting up test database...\n";

// Connect without selecting database
$conn = mysqli_connect($db_server, $db_user, $db_pass);

if (!$conn) {
    die("❌ Could not connect to MySQL server!\n");
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS `$db_name`";
if (mysqli_query($conn, $sql)) {
    echo "✅ Database '$db_name' created/verified\n";
} else {
    die("❌ Error creating database: " . mysqli_error($conn) . "\n");
}

// Select the database
mysqli_select_db($conn, $db_name);

// Read and execute schema file
$schema_file = __DIR__ . '/../database/schema.sql';
if (!file_exists($schema_file)) {
    die("❌ Schema file not found: $schema_file\n");
}

echo "Importing schema...\n";
$sql = file_get_contents($schema_file);

// Use multi_query to handle multiple statements
if (mysqli_multi_query($conn, $sql)) {
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    
    if (mysqli_errno($conn)) {
        $error = mysqli_error($conn);
        // Ignore "already exists" errors
        if (strpos($error, 'already exists') === false && strpos($error, 'Duplicate') === false) {
            echo "⚠️  Warning: " . $error . "\n";
        }
    }
} else {
    $error = mysqli_error($conn);
    if (strpos($error, 'already exists') === false && strpos($error, 'Duplicate') === false) {
        die("❌ Error executing SQL: " . $error . "\n");
    }
}

echo "✅ Test database setup complete!\n";
echo "You can now run: vendor/bin/phpunit\n";
