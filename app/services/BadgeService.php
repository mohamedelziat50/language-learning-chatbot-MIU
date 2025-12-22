<?php

require_once __DIR__ . '/QuizRepositoryInterface.php';
require_once __DIR__ . '/MySQLQuizRepository.php';

class BadgeService {
    private $conn;
    private $user_id;
    private $repo;

    public function __construct($conn, int $user_id) {
        $this->conn = $conn;
        $this->user_id = intval($user_id);
        $this->repo = new MySQLQuizRepository($conn, $this->user_id);
    }

    
    public function evaluateCurrent(): array {
        $quizzes = $this->repo->getByUserId($this->user_id, 1000);
        return $this->evaluateFromList($quizzes);
    }

    public function evaluateFromList(array $quizzes): array {
        $count = count($quizzes);
        $maxPercent = 0.0;
        foreach ($quizzes as $q) {
            if (isset($q['percent'])) {
                $p = floatval($q['percent']);
                if ($p > $maxPercent) $maxPercent = $p;
            } else if (isset($q['score']) && isset($q['total_questions']) && intval($q['total_questions']) > 0) {
                $p = (floatval($q['score']) / floatval($q['total_questions'])) * 100.0;
                if ($p > $maxPercent) $maxPercent = $p;
            }
        }

        $unlocked = [];

        if ($count >= 1) {
            $unlocked[] = [
                'key' => 'first_quiz',
                'label' => 'First Quiz',
                'tier' => 'bronze',
            ];
        }

        if ($maxPercent >= 90.0) {
            $unlocked[] = [
                'key' => 'perfect_score',
                'label' => 'Perfect Score',
                'tier' => 'gold',
            ];
        }

        if ($count >= 10) {
            $unlocked[] = [
                'key' => 'quiz_count_10',
                'label' => '10 Quizzes',
                'tier' => 'bronze',
            ];
        }
        if ($count >= 25) {
            $unlocked[] = [
                'key' => 'quiz_count_25',
                'label' => '25 Quizzes',
                'tier' => 'silver',
            ];
        }
        if ($count >= 50) {
            $unlocked[] = [
                'key' => 'quiz_count_50',
                'label' => '50 Quizzes',
                'tier' => 'gold',
            ];
        }

        return $unlocked;
    }
}
