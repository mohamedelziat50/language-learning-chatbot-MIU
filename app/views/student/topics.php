<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topics</title>
    <link rel="stylesheet" href="../../../public/css/student/student.css">
</head>
<body>
    <?php include '../partials/sidebar.php'; ?>
    <div class="main-content">
        <div class="header">Topics Center</div>
        <div class="card-container">
            <div class="card">
                <h3>Explore Topics</h3>
                <p>Browse and select a topic to start learning or practicing:</p>
                <button class="button-primary">Grammar</button>
                <button class="button-primary">Vocabulary</button>
                <button class="button-primary">Listening</button>
            </div>
            <div class="card">
                <h3>Your Topic Progress</h3>
                <div class="progress-bar">
                    <div class="progress" style="width: 50%;"></div>
                </div>
                <p>You've explored 5 out of 10 topics.</p>
            </div>
            <div class="card">
                <h3>Topic Achievements</h3>
                <p>Earn badges for mastering topics and consistency!</p>
                <span class="badge">Grammar Guru</span>
                <span class="badge">Vocabulary Master</span>
            </div>
            <div class="card">
                <h3>Topic Help & Tips</h3>
                <ul>
                    <li>Focus on one topic at a time for deeper understanding.</li>
                    <li>Review materials and practice exercises regularly.</li>
                    <li>Track your progress and revisit challenging topics.</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>