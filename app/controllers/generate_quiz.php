<?php
session_start();
header("Content-Type: application/json");

// 0. Load .env manually
$envPath = __DIR__ . '/../../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // skip comments
        if (strpos($line, '=') !== false) {
            [$name, $value] = explode('=', $line, 2);
            putenv(trim($name) . '=' . trim($value));
        }
    }
}

// 1. Receive data from frontend
$mcqCount = $_POST['mcqCount'] ?? 5;
$shortCount = $_POST['shortCount'] ?? 2;
$difficulty = $_POST['difficulty'] ?? 2;
$language = $_POST['language'] ?? "French";

// 2. Get API key
$api_key = getenv('OPENAI_API_KEY');
if (!$api_key) {
    echo json_encode([
        "error" => ["message" => "OpenAI API key not set. Check your .env file."]
    ]);
    exit;
}

// 3. AI API Request
$url = "https://api.openai.com/v1/chat/completions";
$data = [
    "model" => "gpt-4o-mini",
    "messages" => [
        [
            "role" => "user",
            "content" => "Generate a quiz for learning $language.
Difficulty: $difficulty/5.
MCQs: $mcqCount.
Short answer questions: $shortCount.

Return JSON ONLY in this format:
{
    \"mcq\": [
        {\"question\": \"...\", \"options\": [\"...\", \"...\", \"...\"], \"answer\": \"...\"}
    ],
    \"short\": [
        {\"question\": \"...\", \"answer\": \"...\"}
    ]
}"
        ]
    ]
];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $api_key"
]);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
