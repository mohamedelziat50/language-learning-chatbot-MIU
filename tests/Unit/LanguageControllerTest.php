<?php
use PHPUnit\Framework\TestCase;

/**
 * LanguageController Tests
 * Tests language functionality
 */

// Load .env (required by project)
require_once __DIR__ . '/../../config/load_env.php';

// Get test database config
$db_server = getenv('DB_TEST_SERVER') ?: 'localhost';
$db_user = getenv('DB_TEST_USER') ?: 'root';
$db_pass = getenv('DB_TEST_PASS') ?: '';
$db_name = getenv('DB_TEST_NAME') ?: 'test_language_learning_chatbot';

// Override DB_NAME BEFORE loading controller to use test database
putenv('DB_NAME=' . $db_name);

// Load model
require_once __DIR__ . '/../../app/models/LanguageModel.php';

class LanguageControllerTest extends TestCase
{
    public function testGetAllLanguages()
    {
        $languages = Language::getAll();
        $this->assertIsArray($languages);
        $this->assertGreaterThan(0, count($languages));
    }

    public function testGetLanguageById()
    {
        $languages = Language::getAll();
        if (count($languages) > 0) {
            $firstLang = $languages[0];
            $lang = Language::getById($firstLang->getId());
            $this->assertNotNull($lang);
            $this->assertEquals($firstLang->getId(), $lang->getId());
        }
    }
}

