<?php
// filepath: app/controllers/lessons.php

header('Content-Type: application/json');
require_once __DIR__ . '/../models/LessonModel.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../helpers/Validator.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

try {
    if ($method === 'GET') {
        if ($action === 'all' || !$action) {
            $lessons = Lesson::getAll();
            $data = array_map(fn($lesson) => [
                'id' => $lesson->getId(),
                'title' => $lesson->getTitle(),
                'content' => $lesson->getContent(),
                'topic_id' => $lesson->getTopicId(),
                'difficulty' => $lesson->getDifficulty()
            ], $lessons);
            ResponseHelper::success('Lessons retrieved', $data);
        }
        elseif ($action === 'show' && isset($_GET['id'])) {
            $lesson = LessonModel::getById(intval($_GET['id']));
            if (!$lesson) {
                ResponseHelper::error('Lesson not found', 404);
            }
            ResponseHelper::success('Lesson retrieved', [
                'id' => $lesson->getId(),
                'title' => $lesson->getTitle(),
                'content' => $lesson->getContent(),
                'topic_id' => $lesson->getTopicId(),
                'difficulty' => $lesson->getDifficulty()
            ]);
        }
        elseif ($action === 'by_topic' && isset($_GET['topic_id'])) {
            $lessons = LessonModel::getByTopic(intval($_GET['topic_id']));
            $data = array_map(fn($lesson) => [
                'id' => $lesson->getId(),
                'title' => $lesson->getTitle(),
                'content' => $lesson->getContent(),
                'topic_id' => $lesson->getTopicId(),
                'difficulty' => $lesson->getDifficulty()
            ], $lessons);
            ResponseHelper::success('Lessons retrieved', $data);
        }
    }
    elseif ($method === 'POST' && $action === 'create') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        $errors = Validator::validate($input, [
            'title' => 'required|string',
            'content' => 'required|string',
            'topic_id' => 'required|integer',
            'difficulty' => 'string'
        ]);
        
        if (!empty($errors)) {
            ResponseHelper::error('Validation failed', 400, $errors);
        }
        
        $lesson = new Lesson(0, $input['title'], $input['content'], $input['topic_id'], $input['difficulty'] ?? 'beginner');
        if ($lesson->save()) {
            ResponseHelper::success('Lesson created successfully', ['id' => $lesson->getId()]);
        } else {
            ResponseHelper::error('Failed to create lesson', 500);
        }
    }
    elseif ($method === 'PUT' && $action === 'update') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (!isset($input['id'])) {
            ResponseHelper::error('ID is required', 400);
        }
        
        $lesson = LessonModel::getById(intval($input['id']));
        if (!$lesson) {
            ResponseHelper::error('Lesson not found', 404);
        }
        
        $lesson->setTitle($input['title'] ?? $lesson->getTitle());
        $lesson->setContent($input['content'] ?? $lesson->getContent());
        $lesson->setDifficulty($input['difficulty'] ?? $lesson->getDifficulty());
        
        if ($lesson->update()) {
            ResponseHelper::success('Lesson updated successfully');
        } else {
            ResponseHelper::error('Failed to update lesson', 500);
        }
    }
    elseif ($method === 'DELETE' && $action === 'delete') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (!isset($input['id'])) {
            ResponseHelper::error('ID is required', 400);
        }
        
        if (Lesson::delete(intval($input['id']))) {
            ResponseHelper::success('Lesson deleted successfully');
        } else {
            ResponseHelper::error('Failed to delete lesson', 500);
        }
    }
    else {
        ResponseHelper::error('Invalid request', 400);
    }
} catch (Exception $e) {
    ResponseHelper::error($e->getMessage(), 500);
}
?>