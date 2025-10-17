class DictionaryManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.focusSearchBox();
    }
    
    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.searchWords.bind(this), 300));
        }
        
        // Language filter
        const languageFilter = document.getElementById('languageFilter');
        if (languageFilter) {
            languageFilter.addEventListener('change', this.filterByLanguage.bind(this));
        }
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', this.handleKeyboardShortcuts.bind(this));
    }
    
    focusSearchBox() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            setTimeout(() => {
                searchInput.focus();
            }, 100);
        }
    }
    
    searchWords() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
        const wordRows = document.querySelectorAll('.word-row');
        const languageSections = document.querySelectorAll('.language-section');
        
        let visibleLanguages = new Set();
        let totalVisibleWords = 0;
        
        wordRows.forEach(row => {
            const english = row.querySelector('.word-english').textContent.toLowerCase();
            const translation = row.querySelector('.word-translation').textContent.toLowerCase();
            const topic = row.querySelector('.word-topic').textContent.toLowerCase();
            const language = row.dataset.language;
            
            const matches = english.includes(searchTerm) || 
                            translation.includes(searchTerm) || 
                            topic.includes(searchTerm);
            
            if (matches) {
                row.classList.remove('hidden');
                visibleLanguages.add(language);
                totalVisibleWords++;
                
                // Highlight matching text
                this.highlightText(row.querySelector('.word-english'), searchTerm);
                this.highlightText(row.querySelector('.word-translation'), searchTerm);
                this.highlightText(row.querySelector('.word-topic'), searchTerm);
            } else {
                row.classList.add('hidden');
                this.removeHighlights(row);
            }
        });
        
        // Show/hide language sections based on visible words
        languageSections.forEach(section => {
            const language = section.dataset.language;
            if (visibleLanguages.has(language) || searchTerm === '') {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        });
        
        this.updateSearchStats(totalVisibleWords, visibleLanguages.size);
    }
    
    highlightText(element, searchTerm) {
        if (!searchTerm) {
            this.removeHighlights(element);
            return;
        }
        
        const text = element.textContent;
        const regex = new RegExp(`(${this.escapeRegex(searchTerm)})`, 'gi');
        const highlighted = text.replace(regex, '<span class="highlight">$1</span>');
        element.innerHTML = highlighted;
    }
    
    removeHighlights(element) {
        const highlights = element.querySelectorAll('.highlight');
        highlights.forEach(highlight => {
            const parent = highlight.parentNode;
            parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
            parent.normalize();
        });
    }
    
    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    
    filterByLanguage() {
        const selectedLanguage = document.getElementById('languageFilter').value;
        const languageSections = document.querySelectorAll('.language-section');
        const wordRows = document.querySelectorAll('.word-row');
        
        languageSections.forEach(section => {
            const language = section.dataset.language;
            
            if (selectedLanguage === 'all' || selectedLanguage === language) {
                section.classList.remove('hidden');
                
                // Show all words in this language section
                const sectionWords = section.querySelectorAll('.word-row');
                sectionWords.forEach(row => {
                    if (!row.classList.contains('hidden-from-search')) {
                        row.classList.remove('hidden');
                    }
                });
            } else {
                section.classList.add('hidden');
                
                // Hide all words in this language section
                const sectionWords = section.querySelectorAll('.word-row');
                sectionWords.forEach(row => {
                    row.classList.add('hidden');
                });
            }
        });
        
        // If a search is active, re-apply search filter
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm) {
            this.searchWords();
        }
    }
    
    updateSearchStats(visibleWords, visibleLanguages) {
        // You can add a stats update here if needed
        console.log(`Showing ${visibleWords} words across ${visibleLanguages} languages`);
    }
    
    handleKeyboardShortcuts(event) {
        // Ctrl+F or Cmd+F to focus search
        if ((event.ctrlKey || event.metaKey) && event.key === 'f') {
            event.preventDefault();
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Escape to clear search
        if (event.key === 'Escape') {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                this.searchWords();
            }
        }
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new DictionaryManager();
});

// Global functions for inline event handlers
function searchWords() {
    const manager = new DictionaryManager();
    manager.searchWords();
}

function filterByLanguage(language) {
    const manager = new DictionaryManager();
    document.getElementById('languageFilter').value = language;
    manager.filterByLanguage();
}