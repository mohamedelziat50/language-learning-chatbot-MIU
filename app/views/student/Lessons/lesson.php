<?php
session_start();
require_once __DIR__ . '../../../../models/TopicModel.php';
require_once __DIR__ . '../../../../models/LanguageModel.php';
require_once __DIR__ . '../../../../models/LessonModel.php';

function getRealLessonContent($language, $topicName) {
    // Get language ID
    $languages = Language::getAll();
    $languageId = null;
    foreach ($languages as $lang) {
        if ($lang->getName() === $language) {
            $languageId = $lang->getId();
            break;
        }
    }
    
    if (!$languageId) return getDefaultContent($topicName);
    
    // Get topic ID
    $topics = Topic::getAll();
    $topicId = null;
    foreach ($topics as $topic) {
        if ($topic->getTitle() === $topicName && $topic->getLanguageId() === $languageId) {
            $topicId = $topic->getId();
            break;
        }
    }
    
    if (!$topicId) return getDefaultContent($topicName);
    
    // Return empty lesson data (no Lesson model yet)
    $lessonData = [
        'vocabulary' => [],
        'phrases' => [],
        'grammar' => [
            "point" => "Grammar Basics",
            "explanation" => "Coming soon",
            "examples" => []
        ],
        'conversation' => [],
        'practice' => []
    ];
    
    return $lessonData;
}

function getDefaultContent($topicName) {
    return [
        "vocabulary" => [],
        "phrases" => [],
        "grammar" => [
            "point" => "Content Not Available",
            "explanation" => "Lesson content for '{$topicName}' is being developed.",
            "examples" => []
        ],
        "conversation" => [],
        "practice" => []
    ];
}

function initializeUserProgress($language, $topic) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_progress'])) {
        $_SESSION['user_progress'] = [];
    }
    if (!isset($_SESSION['user_progress'][$language])) {
        $_SESSION['user_progress'][$language] = [];
    }
    if (!isset($_SESSION['user_progress'][$language][$topic])) {
        $_SESSION['user_progress'][$language][$topic] = [
            'completed_lessons' => [],
            'score' => 0,
            'words_learned' => 0,
            'last_accessed' => date('Y-m-d H:i:s')
        ];
    }
    
    $_SESSION['user_progress'][$language][$topic]['last_accessed'] = date('Y-m-d H:i:s');
}

function updateProgress($language, $topic, $lessonType, $score = 0) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_progress'][$language][$topic]['completed_lessons'])) {
        $_SESSION['user_progress'][$language][$topic]['completed_lessons'] = [];
    }

    if (!in_array($lessonType, $_SESSION['user_progress'][$language][$topic]['completed_lessons'])) {
        $_SESSION['user_progress'][$language][$topic]['completed_lessons'][] = $lessonType;
        $_SESSION['user_progress'][$language][$topic]['score'] += $score;
        
        $content = getRealLessonContent($language, $topic);
        $vocabularyCount = count($content['vocabulary'] ?? []);
        $_SESSION['user_progress'][$language][$topic]['words_learned'] = $vocabularyCount;
        
        $_SESSION['user_progress'][$language][$topic]['last_accessed'] = date('Y-m-d H:i:s');
    }
}

function getProgressStats($language, $topic) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $progress = $_SESSION['user_progress'][$language][$topic] ?? [
        'completed_lessons' => [],
        'score' => 0,
        'words_learned' => 0
    ];
    
    $completed = count($progress['completed_lessons'] ?? []);
    $totalLessons = 5;
    
    return [
        'completed' => $completed,
        'total' => $totalLessons,
        'percentage' => round(($completed / $totalLessons) * 100),
        'score' => $progress['score'] ?? 0,
        'words_learned' => $progress['words_learned'] ?? 0
    ];
}

function getLessonStructure($language, $topicName) {
    $baseStructure = [
        "vocabulary" => [
            "title" => "Essential Vocabulary", 
            "icon" => "fas fa-book", 
            "description" => "Learn key words and phrases for " . $topicName
        ],
        "phrases" => [
            "title" => "Common Phrases", 
            "icon" => "fas fa-comment", 
            "description" => "Useful expressions and sentences for " . $topicName
        ],
        "grammar" => [
            "title" => "Grammar Basics", 
            "icon" => "fas fa-language", 
            "description" => "Important grammar rules for " . $topicName
        ],
        "conversation" => [
            "title" => "Conversation Practice", 
            "icon" => "fas fa-users", 
            "description" => "Real-life dialogue examples for " . $topicName
        ],
        "practice" => [
            "title" => "Practice Exercise", 
            "icon" => "fas fa-pencil-alt", 
            "description" => "Test your knowledge of " . $topicName
        ]
    ];

    $content = getRealLessonContent($language, $topicName);
    $lessons = [];
    
    foreach ($baseStructure as $type => $meta) {
        $lessons[] = array_merge($meta, [
            "id" => count($lessons) + 1, 
            "type" => $type, 
            "content" => $content[$type] ?? []
        ]);
    }
    
    return $lessons;
}

function getAvailableLanguages() {
    return Language::getAll();
}

function getAvailableTopics($language) {
    $languages = Language::getAll();
    $languageId = null;
    
    foreach ($languages as $lang) {
        if ($lang->getName() === $language) {
            $languageId = $lang->getId();
            break;
        }
    }
    
    if (!$languageId) return [];
    
    return Topic::getByLanguage($languageId);
}

function submitPractice($language, $topic, array $answers = [], $csrfToken = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($csrfToken !== null) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$csrfToken)) {
            return ['success' => false, 'score' => 0, 'message' => 'Invalid CSRF token', 'progress' => getProgressStats($language, $topic)];
        }
    }

    $vocab  = isset($answers['vocab']) ? trim((string)$answers['vocab']) : '';
    $phrase = isset($answers['phrase']) ? trim((string)$answers['phrase']) : '';
    $grammar = isset($answers['grammar']) ? trim((string)$answers['grammar']) : '';

    $vocabOk   = mb_strlen($vocab) > 0;
    $phraseOk  = mb_strlen($phrase) >= 7;
    $grammarOk = mb_strlen($grammar) >= 9;

    $validCount = ($vocabOk ? 1 : 0) + ($phraseOk ? 1 : 0) + ($grammarOk ? 1 : 0);

    if ($validCount >= 2) {
        if (!isset($_SESSION['user_progress'][$language][$topic]['completed_lessons'])
            || !in_array('practice', $_SESSION['user_progress'][$language][$topic]['completed_lessons'], true)) {

            updateProgress($language, $topic, 'practice', 10);
            $progress = getProgressStats($language, $topic);
            return [
                'success' => true,
                'score' => 10,
                'message' => 'Practice completed — +10 points awarded.',
                'progress' => $progress
            ];
        }

        return [
            'success' => true,
            'score' => 0,
            'message' => 'Practice already completed previously (no additional points).',
            'progress' => getProgressStats($language, $topic)
        ];
    }

    return [
        'success' => false,
        'score' => 0,
        'message' => 'Please complete at least two of the three tasks with meaningful answers.',
        'progress' => getProgressStats($language, $topic)
    ];
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Input Validation and Sanitization
$lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING) ?: 'English';
$topic = filter_input(INPUT_GET, 'topic', FILTER_SANITIZE_STRING) ?: 'Greetings';

// Initialize Progress
initializeUserProgress($lang, $topic);

// Handle AJAX Progress Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_progress') {
    handleProgressUpdate();
    exit;
}

// Handle Lesson Completion (Legacy GET support)
if (isset($_GET['complete']) && $_GET['complete'] === 'true') {
    $lessonType = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
    $score = (int)filter_input(INPUT_GET, 'score', FILTER_VALIDATE_INT, ['options' => ['default' => 10]]);
    updateProgress($lang, $topic, $lessonType, $score);
}

// Load Lesson Data
$lessons = getLessonStructure($lang, $topic);
$currentLessonIndex = isset($_GET['lesson']) ? max(0, min((int)$_GET['lesson'], count($lessons) - 1)) : 0;
$currentLesson = $lessons[$currentLessonIndex] ?? null;

if (!$currentLesson) {
    header("Location: /language-learning-chatbot-MIU/app/views/student/Topics/Topics.php?lang=" . urlencode($lang));
    exit;
}

$progressStats = getProgressStats($lang, $topic);
$progressPercent = ($currentLessonIndex + 1) / count($lessons) * 100;

// Prepare Data for JS
$lessonData = json_encode($currentLesson['content'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

/**
 * Handle AJAX progress updates with CSRF protection
 */
function handleProgressUpdate() {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }
    
    $lang = filter_input(INPUT_POST, 'lang', FILTER_SANITIZE_STRING) ?: 'English';
    $topic = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_STRING) ?: 'Greetings';
    $lessonType = filter_input(INPUT_POST, 'lesson_type', FILTER_SANITIZE_STRING);
    $score = (int)filter_input(INPUT_POST, 'score', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
    
    updateProgress($lang, $topic, $lessonType, $score);
    echo json_encode(['success' => true, 'progress' => getProgressStats($lang, $topic)]);
}

/**
 * Render lesson content based on type
 */
function renderLessonContent($lesson) {
    $type = $lesson['type'];
    $content = $lesson['content'] ?? [];

    switch ($type) {
        case 'vocabulary':
            renderVocabularyContent($content);
            break;
        case 'phrases':
            renderPhrasesContent($content);
            break;
        case 'grammar':
            renderGrammarContent($content);
            break;
        case 'conversation':
            renderConversationContent($content);
            break;
        case 'practice':
            renderPracticeContent();
            break;
        default:
            renderDefaultContent();
    }
}

function renderVocabularyContent($content) {
    if (empty($content)) {
        echo '<div class="no-content">No vocabulary data available</div>';
        return;
    }
    
    echo '<div class="vocabulary-grid">';
    foreach ($content as $item) {
        echo '<div class="vocabulary-item">';
        echo '<div class="word">' . htmlspecialchars($item['word'] ?? '') . '</div>';
        echo '<div class="translation">' . htmlspecialchars($item['translation'] ?? '') . '</div>';
        echo '<div class="pronunciation">' . htmlspecialchars($item['pronunciation'] ?? '') . '</div>';
        echo '<button class="audio-btn" onclick="playAudio(\'' . htmlspecialchars($item['translation'] ?? '') . '\')"><i class="fas fa-volume-up"></i></button>';
        echo '</div>';
    }
    echo '</div>';
}

function renderPhrasesContent($content) {
    if (empty($content)) {
        echo '<div class="no-content">No phrases data available</div>';
        return;
    }
    
    echo '<div class="phrases-list">';
    foreach ($content as $key => $phrase) {
        echo '<div class="phrase-item">';
        echo '<div class="phrase-key">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) . ':</div>';
        echo '<div class="phrase-text">' . htmlspecialchars($phrase) . '</div>';
        echo '</div>';
    }
    echo '</div>';
}

function renderGrammarContent($content) {
    if (empty($content)) {
        echo '<div class="no-content">No grammar data available</div>';
        return;
    }
    
    echo '<div class="grammar-content">';
    echo '<div class="grammar-point"><h3>' . htmlspecialchars($content['point'] ?? '') . '</h3><p>' . htmlspecialchars($content['explanation'] ?? '') . '</p></div>';
    if (!empty($content['examples'])) {
        echo '<div class="examples"><h4>Examples:</h4>';
        foreach ($content['examples'] as $example) {
            echo '<div class="example">' . htmlspecialchars($example) . '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
}

function renderConversationContent($content) {
    if (empty($content)) {
        echo '<div class="no-content">No conversation data available</div>';
        return;
    }
    
    echo '<div class="conversation-dialogue">';
    foreach ($content as $line) {
        echo '<div class="dialogue-line">';
        echo '<span class="speaker">' . htmlspecialchars($line['speaker'] ?? '') . ':</span>';
        echo '<span class="text">' . htmlspecialchars($line['text'] ?? '') . '</span>';
        echo '<span class="translation">(' . htmlspecialchars($line['translation'] ?? '') . ')</span>';
        echo '</div>';
    }
    echo '</div>';
}

function renderPracticeContent() {
    echo '<div class="practice-intro"><p>Test your knowledge with interactive exercises!</p></div>';
}

function renderDefaultContent() {
    echo '<div class="default-content"><p>Lesson content is being prepared.</p></div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars("$topic - $lang | LinguaLearn"); ?></title>
    <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/Lessons/lesson.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/language-learning-chatbot-MIU/app/views/student/Languages/language.php" class="logo">
                <i class="fas fa-globe-americas"></i> LinguaLearn
            </a>
            <ul class="nav-menu">
                <li><a href="/language-learning-chatbot-MIU/app/views/student/dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="/language-learning-chatbot-MIU/app/views/student/Topics/Topics.php?lang=<?php echo urlencode($lang); ?>" class="nav-link"><i class="fas fa-arrow-left"></i> Back to Topics</a></li>
            </ul>
        </div>
    </nav>

    <div class="lesson-container">
        <div class="lesson-header">
            <h1><?php echo htmlspecialchars($topic); ?></h1>
            <p class="language-badge"><?php echo htmlspecialchars($lang); ?></p>
        </div>

        <div class="lesson-progress">
            <span>Lesson <?php echo $currentLessonIndex + 1; ?> of <?php echo count($lessons); ?></span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $progressPercent; ?>%"></div>
            </div>
            <span><?php echo htmlspecialchars($currentLesson['title']); ?></span>
        </div>

        <div class="lesson-content">
            <div class="lesson-card <?php echo htmlspecialchars($currentLesson['type']); ?>">
                <h2><i class="<?php echo htmlspecialchars($currentLesson['icon']); ?>"></i> <?php echo htmlspecialchars($currentLesson['title']); ?></h2>
                <p class="lesson-description"><?php echo htmlspecialchars($currentLesson['description']); ?></p>
                
                <?php renderLessonContent($currentLesson); ?>
            </div>

            <div class="practice-section" id="practiceSection">
                <h3><i class="fas fa-pencil-alt"></i> Practice Exercise</h3>
                <div id="exerciseContent" aria-live="polite">
                    <div class="loading-spinner">Loading exercise...</div>
                </div>
            </div>

            <div class="lesson-nav">
                <?php if ($currentLessonIndex > 0): ?>
                    <a href="?lang=<?php echo urlencode($lang); ?>&topic=<?php echo urlencode($topic); ?>&lesson=<?php echo $currentLessonIndex - 1; ?>" class="nav-btn">
                        <i class="fas fa-arrow-left"></i> Previous
                    </a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>
                <?php if ($currentLessonIndex < count($lessons) - 1): ?>
                    <a href="?lang=<?php echo urlencode($lang); ?>&topic=<?php echo urlencode($topic); ?>&lesson=<?php echo $currentLessonIndex + 1; ?>" class="nav-btn primary" id="nextBtn">
                        Next Lesson <i class="fas fa-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="/language-learning-chatbot-MIU/app/views/student/Topics/Topics.php?lang=<?php echo urlencode($lang); ?>" class="nav-btn success">
                        Complete Topic <i class="fas fa-check"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const CONFIG = {
            language: "<?php echo addslashes($lang); ?>",
            topic: "<?php echo addslashes($topic); ?>",
            lessonType: "<?php echo addslashes($currentLesson['type'] ?? ''); ?>",
            lessonData: <?php echo $lessonData; ?>,
            csrfToken: "<?php echo $_SESSION['csrf_token']; ?>"
        };
    </script>
    <script src="/language-learning-chatbot-MIU/public/js/Lessons/lesson.js"></script>
</body>
</html>