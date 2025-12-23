<?php
use PHPUnit\Framework\TestCase;

/**
 * UserController Tests
 * Tests user management functionality independently
 * Uses separate test database - production database is safe!
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

// Load controller
require_once __DIR__ . '/../../app/controllers/admin/manage_users.php';

class UserControllerTest extends TestCase
{

    public function testGetUsersReturnsArray()
    {
        $users = UserController::getUsers();
        $this->assertIsArray($users);
    }

    public function testGetTotalUsersCount()
    {
        $count = UserController::getTotalUsersCount();
        $this->assertIsNumeric($count);
        $this->assertGreaterThanOrEqual(0, (int)$count);
    }
    
    public function testAddUserWithValidData()
    {
        // sample email
        $email = 'test' . time() . rand(1000, 9999) . '@test.com';

        // sample data & send a post request to the addUser endpoint - go to controller you'll understand it
        $_POST = [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password123',
            'role' => 'student',
            'status' => 'active'
        ];
        
        $result = UserController::addUser();
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('User created successfully', $result['message']);
    }

    public function testAddUserWithMissingFields()
    {
        $_POST = ['name' => 'Test'];
        $result = UserController::addUser();
        $this->assertEquals('error', $result['status']);
        // The validator returns specific error messages for each missing field
        $this->assertStringContainsString('Email is required', $result['message']);
        $this->assertStringContainsString('Password is required', $result['message']);
    }
}
