// Enhanced Topics Page Functionality
document.addEventListener('DOMContentLoaded', () => {
    const topicsGrid = document.getElementById('topicsGrid');
    const topicSearch = document.getElementById('topicSearch');
    const topicCards = document.querySelectorAll('.topic-card');
    const topicsCount = document.getElementById('topicsCount');

    // Debounced search functionality
    let searchTimeout;
    const handleSearch = (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = e.target.value.toLowerCase().trim();
            let visibleCount = 0;
            
            topicCards.forEach(card => {
                const topicName = card.getAttribute('data-topic').toLowerCase();
                const language = card.getAttribute('data-language').toLowerCase();
                const cardText = card.textContent.toLowerCase();
                
                const isVisible = topicName.includes(searchTerm) || 
                                language.includes(searchTerm) || 
                                cardText.includes(searchTerm);
                
                card.classList.toggle('hidden', !isVisible);
                card.classList.toggle('fade-in', isVisible);
                if (isVisible) visibleCount++;
            });
            
            if (topicsCount) {
                topicsCount.textContent = visibleCount;
            }
        }, 300); // Debounce delay
    };
    topicSearch.addEventListener('input', handleSearch);

    // Enhanced topic card interactions
    const handleCardClick = (card) => {
        // Prevent multiple rapid clicks
        if (card.classList.contains('clicked')) return;
        card.classList.add('clicked');
        
        // Add click animation
        card.style.transform = 'scale(0.98)';
        card.style.background = 'var(--light-green)';
        
        // Get topic data and redirect (simplified, no demo states)
        const language = card.getAttribute('data-language');
        const topic = card.getAttribute('data-topic');
        
        setTimeout(() => {
            console.log(`Starting ${topic} in ${language}`);
            // Actual redirect (uncomment when ready)
            // window.location.href = `../Lessons/lesson.php?lang=${encodeURIComponent(language)}&topic=${encodeURIComponent(topic)}`;
            
            // Reset for demo
            card.classList.remove('clicked');
            card.style.transform = '';
            card.style.background = '';
        }, 500);
    };

    topicCards.forEach(card => {
        card.addEventListener('click', () => handleCardClick(card));
        
        // Keyboard navigation support
        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleCardClick(card);
            }
        });
        
        // Enhanced hover effects
        card.addEventListener('mouseenter', () => {
            card.style.transition = 'all 0.3s ease';
        });
    });

    // Quick language switcher
    const createQuickLanguageSwitcher = () => {
        const languageLinks = document.querySelectorAll('.dropdown-content a');
        languageLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const lang = link.textContent.trim();
                const url = link.getAttribute('href');
                
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
                
                // Redirect after brief delay
                setTimeout(() => {
                    window.location.href = url;
                }, 800);
            });
        });
    };
    createQuickLanguageSwitcher();

    // Add loading states for better UX
    const showLoadingState = () => {
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
    };

    // Progress tracking simulation
    const initializeProgressTracking = () => {
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
    };

    // Initialize features
    showLoadingState();
    setTimeout(initializeProgressTracking, 2000);

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
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
window.addEventListener('error', (e) => {
    console.error('Error occurred:', e.error);
});

// Page visibility handling
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        console.log('Page is now visible');
    }
});