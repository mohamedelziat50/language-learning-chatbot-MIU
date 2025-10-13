<?php
// Sample documents data - in a real application, this would come from a database
$documents = [
    [
        'id' => 1,
        'title' => 'Spanish Grammar Basics',
        'preview' => 'Essential grammar rules for Spanish learners. Covering verb conjugations, tenses, and common structures.',
        'type' => 'Grammar',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
    ],
    [
        'id' => 2,
        'title' => 'French Vocabulary List',
        'preview' => 'Comprehensive vocabulary list with 500+ essential French words and phrases for daily conversations.',
        'type' => 'Vocabulary',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
    ],
    [
        'id' => 3,
        'title' => 'German Pronunciation Guide',
        'preview' => 'Master German pronunciation with audio examples and phonetic transcriptions for difficult sounds.',
        'type' => 'Pronunciation',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 week'))
    ],
    [
        'id' => 4,
        'title' => 'English Conversation Practice',
        'preview' => 'Practice dialogues and conversation starters for improving English speaking skills.',
        'type' => 'Conversation',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 weeks'))
    ],
    [
        'id' => 5,
        'title' => 'Japanese Hiragana Chart',
        'preview' => 'Complete hiragana character chart with stroke order and pronunciation guide.',
        'type' => 'Writing',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-3 weeks'))
    ],
    [
        'id' => 6,
        'title' => 'Italian Cultural Notes',
        'preview' => 'Understanding Italian culture, customs, and social etiquette for better language learning.',
        'type' => 'Culture',
        'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 month'))
    ]
];

// Function to group documents by date
function groupDocumentsByDate($documents) {
    $yesterday = [];
    $earlier = [];
    $today = date('Y-m-d');
    $yesterday_date = date('Y-m-d', strtotime('-1 day'));
    
    foreach ($documents as $doc) {
        $doc_date = date('Y-m-d', strtotime($doc['updated_at']));
        
        if ($doc_date === $yesterday_date) {
            $yesterday[] = $doc;
        } else {
            $earlier[] = $doc;
        }
    }
    
    return ['yesterday' => $yesterday, 'earlier' => $earlier];
}

// Function to format relative time
function getRelativeTime($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'Just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', strtotime($datetime));
    }
}

$groupedDocs = groupDocumentsByDate($documents);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docs - Language Learning Platform</title>
    <link rel="stylesheet" href="../../../public/css/student/student.css">
    <link rel="stylesheet" href="../../../public/css/docs.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include "../partials/sidebar.php"; ?>
    <div class="main-content">
        <!-- Header Section -->
        <div class="docs-header">
            <h1 class="docs-title">Docs</h1>
            <div class="docs-header-actions">
                <button class="btn-new-doc">
                    <i class="fas fa-plus"></i>
                    New Doc
                </button>
                <button class="btn-upload">
                    <i class="fas fa-upload"></i>
                    Upload
                </button>
                <div class="docs-search">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search docs" autocomplete="off">
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="docs-content">
            <?php if (!empty($groupedDocs['yesterday'])): ?>
            <div class="docs-section">
                <h2 class="docs-section-title">Yesterday</h2>
                <div class="docs-grid">
                    <?php foreach ($groupedDocs['yesterday'] as $doc): ?>
                    <div class="doc-card" data-doc-id="<?php echo $doc['id']; ?>">
                        <div class="doc-card-header">
                            <span class="doc-tag"><?php echo htmlspecialchars($doc['type']); ?></span>
                            <button class="doc-menu">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                        <h3 class="doc-title"><?php echo htmlspecialchars($doc['title']); ?></h3>
                        <p class="doc-preview"><?php echo htmlspecialchars($doc['preview']); ?></p>
                        <div class="doc-meta">
                            <span class="doc-date">
                                <i class="fas fa-clock"></i>
                                Edited <?php echo getRelativeTime($doc['updated_at']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($groupedDocs['earlier'])): ?>
            <div class="docs-section">
                <h2 class="docs-section-title">Earlier</h2>
                <div class="docs-grid">
                    <?php foreach ($groupedDocs['earlier'] as $doc): ?>
                    <div class="doc-card" data-doc-id="<?php echo $doc['id']; ?>">
                        <div class="doc-card-header">
                            <span class="doc-tag"><?php echo htmlspecialchars($doc['type']); ?></span>
                            <button class="doc-menu">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                        <h3 class="doc-title"><?php echo htmlspecialchars($doc['title']); ?></h3>
                        <p class="doc-preview"><?php echo htmlspecialchars($doc['preview']); ?></p>
                        <div class="doc-meta">
                            <span class="doc-date">
                                <i class="fas fa-clock"></i>
                                Edited <?php echo getRelativeTime($doc['updated_at']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($groupedDocs['yesterday']) && empty($groupedDocs['earlier'])): ?>
            <div class="docs-empty">
                <div class="docs-empty-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="docs-empty-title">No documents yet</h3>
                <p class="docs-empty-text">Create your first document to get started with your language learning journey.</p>
                <button class="btn-new-doc">
                    <i class="fas fa-plus"></i>
                    Create Your First Doc
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../../public/js/docs.js"></script>
</body>
</html>