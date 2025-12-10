<?php
// filepath: app/models/Dictionary.php

require_once __DIR__ . '/../helpers/FileStorage.php';

class Dictionary {
    private int $id;
    private string $word;
    private string $translation;
    private int $language_id;
    private string $pronunciation;
    private string $example;
    private static FileStorage $storage;

    public function __construct(int $id = 0, string $word = '', string $translation = '', int $language_id = 0, string $pronunciation = '', string $example = '') {
        $this->id = $id;
        $this->word = $word;
        $this->translation = $translation;
        $this->language_id = $language_id;
        $this->pronunciation = $pronunciation;
        $this->example = $example;
        
        if (!isset(self::$storage)) {
            self::$storage = new FileStorage();
        }
    }

    // ===== GETTERS =====
    public function getId(): int { return $this->id; }
    public function getWord(): string { return $this->word; }
    public function getTranslation(): string { return $this->translation; }
    public function getLanguageId(): int { return $this->language_id; }
    public function getPronunciation(): string { return $this->pronunciation; }
    public function getExample(): string { return $this->example; }

    // ===== SETTERS =====
    public function setWord(string $word): void { $this->word = $word; }
    public function setTranslation(string $translation): void { $this->translation = $translation; }
    public function setPronunciation(string $pronunciation): void { $this->pronunciation = $pronunciation; }
    public function setExample(string $example): void { $this->example = $example; }

    // ===== STATIC CRUD METHODS =====
    public static function getAll(): array {
        $data = self::$storage->readFile('dictionary');
        $words = [];
        foreach ($data as $item) {
            $words[] = new self(
                $item['id'],
                $item['word'],
                $item['translation'],
                $item['language_id'],
                $item['pronunciation'] ?? '',
                $item['example'] ?? ''
            );
        }
        return $words;
    }

    public static function getById(int $id): ?Dictionary {
        $data = self::$storage->readFile('dictionary');
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return new self(
                    $item['id'],
                    $item['word'],
                    $item['translation'],
                    $item['language_id'],
                    $item['pronunciation'] ?? '',
                    $item['example'] ?? ''
                );
            }
        }
        return null;
    }

    public static function getByLanguage(int $language_id): array {
        $data = self::$storage->readFile('dictionary');
        $words = [];
        foreach ($data as $item) {
            if ($item['language_id'] == $language_id) {
                $words[] = new self(
                    $item['id'],
                    $item['word'],
                    $item['translation'],
                    $item['language_id'],
                    $item['pronunciation'] ?? '',
                    $item['example'] ?? ''
                );
            }
        }
        return $words;
    }

    public static function search(string $keyword, int $language_id = null): array {
        $data = self::$storage->readFile('dictionary');
        $results = [];
        
        foreach ($data as $item) {
            $matchesKeyword = stripos($item['word'], $keyword) !== false || 
                            stripos($item['translation'], $keyword) !== false;
            $matchesLanguage = $language_id === null || $item['language_id'] == $language_id;
            
            if ($matchesKeyword && $matchesLanguage) {
                $results[] = new self(
                    $item['id'],
                    $item['word'],
                    $item['translation'],
                    $item['language_id'],
                    $item['pronunciation'] ?? '',
                    $item['example'] ?? ''
                );
            }
        }
        
        return $results;
    }

    public function save(): bool {
        $data = self::$storage->readFile('dictionary');
        $this->id = self::$storage->getNextId('dictionary');
        
        $newWord = [
            'id' => $this->id,
            'word' => $this->word,
            'translation' => $this->translation,
            'language_id' => $this->language_id,
            'pronunciation' => $this->pronunciation,
            'example' => $this->example,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $data[] = $newWord;
        return self::$storage->writeFile('dictionary', $data);
    }

    public function update(): bool {
        $data = self::$storage->readFile('dictionary');
        
        foreach ($data as &$item) {
            if ($item['id'] == $this->id) {
                $item['word'] = $this->word;
                $item['translation'] = $this->translation;
                $item['pronunciation'] = $this->pronunciation;
                $item['example'] = $this->example;
                break;
            }
        }
        
        return self::$storage->writeFile('dictionary', $data);
    }

    public static function delete(int $id): bool {
        $data = self::$storage->readFile('dictionary');
        $data = array_filter($data, fn($item) => $item['id'] != $id);
        return self::$storage->writeFile('dictionary', array_values($data));
    }
}
?>
