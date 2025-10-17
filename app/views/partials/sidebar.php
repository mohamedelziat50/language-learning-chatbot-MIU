<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="../../../public/css/student/sidebar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome Icons -->
<div class="sidebar">
    <div class="logo">Language Learning</div>
    <nav id="sidebar-nav">
        <div class="top-links">
            <a href="dashboard.php"<?php if ($currentPage == 'dashboard.php') echo ' class="active"'; ?>><i class="fas fa-home"></i> Dashboard</a>
            <a href="docs.php"<?php if ($currentPage == 'docs.php') echo ' class="active"'; ?>><i class="fas fa-file-alt"></i> Documents</a>
            <a href="quiz.php"<?php if ($currentPage == 'quiz.php') echo ' class="active"'; ?>><i class="fas fa-question-circle"></i> Quiz</a>
            <a href="topics.php"<?php if ($currentPage == 'topics.php') echo ' class="active"'; ?>><i class="fas fa-book"></i> Topics</a>
            <a href="#"<?php if ($currentPage == 'chatbot.php') echo ' class="active"'; ?>><i class="fas fa-robot"></i> Chatbot</a>
            <a href="#"<?php if ($currentPage == 'discussion.php') echo ' class="active"'; ?>><i class="fas fa-comments"></i> Discussion</a>
        </div>
        <div class="bottom-links">
            <a href="#"<?php if ($currentPage == 'profile.php') echo ' class="active"'; ?>><i class="fas fa-user"></i> Profile</a>
            <a href="/language-learning-chatbot-MIU/app/controllers/logout.php"><i class="fas fa-sign-out-alt"></i> Logout </a>
        </div>
    </nav>
</div>