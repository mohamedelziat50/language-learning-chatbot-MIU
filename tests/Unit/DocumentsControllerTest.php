<?php
use PHPUnit\Framework\TestCase;

/**
 * DocumentsController Tests
 * Tests document CRUD operations independently
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
require_once __DIR__ . '/../../app/controllers/DocumentsController.php';

class DocumentsControllerTest extends TestCase
{
    private function createTestUser()
    {
        $conn = $GLOBALS['conn'];
        $email = 'doc_test_' . time() . rand(1000, 9999) . '@test.com';
        mysqli_query($conn, "INSERT INTO users (name, email, password, role, status) VALUES ('Test User', '$email', 'testpass', 'student', 'active')");
        return mysqli_insert_id($conn);
    }

    public function testCreateDocument()
    {
        $userId = $this->createTestUser();
        $id = createDocument($userId, 'Test Document', 'This is test content.');
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testCreateDocumentWithoutUserId()
    {
        $result = createDocument(null, 'Test', 'Content');
        $this->assertFalse($result);
    }

    public function testGetDocumentById()
    {
        $userId = $this->createTestUser();
        $docId = createDocument($userId, 'Document to Get', 'Content here');
        $doc = getDocumentById($docId);
        
        $this->assertIsArray($doc);
        $this->assertEquals($docId, $doc['document_id']);
        $this->assertEquals('Document to Get', $doc['title']);
    }

    public function testGetDocumentsByUser()
    {
        $userId = $this->createTestUser();
        createDocument($userId, 'Test Doc 1', 'Content 1');
        
        $docs = getDocumentsByUser($userId);
        $this->assertIsArray($docs);
        $this->assertGreaterThanOrEqual(1, count($docs));
    }

    public function testUpdateDocument()
    {
        $userId = $this->createTestUser();
        $docId = createDocument($userId, 'Original Title', 'Original content');
        $result = updateDocument($docId, $userId, 'Updated Title', 'Updated content');
        
        $this->assertTrue($result);
        
        // Verify update
        $doc = getDocumentById($docId);
        $this->assertEquals('Updated Title', $doc['title']);
        $this->assertEquals('Updated content', $doc['content']);
    }

    public function testDeleteDocument()
    {
        $userId = $this->createTestUser();
        $docId = createDocument($userId, 'Document to Delete', 'Content');
        $result = deleteDocument($docId, $userId);
        
        $this->assertTrue($result);
        
        // Verify deletion
        $doc = getDocumentById($docId);
        $this->assertFalse($doc);
    }
}
