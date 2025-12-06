<?php
// Suppress error display but log errors to file
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Load admin routes
require_once __DIR__ . '/routes/admin_routes.php';

// Parse request - try PATH_INFO first, then parse from REQUEST_URI
$request = $_SERVER['PATH_INFO'] ?? '';

// If PATH_INFO is empty, try to extract from REQUEST_URI
if (empty($request)) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $request = str_replace('/language-learning-chatbot-MIU/app/index.php', '', $uri);
}

// Clean up the request path
$request = str_replace('/language-learning-chatbot-MIU/app', '', $request);

// Log for debugging
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'not set'));
error_log("Parsed request: " . $request);

$method = $_SERVER['REQUEST_METHOD'];

// Execute router
if (registerUserRoutes($request, $method)) exit;

// No valid route found
http_response_code(404);
echo json_encode([
    "status" => "error",
    "message" => "Invalid route: " . $request
]);
