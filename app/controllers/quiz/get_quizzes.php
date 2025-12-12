<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../config/load_env.php';
require_once __DIR__ . '/../../models/Quiz.php';

// Get database connection
$db_server = getenv('DB_SERVER');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
if (!$conn) {
    echo json_encode(["error" => ["message" => "DB connection failed", "details" => mysqli_connect_error()]]);
    exit;
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => ["message" => "Not authenticated"]]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Create Quiz instance and fetch quizzes
$quiz = new Quiz($conn, $user_id);
$quizzes = $quiz->getQuizzes(50);

echo json_encode(["success" => true, "quizzes" => $quizzes]);

mysqli_close($conn);
?>
