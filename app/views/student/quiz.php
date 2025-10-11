<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="stylesheet" href="../../../public/css/student/student.css">
</head>
<body>
    <?php include '../partials/sidebar.php'; ?>
    <div class="main-content">
        <div class="header">Quiz Center</div>
        <div class="card-container">
            <div class="card">
                <h3>Take a Quiz</h3>
                <p>Test your language skills with interactive quizzes. Select a topic below to begin:</p>
                <button class="button-primary">Grammar Quiz</button>
                <button class="button-primary">Vocabulary Quiz</button>
                <button class="button-primary">Listening Quiz</button>
            </div>
            <div class="card">
                <h3>Your Quiz Progress</h3>
                <div class="progress-bar">
                    <div class="progress" style="width: 50%;"></div>
                </div>
                <p>You've completed 5 out of 10 quizzes.</p>
            </div>
            <div class="card">
                <h3>Quiz Achievements</h3>
                <p>Earn badges for high scores and consistency!</p>
                <span class="badge">Grammar Guru</span>
                <span class="badge">Vocabulary Master</span>
            </div>
            <div class="card">
                <h3>Quiz Help & Tips</h3>
                <ul>
                    <li>Read each question carefully.</li>
                    <li>Review your answers before submitting.</li>
                    <li>Practice regularly for best results.</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>