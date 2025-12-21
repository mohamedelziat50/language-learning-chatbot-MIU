<?php
require_once __DIR__ . '/AIQuizGeneratorInterface.php';

class GroqQuizGenerator implements AIQuizGeneratorInterface {
    
    public function generate(int $mcqCount, int $shortCount, int $difficulty, string $language): array {
        // Get API key
        $api_key = getenv('GROQ_API_KEY');
        if (!$api_key) {
            return [
                "error" => ["message" => "Groq API key not set. Check your .env file."]
            ];
        }

        // Prepare AI API request
        $url = "https://api.groq.com/openai/v1/chat/completions";
        $data = [
            "model" => "llama-3.1-8b-instant",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a quiz generator for language learning. Generate quizzes in the exact JSON format requested."
                ],
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
            ],
            "temperature" => 0.7,
            "max_tokens" => 2000
        ];

        // Call Groq API
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

        // Handle cURL errors
        if ($response === false) {
            error_log("cURL error when calling Groq: " . $curlErr);
            return [
                "error" => ["message" => "Failed to call AI API.", "details" => $curlErr]
            ];
        }

        // Decode response
        $decoded = json_decode($response, true);
        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("Groq returned HTTP $httpCode: " . $response);
            return [
                "error" => ["message" => "AI API error.", "http_code" => $httpCode, "body" => $decoded]
            ];
        }

        // Prepare wrapper response
        $wrapper = [
            "success" => true,
            "mcqCount" => $mcqCount,
            "shortCount" => $shortCount,
            "difficulty" => $difficulty,
            "language" => $language,
            "sent_prompt" => $data['messages'][1]['content'],
            "groq" => $decoded
        ];

        return $wrapper;
    }
}
