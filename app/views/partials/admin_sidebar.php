<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/admin/admin_sidebar.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
<div class="sidebar">
    <div class="logo">
        <span class="logo-text">Admin<span class="bot-text">Dashboard</span></span>
    </div>
    <nav id="sidebar-nav">
        <div class="top-links">
            <a href="/language-learning-chatbot-MIU/app/views/admin/admin-dashboard.php"<?php if ($currentPage == 'admin-dashboard.php') echo ' class="active"'; ?>><i class="fas fa-home"></i> Overview</a>
            <a href="/language-learning-chatbot-MIU/app/views/admin/manage_users.php"<?php if ($currentPage == 'manage_users.php') echo ' class="active"'; ?>><i class="fas fa-users"></i> Manage Users</a>
            <a href="#analytics"<?php if ($currentPage == 'analytics.php') echo ' class="active"'; ?>><i class="fas fa-chart-bar"></i> Analytics</a>
            <a href="#forum"<?php if ($currentPage == 'forum.php') echo ' class="active"'; ?>><i class="fas fa-comments"></i> Forum</a>
            <a href="#reports"<?php if ($currentPage == 'reports.php') echo ' class="active"'; ?>><i class="fas fa-file-alt"></i> Reports</a>
            <a href="#settings"<?php if ($currentPage == 'settings.php') echo ' class="active"'; ?>><i class="fas fa-cog"></i> Settings</a>
        </div>
        <div class="bottom-links">
            <a href="/language-learning-chatbot-MIU/index.php"><i class="fas fa-sign-out-alt"></i> Logout </a>
        </div>
    </nav>
</div>
