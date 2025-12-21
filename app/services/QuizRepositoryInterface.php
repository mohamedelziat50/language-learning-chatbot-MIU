<?php

interface QuizRepositoryInterface {
    public function save(string $language, int $difficulty, int $mcq_count, int $short_count, int $score, int $total, float $percent): array;
    public function getByUserId(int $user_id, int $limit = 50): array;
}

#interface QuizRepositoryInterface open for extension with methods like update, delete, getTotalCount, etc.
#This allows different implementations (MySQL, PostgreSQL, NoSQL) to adhere to the same contract.