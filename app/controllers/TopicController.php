<?php
// app/controllers/topicController.php

header('Content-Type: application/json');
require_once __DIR__ . '/../models/TopicModel.php';
require_once __DIR__ . '/../models/LanguageModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'all';

function respond($code, $payload) {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

try {
    // GET ALL
    if ($method === 'GET' && $action === 'all') {
        $topics = Topic::getAllAsArray();
        respond(200, ['success' => true, 'message' => 'Topics retrieved', 'data' => $topics]);
    }

    // GET ONE
    if ($method === 'GET' && $action === 'show' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $topic = Topic::getById($id);
        if (!$topic) respond(404, ['success' => false, 'message' => 'Topic not found']);
        respond(200, ['success' => true, 'message' => 'Topic retrieved', 'data' => [
            'id' => $topic->getId(),
            'title' => $topic->getTitle(),
            'language_id' => $topic->getLanguageId(),
            'description' => $topic->getDescription(),
            'icon' => $topic->getIcon()
        ]]);
    }

    // GET BY LANGUAGE
    if ($method === 'GET' && $action === 'by_language' && isset($_GET['language_id'])) {
        $language_id = (int)$_GET['language_id'];
        $topics = Topic::getByLanguage($language_id);
        $data = array_map(fn($t) => [
            'id' => $t->getId(),
            'title' => $t->getTitle(),
            'language_id' => $t->getLanguageId(),
            'description' => $t->getDescription(),
            'icon' => $t->getIcon()
        ], $topics);
        respond(200, ['success' => true, 'message' => 'Topics retrieved', 'data' => $data]);
    }

    // CREATE
    if ($method === 'POST' && $action === 'create') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) $input = $_POST;

        $title = trim($input['title'] ?? '');
        $language_id = intval($input['language_id'] ?? 0);
        $description = trim($input['description'] ?? '');
        $icon = trim($input['icon'] ?? '');

        if ($title === '' || $language_id <= 0) {
            respond(400, ['success' => false, 'message' => 'Title and language_id are required']);
        }

        // optional: verify language exists
        if (!Language::getById($language_id)) {
            respond(400, ['success' => false, 'message' => 'Language not found']);
        }

        $topic = new Topic(0, $title, $language_id, $description, $icon);
        if ($topic->save()) {
            respond(201, ['success' => true, 'message' => 'Topic created', 'data' => [
                'id' => $topic->getId(),
                'title' => $topic->getTitle(),
                'language_id' => $topic->getLanguageId(),
                'description' => $topic->getDescription(),
                'icon' => $topic->getIcon()
            ]]);
        }

        respond(500, ['success' => false, 'message' => 'Failed to create topic']);
    }

    // UPDATE
    if ($method === 'PUT' && $action === 'update') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) respond(400, ['success' => false, 'message' => 'Invalid input']);

        $id = intval($input['id'] ?? 0);
        $title = trim($input['title'] ?? '');
        $language_id = intval($input['language_id'] ?? 0);
        $description = trim($input['description'] ?? '');
        $icon = trim($input['icon'] ?? '');

        if ($id <= 0) respond(400, ['success' => false, 'message' => 'ID is required']);
        $topic = Topic::getById($id);
        if (!$topic) respond(404, ['success' => false, 'message' => 'Topic not found']);

        if ($title !== '') $topic->setTitle($title);
        if ($language_id > 0) {
            if (!Language::getById($language_id)) respond(400, ['success' => false, 'message' => 'Language not found']);
            $topic->setLanguageId($language_id);
        }
        if ($description !== '') $topic->setDescription($description);
        if ($icon !== '') $topic->setIcon($icon);

        if ($topic->save()) {
            respond(200, ['success' => true, 'message' => 'Topic updated']);
        }

        respond(500, ['success' => false, 'message' => 'Failed to update topic']);
    }

    // DELETE
    if ($method === 'DELETE' && $action === 'delete') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) respond(400, ['success' => false, 'message' => 'Invalid input']);

        $id = intval($input['id'] ?? 0);
        if ($id <= 0) respond(400, ['success' => false, 'message' => 'ID is required']);

        $topic = Topic::getById($id);
        if (!$topic) respond(404, ['success' => false, 'message' => 'Topic not found']);

        if ($topic->delete()) {
            respond(200, ['success' => true, 'message' => 'Topic deleted']);
        }

        respond(500, ['success' => false, 'message' => 'Failed to delete topic']);
    }

    respond(400, ['success' => false, 'message' => 'Invalid request']);
} catch (Exception $e) {
    respond(500, ['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
