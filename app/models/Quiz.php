<?php
require_once __DIR__ . '/../services/AIQuizGeneratorInterface.php';
require_once __DIR__ . '/../services/GroqQuizGenerator.php';
require_once __DIR__ . '/../services/QuizRepositoryInterface.php';
require_once __DIR__ . '/../services/MySQLQuizRepository.php';

class Quiz {
    private $conn;
    private $user_id;
    private $aiGenerator;
    private $repository;

    public function __construct($conn, $user_id, ?AIQuizGeneratorInterface $aiGenerator = null, ?QuizRepositoryInterface $repository = null) {
        $this->conn = $conn;
        $this->user_id = intval($user_id);
        $this->aiGenerator = $aiGenerator ?? new GroqQuizGenerator();
        $this->repository = $repository ?? new MySQLQuizRepository($conn, $this->user_id);
    }

    public function generateQuiz($mcqCount, $shortCount, $difficulty, $language) {
        return $this->aiGenerator->generate($mcqCount, $shortCount, $difficulty, $language);
    }

    public function saveQuiz($language, $difficulty, $mcq_count, $short_count, $score, $total, $percent) {
        return $this->repository->save($language, $difficulty, $mcq_count, $short_count, $score, $total, $percent);
    }

    public function getQuizzes($limit = 50) {
        return $this->repository->getByUserId($this->user_id, $limit);
    }
}