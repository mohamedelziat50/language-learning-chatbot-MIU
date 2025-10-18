class ProfessionalLessonManager {
    constructor(config) {
        this.config = config;
        this.userScore = 0;
        this.exerciseData = null;
        this.currentExercise = null;
        this.currentQuestions = [];
        this.currentQuestionIndex = 0;
        this.userAnswers = {};
        this.correctAnswers = {};
        this.init();
    }

    async init() {
        console.log('ProfessionalLessonManager initialized for', this.config.lessonType);
        await this.loadExerciseData();
        this.renderExercise();
        this.setupEventListeners();
    }

    async loadExerciseData() {
        try {
            if (this.config.lessonData && Object.keys(this.config.lessonData).length > 0) {
                this.exerciseData = this.config.lessonData;
                console.log('Using PHP-provided lesson data:', this.exerciseData);
                return;
            }

            const response = await fetch('../../../Lesson/lesson_data.json');
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const allData = await response.json();
            this.exerciseData = allData[this.config.language]?.[this.config.topic] || {};
            
        } catch (error) {
            console.error('Failed to load exercise data:', error);
            this.exerciseData = {};
        }
    }

    renderExercise() {
        const container = document.getElementById('exerciseContent');
        if (!container) return;

        const type = this.config.lessonType;
        let html = '';

        switch (type) {
            case 'vocabulary':
                html = this.createVocabularyExercise();
                break;
            case 'phrases':
                html = this.createPhrasesExercise();
                break;
            case 'grammar':
                html = this.createGrammarExercise();
                break;
            case 'conversation':
                html = this.createConversationExercise();
                break;
            case 'practice':
                html = this.createPracticeExercise();
                break;
            default:
                html = this.createFallbackExercise();
        }
        
        container.innerHTML = html;
        this.currentExercise = type;
    }

    createVocabularyExercise() {
        const vocabulary = this.exerciseData.vocabulary || [];
        
        if (vocabulary.length === 0) {
            return this.createNoDataMessage('vocabulary');
        }

        // Generate questions from vocabulary
        this.currentQuestions = this.generateVocabularyQuestions(vocabulary);
        this.currentQuestionIndex = 0;
        this.userAnswers = {};
        this.correctAnswers = {};

        return `
            <div class="exercise vocabulary-exercise">
                <h4><i class="fas fa-book"></i> Vocabulary Quiz</h4>
                <div class="quiz-container">
                    <div class="quiz-header">
                        <div class="progress">Question 1 of ${this.currentQuestions.length}</div>
                        <div class="score">Score: 0/${this.currentQuestions.length}</div>
                    </div>
                    <div id="questionContainer"></div>
                    <div class="quiz-controls">
                        <button class="nav-btn prev-btn" onclick="lessonManager.previousQuestion()" disabled>
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button class="nav-btn next-btn" onclick="lessonManager.nextQuestion()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button class="submit-btn" onclick="lessonManager.submitQuiz()" style="display: none;">
                            <i class="fas fa-paper-plane"></i> Submit Quiz
                        </button>
                    </div>
                </div>
                <div class="feedback" id="quizFeedback"></div>
            </div>
        `;
    }

    generateVocabularyQuestions(vocabulary) {
        const questions = [];
        
        // Multiple Choice Questions
        vocabulary.forEach((item, index) => {
            const word = item.word || item.french || 'Unknown';
            const correctAnswer = item.translation || item.english || 'Unknown';
            
            // Get wrong options from other vocabulary items
            const wrongOptions = vocabulary
                .filter((_, i) => i !== index)
                .map(other => other.translation || other.english)
                .filter(Boolean)
                .slice(0, 3);

            questions.push({
                type: 'multiple_choice',
                question: `What does "${word}" mean in English?`,
                options: this.shuffleArray([correctAnswer, ...wrongOptions]),
                correctAnswer: correctAnswer,
                explanation: `${word} means "${correctAnswer}" in English.`,
                userAnswer: null,
                isCorrect: false
            });
        });

        // Matching Questions
        if (vocabulary.length >= 4) {
            const matchingWords = vocabulary.slice(0, 4);
            questions.push({
                type: 'matching',
                question: 'Match the French words with their English translations:',
                pairs: matchingWords.map(item => ({
                    french: item.word || item.french,
                    english: item.translation || item.english
                })),
                userAnswer: {},
                isCorrect: false
            });
        }

        return this.shuffleArray(questions);
    }

    renderCurrentQuestion() {
        const container = document.getElementById('questionContainer');
        if (!container || !this.currentQuestions[this.currentQuestionIndex]) return;

        const question = this.currentQuestions[this.currentQuestionIndex];
        let html = '';

        switch (question.type) {
            case 'multiple_choice':
                html = this.renderMultipleChoiceQuestion(question);
                break;
            case 'matching':
                html = this.renderMatchingQuestion(question);
                break;
            case 'fill_blank':
                html = this.renderFillBlankQuestion(question);
                break;
        }

        container.innerHTML = html;
        this.updateQuizControls();
    }

    renderMultipleChoiceQuestion(question) {
        const userAnswer = this.userAnswers[this.currentQuestionIndex] || '';
        
        return `
            <div class="question multiple-choice-question">
                <div class="question-text">${question.question}</div>
                <div class="options">
                    ${question.options.map((option, index) => `
                        <div class="option">
                            <input type="radio" 
                                   id="opt${index}" 
                                   name="answer" 
                                   value="${option}"
                                   ${userAnswer === option ? 'checked' : ''}
                                   onchange="lessonManager.saveAnswer('${option}')">
                            <label for="opt${index}">${option}</label>
                        </div>
                    `).join('')}
                </div>
                ${this.showAnswerFeedback(question)}
            </div>
        `;
    }

    renderMatchingQuestion(question) {
        const shuffledEnglish = this.shuffleArray(question.pairs.map(p => p.english));
        const userAnswer = this.userAnswers[this.currentQuestionIndex] || {};
        
        return `
            <div class="question matching-question">
                <div class="question-text">${question.question}</div>
                <div class="matching-pairs">
                    ${question.pairs.map((pair, index) => `
                        <div class="matching-pair">
                            <div class="french-word">${pair.french}</div>
                            <select class="matching-select" 
                                    onchange="lessonManager.saveMatchingAnswer(${index}, this.value)">
                                <option value="">Select translation</option>
                                ${shuffledEnglish.map(eng => `
                                    <option value="${eng}" ${userAnswer[index] === eng ? 'selected' : ''}>
                                        ${eng}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                    `).join('')}
                </div>
                ${this.showAnswerFeedback(question)}
            </div>
        `;
    }

    renderFillBlankQuestion(question) {
        const userAnswer = this.userAnswers[this.currentQuestionIndex] || '';
        
        return `
            <div class="question fill-blank-question">
                <div class="question-text">${question.question}</div>
                <div class="blank-input-container">
                    <input type="text" 
                           class="blank-input" 
                           value="${userAnswer}"
                           placeholder="Type your answer..."
                           oninput="lessonManager.saveAnswer(this.value)">
                </div>
                ${this.showAnswerFeedback(question)}
            </div>
        `;
    }

    showAnswerFeedback(question) {
        if (!question.userAnswer) return '';
        
        const isCorrect = question.isCorrect;
        return `
            <div class="answer-feedback ${isCorrect ? 'correct' : 'incorrect'}">
                <i class="fas fa-${isCorrect ? 'check' : 'times'}"></i>
                ${isCorrect ? 'Correct!' : `Incorrect. The answer is: ${question.correctAnswer}`}
                ${question.explanation ? `<div class="explanation">${question.explanation}</div>` : ''}
            </div>
        `;
    }

    saveAnswer(answer) {
        this.userAnswers[this.currentQuestionIndex] = answer;
        
        const question = this.currentQuestions[this.currentQuestionIndex];
        if (question) {
            question.userAnswer = answer;
            question.isCorrect = answer === question.correctAnswer;
        }
        
        this.updateProgressDisplay();
    }

    saveMatchingAnswer(pairIndex, answer) {
        if (!this.userAnswers[this.currentQuestionIndex]) {
            this.userAnswers[this.currentQuestionIndex] = {};
        }
        this.userAnswers[this.currentQuestionIndex][pairIndex] = answer;
        
        const question = this.currentQuestions[this.currentQuestionIndex];
        if (question && question.pairs) {
            question.userAnswer = this.userAnswers[this.currentQuestionIndex];
            
            // Check if all pairs are correct
            const allCorrect = question.pairs.every((pair, idx) => 
                this.userAnswers[this.currentQuestionIndex][idx] === pair.english
            );
            question.isCorrect = allCorrect;
        }
        
        this.updateProgressDisplay();
    }

    nextQuestion() {
        if (this.currentQuestionIndex < this.currentQuestions.length - 1) {
            this.currentQuestionIndex++;
            this.renderCurrentQuestion();
        }
    }

    previousQuestion() {
        if (this.currentQuestionIndex > 0) {
            this.currentQuestionIndex--;
            this.renderCurrentQuestion();
        }
    }

    updateQuizControls() {
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        const submitBtn = document.querySelector('.submit-btn');
        const progress = document.querySelector('.progress');
        
        if (prevBtn && nextBtn && submitBtn && progress) {
            prevBtn.disabled = this.currentQuestionIndex === 0;
            
            if (this.currentQuestionIndex === this.currentQuestions.length - 1) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
            } else {
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            }
            
            progress.textContent = `Question ${this.currentQuestionIndex + 1} of ${this.currentQuestions.length}`;
        }
        
        this.updateProgressDisplay();
    }

    updateProgressDisplay() {
        const scoreDisplay = document.querySelector('.score');
        if (scoreDisplay) {
            const correctCount = this.currentQuestions.filter(q => q.isCorrect).length;
            scoreDisplay.textContent = `Score: ${correctCount}/${this.currentQuestions.length}`;
        }
    }

    async submitQuiz() {
        const correctCount = this.currentQuestions.filter(q => q.isCorrect).length;
        const totalQuestions = this.currentQuestions.length;
        const score = Math.round((correctCount / totalQuestions) * 10);
        
        // Calculate final score
        const finalScore = score > 0 ? score : 1; // Minimum 1 point for attempt
        
        try {
            await this.updateProgress(finalScore);
            
            const feedback = document.getElementById('quizFeedback');
            if (feedback) {
                const percentage = Math.round((correctCount / totalQuestions) * 100);
                feedback.innerHTML = `
                    <div class="quiz-result ${percentage >= 70 ? 'success' : 'warning'}">
                        <h5><i class="fas fa-${percentage >= 70 ? 'trophy' : 'award'}"></i> Quiz Completed!</h5>
                        <p>You scored ${correctCount} out of ${totalQuestions} (${percentage}%)</p>
                        <p>${this.getPerformanceMessage(percentage)}</p>
                        <button class="review-btn" onclick="lessonManager.showAnswerReview()">
                            <i class="fas fa-list"></i> Review Answers
                        </button>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error submitting quiz:', error);
        }
    }

    getPerformanceMessage(percentage) {
        if (percentage >= 90) return "Excellent work! You've mastered this material! 🎉";
        if (percentage >= 70) return "Good job! You have a solid understanding. 👍";
        if (percentage >= 50) return "Not bad! Keep practicing to improve. 💪";
        return "Keep studying! Review the material and try again. 📚";
    }

    showAnswerReview() {
        const container = document.getElementById('questionContainer');
        if (!container) return;

        let html = `
            <div class="answer-review">
                <h5><i class="fas fa-chart-bar"></i> Answer Review</h5>
                ${this.currentQuestions.map((question, index) => `
                    <div class="review-item ${question.isCorrect ? 'correct' : 'incorrect'}">
                        <div class="review-question">
                            <strong>Q${index + 1}:</strong> ${question.question}
                        </div>
                        <div class="review-answer">
                            <span class="user-answer">Your answer: ${this.getUserAnswerDisplay(question)}</span>
                            ${!question.isCorrect ? `
                                <span class="correct-answer">Correct answer: ${question.correctAnswer}</span>
                            ` : ''}
                        </div>
                        ${question.explanation ? `
                            <div class="review-explanation">${question.explanation}</div>
                        ` : ''}
                    </div>
                `).join('')}
                <button class="retry-btn" onclick="lessonManager.retryQuiz()">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
        `;

        container.innerHTML = html;
        
        // Hide controls during review
        const controls = document.querySelector('.quiz-controls');
        if (controls) controls.style.display = 'none';
    }

    getUserAnswerDisplay(question) {
        if (question.type === 'matching' && question.userAnswer) {
            return Object.values(question.userAnswer).join(', ') || 'Not answered';
        }
        return question.userAnswer || 'Not answered';
    }

    retryQuiz() {
        this.currentQuestionIndex = 0;
        this.userAnswers = {};
        this.currentQuestions.forEach(q => {
            q.userAnswer = null;
            q.isCorrect = false;
        });
        
        this.renderCurrentQuestion();
        
        const controls = document.querySelector('.quiz-controls');
        if (controls) controls.style.display = 'flex';
        
        const feedback = document.getElementById('quizFeedback');
        if (feedback) feedback.innerHTML = '';
    }

    // Other exercise types with similar question systems
    createPhrasesExercise() {
        const phrases = this.exerciseData.phrases || {};
        const phraseEntries = Object.entries(phrases);
        
        if (phraseEntries.length === 0) {
            return this.createNoDataMessage('phrases');
        }

        this.currentQuestions = this.generatePhraseQuestions(phraseEntries);
        this.currentQuestionIndex = 0;
        this.userAnswers = {};

        return `
            <div class="exercise phrases-exercise">
                <h4><i class="fas fa-comment"></i> Phrases Practice</h4>
                <div class="quiz-container">
                    <div class="quiz-header">
                        <div class="progress">Question 1 of ${this.currentQuestions.length}</div>
                        <div class="score">Score: 0/${this.currentQuestions.length}</div>
                    </div>
                    <div id="questionContainer"></div>
                    <div class="quiz-controls">
                        <button class="nav-btn prev-btn" onclick="lessonManager.previousQuestion()" disabled>
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button class="nav-btn next-btn" onclick="lessonManager.nextQuestion()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button class="submit-btn" onclick="lessonManager.submitQuiz()" style="display: none;">
                            <i class="fas fa-paper-plane"></i> Submit Quiz
                        </button>
                    </div>
                </div>
                <div class="feedback" id="quizFeedback"></div>
            </div>
        `;
    }

    generatePhraseQuestions(phraseEntries) {
        return phraseEntries.map(([key, phrase], index) => {
            const phraseText = typeof phrase === 'string' ? phrase : phrase.text || phrase.french || '';
            const words = phraseText.split(' ');
            const blankIndex = Math.floor(Math.random() * words.length);
            const correctAnswer = words[blankIndex];
            words[blankIndex] = '_______';
            
            return {
                type: 'fill_blank',
                question: `Complete the phrase: "${words.join(' ')}"`,
                correctAnswer: correctAnswer,
                explanation: `The complete phrase is: "${phraseText}"`,
                userAnswer: null,
                isCorrect: false
            };
        });
    }

    createGrammarExercise() {
        const grammar = this.exerciseData.grammar || {};
        const examples = grammar.examples || [];
        
        if (examples.length === 0) {
            return this.createNoDataMessage('grammar examples');
        }

        this.currentQuestions = this.generateGrammarQuestions(examples);
        this.currentQuestionIndex = 0;
        this.userAnswers = {};

        return `
            <div class="exercise grammar-exercise">
                <h4><i class="fas fa-language"></i> Grammar Practice</h4>
                <div class="quiz-container">
                    <div class="quiz-header">
                        <div class="progress">Question 1 of ${this.currentQuestions.length}</div>
                        <div class="score">Score: 0/${this.currentQuestions.length}</div>
                    </div>
                    <div id="questionContainer"></div>
                    <div class="quiz-controls">
                        <button class="nav-btn prev-btn" onclick="lessonManager.previousQuestion()" disabled>
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button class="nav-btn next-btn" onclick="lessonManager.nextQuestion()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button class="submit-btn" onclick="lessonManager.submitQuiz()" style="display: none;">
                            <i class="fas fa-paper-plane"></i> Submit Quiz
                        </button>
                    </div>
                </div>
                <div class="feedback" id="quizFeedback"></div>
            </div>
        `;
    }

    generateGrammarQuestions(examples) {
        return examples.map((example, index) => {
            const exampleText = typeof example === 'string' ? example : example.text || '';
            return {
                type: 'fill_blank',
                question: `Correct this sentence: "${this.createIncorrectVersion(exampleText)}"`,
                correctAnswer: exampleText,
                explanation: `The correct sentence is: "${exampleText}"`,
                userAnswer: null,
                isCorrect: false
            };
        });
    }

    createPracticeExercise() {
        const vocabulary = this.exerciseData.vocabulary || [];
        const phrases = this.exerciseData.phrases || {};
        const grammar = this.exerciseData.grammar || {};
        
        if (vocabulary.length === 0 && Object.keys(phrases).length === 0) {
            return this.createNoDataMessage('practice');
        }

        this.currentQuestions = [
            ...this.generateVocabularyQuestions(vocabulary),
            ...this.generatePhraseQuestions(Object.entries(phrases)),
            ...this.generateGrammarQuestions(grammar.examples || [])
        ].slice(0, 8); // Limit to 8 questions for mixed practice

        this.currentQuestionIndex = 0;
        this.userAnswers = {};

        return `
            <div class="exercise practice-exercise">
                <h4><i class="fas fa-star"></i> Mixed Practice</h4>
                <div class="quiz-container">
                    <div class="quiz-header">
                        <div class="progress">Question 1 of ${this.currentQuestions.length}</div>
                        <div class="score">Score: 0/${this.currentQuestions.length}</div>
                    </div>
                    <div id="questionContainer"></div>
                    <div class="quiz-controls">
                        <button class="nav-btn prev-btn" onclick="lessonManager.previousQuestion()" disabled>
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button class="nav-btn next-btn" onclick="lessonManager.nextQuestion()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button class="submit-btn" onclick="lessonManager.submitQuiz()" style="display: none;">
                            <i class="fas fa-paper-plane"></i> Submit Quiz
                        </button>
                    </div>
                </div>
                <div class="feedback" id="quizFeedback"></div>
            </div>
        `;
    }

    // Ensure practice UI always appears and awards +10 when student completes correctly.
    renderPracticeExercise() {
        const container = document.getElementById('exerciseContent');
        if (!container) return;

        // always show practice fields even when lessonData is empty
        const sample = (this.lessonData && Array.isArray(this.lessonData.vocabulary) && this.lessonData.vocabulary[0])
            ? (this.lessonData.vocabulary[0].word || this.lessonData.vocabulary[0].translation || 'Thank you')
            : 'Thank you';

        container.innerHTML = `
            <div class="exercise-content practice-exercise">
                <div class="question"><h4>Practice Exercise — ${this.escapeHtml(this.topic || this.language)}</h4></div>

                <div class="exercise-section">
                    <label for="vocabInput"><strong>Vocabulary</strong> — translate: "<em>${this.escapeHtml(sample)}</em>"</label><br>
                    <input id="vocabInput" class="quick-answer-input" type="text" placeholder="Translation..." aria-label="Vocabulary translation">
                </div>

                <div class="exercise-section">
                    <label for="phraseInput"><strong>Phrase</strong> — write one sentence using words from the lesson</label><br>
                    <textarea id="phraseInput" class="sentence-construction" rows="3" placeholder="Write a sentence..." aria-label="Phrase construction"></textarea>
                </div>

                <div class="exercise-section">
                    <label for="grammarInput"><strong>Grammar</strong> — show one example using the grammar point</label><br>
                    <textarea id="grammarInput" class="grammar-application" rows="2" placeholder="Grammar example..." aria-label="Grammar application"></textarea>
                </div>

                <div class="practice-actions" style="margin-top:1rem">
                    <button id="practiceSubmit" class="submit-btn">Submit Answers</button>
                    <div id="practiceFeedback" style="margin-top:0.8rem"></div>
                </div>
            </div>
        `;

        // attach handler
        const submitBtn = document.getElementById('practiceSubmit');
        if (submitBtn) submitBtn.addEventListener('click', () => this.checkComprehensiveExercise());
    }

    // Simple validation: if user provides meaningful answers, award +10 points once.
    checkComprehensiveExercise() {
        const feedbackEl = document.getElementById('practiceFeedback');
        if (!feedbackEl) return;

        const vocabInput = document.getElementById('vocabInput');
        const phraseInput = document.getElementById('phraseInput');
        const grammarInput = document.getElementById('grammarInput');

        // Basic checks for "solved correctly"
        const vocabOk = vocabInput && vocabInput.value.trim().length > 0;
        const phraseOk = phraseInput && phraseInput.value.trim().length > 6;
        const grammarOk = grammarInput && grammarInput.value.trim().length > 8;

        // Decide success: require at least two of three to be valid OR all fields if you prefer stricter rules
        const passed = ([vocabOk, phraseOk, grammarOk].filter(Boolean).length >= 2);

        if (passed) {
            // award exactly +10 points for completing practice correctly
            const points = 10;
            this.userScore = (this.userScore || 0) + points;

            // Persist to server (lesson.php supports POST action=update_progress)
            this.updateProgress(points);

            feedbackEl.innerHTML = `<div class="feedback success">✅ Well done — practice completed! +${points} points awarded.</div>`;

            // disable submit to prevent duplicate awarding
            const submitBtn = document.getElementById('practiceSubmit');
            if (submitBtn) submitBtn.disabled = true;
        } else {
            feedbackEl.innerHTML = `<div class="feedback error">❌ Please complete at least two of the three tasks with meaningful answers.</div>`;
        }
    }

    // Utility methods
    shuffleArray(array) {
        return array.sort(() => Math.random() - 0.5);
    }

    createIncorrectVersion(sentence) {
        const modifications = [
            sentence.replace(/\b(is|am|are)\b/, 'be'),
            sentence.replace(/\b(the|a|an)\b/, ''),
            sentence + '...',
            sentence.toLowerCase(),
            sentence.replace(/\.$/, '')
        ];
        return modifications[Math.floor(Math.random() * modifications.length)];
    }

    createNoDataMessage(dataType) {
        return `
            <div class="exercise no-data-exercise">
                <h4><i class="fas fa-clipboard-list"></i> Practice Exercise</h4>
                <div class="no-data-message">
                    <i class="fas fa-info-circle"></i>
                    <p>No ${dataType} data available for exercises.</p>
                    <p>Please check back later or try another lesson.</p>
                </div>
            </div>
        `;
    }

    createFallbackExercise() {
        return `
            <div class="exercise fallback-exercise">
                <h4>Interactive Exercise</h4>
                <p>Exercise content for this lesson type is being developed.</p>
            </div>
        `;
    }

    setupEventListeners() {
        // Render first question after DOM update
        setTimeout(() => {
            this.renderCurrentQuestion();
        }, 100);
    }

    async updateProgress(score) {
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'update_progress',
                    lang: this.config.language,
                    topic: this.config.topic,
                    lesson_type: this.currentExercise,
                    score: score,
                    csrf_token: this.config.csrfToken
                })
            });

            const result = await response.json();
            if (result.success) {
                console.log('Progress updated successfully');
            }
        } catch (error) {
            console.error('Failed to update progress:', error);
        }
    }
}

// Initialize when DOM loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof CONFIG !== 'undefined') {
        window.lessonManager = new ProfessionalLessonManager(CONFIG);
    }
});

function playAudio(text) {
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'fr-FR';
        speechSynthesis.speak(utterance);
    }
}