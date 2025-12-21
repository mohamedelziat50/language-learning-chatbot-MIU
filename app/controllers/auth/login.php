<?php
session_start();
include(__DIR__ . '/../../../config/db_connect.php');
require_once(__DIR__ . '/../../models/User.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Create user instance
    $userModel = new User($conn);
    
    // Verify login credentials
    $user = $userModel->verifyLogin($email, $password);

    if ($user) {
        // Successful login
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['created_at'] = $user['created_at'];
        $_SESSION['status'] = $user['status'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header('Location: /language-learning-chatbot-MIU/app/views/admin/admin-dashboard.php');
        } else {
            header('Location: /language-learning-chatbot-MIU/app/views/student/dashboard.php');
        }
        exit;
    }

    echo "<script>alert('Invalid email or password!'); window.history.back();</script>";
    exit;
}
?>
