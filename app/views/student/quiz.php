<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Quiz</title>
  <link rel="stylesheet" href="../../../public/css/student/student.css">
  <link rel="stylesheet" href="../../../public/css/student/quiz.css">
</head>
<body>
<?php include '../partials/sidebar.php'; ?>

  <div class="main-content">
  <section class="quiz-settings">
    <div class="settings-container">

      <h1>Create Your Quiz</h1>

      <div class="settings-grid">
        <!-- Time -->
        <div class="setting time">
          <h3>Test Duration</h3>
          <div class="duration-control">
            <button id="decreaseTime">−</button>
            <span id="timeDisplay">10 min</span>
            <button id="increaseTime">+</button>
          </div>
          <label><input type="checkbox" id="enforceTime" checked> Enforce time limit</label>
        </div>

        <!-- Questions -->
        <div class="setting questions">
          <h3>Questions</h3>
          <div class="question-types">
            <div>
              <label>Multiple Choice</label>
              <input type="number" id="mcqCount" value="10" min="0" max="50">
            </div>
            <div>
              <label>Short Answer</label>
              <input type="number" id="shortCount" value="2" min="0" max="20">
            </div>
          </div>
        </div>

        <!-- Difficulty -->
        <div class="setting difficulty">
          <h3>Difficulty</h3>
          <input type="range" id="difficultySlider" min="1" max="5" value="2">
          <div class="difficulty-labels">
            <span>Easy</span>
            <span>Hard</span>
          </div>
        </div>

      </div>

      <button id="createBtn" class="create-btn">Create Test</button>

      <!-- Previous Quiz Results -->
    <section class="previous-quizzes">
      <h2>Your Previous Quizzes</h2>

      <div class="quiz-history">
        <div class="quiz-record">
          <span class="quiz-lang">French</span>
          <span class="quiz-diff">Difficulty: 3/5</span>
          <span class="quiz-score">Score: 8/10</span>
          <span class="quiz-date">Taken: Oct 18, 2025</span>
        </div>

        <div class="quiz-record">
          <span class="quiz-lang">Spanish</span>
          <span class="quiz-diff">Difficulty: 2/5</span>
          <span class="quiz-score">Score: 9/10</span>
          <span class="quiz-date">Taken: Oct 10, 2025</span>
        </div>
        
      </div>
    </section>


        </div>
      </section>

  
  <!-- Quiz Output Section -->
  <section id="quizOutput" class="hidden">
  <div class="quiz-container">
    <div class="quiz-header">
      <h2>Your Quiz</h2>
      <div class="timer">
        ⏱ <span id="timerDisplay">10:00</span>
      </div>
    </div>
    <div id="quizQuestions"></div> <!-- questions go here -->
    <div class="submit-container">
      <button id="submitQuiz" class="submit-btn">Submit Quiz</button>
    </div>
  </div>
</section>

<div id="reviewSection" style="display:none;">
    <h2>Review Your Answers</h2>
    <div id="reviewContainer"></div>

    <button id="backToSettings">Back to Settings</button>
</div>

  <!-- Loading Spinner -->
  <div id="loadingSpinner" class="hidden">
    <div class="spinner"></div>
  </div>

  <script src="../../../public/js/student/quiz.js"></script>
</div>
</body>
</html>
