<?php
session_start();
ob_start();
require_once __DIR__ . '/../controllers/DocumentsController.php';
ob_clean();

$document_id = $_GET['id'] ?? null;
$document_title = "Untitled Document";
$document_content = "";
$user_id = $_SESSION['user_id'] ?? null;

if ($document_id && $user_id) {
    $document_data = getDocumentById($document_id);
    if ($document_data && $document_data['owner_id'] == $user_id) {
        $document_title = htmlspecialchars($document_data['title']);
        $document_content = htmlspecialchars($document_data['content']);
    } else {
        header("Location: ./student/docs.php");
        exit();
    }
} else if (!$user_id) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Editor - Language Learning Chatbot</title>
    <link rel="stylesheet" href="../../public/css/document.css">
    <link rel="stylesheet" href="../../public/css/notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php
    // Calculate base path for API calls
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    // Get the directory of the current script
    $scriptDir = dirname($scriptName);
    // Remove the /app/views part to get project root
    $projectRoot = str_replace('/app/views', '', $scriptDir);
    $projectRoot = str_replace('\\app\\views', '', $projectRoot); // Windows
    // Ensure we have a base path (could be empty if at root, or /project-name)
    $apiBase = rtrim($projectRoot, '/') . '/app';
    ?>
    <script>
        // Base path for API calls
        window.API_BASE = '<?php echo htmlspecialchars($apiBase); ?>';
    </script>
</head>
<body>
    <div class="document-container">
        <!-- Main Content Area (no left sidebar) -->
        <div class="main-content-area">
            <button class="sidebar-toggle" id="sidebar-toggle" title="Hide Assistant">
                <i class="fas fa-chevron-right"></i>
            </button>
            <!-- Left Sidebar -->
            <aside class="left-sidebar" id="left-sidebar">
                <div class="ls-account">
                    <div class="ls-name">Mohamed Hesham</div>
                    <div class="ls-email">mohamedelziat50@gmail.com</div>
                    <span class="ls-badge">Free</span>
                </div>
                <nav class="ls-nav">
                    <button class="ls-nav-btn active" data-view="stats"><i class="fas fa-chart-bar"></i><span>Stats</span></button>
                    <button class="ls-nav-btn" data-view="versions"><i class="fas fa-clock-rotate-left"></i><span>Versions</span></button>
                </nav>
                <div class="ls-content">
                    <div id="ls-stats" class="ls-view">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-font"></i></div>
                            <div class="stat-info">
                                <div class="stat-value" id="stat-words">0</div>
                                <div class="stat-label">Words</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-keyboard"></i></div>
                            <div class="stat-info">
                                <div class="stat-value" id="stat-chars">0</div>
                                <div class="stat-label">Characters</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                            <div class="stat-info">
                                <div class="stat-value" id="stat-reading">0 min</div>
                                <div class="stat-label">Reading time</div>
                            </div>
                        </div>
                    </div>
                    <div id="ls-versions" class="ls-view" style="display:none">
                        <p class="muted">Recent saves</p>
                        <div id="version-list"></div>
                    </div>
                </div>
                <div class="ls-footer">
                    <a href="./student/docs.php" class="ls-footer-btn"><i class="fas fa-arrow-left"></i><span>Back to Dashboard</span></a>
                    <button class="ls-footer-btn" onclick="alert('Sign out')"><i class="fas fa-right-from-bracket"></i><span>Sign out</span></button>
                </div>
            </aside>
            <!-- Editor Section -->
            <div class="editor-section">
                <div class="editor-container">
                    <!-- Inline document header at the top of the editor -->
                        <div class="doc-header">
                        <div class="document-title-section">
                            <input type="text" id="document-title" value="<?php echo $document_title; ?>" class="document-title-input">
                            <span class="save-status" id="save-status">All changes saved</span>
                        </div>
                        <div class="doc-header-actions">
                            <button class="goals-btn">
                                <i class="fas fa-bullseye"></i>
                                Goals
                            </button>
                            <button class="overall-score-btn" type="button">
                                <span class="score-label">Overall Score</span>
                                <span class="score-value">--</span>
                            </button>
                        </div>
                    </div>

                    <div class="editor-content">
                        <textarea 
                            id="document-editor" 
                            placeholder="Start writing your document…"
                            autofocus><?php echo $document_content; ?></textarea>
                        <input type="hidden" id="document-id" value="<?php echo $document_id ?? ''; ?>">
                    </div>

                    <!-- Formatting toolbar at the bottom -->
                    <div class="editor-toolbar editor-toolbar-bottom">
                        <div class="toolbar-group">
                            <button class="toolbar-btn" title="Bold"><i class="fas fa-bold"></i></button>
                            <button class="toolbar-btn" title="Italic"><i class="fas fa-italic"></i></button>
                            <button class="toolbar-btn" title="Underline"><i class="fas fa-underline"></i></button>
                        </div>
                        <div class="toolbar-group">
                            <button class="toolbar-btn" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                            <button class="toolbar-btn" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (full height on the right) -->
            <div class="right-sidebar">
                <div class="sidebar-tabs">
                    <button class="tab-btn active" data-tab="ai">
                        <i class="fas fa-robot"></i>
                        <span>Write with generative AI</span>
                    </button>
                    <button class="tab-btn" data-tab="review">
                        <i class="fas fa-check-circle"></i>
                        <span>Review suggestions</span>
                    </button>
                    <button class="tab-btn" data-tab="plagiarism">
                        <i class="fas fa-quote-right"></i>
                        <span>Check for AI
                            <br>text & plagiarism</span>
                    </button>
                </div>

                <div class="tab-content">
                    <!-- Review Suggestions Tab -->
                    <div class="tab-panel" id="review-panel">
                        <div class="suggestions-header">
                            <h3>Review suggestions <span class="badge">0</span></h3>
                            <button class="analyze-btn" id="analyze-btn" title="Analyze document for grammar and vocabulary">
                                <i class="fas fa-spell-check"></i>
                                <span>Analyze</span>
                            </button>
                        </div>

                        <div class="category-chips">
                            <button class="chip correctness active"><span class="chip-underline"></span>Correctness</button>
                            <button class="chip clarity"><span class="chip-underline"></span>Clarity</button>
                            <button class="chip engagement"><span class="chip-underline"></span>Engagement</button>
                            <button class="chip delivery"><span class="chip-underline"></span>Delivery</button>
                        </div>

                        <div class="pro-header">
                            <span class="star">★</span>
                            <span>Pro suggestions</span>
                            <span class="badge" id="pro-count">0</span>
                        </div>

                        <div class="suggestions-list suggestions-list-flat" id="review-suggestions">
                            <!-- Populated by JS with suggestion cards -->
                        </div>
                    </div>

                    <!-- AI Writing Assistant Tab -->
                    <div class="tab-panel active" id="ai-panel">
                        <div class="ai-banner">
                            <div class="banner-text">
                                <h4>Set your voice</h4>
                                <p>Choose how you want your generated text to sound.</p>
                            </div>
                            <button class="banner-btn"><i class="fas fa-sliders-h"></i> Set voice</button>
                        </div>

                        <div class="ai-ideas">
                            <h3>What do you want to do?</h3>
                            <p class="muted">Choose an option or type below</p>
                            <div class="ideas-actions">
                                <button class="idea-btn" data-action="improve">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                    Improve it
                                </button>
                                <button class="idea-btn" data-action="persuasive">
                                    <i class="fas fa-bullhorn"></i>
                                    Make it persuasive
                                </button>
                                <button class="idea-btn" data-action="assertive">
                                    <i class="fas fa-hand-peace"></i>
                                    Make it assertive
                                </button>
                                <button class="idea-btn" data-action="ideas">
                                    <i class="fas fa-lightbulb"></i>
                                    More ideas
                                </button>
                            </div>
                        </div>

                        <div class="ai-chat-container">
                            <div class="ai-messages" id="ai-messages">
                                <div class="ai-chat-header">
                                    <h4>AI Assistant</h4>
                                    <button class="clear-chat-btn" id="clear-chat-btn" title="Clear conversation">
                                        <i class="fas fa-broom"></i>
                                        <span>Clear</span>
                                    </button>
                                </div>
                                <div class="ai-empty-state" id="ai-empty-state">
                                    <svg class="ai-empty-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 8V4H8"/>
                                        <rect width="16" height="12" x="4" y="8" rx="2"/>
                                        <path d="M2 14h2"/>
                                        <path d="M20 14h2"/>
                                        <path d="M15 13v2"/>
                                        <path d="M9 13v2"/>
                                    </svg>
                                    <div class="ai-empty-text">Start a conversation with the AI assistant</div>
                                </div>
                            </div>
                            <div class="ai-input-container">
                                <input type="text" id="ai-text-input" placeholder="Tell us to...">
                                <button class="ai-send-btn" id="ai-send-btn"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Plagiarism Check Tab -->
                    <div class="tab-panel" id="plagiarism-panel">
                        <div class="plagiarism-header">
                            <h3>Plagiarism and AI text check</h3>
                            <div class="style-switcher">APA ▾</div>
                        </div>

                        <div class="plagiarism-body">
                            <div class="plag-summary">
                                <span class="percent">0%</span> of the text matches known sources.
                            </div>

                            <div class="plag-placeholder">
                                <div class="shield"><i class="fas fa-shield-alt"></i></div>
                                <h4>You're a true original.<br>Ensure your work is, too.</h4>
                                <ul class="benefits">
                                    <li><i class="fas fa-arrow-right"></i> Catch accidental plagiarism</li>
                                    <li><i class="fas fa-arrow-right"></i> Detect similarities to AI text</li>
                                    <li><i class="fas fa-arrow-right"></i> Get fully formatted citations</li>
                                </ul>
                                <button class="cta-btn" id="check-plagiarism-btn"><i class="fas fa-search"></i> Check document</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../public/js/notifications.js"></script>
    <script src="../../public/js/document.js"></script>
</body>
</html>
