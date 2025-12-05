<?php
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
require_once '../../models/Lesson.php';

$database = new Database();
$conn = $database->connect();
$lesson = new Lesson($conn);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $data = $lesson->getById($_GET['id']);
        } elseif (isset($_GET['topic_id'])) {
            $data = $lesson->getByTopic($_GET['topic_id']);
        } else {
            $data = $lesson->getAll();
        }
        echo json_encode(['success' => true, 'data' => $data]);
    }
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['title']) || !isset($input['content']) || !isset($input['topic_id'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        $result = $lesson->create($input['title'], $input['content'], $input['topic_id'], $input['difficulty'] ?? 'beginner');
        echo json_encode(['success' => $result, 'message' => 'Lesson created']);
    }
    elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $lesson->update($input['id'], $input['title'], $input['content'], $input['difficulty'] ?? 'beginner');
        echo json_encode(['success' => $result, 'message' => 'Lesson updated']);
    }
    elseif ($method === 'DELETE') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $lesson->delete($input['id']);
        echo json_encode(['success' => $result, 'message' => 'Lesson deleted']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>