<?php
session_start();
session_unset();
session_destroy();

// Redirect to home page after logout
header("Location: /language-learning-chatbot-MIU/index.php", true, 302);
exit();
?>
