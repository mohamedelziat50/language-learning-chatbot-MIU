<?php
require_once __DIR__ . '/load_env.php';

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT');


$conn = mysqli_connect($host, $user, $pass, $dbname, $port);
 
// Check if connection is successful, otherwise it will show an error message in the browser
if (!$conn) {
    echo "❌ Could not connect to the database!<br>";
}
?>