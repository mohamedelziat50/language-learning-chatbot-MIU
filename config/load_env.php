<?php
/**
 * Load environment variables from .env file
 */

function loadEnv($path) {
    if (!file_exists($path)) {
        die("❌ .env file not found at: $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        die("❌ Failed to read .env file!");
    }

    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip empty lines and comments
        if (empty($line) || $line[0] === '#') {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') === false) {
            continue; // Skip invalid lines
        }

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Skip if key is empty
        if (empty($key)) {
            continue;
        }
        
        // Set as environment variable
        putenv("$key=$value");
    }
}

// Load .env file from root directory
loadEnv(__DIR__ . '/../.env');
?>

