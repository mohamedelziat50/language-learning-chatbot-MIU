<?php
session_start();
header("Content-Type: application/json");

// Load environment variables
require_once __DIR__ . '/../../../config/load_env.php';

// Include Quiz model
require_once __DIR__ . '/../../models/Quiz.php';

// 1. Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => ["message" => "Invalid request method. Use POST."]]);
    exit;
}

// 2. Get and validate input parameters
$mcqCount = isset($_POST['mcqCount']) && $_POST['mcqCount'] !== '' ? intval($_POST['mcqCount']) : 5;
$shortCount = isset($_POST['shortCount']) && $_POST['shortCount'] !== '' ? intval($_POST['shortCount']) : 2;
$difficulty = isset($_POST['difficulty']) && $_POST['difficulty'] !== '' ? intval($_POST['difficulty']) : 2;
$language = isset($_POST['language']) && $_POST['language'] !== '' ? trim($_POST['language']) : "French";

// Log received parameters for debugging
error_log("generate_quiz POST received: " . json_encode(["mcqCount" => $mcqCount, "shortCount" => $shortCount, "difficulty" => $difficulty, "language" => $language]));

// 3. Create Quiz instance and generate quiz
// Note: We don't actually need a database connection for generateQuiz, but we instantiate for consistency
$db_server = getenv('DB_SERVER');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
if (!$conn) {
    echo json_encode(["error" => ["message" => "DB connection failed", "details" => mysqli_connect_error()]]);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$quiz = new Quiz($conn, $user_id);

$result = $quiz->generateQuiz($mcqCount, $shortCount, $difficulty, $language);

echo json_encode($result);

mysqli_close($conn);
?>
