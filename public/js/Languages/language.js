// Language selection and search functionality
document.addEventListener('DOMContentLoaded', function() {
    const languageGrid = document.getElementById('languageGrid');
    const languageSearch = document.getElementById('languageSearch');
    const languageCards = document.querySelectorAll('.language-card');

    // Search functionality
    languageSearch.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        
        languageCards.forEach(card => {
            const languageName = card.getAttribute('data-language');
            const displayName = card.querySelector('.language-name').textContent.toLowerCase();
            
            if (displayName.includes(searchTerm) || languageName.includes(searchTerm)) {
                card.classList.remove('hidden');
                card.classList.add('fade-in');
            } else {
                card.classList.add('hidden');
            }
        });
    });

    // Language selection with smooth transition
    languageCards.forEach(card => {
        card.addEventListener('click', function() {
            const languageName = this.querySelector('.language-name').textContent;
            
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            // Show selection confirmation
            const selectBtn = this.querySelector('.select-btn');
            const originalText = selectBtn.textContent;
            selectBtn.textContent = 'Selected!';
            selectBtn.style.background = '#0d8a42';
            
            setTimeout(() => {
                selectBtn.textContent = originalText;
                selectBtn.style.background = '';
                
                // Redirect to topics page
                window.location.href = `../Topics/Topics.php?lang=${encodeURIComponent(languageName)}`;
            }, 800);
        });
    });

    // Add hover sound effect (optional)
    languageCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            languageSearch.value = '';
            languageSearch.dispatchEvent(new Event('input'));
            languageSearch.blur();
        }
    });

    // Focus search on Ctrl+K / Cmd+K
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            languageSearch.focus();
        }
    });
});

// Add loading animation
function showLoadingAnimation() {
    const loader = document.createElement('div');
    loader.className = 'page-loader';
    loader.innerHTML = `
        <div class="loader-spinner"></div>
        <p>Loading language content...</p>
    `;
    loader.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `;
    document.body.appendChild(loader);
    
    setTimeout(() => {
        loader.remove();
    }, 1500);
}

// Enhanced error handling
window.addEventListener('error', function(e) {
    console.error('Error occurred:', e.error);
});