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

function handle_analyzeDocument($document_id) {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    // Verify document ownership
    $document = getDocumentById($document_id);
    if (!$document) {
        echo json_encode(["status" => "error", "message" => "Document not found"]);
        return;
    }
    
    if ($document['owner_id'] != $user_id) {
        echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
        return;
    }
    
    $content = $document['content'] ?? '';
    if (empty($content)) {
        echo json_encode(["status" => "error", "message" => "Document content is empty"]);
        return;
    }
    
    // Get analysis type from request body (default to both)
    $input = json_decode(file_get_contents('php://input'), true);
    $analyze_grammar = $input['analyze_grammar'] ?? true;
    $analyze_vocabulary = $input['analyze_vocabulary'] ?? true;
    
    $suggestions = [];
    
    // Analyze grammar
    if ($analyze_grammar) {
        $grammar_suggestions = analyzeGrammar($document_id, $content);
        if ($grammar_suggestions !== false) {
            $suggestions = array_merge($suggestions, $grammar_suggestions);
        }
    }
    
    // Analyze vocabulary
    if ($analyze_vocabulary) {
        $vocab_suggestions = analyzeVocabulary($document_id, $content);
        if ($vocab_suggestions !== false) {
            $suggestions = array_merge($suggestions, $vocab_suggestions);
        }
    }
    
    echo json_encode([
        "status" => "success", 
        "message" => "Analysis completed",
        "suggestions_count" => count($suggestions),
        "suggestion_ids" => $suggestions
    ]);
}

function handle_getSuggestions($document_id) {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    // Verify document ownership
    $document = getDocumentById($document_id);
    if (!$document) {
        echo json_encode(["status" => "error", "message" => "Document not found"]);
        return;
    }
    
    if ($document['owner_id'] != $user_id) {
        echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
        return;
    }
    
    $suggestions = getSuggestions($document_id);
    
    if ($suggestions === false) {
        echo json_encode(["status" => "error", "message" => "Failed to retrieve suggestions"]);
        return;
    }
    
    echo json_encode([
        "status" => "success",
        "suggestions" => $suggestions,
        "count" => count($suggestions)
    ]);
}

function handle_applySuggestion($suggestion_id) {
    session_start();
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        return;
    }
    
    // Get the suggestion to verify document ownership
    require_once __DIR__ . '/../../config/db_connect.php';
    global $conn;
    
    $sql = "SELECT ds.*, d.owner_id FROM document_suggestions ds 
            JOIN documents d ON ds.document_id = d.document_id 
            WHERE ds.suggestion_id = " . intval($suggestion_id);
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        echo json_encode(["status" => "error", "message" => "Suggestion not found"]);
        return;
    }
    
    $suggestion_data = mysqli_fetch_assoc($result);
    
    // Verify document ownership
    if ($suggestion_data['owner_id'] != $user_id) {
        echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
        return;
    }
    
    // Apply the suggestion
    $success = applySuggestion($suggestion_id);
    
    if ($success) {
        echo json_encode([
            "status" => "success",
            "message" => "Suggestion applied successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to apply suggestion"
        ]);
    }
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
    
    // POST /documents/{id}/analyze
    if (preg_match('/^\/documents\/(\d+)\/analyze$/', $request, $matches) && $method === 'POST') {
        handle_analyzeDocument($matches[1]);
        return true;
    }
    
    // GET /documents/{id}/suggestions
    if (preg_match('/^\/documents\/(\d+)\/suggestions$/', $request, $matches) && $method === 'GET') {
        handle_getSuggestions($matches[1]);
        return true;
    }
    
    // POST /suggestions/{id}/applySuggestion
    if (preg_match('/^\/suggestions\/(\d+)\/applySuggestion$/', $request, $matches) && $method === 'POST') {
        handle_applySuggestion($matches[1]);
        return true;
    }
    
    return false;
}

?>
