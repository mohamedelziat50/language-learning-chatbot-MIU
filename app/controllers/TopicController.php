<?php
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
require_once '../../models/Topic.php';

$database = new Database();
$conn = $database->connect();
$topic = new Topic($conn);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $data = $topic->getById($_GET['id']);
        } elseif (isset($_GET['language_id'])) {
            $data = $topic->getByLanguage($_GET['language_id']);
        } else {
            $data = $topic->getAll();
        }
        echo json_encode(['success' => true, 'data' => $data]);
    }
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['title']) || !isset($input['language_id'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        $result = $topic->create($input['title'], $input['language_id'], $input['description'] ?? '');
        echo json_encode(['success' => $result, 'message' => 'Topic created']);
    }
    elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $topic->update($input['id'], $input['title'], $input['description'] ?? '');
        echo json_encode(['success' => $result, 'message' => 'Topic updated']);
    }
    elseif ($method === 'DELETE') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $topic->delete($input['id']);
        echo json_encode(['success' => $result, 'message' => 'Topic deleted']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>