<?php
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
require_once '../../models/LanguageRepository.php';

$database = new Database();
$conn = $database->connect();
$languageRepo = new LanguageRepository($conn);

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $data = $language->getById($_GET['id']);
        } else {
            $data = $language->getAll();
        }
        echo json_encode(['success' => true, 'data' => $data]);
    }
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['name']) || !isset($input['code'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        $result = $language->create($input['name'], $input['code']);
        echo json_encode(['success' => $result, 'message' => 'Language created']);
    }
    elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $language->update($input['id'], $input['name'], $input['code']);
        echo json_encode(['success' => $result, 'message' => 'Language updated']);
    }
    elseif ($method === 'DELETE') {
        $input = json_decode(file_get_contents("php://input"), true);
        $result = $language->delete($input['id']);
        echo json_encode(['success' => $result, 'message' => 'Language deleted']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>