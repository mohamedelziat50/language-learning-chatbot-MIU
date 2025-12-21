<?php
include(__DIR__ . '/../../../config/db_connect.php');
require_once(__DIR__ . '/../../models/User.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Create user instance
    $userModel = new User($conn);
    
    // Check if email already exists
    if ($userModel->emailExists($email)) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit;
    }

    // Set user properties
    $userModel->name = $name;
    $userModel->email = $email;
    $userModel->password = $password;
    $userModel->role = 'student'; // Default role
    $userModel->status = 'active'; // Default status

    // Create user
    $result = $userModel->create();

    if ($result['status'] === 'success') {
        echo "<script>alert('Sign-up successful! You can now log in.'); window.history.back();</script>";
    } else {
        echo "<script>alert('Error during sign-up: " . $result['message'] . "'); window.history.back();</script>";
    }

    mysqli_close($conn);
}
?>
