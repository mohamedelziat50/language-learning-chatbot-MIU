# Task: Convert Model Classes to Entity Classes and Handle Data Files

## Steps to Complete

- [x] Refactor app/models/lessonModel.php to define Lesson entity class with properties, constructor, getters, setters
- [x] Create app/models/LessonRepository.php for database operations
- [x] Update app/controllers/lessonController.php to use LessonRepository instead of Lesson model
- [x] Refactor app/models/dictionaryModel.php to define Dictionary entity class
- [x] Create app/models/DictionaryRepository.php for database operations
- [x] Update app/controllers/dictionaryController.php to use DictionaryRepository
- [x] Refactor app/models/languageModel.php to define Language entity class
- [x] Create app/models/LanguageRepository.php for database operations
- [x] Update app/controllers/languageController.php to use LanguageRepository
- [x] Refactor app/models/TopicModel.php to define Topic entity class
- [x] Create app/models/TopicRepository.php for database operations
- [x] Update app/controllers/TopicController.php to use TopicRepository
- [x] Create app/seeders/DictionarySeeder.php to populate dictionary data from JSON into database
- [x] Create app/seeders/LessonSeeder.php to populate lesson data from JSON into database
- [ ] Test the refactored functionality and data seeding (critical-path testing for CRUD operations and seeders)
