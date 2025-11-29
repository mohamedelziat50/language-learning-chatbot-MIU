<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/load_env.php';

$db_server = getenv('DB_SERVER');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
if (!$conn) {
    echo json_encode(["error" => ["message" => "DB connection failed", "details" => mysqli_connect_error()]]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => ["message" => "Not authenticated"]]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Ensure quizzes table exists
$createSQL = "CREATE TABLE IF NOT EXISTS quizzes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    language VARCHAR(100) DEFAULT NULL,
    difficulty TINYINT DEFAULT NULL,
    mcq_count INT DEFAULT NULL,
    short_count INT DEFAULT NULL,
    score INT DEFAULT NULL,
    total_questions INT DEFAULT NULL,
    percent FLOAT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

mysqli_query($conn, $createSQL);

$sql = "SELECT id, language, difficulty, mcq_count, short_count, score, total_questions, percent, created_at FROM quizzes WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$rows = [];
while ($row = mysqli_fetch_assoc($res)) {
    $rows[] = $row;
}

echo json_encode(["success" => true, "quizzes" => $rows]);

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
