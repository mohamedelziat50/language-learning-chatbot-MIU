<?php
// filepath: app/models/Lesson.php

require_once __DIR__ . '/../helpers/FileStorage.php';

class Lesson {
    private int $id;
    private string $title;
    private string $content;
    private int $topic_id;
    private string $difficulty;
    private static FileStorage $storage;

    public function __construct(int $id = 0, string $title = '', string $content = '', int $topic_id = 0, string $difficulty = 'beginner') {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->topic_id = $topic_id;
        $this->difficulty = $difficulty;
        
        if (!isset(self::$storage)) {
            self::$storage = new FileStorage();
        }
    }

    // ===== GETTERS =====
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getContent(): string { return $this->content; }
    public function getTopicId(): int { return $this->topic_id; }
    public function getDifficulty(): string { return $this->difficulty; }

    // ===== SETTERS =====
    public function setTitle(string $title): void { $this->title = $title; }
    public function setContent(string $content): void { $this->content = $content; }
    public function setDifficulty(string $difficulty): void { $this->difficulty = $difficulty; }

    // ===== STATIC CRUD METHODS =====
    public static function getAll(): array {
        $data = self::$storage->readFile('lessons');
        $lessons = [];
        foreach ($data as $item) {
            $lessons[] = new self(
                $item['id'],
                $item['title'],
                $item['content'],
                $item['topic_id'],
                $item['difficulty'] ?? 'beginner'
            );
        }
        return $lessons;
    }

    public static function getById(int $id): ?Lesson {
        $data = self::$storage->readFile('lessons');
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return new self(
                    $item['id'],
                    $item['title'],
                    $item['content'],
                    $item['topic_id'],
                    $item['difficulty'] ?? 'beginner'
                );
            }
        }
        return null;
    }

    public static function getByTopic(int $topic_id): array {
        $data = self::$storage->readFile('lessons');
        $lessons = [];
        foreach ($data as $item) {
            if ($item['topic_id'] == $topic_id) {
                $lessons[] = new self(
                    $item['id'],
                    $item['title'],
                    $item['content'],
                    $item['topic_id'],
                    $item['difficulty'] ?? 'beginner'
                );
            }
        }
        return $lessons;
    }

    public function save(): bool {
        $data = self::$storage->readFile('lessons');
        $this->id = self::$storage->getNextId('lessons');
        
        $newLesson = [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'topic_id' => $this->topic_id,
            'difficulty' => $this->difficulty,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $data[] = $newLesson;
        return self::$storage->writeFile('lessons', $data);
    }

    public function update(): bool {
        $data = self::$storage->readFile('lessons');
        
        foreach ($data as &$item) {
            if ($item['id'] == $this->id) {
                $item['title'] = $this->title;
                $item['content'] = $this->content;
                $item['difficulty'] = $this->difficulty;
                break;
            }
        }
        
        return self::$storage->writeFile('lessons', $data);
    }

    public static function delete(int $id): bool {
        $data = self::$storage->readFile('lessons');
        $data = array_filter($data, fn($item) => $item['id'] != $id);
        return self::$storage->writeFile('lessons', array_values($data));
    }
}
?>
