<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/student/sidebar.css"> <!-- Sidebar Styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome Icons -->
<div class="sidebar">
    <div class="logo">
        <svg class="logo-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
        </svg>
            <a href="/language-learning-chatbot-MIU/index.php" class="logo-text" style="text-decoration:none;color:inherit;">
                Lingua<span class="bot-text">Bot</span>
            </a>
    </div>
    <nav id="sidebar-nav">
        <div class="top-links">
            <a href="/language-learning-chatbot-MIU/app/views/student/dashboard.php"<?php if ($currentPage == 'dashboard.php') echo ' class="active"'; ?>><i class="fas fa-home"></i> Dashboard</a>
            <a href="/language-learning-chatbot-MIU/app/views/student/docs.php"<?php if ($currentPage == 'docs.php') echo ' class="active"'; ?>><i class="fas fa-file-alt"></i> Documents</a>
            <a href="/language-learning-chatbot-MIU/app/views/student/quiz.php"<?php if ($currentPage == 'quiz.php') echo ' class="active"'; ?>><i class="fas fa-question-circle"></i> Quiz</a>
            <a href="/language-learning-chatbot-MIU/app/views/student/Languages/language.php" <?php if ($currentPage == 'language.php') echo 'class="active"'; ?>><i class="fas fa-book"></i> Languages</a>
        
        </div>
        <div class="bottom-links">
            <a href="/language-learning-chatbot-MIU/app/views/student/profile.php"<?php if ($currentPage == 'profile.php') echo ' class="active"'; ?>><i class="fas fa-user"></i> Profile</a>
            <a href="/language-learning-chatbot-MIU/app/controllers/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout </a>
        </div>
    </nav>
</div>