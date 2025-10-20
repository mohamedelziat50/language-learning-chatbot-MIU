document.addEventListener("DOMContentLoaded", () => {
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
      loadingSpinner.classList.add("hidden");
      quizOutput.classList.remove("hidden");
      quizOutput.scrollIntoView({ behavior: "smooth" });

      // Start timer (10 minutes = 600 seconds)
      startTimer(600);
    }, 2000);
  });

  // Timer function
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

  // Submit quiz function
  function submitQuiz() {
    alert("Your quiz has been submitted successfully!");
    location.reload(); // reset to start page for demo
  }

  const submitButton = document.getElementById("submitQuiz");
  submitButton.addEventListener("click", submitQuiz);

  // Handle answer selection visual
  document.querySelectorAll(".question li").forEach(option => {
    option.addEventListener("click", () => {
      const parent = option.closest("ul");
      parent.querySelectorAll("li").forEach(li => li.classList.remove("selected"));
      option.classList.add("selected");
      option.querySelector("input").checked = true;
    });
  });
});
