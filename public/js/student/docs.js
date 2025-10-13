/**
 * Docs Page JavaScript - Grammarly-inspired functionality
 * Handles search, animations, and interactive features
 */

class DocsManager {
    constructor() {
        this.searchInput = null;
        this.docCards = [];
        this.allDocs = [];
        this.currentFilter = 'all';
        this.init();
    }

    init() {
        this.setupElements();
        this.setupEventListeners();
        this.loadDocuments();
        this.setupAnimations();
    }

    setupElements() {
        this.searchInput = document.querySelector('.docs-search input');
        this.docCards = document.querySelectorAll('.doc-card');
        this.allDocs = Array.from(this.docCards);
        
        // Debug logging
        console.log('DocsManager initialized:', {
            searchInput: !!this.searchInput,
            docCards: this.docCards.length,
            allDocs: this.allDocs.length
        });
    }

    setupEventListeners() {
        // Search functionality
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });

            // Clear search on escape
            this.searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.clearSearch();
                }
            });
        } else {
            console.warn('Search input not found');
        }

        // Card hover animations
        this.docCards.forEach(card => {
            this.setupCardHover(card);
        });

        // New document button
        const newDocBtn = document.querySelector('.btn-new-doc');
        if (newDocBtn) {
            newDocBtn.addEventListener('click', () => {
                this.createNewDocument();
            });
        } else {
            console.warn('New document button not found');
        }

        // Upload button
        const uploadBtn = document.querySelector('.btn-upload');
        if (uploadBtn) {
            uploadBtn.addEventListener('click', () => {
                this.handleUpload();
            });
        } else {
            console.warn('Upload button not found');
        }

        // Document menu buttons
        const menuButtons = document.querySelectorAll('.doc-menu');
        if (menuButtons.length > 0) {
            menuButtons.forEach(menuBtn => {
                menuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleDocumentMenu(e.target);
                });
            });
        }

        // Click outside to close menus
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.doc-menu')) {
                this.closeAllMenus();
            }
        });
    }

    setupCardHover(card) {
        card.addEventListener('mouseenter', () => {
            this.animateCardHover(card, true);
        });

        card.addEventListener('mouseleave', () => {
            this.animateCardHover(card, false);
        });

        // Click to open document
        card.addEventListener('click', (e) => {
            if (!e.target.closest('.doc-menu')) {
                this.openDocument(card);
            }
        });
    }

    animateCardHover(card, isHovering) {
        if (isHovering) {
            card.style.transform = 'translateY(-4px)';
            card.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
            card.style.borderColor = '#027e6f';
        } else {
            card.style.transform = 'translateY(0)';
            card.style.boxShadow = '0 2px 6px rgba(0, 0, 0, 0.08)';
            card.style.borderColor = '#e5e5e5';
        }
    }

    setupAnimations() {
        // Staggered animation for cards
        this.docCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    }

    handleSearch(query) {
        const searchTerm = query.toLowerCase().trim();
        
        if (searchTerm === '') {
            this.showAllDocuments();
            return;
        }

        this.docCards.forEach(card => {
            try {
                const titleElement = card.querySelector('.doc-title');
                const previewElement = card.querySelector('.doc-preview');
                const tagElement = card.querySelector('.doc-tag');
                
                if (!titleElement || !previewElement || !tagElement) {
                    console.warn('Missing elements in doc card:', card);
                    return;
                }
                
                const title = titleElement.textContent.toLowerCase();
                const preview = previewElement.textContent.toLowerCase();
                const tag = tagElement.textContent.toLowerCase();
                
                const matches = title.includes(searchTerm) || 
                              preview.includes(searchTerm) || 
                              tag.includes(searchTerm);
                
                if (matches) {
                    card.style.display = 'block';
                    this.highlightSearchTerm(card, searchTerm);
                } else {
                    card.style.display = 'none';
                }
            } catch (error) {
                console.error('Error processing search for card:', error, card);
            }
        });

        this.updateSearchResults(searchTerm);
    }

    highlightSearchTerm(card, searchTerm) {
        const elements = card.querySelectorAll('.doc-title, .doc-preview');
        
        elements.forEach(element => {
            const text = element.textContent;
            const highlightedText = text.replace(
                new RegExp(`(${searchTerm})`, 'gi'),
                '<span class="search-highlight">$1</span>'
            );
            element.innerHTML = highlightedText;
        });
    }

    clearSearch() {
        if (this.searchInput) {
            this.searchInput.value = '';
            this.showAllDocuments();
            this.removeHighlights();
        }
    }

    showAllDocuments() {
        this.docCards.forEach(card => {
            card.style.display = 'block';
        });
        this.removeHighlights();
    }

    removeHighlights() {
        document.querySelectorAll('.search-highlight').forEach(highlight => {
            const parent = highlight.parentNode;
            parent.innerHTML = parent.textContent;
        });
    }

    updateSearchResults(searchTerm) {
        const visibleCards = Array.from(this.docCards).filter(card => 
            card.style.display !== 'none'
        );
        
        // Update section titles based on search results
        const sections = document.querySelectorAll('.docs-section');
        sections.forEach(section => {
            const title = section.querySelector('.docs-section-title');
            if (title) {
                if (searchTerm) {
                    title.textContent = `Search results for "${searchTerm}" (${visibleCards.length} found)`;
                } else {
                    // Restore original titles
                    const sectionIndex = Array.from(sections).indexOf(section);
                    const originalTitles = ['Yesterday', 'Earlier'];
                    title.textContent = originalTitles[sectionIndex] || 'Documents';
                }
            }
        });
    }

    createNewDocument() {
        // Simulate creating a new document
        console.log('Creating new document...');
        
        // Show loading state
        this.showLoadingState();
        
        // Simulate API call
        setTimeout(() => {
            this.hideLoadingState();
            this.showNotification('New document created successfully!', 'success');
            
            // In a real implementation, you would redirect to the document editor
            // window.location.href = '/editor.php?new=true';
        }, 1000);
    }

    handleUpload() {
        // Create file input for upload
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.pdf,.doc,.docx,.txt';
        fileInput.multiple = true;
        
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            this.processUploadedFiles(files);
        });
        
        fileInput.click();
    }

    processUploadedFiles(files) {
        if (files.length === 0) return;
        
        this.showLoadingState();
        
        // Simulate file processing
        setTimeout(() => {
            this.hideLoadingState();
            this.showNotification(`${files.length} file(s) uploaded successfully!`, 'success');
        }, 1500);
    }

    openDocument(card) {
        const docId = card.dataset.docId || '1';
        console.log(`Opening document ${docId}...`);
        
        // In a real implementation, you would redirect to the document editor
        // window.location.href = `/editor.php?id=${docId}`;
        
        // Show notification for opening document
        this.showNotification('Opening document...', 'info');
    }

    toggleDocumentMenu(menuBtn) {
        // Close other menus first
        this.closeAllMenus();
        
        // Create menu dropdown
        const menu = this.createDocumentMenu(menuBtn);
        document.body.appendChild(menu);
        
        // Position menu directly under the button
        const rect = menuBtn.getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.top = `${rect.bottom + 4}px`;
        menu.style.left = `${rect.left}px`;
        menu.style.zIndex = '1000';
        
        // Show menu with animation
        menu.style.opacity = '0';
        menu.style.transform = 'translateY(-10px)';
        requestAnimationFrame(() => {
            menu.style.transition = 'all 0.2s ease';
            menu.style.opacity = '1';
            menu.style.transform = 'translateY(0)';
        });
    }

    createDocumentMenu(menuBtn) {
        const menu = document.createElement('div');
        menu.className = 'doc-menu-dropdown';
        menu.style.cssText = `
            background: white;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 8px 0;
            min-width: 160px;
        `;
        
        const menuItems = [
            { text: 'Open', icon: '📄', action: () => this.openDocument(menuBtn.closest('.doc-card')) },
            { text: 'Rename', icon: '✏️', action: () => this.renameDocument(menuBtn.closest('.doc-card')) },
            { text: 'Duplicate', icon: '📋', action: () => this.duplicateDocument(menuBtn.closest('.doc-card')) },
            { text: 'Share', icon: '🔗', action: () => this.shareDocument(menuBtn.closest('.doc-card')) },
            { text: 'Delete', icon: '🗑️', action: () => this.deleteDocument(menuBtn.closest('.doc-card')) }
        ];
        
        menuItems.forEach(item => {
            const menuItem = document.createElement('div');
            menuItem.style.cssText = `
                padding: 8px 16px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                color: #333;
                transition: background-color 0.2s ease;
            `;
            menuItem.innerHTML = `${item.icon} ${item.text}`;
            
            menuItem.addEventListener('mouseenter', () => {
                menuItem.style.backgroundColor = '#f8f9fa';
            });
            
            menuItem.addEventListener('mouseleave', () => {
                menuItem.style.backgroundColor = 'transparent';
            });
            
            menuItem.addEventListener('click', (e) => {
                e.stopPropagation();
                item.action();
                menu.remove();
            });
            
            menu.appendChild(menuItem);
        });
        
        return menu;
    }

    closeAllMenus() {
        document.querySelectorAll('.doc-menu-dropdown').forEach(menu => {
            menu.remove();
        });
    }

    renameDocument(card) {
        const titleElement = card.querySelector('.doc-title');
        const currentTitle = titleElement.textContent;
        
        const newTitle = prompt('Enter new document name:', currentTitle);
        if (newTitle && newTitle !== currentTitle) {
            titleElement.textContent = newTitle;
            this.showNotification('Document renamed successfully!', 'success');
        }
    }

    duplicateDocument(card) {
        console.log('Duplicating document...');
        this.showNotification('Document duplicated successfully!', 'success');
    }

    shareDocument(card) {
        console.log('Sharing document...');
        this.showNotification('Share link copied to clipboard!', 'success');
    }

    deleteDocument(card) {
        if (confirm('Are you sure you want to delete this document?')) {
            card.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                card.remove();
                this.showNotification('Document deleted successfully!', 'success');
            }, 300);
        }
    }

    loadDocuments() {
        // Simulate loading documents
        console.log('Loading documents...');
        
        // Check if we have any document cards
        if (this.docCards.length === 0) {
            console.warn('No document cards found on the page');
        } else {
            console.log(`Found ${this.docCards.length} document cards`);
        }
        
        // In a real implementation, you would fetch from an API
        // fetch('/api/documents')
        //     .then(response => response.json())
        //     .then(docs => this.renderDocuments(docs));
    }

    showLoadingState() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'docs-loading';
        loadingDiv.innerHTML = '<div class="docs-loading-spinner"></div>';
        document.querySelector('.main-content').appendChild(loadingDiv);
    }

    hideLoadingState() {
        const loadingDiv = document.querySelector('.docs-loading');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }

    showNotification(message, type = 'info') {
        // Use the global notification system
        if (window.NotificationManager) {
            window.NotificationManager.show(message, type);
        } else {
            console.warn('NotificationManager not available');
        }
    }
}

// Add CSS animations for document cards only
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.95); }
    }
`;
document.head.appendChild(style);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    try {
        new DocsManager();
    } catch (error) {
        console.error('Error initializing DocsManager:', error);
    }
});

// Export for potential external use
window.DocsManager = DocsManager;
