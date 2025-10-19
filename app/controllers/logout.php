<?php
session_start();
session_unset();
session_destroy();

// Redirect to home page after logout
header("Location: /language-learning-chatbot-MIU/app/views/home_page/home_page.php");
exit();
?>
