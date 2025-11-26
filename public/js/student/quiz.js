document.addEventListener("DOMContentLoaded", () => {
  // Duration control
  let time = 10; // default 10 minutes
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

  // Create Quiz button
  document.getElementById("createBtn").addEventListener("click", function () {
    const mcq = document.getElementById("mcqCount").value;
    const shortQ = document.getElementById("shortCount").value;
    const difficulty = document.getElementById("difficultySlider").value;

    // TEMP: demo language
    const language = "French"; 

    const formData = new FormData();
    formData.append("mcqCount", mcq);
    formData.append("shortCount", shortQ);
    formData.append("difficulty", difficulty);
    formData.append("language", language);

    // Show loading spinner
    document.getElementById("loadingSpinner").classList.remove("hidden");

    fetch("/language-learning-chatbot-MIU/app/controllers/generate_quiz.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("loadingSpinner").classList.add("hidden");

        const quiz = data.choices[0].message.content;
        const quizJson = JSON.parse(quiz);

        // Display questions dynamically
        displayQuiz(quizJson);

        // Hide settings and show quiz
        document.querySelector(".quiz-settings").classList.add("hidden");
        const quizOutput = document.getElementById("quizOutput");
        quizOutput.classList.remove("hidden");
        quizOutput.scrollIntoView({ behavior: "smooth" });

        // Start timer (convert minutes to seconds)
        startTimer(time * 60);
    })
    .catch(err => {
        console.error(err);
        document.getElementById("loadingSpinner").classList.add("hidden");
        alert("Failed to generate quiz. Check console for errors.");
    });
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
      }
    }, 1000);
  }

  

  const submitButton = document.getElementById("submitQuiz");
  submitButton.addEventListener("click", submitQuiz);
});

// Display quiz dynamically
function displayQuiz(quizData) {
  const quizQuestions = document.getElementById("quizQuestions");
  quizQuestions.innerHTML = ""; // clear old questions

  // MCQs
  quizData.mcq.forEach((q, idx) => {
    const mcqHTML = `
  <div class="question" data-correct="${q.answer}">
    <p><strong>${idx + 1}.</strong> ${q.question}</p>
    <ul>
      ${q.options.map(o => `<li><input type="radio" name="q${idx}" value="${o}"> ${o}</li>`).join("")}
    </ul>
  </div>`;

    quizQuestions.insertAdjacentHTML("beforeend", mcqHTML);
  });

  // Short answer
  quizData.short.forEach((q, idx) => {
    const shortHTML = `
  <div class="question" data-correct="${q.answer}">
    <p><strong>${quizData.mcq.length + idx + 1}.</strong> ${q.question}</p>
    <textarea rows="4"></textarea>
  </div>`;

    quizQuestions.insertAdjacentHTML("beforeend", shortHTML);
  });

  // Handle answer selection visual after questions are inserted
  quizQuestions.querySelectorAll("li").forEach(option => {
    option.addEventListener("click", () => {
      const parent = option.closest("ul");
      parent.querySelectorAll("li").forEach(li => li.classList.remove("selected"));
      option.classList.add("selected");
      option.querySelector("input").checked = true;
    });
  });
}


document.getElementById("submitQuiz").addEventListener("click", function () {
    const quizSection = document.getElementById("quizOutput");
    const settingsSection = document.querySelector(".quiz-settings");
    const reviewSection = document.getElementById("reviewSection");
    const reviewContainer = document.getElementById("reviewContainer");

    reviewContainer.innerHTML = ""; // clear previous reviews

    // Collect answers
    const questions = document.querySelectorAll(".question");
    questions.forEach((q, index) => {
        const selected = q.querySelector("input[type=radio]:checked");
        const correct = q.getAttribute("data-correct");

        const reviewItem = document.createElement("div");
        reviewItem.classList.add("review-item");

        reviewItem.innerHTML = `
            <h3>Question ${index + 1}</h3>
            <p>${q.querySelector("p").innerText}</p>

        `;

        // If user selected something
        if (selected) {
            if (selected.value === correct) {
                reviewItem.innerHTML += `
                    <p class="correct">Your Answer: ${selected.value}</p>
                `;
            } else {
                reviewItem.innerHTML += `
                    <p class="wrong">Your Answer: ${selected.value}</p>
                    <p class="correct-answer-highlight">Correct Answer: ${correct}</p>
                `;
            }
        } else {
            // unanswered
            reviewItem.innerHTML += `
                <p class="wrong">No answer selected</p>
                <p class="correct-answer-highlight">Correct Answer: ${correct}</p>
            `;
        }

        reviewContainer.appendChild(reviewItem);
    });

    // Show review mode
    quizSection.style.display = "none";
    settingsSection.style.display = "none";
    reviewSection.style.display = "block";
  
    // Scroll to top
    reviewSection.scrollIntoView({ behavior: "smooth", block: "start" });
});

// Back to settings
document.getElementById("backToSettings").addEventListener("click", function () {
    // Hide review section
    document.getElementById("reviewSection").style.display = "none";

    // Show settings
    const settingsSection = document.querySelector(".quiz-settings");
    settingsSection.classList.remove("hidden");
    settingsSection.style.display = "block";

    // Hide quiz output
    const quizSection = document.getElementById("quizOutput");
    quizSection.classList.add("hidden");
});

