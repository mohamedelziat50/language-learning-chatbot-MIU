// Simple and Reliable Lesson Manager
class SimpleLessonManager {
    constructor() {
        this.userScore = 0;
        this.init();
    }

    init() {
        console.log('SimpleLessonManager initialized');
        this.loadExercise();
    }

    loadExercise() {
        const exerciseContent = document.getElementById('exerciseContent');
        if (!exerciseContent) {
            console.error('Exercise content element not found');
            return;
        }

        console.log('Loading exercise...');
        console.log('Language:', typeof language !== 'undefined' ? language : 'undefined');
        console.log('Topic:', typeof topic !== 'undefined' ? topic : 'undefined');
        console.log('Lesson Type:', typeof currentLessonType !== 'undefined' ? currentLessonType : 'undefined');

        // Load data from JSON file
        this.loadLessonData();
    }

    async loadLessonData() {
        try {
            const response = await fetch('lesson_data.json');
            const allData = await response.json();
            
            console.log('Loaded JSON data:', allData);
            
            const currentLanguage = typeof language !== 'undefined' ? language : 'French';
            const currentTopic = typeof topic !== 'undefined' ? topic : 'Greetings';
            
            console.log('Looking for:', currentLanguage, currentTopic);
            
            if (allData[currentLanguage] && allData[currentLanguage][currentTopic]) {
                const lessonData = allData[currentLanguage][currentTopic];
                console.log('Found lesson data:', lessonData);
                this.createExerciseFromData(lessonData);
            } else {
                console.log('No data found, using fallback');
                this.createFallbackExercise();
            }
        } catch (error) {
            console.error('Error loading lesson data:', error);
            this.createFallbackExercise();
        }
    }

    createExerciseFromData(lessonData) {
        const exerciseContent = document.getElementById('exerciseContent');
        
        console.log('Creating exercise from data:', lessonData);
        
        // Check what type of content we have
        if (lessonData.vocabulary && lessonData.vocabulary.length > 0) {
            this.createVocabularyExerciseFromData(lessonData.vocabulary);
        } else if (lessonData.phrases && Object.keys(lessonData.phrases).length > 0) {
            this.createPhrasesExerciseFromData(lessonData.phrases);
        } else if (lessonData.grammar) {
            this.createGrammarExerciseFromData(lessonData.grammar);
        } else if (lessonData.conversation && lessonData.conversation.length > 0) {
            this.createConversationExerciseFromData(lessonData.conversation);
        } else {
            this.createFallbackExercise();
        }
    }

    createVocabularyExerciseFromData(vocabulary) {
        const exerciseContent = document.getElementById('exerciseContent');
        
        if (vocabulary.length < 2) {
            this.createFallbackExercise();
            return;
        }

        // Pick a random word
        const testWord = vocabulary[Math.floor(Math.random() * vocabulary.length)];
        const wrongOptions = vocabulary
            .filter(item => item.word !== testWord.word)
            .sort(() => Math.random() - 0.5)
            .slice(0, 3);

        const allOptions = [testWord, ...wrongOptions].sort(() => Math.random() - 0.5);

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">
                    <h4>What is the correct translation for "<strong>${testWord.word}</strong>" in ${typeof language !== 'undefined' ? language : 'French'}?</h4>
                </div>
                <div class="options-grid">
                    ${allOptions.map((item, index) => `
                        <div class="option" data-correct="${item.word === testWord.word}" onclick="selectOption(this)">
                            <strong>${item.translation}</strong>
                            <div class="pronunciation">${item.pronunciation || ''}</div>
                        </div>
                    `).join('')}
                </div>
                <div class="exercise-hint">
                    <i class="fas fa-lightbulb"></i> Choose the correct translation!
                </div>
                <button class="submit-btn" onclick="checkAnswer()" disabled>
                    <i class="fas fa-check"></i> Check Answer
                </button>
                <div id="feedback"></div>
            </div>
        `;

        exerciseContent.innerHTML = exerciseHTML;
        this.setupEventListeners();
    }

    createPhrasesExerciseFromData(phrases) {
        const exerciseContent = document.getElementById('exerciseContent');
        const phraseEntries = Object.entries(phrases);
        
        if (phraseEntries.length === 0) {
            this.createFallbackExercise();
            return;
        }

        const [situation, correctAnswer] = phraseEntries[Math.floor(Math.random() * phraseEntries.length)];

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">
                    <h4>How would you say this in ${typeof language !== 'undefined' ? language : 'French'}?</h4>
                </div>
                <div class="scenario">
                    <strong>Situation:</strong> ${this.getPhraseSituation(situation)}
                </div>
                <div class="fill-blank-exercise">
                    <textarea class="phrase-input" placeholder="Write your translation here..." rows="3"></textarea>
                    <div class="writing-tips">
                        <i class="fas fa-lightbulb"></i> Tip: Try to remember the phrases we learned!
                    </div>
                    <button class="submit-btn" onclick="checkPhraseAnswer('${correctAnswer}')">
                        <i class="fas fa-check"></i> Check Translation
                    </button>
                </div>
                <div id="feedback"></div>
            </div>
        `;

        exerciseContent.innerHTML = exerciseHTML;
    }

    createGrammarExerciseFromData(grammar) {
        const exerciseContent = document.getElementById('exerciseContent');

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">
                    <h4>Apply the grammar rule to complete the exercise:</h4>
                </div>
                <div class="grammar-exercise">
                    <div class="grammar-rule">
                        <strong>Rule:</strong> ${grammar.explanation || 'Grammar rule'}
                    </div>
                    <div class="exercise-task">
                        <p><strong>Task:</strong> Create 2 sentences using the grammar rule we just learned.</p>
                        <div class="sentence-input-group">
                            <label>Sentence 1:</label>
                            <textarea class="sentence-input" placeholder="Write your first sentence here..." rows="2"></textarea>
                        </div>
                        <div class="sentence-input-group">
                            <label>Sentence 2:</label>
                            <textarea class="sentence-input" placeholder="Write your second sentence here..." rows="2"></textarea>
                        </div>
                    </div>
                    <button class="submit-btn" onclick="checkGrammarExercise()">
                        <i class="fas fa-check"></i> Submit Sentences
                    </button>
                </div>
                <div id="feedback"></div>
            </div>
        `;

        exerciseContent.innerHTML = exerciseHTML;
    }

    createConversationExerciseFromData(conversation) {
        const exerciseContent = document.getElementById('exerciseContent');
        
        if (conversation.length < 2) {
            this.createFallbackExercise();
            return;
        }

        const missingIndex = Math.floor(Math.random() * conversation.length);

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">
                    <h4>Complete the conversation by filling in the missing line:</h4>
                </div>
                <div class="conversation-exercise">
                    ${conversation.map((line, index) => `
                        <div class="dialogue-line ${index === missingIndex ? 'missing-line' : ''}">
                            <span class="speaker">${line.speaker}:</span>
                            ${index === missingIndex ?
                                `<div class="missing-input-container">
                                    <textarea class="dialogue-input" placeholder="What should ${line.speaker} say here?" rows="2"></textarea>
                                    <div class="hint">Hint: This should be about "${line.translation || line.text || ''}"</div>
                                </div>` :
                                `<span class="text">${line.text}</span>
                                <span class="translation-hint">(${line.translation || ''})</span>`
                            }
                        </div>
                    `).join('')}
                </div>
                <button class="submit-btn" onclick="checkConversationAnswer(${missingIndex})">
                    <i class="fas fa-check"></i> Check Your Response
                </button>
                <div id="feedback"></div>
            </div>
        `;

        exerciseContent.innerHTML = exerciseHTML;
    }

    createFallbackExercise() {
        const exerciseContent = document.getElementById('exerciseContent');
        
        // Fallback vocabulary for different languages
        const fallbackVocabulary = {
            'French': [
                { word: "Hello", translation: "Bonjour", pronunciation: "bon-zhoor" },
                { word: "Thank you", translation: "Merci", pronunciation: "mair-see" }
            ],
            'Spanish': [
                { word: "Hello", translation: "Hola", pronunciation: "oh-lah" },
                { word: "Thank you", translation: "Gracias", pronunciation: "grah-see-ahs" }
            ],
            'German': [
                { word: "Hello", translation: "Hallo", pronunciation: "hah-loh" },
                { word: "Thank you", translation: "Danke", pronunciation: "dahn-keh" }
            ],
            'English': [
                { word: "Hello", translation: "Hello", pronunciation: "heh-loh" },
                { word: "Thank you", translation: "Thank you", pronunciation: "thangk yoo" }
            ]
        };

        const currentLanguage = typeof language !== 'undefined' ? language : 'French';
        const vocabulary = fallbackVocabulary[currentLanguage] || fallbackVocabulary['French'];

        const testWord = vocabulary[Math.floor(Math.random() * vocabulary.length)];
        const wrongOptions = vocabulary
            .filter(item => item.word !== testWord.word)
            .sort(() => Math.random() - 0.5)
            .slice(0, 2);

        const allOptions = [testWord, ...wrongOptions].sort(() => Math.random() - 0.5);

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">
                    <h4>What is the correct translation for "<strong>${testWord.word}</strong>" in ${currentLanguage}?</h4>
                    </div>
                <div class="options-grid">
                    ${allOptions.map((item, index) => `
                        <div class="option" data-correct="${item.word === testWord.word}" onclick="selectOption(this)">
                            <strong>${item.translation}</strong>
                            <div class="pronunciation">${item.pronunciation}</div>
                    </div>
                    `).join('')}
                    </div>
                <div class="exercise-hint">
                    <i class="fas fa-lightbulb"></i> Choose the correct translation!
                </div>
                <button class="submit-btn" onclick="checkAnswer()" disabled>
                    <i class="fas fa-check"></i> Check Answer
                </button>
                <div id="feedback"></div>
            </div>
        `;

        exerciseContent.innerHTML = exerciseHTML;
        this.setupEventListeners();
    }

    getPhraseSituation(situationKey) {
        const situations = {
            'formal_greeting': 'You meet your professor in the morning. How do you greet them formally?',
            'informal_greeting': 'You see your friend at a cafe. How do you greet them casually?',
            'ordering': 'You\'re at a restaurant and want to order politely.',
            'asking_directions': 'You\'re lost and need to find the train station.',
            'introducing_family': 'You\'re showing a photo and introducing your mother.',
            'asking_about_family': 'You want to know how many siblings someone has.',
            'default': 'Translate this phrase for daily conversation'
        };
        return situations[situationKey] || situations.default;
    }

    setupEventListeners() {
        // Add click handlers for options
        const options = document.querySelectorAll('.option');
        options.forEach(option => {
            option.addEventListener('click', function() {
                // Remove previous selection
                options.forEach(opt => opt.classList.remove('selected'));
                // Add selection to clicked option
                this.classList.add('selected');
                // Enable submit button
                document.querySelector('.submit-btn').disabled = false;
            });
        });
    }

    selectOption(element) {
        const options = document.querySelectorAll('.option');
        options.forEach(opt => opt.classList.remove('selected'));
        element.classList.add('selected');
        document.querySelector('.submit-btn').disabled = false;
    }

    checkAnswer() {
        const selectedOption = document.querySelector('.option.selected');
        if (!selectedOption) {
            this.showFeedback('Please select an answer first!', 'error');
            return;
        }

        const isCorrect = selectedOption.getAttribute('data-correct') === 'true';
        const options = document.querySelectorAll('.option');

        if (isCorrect) {
            selectedOption.classList.add('correct');
            this.userScore += 10;
            this.showFeedback('✅ Correct! Excellent job!', 'success');
            this.enableNextButton();
        } else {
            selectedOption.classList.add('incorrect');
            // Show correct answer
            options.forEach(opt => {
                if (opt.getAttribute('data-correct') === 'true') {
                    opt.classList.add('correct');
                }
            });
            this.showFeedback('❌ Not quite right. The correct answer is highlighted.', 'error');
        }

        document.querySelector('.submit-btn').disabled = true;
    }

    showFeedback(message, type) {
        const feedbackDiv = document.getElementById('feedback');
        if (feedbackDiv) {
            feedbackDiv.innerHTML = `
                <div class="feedback ${type}">
                    ${message}
                    ${type === 'success' ? `<div class="score">+10 points! Total: ${this.userScore}</div>` : ''}
                </div>
            `;
        }
    }

    enableNextButton() {
        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) {
            nextBtn.style.opacity = '1';
            nextBtn.style.pointerEvents = 'auto';
        }
    }

    checkPhraseAnswer(correctAnswer) {
        const userInput = document.querySelector('.phrase-input');
        if (!userInput) {
            this.showFeedback('Please enter your translation first!', 'error');
            return;
        }

        const userAnswer = userInput.value.trim().toLowerCase();
        const correctAnswerLower = correctAnswer.toLowerCase();

        if (userAnswer === correctAnswerLower) {
            this.userScore += 15;
            this.showFeedback('✅ Excellent! Your translation is correct!', 'success');
            this.enableNextButton();
        } else {
            this.showFeedback(`❌ Not quite right. The correct answer is: "${correctAnswer}"`, 'error');
        }
    }

    checkGrammarExercise() {
        const sentences = document.querySelectorAll('.sentence-input');
        let completedSentences = 0;

        sentences.forEach(sentence => {
            if (sentence.value.trim().length > 0) {
                completedSentences++;
            }
        });

        if (completedSentences === 0) {
            this.showFeedback('Please write at least one sentence!', 'error');
            return;
        }

        this.userScore += completedSentences * 10;
        this.showFeedback(`✅ Great job! You completed ${completedSentences} sentence(s). +${completedSentences * 10} points!`, 'success');
        this.enableNextButton();
    }

    checkConversationAnswer(missingIndex) {
        const userInput = document.querySelector('.dialogue-input');
        if (!userInput) {
            this.showFeedback('Please enter your response first!', 'error');
            return;
        }

        const userAnswer = userInput.value.trim();
        if (userAnswer.length === 0) {
            this.showFeedback('Please enter your response first!', 'error');
            return;
        }

        this.userScore += 20;
        this.showFeedback('✅ Good response! Conversation practice completed!', 'success');
        this.enableNextButton();
    }
}

// Global functions for onclick handlers
function selectOption(element) {
    window.lessonManager.selectOption(element);
}

function checkAnswer() {
    window.lessonManager.checkAnswer();
}

function checkPhraseAnswer(correctAnswer) {
    window.lessonManager.checkPhraseAnswer(correctAnswer);
}

function checkGrammarExercise() {
    window.lessonManager.checkGrammarExercise();
}

function checkConversationAnswer(missingIndex) {
    window.lessonManager.checkConversationAnswer(missingIndex);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing lesson manager...');
    
    // Check if required elements exist
    const exerciseContent = document.getElementById('exerciseContent');
    if (!exerciseContent) {
        console.error('Exercise content element not found');
            return;
        }

    try {
        window.lessonManager = new SimpleLessonManager();
        console.log('Lesson manager started successfully');
    } catch (error) {
        console.error('Failed to initialize lesson manager:', error);
        exerciseContent.innerHTML = `
            <div class="error-message">
                <p>Error loading exercise. Please refresh the page.</p>
                <button onclick="location.reload()" class="btn-primary">Reload Page</button>
            </div>
        `;
    }
});

// Add CSS styles
const style = document.createElement('style');
style.textContent = `
    .exercise-content {
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 1rem 0;
    }
    
    .question {
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
        color: #333;
    }
    
    .options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .option {
        padding: 1rem;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }
    
    .option:hover {
        border-color: #28a745;
        transform: translateY(-2px);
    }
    
    .option.selected {
        border-color: #007bff;
        background: #e3f2fd;
    }
    
    .option.correct {
        border-color: #28a745;
        background: #d4edda;
    }
    
    .option.incorrect {
        border-color: #dc3545;
        background: #f8d7da;
    }
    
    .pronunciation {
        font-style: italic;
        color: #666;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    
    .exercise-hint {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 4px;
        padding: 0.75rem;
        margin-bottom: 1rem;
        color: #856404;
    }
    
    .submit-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: background 0.3s ease;
    }
    
    .submit-btn:hover:not(:disabled) {
        background: #218838;
    }
    
    .submit-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }
    
    .feedback {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 4px;
        font-weight: 500;
    }
    
    .feedback.success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .feedback.error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .score {
        margin-top: 0.5rem;
        font-weight: bold;
        color: #28a745;
    }
    
    .error-message {
        text-align: center;
        padding: 2rem;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        color: #721c24;
    }
    
    .btn-primary {
        background: #007bff;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 1rem;
    }
    
    .scenario {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 4px;
        padding: 1rem;
        margin: 1rem 0;
        color: #1565c0;
    }
    
    .phrase-input, .sentence-input, .dialogue-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e9ecef;
        border-radius: 4px;
        font-size: 1rem;
        margin: 0.5rem 0;
        resize: vertical;
    }
    
    .phrase-input:focus, .sentence-input:focus, .dialogue-input:focus {
        border-color: #007bff;
        outline: none;
    }
    
    .writing-tips {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 4px;
        padding: 0.75rem;
        margin: 1rem 0;
        color: #856404;
        font-size: 0.9rem;
    }
    
    .grammar-rule {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 1rem;
        margin: 1rem 0;
        border-radius: 0 4px 4px 0;
    }
    
    .sentence-input-group {
        margin: 1rem 0;
    }
    
    .sentence-input-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }
    
    .conversation-exercise {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin: 1rem 0;
    }
    
    .dialogue-line {
        margin: 0.75rem 0;
        padding: 0.75rem;
        border-radius: 4px;
        background: white;
        border: 1px solid #e9ecef;
    }
    
    .dialogue-line.missing-line {
        background: #fff3cd;
        border-color: #ffeaa7;
    }
    
    .speaker {
        font-weight: bold;
        color: #007bff;
        margin-right: 0.5rem;
    }
    
    .translation-hint {
        font-style: italic;
        color: #666;
        font-size: 0.9rem;
        margin-left: 0.5rem;
    }
    
    .missing-input-container {
        margin-top: 0.5rem;
    }
    
    .hint {
        font-size: 0.8rem;
        color: #666;
        margin-top: 0.25rem;
        font-style: italic;
    }
`;
document.head.appendChild(style);