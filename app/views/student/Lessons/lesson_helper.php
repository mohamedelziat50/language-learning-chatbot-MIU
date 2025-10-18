<?php

define('LESSON_DATA_PATH', __DIR__ . '/lesson_data.json');
define('CACHE_EXPIRY', 3600); 
$lessonDataCache = null;
$cacheTimestamp = 0;

function loadLessonData($language, $topicName) {
    global $lessonDataCache, $cacheTimestamp;

    if (!is_string($language) || !is_string($topicName) || empty($language) || empty($topicName)) {
        error_log("Invalid input for loadLessonData: lang=$language, topic=$topicName");
        return null;
    }

    if ($lessonDataCache && (time() - $cacheTimestamp) < CACHE_EXPIRY) {
        return $lessonDataCache[$language][$topicName] ?? null;
    }

    if (!file_exists(LESSON_DATA_PATH)) {
        error_log("Lesson data file not found: " . LESSON_DATA_PATH);
        return null;
    }

    $jsonData = file_get_contents(LESSON_DATA_PATH);
    if ($jsonData === false) {
        error_log("Failed to read lesson data file: " . LESSON_DATA_PATH);
        return null;
    }

    $allData = json_decode($jsonData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return null;
    }

    $lessonDataCache = $allData;
    $cacheTimestamp = time();

    return $allData[$language][$topicName] ?? null;
}

function getRealLessonContent($language, $topicName) {
    $lessonData = loadLessonData($language, $topicName);
    
    if (!$lessonData) {
        error_log("No lesson data found for language: $language, topic: $topicName");
        return [
            "vocabulary" => [],
            "phrases" => [],
            "grammar" => [
                "point" => "Content Not Available", 
                "explanation" => "Lesson content for '{$topicName}' in {$language} is being developed.", 
                "examples" => []
            ],
            "conversation" => []
        ];
    }
    
    return $lessonData;
}

function initializeUserProgress($language, $topic) {
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
    if (!isset($_SESSION['user_progress'][$language][$topic]['completed_lessons'])) {
        $_SESSION['user_progress'][$language][$topic]['completed_lessons'] = [];
    }

    if (!in_array($lessonType, $_SESSION['user_progress'][$language][$topic]['completed_lessons'])) {
        $_SESSION['user_progress'][$language][$topic]['completed_lessons'][] = $lessonType;
        $_SESSION['user_progress'][$language][$topic]['score'] += $score;
        
        // Count actual words learned from vocabulary
        $content = getRealLessonContent($language, $topic);
        $vocabularyCount = count($content['vocabulary'] ?? []);
        $_SESSION['user_progress'][$language][$topic]['words_learned'] = $vocabularyCount;
        
        $_SESSION['user_progress'][$language][$topic]['last_accessed'] = date('Y-m-d H:i:s');
    }
}

function getProgressStats($language, $topic) {
    $progress = $_SESSION['user_progress'][$language][$topic] ?? [
        'completed_lessons' => [],
        'score' => 0,
        'words_learned' => 0
    ];
    
    $completed = count($progress['completed_lessons'] ?? []);
    $totalLessons = 5; // vocabulary, phrases, grammar, conversation, practice
    
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

/**
 * Get available languages from lesson data
 */
function getAvailableLanguages() {
    global $lessonDataCache, $cacheTimestamp;
    
    if (!$lessonDataCache || (time() - $cacheTimestamp) >= CACHE_EXPIRY) {
        loadLessonData('English', 'Greetings'); // This will refresh cache
    }
    
    return array_keys($lessonDataCache ?? []);
}

/**
 * Get available topics for a language
 */
function getAvailableTopics($language) {
    global $lessonDataCache, $cacheTimestamp;
    
    if (!$lessonDataCache || (time() - $cacheTimestamp) >= CACHE_EXPIRY) {
        loadLessonData($language, 'Greetings'); // This will refresh cache
    }
    
    return array_keys($lessonDataCache[$language] ?? []);
}

/**
 * Validate and process a practice submission.
 *
 * Expected $answers array keys:
 *   - 'vocab'    => string (translation)
 *   - 'phrase'   => string (sentence)
 *   - 'grammar'  => string (example)
 * Optionally pass $csrfToken to verify against $_SESSION['csrf_token'].
 *
 * Returns associative array:
 *   ['success' => bool, 'score' => int, 'message' => string, 'progress' => array]
 */
function submitPractice($language, $topic, array $answers = [], $csrfToken = null) {
    // Ensure session is available for CSRF and progress
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    // Optional CSRF check
    if ($csrfToken !== null) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$csrfToken)) {
            return ['success' => false, 'score' => 0, 'message' => 'Invalid CSRF token', 'progress' => getProgressStats($language, $topic)];
        }
    }

    // Basic sanitation
    $vocab  = isset($answers['vocab']) ? trim((string)$answers['vocab']) : '';
    $phrase = isset($answers['phrase']) ? trim((string)$answers['phrase']) : '';
    $grammar = isset($answers['grammar']) ? trim((string)$answers['grammar']) : '';

    // Simple validation rules (mirror client-side):
    $vocabOk   = mb_strlen($vocab) > 0;        // any non-empty translation
    $phraseOk  = mb_strlen($phrase) >= 7;      // sentence-like
    $grammarOk = mb_strlen($grammar) >= 9;     // example-like

    $validCount = ($vocabOk ? 1 : 0) + ($phraseOk ? 1 : 0) + ($grammarOk ? 1 : 0);

    // Require at least 2 of 3 tasks to be valid to pass
    if ($validCount >= 2) {
        // Award +10 only once per topic/practice
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

        // Already completed previously
        return [
            'success' => true,
            'score' => 0,
            'message' => 'Practice already completed previously (no additional points).',
            'progress' => getProgressStats($language, $topic)
        ];
    }

    // Not enough valid answers
    return [
        'success' => false,
        'score' => 0,
        'message' => 'Please complete at least two of the three tasks with meaningful answers.',
        'progress' => getProgressStats($language, $topic)
    ];
}
?>