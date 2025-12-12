<?php
require_once __DIR__ . '/../../../models/dictionaryModel.php';

$model = new DictionaryModel(__DIR__ . '/../../../data/dictionary.php');

$dictionaryData = $model->getAll();

// Load counts
$totalWords = $model->getTotalWords();
$totalTopics = $model->getTotalTopics();

$referrerLanguage = $_GET['lang'] ?? $_GET['language'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Dictionary - Language Learning Bot</title>
    <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/Dictionary/dictionary.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Complete Dictionary</h1>
        <p>All vocabulary words across all languages</p>
    </div>

    <div class="language-selector">
        <select id="languageFilter" onchange="filterByLanguage(this.value)">
            <option value="all">All Languages</option>
            <option value="French">French</option>
            <option value="Spanish">Spanish</option>
            <option value="German">German</option>
            <option value="Italian">Italian</option>
            <option value="English">English</option>
            <option value="Arabic">Arabic</option>
            <option value="Japanese">Japanese</option>
        </select>
    </div>

    <div class="dictionary-content">
        <div class="stats">
            <h3>Dictionary Statistics</h3>
            <p>Total Languages: <?php echo count($dictionaryData); ?> |
                Total Topics: <?php echo $totalTopics; ?> |
                Total Words: <?php echo $totalWords; ?>
            </p>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search across all languages..." onkeyup="searchWords()">
        </div>

        <?php foreach ($dictionaryData as $language => $topics): ?>
            <div class="language-section" data-language="<?php echo $language; ?>">
                <div class="language-header">
                    <h2>🌍 <?php echo $language; ?></h2>
                    <span class="word-count">
                        <?php 
                            $langWordCount = 0;
                            foreach ($topics as $topicWords) {
                                $langWordCount += count($topicWords);
                            }
                            echo $langWordCount . ' words';
                        ?>
                    </span>
                </div>

                <?php foreach ($topics as $topic => $words): ?>
                    <div class="topic-section">
                        <div class="topic-header">
                            <h3>
                                <?php 
                                    $topicIcons = [
                                        'Greetings' => '👋', 'Food' => '🍕', 'Travel' => '✈️', 'Numbers' => '🔢', 'Family' => '👨‍👩‍👧‍👦',
                                        'Basics' => '🔤', 'Colors' => '🎨', 'Animals' => '🐾', 'Clothing' => '👕', 'Phrases' => '💬',
                                        'Introductions' => '👋', 'Work' => '💼', 'Hobbies' => '🎯', 'Weather' => '☀️', 'Directions' => '🗺️',
                                        'Religion & Culture' => '🕌', 'Alphabet' => '🔤', 'Traditions' => '🎎', 'Cuisine' => '🍽️',
                                        'Hiragana' => 'あ', 'Katakana' => 'ア', 'Anime Culture' => '🎌'
                                    ];
                                    echo ($topicIcons[$topic] ?? '📖') . ' ' . $topic;
                                ?>
                                <small>(<?php echo count($words); ?> words)</small>
                            </h3>
                        </div>
                        
                        <table class="words-table">
                            <thead>
                                <tr>
                                    <th width="40%">English</th>
                                    <th width="40%"><?php echo $language; ?> Translation</th>
                                    <th width="20%">Topic</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($words as $english => $translation): ?>
                                    <tr class="word-row" data-language="<?php echo $language; ?>">
                                        <td class="word-english"><?php echo htmlspecialchars($english); ?></td>
                                        <td class="word-translation"><?php echo htmlspecialchars($translation); ?></td>
                                        <td class="word-topic"><?php echo htmlspecialchars($topic); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="/language-learning-chatbot-MIU/public/js/Dictionary/dictionary.js"></script>
</body>
</html>
