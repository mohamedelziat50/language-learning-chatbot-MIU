document.addEventListener('DOMContentLoaded', () => {
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