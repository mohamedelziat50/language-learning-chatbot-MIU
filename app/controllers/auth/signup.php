<?php
include(__DIR__ . '/../../../config/db_connect.php');
require_once(__DIR__ . '/../../models/User.php');
require_once(__DIR__ . '/../../services/UserValidator.php');
require_once(__DIR__ . '/../../services/ResponseHandler.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate input
    $validator = new UserValidator();
    $validation = $validator->validateCreate([
        'name' => $name,
        'email' => $email,
        'password' => $password
    ]);
    
    if (!$validation['valid']) {
        ResponseHandler::alertAndBack(implode(', ', $validation['errors']));
    }

    // Create user instance
    $userModel = new User($conn);
    
    // Check if email already exists
    if ($userModel->emailExists($email)) {
        ResponseHandler::alertAndBack('Email already registered!');
    }

    // Set user properties
    $userModel->name = $name;
    $userModel->email = $email;
    $userModel->password = password_hash($password, PASSWORD_DEFAULT); // Hash password here
    $userModel->role = 'student';
    $userModel->status = 'active';

    // Create user
    $result = $userModel->create();

    if ($result['status'] === 'success') {
        ResponseHandler::alertAndBack('Sign-up successful! You can now log in.');
    }
    
    ResponseHandler::alertAndBack('Error during sign-up: ' . $result['message']);
}
?>
