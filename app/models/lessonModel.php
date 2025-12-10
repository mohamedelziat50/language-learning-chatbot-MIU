<?php
class LessonModel {
    public static function getAllLessons($language = null) {
        $lessonsData = require __DIR__ . '/../data/lessons.php';

        if ($language) {
            return $lessonsData[$language] ?? [];
        }

        return $lessonsData;
    }

    public static function getLessonByLanguageAndTopic($language, $topic) {
        $lessonsData = require __DIR__ . '/../data/lessons.php';
        return $lessonsData[$language][$topic] ?? null;
    }

    public static function getAvailableLanguages() {
        $lessonsData = require __DIR__ . '/../data/lessons.php';
        return array_keys($lessonsData);
    }

    public static function getAvailableTopics($language) {
        $lessonsData = require __DIR__ . '/../data/lessons.php';
        return array_keys($lessonsData[$language] ?? []);
    }
}
?>
