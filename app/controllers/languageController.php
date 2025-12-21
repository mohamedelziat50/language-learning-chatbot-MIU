<?php
// app/controllers/LanguageController.php

header('Content-Type: application/json');

require_once __DIR__ . '/../models/LanguageModel.php';
require_once __DIR__ . '/../../config/db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {

    /*
    |--------------------------------------------------
    | GET ALL LANGUAGES
    |--------------------------------------------------
    */
    if ($method === 'GET' && $action === 'all') {

        $languages = Language::getAll();

        $data = array_map(fn($lang) => [
            'id'   => $lang->getId(),
            'name' => $lang->getName(),
            'code' => $lang->getCode(),
            'flag' => $lang->getFlag()
        ], $languages);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
        exit;
    }

    /*
    |--------------------------------------------------
    | SELECT LANGUAGE FOR USER
    |--------------------------------------------------
    */
    if ($method === 'PUT' && $action === 'select') {

        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['user_id']) || empty($input['code'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'user_id and code required']);
            exit;
        }

        $language = Language::getByCode(strtolower($input['code']));

        if (!$language) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Language not found']);
            exit;
        }

        // Update user's selected language using language_id
        $user_id = mysqli_real_escape_string($conn, $input['user_id']);
        $language_id = mysqli_real_escape_string($conn, $language->getId());

        $sql = "UPDATE users SET selected_language_id = '$language_id' WHERE user_id = '$user_id'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Language selected successfully',
                'data' => [
                    'user_id' => (int)$input['user_id'],
                    'language' => $language->getName(),
                    'code' => $language->getCode()
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }
        exit;
    }

    /*
    |--------------------------------------------------
    | INVALID REQUEST
    |--------------------------------------------------
    */
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>