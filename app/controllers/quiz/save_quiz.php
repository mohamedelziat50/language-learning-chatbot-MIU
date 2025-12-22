<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../config/db_connect.php';
require_once __DIR__ . '/../../models/Quiz.php';
require_once __DIR__ . '/../../services/BadgeService.php';

// Database connection is available via $conn from db_connect.php
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

// Evaluate badges BEFORE saving (to compute newly unlocked after save)
$badgeService = new BadgeService($conn, $user_id);
$beforeBadges = $badgeService->evaluateCurrent();

$result = $quiz->saveQuiz($language, $difficulty, $mcq_count, $short_count, $score, $total, $percent);

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
