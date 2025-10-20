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

        <!-- Language -->
        <div class="setting language">
          <h3>Language</h3>
          <select id="languageSelect">
            <option value="english">English</option>
            <option value="french">French</option>
            <option value="spanish">Spanish</option>
            <option value="arabic">Arabic</option>
          </select>
        </div>
      </div>

      <button id="createBtn" class="create-btn">Create Test</button>

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

  <script>
  // Duration control
  let time = 10;
  const timeDisplay = document.getElementById("timeDisplay");
  document.getElementById("increaseTime").addEventListener("click", () => {
    time += 5;
    timeDisplay.textContent = `${time} min`;
  });
  document.getElementById("decreaseTime").addEventListener("click", () => {
    if (time > 5) {
      time -= 5;
      timeDisplay.textContent = `${time} min`;
    }
  });

  // Create test logic
  const createBtn = document.getElementById("createBtn");
  const loadingSpinner = document.getElementById("loadingSpinner");
  const quizOutput = document.getElementById("quizOutput");
  const quizSettings = document.querySelector(".quiz-settings");

  createBtn.addEventListener("click", () => {
    // Hide settings, show loader
    quizSettings.classList.add("hidden");
    quizOutput.classList.add("hidden");
    loadingSpinner.classList.remove("hidden");

    // Simulate loading delay (2 seconds)
    setTimeout(() => {
      loadingSpinner.classList.add("hidden"); // Hide loader
      quizOutput.classList.remove("hidden");  // Show quiz
      quizOutput.scrollIntoView({ behavior: "smooth" });
    }, 2000);
  });

  function startTimer(duration) {
    let timer = duration, minutes, seconds;
    const display = document.getElementById("timerDisplay");
    const interval = setInterval(() => {
      minutes = parseInt(timer / 60, 10);
      seconds = parseInt(timer % 60, 10);

      minutes = minutes < 10 ? "0" + minutes : minutes;
      seconds = seconds < 10 ? "0" + seconds : seconds;

      display.textContent = `${minutes}:${seconds}`;

      if (--timer < 0) {
        clearInterval(interval);
        alert("Time’s up! Submitting your quiz...");
        submitQuiz();
      }
    }, 1000);
  }

  function submitQuiz() {
    alert("Your quiz has been submitted successfully!");
    location.reload(); // reset to start page for demo
  }

  const submitButton = document.getElementById("submitQuiz");
  submitButton.addEventListener("click", submitQuiz);

  // Update inside your existing setTimeout (after quizOutput shows)
  createBtn.addEventListener("click", () => {
    quizSettings.classList.add("hidden");
    quizOutput.classList.add("hidden");
    loadingSpinner.classList.remove("hidden");

    setTimeout(() => {
      loadingSpinner.classList.add("hidden");
      quizOutput.classList.remove("hidden");
      quizOutput.scrollIntoView({ behavior: "smooth" });

      // Start timer (10 minutes = 600 seconds)
      startTimer(600);
    }, 2000);
  });


  // Handle answer selection visual
document.querySelectorAll(".question li").forEach(option => {
  option.addEventListener("click", () => {
    const parent = option.closest("ul");
    parent.querySelectorAll("li").forEach(li => li.classList.remove("selected"));
    option.classList.add("selected");
    option.querySelector("input").checked = true;
  });
});

</script>

</body>
</html>
