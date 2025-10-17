<?php
$lang = $_GET['lang'] ?? null;

// If no language was selected, send back to selection page
if (!$lang) {
    header("Location: ../Languages/language.php");
    exit;
}

// 🌍 Define topics and their images (Expanded with more topics)
$topics = [
    "French" => [
        ["name" => "Greetings", "img" => "https://cdn-icons-png.flaticon.com/512/2922/2922688.png", "description" => "Learn basic French greetings and introductions"],
        ["name" => "Food & Dining", "img" => "https://cdn-icons-png.flaticon.com/512/3075/3075977.png", "description" => "French cuisine vocabulary and restaurant phrases"],
        ["name" => "Travel", "img" => "https://cdn-icons-png.flaticon.com/512/201/201623.png", "description" => "Essential travel phrases and directions"],
        ["name" => "Numbers", "img" => "https://cdn-icons-png.flaticon.com/512/4144/4144510.png", "description" => "Count, tell time, and use numbers"],
        ["name" => "Family", "img" => "https://cdn-icons-png.flaticon.com/512/3048/3048122.png", "description" => "Family members and relationships"],
        ["name" => "Shopping", "img" => "https://cdn-icons-png.flaticon.com/512/1170/1170678.png", "description" => "Shopping vocabulary and transactions"],
        ["name" => "Weather", "img" => "https://cdn-icons-png.flaticon.com/512/1163/1163628.png", "description" => "Weather terms and seasonal vocabulary"],
        ["name" => "Daily Routine", "img" => "https://cdn-icons-png.flaticon.com/512/2997/2997898.png", "description" => "Everyday activities and schedule"]
    ],
    "Spanish" => [
        ["name" => "Basics", "img" => "https://cdn-icons-png.flaticon.com/512/5522/5522310.png", "description" => "Fundamental Spanish phrases"],
        ["name" => "Colors", "img" => "https://cdn-icons-png.flaticon.com/512/1163/1163624.png", "description" => "Color vocabulary and descriptions"],
        ["name" => "Animals", "img" => "https://cdn-icons-png.flaticon.com/512/616/616408.png", "description" => "Animal names and nature terms"],
        ["name" => "Clothing", "img" => "https://cdn-icons-png.flaticon.com/512/892/892458.png", "description" => "Clothing items and fashion vocabulary"],
        ["name" => "Common Phrases", "img" => "https://cdn-icons-png.flaticon.com/512/3159/3159310.png", "description" => "Useful everyday expressions"],
        ["name" => "Food & Drinks", "img" => "https://cdn-icons-png.flaticon.com/512/1046/1046784.png", "description" => "Spanish cuisine and beverages"],
        ["name" => "Transportation", "img" => "https://cdn-icons-png.flaticon.com/512/744/744465.png", "description" => "Transport vocabulary and directions"],
        ["name" => "Hobbies", "img" => "https://cdn-icons-png.flaticon.com/512/553/553416.png", "description" => "Leisure activities and interests"]
    ],
    "German" => [
        ["name" => "Introductions", "img" => "https://cdn-icons-png.flaticon.com/512/3135/3135715.png", "description" => "Formal and informal introductions"],
        ["name" => "Work & Business", "img" => "https://cdn-icons-png.flaticon.com/512/942/942748.png", "description" => "Professional vocabulary"],
        ["name" => "Hobbies", "img" => "https://cdn-icons-png.flaticon.com/512/553/553416.png", "description" => "Leisure activities and sports"],
        ["name" => "Weather", "img" => "https://cdn-icons-png.flaticon.com/512/1163/1163628.png", "description" => "Weather expressions and seasons"],
        ["name" => "Directions", "img" => "https://cdn-icons-png.flaticon.com/512/854/854878.png", "description" => "Asking for and giving directions"],
        ["name" => "Shopping", "img" => "https://cdn-icons-png.flaticon.com/512/1170/1170678.png", "description" => "Retail and market vocabulary"],
        ["name" => "Food & Dining", "img" => "https://cdn-icons-png.flaticon.com/512/1046/1046784.png", "description" => "German cuisine and meals"],
        ["name" => "Family & Relationships", "img" => "https://cdn-icons-png.flaticon.com/512/1076/1076928.png", "description" => "Family terms and social connections"]
    ],
    "Italian" => [
        ["name" => "Introductions", "img" => "https://cdn-icons-png.flaticon.com/512/3135/3135715.png", "description" => "Basic greetings and introductions"],
        ["name" => "Work", "img" => "https://cdn-icons-png.flaticon.com/512/942/942748.png", "description" => "Professional and work vocabulary"],
        ["name" => "Weather", "img" => "https://cdn-icons-png.flaticon.com/512/1163/1163628.png", "description" => "Climate and weather expressions"],
        ["name" => "Culture & Traditions", "img" => "https://cdn-icons-png.flaticon.com/512/857/857681.png", "description" => "Italian culture and customs"],
        ["name" => "Directions", "img" => "https://cdn-icons-png.flaticon.com/512/854/854878.png", "description" => "Navigation and location phrases"],
        ["name" => "Food & Cuisine", "img" => "https://cdn-icons-png.flaticon.com/512/3075/3075977.png", "description" => "Italian food and cooking terms"],
        ["name" => "Art & History", "img" => "https://cdn-icons-png.flaticon.com/512/1046/1046784.png", "description" => "Art, history, and cultural heritage"],
        ["name" => "Travel", "img" => "https://cdn-icons-png.flaticon.com/512/201/201623.png", "description" => "Travel phrases and tourism vocabulary"]
    ],
    "English" => [
        ["name" => "Work & Career", "img" => "https://cdn-icons-png.flaticon.com/512/942/942748.png", "description" => "Professional English for work"],
        ["name" => "Hobbies", "img" => "https://cdn-icons-png.flaticon.com/512/553/553416.png", "description" => "Leisure activities and interests"],
        ["name" => "Weather", "img" => "https://cdn-icons-png.flaticon.com/512/1163/1163628.png", "description" => "Weather vocabulary and small talk"],
        ["name" => "Greetings", "img" => "https://cdn-icons-png.flaticon.com/512/2922/2922688.png", "description" => "Formal and informal greetings"],        
        ["name" => "Directions", "img" => "https://cdn-icons-png.flaticon.com/512/854/854878.png", "description" => "Asking for and giving directions"],
        ["name" => "Business English", "img" => "https://cdn-icons-png.flaticon.com/512/3135/3135715.png", "description" => "Professional communication"],
        ["name" => "Social Situations", "img" => "https://cdn-icons-png.flaticon.com/512/1076/1076928.png", "description" => "Conversations and social English"],
        ["name" => "Academic English", "img" => "https://cdn-icons-png.flaticon.com/512/2997/2997898.png", "description" => "Study and academic vocabulary"]
    ],
    "Arabic" => [
        ["name" => "Alphabet", "img" => "https://cdn-icons-png.flaticon.com/512/1250/1250683.png", "description" => "Arabic script and pronunciation"],
        ["name" => "Greetings", "img" => "https://cdn-icons-png.flaticon.com/512/2922/2922688.png", "description" => "Traditional Arabic greetings"],
        ["name" => "Traditions", "img" => "https://cdn-icons-png.flaticon.com/512/2913/2913202.png", "description" => "Cultural traditions and customs"],
        ["name" => "Religion & Culture", "img" => "https://cdn-icons-png.flaticon.com/512/857/857681.png", "description" => "Religious and cultural terms"],
        ["name" => "Cuisine", "img" => "https://cdn-icons-png.flaticon.com/512/3075/3075977.png", "description" => "Arabic food and dining vocabulary"],
        ["name" => "Numbers & Time", "img" => "https://cdn-icons-png.flaticon.com/512/4144/4144510.png", "description" => "Counting and time expressions"],
        ["name" => "Family & Society", "img" => "https://cdn-icons-png.flaticon.com/512/3048/3048122.png", "description" => "Family relationships and social terms"],
        ["name" => "Business Arabic", "img" => "https://cdn-icons-png.flaticon.com/512/3135/3135715.png", "description" => "Professional and business vocabulary"]
    ],
    "Japanese" => [
        ["name" => "Hiragana", "img" => "https://cdn-icons-png.flaticon.com/512/1836/1836878.png", "description" => "Learn Hiragana characters"],
        ["name" => "Katakana", "img" => "https://cdn-icons-png.flaticon.com/512/1836/1836889.png", "description" => "Learn Katakana characters"],
        ["name" => "Greetings", "img" => "https://cdn-icons-png.flaticon.com/512/2922/2922688.png", "description" => "Japanese greetings and etiquette"],
        ["name" => "Pop Culture", "img" => "https://cdn-icons-png.flaticon.com/512/3159/3159310.png", "description" => "Anime, manga, and modern culture"],
        ["name" => "Travel", "img" => "https://cdn-icons-png.flaticon.com/512/201/201623.png", "description" => "Travel phrases and navigation"],
        ["name" => "Food & Dining", "img" => "https://cdn-icons-png.flaticon.com/512/3075/3075977.png", "description" => "Japanese cuisine and restaurant phrases"],
        ["name" => "Daily Life", "img" => "https://cdn-icons-png.flaticon.com/512/2997/2997898.png", "description" => "Everyday conversations and situations"],
        ["name" => "Business Japanese", "img" => "https://cdn-icons-png.flaticon.com/512/942/942748.png", "description" => "Formal and business communication"]
    ],
    "Chinese" => [
        ["name" => "Pinyin & Tones", "img" => "https://cdn-icons-png.flaticon.com/512/1250/1250683.png", "description" => "Pronunciation and tone system"],
        ["name" => "Basic Characters", "img" => "https://cdn-icons-png.flaticon.com/512/1836/1836878.png", "description" => "Fundamental Chinese characters"],
        ["name" => "Greetings", "img" => "https://cdn-icons-png.flaticon.com/512/2922/2922688.png", "description" => "Chinese greetings and etiquette"],
        ["name" => "Food Culture", "img" => "https://cdn-icons-png.flaticon.com/512/3075/3075977.png", "description" => "Chinese cuisine and dining customs"],
        ["name" => "Numbers", "img" => "https://cdn-icons-png.flaticon.com/512/4144/4144510.png", "description" => "Counting and numerical expressions"],
        ["name" => "Family Terms", "img" => "https://cdn-icons-png.flaticon.com/512/3048/3048122.png", "description" => "Family relationships and hierarchy"],
        ["name" => "Travel Phrases", "img" => "https://cdn-icons-png.flaticon.com/512/201/201623.png", "description" => "Essential travel vocabulary"],
        ["name" => "Business Chinese", "img" => "https://cdn-icons-png.flaticon.com/512/3135/3135715.png", "description" => "Professional communication"]
    ],
    "Portuguese" => [
        ["name" => "Greetings", "img" => "https://cdn-icons-png.flaticon.com/512/2922/2922688.png", "description" => "Portuguese greetings and introductions"],
        ["name" => "Food & Cuisine", "img" => "https://cdn-icons-png.flaticon.com/512/3075/3075977.png", "description" => "Portuguese and Brazilian cuisine"],
        ["name" => "Travel", "img" => "https://cdn-icons-png.flaticon.com/512/201/201623.png", "description" => "Travel and navigation phrases"],
        ["name" => "Family", "img" => "https://cdn-icons-png.flaticon.com/512/3048/3048122.png", "description" => "Family terms and relationships"],
        ["name" => "Culture", "img" => "https://cdn-icons-png.flaticon.com/512/857/857681.png", "description" => "Cultural traditions and customs"],
        ["name" => "Business", "img" => "https://cdn-icons-png.flaticon.com/512/942/942748.png", "description" => "Professional Portuguese vocabulary"],
        ["name" => "Daily Life", "img" => "https://cdn-icons-png.flaticon.com/512/2997/2997898.png", "description" => "Everyday conversations"],
        ["name" => "Music & Arts", "img" => "https://cdn-icons-png.flaticon.com/512/3159/3159310.png", "description" => "Music, arts, and entertainment"]
    ]
];

// Add more languages here following the same pattern...
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
    <!-- 🌍 Enhanced Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-globe-americas"></i>
                <span>LinguaLearn</span>
            </div>

            <ul class="nav-menu">
                <li class="dropdown">
                    <a href="../Languages/language.php" class="nav-link">
                        <i class="fas fa-language"></i>
                        Languages <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-content">
                        <a href="../Topics/Topics.php?lang=French"><i class="fas fa-flag"></i> French</a>
                        <a href="../Topics/Topics.php?lang=Spanish"><i class="fas fa-flag"></i> Spanish</a>
                        <a href="../Topics/Topics.php?lang=German"><i class="fas fa-flag"></i> German</a>
                        <a href="../Topics/Topics.php?lang=Italian"><i class="fas fa-flag"></i> Italian</a>
                        <a href="../Topics/Topics.php?lang=English"><i class="fas fa-flag"></i> English</a>
                        <a href="../Topics/Topics.php?lang=Arabic"><i class="fas fa-flag"></i> Arabic</a>
                        <a href="../Topics/Topics.php?lang=Japanese"><i class="fas fa-flag"></i> Japanese</a>
                        <a href="../Topics/Topics.php?lang=Chinese"><i class="fas fa-flag"></i> Chinese</a>
                        <a href="../Topics/Topics.php?lang=Portuguese"><i class="fas fa-flag"></i> Portuguese</a>
                    </div>
                </li>
                <li><a href="../dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="../Dictionary/dictionary.php" class="nav-link"><i class="fas fa-book"></i> Dictionary</a></li>
                <li><a href="../progress.php" class="nav-link"><i class="fas fa-chart-line"></i> My Progress</a></li>
                <li><a href="../profile.php" class="nav-link"><i class="fas fa-user"></i> Profile</a></li>
            </ul>
        </div>
    </nav>

    <!-- 🧠 Enhanced Page Content -->
    <div class="page-container">
        <div class="page-header">
            <div class="header-content">
                <h1>Learn <span class="language-highlight"><?php echo htmlspecialchars($lang); ?></span></h1>
                <p>Choose a topic to start your learning journey. Each topic contains vocabulary, phrases, and interactive exercises.</p>
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
                    <span id="topicsCount"><?php echo count($topics[$lang] ?? []); ?></span> topics available
                </div>
            </div>

            <div class="topics-grid" id="topicsGrid">
                <?php
                if (array_key_exists($lang, $topics)) {
                    foreach ($topics[$lang] as $topic):
                        $tName   = htmlspecialchars($topic['name'], ENT_QUOTES);
                        $tImg    = htmlspecialchars($topic['img'], ENT_QUOTES);
                        $tDesc   = htmlspecialchars($topic['description'], ENT_QUOTES);
                        $langEsc = htmlspecialchars($lang, ENT_QUOTES);
                        $langUrl = urlencode($lang);
                        $topicUrl = urlencode($topic['name']);
                ?>
                        <div class="topic-card"
                            data-language="<?php echo $langEsc; ?>"
                            data-topic="<?php echo $tName; ?>"
                            onclick="window.location.href='../Lessons/lesson.php?lang=<?php echo $langUrl; ?>&topic=<?php echo $topicUrl; ?>'">
                            
                            <div class="card-icon">
                                <img src="<?php echo $tImg; ?>" alt="<?php echo $tName; ?>">
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
                <?php
                    endforeach;
                } else {
                    echo "<div class='no-topics-message'>
                            <i class='fas fa-folder-open'></i>
                            <h3>No Topics Available</h3>
                            <p>We're working on adding topics for this language. Check back soon!</p>
                            <a href='../Languages/language.php' class='btn-primary'>Choose Another Language</a>
                        </div>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- 📚 Progress Section -->
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