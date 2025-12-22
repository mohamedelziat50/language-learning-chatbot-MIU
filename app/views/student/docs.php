<?php
session_start();
ob_start();
require_once __DIR__ . '/../../controllers/DocumentsController.php';
ob_clean();

// Get documents from database
$user_id = $_SESSION['user_id'] ?? null;
$documents = [];

if ($user_id) {
    $db_documents = getDocumentsByUser($user_id);
    
    foreach ($db_documents as $doc) {
        $documents[] = [
            'id' => $doc['document_id'],
            'title' => $doc['title'],
            'preview' => $doc['preview_text'] ?? 'New document - Start writing to see preview...',
            'type' => 'Document',
            'updated_at' => $doc['updated_at'],
            'created_at' => $doc['created_at']
        ];
    }
}

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
    <link rel="stylesheet" href="../../../public/css/student/docs.css">
    <link rel="stylesheet" href="../../../public/css/notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <?php
    // Calculate base path for API calls
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    // Get the directory of the current script
    $scriptDir = dirname($scriptName);
    // Remove the /app/views/student part to get project root
    $projectRoot = str_replace('/app/views/student', '', $scriptDir);
    $projectRoot = str_replace('\\app\\views\\student', '', $projectRoot); // Windows
    // Ensure we have a base path (could be empty if at root, or /project-name)
    $apiBase = rtrim($projectRoot, '/') . '/app';
    ?>
    <script>
        // Base path for API calls
        window.API_BASE = '<?php echo htmlspecialchars($apiBase); ?>';
    </script>
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

    <script src="../../../public/js/notifications.js"></script>
    <script src="../../../public/js/student/docs.js"></script>
</body>
</html>