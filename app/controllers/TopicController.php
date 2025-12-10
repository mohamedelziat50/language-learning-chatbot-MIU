<?php
// filepath: app/controllers/topic.php

header('Content-Type: application/json');
require_once __DIR__ . '/../models/Topic.php';
require_once __DIR__ . '/../models/Language.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'all';

try {
    // ===== GET REQUESTS =====
    if ($method === 'GET') {
        // Get all topics
        if ($action === 'all') {
            $topics = Topic::getAll();
            $data = array_map(fn($topic) => [
                'id' => $topic->getId(),
                'title' => $topic->getTitle(),
                'language_id' => $topic->getLanguageId(),
                'description' => $topic->getDescription(),
                'icon' => $topic->getIcon()
            ], $topics);

            echo json_encode(['success' => true, 'message' => 'Topics retrieved', 'data' => $data]);
            exit;
        }

        // Get topic by ID
        if ($action === 'show' && isset($_GET['id'])) {
            $topic = Topic::getById(intval($_GET['id']));
            if (!$topic) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Topic not found']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Topic retrieved', 'data' => [
                'id' => $topic->getId(),
                'title' => $topic->getTitle(),
                'language_id' => $topic->getLanguageId(),
                'description' => $topic->getDescription(),
                'icon' => $topic->getIcon()
            ]]);
            exit;
        }

        // Get topics by language ID
        if ($action === 'by_language' && isset($_GET['language_id'])) {
            $language_id = intval($_GET['language_id']);
            $topics = Topic::getByLanguage($language_id);
            $data = array_map(fn($topic) => [
                'id' => $topic->getId(),
                'title' => $topic->getTitle(),
                'language_id' => $topic->getLanguageId(),
                'description' => $topic->getDescription(),
                'icon' => $topic->getIcon()
            ], $topics);

            echo json_encode(['success' => true, 'message' => 'Topics retrieved', 'data' => $data]);
            exit;
        }
    }

    // ===== POST REQUEST - CREATE =====
    if ($method === 'POST' && $action === 'create') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        if (empty($input['title']) || empty($input['language_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Title and language_id are required']);
            exit;
        }

        $topic = new Topic(
            0,
            trim($input['title']),
            intval($input['language_id']),
            $input['description'] ?? '',
            $input['icon'] ?? ''
        );

        if ($topic->save()) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Topic created', 'id' => $topic->getId()]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create topic']);
        }
        exit;
    }

    // ===== PUT REQUEST - UPDATE =====
    if ($method === 'PUT' && $action === 'update') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID is required']);
            exit;
        }

        $topic = Topic::getById(intval($input['id']));
        if (!$topic) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Topic not found']);
            exit;
        }

        if (isset($input['title'])) $topic->setTitle(trim($input['title']));
        if (isset($input['description'])) $topic->setDescription($input['description']);
        if (isset($input['icon'])) $topic->setIcon($input['icon']);

        if ($topic->update()) {
            echo json_encode(['success' => true, 'message' => 'Topic updated']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update topic']);
        }
        exit;
    }

    // ===== DELETE REQUEST =====
    if ($method === 'DELETE' && $action === 'delete') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID is required']);
            exit;
        }

        $topic = Topic::getById(intval($input['id']));
        if (!$topic) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Topic not found']);
            exit;
        }

        if (Topic::delete(intval($input['id']))) {
            echo json_encode(['success' => true, 'message' => 'Topic deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete topic']);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
