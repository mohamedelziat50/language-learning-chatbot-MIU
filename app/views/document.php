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
                            <div class="ai-chat-header">
                                <h4>AI Assistant</h4>
                                <button class="clear-chat-btn" id="clear-chat-btn" title="Clear conversation">
                                    <i class="fas fa-broom"></i>
                                    <span>Clear</span>
                                </button>
                            </div>
                            <div class="ai-messages" id="ai-messages"></div>
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

    <script src="../../public/js/document.js"></script>
</body>
</html>
