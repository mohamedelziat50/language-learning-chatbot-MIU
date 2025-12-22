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
    | GET USER PROFILE (SELECTED LANGUAGE)
    |--------------------------------------------------
    */
    if ($method === 'GET' && $action === 'profile') {

        // For demo purposes, assuming user_id comes from session
        // In a real app, you'd get this from session
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit;
        }

        $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
        $sql = "SELECT selected_language_id, selected_language FROM users WHERE user_id = '$user_id'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            echo json_encode([
                'success' => true,
                'data' => [
                    'selected_language_id' => $row['selected_language_id'],
                    'selected_language' => $row['selected_language']
                ]
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        exit;
    }
    if ($method === 'PUT' && $action === 'select') {

        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        if (empty($input['code'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Language code required']);
            exit;
        }

        $language = Language::getByCode(strtolower($input['code']));

        if (!$language) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Language not found']);
            exit;
        }

        // Update user's selected language using both ID and code
        $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
        $language_id = mysqli_real_escape_string($conn, $language->getId());
        $language_code = mysqli_real_escape_string($conn, $language->getCode());

        $sql = "UPDATE users SET selected_language_id = '$language_id', selected_language = '$language_code' WHERE user_id = '$user_id'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Language selected successfully',
                'data' => [
                    'user_id' => $_SESSION['user_id'],
                    'language_id' => $language->getId(),
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