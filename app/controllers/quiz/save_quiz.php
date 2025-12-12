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

// Get JSON payload
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(["error" => ["message" => "Invalid JSON payload"]]);
    exit;
}

// Extract quiz data
$language = isset($data['language']) ? $data['language'] : null;
$difficulty = isset($data['difficulty']) ? intval($data['difficulty']) : null;
$mcq_count = isset($data['mcqCount']) ? intval($data['mcqCount']) : null;
$short_count = isset($data['shortCount']) ? intval($data['shortCount']) : null;
$score = isset($data['score']) ? intval($data['score']) : null;
$total = isset($data['total']) ? intval($data['total']) : null;
$percent = isset($data['percent']) ? floatval($data['percent']) : null;

// Create Quiz instance and save
$quiz = new Quiz($conn, $user_id);
$result = $quiz->saveQuiz($language, $difficulty, $mcq_count, $short_count, $score, $total, $percent);

echo json_encode($result);

mysqli_close($conn);
?>
