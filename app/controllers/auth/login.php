<?php
session_start();
include(__DIR__ . '/../../../config/db_connect.php');
require_once(__DIR__ . '/../../models/User.php');
require_once(__DIR__ . '/../../services/UserValidator.php');
require_once(__DIR__ . '/../../services/ResponseHandler.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input format
    $validator = new UserValidator();
    $validation = $validator->validateLogin($email, $password);
    
    if (!$validation['valid']) {
        ResponseHandler::alertAndBack(implode(', ', $validation['errors']));
    }

    // Create user instance and verify credentials
    $userModel = new User($conn);
    $user = $userModel->verifyLogin($email, $password);

    if ($user) {
        // Successful login - set session data
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['created_at'] = $user['created_at'];
        $_SESSION['status'] = $user['status'];

        // Redirect based on role
        $redirectUrl = ($user['role'] === 'admin') 
            ? '/language-learning-chatbot-MIU/app/views/admin/admin-dashboard.php'
            : '/language-learning-chatbot-MIU/app/views/student/dashboard.php';
            
        ResponseHandler::redirect($redirectUrl);
    }

    ResponseHandler::alertAndBack('Invalid email or password!');
}
?>
