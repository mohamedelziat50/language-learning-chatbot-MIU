<?php
// filepath: app/models/TopicModel.php

class Topic {
    private int $id;
    private string $title;
    private int $language_id;
    private string $description;
    private string $icon;

    public function __construct(int $id = 0, string $title = '', int $language_id = 0, string $description = '', string $icon = '') {
        $this->id = $id;
        $this->title = $title;
        $this->language_id = $language_id;
        $this->description = $description;
        $this->icon = $icon;
    }

    // ===== GETTERS =====
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getLanguageId(): int { return $this->language_id; }
    public function getDescription(): string { return $this->description; }
    public function getIcon(): string { return $this->icon; }

    // ===== SETTERS =====
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setIcon(string $icon): void { $this->icon = $icon; }

    // ===== DATA FILE HELPER =====
    private static function getTopicsData(): array {
        $path = __DIR__ . '/../data/topics.php';
        if (!file_exists($path)) return [];
        return require $path;
    }

    // ===== STATIC CRUD METHODS =====
    public static function getAll(): array {
        $topicsData = self::getTopicsData();
        $topics = [];
        
        foreach ($topicsData as $item) {
            $topics[] = new self(
                $item['id'] ?? 0,
                $item['title'] ?? '',
                $item['language_id'] ?? 0,
                $item['description'] ?? '',
                $item['icon'] ?? ''
            );
        }
        
        return $topics;
    }

    public static function getById(int $id): ?Topic {
        $topicsData = self::getTopicsData();
        
        foreach ($topicsData as $item) {
            if (($item['id'] ?? 0) == $id) {
                return new self(
                    $item['id'],
                    $item['title'] ?? '',
                    $item['language_id'] ?? 0,
                    $item['description'] ?? '',
                    $item['icon'] ?? ''
                );
            }
        }
        
        return null;
    }

    public static function getByLanguage(int $language_id): array {
        $topicsData = self::getTopicsData();
        $topics = [];
        
        foreach ($topicsData as $item) {
            if (($item['language_id'] ?? 0) == $language_id) {
                $topics[] = new self(
                    $item['id'] ?? 0,
                    $item['title'] ?? '',
                    $item['language_id'] ?? 0,
                    $item['description'] ?? '',
                    $item['icon'] ?? ''
                );
            }
        }
        
        return $topics;
    }

    public function save(): bool {
        // For now, saving to PHP file requires manual update
        // In production, convert topics.php to topics.json for persistence
        return false;
    }

    public function update(): bool {
        // For now, updating PHP file requires manual update
        return false;
    }

    public static function delete(int $id): bool {
        // For now, deleting from PHP file requires manual update
        return false;
    }
}
?>
