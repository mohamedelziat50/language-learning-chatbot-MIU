<?php
/**
 * Student Profile Controller
 * Handles profile updates for students
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../config/db_connect.php';
require_once __DIR__ . '/../../models/User.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$userId = intval($_SESSION['user_id']);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Get POST data
    $data = $_POST;
    
    // Validate required fields
    if (empty($data['username']) || empty($data['email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Username and email are required']);
        exit();
    }
    
    // Verify user_id matches session (security check)
    if (isset($data['user_id']) && intval($data['user_id']) !== $userId) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit();
    }
    
    // Check if email is already taken by another user
    $userModel = new User($conn);
    $existingUser = $userModel->getByEmail($data['email']);
    
    if ($existingUser && $existingUser['user_id'] != $userId) {
        echo json_encode(['status' => 'error', 'message' => 'Email already in use by another account']);
        exit();
    }
    
    // Update user profile (store username in name field)
    $userModel->user_id = $userId;
    $userModel->name = trim($data['username']);
    $userModel->email = trim($data['email']);
    
    // Get current user data to preserve role and status
    $currentUser = $userModel->getById($userId);
    if ($currentUser) {
        $userModel->role = $currentUser['role'];
        $userModel->status = $currentUser['status'];
    }
    
    $result = $userModel->update();
    
    if ($result['status'] === 'success') {
        // Update session with new data
        $_SESSION['user_name'] = $userModel->name;
        $_SESSION['user_email'] = $userModel->email;
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'username' => $userModel->name,
                'email' => $userModel->email
            ]
        ]);
    } else {
        echo json_encode($result);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
