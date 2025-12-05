<?php
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
require_once '../../models/Dictionary.php';

$database = new Database();
$conn = $database->connect();
$dictionary = new Dictionary($conn);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $data = $dictionary->getById($_GET['id']);
        } elseif (isset($_GET['search'])) {
            $language_id = $_GET['language_id'] ?? null;
            $data = $dictionary->search($_GET['search'], $language_id);
        } elseif (isset($_GET['language_id'])) {
            $data = $dictionary->getByLanguage($_GET['language_id']);
        } else {
            $data = $dictionary->getAll();
        }
        echo json_encode(['success' => true, 'data' => $data]);
    }
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['word']) || !isset($input['translation']) || !isset($input['language_id'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        $result = $dictionary->create($input['word'], $input['translation'], $input['language_id'], 
        $input['pronunciation'] ?? '', $input['example'] ?? '');
        echo json_encode(['success' => $result, 'message' => 'Word added']);
    }
    elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $dictionary->update($input['id'], $input['word'], $input['translation'],
        $input['pronunciation'] ?? '', $input['example'] ?? '');
        echo json_encode(['success' => $result, 'message' => 'Word updated']);
    }
    elseif ($method === 'DELETE') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $dictionary->delete($input['id']);
        echo json_encode(['success' => $result, 'message' => 'Word deleted']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>