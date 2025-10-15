// Enhanced Topics Page Functionality
document.addEventListener('DOMContentLoaded', function() {
    const topicsGrid = document.getElementById('topicsGrid');
    const topicSearch = document.getElementById('topicSearch');
    const topicCards = document.querySelectorAll('.topic-card');
    const topicsCount = document.getElementById('topicsCount');

    // Search functionality with enhanced features
    topicSearch.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        let visibleCount = 0;
        
        topicCards.forEach(card => {
            const topicName = card.getAttribute('data-topic').toLowerCase();
            const language = card.getAttribute('data-language').toLowerCase();
            const cardText = card.textContent.toLowerCase();
            
            if (topicName.includes(searchTerm) || 
                language.includes(searchTerm) || 
                cardText.includes(searchTerm)) {
                card.classList.remove('hidden');
                card.classList.add('fade-in');
                visibleCount++;
            } else {
                card.classList.add('hidden');
                card.classList.remove('fade-in');
            }
        });
        
        // Update topics count
        if (topicsCount) {
            topicsCount.textContent = visibleCount;
        }
    });

    // Enhanced topic card interactions
    topicCards.forEach(card => {
        // Click handler with visual feedback
        card.addEventListener('click', function(e) {
            // Prevent multiple rapid clicks
            if (this.classList.contains('clicked')) return;
            this.classList.add('clicked');
            
            // Add click animation
            this.style.transform = 'scale(0.98)';
            this.style.background = 'var(--light-green)';
            
            // Get topic data
            const language = this.getAttribute('data-language');
            const topic = this.getAttribute('data-topic');
            
            // Show loading state
            const originalContent = this.innerHTML;
            this.innerHTML = `
                <div class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading ${topic}...</p>
                </div>
            `;
            
            // Simulate loading and redirect
            setTimeout(() => {
                // In real implementation, this would redirect to the chatbot
                console.log(`Starting ${topic} in ${language}`);
                
                // For now, just show a success message and redirect
                this.innerHTML = `
                    <div class="success-state">
                        <i class="fas fa-check-circle"></i>
                        <p>Starting ${topic}!</p>
                    </div>
                `;
                
                setTimeout(() => {
                    // Redirect to chatbot (commented for now as per your note)
                    // window.location.href = `../Chatbot/chatbot.php?lang=${encodeURIComponent(language)}&topic=${encodeURIComponent(topic)}`;
                    
                    // Reset card for demo purposes
                    this.innerHTML = originalContent;
                    this.classList.remove('clicked');
                    this.style.transform = '';
                    this.style.background = '';
                }, 1000);
                
            }, 1500);
        });

        // Keyboard navigation support
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });

        // Enhanced hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });

    // Quick language switcher
    function createQuickLanguageSwitcher() {
        const languageLinks = document.querySelectorAll('.dropdown-content a');
        languageLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const lang = this.textContent.trim();
                const url = this.getAttribute('href');
                
                // Show loading overlay
                const overlay = document.createElement('div');
                overlay.className = 'page-transition';
                overlay.innerHTML = `
                    <div class="transition-content">
                        <i class="fas fa-language fa-spin"></i>
                        <p>Loading ${lang} topics...</p>
                    </div>
                `;
                document.body.appendChild(overlay);
                
                // Redirect after brief delay for smooth transition
                setTimeout(() => {
                    window.location.href = url;
                }, 800);
            });
        });
    }
    createQuickLanguageSwitcher();

    // Add loading states for better UX
    function showLoadingState() {
        const loader = document.createElement('div');
        loader.className = 'page-loader';
        loader.innerHTML = `
            <div class="loader-content">
                <div class="loader-spinner"></div>
                <p>Preparing your learning experience...</p>
            </div>
        `;
        document.body.appendChild(loader);
        
        setTimeout(() => {
            loader.remove();
        }, 1500);
    }

    // Progress tracking simulation
    function initializeProgressTracking() {
        const statNumbers = document.querySelectorAll('.stat-number');
        statNumbers.forEach(stat => {
            const target = parseInt(stat.textContent);
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    stat.textContent = target;
                    clearInterval(timer);
                } else {
                    stat.textContent = Math.floor(current);
                }
            }, 30);
        });
    }

    // Initialize features
    showLoadingState();
    setTimeout(initializeProgressTracking, 2000);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Focus search on Ctrl+K / Cmd+K
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            topicSearch.focus();
        }
        
        // Clear search on Escape
        if (e.key === 'Escape' && document.activeElement === topicSearch) {
            topicSearch.value = '';
            topicSearch.dispatchEvent(new Event('input'));
            topicSearch.blur();
        }
    });

    // Add CSS for loading states
    const style = document.createElement('style');
    style.textContent = `
        .loading-state, .success-state {
            text-align: center;
            padding: 2rem 1rem;
        }
        
        .loading-state i, .success-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .loading-state i {
            color: var(--primary-green);
        }
        
        .success-state i {
            color: var(--accent-green);
        }
        
        .page-transition {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .transition-content {
            text-align: center;
        }
        
        .transition-content i {
            font-size: 3rem;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }
        
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9998;
        }
        
        .loader-content {
            text-align: center;
        }
        
        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--light-green);
            border-top: 4px solid var(--primary-green);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .topic-card.clicked {
            pointer-events: none;
        }
    `;
    document.head.appendChild(style);
});

// Error handling
window.addEventListener('error', function(e) {
    console.error('Error occurred:', e.error);
});

// Page visibility handling
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Page became visible again
        console.log('Page is now visible');
    }
});