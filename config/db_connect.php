<?php
require_once __DIR__ . '/load_env.php';

$db_server = getenv('DB_SERVER');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_name = getenv('DB_NAME');

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
 
if ($conn) {
    echo "✅ You are connected to the database!<br>";
} else {
    echo "❌ Could not connect to the database!<br>";
}
?>