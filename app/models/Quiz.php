<?php

class Quiz {
    private $conn;
    private $user_id;

    public function __construct($conn, $user_id) {
        $this->conn = $conn;
        $this->user_id = intval($user_id);
    }

    /**
     * Generate a quiz using Groq API
     * 
     * @param int $mcqCount Number of multiple choice questions
     * @param int $shortCount Number of short answer questions
     * @param int $difficulty Difficulty level (1-5)
     * @param string $language Language to generate quiz for
     * @return array API response with quiz data
     */
    public function generateQuiz($mcqCount, $shortCount, $difficulty, $language) {
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

    /**
     * Save a quiz result to the database
     * 
     * @param string $language Language of the quiz
     * @param int $difficulty Difficulty level
     * @param int $mcq_count Number of MCQ questions
     * @param int $short_count Number of short answer questions
     * @param int $score User's score
     * @param int $total Total number of questions
     * @param float $percent Percentage score
     * @return array Result with success status and quiz_id
     */
    public function saveQuiz($language, $difficulty, $mcq_count, $short_count, $score, $total, $percent) {
        // Prepare parameters
        $language = mysqli_real_escape_string($this->conn, $language);
        $difficulty = intval($difficulty);
        $mcq_count = intval($mcq_count);
        $short_count = intval($short_count);
        $score = intval($score);
        $total = intval($total);
        $percent = floatval($percent);

        // Ensure quizzes table exists
        $createSQL = "CREATE TABLE IF NOT EXISTS quizzes (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            language VARCHAR(100) DEFAULT NULL,
            difficulty TINYINT DEFAULT NULL,
            mcq_count INT DEFAULT NULL,
            short_count INT DEFAULT NULL,
            score INT DEFAULT NULL,
            total_questions INT DEFAULT NULL,
            percent FLOAT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (user_id)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        mysqli_query($this->conn, $createSQL);

        // Insert quiz record
        $stmt = mysqli_prepare($this->conn, "INSERT INTO quizzes (user_id, language, difficulty, mcq_count, short_count, score, total_questions, percent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'isiiiiid', $this->user_id, $language, $difficulty, $mcq_count, $short_count, $score, $total, $percent);

        if (mysqli_stmt_execute($stmt)) {
            $quiz_id = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt);
            return ["success" => true, "quiz_id" => $quiz_id];
        } else {
            mysqli_stmt_close($stmt);
            return ["error" => ["message" => "Failed to save quiz", "details" => mysqli_error($this->conn)]];
        }
    }

    /**
     * Get all quizzes for the authenticated user
     * 
     * @param int $limit Maximum number of quizzes to return (default 50)
     * @return array Array of quiz records
     */
    public function getQuizzes($limit = 50) {
        // Ensure quizzes table exists
        $createSQL = "CREATE TABLE IF NOT EXISTS quizzes (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            language VARCHAR(100) DEFAULT NULL,
            difficulty TINYINT DEFAULT NULL,
            mcq_count INT DEFAULT NULL,
            short_count INT DEFAULT NULL,
            score INT DEFAULT NULL,
            total_questions INT DEFAULT NULL,
            percent FLOAT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (user_id)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        mysqli_query($this->conn, $createSQL);

        // Fetch quizzes for user
        $sql = "SELECT id, language, difficulty, mcq_count, short_count, score, total_questions, percent, created_at FROM quizzes WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        $limit = intval($limit);
        mysqli_stmt_bind_param($stmt, 'ii', $this->user_id, $limit);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        $rows = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $rows[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $rows;
    }
}

?>
