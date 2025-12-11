<?php
// app/controllers/languageController.php

header('Content-Type: application/json');

require_once __DIR__ . '/../models/LanguageModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'all';

try {

    /*
    |--------------------------------------------------------------------------
    | GET ALL LANGUAGES
    |--------------------------------------------------------------------------
    */
    if ($method === 'GET' && $action === 'all') {

        $languages = Language::getAll();

        $data = array_map(fn($lang) => [
            'id'    => $lang->getId(),
            'name'  => $lang->getName(),
            'code'  => $lang->getCode(),
            'flag'  => $lang->getFlag()
        ], $languages);

        echo json_encode([
            'success' => true,
            'message' => 'Languages retrieved successfully',
            'data' => $data
        ]);
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET LANGUAGE BY ID
    |--------------------------------------------------------------------------
    */
    if ($method === 'GET' && $action === 'show' && isset($_GET['id'])) {

        $language = Language::getById((int)$_GET['id']);

        if (!$language) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Language not found']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Language retrieved successfully',
            'data' => [
                'id'    => $language->getId(),
                'name'  => $language->getName(),
                'code'  => $language->getCode(),
                'flag'  => $language->getFlag()
            ]
        ]);
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE LANGUAGE (POST)
    |--------------------------------------------------------------------------
    */
    if ($method === 'POST') {

        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['name']) || empty($input['code'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Name and code are required']);
            exit;
        }

        $language = new Language(
            0, 
            trim($input['name']),
            strtolower(trim($input['code'])),
            $input['flag'] ?? ''
        );

        if ($language->save()) {
            echo json_encode(['success' => true, 'message' => 'Language created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create language']);
        }
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE LANGUAGE (PUT)
    |--------------------------------------------------------------------------
    */
    if ($method === 'PUT') {

        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID is required']);
            exit;
        }

        $language = Language::getById((int)$input['id']);

        if (!$language) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Language not found']);
            exit;
        }

        if (isset($input['name'])) $language->setName($input['name']);
        if (isset($input['code'])) $language->setCode(strtolower($input['code']));
        if (isset($input['flag'])) $language->setFlag($input['flag']);

        if (Language::update(
            $language->getId(),
            $language->getName(),
            $language->getCode(),
            $language->getFlag()
        )) {
            echo json_encode(['success' => true, 'message' => 'Language updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update language']);
        }
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE LANGUAGE (DELETE)
    |--------------------------------------------------------------------------
    */
    if ($method === 'DELETE') {

        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID is required']);
            exit;
        }

        $language = Language::getById((int)$input['id']);

        if (!$language) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Language not found']);
            exit;
        }

        if ($language->delete()) {
            echo json_encode(['success' => true, 'message' => 'Language deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete language']);
        }
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | INVALID REQUEST
    |--------------------------------------------------------------------------
    */
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid endpoint or method']);


    } catch (Exception $e) {
    
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ]);
    }
    
?>