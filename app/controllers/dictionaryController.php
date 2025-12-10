<?php
// filepath: app/controllers/dictionary.php

header('Content-Type: application/json');
require_once __DIR__ . '/../models/Dictionary.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../helpers/Validator.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

try {
    if ($method === 'GET') {
        if ($action === 'all' || !$action) {
            $words = Dictionary::getAll();
            $data = array_map(fn($word) => [
                'id' => $word->getId(),
                'word' => $word->getWord(),
                'translation' => $word->getTranslation(),
                'language_id' => $word->getLanguageId(),
                'pronunciation' => $word->getPronunciation(),
                'example' => $word->getExample()
            ], $words);
            ResponseHelper::success('Words retrieved', $data);
        }
        elseif ($action === 'show' && isset($_GET['id'])) {
            $word = Dictionary::getById(intval($_GET['id']));
            if (!$word) {
                ResponseHelper::error('Word not found', 404);
            }
            ResponseHelper::success('Word retrieved', [
                'id' => $word->getId(),
                'word' => $word->getWord(),
                'translation' => $word->getTranslation(),
                'language_id' => $word->getLanguageId(),
                'pronunciation' => $word->getPronunciation(),
                'example' => $word->getExample()
            ]);
        }
        elseif ($action === 'by_language' && isset($_GET['language_id'])) {
            $words = Dictionary::getByLanguage(intval($_GET['language_id']));
            $data = array_map(fn($word) => [
                'id' => $word->getId(),
                'word' => $word->getWord(),
                'translation' => $word->getTranslation(),
                'language_id' => $word->getLanguageId(),
                'pronunciation' => $word->getPronunciation(),
                'example' => $word->getExample()
            ], $words);
            ResponseHelper::success('Words retrieved', $data);
        }
        elseif ($action === 'search' && isset($_GET['keyword'])) {
            $language_id = $_GET['language_id'] ?? null;
            $words = Dictionary::search($_GET['keyword'], $language_id ? intval($language_id) : null);
            $data = array_map(fn($word) => [
                'id' => $word->getId(),
                'word' => $word->getWord(),
                'translation' => $word->getTranslation(),
                'language_id' => $word->getLanguageId(),
                'pronunciation' => $word->getPronunciation(),
                'example' => $word->getExample()
            ], $words);
            ResponseHelper::success('Search results', $data);
        }
    }
    elseif ($method === 'POST' && $action === 'create') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        $errors = Validator::validate($input, [
            'word' => 'required|string',
            'translation' => 'required|string',
            'language_id' => 'required|integer',
            'pronunciation' => 'string',
            'example' => 'string'
        ]);
        
        if (!empty($errors)) {
            ResponseHelper::error('Validation failed', 400, $errors);
        }
        
        $dictionary = new Dictionary(0, $input['word'], $input['translation'], $input['language_id'], $input['pronunciation'] ?? '', $input['example'] ?? '');
        if ($dictionary->save()) {
            ResponseHelper::success('Word created successfully', ['id' => $dictionary->getId()]);
        } else {
            ResponseHelper::error('Failed to create word', 500);
        }
    }
    elseif ($method === 'PUT' && $action === 'update') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (!isset($input['id'])) {
            ResponseHelper::error('ID is required', 400);
        }
        
        $dictionary = Dictionary::getById(intval($input['id']));
        if (!$dictionary) {
            ResponseHelper::error('Word not found', 404);
        }
        
        $dictionary->setWord($input['word'] ?? $dictionary->getWord());
        $dictionary->setTranslation($input['translation'] ?? $dictionary->getTranslation());
        $dictionary->setPronunciation($input['pronunciation'] ?? $dictionary->getPronunciation());
        $dictionary->setExample($input['example'] ?? $dictionary->getExample());
        
        if ($dictionary->update()) {
            ResponseHelper::success('Word updated successfully');
        } else {
            ResponseHelper::error('Failed to update word', 500);
        }
    }
    elseif ($method === 'DELETE' && $action === 'delete') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (!isset($input['id'])) {
            ResponseHelper::error('ID is required', 400);
        }
        
        if (Dictionary::delete(intval($input['id']))) {
            ResponseHelper::success('Word deleted successfully');
        } else {
            ResponseHelper::error('Failed to delete word', 500);
        }
    }
    else {
        ResponseHelper::error('Invalid request', 400);
    }
} catch (Exception $e) {
    ResponseHelper::error($e->getMessage(), 500);
}
?>