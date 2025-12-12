<?php
// app/models/Topic.php

class Topic {
    private int $id;
    private string $title;
    private int $language_id;
    private string $description;
    private string $icon;

    private static string $dataFile = __DIR__ . '/../data/topics.json';

    public function __construct(int $id = 0, string $title = '', int $language_id = 0, string $description = '', string $icon = '') {
        $this->id = $id;
        $this->title = $title;
        $this->language_id = $language_id;
        $this->description = $description;
        $this->icon = $icon;
    }

    // ===== Getters =====
    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getLanguageId(): int { return $this->language_id; }
    public function getDescription(): string { return $this->description; }
    public function getIcon(): string { return $this->icon; }

    // ===== Setters =====
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setIcon(string $icon): void { $this->icon = $icon; }
    public function setLanguageId(int $language_id): void { $this->language_id = $language_id; }

    // ===== File helpers =====
    private static function readData(): array {
        if (!file_exists(self::$dataFile)) return [];
        $json = file_get_contents(self::$dataFile);
        $arr = json_decode($json, true);
        return is_array($arr) ? $arr : [];
    }

    private static function writeData(array $arr): bool {
        $dir = dirname(self::$dataFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tmp = tempnam($dir, 'tmp_topics_');
        if ($tmp === false) return false;

        $written = file_put_contents($tmp, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        if ($written === false) {
            @unlink($tmp);
            return false;
        }

        if (!rename($tmp, self::$dataFile)) {
            @unlink($tmp);
            return false;
        }

        return true;
    }

    // ===== CRUD =====
    public static function getAll(): array {
        $data = self::readData();
        $out = [];
        foreach ($data as $item) {
            $out[] = new self(
                $item['id'] ?? 0,
                $item['title'] ?? '',
                $item['language_id'] ?? 0,
                $item['description'] ?? '',
                $item['icon'] ?? ''
            );
        }
        return $out;
    }

    public static function getAllAsArray(): array {
        return self::readData();
    }

    public static function getById(int $id): ?Topic {
        $data = self::readData();
        foreach ($data as $item) {
            if ((int)($item['id'] ?? 0) === $id) {
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
        $data = self::readData();
        $out = [];
        foreach ($data as $item) {
            if ((int)($item['language_id'] ?? 0) === $language_id) {
                $out[] = new self(
                    $item['id'] ?? 0,
                    $item['title'] ?? '',
                    $item['language_id'] ?? 0,
                    $item['description'] ?? '',
                    $item['icon'] ?? ''
                );
            }
        }
        return $out;
    }

    public function save(): bool {
        $data = self::readData();

        if ($this->id === 0) {
            // create new id
            $maxId = 0;
            foreach ($data as $item) {
                if (isset($item['id']) && (int)$item['id'] > $maxId) $maxId = (int)$item['id'];
            }
            $this->id = $maxId + 1;
            $data[] = [
                'id' => $this->id,
                'title' => $this->title,
                'language_id' => $this->language_id,
                'description' => $this->description,
                'icon' => $this->icon
            ];
            return self::writeData($data);
        }

        // update existing
        $updated = false;
        foreach ($data as &$item) {
            if ((int)($item['id'] ?? 0) === $this->id) {
                $item['title'] = $this->title;
                $item['language_id'] = $this->language_id;
                $item['description'] = $this->description;
                $item['icon'] = $this->icon;
                $updated = true;
                break;
            }
        }
        if ($updated) return self::writeData($data);

        // if not found, append
        $data[] = [
            'id' => $this->id,
            'title' => $this->title,
            'language_id' => $this->language_id,
            'description' => $this->description,
            'icon' => $this->icon
        ];
        return self::writeData($data);
    }

    public function delete(): bool {
        $data = self::readData();
        $found = false;
        foreach ($data as $i => $item) {
            if ((int)($item['id'] ?? 0) === $this->id) {
                array_splice($data, $i, 1);
                $found = true;
                break;
            }
        }
        if (!$found) return false;
        return self::writeData($data);
    }

    // Convenience static wrappers
    public static function create(string $title, int $language_id, string $description = '', string $icon = ''): ?Topic {
        $topic = new self(0, $title, $language_id, $description, $icon);
        return $topic->save() ? $topic : null;
    }

    public static function updateTopic(int $id, string $title, int $language_id, string $description = '', string $icon = ''): bool {
        $topic = self::getById($id);
        if (!$topic) return false;
        $topic->setTitle($title);
        $topic->setLanguageId($language_id);
        $topic->setDescription($description);
        $topic->setIcon($icon);
        return $topic->save();
    }

    public static function remove(int $id): bool {
        $topic = self::getById($id);
        if (!$topic) return false;
        return $topic->delete();
    }
}
?>