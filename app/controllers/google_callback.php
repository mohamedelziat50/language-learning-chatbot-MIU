<?php
session_start();
include(__DIR__ . '/../../config/db_connect.php');
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$client_id = $_ENV['GOOGLE_CLIENT_ID'];
$client_secret = $_ENV['GOOGLE_CLIENT_SECRET'];
$redirect_uri = $_ENV['GOOGLE_REDIRECT_URI'];


if (!isset($_GET['code'])) {
    die("Google auth failed.");
}

$code = $_GET['code'];

/* Step 1 — exchange code for access token */
$token_request = "https://oauth2.googleapis.com/token";

$data = [
    "code" => $code,
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "redirect_uri" => $redirect_uri,
    "grant_type" => "authorization_code"
];

$options = [
    "http" => [
        "header"  => "Content-Type: application/x-www-form-urlencoded\r\n",
        "method"  => "POST",
        "content" => http_build_query($data)
    ]
];

$response = file_get_contents($token_request, false, stream_context_create($options));
$token = json_decode($response, true);

$access_token = $token["access_token"];

/* Step 2 — get user info */
$user_json = file_get_contents("https://www.googleapis.com/oauth2/v3/userinfo?access_token=$access_token");
$google = json_decode($user_json, true);

$email = $google["email"];
$name  = $google["name"];

/* Step 3 — check if user exists */
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    // Existing user
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['id'];
    $role = $user['role'];
} else {
    // Create new account
    $role = 'student';
    $insert = mysqli_prepare($conn, "INSERT INTO users (fullname, email, role) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insert, "sss", $name, $email, $role);
    mysqli_stmt_execute($insert);
    $user_id = mysqli_insert_id($conn);
}

/* Step 4 — log user in (SAME as login.php) */
$_SESSION['user_id']   = $user_id;
$_SESSION['user_name'] = $name;
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = $role;

/* Step 5 — redirect */
if ($role === 'admin') {
    header("Location: /language-learning-chatbot-MIU/app/views/admin/admin-dashboard.php");
} else {
    header("Location: /language-learning-chatbot-MIU/app/views/student/dashboard.php");
}
exit;
?>
