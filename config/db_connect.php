<?php
// Database connection
$host = 'localhost';
$dbname = 'chatbot_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Dummy data for testing purposes
$dummyData = [
    ['id' => 1, 'name' => 'John Doe', 'role' => 'student'],
    ['id' => 2, 'name' => 'Jane Smith', 'role' => 'tutor'],
    ['id' => 3, 'name' => 'Admin User', 'role' => 'admin']
];

// Print dummy data for verification
foreach ($dummyData as $data) {
    echo "ID: {$data['id']}, Name: {$data['name']}, Role: {$data['role']}\n";
}
