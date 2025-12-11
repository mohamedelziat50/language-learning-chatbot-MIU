<?php
// filepath: app/models/Lesson.php

class Lesson {
    private int $id;
    private int $language_id;
    private int $topic_id;
    private string $type;
    private string $title;
    private string $icon;
    private string $description;
    private string $difficulty;
    private array $content;

    public function __construct(
        int $id,
        int $language_id,
        int $topic_id,
        string $type,
        string $title,
        string $icon = '',
        string $description = '',
        string $difficulty = 'beginner',
        array $content = []
    ) {
        $this->id = $id;
        $this->language_id = $language_id;
        $this->topic_id = $topic_id;
        $this->type = $type;
        $this->title = $title;
        $this->icon = $icon;
        $this->description = $description;
        $this->difficulty = $difficulty;
        $this->content = $content;
    }

    // ===== GETTERS =====
    public function getId(): int { return $this->id; }
    public function getLanguageId(): int { return $this->language_id; }
    public function getTopicId(): int { return $this->topic_id; }
    public function getType(): string { return $this->type; }
    public function getTitle(): string { return $this->title; }
    public function getIcon(): string { return $this->icon; }
    public function getDescription(): string { return $this->description; }
    public function getDifficulty(): string { return $this->difficulty; }
    public function getContent(): array { return $this->content; }

    // ===== STATIC METHODS =====
    private static function getData(): array {
        return require __DIR__ . '../data/lessons.php';
    }

    public static function getAllLessons(): array {
        $lessonsData = self::getData();
        $lessons = [];
        foreach ($lessonsData as $l) {
            $lessons[] = new self(
                $l['id'],
                $l['language_id'],
                $l['topic_id'],
                $l['type'],
                $l['title'],
                $l['icon'] ?? '',
                $l['description'] ?? '',
                $l['difficulty'] ?? 'beginner',
                $l['content'] ?? []
            );
        }
        return $lessons;
    }

    public static function getById(int $id): ?self {
        foreach (self::getAllLessons() as $lesson) {
            if ($lesson->getId() === $id) return $lesson;
        }
        return null;
    }

    public static function getByTopic(int $topic_id): array {
        return array_filter(self::getAllLessons(), fn($l) => $l->getTopicId() === $topic_id);
    }

    public static function getByLanguageAndTopic(int $language_id, int $topic_id): array {
        return array_filter(
            self::getAllLessons(),
            fn($l) => $l->getLanguageId() === $language_id && $l->getTopicId() === $topic_id
        );
    }

    public static function addLesson(self $lesson): void {
        // For now, addLesson can be implemented as append to file if needed
        // Or left empty until you implement DB storage
    }
}
?>
