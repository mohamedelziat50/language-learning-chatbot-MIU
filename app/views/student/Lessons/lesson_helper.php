<?php
// filepath: app/views/student/Lessons/lesson_helper.php

require_once __DIR__ . '/../../models/TopicModel.php';
require_once __DIR__ . '/../../models/LanguageModel.php';

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
?>