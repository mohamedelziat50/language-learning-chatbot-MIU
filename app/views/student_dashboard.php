<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="../../public/css/student_dashboard.css"> <!-- Replace with actual CSS file -->
</head>
<body>
    <div class="sidebar">
        <div class="logo">Language Learning</div>
        <nav id="sidebar-nav">
            <div class="top-links">
                <a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="#"><i class="fas fa-robot"></i> Chatbot</a>
                <a href="#"><i class="fas fa-book"></i> Topics</a>
                <a href="#" target="_blank"><i class="fas fa-question-circle"></i> Quiz</a>
                <a href="#"><i class="fas fa-comments"></i> Discussion</a>
            </div>
            <div class="bottom-links">
                <a href="#"><i class="fas fa-user"></i> Profile</a>
                <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>
    </div>
    <div class="main-content">
        <div class="header">Welcome back, Mohamed!</div>
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