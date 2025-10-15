class RealLessonManager {
    constructor() {
        this.currentExercise = null;
        this.userScore = 0;
        this.currentLessonType = lessonData.type;
        this.init();
    }

    init() {
        this.loadRealExercise();
        this.setupEventListeners();
        console.log('Lesson manager initialized for:', this.currentLessonType);
    }

    loadRealExercise() {
        const exerciseContent = document.getElementById('exerciseContent');
        if (!lessonData || !exerciseContent) {
            console.error('Exercise content not found');
            return;
        }

        try {
            switch (this.currentLessonType) {
                case 'vocabulary':
                    this.renderVocabularyExercise();
                    break;
                case 'phrases':
                    this.renderPhrasesExercise();
                    break;
                case 'grammar':
                    this.renderGrammarExercise();
                    break;
                case 'conversation':
                    this.renderConversationExercise();
                    break;
                case 'practice':
                    this.renderComprehensiveExercise();
                    break;
                default:
                    this.renderDefaultExercise();
            }
        } catch (error) {
            console.error('Error loading exercise:', error);
            this.showError('Failed to load exercise. Please refresh the page.');
        }
    }

    renderVocabularyExercise() {
        const vocabulary = lessonData.content;
        if (!vocabulary || !Array.isArray(vocabulary)) {
            this.showError('Vocabulary content not available');
            return;
        }

        const testWord = vocabulary[Math.floor(Math.random() * vocabulary.length)];
        const wrongOptions = vocabulary
            .filter(item => item.word !== testWord.word)
            .sort(() => Math.random() - 0.5)
            .slice(0, 3);
        
        const allOptions = [testWord, ...wrongOptions].sort(() => Math.random() - 0.5);

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">What is the correct translation for "<strong>${this.escapeHtml(testWord.word)}</strong>" in ${language}?</div>
                <div class="options-grid">
                    ${allOptions.map((item, index) => `
                        <div class="option" data-correct="${item.word === testWord.word}" onclick="realLessonManager.selectOption(this)">
                            <strong>${this.escapeHtml(item.translation)}</strong>
                            ${item.pronunciation ? `<div class="pronunciation-hint">${this.escapeHtml(item.pronunciation)}</div>` : ''}
                        </div>
                    `).join('')}
                </div>
                <div class="exercise-hint">
                    <i class="fas fa-lightbulb"></i> Remember: We learned this word in the vocabulary section!
                </div>
                <button class="submit-btn" onclick="realLessonManager.checkAnswer()" disabled>
                    <i class="fas fa-check"></i> Check Answer
                </button>
                <div id="feedback"></div>
            </div>
        `;
        
        document.getElementById('exerciseContent').innerHTML = exerciseHTML;
    }

    renderPhrasesExercise() {
        const phrases = lessonData.content;
        if (!phrases || typeof phrases !== 'object') {
            this.showError('Phrases content not available');
            return;
        }

        const phraseEntries = Object.entries(phrases);
        if (phraseEntries.length === 0) {
            this.showError('No phrases available for this exercise');
            return;
        }

        const testPhrase = phraseEntries[Math.floor(Math.random() * phraseEntries.length)];
        const [situation, correctAnswer] = testPhrase;

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">How would you say this in ${language}?</div>
                <div class="scenario">
                    <strong>Situation:</strong> ${this.getPhraseSituation(situation)}
                </div>
                <div class="fill-blank-exercise">
                    <textarea class="phrase-input" placeholder="Write your translation here..." rows="3"></textarea>
                    <div class="writing-tips">
                        <i class="fas fa-tips"></i> Tip: Try to remember the phrases we learned earlier!
                    </div>
                    <button class="submit-btn" onclick="realLessonManager.checkPhraseAnswer('${this.escapeHtml(correctAnswer)}')">
                        <i class="fas fa-check"></i> Check Translation
                    </button>
                </div>
                <div id="feedback"></div>
            </div>
        `;
        
        document.getElementById('exerciseContent').innerHTML = exerciseHTML;
    }

    renderGrammarExercise() {
        const grammar = lessonData.content;
        if (!grammar || typeof grammar !== 'object') {
            this.showError('Grammar content not available');
            return;
        }

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">Apply the grammar rule to complete the exercise:</div>
                <div class="grammar-exercise">
                    <div class="grammar-rule">
                        <strong>Rule:</strong> ${this.escapeHtml(grammar.explanation || 'No explanation available')}
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
                        
                        ${grammar.examples && grammar.examples.length > 0 ? `
                            <div class="examples-reference">
                                <strong>Examples from the lesson:</strong>
                                ${grammar.examples.map(example => `<div class="example-item">${this.escapeHtml(example)}</div>`).join('')}
                            </div>
                        ` : ''}
                    </div>
                    
                    <button class="submit-btn" onclick="realLessonManager.checkGrammarExercise()">
                        <i class="fas fa-check"></i> Submit Sentences
                    </button>
                </div>
                <div id="feedback"></div>
            </div>
        `;
        
        document.getElementById('exerciseContent').innerHTML = exerciseHTML;
    }

    renderConversationExercise() {
        const conversation = lessonData.content;
        if (!conversation || !Array.isArray(conversation)) {
            this.showError('Conversation content not available');
            return;
        }

        const missingIndex = Math.floor(Math.random() * conversation.length);
        const missingLine = conversation[missingIndex];

        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">Complete the conversation by filling in the missing line:</div>
                <div class="conversation-exercise">
                    <div class="conversation-context">
                        <strong>Context:</strong> ${this.getConversationContext(topic)}
                    </div>
                    
                    ${conversation.map((line, index) => `
                        <div class="dialogue-line ${index === missingIndex ? 'missing-line' : ''}">
                            <span class="speaker">${this.escapeHtml(line.speaker)}:</span>
                            ${index === missingIndex ? 
                                `<div class="missing-input-container">
                                    <textarea class="dialogue-input" placeholder="What should ${this.escapeHtml(line.speaker)} say here?" rows="2"></textarea>
                                    <div class="hint">Hint: This should be about "${this.escapeHtml(line.translation)}"</div>
                                </div>` :
                                `<span class="text">${this.escapeHtml(line.text)}</span>
                                <span class="translation-hint">(${this.escapeHtml(line.translation)})</span>`
                            }
                        </div>
                    `).join('')}
                </div>
                <button class="submit-btn" onclick="realLessonManager.checkConversationAnswer(${missingIndex})">
                    <i class="fas fa-check"></i> Check Your Response
                </button>
                <div id="feedback"></div>
            </div>
        `;
        
        document.getElementById('exerciseContent').innerHTML = exerciseHTML;
    }

    renderComprehensiveExercise() {
        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">Final Practice: Test Your Knowledge of ${this.escapeHtml(topic)}</div>
                <div class="comprehensive-exercise">
                    <div class="exercise-section">
                        <h4>Vocabulary Recall</h4>
                        <p>Write the ${language} translation for: <strong>"Thank you"</strong></p>
                        <input type="text" class="quick-answer-input" placeholder="Translation...">
                    </div>
                    
                    <div class="exercise-section">
                        <h4>Phrase Construction</h4>
                        <p>Create a sentence using vocabulary words we learned:</p>
                        <textarea class="sentence-construction" placeholder="Your sentence..." rows="2"></textarea>
                    </div>
                    
                    <div class="exercise-section">
                        <h4>Grammar Application</h4>
                        <p>Use the grammar rule correctly in a new context:</p>
                        <textarea class="grammar-application" placeholder="Your example..." rows="2"></textarea>
                    </div>
                    
                    <div class="scoring-info">
                        <i class="fas fa-star"></i> Complete all sections to earn maximum points!
                    </div>
                </div>
                <button class="submit-btn" onclick="realLessonManager.checkComprehensiveExercise()">
                    <i class="fas fa-check"></i> Submit Final Answers
                </button>
                <div id="feedback"></div>
            </div>
        `;
        
        document.getElementById('exerciseContent').innerHTML = exerciseHTML;
    }

    renderDefaultExercise() {
        const exerciseHTML = `
            <div class="exercise-content">
                <div class="question">Practice Exercise</div>
                <div class="default-exercise">
                    <p>This exercise type is not yet implemented. Please try another lesson.</p>
                </div>
            </div>
        `;
        document.getElementById('exerciseContent').innerHTML = exerciseHTML;
    }

    // Helper Methods
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getPhraseSituation(situationKey) {
        const situations = {
            'formal_greeting': 'You meet your professor in the morning. How do you greet them formally?',
            'informal_greeting': 'You see your friend at a cafe. How do you greet them casually?',
            'ordering': 'You\'re at a restaurant and want to order pasta politely.',
            'asking_directions': 'You\'re lost and need to find the train station.',
            'introducing_family': 'You\'re showing a photo and introducing your mother.',
            'asking_about_family': 'You want to know how many siblings someone has.',
            'default': 'Translate this phrase for daily conversation'
        };
        
        return situations[situationKey] || situations.default;
    }

    getConversationContext(topicName) {
        const contexts = {
            'Greetings': 'Two friends meeting in the morning',
            'Food': 'A customer ordering at a restaurant', 
            'Travel': 'A tourist asking for directions',
            'Family': 'Two people talking about their families',
            'default': 'Daily conversation practice'
        };
        return contexts[topicName] || contexts.default;
    }

    // Exercise Methods
    selectOption(optionElement) {
        const options = document.querySelectorAll('.option');
        options.forEach(opt => {
            opt.classList.remove('selected');
        });
        optionElement.classList.add('selected');
        
        const submitBtn = document.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    }

    checkAnswer() {
        const selectedOption = document.querySelector('.option.selected');
        if (!selectedOption) {
            this.showFeedback('Please select an answer first.', 'incorrect');
            return;
        }

        const isCorrect = selectedOption.dataset.correct === 'true';
        
        if (isCorrect) {
            selectedOption.classList.add('correct');
            this.userScore += 10;
            this.showFeedback('✅ Correct! Excellent job remembering the vocabulary!', 'correct');
            this.markLessonComplete(10);
        } else {
            selectedOption.classList.add('incorrect');
            this.showFeedback('❌ Not quite. Let\'s review the correct answer.', 'incorrect');
            
            const options = document.querySelectorAll('.option');
            options.forEach(opt => {
                if (opt.dataset.correct === 'true') {
                    opt.classList.add('correct');
                }
            });
        }
        
        const submitBtn = document.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
    }

    checkPhraseAnswer(correctAnswer) {
        const phraseInput = document.querySelector('.phrase-input');
        if (!phraseInput) {
            this.showFeedback('Exercise element not found.', 'error');
            return;
        }

        const userAnswer = phraseInput.value.trim();
        
        if (!userAnswer) {
            this.showFeedback('Please write your translation before checking.', 'incorrect');
            return;
        }

        // Simple validation - in real app, use more sophisticated checking
        const normalizedCorrect = correctAnswer.toLowerCase().replace(/[.,!?]/g, '').trim();
        const normalizedUser = userAnswer.toLowerCase().replace(/[.,!?]/g, '').trim();

        if (normalizedUser === normalizedCorrect) {
            phraseInput.style.borderColor = 'var(--primary-green)';
            phraseInput.style.background = 'var(--light-green)';
            this.userScore += 15;
            this.showFeedback('✅ Excellent! Your translation is correct!', 'correct');
            this.markLessonComplete(15);
        } else {
            phraseInput.style.borderColor = '#ef4444';
            phraseInput.style.background = '#fef2f2';
            this.showFeedback(`Almost! The correct phrase is: "${correctAnswer}"`, 'incorrect');
        }
    }

    checkGrammarExercise() {
        const inputs = document.querySelectorAll('.sentence-input');
        let filledCount = 0;
        
        inputs.forEach(input => {
            if (input.value.trim().length > 5) {
                filledCount++;
                input.style.borderColor = 'var(--primary-green)';
                input.style.background = 'var(--light-green)';
            } else {
                input.style.borderColor = '#ef4444';
                input.style.background = '#fef2f2';
            }
        });
        
        if (filledCount === inputs.length) {
            this.userScore += 20;
            this.showFeedback('✅ Great work! You successfully applied the grammar rules!', 'correct');
            this.markLessonComplete(20);
        } else {
            this.showFeedback('❌ Please complete both sentences with meaningful examples.', 'incorrect');
        }
    }

    checkConversationAnswer(missingIndex) {
        const dialogueInput = document.querySelector('.dialogue-input');
        if (!dialogueInput) {
            this.showFeedback('Exercise element not found.', 'error');
            return;
        }

        const userInput = dialogueInput.value.trim();
        
        if (!userInput) {
            this.showFeedback('Please write your response before checking.', 'incorrect');
            return;
        }

        if (userInput.length > 10) {
            dialogueInput.style.borderColor = 'var(--primary-green)';
            dialogueInput.style.background = 'var(--light-green)';
            this.userScore += 15;
            this.showFeedback('✅ Perfect! Your response makes the conversation flow naturally!', 'correct');
            this.markLessonComplete(15);
        } else {
            this.showFeedback('❌ Try to write a more complete response that continues the conversation.', 'incorrect');
        }
    }

    checkComprehensiveExercise() {
        let score = 0;
        let feedback = [];
        
        // Check vocabulary
        const vocabInput = document.querySelector('.quick-answer-input');
        if (vocabInput) {
            const thankYouTranslation = this.getThankYouTranslation();
            if (vocabInput.value.trim().toLowerCase() === thankYouTranslation.toLowerCase()) {
                score += 10;
                feedback.push('✅ Vocabulary: Correct!');
                vocabInput.style.borderColor = 'var(--primary-green)';
            } else {
                feedback.push('❌ Vocabulary: The translation for "Thank you" is "' + thankYouTranslation + '"');
                vocabInput.style.borderColor = '#ef4444';
            }
        }
        
        // Check sentence construction
        const sentenceInput = document.querySelector('.sentence-construction');
        if (sentenceInput && sentenceInput.value.trim().length > 10) {
            score += 10;
            feedback.push('✅ Sentence: Good construction!');
            sentenceInput.style.borderColor = 'var(--primary-green)';
        } else {
            feedback.push('❌ Sentence: Please write a complete sentence');
            if (sentenceInput) sentenceInput.style.borderColor = '#ef4444';
        }
        
        // Check grammar application
        const grammarInput = document.querySelector('.grammar-application');
        if (grammarInput && grammarInput.value.trim().length > 8) {
            score += 10;
            feedback.push('✅ Grammar: Well applied!');
            grammarInput.style.borderColor = 'var(--primary-green)';
        } else {
            feedback.push('❌ Grammar: Please provide a complete example');
            if (grammarInput) grammarInput.style.borderColor = '#ef4444';
        }
        
        this.userScore += score;
        const finalFeedback = feedback.join('<br>');
        
        if (score >= 20) {
            this.showFeedback('🎉 Excellent! You scored ' + score + '/30 points!<br>' + finalFeedback, 'correct');
            this.markLessonComplete(score);
        } else {
            this.showFeedback('📝 Good effort! You scored ' + score + '/30 points.<br>' + finalFeedback, 'incorrect');
        }
    }

    getThankYouTranslation() {
        const translations = {
            'French': 'merci',
            'Spanish': 'gracias', 
            'German': 'danke',
            'Italian': 'grazie',
            'Arabic': 'شكرا',
            'Japanese': 'ありがとう',
            'English': 'thank you'
        };
        return translations[language] || 'thank you';
    }

    markLessonComplete(score) {
        console.log('Lesson completed with score:', score);
        
        // Enable next button after a delay
        setTimeout(() => {
            this.enableNextButton();
            
            // In a real app, you would send this to the server
            // For now, we'll just update the UI
            this.updateProgressDisplay(score);
        }, 1500);
    }

    updateProgressDisplay(score) {
        // Update any progress indicators on the page
        const progressElements = document.querySelectorAll('.progress-indicator');
        progressElements.forEach(element => {
            element.textContent = `Score: ${this.userScore}`;
        });
    }

    enableNextButton() {
        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) {
            nextBtn.classList.add('pulse-animation');
            nextBtn.style.opacity = '1';
        }
    }

    showFeedback(message, type) {
        const feedbackDiv = document.getElementById('feedback');
        if (!feedbackDiv) {
            console.error('Feedback div not found');
            return;
        }

        feedbackDiv.innerHTML = `
            <div class="feedback ${type}">
                <div class="feedback-content">
                    ${message}
                </div>
                ${type === 'correct' ? `
                    <div class="score-earned">
                        <i class="fas fa-coins"></i> +${this.getCurrentScore()} points earned!
                    </div>
                ` : ''}
            </div>
        `;
    }

    showError(message) {
        this.showFeedback(`❌ Error: ${message}`, 'error');
    }

    getCurrentScore() {
        return this.userScore;
    }

    setupEventListeners() {
        // Audio playback for vocabulary items
        document.addEventListener('click', (e) => {
            if (e.target.closest('.audio-btn')) {
                const vocabularyItem = e.target.closest('.vocabulary-item');
                if (vocabularyItem) {
                    const translation = vocabularyItem.querySelector('.translation');
                    if (translation) {
                        this.playAudio(translation.textContent);
                    }
                }
            }
        });

        // Enter key support for text inputs
        document.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && e.target.classList.contains('phrase-input')) {
                const checkBtn = document.querySelector('.submit-btn');
                if (checkBtn) checkBtn.click();
            }
        });
    }

    playAudio(text) {
        // Simple console log for now - in real app, implement text-to-speech
        console.log('Audio playback requested for:', text);
        
        // Example of how you might implement TTS:
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = this.getLanguageCode(language);
            speechSynthesis.speak(utterance);
        }
    }

    getLanguageCode(lang) {
        const codes = {
            'French': 'fr-FR',
            'Spanish': 'es-ES',
            'German': 'de-DE',
            'Italian': 'it-IT',
            'English': 'en-US'
        };
        return codes[lang] || 'en-US';
    }
}

// Safe initialization
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Check if required variables are defined
        if (typeof lessonData !== 'undefined' && typeof language !== 'undefined' && typeof topic !== 'undefined') {
            window.realLessonManager = new RealLessonManager();
            console.log('Lesson manager started successfully');
        } else {
            console.error('Required variables not defined. Please refresh the page.');
            document.getElementById('exerciseContent').innerHTML = `
                <div class="error-message">
                    <p>Unable to load exercise. Please refresh the page or contact support.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Failed to initialize lesson manager:', error);
        document.getElementById('exerciseContent').innerHTML = `
            <div class="error-message">
                <p>Error loading exercise content. Please try again later.</p>
                <button onclick="location.reload()" class="nav-btn primary">Reload Page</button>
            </div>
        `;
    }
});

// Add CSS for error states
const errorStyles = `
    .error-message {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        color: #dc2626;
    }
    
    .feedback.error {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = errorStyles;
document.head.appendChild(styleSheet);