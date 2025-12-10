<?php
// filepath: app/controllers/language.php

header('Content-Type: application/json');
require_once __DIR__ . '/../models/Language.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../helpers/Validator.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

try {
    if ($method === 'GET') {
        if ($action === 'all' || !$action) {
            $languages = Language::getAll();
            $data = array_map(fn($lang) => [
                'id' => $lang->getId(),
                'name' => $lang->getName(),
                'code' => $lang->getCode(),
                'flag' => $lang->getFlag()
            ], $languages);
            ResponseHelper::success('Languages retrieved', $data);
        } 
        elseif ($action === 'show' && isset($_GET['id'])) {
            $language = Language::getById(intval($_GET['id']));
            if (!$language) {
                ResponseHelper::error('Language not found', 404);
            }
            ResponseHelper::success('Language retrieved', [
                'id' => $language->getId(),
                'name' => $language->getName(),
                'code' => $language->getCode(),
                'flag' => $language->getFlag()
            ]);
        }
    }
    elseif ($method === 'POST' && $action === 'create') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        $errors = Validator::validate($input, [
            'name' => 'required|string',
            'code' => 'required|string|min:2',
            'flag' => 'string'
        ]);
        
        if (!empty($errors)) {
            ResponseHelper::error('Validation failed', 400, $errors);
        }
        
        $language = new Language(0, $input['name'], $input['code'], $input['flag'] ?? '');
        if ($language->save()) {
            ResponseHelper::success('Language created successfully', ['id' => $language->getId()]);
        } else {
            ResponseHelper::error('Failed to create language', 500);
        }
    }
    elseif ($method === 'PUT' && $action === 'update') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (!isset($input['id'])) {
            ResponseHelper::error('ID is required', 400);
        }
        
        $language = Language::getById(intval($input['id']));
        if (!$language) {
            ResponseHelper::error('Language not found', 404);
        }
        
        $language->setName($input['name'] ?? $language->getName());
        $language->setCode($input['code'] ?? $language->getCode());
        $language->setFlag($input['flag'] ?? $language->getFlag());
        
        if ($language->update()) {
            ResponseHelper::success('Language updated successfully');
        } else {
            ResponseHelper::error('Failed to update language', 500);
        }
    }
    elseif ($method === 'DELETE' && $action === 'delete') {
        $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        if (!isset($input['id'])) {
            ResponseHelper::error('ID is required', 400);
        }
        
        if (Language::delete(intval($input['id']))) {
            ResponseHelper::success('Language deleted successfully');
        } else {
            ResponseHelper::error('Failed to delete language', 500);
        }
    }
    else {
        ResponseHelper::error('Invalid request', 400);
    }
} catch (Exception $e) {
    ResponseHelper::error($e->getMessage(), 500);
}
?>