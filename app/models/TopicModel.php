<?php
// filepath: app/models/Topic.php

require_once __DIR__ . '/../helpers/FileStorage.php';

class Topic {
    private int $id;
    private string $title;
    private int $language_id;
    private string $description;
    private string $icon;
    private static FileStorage $storage;

    public function __construct(int $id = 0, string $title = '', int $language_id = 0, string $description = '', string $icon = '') {
        $this->id = $id;
        $this->title = $title;
        $this->language_id = $language_id;
        $this->description = $description;
        $this->icon = $icon;
        
        if (!isset(self::$storage)) {
            self::$storage = new FileStorage();
        }
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

    // ===== STATIC CRUD METHODS =====
    public static function getAll(): array {
        $data = self::$storage->readFile('topics');
        $topics = [];
        foreach ($data as $item) {
            $topics[] = new self(
                $item['id'],
                $item['title'],
                $item['language_id'],
                $item['description'] ?? '',
                $item['icon'] ?? ''
            );
        }
        return $topics;
    }

    public static function getById(int $id): ?Topic {
        $data = self::$storage->readFile('topics');
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return new self(
                    $item['id'],
                    $item['title'],
                    $item['language_id'],
                    $item['description'] ?? '',
                    $item['icon'] ?? ''
                );
            }
        }
        return null;
    }

    public static function getByLanguage(int $language_id): array {
        $data = self::$storage->readFile('topics');
        $topics = [];
        foreach ($data as $item) {
            if ($item['language_id'] == $language_id) {
                $topics[] = new self(
                    $item['id'],
                    $item['title'],
                    $item['language_id'],
                    $item['description'] ?? '',
                    $item['icon'] ?? ''
                );
            }
        }
        return $topics;
    }

    public function save(): bool {
        $data = self::$storage->readFile('topics');
        $this->id = self::$storage->getNextId('topics');
        
        $newTopic = [
            'id' => $this->id,
            'title' => $this->title,
            'language_id' => $this->language_id,
            'description' => $this->description,
            'icon' => $this->icon,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $data[] = $newTopic;
        return self::$storage->writeFile('topics', $data);
    }

    public function update(): bool {
        $data = self::$storage->readFile('topics');
        
        foreach ($data as &$item) {
            if ($item['id'] == $this->id) {
                $item['title'] = $this->title;
                $item['description'] = $this->description;
                $item['icon'] = $this->icon;
                break;
            }
        }
        
        return self::$storage->writeFile('topics', $data);
    }

    public static function delete(int $id): bool {
        $data = self::$storage->readFile('topics');
        $data = array_filter($data, fn($item) => $item['id'] != $id);
        return self::$storage->writeFile('topics', array_values($data));
    }
}
?>
