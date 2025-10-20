<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /language-learning-chatbot-MIU/index.php");
    exit();
}

$fullName = $_SESSION['user_name'] ?? 'User';
$firstName = explode(' ', trim($fullName))[0];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/student/student.css">
</head>
<body>
    <?php include '../partials/sidebar.php'; ?>
    <div class="main-content">
        <div class="header">Welcome back, <?php echo htmlspecialchars($firstName); ?>!</div>
        <div class="card-container">
            <div class="card">
                <h3>Recent Conversations</h3>
                <p>Snippet of previous chats...</p>
            </div>
            <div class="card">
                <h3>Your Progress</h3>
                <div class="progress-bar">
                    <div class="progress"></div>
                </div>
            </div>
            <div class="card">
                <h3>Achievements / Badges</h3>
                <p>Show badges here...</p>
            </div>
            <div class="card">
                <h3>Quick Practice Topics</h3>
                <button class="button-primary">Topic 1</button>
                <button class="button-primary">Topic 2</button>
            </div>
        </div>
    </div>
</body>
</html>