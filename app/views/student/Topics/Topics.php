<?php
$lang = $_GET['lang'] ?? null;

// If no language selected → go back
if (!$lang) {
    header("Location: ../Languages/language.php");
    exit;
}

// Load topics data from JSON
$jsonPath = __DIR__ . '/../Lessons/lesson_data.json';
if (!file_exists($jsonPath)) {
    die("❌ Topics data file not found!");
}

$jsonData = file_get_contents($jsonPath);
$topicsData = json_decode($jsonData, true);

// Extract topics for this language from JSON
$languageTopics = $topicsData[$lang] ?? [];
$topics = [];

foreach ($languageTopics as $topicName => $topicContent) {
    $topics[] = [
        'name' => $topicName,
        'description' => getTopicDescription($topicName)
    ];
}

// Helper function to get topic descriptions
function getTopicDescription($topicName) {
    $descriptions = [
        'Greetings' => 'Learn how to greet people formally and informally',
        'Food & Dining' => 'Master restaurant vocabulary and ordering phrases',
        'Travel' => 'Essential phrases for airports, hotels, and transportation',
        'Family' => 'Talk about family members and relationships',
        'Shopping' => 'Navigate stores, prices, and purchases confidently',
        'Numbers' => 'Learn counting, prices, and basic mathematics',
        'Weather' => 'Discuss weather conditions and forecasts',
        'Daily Routine' => 'Describe your daily activities and schedule',
        'Basics' => 'Essential words and phrases for beginners',
        'Colors' => 'Learn colors and descriptive vocabulary',
        'Food & Drinks' => 'Food items, drinks, and meal-related vocabulary',
        'Transportation' => 'Public transport, directions, and travel',
        'Hobbies' => 'Talk about interests and free time activities',
        'Introductions' => 'Introduce yourself and others properly'
    ];
    
    return $descriptions[$topicName] ?? 'Learn essential vocabulary and phrases for this topic';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Topics - <?php echo htmlspecialchars($lang); ?> | LinguaLearn</title>
    <link rel="stylesheet" href="../../../../public/css/Topics/Topics.css">
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
                    <a href="../Languages/language.php" class="nav-link">
                        <i class="fas fa-language"></i> Languages <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-content">
                        <?php
                        foreach ($topicsData as $language => $list) {
                            $langUrl = urlencode($language);
                            echo "<a href='../Topics/Topics.php?lang=$langUrl'><i class='fas fa-flag'></i> $language</a>";
                        }
                        ?>
                    </div>
                </li>
                <li><a href="../dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="../Dictionary/dictionary.php" class="nav-link"><i class="fas fa-book"></i> Dictionary</a></li>
                <li><a href="../progress.php" class="nav-link"><i class="fas fa-chart-line"></i> My Progress</a></li>
                <li><a href="../profile.php" class="nav-link"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </div>
    </nav>

    <!-- 🧠 Page Content -->
    <div class="page-container">
        <div class="page-header">
            <div class="header-content">
                <h1>Learn <span class="language-highlight"><?php echo htmlspecialchars($lang); ?></span></h1>
                <p>Choose a topic to start your learning journey. Each topic contains vocabulary, phrases, and interactive lessons.</p>
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="topicSearch" placeholder="Search topics..." class="search-input">
                </div>
            </div>
        </div>

        <div class="topics-section">
            <div class="section-header">
                <h2>Available Topics</h2>
                <div class="topics-count">
                    <span id="topicsCount"><?php echo count($topics); ?></span> topics available
                </div>
            </div>

            <div class="topics-grid" id="topicsGrid">
                <?php if (!empty($topics)): ?>
                    <?php foreach ($topics as $topic): ?>
                        <?php
                            $tName   = htmlspecialchars($topic['name']);
                            $tDesc   = htmlspecialchars($topic['description']);
                            $langUrl = urlencode($lang);
                            $topicUrl = urlencode($topic['name']);
                            $topicIcon = getTopicIcon($tName);
                        ?>
                        <div class="topic-card"
                            data-topic="<?php echo $tName; ?>"
                            data-language="<?php echo htmlspecialchars($lang); ?>"
                            onclick="window.location.href='../Lessons/lesson.php?lang=<?php echo $langUrl; ?>&topic=<?php echo $topicUrl; ?>'">
                            
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
                        <a href="../Languages/language.php" class="btn-primary">Choose Another Language</a>
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

    <script src="../../../../public/js/Topics/Topics.js"></script>
</body>
</html>

<?php
// Helper function to get topic icons
function getTopicIcon($topicName) {
    $icons = [
        'Greetings' => 'fas fa-handshake',
        'Food & Dining' => 'fas fa-utensils',
        'Travel' => 'fas fa-plane',
        'Family' => 'fas fa-users',
        'Shopping' => 'fas fa-shopping-cart',
        'Numbers' => 'fas fa-sort-numeric-up',
        'Weather' => 'fas fa-cloud-sun',
        'Daily Routine' => 'fas fa-calendar-day',
        'Basics' => 'fas fa-star',
        'Colors' => 'fas fa-palette',
        'Food & Drinks' => 'fas fa-coffee',
        'Transportation' => 'fas fa-bus',
        'Hobbies' => 'fas fa-gamepad',
        'Introductions' => 'fas fa-user-plus'
    ];
    
    return $icons[$topicName] ?? 'fas fa-book';
}
?>