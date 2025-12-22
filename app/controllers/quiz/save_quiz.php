<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../config/load_env.php';
require_once __DIR__ . '/../../models/Quiz.php';
require_once __DIR__ . '/../../services/BadgeService.php';

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

// Override language with user's selected language if available
$derivedLanguage = null;
$sql = "SELECT 
        u.selected_language,
        u.selected_language_id,
        COALESCE(l_id.name, l_code.name, u.selected_language) AS resolved_language
    FROM users u
    LEFT JOIN languages l_id ON l_id.language_id = u.selected_language_id
    LEFT JOIN languages l_code ON l_code.code = u.selected_language
    WHERE u.user_id = ?
    LIMIT 1";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && ($row = mysqli_fetch_assoc($res))) {
        if (!empty($row['resolved_language'])) {
            $derivedLanguage = $row['resolved_language'];
        }
    }
    mysqli_stmt_close($stmt);
}
if (!empty($derivedLanguage)) {
    $language = $derivedLanguage;
}

// Evaluate badges BEFORE saving (to compute newly unlocked after save)
$badgeService = new BadgeService($conn, $user_id);
$beforeBadges = $badgeService->evaluateCurrent();

$result = $quiz->saveQuiz($language ?? '', $difficulty ?? 0, $mcq_count ?? 0, $short_count ?? 0, $score ?? 0, $total ?? 0, $percent ?? 0.0);

if (isset($result['success']) && $result['success'] === true) {
    $afterBadges = $badgeService->evaluateCurrent();
    $beforeKeys = array_map(function($b){ return $b['key']; }, $beforeBadges);
    $afterKeys = array_map(function($b){ return $b['key']; }, $afterBadges);
    $newKeys = array_values(array_diff($afterKeys, $beforeKeys));
    $newlyUnlocked = array_values(array_filter($afterBadges, function($b) use ($newKeys){ return in_array($b['key'], $newKeys, true); }));
    $result['newlyUnlocked'] = $newlyUnlocked;
}

echo json_encode($result);

mysqli_close($conn);
?>
