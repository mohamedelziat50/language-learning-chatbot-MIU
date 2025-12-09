<?php
require_once '../../config/db_connect.php';
require_once '../models/DictionaryRepository.php';

class DictionarySeeder {
    private $dictionaryRepo;

    public function __construct($conn) {
        $this->dictionaryRepo = new DictionaryRepository($conn);
    }

    public function seed() {
        $jsonFile = '../../views/student/Dictionary/dictionary.json';
        if (!file_exists($jsonFile)) {
            echo "Dictionary JSON file not found.\n";
            return;
        }

        $data = json_decode(file_get_contents($jsonFile), true);
        if (!$data) {
            echo "Invalid JSON data.\n";
            return;
        }

        foreach ($data as $language => $categories) {
            // Assume language exists or create it
            // For simplicity, assume languages are seeded separately
            $languageId = $this->getLanguageId($language);
            if (!$languageId) continue;

            foreach ($categories as $category => $words) {
                foreach ($words as $word => $translation) {
                    $dictionary = new Dictionary(null, $word, $translation, $languageId, '', $category);
                    $this->dictionaryRepo->create($dictionary);
                }
            }
        }
        echo "Dictionary seeded successfully.\n";
    }

    private function getLanguageId($languageName) {
        // Simple lookup; in real app, query DB
        $languages = ['French' => 1, 'Spanish' => 2, 'German' => 3, 'Italian' => 4, 'English' => 5, 'Arabic' => 6, 'Japanese' => 7];
        return $languages[$languageName] ?? null;
    }
}

// Usage
$database = new Database();
$conn = $database->connect();
$seeder = new DictionarySeeder($conn);
$seeder->seed();
?>
