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

    // Debug: log form values
    for (const pair of formData.entries()) {
      console.log(pair[0] + ':', pair[1]);
    }

    // Show loading spinner
    document.getElementById("loadingSpinner").classList.remove("hidden");

    fetch("/language-learning-chatbot-MIU/app/controllers/quiz/generate_quiz.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(text => {
      document.getElementById("loadingSpinner").classList.add("hidden");

      // Log raw response for debugging
      console.log('Raw server response:', text);

      let parsed;
      try {
        parsed = JSON.parse(text);
      } catch (e) {
        console.error('Failed to parse server JSON response:', e, text);
        alert('Server returned invalid JSON. Check console for details.');
        return;
      }

      // Handle server-side error payloads
      if (parsed.error) {
        console.error('Server error:', parsed.error);
        alert('Failed to generate quiz: ' + (parsed.error.message || JSON.stringify(parsed.error)));
        return;
      }

      // Wrapper format: { success:true, groq: { choices: [...] }, sent_prompt: "..." }
      const aiResp = parsed.groq || parsed;

      if (!aiResp.choices || !aiResp.choices[0] || !aiResp.choices[0].message) {
        console.error('Unexpected AI payload:', aiResp);
        alert('Unexpected AI response. See console for details.');
        return;
      }

      const quizContent = aiResp.choices[0].message.content;
      let quizJson;
      try {
        quizJson = JSON.parse(quizContent);
      } catch (e) {
        console.error('Failed to parse quiz JSON from AI content:', e, quizContent);
        // As fallback, if the AI already returned parsed object in wrapper, try other locations
        if (parsed.groq && parsed.groq.data) {
          quizJson = parsed.groq.data;
        } else {
          alert('Failed to parse quiz JSON from AI. Check console.');
          return;
        }
      }

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
      console.error('Fetch error:', err);
      document.getElementById("loadingSpinner").classList.add("hidden");
      alert("Failed to generate quiz. Check console for errors.");
    });
  });

  // Load previous quizzes on page load
  loadPreviousQuizzes();

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

  // Collect answers and compute score
  const questions = document.querySelectorAll(".question");
  let correctCount = 0;
  const totalQuestions = questions.length;

  questions.forEach((q, index) => {
    const radio = q.querySelector("input[type=radio]:checked");
    const textarea = q.querySelector("textarea");
    const correct = q.getAttribute("data-correct");

    const reviewItem = document.createElement("div");
    reviewItem.classList.add("review-item");

    reviewItem.innerHTML = `
      <h3>Question ${index + 1}</h3>
      <p>${q.querySelector("p").innerText}</p>
    `;

    // Multiple choice handling
    if (radio) {
      if (radio.value === correct) {
        correctCount++;
        reviewItem.innerHTML += `<p class="correct">Your Answer: ${radio.value}</p>`;
      } else {
        reviewItem.innerHTML += `
          <p class="wrong">Your Answer: ${radio.value}</p>
          <p class="correct-answer-highlight">Correct Answer: ${correct}</p>
        `;
      }
    } else if (textarea) {
      // Short answer: compare trimmed, case-insensitive
      const userAns = textarea.value ? textarea.value.trim() : "";
      if (userAns.length > 0 && correct) {
        const normalizedUser = userAns.toLowerCase();
        const normalizedCorrect = String(correct).toLowerCase();
        if (normalizedUser === normalizedCorrect) {
          correctCount++;
          reviewItem.innerHTML += `<p class="correct">Your Answer: ${userAns}</p>`;
        } else {
          reviewItem.innerHTML += `
            <p class="wrong">Your Answer: ${userAns}</p>
            <p class="correct-answer-highlight">Correct Answer: ${correct}</p>
          `;
        }
      } else {
        // unanswered
        reviewItem.innerHTML += `
          <p class="wrong">No answer provided</p>
          <p class="correct-answer-highlight">Correct Answer: ${correct}</p>
        `;
      }
    } else {
      // fallback for unknown question types
      reviewItem.innerHTML += `<p class="wrong">No answer detected</p><p class="correct-answer-highlight">Correct Answer: ${correct}</p>`;
    }

    reviewContainer.appendChild(reviewItem);
  });

  // Insert score summary at top
  const percent = totalQuestions > 0 ? Math.round((correctCount / totalQuestions) * 100) : 0;
  const scoreBanner = document.createElement('div');
  scoreBanner.classList.add('score-banner');
  scoreBanner.innerHTML = `<h3>Score: ${correctCount} / ${totalQuestions} (${percent}%)</h3>`;
  reviewContainer.prepend(scoreBanner);

    // Save quiz result to server (send JSON)
    const payload = {
      language: document.querySelector('.quiz-lang') ? document.querySelector('.quiz-lang').innerText : 'Unknown',
      difficulty: Number(document.getElementById('difficultySlider') ? document.getElementById('difficultySlider').value : 0),
      mcqCount: Number(document.getElementById('mcqCount') ? document.getElementById('mcqCount').value : 0),
      shortCount: Number(document.getElementById('shortCount') ? document.getElementById('shortCount').value : 0),
      score: correctCount,
      total: totalQuestions,
      percent: percent
    };

    fetch('/language-learning-chatbot-MIU/app/controllers/quiz/save_quiz.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (data && data.success) {
        console.log('Quiz saved, id=', data.quiz_id);
        // Refresh previous quizzes list
        loadPreviousQuizzes();
      } else {
        console.warn('Failed to save quiz', data);
      }
    })
    .catch(err => console.error('Save quiz error', err));

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

// Fetch and render previous quizzes
function loadPreviousQuizzes() {
  fetch('/language-learning-chatbot-MIU/app/controllers/quiz/get_quizzes.php', {
    method: 'GET',
    credentials: 'same-origin'
  })
  .then(res => res.json())
  .then(data => {
    if (!data || !data.success) {
      console.warn('Could not load previous quizzes', data);
      return;
    }

    const container = document.querySelector('.quiz-history');
    if (!container) return;

    container.innerHTML = '';

    data.quizzes.forEach(q => {
      const rec = document.createElement('div');
      rec.classList.add('quiz-record');
      const date = new Date(q.created_at).toLocaleString();
      rec.innerHTML = `
        <span class="quiz-lang">${q.language || 'Unknown'}</span>
        <span class="quiz-diff">Difficulty: ${q.difficulty || '-'} /5</span>
        <span class="quiz-score">Score: ${q.score || 0}/${q.total_questions || 0} (${q.percent ? Math.round(q.percent) : 0}%)</span>
        <span class="quiz-date">Taken: ${date}</span>
      `;
      container.appendChild(rec);
    });
  })
  .catch(err => console.error('Load quizzes error', err));
}

