<?php
// filepath: app/controllers/topics.php

header('Content-Type: application/json');
require_once __DIR__ . '/../models/Topic.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../helpers/Validator.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

try {
    if ($method === 'GET') {
        if ($action === 'all' || !$action) {
            $topics = Topic::getAll();
            $data = array_map(fn($topic) => [
                'id' => $topic->getId(),
                'title' => $topic->getTitle(),
                'language_id' => $topic->getLanguageId(),
                'description' => $topic->getDescription(),
                'icon' => $topic->getIcon()
            ], $topics);
            ResponseHelper::success('Topics retrieved', $data);
        }
        elseif ($action === 'show' && isset($_GET['id'])) {
            $topic = Topic::getById(intval($_GET['id']));
            if (!$topic) {
                ResponseHelper::error('Topic not found', 404);
            }
            ResponseHelper::success('Topic retrieved', [
                'id' => $topic->getId(),
                'title' => $topic->getTitle(),
                'language_id' => $topic->getLanguageId(),
                'description' => $topic->getDescription(),
                'icon' => $topic->getIcon()
            ]);
        }
        elseif ($action === 'by_language' && isset($_GET['language_id'])) {
            $topics = Topic::getByLanguage(intval($_GET['language_id']));
            $data = array_map(fn($topic) => [
                'id' => $topic->getId(),
                'title' => $topic->getTitle(),
                'language_id' => $topic->getLanguageId(),
                'description' => $topic->getDescription(),
                'icon' => $topic->getIcon()
            ], $topics);
            ResponseHelper::success('Topics retrieved', $data);
        }
    }
    elseif ($method === 'POST' && $action === 'create') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        $errors = Validator::validate($input, [
            'title' => 'required|string',
            'language_id' => 'required|integer',
            'description' => 'string',
            'icon' => 'string'
        ]);
        
        if (!empty($errors)) {
            ResponseHelper::error('Validation failed', 400, $errors);
        }
        
        $topic = new Topic(0, $input['title'], $input['language_id'], $input['description'] ?? '', $input['icon'] ?? '');
        if ($topic->save()) {
            ResponseHelper::success('Topic created successfully', ['id' => $topic->getId()]);
        } else {
            ResponseHelper::error('Failed to create topic', 500);
        }
    }
    elseif ($method === 'PUT' && $action === 'update') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (!isset($input['id'])) {
            ResponseHelper::error('ID is required', 400);
        }
        
        $topic = Topic::getById(intval($input['id']));
        if (!$topic) {
            ResponseHelper::error('Topic not found', 404);
        }
        
        $topic->setTitle($input['title'] ?? $topic->getTitle());
        $topic->setDescription($input['description'] ?? $topic->getDescription());
        $topic->setIcon($input['icon'] ?? $topic->getIcon());
        
        if ($topic->update()) {
            ResponseHelper::success('Topic updated successfully');
        } else {
            ResponseHelper::error('Failed to update topic', 500);
        }
    }
    elseif ($method === 'DELETE' && $action === 'delete') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (!isset($input['id'])) {
            ResponseHelper::error('ID is required', 400);
        }
        
        if (Topic::delete(intval($input['id']))) {
            ResponseHelper::success('Topic deleted successfully');
        } else {
            ResponseHelper::error('Failed to delete topic', 500);
        }
    }
    else {
        ResponseHelper::error('Invalid request', 400);
    }
} catch (Exception $e) {
    ResponseHelper::error($e->getMessage(), 500);
}
?>