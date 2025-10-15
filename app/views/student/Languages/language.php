<?php
$languages = [
    ["name" => "French", "flag" => "https://flagcdn.com/w320/fr.png"],
    ["name" => "Spanish", "flag" => "https://flagcdn.com/w320/es.png"],
    ["name" => "German", "flag" => "https://flagcdn.com/w320/de.png"],
    ["name" => "English", "flag" => "https://flagcdn.com/w320/gb.png"],
    ["name" => "Italian", "flag" => "https://flagcdn.com/w320/it.png"],
    ["name" => "Arabic", "flag" => "https://flagcdn.com/w320/eg.png"],
    ["name" => "Japanese", "flag" => "https://flagcdn.com/w320/jp.png"],
    ["name" => "Chinese", "flag" => "https://flagcdn.com/w320/cn.png"],
    ["name" => "Portuguese", "flag" => "https://flagcdn.com/w320/pt.png"],
    ["name" => "Russian", "flag" => "https://flagcdn.com/w320/ru.png"],
    ["name" => "Korean", "flag" => "https://flagcdn.com/w320/kr.png"],
    ["name" => "Hindi", "flag" => "https://flagcdn.com/w320/in.png"],
    ["name" => "Turkish", "flag" => "https://flagcdn.com/w320/tr.png"],
    ["name" => "Dutch", "flag" => "https://flagcdn.com/w320/nl.png"],
    ["name" => "Swedish", "flag" => "https://flagcdn.com/w320/se.png"]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Language - LinguaLearn</title>
    <link rel="stylesheet" href="../../../public/css/Languages/language.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include '../partials/sidebar.php'; ?>
<!-- ✅ Enhanced Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <h1 class="logo">🌍 LinguaLearn</h1>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="nav-link">Home</a></li>
            <li><a href="profile.php" class="nav-link">Profile</a></li>
            <li><a href="about.php" class="nav-link">About</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li>
        </ul>
    </div>
</nav>

<!-- ✅ Enhanced Page Header -->
<section class="page-header">
    <div class="header-content">
        <h2>Choose Your Language</h2>
        <p>Select a language to start your learning journey with our AI chatbot</p>
        <div class="search-container">
            <input type="text" id="languageSearch" placeholder="Search languages..." class="search-input">
        </div>
    </div>
</section>

<!-- ✅ Language Grid with 5 items per row -->
<div class="language-grid-container">
    <div class="language-grid" id="languageGrid">
        <?php foreach ($languages as $lang): ?>
            <div class="language-card" data-language="<?php echo strtolower($lang['name']); ?>">
                <div class="flag-container">
                    <img src="<?php echo $lang['flag']; ?>" alt="<?php echo $lang['name']; ?> Flag" class="flag-image">
                </div>
                <h3 class="language-name"><?php echo $lang['name']; ?></h3>
                <div class="select-btn">Start Learning</div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ✅ Enhanced Footer -->
<footer class="footer">
    <div class="footer-content">
        <p>© 2025 LinguaLearn | Created by Team AI Language Learning</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Support</a>
        </div>
    </div>
</footer>

<script src="../../../public/js/Languages/language.js"></script>
</body>
</html>