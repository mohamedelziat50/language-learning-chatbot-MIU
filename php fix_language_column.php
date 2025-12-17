<?php
// fix_language_column.php
require_once __DIR__ . '/config/db_connect.php';

try {
    // Add the column if it doesn't exist
    $sql = "ALTER TABLE users 
            ADD COLUMN IF NOT EXISTS selected_language VARCHAR(50) NULL DEFAULT NULL 
            AFTER role";
    
    if ($conn->query($sql)) {
        echo "✓ Column 'selected_language' added successfully!\n";
    } else {
        echo "✗ Error: " . $conn->error . "\n";
    }
    
    // Verify
    $result = $conn->query("DESCRIBE users");
    echo "\nCurrent users table structure:\n";
    echo "================================\n";
    while ($row = $result->fetch_assoc()) {
        echo sprintf("%-20s %-20s %-10s %-10s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'], 
            $row['Key']);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>