<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinguaBot - Your AI Language Partner</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/home_page/home_page.css">
    <link rel="stylesheet" href="./public/css/popup/popup.css">

</head>
<body>

    <div id="app">
        <?php include __DIR__ . '/app/views/partials/navbar.php'; ?>

        <!-- Main Content -->
        <main style="flex-grow: 1;">

            <!-- Hero Section -->
            <section class="hero-section container">

                <!-- Left Column: Marketing Copy -->
                <div class="hero-content">
                    <span class="hero-badge">Practice without fear.</span>
                    <h1 class="hero-title">
                        Fluency is just a <span class="highlight">chat</span> away.
                    </h1>
                    <p class="hero-subtitle">
                        LinguaBot is your AI language partner that gives you real-time feedback, grammar corrections, and confidence-boosting conversations. Stop studying, start speaking.
                    </p>
                    <div class="hero-actions">
                        <a href="#cta" class="btn-primary">Start Your Free Trial</a>
                        <a href="#features" class="btn-secondary">Explore Features</a>
                    </div>
                </div>

                <!-- Right Column: Creative Floating Video -->
                <div class="showcase-column">
                    <!-- Decorative background circles -->
                    <div class="bg-circle bg-circle-1"></div>
                    <div class="bg-circle bg-circle-2"></div>

                    <!-- Video wrapper with floating badge -->
                    <div class="video-wrapper">
                        <video
                        src="/language-learning-chatbot-MIU/public/videos/home_page_video.mp4"
                        type="video/mp4"
                            autoplay
                            loop
                            muted
                            playsinline
                            alt="Demo video showing real-time language correction"
                        >
                            Your browser does not support the video tag.
                        </video>

                        <!-- Floating badge -->
                        <div class="floating-badge">
                            <div class="pulse-dot"></div>
                            <span>Real-time Corrections</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Feature Showcase Section -->
            <section id="features" class="features-section">
                <div class="container">
                    <h2>Learn Smarter, Not Harder</h2>
                    <p class="features-subtitle">
                        Our platform is engineered to turn your mistakes into immediate, meaningful learning opportunities, focusing on practical application.
                    </p>

                    <div class="features-grid">

                        <!-- Feature 1 -->
                        <div class="feature-card">
                            <div class="feature-icon teal-gradient">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.001 12.001 0 0012 21a12.001 12.001 0 008.618-14.016z"></path></svg>
                            </div>
                            <h3>Real-time Grammar Check</h3>
                            <p>Catch mistakes instantly with clear, contextual explanations. We cover conjugation, syntax, and proper usage in a conversational flow.</p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="feature-card">
                            <div class="feature-icon blue-gradient">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h3>Dynamic Vocabulary</h3>
                            <p>Tired of repeating the same words? LinguaBot suggests richer, more natural vocabulary alternatives based on your current conversation.</p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="feature-card">
                            <div class="feature-icon purple-gradient">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.444 3-6.5S13.657 6 12 6s-3 4.444-3 6.5S10.343 21 12 21z"></path></svg>
                            </div>
                            <h3>Cultural Immersion</h3>
                            <p>Practice speaking in various roles (tourist, colleague, friend) and learn local idioms and cultural context directly within the chat.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Call to Action Section -->
            <section id="cta" class="cta-footer-section">
                <div class="container">
                    <h2>Ready to chat your way to fluency?</h2>
                    <p>
                        Join thousands of dedicated language learners who are replacing textbook drills with engaging, personalized conversation.
                    </p>
                    <a href="#" class="cta-main-button">Start Your 7-Day Free Trial</a>
                </div>
            </section>
        </main>

        <?php include __DIR__ . '/app/views/partials/footer.php'; ?>
    </div>

    <script src="./public/js/home_page/home_page.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php include __DIR__ . '/app/views/partials/auth_popup.php'; ?>

</body>
</html>
