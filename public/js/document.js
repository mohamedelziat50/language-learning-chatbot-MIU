// Document Editor JavaScript - Grammarly-inspired functionality

class DocumentEditor {
    constructor() {
        this.currentTab = 'ai';
        this.isAutoSaving = false;
        this.saveTimeout = null;
        this.suggestions = [];
        this.aiMessages = [];
        
        this.initializeElements();
        this.bindEvents();
        this.initializeEditor();
        this.loadMockSuggestions();
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
        this.sidebar = document.querySelector('.right-sidebar');
        this.sidebarToggle = document.getElementById('sidebar-toggle');
        // Left sidebar elements
        this.leftSidebar = document.getElementById('left-sidebar');
        this.lsViews = {
            stats: document.getElementById('ls-stats'),
            versions: document.getElementById('ls-versions')
        };
        this.docMenuBtn = document.getElementById('doc-menu-btn');
        this.docMenu = document.getElementById('doc-menu');
        
        // AI elements
        this.aiInput = document.getElementById('ai-text-input');
        this.aiSendBtn = document.getElementById('ai-send-btn');
        this.aiMessages = document.getElementById('ai-messages');
        this.clearChatBtn = document.getElementById('clear-chat-btn');
        
        // Plagiarism elements
        this.checkPlagiarismBtn = document.getElementById('check-plagiarism-btn');
        this.plagiarismPlaceholder = document.querySelector('.plag-placeholder');
        
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
            this.analyzeContent();
            this.renderStats(); // Update stats live
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

        // Sidebar toggle
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

        // Doc menu toggle
        this.docMenuBtn.addEventListener('click', () => {
            const isOpen = this.docMenu.classList.toggle('open');
            if (isOpen) {
                document.addEventListener('click', this.closeMenuOnOutside, { once: true });
            }
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

    renderOutline() {
        const text = this.documentEditor.value;
        const lines = text.split(/\n/);
        const items = [];
        lines.forEach((line, idx) => {
            const trimmed = line.trim();
            if (!trimmed) return;
            // Treat lines starting with #, ##, ### or ALL CAPS as headings
            let level = 2;
            if (/^#{1,3}\s+/.test(trimmed)) level = trimmed.match(/^#{1,3}/)[0].length;
            else if (/^[A-Z][A-Z\s\-:,]{3,}$/.test(trimmed)) level = 1;
            else return;
            items.push({ level, text: trimmed.replace(/^#{1,3}\s+/, ''), index: idx });
        });
        if (items.length === 0) {
            this.lsViews.outline.innerHTML = '<p class="muted">No headings found. Add lines starting with #, ##, or ###.</p>';
            return;
        }
        this.lsViews.outline.innerHTML = items.map(i => `
            <div class="outline-item">
                <span class="lvl-${i.level}">${i.text}</span>
            </div>
        `).join('');
        // Click to scroll (approximate by counting newlines)
        Array.from(this.lsViews.outline.children).forEach((el, i) => {
            el.addEventListener('click', () => {
                this.scrollEditorToLine(items[i].index);
            });
        });
    }

    scrollEditorToLine(lineIndex) {
        const text = this.documentEditor.value;
        const lines = text.split(/\n/);
        let caretPos = 0;
        for (let i = 0; i < lineIndex; i++) caretPos += lines[i].length + 1;
        this.documentEditor.focus();
        this.documentEditor.setSelectionRange(caretPos, caretPos);
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
        const mainContentArea = document.querySelector('.main-content-area');
        const isHidden = this.sidebar.classList.toggle('hidden');
        const icon = this.sidebarToggle.querySelector('i');
        
        if (isHidden) {
            // Hide sidebar and expand main content
            mainContentArea.classList.add('sidebar-hidden');
            this.sidebarToggle.classList.add('sidebar-hidden');
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-left');
            this.sidebarToggle.title = 'Show Assistant';
        } else {
            // Show sidebar and restore two-column layout
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

    performAutoSave() {
        this.isAutoSaving = true;
        
        // Simulate save operation
        setTimeout(() => {
            this.saveContent();
            this.updateSaveStatus('All changes saved', 'saved');
            this.isAutoSaving = false;
        }, 500);
    }

    saveContent() {
        const content = {
            title: this.documentTitle.value,
            content: this.documentEditor.value,
            lastModified: new Date().toISOString()
        };

        // Save to localStorage (in real app, this would be sent to server)
        localStorage.setItem('document_content', JSON.stringify(content));

        // Append to versions (keep last 10)
        const saved = localStorage.getItem('document_versions');
        const versions = saved ? JSON.parse(saved) : [];
        versions.push({ timestamp: Date.now(), title: content.title, preview: content.content.slice(0, 120) });
        while (versions.length > 10) versions.shift();
        localStorage.setItem('document_versions', JSON.stringify(versions));
    }

    loadSavedContent() {
        const saved = localStorage.getItem('document_content');
        if (saved) {
            try {
                const content = JSON.parse(saved);
                this.documentTitle.value = content.title || 'Untitled Document';
                this.documentEditor.value = content.content || '';
            } catch (e) {
                console.error('Error loading saved content:', e);
            }
        }
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

    analyzeContent() {
        // Mock content analysis for suggestions
        const content = this.documentEditor.value;
        
        if (content.length < 10) {
            this.clearSuggestions();
            return;
        }

        // Generate mock suggestions based on content
        this.generateMockSuggestions(content);
    }

    generateMockSuggestions(content) {
        const suggestions = [];
        
        // Mock grammar suggestions
        if (content.includes('its') && !content.includes("it's")) {
            suggestions.push({
                category: 'correctness',
                type: 'grammar',
                text: 'Consider using "it\'s" instead of "its"',
                description: 'Use "it\'s" when you mean "it is" or "it has"',
                position: content.indexOf('its')
            });
        }

        if (content.includes('alot')) {
            suggestions.push({
                category: 'correctness',
                type: 'spelling',
                text: 'Consider using "a lot" instead of "alot"',
                description: '"A lot" should be written as two words',
                position: content.indexOf('alot')
            });
        }

        // Mock clarity suggestions
        if (content.includes('very') && content.length > 50) {
            suggestions.push({
                category: 'clarity',
                type: 'wordiness',
                text: 'Consider removing "very" for stronger writing',
                description: 'Stronger adjectives can replace "very" + adjective',
                position: content.indexOf('very')
            });
        }

        // Mock engagement suggestions
        if (content.length > 100 && !content.includes('?')) {
            suggestions.push({
                category: 'engagement',
                type: 'interaction',
                text: 'Consider adding a question to engage readers',
                description: 'Questions can make your writing more interactive',
                position: content.length - 1
            });
        }

        this.displaySuggestions(suggestions);
    }

    displaySuggestions(suggestions) {
        // Clear existing suggestions
        this.clearSuggestions();

        // Flat list for refined UI
        const listContainer = document.getElementById('review-suggestions');
        let proCount = 0;
        suggestions.forEach(suggestion => {
            const suggestionElement = this.createSuggestionElement(suggestion);
            listContainer.appendChild(suggestionElement);
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
        const category = suggestion.category.charAt(0).toUpperCase() + suggestion.category.slice(1);
        div.innerHTML = `
            <div class="meta"><i class="fas fa-shield-alt"></i> ${category} · ${suggestion.type}</div>
            <div class="text">${suggestion.text}</div>
            <div class="suggestion-description">${suggestion.description}</div>
            <div class="actions">
                <button class="accept" onclick="documentEditor.acceptSuggestion(this)">Accept</button>
                <button class="dismiss" onclick="documentEditor.dismissSuggestion(this)">Dismiss</button>
            </div>
        `;

        return div;
    }

    acceptSuggestion(button) {
        // Mock accepting suggestion
        const suggestionItem = button.closest('.suggestion-item');
        suggestionItem.style.opacity = '0.5';
        suggestionItem.style.pointerEvents = 'none';
        
        // Update suggestion count
        this.updateSuggestionCount();
        
        // Show success message
        this.showNotification('Suggestion accepted', 'success');
    }

    dismissSuggestion(button) {
        // Mock dismissing suggestion
        const suggestionItem = button.closest('.suggestion-item');
        suggestionItem.remove();
        
        // Update suggestion count
        this.updateSuggestionCount();
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
        this.aiMessages.innerHTML = '';
        this.showNotification('Chat cleared', 'info');
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
        this.showNotification(`${type} formatting applied`, 'info');
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

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '12px 16px',
            borderRadius: '8px',
            color: 'white',
            fontSize: '14px',
            fontWeight: '500',
            zIndex: '10000',
            opacity: '0',
            transform: 'translateX(100%)',
            transition: 'all 0.3s ease'
        });
        
        // Set background color based on type
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        notification.style.backgroundColor = colors[type] || colors.info;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    closeMenuOnOutside = (e) => {
        if (!this.docMenu.contains(e.target) && e.target !== this.docMenuBtn) {
            this.docMenu.classList.remove('open');
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
