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

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(["error" => ["message" => "Invalid JSON payload"]]);
    exit;
}

$language = isset($data['language']) ? mysqli_real_escape_string($conn, $data['language']) : null;
$difficulty = isset($data['difficulty']) ? intval($data['difficulty']) : null;
$mcq_count = isset($data['mcqCount']) ? intval($data['mcqCount']) : null;
$short_count = isset($data['shortCount']) ? intval($data['shortCount']) : null;
$score = isset($data['score']) ? intval($data['score']) : null;
$total = isset($data['total']) ? intval($data['total']) : null;
$percent = isset($data['percent']) ? floatval($data['percent']) : null;

// Ensure quizzes table exists (safe to run repeatedly)
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

$stmt = mysqli_prepare($conn, "INSERT INTO quizzes (user_id, language, difficulty, mcq_count, short_count, score, total_questions, percent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'isiiiiid', $user_id, $language, $difficulty, $mcq_count, $short_count, $score, $total, $percent);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true, "quiz_id" => mysqli_insert_id($conn)]);
} else {
    echo json_encode(["error" => ["message" => "Failed to save quiz", "details" => mysqli_error($conn)]]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
