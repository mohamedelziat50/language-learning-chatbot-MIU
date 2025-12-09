<?php
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
require_once '../../models/DictionaryRepository.php';

$database = new Database();
$conn = $database->connect();
$dictionaryRepo = new DictionaryRepository($conn);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $data = $dictionaryRepo->getById($_GET['id']);
        } elseif (isset($_GET['search'])) {
            $language_id = $_GET['language_id'] ?? null;
            $data = $dictionaryRepo->search($_GET['search'], $language_id);
        } elseif (isset($_GET['language_id'])) {
            $data = $dictionaryRepo->getByLanguage($_GET['language_id']);
        } else {
            $data = $dictionaryRepo->getAll();
        }
        echo json_encode(['success' => true, 'data' => $data]);
    }
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['word']) || !isset($input['translation']) || !isset($input['language_id'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        $dictionary = new Dictionary(null, $input['word'], $input['translation'], $input['language_id'],
        $input['pronunciation'] ?? '', $input['example'] ?? '');
        $result = $dictionaryRepo->create($dictionary);
        echo json_encode(['success' => $result, 'message' => 'Word added']);
    }
    elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents("php://input"), true);
        $dictionary = new Dictionary($input['id'], $input['word'], $input['translation'], null,
        $input['pronunciation'] ?? '', $input['example'] ?? '');
        $result = $dictionaryRepo->update($dictionary);
        echo json_encode(['success' => $result, 'message' => 'Word updated']);
    }
    elseif ($method === 'DELETE') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $dictionaryRepo->delete($input['id']);
        echo json_encode(['success' => $result, 'message' => 'Word deleted']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>