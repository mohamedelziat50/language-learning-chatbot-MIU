<?php
require_once '../../../models/languageModel.php';
$languages = Language::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Language - LinguaLearn</title>

    <!-- Styles -->
    <link rel="stylesheet" href="../../../public/css/Languages/language.css">
    <link rel="stylesheet" href="../../../public/css/student/student.css">
    <link rel="stylesheet" href="../../../public/css/student/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include "../../partials/sidebar.php" ?>

<div class="main-content">
    <div class="main-layout">
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

            <div class="language-grid-container">
                <div class="language-grid" id="languageGrid">

                    <?php foreach ($languages as $lang): ?>
                        <div class="language-card" data-language="<?php echo strtolower($lang->getName()); ?>">
                            <div class="flag-container">
                                <img src="<?php echo $lang->getFlag(); ?>" 
                                    alt="<?php echo $lang->getName(); ?> Flag"
                                    class="flag-image">
                            </div>
                            <h3 class="language-name"><?php echo $lang->getName(); ?></h3>
                            <div class="select-btn">Start Learning</div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="../../../public/js/Languages/language.js"></script>

</body>
</html>
