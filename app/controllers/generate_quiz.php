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

// 1. Receive data from frontend and validate
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => ["message" => "Invalid request method. Use POST."]]);
    exit;
}

// Defensive read: treat empty strings as not set
$mcqCount = isset($_POST['mcqCount']) && $_POST['mcqCount'] !== '' ? intval($_POST['mcqCount']) : 5;
$shortCount = isset($_POST['shortCount']) && $_POST['shortCount'] !== '' ? intval($_POST['shortCount']) : 2;
$difficulty = isset($_POST['difficulty']) && $_POST['difficulty'] !== '' ? intval($_POST['difficulty']) : 2;
$language = isset($_POST['language']) && $_POST['language'] !== '' ? trim($_POST['language']) : "French";

// Log received parameters for debugging (written to PHP error log)
error_log("generate_quiz POST received: " . json_encode(["mcqCount" => $mcqCount, "shortCount" => $shortCount, "difficulty" => $difficulty, "language" => $language]));

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
$curlErr = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($response === false) {
    error_log("cURL error when calling OpenAI: " . $curlErr);
    echo json_encode(["error" => ["message" => "Failed to call AI API.", "details" => $curlErr]]);
    exit;
}

// If OpenAI returned a non-200, try to forward that error
$decoded = json_decode($response, true);
if ($httpCode < 200 || $httpCode >= 300) {
    error_log("OpenAI returned HTTP $httpCode: " . $response);
    echo json_encode(["error" => ["message" => "AI API error.", "http_code" => $httpCode, "body" => $decoded]]);
    exit;
}

// Decode OpenAI response so we can wrap it with helpful debug info
$decodedOpenAI = json_decode($response, true);

// Prepare wrapper so client can see what was sent and what was received
$wrapper = [
    "success" => true,
    "mcqCount" => $mcqCount,
    "shortCount" => $shortCount,
    "difficulty" => $difficulty,
    "language" => $language,
    "sent_prompt" => $data['messages'][0]['content'],
    "openai" => $decodedOpenAI
];

echo json_encode($wrapper);
