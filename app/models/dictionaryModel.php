<?php
class DictionaryModel {
    public static function getAllDictionary($language = null) {
        $dictionaryData = require __DIR__ . '/../data/dictionary.php';

        if ($language) {
            return $dictionaryData[$language] ?? [];
        }

        return $dictionaryData;
    }

    public static function getDictionaryByLanguage($language) {
        $dictionaryData = require __DIR__ . '/../data/dictionary.php';
        return $dictionaryData[$language] ?? [];
    }

    public static function getDictionaryByLanguageAndTopic($language, $topic) {
        $dictionaryData = require __DIR__ . '/../data/dictionary.php';
        return $dictionaryData[$language][$topic] ?? [];
    }

    public static function getAvailableLanguages() {
        $dictionaryData = require __DIR__ . '/../data/dictionary.php';
        return array_keys($dictionaryData);
    }

    public static function getAvailableTopics($language) {
        $dictionaryData = require __DIR__ . '/../data/dictionary.php';
        return array_keys($dictionaryData[$language] ?? []);
    }

    public static function getTotalWords() {
        $dictionaryData = require __DIR__ . '/../data/dictionary.php';
        $totalWords = 0;
        foreach ($dictionaryData as $language => $topics) {
            foreach ($topics as $words) {
                $totalWords += count($words);
            }
        }
        return $totalWords;
    }

    public static function getTotalTopics() {
        $dictionaryData = require __DIR__ . '/../data/dictionary.php';
        $totalTopics = 0;
        foreach ($dictionaryData as $language => $topics) {
            $totalTopics += count($topics);
        }
        return $totalTopics;
    }
}
?>
