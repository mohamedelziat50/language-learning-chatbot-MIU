<?php
// Migration script to convert selected_language VARCHAR to foreign key INT UNSIGNED
require_once __DIR__ . '/../config/db_connect.php';

try {
    echo "Starting migration...\n";

    // Step 1: Add new column
    $sql1 = "ALTER TABLE users ADD COLUMN selected_language_id INT UNSIGNED NULL AFTER role";
    if (mysqli_query($conn, $sql1)) {
        echo "✓ Added selected_language_id column\n";
    } else {
        throw new Exception("Failed to add column: " . mysqli_error($conn));
    }

    // Step 2: Add foreign key constraint
    $sql2 = "ALTER TABLE users ADD CONSTRAINT fk_user_language FOREIGN KEY (selected_language_id) REFERENCES languages(language_id) ON DELETE SET NULL";
    if (mysqli_query($conn, $sql2)) {
        echo "✓ Added foreign key constraint\n";
    } else {
        throw new Exception("Failed to add foreign key: " . mysqli_error($conn));
    }

    // Step 3: Populate the new column with language_id values
    $sql3 = "UPDATE users u
            INNER JOIN languages l ON LOWER(TRIM(u.selected_language)) = LOWER(TRIM(l.name))
            SET u.selected_language_id = l.language_id
            WHERE u.selected_language IS NOT NULL AND u.selected_language != ''";
    if (mysqli_query($conn, $sql3)) {
        echo "✓ Migrated existing language data\n";
    } else {
        throw new Exception("Failed to migrate data: " . mysqli_error($conn));
    }

    // Step 4: Drop the old column
    $sql4 = "ALTER TABLE users DROP COLUMN selected_language";
    if (mysqli_query($conn, $sql4)) {
        echo "✓ Dropped old selected_language column\n";
    } else {
        throw new Exception("Failed to drop column: " . mysqli_error($conn));
    }

    echo "Migration completed successfully!\n";
    echo "The selected_language column is now a foreign key to languages.language_id\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

mysqli_close($conn);
?>
