<?php
require_once __DIR__ . '/QuizRepositoryInterface.php';

#extend QuizRepositoryInterface to implement MySQL-specific quiz data operations
class MySQLQuizRepository implements QuizRepositoryInterface {
    private $conn;
    private $user_id;

    public function __construct($conn, int $user_id) {
        $this->conn = $conn;
        $this->user_id = $user_id;
    }

    public function save(string $language, int $difficulty, int $mcq_count, int $short_count, int $score, int $total, float $percent): array {
        $language = mysqli_real_escape_string($this->conn, $language);
        $difficulty = intval($difficulty);
        $mcq_count = intval($mcq_count);
        $short_count = intval($short_count);
        $score = intval($score);
        $total = intval($total);
        $percent = floatval($percent);
        
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

    public function getByUserId(int $user_id, int $limit = 50): array {
        $sql = "SELECT id, language, difficulty, mcq_count, short_count, score, total_questions, percent, created_at FROM quizzes WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        $limit = intval($limit);
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $limit);
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
