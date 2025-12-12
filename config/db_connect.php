<?php
require_once __DIR__ . '/load_env.php';

$db_server = getenv('DB_SERVER');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
 
// Check if connection is successful, otherwise it will show an error message in the browser
if (!$conn) {
    echo "❌ Could not connect to the database!<br>";
}
?>