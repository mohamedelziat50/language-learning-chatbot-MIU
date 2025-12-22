<?php
/**
 * Admin API Endpoints
 * Routes for analytics and dashboard data
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/db_connect.php';
require_once __DIR__ . '/AdminAnalyticsController.php';

// Get the request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Parse the URI
$basePath = '/language-learning-chatbot-MIU/app/controllers/admin/api.php';
$route = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));

try {
    $controller = new AdminAnalyticsController($conn);
    
    // Route handling
    switch ($route) {
        case '/overview':
        case '/stats/overview':
            if ($method === 'GET') {
                $controller->getOverviewStats();
            }
            break;
            
        case '/activity':
        case '/stats/activity':
            if ($method === 'GET') {
                $controller->getUserActivity();
            }
            break;
            
        case '/queries':
        case '/stats/queries':
            if ($method === 'GET') {
                $controller->getCommonQueries();
            }
            break;
            
        case '/recent':
        case '/stats/recent':
            if ($method === 'GET') {
                $controller->getRecentActivity();
            }
            break;
            
        case '/quiz':
        case '/stats/quiz':
            if ($method === 'GET') {
                $controller->getQuizAnalytics();
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Endpoint not found'
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
