<?php
require_once __DIR__ . '/../controllers/DocumentsController.php';

function handle_createDocument() {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $document_id = createDocument($user_id, $title, $content);
    echo json_encode(["status" => "success", "document_id" => $document_id]);
}

function handle_getDocuments() {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    $documents = getDocumentsByUser($user_id);
    echo json_encode(["status" => "success", "documents" => $documents]);
}

function handle_getDocument($document_id) {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    $document = getDocumentById($document_id);
    echo json_encode(["status" => "success", "document" => $document]);
}

function handle_updateDocument($document_id) {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $success = updateDocument($document_id, $user_id, $title, $content);
    echo json_encode(["status" => "success", "success" => $success]);
}

function handle_deleteDocument($document_id) {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    $success = deleteDocument($document_id, $user_id);
    echo json_encode(["status" => "success", "success" => $success]);
}

function handle_searchDocuments() {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    $query = $_GET['q'] ?? '';
    $documents = searchDocuments($user_id, $query);
    echo json_encode(["status" => "success", "documents" => $documents]);
}

// Register document routes ALL IN ONE FUNCTION
function registerDocumentRoutes($request, $method) {
    // POST /documents/create
    if ($request === '/documents/create' && $method === 'POST') {
        handle_createDocument();
        return true;
    }
    
    // GET /documents
    if ($request === '/documents' && $method === 'GET') {
        handle_getDocuments();
        return true;
    }
    
    // GET /documents/{id}
    if (preg_match('/^\/documents\/(\d+)$/', $request, $matches) && $method === 'GET') {
        handle_getDocument($matches[1]);
        return true;
    }
    
    // POST /documents/{id}/update
    if (preg_match('/^\/documents\/(\d+)\/update$/', $request, $matches) && $method === 'POST') {
        handle_updateDocument($matches[1]);
        return true;
    }
    
    // POST /documents/{id}/delete
    if (preg_match('/^\/documents\/(\d+)\/delete$/', $request, $matches) && $method === 'POST') {
        handle_deleteDocument($matches[1]);
        return true;
    }
    
    // GET /documents/search?q={query}
    if ($request === '/documents/search' && $method === 'GET') {
        handle_searchDocuments();
        return true;
    }
    
    return false;
}

?>
