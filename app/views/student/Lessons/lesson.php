<?php
session_start();
$lang = $_GET['lang'] ?? 'English';
$topic = $_GET['topic'] ?? 'Greetings';

// Initialize progress tracking
if (!isset($_SESSION['user_progress'])) {
    $_SESSION['user_progress'] = [];
}

if (!isset($_SESSION['user_progress'][$lang])) {
    $_SESSION['user_progress'][$lang] = [];
}

if (!isset($_SESSION['user_progress'][$lang][$topic])) {
    $_SESSION['user_progress'][$lang][$topic] = [
        'completed_lessons' => [],
        'score' => 0,
        'words_learned' => 0,
        'last_accessed' => date('Y-m-d H:i:s')
    ];
}

// Load lesson data from JSON
function loadLessonData($language, $topicName) {
    $jsonPath = __DIR__ . '../Lessons/lesson_data.json';
    if (!file_exists($jsonPath)) {
        return null;
    }
    
    $jsonData = file_get_contents($jsonPath);
    $allData = json_decode($jsonData, true);
    
    if (isset($allData[$language][$topicName])) {
        return $allData[$language][$topicName];
    }
    
    return null;
}

// Get lesson content from JSON
function getRealLessonContent($language, $topicName) {
    $lessonData = loadLessonData($language, $topicName);
    
    if ($lessonData) {
        return $lessonData;
    }
    
    // Fallback for missing content
        return [
            "vocabulary" => [],
            "phrases" => [],
            "grammar" => [
                "point" => "Content Not Available",
                "explanation" => "Lesson content for '{$topicName}' is being developed.",
                "examples" => []
            ],
            "conversation" => []
        ];
}


// Progress tracking functions
function updateProgress($language, $topic, $lessonType, $score = 0) {
    if (!isset($_SESSION['user_progress'][$language][$topic]['completed_lessons'])) {
        $_SESSION['user_progress'][$language][$topic]['completed_lessons'] = [];
    }
    
    if (!in_array($lessonType, $_SESSION['user_progress'][$language][$topic]['completed_lessons'])) {
        $_SESSION['user_progress'][$language][$topic]['completed_lessons'][] = $lessonType;
        $_SESSION['user_progress'][$language][$topic]['score'] += $score;
        $_SESSION['user_progress'][$language][$topic]['words_learned'] = count(getRealLessonContent($language, $topic)['vocabulary']);
        $_SESSION['user_progress'][$language][$topic]['last_accessed'] = date('Y-m-d H:i:s');
    }
}

function getProgressStats($language, $topic) {
    if (!isset($_SESSION['user_progress'][$language][$topic])) {
        return [
            'completed' => 0,
            'total' => 5,
            'percentage' => 0,
            'score' => 0,
            'words_learned' => 0
        ];
    }
    
    $progress = $_SESSION['user_progress'][$language][$topic];
    $completed = count($progress['completed_lessons'] ?? []);
    $percentage = ($completed / 5) * 100;
    
    return [
        'completed' => $completed,
        'total' => 5,
        'percentage' => $percentage,
        'score' => $progress['score'] ?? 0,
        'words_learned' => $progress['words_learned'] ?? 0
    ];
}

// Handle lesson completion
if (isset($_GET['complete']) && $_GET['complete'] === 'true') {
    $lessonType = $_GET['type'] ?? '';
    $score = $_GET['score'] ?? 10;
    updateProgress($lang, $topic, $lessonType, $score);
}

// Get lesson structure
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
    $lessonTypes = array_keys($baseStructure);
    
    foreach ($lessonTypes as $index => $type) {
        $lessons[] = [
            "id" => $index + 1,
            "type" => $type,
            "title" => $baseStructure[$type]["title"],
            "icon" => $baseStructure[$type]["icon"],
            "description" => $baseStructure[$type]["description"],
            "content" => $content[$type] ?? []
        ];
    }
    
    return $lessons;
}

$lessons = getLessonStructure($lang, $topic);
$totalLessons = count($lessons);
$currentLessonIndex = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 0;
$currentLesson = $lessons[$currentLessonIndex] ?? $lessons[0];

// Get progress stats
$progressStats = getProgressStats($lang, $topic);

if (!$currentLesson) {
    header("Location: ../Topics/Topics.php?lang=" . urlencode($lang));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learn <?php echo htmlspecialchars($topic); ?> - <?php echo htmlspecialchars($lang); ?> | LinguaLearn</title>
    <link rel="stylesheet" href="../../../../public/css/Lessons/lesson.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../Languages/language.php" class="logo">
                <i class="fas fa-globe-americas"></i>
                LinguaLearn
            </a>
            <ul class="nav-menu">
                <li><a href="../dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="../Topics/Topics.php?lang=<?php echo urlencode($lang); ?>" class="nav-link"><i class="fas fa-arrow-left"></i> Back to Topics</a></li>
            </ul>
        </div>
    </nav>

    <div class="lesson-container">
        <div class="lesson-header">
            <h1>Learn <?php echo htmlspecialchars($topic); ?></h1>
            <p class="language-badge"><?php echo htmlspecialchars($lang); ?></p>
        </div>

        <!-- Progress Bar -->
        <div class="lesson-progress">
            <span>Lesson <?php echo $currentLessonIndex + 1; ?> of <?php echo $totalLessons; ?></span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo (($currentLessonIndex + 1) / $totalLessons) * 100; ?>%"></div>
            </div>
            <span><?php echo $currentLesson['title']; ?></span>
        </div>

        <!-- Lesson Content -->
        <div class="lesson-content">
            <div class="lesson-card <?php echo $currentLesson['type']; ?>">
                <h2><i class="<?php echo $currentLesson['icon']; ?>"></i> <?php echo $currentLesson['title']; ?></h2>
                <p class="lesson-description"><?php echo $currentLesson['description']; ?></p>
                
                <?php if ($currentLesson['type'] === 'vocabulary' && !empty($currentLesson['content'])): ?>
                    <div class="vocabulary-grid">
                        <?php foreach ($currentLesson['content'] as $item): ?>
                            <div class="vocabulary-item">
                                <div class="word"><?php echo htmlspecialchars($item['word']); ?></div>
                                <div class="translation"><?php echo htmlspecialchars($item['translation']); ?></div>
                                <div class="pronunciation"><?php echo htmlspecialchars($item['pronunciation']); ?></div>
                                <button class="audio-btn" onclick="playAudio('<?php echo htmlspecialchars($item['translation']); ?>')">
                                    <i class="fas fa-volume-up"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'phrases' && !empty($currentLesson['content'])): ?>
                    <div class="phrases-list">
                        <?php foreach ($currentLesson['content'] as $key => $phrase): ?>
                            <div class="phrase-item">
                                <div class="phrase-key"><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</div>
                                <div class="phrase-text"><?php echo htmlspecialchars($phrase); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'grammar' && !empty($currentLesson['content'])): ?>
                    <div class="grammar-content">
                        <div class="grammar-point">
                            <h3><?php echo htmlspecialchars($currentLesson['content']['point']); ?></h3>
                            <p><?php echo htmlspecialchars($currentLesson['content']['explanation']); ?></p>
                        </div>
                        <?php if (!empty($currentLesson['content']['examples'])): ?>
                            <div class="examples">
                                <h4>Examples:</h4>
                                <?php foreach ($currentLesson['content']['examples'] as $example): ?>
                                    <div class="example"><?php echo htmlspecialchars($example); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'conversation' && !empty($currentLesson['content'])): ?>
                    <div class="conversation-dialogue">
                        <?php foreach ($currentLesson['content'] as $line): ?>
                            <div class="dialogue-line">
                                <span class="speaker"><?php echo htmlspecialchars($line['speaker']); ?>:</span>
                                <span class="text"><?php echo htmlspecialchars($line['text']); ?></span>
                                <span class="translation">(<?php echo htmlspecialchars($line['translation']); ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'practice'): ?>
                    <div class="practice-intro">
                        <p>Test your knowledge with interactive exercises!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Practice Section -->
            <div class="practice-section" id="practiceSection">
                <h3><i class="fas fa-pencil-alt"></i> Practice Exercise</h3>
                <div id="exerciseContent">
                    <!-- Exercise will be loaded here by JavaScript -->
                </div>
            </div>

            <!-- Navigation -->
            <div class="lesson-nav">
                <?php if ($currentLessonIndex > 0): ?>
                    <a href="?lang=<?php echo urlencode($lang); ?>&topic=<?php echo urlencode($topic); ?>&lesson=<?php echo $currentLessonIndex - 1; ?>" class="nav-btn">
                        <i class="fas fa-arrow-left"></i> Previous
                    </a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>

                <?php if ($currentLessonIndex < $totalLessons - 1): ?>
                    <a href="?lang=<?php echo urlencode($lang); ?>&topic=<?php echo urlencode($topic); ?>&lesson=<?php echo $currentLessonIndex + 1; ?>" class="nav-btn primary" id="nextBtn">
                        Next Lesson <i class="fas fa-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="../Topics/Topics.php?lang=<?php echo urlencode($lang); ?>" class="nav-btn success">
                        Complete Topic <i class="fas fa-check"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Simple data passing
        const language = "<?php echo addslashes($lang); ?>";
        const topic = "<?php echo addslashes($topic); ?>";
        const currentLessonType = "<?php echo addslashes($currentLesson['type'] ?? ''); ?>";
        
        console.log('Lesson Data:', {
            language: language,
            topic: topic,
            type: currentLessonType
        });
    </script>
    <script src="../../../../public/js/Lessons/lesson.js"></script>
</body>
</html>