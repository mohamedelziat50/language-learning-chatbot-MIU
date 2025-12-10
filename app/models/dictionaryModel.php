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
}
