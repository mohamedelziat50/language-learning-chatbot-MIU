<?php
// filepath: app/models/Lesson.php
require_once __DIR__ . '/../data/lessons.php';

class Lesson {
    private int $id;
    private string $title;
    private string $type;
    private string $language;
    private string $topic;
    private string $icon;
    private string $description;
    private string $difficulty;
    private array $content;

    public function __construct(
        int $id,
        string $title,
        string $type,
        string $language,
        string $topic,
        string $icon = '',
        string $description = '',
        string $difficulty = 'beginner',
        array $content = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->language = $language;
        $this->topic = $topic;
        $this->icon = $icon;
        $this->description = $description;
        $this->difficulty = $difficulty;
        $this->content = $content;
    }

    // ===== GETTERS =====
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getType(): string { return $this->type; }
    public function getLanguage(): string { return $this->language; }
    public function getTopic(): string { return $this->topic; }
    public function getIcon(): string { return $this->icon; }
    public function getDescription(): string { return $this->description; }
    public function getDifficulty(): string { return $this->difficulty; }
    public function getContent(): array { return $this->content; }

    // ===== STATIC METHODS =====

    private static function getData(): array {
        return require __DIR__ . '/../data/lessons.php';
    }

    public static function getAll(): array {
        $lessonsData = self::getData();
        $lessons = [];

        foreach ($lessonsData as $language => $topics) {
            foreach ($topics as $topic => $types) {
                foreach ($types as $type => $content) {
                    $lessons[] = new self(
                        rand(1000, 9999), // temporary ID
                        ucfirst($type),
                        $type,
                        $language,
                        $topic,
                        $types[$type]['icon'] ?? '',
                        $types[$type]['description'] ?? '',
                        'beginner',
                        $types[$type]['content'] ?? $content
                    );
                }
            }
        }

        return $lessons;
    }

    public static function getByLanguageAndTopic(string $language, string $topic): array {
        $lessonsData = self::getData();
        $lessons = [];

        if (!isset($lessonsData[$language][$topic])) {
            return [];
        }

        foreach ($lessonsData[$language][$topic] as $type => $content) {
            $lessons[] = new self(
                rand(1000, 9999), // temporary ID
                ucfirst($type),
                $type,
                $language,
                $topic,
                $content['icon'] ?? '',
                $content['description'] ?? '',
                'beginner',
                $content['content'] ?? $content
            );
        }

        return $lessons;
    }

    public static function getAvailableLanguages(): array {
        $lessonsData = self::getData();
        return array_keys($lessonsData);
    }

    public static function getAvailableTopics(string $language): array {
        $lessonsData = self::getData();
        return array_keys($lessonsData[$language] ?? []);
    }
}
?>
