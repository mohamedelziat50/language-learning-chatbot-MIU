<?php
// filepath: app/models/Language.php

class Language {
    private int $id;
    private string $name;
    private string $code;
    private string $flag;

    public function __construct(int $id = 0, string $name = '', string $code = '', string $flag = '') {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->flag = $flag;
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
        $languagesData = require __DIR__ . '/../data/languages.php';
        $languages = [];
        foreach ($languagesData as $lang) {
            $languages[] = new self(
                $lang['id'],
                $lang['name'],
                $lang['code'],
                $lang['flag']
            );
        }
        return $languages;
    }

    public static function getAllAsArray(): array {
        $languagesData = require __DIR__ . '/../data/languages.php';
        return $languagesData;
    }

    public static function getById(int $id): ?Language {
        $languages = self::getAll();
        foreach ($languages as $lang) {
            if ($lang->getId() === $id) {
                return $lang;
            }
        }
        return null;
    }

    public function save(): bool {
        // For simplicity, this method does not actually persist data.
        // In a real application, you would implement database storage here.
        return true;
    }

    public function delete(): bool {
        // For simplicity, this method does not actually delete data.
        // In a real application, you would implement database deletion here.
        return true;
    }

    public static function getByCode(string $code): ?Language {
        $languages = self::getAll();
        foreach ($languages as $lang) {
            if ($lang->getCode() === $code) {
                return $lang;
            }
        }
        return null;
    }

    public static function exists(string $code): bool {
        return self::getByCode($code) !== null;
    }

    public static function update(int $id, string $name, string $code, string $flag): bool {
        // For simplicity, this method does not actually update data.
        // In a real application, you would implement database update here.
        return true;

    }


}
?>
