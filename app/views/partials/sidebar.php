<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="../../../public/css/sidebar.css">
<div class="sidebar">
    <div class="logo">Language Learning</div>
    <nav id="sidebar-nav">
        <div class="top-links">
            <a href="student_dashboard.php"<?php if ($currentPage == 'student_dashboard.php') echo ' class="active"'; ?>><i class="fas fa-home"></i> Dashboard</a>
            <a href="student_docs.php"<?php if ($currentPage == 'student_docs.php') echo ' class="active"'; ?>><i class="fas fa-file-alt"></i> Docs</a>
            <a href="#"<?php if ($currentPage == 'chatbot.php') echo ' class="active"'; ?>><i class="fas fa-robot"></i> Chatbot</a>
            <a href="#"<?php if ($currentPage == 'topics.php') echo ' class="active"'; ?>><i class="fas fa-book"></i> Topics</a>
            <a href="#"<?php if ($currentPage == 'student_quiz.php') echo ' class="active"'; ?>><i class="fas fa-question-circle"></i> Quiz</a>
            <a href="#"<?php if ($currentPage == 'discussion.php') echo ' class="active"'; ?>><i class="fas fa-comments"></i> Discussion</a>
        </div>
        <div class="bottom-links">
            <a href="#"<?php if ($currentPage == 'profile.php') echo ' class="active"'; ?>><i class="fas fa-user"></i> Profile</a>
            <a href="#"<?php if ($currentPage == 'logout.php') echo ' class="active"'; ?>><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>
</div>