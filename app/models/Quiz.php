<?php
require_once __DIR__ . '/../services/AIQuizGeneratorInterface.php';
require_once __DIR__ . '/../services/GroqQuizGenerator.php';

class Quiz {
    private $conn;
    private $user_id;
    private $aiGenerator;

    public function __construct($conn, $user_id, ?AIQuizGeneratorInterface $aiGenerator = null) {
        $this->conn = $conn;
        $this->user_id = intval($user_id);
        $this->aiGenerator = $aiGenerator ?? new GroqQuizGenerator();
    }

    public function generateQuiz($mcqCount, $shortCount, $difficulty, $language) {
        return $this->aiGenerator->generate($mcqCount, $shortCount, $difficulty, $language);
    }

    public function saveQuiz($language, $difficulty, $mcq_count, $short_count, $score, $total, $percent) {
        $language = mysqli_real_escape_string($this->conn, $language);
        $difficulty = intval($difficulty);
        $mcq_count = intval($mcq_count);
        $short_count = intval($short_count);
        $score = intval($score);
        $total = intval($total);
        $percent = floatval($percent);
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

    public function getQuizzes($limit = 50) {
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
    }
}