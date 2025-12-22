// Document Editor JavaScript - Grammarly-inspired functionality

class DocumentEditor {
    constructor() {
        this.currentTab = 'ai';
        this.isAutoSaving = false;
        this.saveTimeout = null;
        this.suggestions = [];
        this.aiMessages = [];
        this.analysisTimeout = null;
        this.isAnalyzing = false;
        this.inlineHighlights = [];

        this.initializeElements();
        this.bindEvents();
        this.initializeEditor();
        this.loadSuggestions();
        this.initializeResponsive();
        // Default active tab: AI panel
        this.switchTab('ai');
    }

    initializeElements() {
        // Main elements
        this.documentTitle = document.getElementById('document-title');
        this.documentEditor = document.getElementById('document-editor');
        this.saveStatus = document.getElementById('save-status');

        // Tab elements
        this.tabButtons = document.querySelectorAll('.tab-btn');
        this.tabPanels = document.querySelectorAll('.tab-panel');
        this.rightSidebar = document.querySelector('.right-sidebar');
        this.sidebarToggle = document.getElementById('sidebar-toggle');
        this.mainContentArea = document.querySelector('.main-content-area');

        // Left sidebar elements
        this.leftSidebar = document.getElementById('left-sidebar');
        this.lsViews = {
            stats: document.getElementById('ls-stats'),
            versions: document.getElementById('ls-versions')
        };

        // AI elements
        this.aiInput = document.getElementById('ai-text-input');
        this.aiSendBtn = document.getElementById('ai-send-btn');
        this.aiMessages = document.getElementById('ai-messages');
        this.clearChatBtn = document.getElementById('clear-chat-btn');

        // Plagiarism elements
        this.checkPlagiarismBtn = document.getElementById('check-plagiarism-btn');
        this.plagiarismPlaceholder = document.querySelector('.plag-placeholder');

        // Analysis elements
        this.analyzeBtn = document.getElementById('analyze-btn');

        // Toolbar elements
        this.toolbarBtns = document.querySelectorAll('.toolbar-btn');
    }

    bindEvents() {
        // Tab switching
        this.tabButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tab = e.currentTarget.dataset.tab;
                this.switchTab(tab);
            });
        });

        // Document title editing
        this.documentTitle.addEventListener('input', () => {
            this.triggerAutoSave();
        });

        // Document content editing
        this.documentEditor.addEventListener('input', () => {
            this.triggerAutoSave();
            this.debouncedAnalyzeContent();
            this.renderStats(); // Update stats live
            this.clearInlineHighlights();
        });

        // AI chat
        this.aiSendBtn.addEventListener('click', () => {
            this.sendAIMessage();
        });

        // Idea buttons -> prefill prompt and generate
        document.querySelectorAll('.idea-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.action;
                const seed = this.documentEditor.value || 'the current text';
                let prompt = '';
                switch (action) {
                    case 'improve':
                        prompt = `Improve it: ${seed}`;
                        break;
                    case 'persuasive':
                        prompt = `Make it persuasive: ${seed}`;
                        break;
                    case 'assertive':
                        prompt = `Make it assertive: ${seed}`;
                        break;
                    case 'ideas':
                        prompt = `Give me more ideas based on: ${seed}`;
                        break;
                }
                this.aiInput.value = prompt;
                this.sendAIMessage();
            });
        });

        this.aiInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendAIMessage();
            }
        });

        // Clear chat
        this.clearChatBtn.addEventListener('click', () => {
            this.clearAIChat();
        });

        // Plagiarism check
        this.checkPlagiarismBtn.addEventListener('click', () => {
            this.checkPlagiarism();
        });

        // Manual analyze button
        const analyzeBtn = document.getElementById('analyze-btn');
        if (analyzeBtn) {
            analyzeBtn.addEventListener('click', () => {
                this.analyzeContent();
            });
        }

        // Toolbar buttons
        this.toolbarBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleToolbarAction(e.currentTarget);
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            this.handleKeyboardShortcuts(e);
        });

        // Sidebar toggle (handled in initializeResponsive for mobile)
        this.sidebarToggle.addEventListener('click', () => {
            this.toggleSidebar();
        });

        // Left sidebar nav switching
        this.leftSidebar.querySelectorAll('.ls-nav-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.leftSidebar.querySelectorAll('.ls-nav-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const view = btn.dataset.view;
                this.switchLeftSidebarView(view);
            });
        });
    }

    switchLeftSidebarView(view) {
        Object.keys(this.lsViews).forEach(k => this.lsViews[k].style.display = 'none');
        switch (view) {
            case 'stats':
                this.renderStats();
                this.lsViews.stats.style.display = 'block';
                break;
            case 'versions':
                this.renderVersions();
                this.lsViews.versions.style.display = 'block';
                break;
        }
    }

    renderStats() {
        const content = this.documentEditor.value;
        const words = content.trim().split(/\s+/).filter(Boolean).length;
        const chars = content.replace(/\s/g, '').length;
        const readingTime = Math.max(1, Math.round(words / 200));

        document.getElementById('stat-words').textContent = words;
        document.getElementById('stat-chars').textContent = chars;
        document.getElementById('stat-reading').textContent = readingTime + ' min';
    }

    renderVersions() {
        const saved = localStorage.getItem('document_versions');
        const versions = saved ? JSON.parse(saved) : [];
        const listContainer = document.getElementById('version-list');

        if (versions.length === 0) {
            listContainer.innerHTML = '<p class="muted">No versions yet. Auto-saves will appear here.</p>';
            return;
        }

        listContainer.innerHTML = versions.reverse().map(v => `
            <div class="version-item">
                <div class="version-time">${new Date(v.timestamp).toLocaleString()}</div>
                <div class="version-preview">${v.preview || 'No preview'}</div>
            </div>
        `).join('');
    }

    toggleSidebar() {
        const width = window.innerWidth;

        // Mobile behavior (768px and below)
        if (width <= 768) {
            this.rightSidebar.classList.toggle('active');
            return;
        }

        // Desktop behavior (above 768px)
        const mainContentArea = document.querySelector('.main-content-area');
        const isHidden = this.rightSidebar.classList.toggle('hidden');
        const icon = this.sidebarToggle.querySelector('i');

        if (isHidden) {
            // Hide sidebar and expand main content
            mainContentArea.classList.add('sidebar-hidden');
            this.sidebarToggle.classList.add('sidebar-hidden');
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-left');
            this.sidebarToggle.title = 'Show Assistant';
        } else {
            // Show sidebar and restore layout
            mainContentArea.classList.remove('sidebar-hidden');
            this.sidebarToggle.classList.remove('sidebar-hidden');
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
            this.sidebarToggle.title = 'Hide Assistant';
        }
    }

    initializeEditor() {
        // Set initial focus
        this.documentEditor.focus();

        // Initialize autosave
        this.startAutoSave();

        // Load any saved content
        this.loadSavedContent();
    }

    switchTab(tabName) {
        // Update tab buttons
        this.tabButtons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.tab === tabName) {
                btn.classList.add('active');
            }
        });

        // Update tab panels
        this.tabPanels.forEach(panel => {
            panel.classList.remove('active');
            if (panel.id === `${tabName}-panel`) {
                panel.classList.add('active');
            }
        });

        this.currentTab = tabName;
    }

    triggerAutoSave() {
        // Clear existing timeout
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }

        // Show saving status
        this.updateSaveStatus('Saving...', 'saving');

        // Set new timeout
        this.saveTimeout = setTimeout(() => {
            this.performAutoSave();
        }, 2000); // Save after 2 seconds of inactivity
    }

    async performAutoSave() {
        if (this.isAutoSaving) return;

        this.isAutoSaving = true;
        this.updateSaveStatus('Saving...', 'saving');

        const success = await this.saveContent();

        if (success) {
            this.updateSaveStatus('All changes saved', 'saved');
        } else {
            this.updateSaveStatus('Failed to save', 'error');
        }

        this.isAutoSaving = false;
    }

    async saveContent() {
        const documentId = document.getElementById('document-id')?.value;
        const title = this.documentTitle.value.trim() || 'Untitled Document';
        const content = this.documentEditor.value;

        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);

        try {
            let url;
            if (documentId) {
                url = `/index.php/documents/${documentId}/update`;
            } else {
                url = `/index.php/documents/create`;
            }

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.status === 'success') {
                if (!documentId && data.document_id) {
                    // New document created, update the hidden input and URL
                    const hiddenInput = document.getElementById('document-id');
                    if (hiddenInput) {
                        hiddenInput.value = data.document_id;
                    } else {
                        // Create hidden input if it doesn't exist
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.id = 'document-id';
                        input.value = data.document_id;
                        this.documentEditor.parentElement.appendChild(input);
                    }
                    window.history.replaceState({}, '', `?id=${data.document_id}`);
                }
                return true;
            } else {
                console.error('Save failed:', data);
                return false;
            }
        } catch (error) {
            console.error('Error saving document:', error);
            return false;
        }
    }

    loadSavedContent() {
        // Content is loaded from PHP, no need to load from localStorage
    }

    updateSaveStatus(message, status = 'saved') {
        this.saveStatus.textContent = message;
        this.saveStatus.className = `save-status ${status}`;
    }

    startAutoSave() {
        // Auto-save every 30 seconds as backup
        setInterval(() => {
            if (!this.isAutoSaving && this.documentEditor.value.trim()) {
                this.performAutoSave();
            }
        }, 30000);
    }

    debouncedAnalyzeContent() {
        // Clear existing timeout
        if (this.analysisTimeout) {
            clearTimeout(this.analysisTimeout);
        }

        // Set new timeout - analyze after 4 seconds of inactivity
        this.analysisTimeout = setTimeout(() => {
            this.analyzeContent();
        }, 4000);
    }

    async analyzeContent() {
        const content = this.documentEditor.value.trim();

        // Check content length first (before checking document ID)
        if (content.length < 10) {
            this.clearSuggestions();
            this.clearInlineHighlights();
            window.NotificationManager?.showNotification('Document is too short to analyze. Please add more content (at least 10 characters).', 'info');
            return;
        }

        if (this.isAnalyzing) {
            return; // Already analyzing
        }

        this.isAnalyzing = true;
        this.updateSaveStatus('Analyzing...', 'saving');

        // Update analyze button
        if (this.analyzeBtn) {
            this.analyzeBtn.disabled = true;
            const icon = this.analyzeBtn.querySelector('i');
            const span = this.analyzeBtn.querySelector('span');
            if (icon) icon.className = 'fas fa-spinner fa-spin';
            if (span) span.textContent = 'Analyzing...';
        }

        try {
            // First, save the document to ensure latest content is analyzed
            const saveSuccess = await this.saveContent();

            if (!saveSuccess) {
                window.NotificationManager?.showNotification('Failed to save document. Please try again.', 'error');
                return;
            }

            // Get document ID after saving (in case it was a new document)
            let documentId = document.getElementById('document-id')?.value;

            if (!documentId) {
                window.NotificationManager?.showNotification('Document not saved yet. Please wait a moment and try again.', 'info');
                return;
            }

            // Call the analyze API
            const response = await fetch(`../../app/index.php/documents/${documentId}/analyze`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    analyze_grammar: true,
                    analyze_vocabulary: true
                })
            });

            const data = await response.json();

            // Load suggestions after analysis
            await this.loadSuggestions();

            // Check API status and show appropriate messages
            if (data.api_status) {
                const apiErrors = [];
                if (data.api_status.grammar === 'failed' || data.api_status.grammar === 'no_key') {
                    apiErrors.push('Grammar analysis: ' + (data.errors?.grammar || 'API unavailable'));
                }
                if (data.api_status.vocabulary === 'failed' || data.api_status.vocabulary === 'no_key') {
                    apiErrors.push('Vocabulary analysis: ' + (data.errors?.vocabulary || 'API unavailable'));
                }

                if (apiErrors.length > 0) {
                    window.NotificationManager?.showNotification(
                        'API Error: ' + apiErrors.join(' | ') + '. Check error logs for details.',
                        'error'
                    );
                }
            }

            if (data.status === 'success' || data.status === 'warning') {
                const suggestionCount = data.suggestions_count || 0;
                if (suggestionCount > 0) {
                    window.NotificationManager?.showNotification(
                        `Analysis complete: ${suggestionCount} suggestion${suggestionCount !== 1 ? 's' : ''} found`,
                        data.status === 'warning' ? 'warning' : 'success'
                    );
                } else {
                    const message = data.status === 'warning'
                        ? 'Analysis complete: No suggestions found (API had issues). Your document may still have errors.'
                        : 'Analysis complete: No suggestions found. Your document looks good!';
                    window.NotificationManager?.showNotification(message, data.status === 'warning' ? 'warning' : 'success');
                }
            } else {
                console.error('Analysis failed:', data.message, data.errors);
                const errorMsg = data.errors ? Object.values(data.errors).join(' | ') : (data.message || 'Unknown error');
                window.NotificationManager?.showNotification('Analysis failed: ' + errorMsg, 'error');
            }
        } catch (error) {
            console.error('Error analyzing content:', error);
            window.NotificationManager?.showNotification('Error analyzing content: ' + error.message, 'error');
        } finally {
            this.isAnalyzing = false;

            // Restore analyze button
            if (this.analyzeBtn) {
                this.analyzeBtn.disabled = false;
                const icon = this.analyzeBtn.querySelector('i');
                const span = this.analyzeBtn.querySelector('span');
                if (icon) icon.className = 'fas fa-spell-check';
                if (span) span.textContent = 'Analyze';
            }
        }
    }

    async loadSuggestions() {
        const documentId = document.getElementById('document-id')?.value;

        if (!documentId) {
            this.clearSuggestions();
            return;
        }

        try {
            const response = await fetch(`../../app/index.php/documents/${documentId}/suggestions`);
            const data = await response.json();

            if (data.status === 'success') {
                this.suggestions = data.suggestions || [];
                this.displaySuggestions(this.suggestions);
                this.renderInlineHighlights();
            } else {
                console.error('Failed to load suggestions:', data.message);
                this.clearSuggestions();
            }
        } catch (error) {
            console.error('Error loading suggestions:', error);
            this.clearSuggestions();
        }
    }

    displaySuggestions(suggestions) {
        // Clear existing suggestions
        this.clearSuggestions();

        if (!suggestions || suggestions.length === 0) {
            const listContainer = document.getElementById('review-suggestions');
            if (listContainer) {
                listContainer.innerHTML = '<p class="muted" style="padding: 20px; text-align: center;">No suggestions found. Your document looks good!</p>';
            }
            this.updateSuggestionCount(0);
            return;
        }

        // Flat list for refined UI
        const listContainer = document.getElementById('review-suggestions');
        let proCount = 0;

        suggestions.forEach(suggestion => {
            const suggestionElement = this.createSuggestionElement(suggestion);
            if (listContainer) {
                listContainer.appendChild(suggestionElement);
            }
            proCount++;
        });

        const proBadge = document.getElementById('pro-count');
        if (proBadge) proBadge.textContent = proCount.toString();

        // Update suggestion count
        this.updateSuggestionCount(suggestions.length);
    }

    createSuggestionElement(suggestion) {
        const div = document.createElement('div');
        div.className = 'suggestion-card';
        div.dataset.suggestionId = suggestion.suggestion_id;

        // Map suggestion types to categories
        const categoryMap = {
            'grammar': 'Correctness',
            'vocabulary': 'Clarity',
            'spelling': 'Correctness',
            'clarity': 'Clarity'
        };

        const category = categoryMap[suggestion.suggestion_type] || 'Correctness';
        const iconMap = {
            'grammar': 'fa-spell-check',
            'vocabulary': 'fa-book',
            'spelling': 'fa-exclamation-triangle',
            'clarity': 'fa-lightbulb'
        };
        const icon = iconMap[suggestion.suggestion_type] || 'fa-shield-alt';

        const originalText = suggestion.original_text || '';
        const suggestedText = suggestion.suggested_text || '';
        const explanation = suggestion.explanation || 'No explanation available';

        div.innerHTML = `
            <div class="meta"><i class="fas ${icon}"></i> ${category} · ${suggestion.suggestion_type}</div>
            <div class="text">
                <span class="original-text">"${this.escapeHtml(originalText)}"</span>
                ${suggestedText ? `<span class="arrow">→</span> <span class="suggested-text">"${this.escapeHtml(suggestedText)}"</span>` : ''}
            </div>
            <div class="suggestion-description">${this.escapeHtml(explanation)}</div>
            <div class="actions">
                <button class="accept" onclick="documentEditor.acceptSuggestion(this, ${suggestion.suggestion_id})">Accept</button>
                <button class="dismiss" onclick="documentEditor.dismissSuggestion(this, ${suggestion.suggestion_id})">Dismiss</button>
            </div>
        `;

        return div;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async acceptSuggestion(button, suggestionId) {
        const suggestionCard = button.closest('.suggestion-card');
        if (!suggestionCard) return;

        // Disable button and show loading
        button.disabled = true;
        button.textContent = 'Applying...';

        try {
            const response = await fetch(`../../app/index.php/suggestions/${suggestionId}/applySuggestion`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.status === 'success') {
                // Disable all suggestion cards (make them unclickable)
                const allCards = document.querySelectorAll('.suggestion-card');
                allCards.forEach(card => {
                    card.style.pointerEvents = 'none';
                    card.style.opacity = '0.6';
                });

                // Fade out the applied suggestion smoothly
                suggestionCard.style.transition = 'opacity 0.3s ease';
                suggestionCard.style.opacity = '0';
                
                setTimeout(async () => {
                    // Clear all suggestions from UI
                    this.clearSuggestions();

                    // Reload document content
                    await this.reloadDocumentContent();

                    // Automatically re-analyze the updated content
                    await this.analyzeContent();

                    // Show success message
                    window.NotificationManager?.showNotification('Suggestion applied successfully', 'success');
                }, 300);
            } else {
                button.disabled = false;
                button.textContent = 'Accept';
                window.NotificationManager?.showNotification('Failed to apply suggestion: ' + (data.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Error accepting suggestion:', error);
            button.disabled = false;
            button.textContent = 'Accept';
            window.NotificationManager?.showNotification('Error applying suggestion', 'error');
        }
    }

    dismissSuggestion(button, suggestionId) {
        const suggestionCard = button.closest('.suggestion-card');
        if (suggestionCard) {
            suggestionCard.style.transition = 'opacity 0.3s';
            suggestionCard.style.opacity = '0';
            setTimeout(() => {
                suggestionCard.remove();
                this.updateSuggestionCount();
            }, 300);
        }
    }

    async reloadDocumentContent() {
        const documentId = document.getElementById('document-id')?.value;
        if (!documentId) return;

        try {
            const response = await fetch(`../../app/index.php/documents/${documentId}`);
            const data = await response.json();

            if (data.status === 'success' && data.document) {
                // Update editor content
                this.documentEditor.value = data.document.content || '';
                this.documentTitle.value = data.document.title || '';

                // Trigger auto-save status update
                this.updateSaveStatus('Content updated', 'saved');
            }
        } catch (error) {
            console.error('Error reloading document:', error);
        }
    }

    renderInlineHighlights() {
        this.clearInlineHighlights();

        if (!this.suggestions || this.suggestions.length === 0) {
            return;
        }

        // Note: Since we're using a textarea, we can't directly highlight text inline
        // Instead, we'll show highlights in a separate overlay or use markers
        // For now, we'll store the highlights for potential future use with contenteditable div

        this.suggestions.forEach(suggestion => {
            if (suggestion.position_start !== null && suggestion.position_end !== null) {
                this.inlineHighlights.push({
                    start: suggestion.position_start,
                    end: suggestion.position_end,
                    type: suggestion.suggestion_type,
                    id: suggestion.suggestion_id
                });
            }
        });

        // If we want to add visual indicators, we could add markers next to the textarea
        // For now, clicking on suggestions in the sidebar will be the primary interaction
    }

    clearInlineHighlights() {
        this.inlineHighlights = [];
        // Remove any highlight markers if they exist
        const markers = document.querySelectorAll('.inline-highlight-marker');
        markers.forEach(marker => marker.remove());
    }

    clearSuggestions() {
        const container = document.getElementById('review-suggestions');
        if (container) container.innerHTML = '';

        this.updateSuggestionCount(0);
    }

    updateSuggestionCount(count = null) {
        const countElement = document.querySelector('#review-panel .badge');
        if (count === null) {
            // Count current suggestions
            const allSuggestions = document.querySelectorAll('#review-suggestions .suggestion-card');
            count = allSuggestions.length;
        }
        if (countElement) countElement.textContent = `${count}`;
    }

    sendAIMessage() {
        const message = this.aiInput.value.trim();
        if (!message) return;

        // Add user message
        this.addAIMessage(message, 'user');

        // Clear input
        this.aiInput.value = '';

        // Show typing indicator
        this.showTypingIndicator();

        // Simulate AI response
        setTimeout(() => {
            this.hideTypingIndicator();
            this.addAIMessage(this.generateAIResponse(message), 'ai');
        }, 1500);
    }

    addAIMessage(message, sender) {
        // Hide empty state on first message
        const emptyState = document.getElementById('ai-empty-state');
        if (emptyState) {
            emptyState.style.display = 'none';
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `ai-message ${sender}`;

        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="ai-content">
                    <p>${message}</p>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="ai-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="ai-content">
                    <p>${message}</p>
                </div>
            `;
        }

        this.aiMessages.appendChild(messageDiv);
        this.aiMessages.scrollTop = this.aiMessages.scrollHeight;
    }

    showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'ai-message typing-indicator';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="ai-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="ai-content">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;

        this.aiMessages.appendChild(typingDiv);
        this.aiMessages.scrollTop = this.aiMessages.scrollHeight;
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    generateAIResponse(userMessage) {
        const responses = [
            "I can help you improve that sentence. Try using more specific words to make it clearer.",
            "Consider breaking this into shorter sentences for better readability.",
            "This is a good start! You might want to add more details to support your main point.",
            "Your writing is clear, but you could make it more engaging by asking a question.",
            "I notice you're using passive voice here. Active voice would make this stronger.",
            "This paragraph flows well! Consider adding a transition to connect it to the next one."
        ];

        return responses[Math.floor(Math.random() * responses.length)];
    }

    clearAIChat() {
        // Clear all messages but keep the header and empty state
        const messages = this.aiMessages.querySelectorAll('.ai-message, .typing-indicator');
        messages.forEach(msg => msg.remove());

        // Show empty state again
        const emptyState = document.getElementById('ai-empty-state');
        if (emptyState) {
            emptyState.style.display = 'flex';
        }

        window.NotificationManager.showNotification('Chat cleared', 'info');
    }

    checkPlagiarism() {
        this.checkPlagiarismBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
        this.checkPlagiarismBtn.disabled = true;

        // Simulate plagiarism check
        setTimeout(() => {
            this.checkPlagiarismBtn.innerHTML = '<i class="fas fa-search"></i> Check Document';
            this.checkPlagiarismBtn.disabled = false;

            // Show results
            this.showPlagiarismResults();
        }, 2000);
    }

    showPlagiarismResults() {
        const results = this.plagiarismPlaceholder;
        if (results) {
            results.innerHTML = `
                <div class="shield"><i class="fas fa-shield-alt"></i></div>
                <h4>You're a true original.<br>Ensure your work is, too.</h4>
                <ul class="benefits">
                    <li><i class="fas fa-arrow-right"></i> Catch accidental plagiarism</li>
                    <li><i class="fas fa-arrow-right"></i> Detect similarities to AI text</li>
                    <li><i class="fas fa-arrow-right"></i> Get fully formatted citations</li>
                </ul>
                <button class="cta-btn" id="check-plagiarism-btn"><i class="fas fa-search"></i> Check document</button>
            `;
        }
    }

    handleToolbarAction(button) {
        const action = button.title.toLowerCase();

        // Toggle active state
        button.classList.toggle('active');

        // Apply formatting (mock implementation)
        switch (action) {
            case 'bold':
                this.applyFormatting('bold');
                break;
            case 'italic':
                this.applyFormatting('italic');
                break;
            case 'underline':
                this.applyFormatting('underline');
                break;
            case 'bullet list':
                this.applyFormatting('bulletList');
                break;
            case 'numbered list':
                this.applyFormatting('numberedList');
                break;
        }
    }

    applyFormatting(type) {
        // Mock formatting implementation
        console.log(`Applying ${type} formatting`);
        window.NotificationManager.showNotification(`${type} formatting applied`, 'info');
    }

    handleKeyboardShortcuts(e) {
        // Ctrl+S for save
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            this.performAutoSave();
        }

        // Ctrl+B for bold
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            const boldBtn = document.querySelector('[title="Bold"]');
            this.handleToolbarAction(boldBtn);
        }

        // Ctrl+I for italic
        if (e.ctrlKey && e.key === 'i') {
            e.preventDefault();
            const italicBtn = document.querySelector('[title="Italic"]');
            this.handleToolbarAction(italicBtn);
        }
    }


    // Removed loadMockSuggestions - now using loadSuggestions() which is called in constructor

    initializeResponsive() {
        // Handle mobile sidebar behavior
        this.handleMobileResize();
        window.addEventListener('resize', () => this.handleMobileResize());

        // Close right sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (this.rightSidebar.classList.contains('active') &&
                    !this.rightSidebar.contains(e.target) &&
                    !this.sidebarToggle.contains(e.target)) {
                    this.rightSidebar.classList.remove('active');
                }
            }
        });
    }

    handleMobileResize() {
        const width = window.innerWidth;

        // Reset sidebar states on resize
        if (width > 768) {
            // Remove mobile classes
            this.rightSidebar.classList.remove('active');
            this.mainContentArea.classList.remove('sidebar-hidden');

            // Restore hidden state if it was hidden before
            const wasHidden = this.rightSidebar.classList.contains('hidden');
            if (wasHidden) {
                this.mainContentArea.classList.add('sidebar-hidden');
            }
        } else {
            // Mobile mode: always show as bottom sheet
            this.rightSidebar.classList.remove('hidden');
            this.mainContentArea.classList.remove('sidebar-hidden');
            this.sidebarToggle.classList.remove('sidebar-hidden');

            // Reset icon
            const icon = this.sidebarToggle.querySelector('i');
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
        }
    }
}

// Initialize the document editor when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.documentEditor = new DocumentEditor();
});

// Add typing indicator styles
const style = document.createElement('style');
style.textContent = `
    .typing-indicator .typing-dots {
        display: flex;
        gap: 4px;
        align-items: center;
    }
    
    .typing-indicator .typing-dots span {
        width: 6px;
        height: 6px;
        background: #6b7280;
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out;
    }
    
    .typing-indicator .typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-indicator .typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.4;
        }
        30% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }
    
    .ai-message.user {
        justify-content: flex-end;
    }
    
    .ai-message.user .ai-content {
        background: var(--primary-color);
        color: white;
        border-top-right-radius: 4px;
    }
`;
document.head.appendChild(style);
