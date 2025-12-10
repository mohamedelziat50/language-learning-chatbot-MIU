<?php
// filepath: app/controllers/lessons.php

header('Content-Type: application/json');
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/TopicModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'all';

try {
    // ===== GET REQUESTS =====
    if ($method === 'GET') {
        if ($action === 'all') {
            $lessons = Lesson::getAllLessons();
            $data = array_map(fn($lesson) => [
                'id' => $lesson->getId(),
                'title' => $lesson->getTitle(),
                'type' => $lesson->getType(),
                'language_id' => $lesson->getLanguageId(),
                'topic_id' => $lesson->getTopicId(),
                'icon' => $lesson->getIcon(),
                'description' => $lesson->getDescription(),
                'difficulty' => $lesson->getDifficulty(),
                'content' => $lesson->getContent()
            ], $lessons);

            echo json_encode(['success' => true, 'message' => 'Lessons retrieved', 'data' => $data]);
            exit;
        }

        if ($action === 'show' && isset($_GET['id'])) {
            $lesson = Lesson::getById(intval($_GET['id']));
            if (!$lesson) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Lesson not found']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Lesson retrieved', 'data' => [
                'id' => $lesson->getId(),
                'title' => $lesson->getTitle(),
                'type' => $lesson->getType(),
                'language_id' => $lesson->getLanguageId(),
                'topic_id' => $lesson->getTopicId(),
                'icon' => $lesson->getIcon(),
                'description' => $lesson->getDescription(),
                'difficulty' => $lesson->getDifficulty(),
                'content' => $lesson->getContent()
            ]]);
            exit;
        }

        if ($action === 'by_topic' && isset($_GET['topic_id'])) {
            $topic_id = intval($_GET['topic_id']);
            if (!Topic::getById($topic_id)) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Topic not found']);
                exit;
            }

            $lessons = Lesson::getByTopic($topic_id);
            $data = array_map(fn($lesson) => [
                'id' => $lesson->getId(),
                'title' => $lesson->getTitle(),
                'type' => $lesson->getType(),
                'language_id' => $lesson->getLanguageId(),
                'topic_id' => $lesson->getTopicId(),
                'icon' => $lesson->getIcon(),
                'description' => $lesson->getDescription(),
                'difficulty' => $lesson->getDifficulty(),
                'content' => $lesson->getContent()
            ], $lessons);

            echo json_encode(['success' => true, 'message' => 'Lessons retrieved', 'data' => $data]);
            exit;
        }

        if ($action === 'by_language_topic' && isset($_GET['language_id'], $_GET['topic_id'])) {
            $lessons = Lesson::getByLanguageAndTopic(intval($_GET['language_id']), intval($_GET['topic_id']));
            $data = array_map(fn($lesson) => [
                'id' => $lesson->getId(),
                'title' => $lesson->getTitle(),
                'type' => $lesson->getType(),
                'language_id' => $lesson->getLanguageId(),
                'topic_id' => $lesson->getTopicId(),
                'icon' => $lesson->getIcon(),
                'description' => $lesson->getDescription(),
                'difficulty' => $lesson->getDifficulty(),
                'content' => $lesson->getContent()
            ], $lessons);

            echo json_encode(['success' => true, 'message' => 'Lessons retrieved', 'data' => $data]);
            exit;
        }
    }

    // ===== POST REQUEST - CREATE =====
    if ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        if (empty($input['title']) || empty($input['type']) || empty($input['language_id']) || empty($input['topic_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Title, type, language_id, and topic_id are required']);
            exit;
        }

        $difficulty = $input['difficulty'] ?? 'beginner';
        if (!in_array($difficulty, ['beginner', 'intermediate', 'advanced'])) {
            $difficulty = 'beginner';
        }

        // Create lesson instance
        $lesson = new Lesson(
            intval($input['id'] ?? 0),
            trim($input['title']),
            $input['type'],
            intval($input['language_id']),
            intval($input['topic_id']),
            $input['icon'] ?? 'fas fa-book',
            $input['description'] ?? '',
            $difficulty,
            $input['content'] ?? []
        );

        Lesson::addLesson($lesson);

        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Lesson created', 'id' => $lesson->getId()]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
