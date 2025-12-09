<?php
require_once '../../config/db_connect.php';
require_once '../models/LessonRepository.php';
require_once '../models/TopicRepository.php';
require_once '../models/LanguageRepository.php';

class LessonSeeder {
    private $lessonRepo;
    private $topicRepo;
    private $languageRepo;

    public function __construct($conn) {
        $this->lessonRepo = new LessonRepository($conn);
        $this->topicRepo = new TopicRepository($conn);
        $this->languageRepo = new LanguageRepository($conn);
    }

    public function seed() {
        $jsonFile = '../../views/student/Lessons/lesson_data.json';
        if (!file_exists($jsonFile)) {
            echo "Lesson data JSON file not found.\n";
            return;
        }

        $data = json_decode(file_get_contents($jsonFile), true);
        if (!$data) {
            echo "Invalid JSON data.\n";
            return;
        }

        foreach ($data as $language => $topics) {
            // Get or create language
            $languageId = $this->getOrCreateLanguage($language);

            foreach ($topics as $topicName => $lessons) {
                // Get or create topic
                $topicId = $this->getOrCreateTopic($topicName, $languageId);

                foreach ($lessons as $lessonName => $content) {
                    // Create lesson with content as JSON string
                    $lesson = new Lesson(null, $lessonName, json_encode($content), $topicId, 'beginner');
                    $this->lessonRepo->create($lesson);
                }
            }
        }
        echo "Lessons seeded successfully.\n";
    }

    private function getOrCreateLanguage($languageName) {
        // Assume languages are seeded separately; for now, hardcode IDs
        $languages = ['French' => 1, 'Spanish' => 2, 'German' => 3, 'Italian' => 4, 'English' => 5, 'Arabic' => 6, 'Japanese' => 7];
        return $languages[$languageName] ?? 1; // Default to 1 if not found
    }

    private function getOrCreateTopic($topicName, $languageId) {
        // Check if topic exists, else create
        $topics = $this->topicRepo->getByLanguage($languageId);
        foreach ($topics as $topic) {
            if ($topic->getTitle() === $topicName) {
                return $topic->getId();
            }
        }
        // Create new topic
        $topic = new Topic(null, $topicName, $languageId, '');
        $this->topicRepo->create($topic);
        return $topic->getId();
    }
}

// Usage
$database = new Database();
$conn = $database->connect();
$seeder = new LessonSeeder($conn);
$seeder->seed();
?>
