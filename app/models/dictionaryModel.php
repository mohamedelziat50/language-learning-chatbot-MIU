<?php

class DictionaryModel
{
    private array $dictionary = [];

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception("Dictionary file not found: " . $filePath);
        }

        $this->dictionary = include $filePath;

        if (!is_array($this->dictionary)) {
            throw new Exception("Dictionary file does not return a valid array.");
        }
    }

    public function getAll(): array
    {
        return $this->dictionary;
    }

    public function getTotalTopics(): int
    {
        $count = 0;
        foreach ($this->dictionary as $language => $topics) {
            $count += count($topics);
        }
        return $count;
    }

    public function getTotalWords(): int
    {
        $count = 0;
        foreach ($this->dictionary as $language => $topics) {
            foreach ($topics as $words) {
                $count += count($words);
            }
        }
        return $count;
    }

    public function getLanguages(): array
    {
        return array_keys($this->dictionary);
    }

    public function getCategories(string $language): array
    {
        if (!isset($this->dictionary[$language])) {
            return [];
        }
        return array_keys($this->dictionary[$language]);
    }

    public function getWords(string $language, string $category): array
    {
        if (!isset($this->dictionary[$language][$category])) {
            return [];
        }
        return $this->dictionary[$language][$category];
    }

    public function search(string $term): array
    {
        $results = [];
        $termLower = strtolower($term);
        foreach ($this->dictionary as $language => $categories) {
            foreach ($categories as $category => $words) {
                foreach ($words as $english => $translation) {
                    if (stripos($english, $termLower) !== false || stripos($translation, $termLower) !== false) {
                        $results[] = [
                            'language' => $language,
                            'category' => $category,
                            'english' => $english,
                            'translation' => $translation
                        ];
                    }
                }
            }
        }
        return $results;
    }
}
