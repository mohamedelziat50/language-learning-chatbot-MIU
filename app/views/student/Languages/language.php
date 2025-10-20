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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Language - LinguaLearn</title>

    <!-- ✅ Main Styles -->
    <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/Languages/language.css">
    <!-- ✅ Student stylesheet (added) -->
    <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/student/student.css">
    <!-- ✅ Sidebar Styles -->
    <link rel="stylesheet" href="/language-learning-chatbot-MIU/public/css/student/sidebar.css">
    <!-- ✅ Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- ✅ Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- include sidebar partial (use correct relative path) -->
<?php include "../../partials/sidebar.php" ?>

<!-- wrap the entire main content in .main-content as requested -->
<div class="main-content">
    <!-- ✅ Main Layout Wrapper -->
    <div class="main-layout">
        <!-- ✅ Main Page Content -->
        <main class="content-area">

            <section class="page-header">
                <div class="header-content">
                    <h2>Choose Your Language</h2>
                    <p>Select a language to start your learning journey with our AI chatbot</p>
                    <div class="search-container">
                        <input type="text" id="languageSearch" placeholder="Search languages..." class="search-input">
                    </div>
                </div>
            </section>

            <!-- ✅ Language Grid -->
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

        </main>
    </div>
</div>

<!-- ✅ Scripts -->
<script src="/language-learning-chatbot-MIU/public/js/Languages/language.js"></script>

</body>
</html>