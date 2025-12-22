<?php
header('Content-Type: application/json');
require_once __DIR__ . '/manage_users.php';

try {
    $users = UserController::getUsersQuizAverages();
    $summary = UserController::getGlobalQuizPerformanceSummary();

    echo json_encode(['success' => true, 'users' => $users, 'summary' => $summary]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
