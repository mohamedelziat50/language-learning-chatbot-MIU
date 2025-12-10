<?php
// filepath: app/models/Language.php

require_once __DIR__ . '/../helpers/FileStorage.php';

class Language {
    private int $id;
    private string $name;
    private string $code;
    private string $flag;
    private static FileStorage $storage;

    public function __construct(int $id = 0, string $name = '', string $code = '', string $flag = '') {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->flag = $flag;
        
        if (!isset(self::$storage)) {
            self::$storage = new FileStorage();
        }
    }

    // ===== GETTERS =====
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getCode(): string { return $this->code; }
    public function getFlag(): string { return $this->flag; }

    // ===== SETTERS =====
    public function setName(string $name): void { $this->name = $name; }
    public function setCode(string $code): void { $this->code = $code; }
    public function setFlag(string $flag): void { $this->flag = $flag; }

    // ===== STATIC CRUD METHODS =====
    public static function getAll(): array {
        $data = self::$storage->readFile('languages');
        $languages = [];
        foreach ($data as $item) {
            $languages[] = new self(
                $item['id'],
                $item['name'],
                $item['code'],
                $item['flag'] ?? ''
            );
        }
        return $languages;
    }

    public static function getById(int $id): ?Language {
        $data = self::$storage->readFile('languages');
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return new self(
                    $item['id'],
                    $item['name'],
                    $item['code'],
                    $item['flag'] ?? ''
                );
            }
        }
        return null;
    }

    public static function getByCode(string $code): ?Language {
        $data = self::$storage->readFile('languages');
        foreach ($data as $item) {
            if ($item['code'] === $code) {
                return new self(
                    $item['id'],
                    $item['name'],
                    $item['code'],
                    $item['flag'] ?? ''
                );
            }
        }
        return null;
    }

    public function save(): bool {
        $data = self::$storage->readFile('languages');
        $this->id = self::$storage->getNextId('languages');
        
        $newLanguage = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'flag' => $this->flag,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $data[] = $newLanguage;
        return self::$storage->writeFile('languages', $data);
    }

    public function update(): bool {
        $data = self::$storage->readFile('languages');
        
        foreach ($data as &$item) {
            if ($item['id'] == $this->id) {
                $item['name'] = $this->name;
                $item['code'] = $this->code;
                $item['flag'] = $this->flag;
                break;
            }
        }
        
        return self::$storage->writeFile('languages', $data);
    }

    public static function delete(int $id): bool {
        $data = self::$storage->readFile('languages');
        $data = array_filter($data, fn($item) => $item['id'] != $id);
        return self::$storage->writeFile('languages', array_values($data));
    }
}
?>
