<?php
// Include helper functions for better organization
require_once '../Topics/helpers.php'; // Create this file with getTopicDescription() and getTopicIcon()

$lang = $_GET['lang'] ?? null;

// Validate language parameter (basic check against JSON keys)
if (!$lang) {
    header("Location: /language-learning-chatbot-MIU/app/views/student/Languages/language.php");
    exit;
}

// Load topics data from JSON (path adjusted to match schema: Lesson/)
$jsonPath = __DIR__ . '/../Lessons/lesson_data.json';
if (!file_exists($jsonPath)) {
    die("❌ Topics data file not found!");
}

$jsonData = file_get_contents($jsonPath);
$topicsData = json_decode($jsonData, true);

// Error handling for invalid JSON
if ($topicsData === null) {
    die("❌ Invalid JSON data!");
}

// Validate lang exists in JSON
if (!isset($topicsData[$lang])) {
    die("❌ Language not found in data!");
}

// Extract topics for this language from JSON
$languageTopics = $topicsData[$lang] ?? [];
$topics = [];

foreach ($languageTopics as $topicName => $topicContent) {
    // Pull description from JSON if available, else fallback to helper
    $description = $topicContent['description'] ?? getTopicDescription($topicName);
    $topics[] = [
        'name' => $topicName,
        'description' => $description
    ];
}

// Prepare data for HTML rendering
$pageTitle = htmlspecialchars($lang);
$topicsCount = count($topics);
$languageHighlight = htmlspecialchars($lang);
$langUrl = urlencode($lang);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Topics - <?php echo $pageTitle; ?> | LinguaLearn</title>
    <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/Topics/Topics.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- 🌍 Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-globe-americas"></i>
                <span>LinguaLearn</span>
            </div>

            <ul class="nav-menu">
                <li class="dropdown">
                    <a href="/language-learning-chatbot-MIU/app/views/student/Languages/language.php" class="nav-link">
                        <i class="fas fa-language"></i> Languages <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-content">
                        <?php
                        foreach ($topicsData as $language => $list) {
                            $langUrlDropdown = urlencode($language);
                            $languageEscaped = htmlspecialchars($language);
                            echo "<a href='/language-learning-chatbot-MIU/app/views/student/Topics/Topics.php?lang=$langUrlDropdown'><i class='fas fa-flag'></i> $languageEscaped</a>";
                        }
                        ?>
                    </div>
                </li>
                <li><a href="/language-learning-chatbot-MIU/app/views/student/dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="/language-learning-chatbot-MIU/app/views/student/Dictionary/dictionary.php" class="nav-link"><i class="fas fa-book"></i> Dictionary</a></li>
                <li><a href="/language-learning-chatbot-MIU/app/views/student/progress.php" class="nav-link"><i class="fas fa-chart-line"></i> My Progress</a></li>
                <li><a href="/language-learning-chatbot-MIU/app/views/student/profile.php" class="nav-link"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </div>
    </nav>

    <!-- 🧠 Page Content -->
    <div class="page-container">
        <div class="page-header">
            <div class="header-content">
                <h1>Learn <span class="language-highlight"><?php echo $languageHighlight; ?></span></h1>
                <p>Choose a topic to start your learning journey. Each topic contains vocabulary, phrases, and interactive lessons.</p>
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="topicSearch" placeholder="Search topics..." class="search-input" aria-label="Search topics">
                </div>
            </div>
        </div>

        <div class="topics-section">
            <div class="section-header">
                <h2>Available Topics</h2>
                <div class="topics-count">
                    <span id="topicsCount"><?php echo $topicsCount; ?></span> topics available
                </div>
            </div>

            <div class="topics-grid" id="topicsGrid">
                <?php if (!empty($topics)): ?>
                    <?php foreach ($topics as $topic): ?>
                        <?php
                            $tName = htmlspecialchars($topic['name']);
                            $tDesc = htmlspecialchars($topic['description']);
                            $topicUrl = urlencode($topic['name']);
                            $topicIcon = getTopicIcon($tName);
                        ?>
                        <div class="topic-card"
                            data-topic="<?php echo $tName; ?>"
                            data-language="<?php echo $languageHighlight; ?>"
                            onclick="window.location.href='../Lessons/lesson.php?lang=<?php echo $langUrl; ?>&topic=<?php echo $topicUrl; ?>'"
                            tabindex="0"
                            role="button"
                            aria-label="Start learning <?php echo $tName; ?>">
                            
                            <div class="card-icon">
                                <i class="<?php echo $topicIcon; ?>"></i>
                            </div>
                            <div class="card-content">
                                <h3><?php echo $tName; ?></h3>
                                <p><?php echo $tDesc; ?></p>
                                <div class="card-footer">
                                    <span class="start-learning">Start Learning</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                            <div class="card-hover">
                                <i class="fas fa-play-circle"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-topics-message">
                        <i class="fas fa-folder-open"></i>
                        <h3>No Topics Available</h3>
                        <p>We're working on adding topics for this language. Check back soon!</p>
                        <a href="/language-learning-chatbot-MIU/app/views/student/Languages/language.php" class="btn-primary">Choose Another Language</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 📚 Progress -->
    <div class="progress-section">
        <div class="progress-container">
            <h3>Your Learning Progress</h3>
            <div class="progress-stats">
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Topics Completed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Words Learned</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Practice Sessions</div>
                </div>
            </div>
        </div>
    </div>

    <script src="/language-learning-chatbot-MIU/public/js/Topics/Topics.js"></script>
</body>
</html>