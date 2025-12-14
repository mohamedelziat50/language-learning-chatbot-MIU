<?php
session_start();
include(__DIR__ . '/../../../config/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Use prepared statements to avoid SQL injection and fetch a single row
    $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            // Handle both schema variants (user_id vs id, name vs fullname)
            $userId = $user['user_id'] ?? $user['id'] ?? null;
            $userName = $user['name'] ?? $user['fullname'] ?? '';

            if ($userId === null) {
                echo "<script>alert('Login failed: user id missing.'); window.history.back();</script>";
                exit;
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $userName;
            $_SESSION['user_email'] = $user['email'] ?? '';
            $_SESSION['user_role'] = $user['role'] ?? 'student';
            $_SESSION['created_at'] = $user['created_at'] ?? null;
            $_SESSION['status'] = $user['status'] ?? null;

            if (($_SESSION['user_role']) === 'admin') {
                header('Location: /language-learning-chatbot-MIU/app/views/admin/admin-dashboard.php');
            } else {
                header('Location: /language-learning-chatbot-MIU/app/views/student/dashboard.php');
            }
            exit;
        }

        echo "<script>alert('Incorrect password!'); window.history.back();</script>";
        exit;
    }

    echo "<script>alert('Email not found!'); window.history.back();</script>";
    exit;
}
?>
