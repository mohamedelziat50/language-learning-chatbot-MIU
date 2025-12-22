<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/db_connect.php';
require_once __DIR__ . '/AdminAnalyticsController.php';

try {
    $controller = new AdminAnalyticsController($conn);
    $controller->getQuizAnalytics();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

