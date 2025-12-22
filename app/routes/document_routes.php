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
    try {
        session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Not logged in"]);
            return;
        }
        
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        
        // Log request details for debugging (without logging full content if too long)
        $content_length = strlen($content);
        error_log("Update document request: document_id=$document_id, user_id=$user_id, title_length=" . strlen($title) . ", content_length=$content_length");
        
        // Validate inputs
        if (empty($title)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Title is required"]);
            return;
        }
        
        $success = updateDocument($document_id, $user_id, $title, $content);
        
        if ($success) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Document updated successfully"]);
        } else {
            http_response_code(500);
            error_log("Update document failed - check previous error logs for details");
            echo json_encode([
                "status" => "error", 
                "message" => "Failed to update document. Please check server error logs for details."
            ]);
        }
    } catch (Exception $e) {
        error_log("Update document exception: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Server error occurred"]);
    } catch (Error $e) {
        error_log("Update document fatal error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Server error occurred"]);
    }
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
    
    // Delete all existing suggestions for this document before analyzing
    require_once __DIR__ . '/../../config/db_connect.php';
    global $conn;
    $delete_all_sql = "DELETE FROM document_suggestions WHERE document_id = $document_id";
    mysqli_query($conn, $delete_all_sql);
    
    $suggestions = [];
    $api_statuses = [];
    $errors = [];
    
    // Analyze grammar
    if ($analyze_grammar) {
        $grammar_result = analyzeGrammar($document_id, $content);
        if (is_array($grammar_result) && isset($grammar_result['suggestions'])) {
            $suggestions = array_merge($suggestions, $grammar_result['suggestions']);
            $api_statuses['grammar'] = $grammar_result['api_status'] ?? 'unknown';
            if (!empty($grammar_result['error'])) {
                $errors['grammar'] = $grammar_result['error'];
            }
        }
    }
    
    // Analyze vocabulary
    if ($analyze_vocabulary) {
        $vocab_result = analyzeVocabulary($document_id, $content);
        if (is_array($vocab_result) && isset($vocab_result['suggestions'])) {
            $suggestions = array_merge($suggestions, $vocab_result['suggestions']);
            $api_statuses['vocabulary'] = $vocab_result['api_status'] ?? 'unknown';
            if (!empty($vocab_result['error'])) {
                $errors['vocabulary'] = $vocab_result['error'];
            }
        }
    }
    
    // Determine overall status
    $overall_status = 'success';
    $message = "Analysis completed";
    
    if (in_array('failed', $api_statuses) || in_array('no_key', $api_statuses)) {
        $overall_status = 'warning';
        $message = "Analysis completed with API issues";
    } elseif (in_array('error', $api_statuses)) {
        $overall_status = 'error';
        $message = "Analysis failed";
    }
    
    echo json_encode([
        "status" => $overall_status, 
        "message" => $message,
        "suggestions_count" => count($suggestions),
        "suggestion_ids" => $suggestions,
        "api_status" => $api_statuses,
        "errors" => $errors
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
    
    // GET /documents/api-status (check if OpenAI API is working)
    if ($request === '/documents/api-status' && $method === 'GET') {
        handle_checkApiStatus();
        return true;
    }
    
    return false;
}

function handle_checkApiStatus() {
    require_once __DIR__ . '/../services/AIGrammarService.php';
    require_once __DIR__ . '/../../config/load_env.php';
    
    $api_key = getenv('OPENAI_API_KEY');
    
    if (!$api_key) {
        echo json_encode([
            "status" => "error",
            "api_configured" => false,
            "message" => "OpenAI API key not configured. Please set OPENAI_API_KEY in your .env file."
        ]);
        return;
    }
    
    // Test with a simple API call
    $ai_service = new AIGrammarService();
    $test_content = "This is a test sentence with a grammer error.";
    $result = $ai_service->analyzeContent($test_content, true, false);
    
    if ($result === false) {
        echo json_encode([
            "status" => "error",
            "api_configured" => true,
            "api_working" => false,
            "message" => "OpenAI API key is configured but API call failed. Check error logs for details."
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "api_configured" => true,
            "api_working" => true,
            "message" => "OpenAI API is working correctly."
        ]);
    }
}

?>
