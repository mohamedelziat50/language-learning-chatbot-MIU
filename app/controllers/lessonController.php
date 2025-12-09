<?php
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
require_once '../../models/LessonRepository.php';

$database = new Database();
$conn = $database->connect();
$lessonRepo = new LessonRepository($conn);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $data = $lessonRepo->getById($_GET['id']);
        } elseif (isset($_GET['topic_id'])) {
            $data = $lessonRepo->getByTopic($_GET['topic_id']);
        } else {
            $data = $lessonRepo->getAll();
        }
        echo json_encode(['success' => true, 'data' => $data]);
    }
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['title']) || !isset($input['content']) || !isset($input['topic_id'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        $lesson = new Lesson(null, $input['title'], $input['content'], $input['topic_id'], $input['difficulty'] ?? 'beginner');
        $result = $lessonRepo->create($lesson);
        echo json_encode(['success' => $result, 'message' => 'Lesson created']);
    }
    elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents("php://input"), true);
        $lesson = new Lesson($input['id'], $input['title'], $input['content'], null, $input['difficulty'] ?? 'beginner');
        $result = $lessonRepo->update($lesson);
        echo json_encode(['success' => $result, 'message' => 'Lesson updated']);
    }
    elseif ($method === 'DELETE') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $lessonRepo->delete($input['id']);
        echo json_encode(['success' => $result, 'message' => 'Lesson deleted']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>