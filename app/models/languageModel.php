<?php
// app/models/Language.php


class Language {
    private int $id;
    private string $name;
    private string $code;
    private string $flag;
    
    private static string $dataFile = __DIR__ . '/../data/languages.json';
    
    
    public function __construct(int $id = 0, string $name = '', string $code = '', string $flag = '') {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->flag = $flag;
    }

    // ===== Getters =====
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getCode(): string { return $this->code; }
    public function getFlag(): string { return $this->flag; }

    // ===== Setters =====
    public function setName(string $name): void { $this->name = $name; }
    public function setCode(string $code): void { $this->code = $code; }
    public function setFlag(string $flag): void { $this->flag = $flag; }

    // ===== File helpers =====
    private static function readData(): array {
        if (!file_exists(self::$dataFile)) {
            return [];
        }
        $json = file_get_contents(self::$dataFile);
        $arr = json_decode($json, true);
        return is_array($arr) ? $arr : [];
    }

    private static function writeData(array $arr): bool {
        $dir = dirname(self::$dataFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tmp = tempnam($dir, 'tmp_lang_');
        if ($tmp === false) return false;

        $written = file_put_contents($tmp, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        if ($written === false) {
            @unlink($tmp);
            return false;
        }

        // Atomic replace
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
            $out[] = new self($item['id'], $item['name'], $item['code'], $item['flag']);
        }
        return $out;
    }

    public static function getAllAsArray(): array {
        return self::readData();
    }

    public static function getById(int $id): ?Language {
        $data = self::readData();
        foreach ($data as $item) {
            if ((int)$item['id'] === $id) {
                return new self($item['id'], $item['name'], $item['code'], $item['flag']);
            }
        }
        return null;
    }

    public static function getByCode(string $code): ?Language {
        $data = self::readData();
        foreach ($data as $item) {
            if ($item['code'] === $code) {
                return new self($item['id'], $item['name'], $item['code'], $item['flag']);
            }
        }
        return null;
    }

    public static function exists(string $code): bool {
        return self::getByCode($code) !== null;
    }
    

    // Create or update via instance save()
    public function save(): bool {
        $data = self::readData();
    
    
        if ($this->id === 0) {
            // Create new: determine next ID
            $maxId = 0;
            foreach ($data as $item) {
                if (isset($item['id']) && (int)$item['id'] > $maxId) $maxId = (int)$item['id'];
            }
            $this->id = $maxId + 1;
            $data[] = ['id' => $this->id, 'name' => $this->name, 'code' => $this->code, 'flag' => $this->flag];
            return self::writeData($data);
        }
        
        
        // Update existing
        $updated = false;
        foreach ($data as &$item) {
            if ((int)$item['id'] === $this->id) {
                $item['name'] = $this->name;
                $item['code'] = $this->code;
                $item['flag'] = $this->flag;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            return self::writeData($data);
        }
        
        
        // If not found, append as new
        $data[] = ['id' => $this->id, 'name' => $this->name, 'code' => $this->code, 'flag' => $this->flag];
        return self::writeData($data);
    }

    public function delete(): bool {
        $data = self::readData();
        $found = false;
        foreach ($data as $i => $item) {
            if ((int)$item['id'] === $this->id) {
                $found = true;
                array_splice($data, $i, 1);
                break;
            }
        }
        if (!$found) return false;
        return self::writeData($data);
        }

    // Static convenience wrappers
    public static function create(string $name, string $code, string $flag = ''): ?Language {
        $lang = new self(0, $name, strtolower($code), $flag);
        return $lang->save() ? $lang : null;
    }

    public static function update(int $id, string $name, string $code, string $flag = ''): bool {
        $language = self::getById($id);
        if (!$language) return false;
        $language->setName($name);
        $language->setCode(strtolower($code));
        $language->setFlag($flag);
        return $language->save();
    }
    
    
    public static function remove(int $id): bool {
        $language = self::getById($id);
        if (!$language) return false;
        return $language->delete();
    }
}
?>
