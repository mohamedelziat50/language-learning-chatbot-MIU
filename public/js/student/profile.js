document.addEventListener('DOMContentLoaded', () => {
    // --- 0. LOAD USER PROFILE DATA ---
    loadUserProfile();

    // --- 1. DARK MODE TOGGLE LOGIC ---
    const themeToggle = document.getElementById('checkbox');
    const body = document.body;

    // Check for saved theme preference or system preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        themeToggle.checked = true;
    }

    themeToggle.addEventListener('change', () => {
        if (themeToggle.checked) {
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
        } else {
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
        }
    });

    // --- 2. CARD ANIMATION (for advanced staggering/fade-in if not pure CSS) ---
    // The CSS already handles a basic staggered fade-in (see styles.css),
    // but this JS is a hook for more complex library-based animations (like GSAP)
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        // Assign a dynamic delay for a smooth staggered entrance effect
        card.style.animationDelay = `${0.1 + index * 0.1}s`;
    });

    // --- 3. BADGE HOVER INTERACTIVITY (Simple Tooltip Placeholder) ---
    const badgeItems = document.querySelectorAll('.badge-item:not(.locked)');
    badgeItems.forEach(badge => {
        const title = badge.getAttribute('data-title');
        
        badge.addEventListener('mouseover', () => {
            // In a real app, you'd use a robust tooltip library or component
            badge.setAttribute('title', title); 
        });

        badge.addEventListener('click', () => {
            // Opens 'Badge Details' modal (as requested in the prompt)
            alert(`Modal: Details for "${title}". XP: 150.`); 
        });
    });

    // --- 4. ADVANCED VISUALIZATION PLACEHOLDERS ---
    // For Section 2 (Skill Stats) and Section 6 (Line Chart), you would integrate:
    /* // Example for a Line Chart using Chart.js:
    const ctx = document.getElementById('progressLineChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            // ... data and labels
        },
        options: {
            // ... custom playful, yet clean, chart options
        }
    });
    // For 3D graphs, you'd need Three.js or similar libraries.
    */
});

/**
 * Load user profile data from backend and update UI
 */
async function loadUserProfile() {
    try {
        const response = await fetch('/language-learning-chatbot-MIU/app/controllers/languageController.php?action=profile');

        if (!response.ok) {
            console.error('Failed to load profile data');
            return;
        }

        const result = await response.json();

        if (result.success && result.data) {
            updateProfileUI(result.data);
        }
    } catch (error) {
        console.error('Error loading profile:', error);
    }
}

/**
 * Update profile UI with fetched user data
 */
function updateProfileUI(userData) {
    // Update selected language in language tags
    const learningTag = document.querySelector('.tag-learning .tag-value');
    if (learningTag) {
        // Get language name from the languages data
        fetch('/language-learning-chatbot-MIU/app/controllers/languageController.php?action=all')
            .then(response => response.json())
            .then(result => {
                if (result.success && result.data) {
                    let language = null;
                    
                    // Try to find by ID first (more efficient)
                    if (userData.selected_language_id) {
                        language = result.data.find(lang => lang.id === parseInt(userData.selected_language_id));
                    }
                    
                    // Fall back to finding by code
                    if (!language && userData.selected_language) {
                        language = result.data.find(lang => lang.code === userData.selected_language);
                    }
                    
                    if (language) {
                        learningTag.textContent = language.name;
                    }
                }
            })
            .catch(error => console.error('Error loading languages:', error));
    }
}