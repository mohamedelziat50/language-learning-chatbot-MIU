<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Quiz</title>
  <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/student/student.css">
  <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/student/quiz.css">
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
    

    <div class="question">
    <p><strong>1.</strong> Which of the following is the correct definite article for the French word <em>“école”</em> (school)?</p>
    <ul>
      <li><input type="radio" name="q1"> Le école</li>
      <li><input type="radio" name="q1"> La école</li>
      <li><input type="radio" name="q1"> L’école</li>
    </ul>
  </div>

  <div class="question">
    <p><strong>2.</strong> Write a short sentence in French using the verb <em>“avoir”</em> (to have) in the present tense.</p>
    <textarea rows="4" placeholder="Écrivez votre phrase ici..."></textarea>
  </div>

  <div class="submit-container">
    <button id="submitQuiz" class="submit-btn">Submit Quiz</button>
  </div>
</section>

  <!-- Loading Spinner -->
  <div id="loadingSpinner" class="hidden">
    <div class="spinner"></div>
  </div>

  <script src="/language-learning-chatbot-MIU/public/js/student/quiz.js"></script>
</div>
</body>
</html>
