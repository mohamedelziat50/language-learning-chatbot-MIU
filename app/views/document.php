<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Editor - Language Learning Chatbot</title>
    <link rel="stylesheet" href="../../public/css/document.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
</head>
<body>
    <div class="document-container">
        <!-- Main Content Area (no left sidebar) -->
        <div class="main-content-area">
            <!-- Editor Section -->
            <div class="editor-section">
                <div class="editor-container">
                    <!-- Inline document header at the top of the editor -->
                        <div class="doc-header">
                        <div class="document-title-section">
                            <input type="text" id="document-title" value="Untitled Document" class="document-title-input">
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
                            autofocus
                        ></textarea>
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
                    <button class="tab-btn active" data-tab="review">
                        <i class="fas fa-check-circle"></i>
                        <span>Review</span>
                    </button>
                    <button class="tab-btn" data-tab="ai">
                        <i class="fas fa-robot"></i>
                        <span>Write with AI</span>
                    </button>
                    <button class="tab-btn" data-tab="plagiarism">
                        <i class="fas fa-shield-alt"></i>
                        <span>Plagiarism</span>
                    </button>
                </div>

                <div class="tab-content">
                    <!-- Review Suggestions Tab -->
                    <div class="tab-panel active" id="review-panel">
                        <div class="suggestions-header">
                            <h3>Review Suggestions</h3>
                            <span class="suggestion-count">0 suggestions</span>
                        </div>
                        
                        <div class="suggestion-categories">
                            <div class="category-section">
                                <h4 class="category-title">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Correctness
                                </h4>
                                <div class="suggestions-list" id="correctness-suggestions">
                                    <!-- Suggestions will be populated by JavaScript -->
                                </div>
                            </div>

                            <div class="category-section">
                                <h4 class="category-title">
                                    <i class="fas fa-eye"></i>
                                    Clarity
                                </h4>
                                <div class="suggestions-list" id="clarity-suggestions">
                                    <!-- Suggestions will be populated by JavaScript -->
                                </div>
                            </div>

                            <div class="category-section">
                                <h4 class="category-title">
                                    <i class="fas fa-heart"></i>
                                    Engagement
                                </h4>
                                <div class="suggestions-list" id="engagement-suggestions">
                                    <!-- Suggestions will be populated by JavaScript -->
                                </div>
                            </div>

                            <div class="category-section">
                                <h4 class="category-title">
                                    <i class="fas fa-paper-plane"></i>
                                    Delivery
                                </h4>
                                <div class="suggestions-list" id="delivery-suggestions">
                                    <!-- Suggestions will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Writing Assistant Tab -->
                    <div class="tab-panel" id="ai-panel">
                        <div class="ai-header">
                            <h3>Write with AI</h3>
                            <p class="ai-subtitle">Get help with your writing</p>
                        </div>
                        
                        <div class="ai-chat-container">
                            <div class="ai-messages" id="ai-messages">
                                <div class="ai-message">
                                    <div class="ai-avatar">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="ai-content">
                                        <p>Hi! I'm your AI writing assistant. How can I help you improve your document today?</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ai-input-container">
                                <textarea 
                                    id="ai-input" 
                                    placeholder="Ask AI for help with your writing..."
                                    rows="3"
                                ></textarea>
                                <button class="ai-send-btn" id="ai-send-btn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Plagiarism Check Tab -->
                    <div class="tab-panel" id="plagiarism-panel">
                        <div class="plagiarism-header">
                            <h3>Plagiarism Check</h3>
                            <button class="check-plagiarism-btn" id="check-plagiarism-btn">
                                <i class="fas fa-search"></i>
                                Check Document
                            </button>
                        </div>
                        
                        <div class="plagiarism-results" id="plagiarism-results">
                            <div class="no-results">
                                <i class="fas fa-shield-check"></i>
                                <p>No plagiarism detected</p>
                                <span class="result-subtitle">Your document appears to be original</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../public/js/document.js"></script>
</body>
</html>
