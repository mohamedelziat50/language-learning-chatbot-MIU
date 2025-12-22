<?php
/**
 * Documents Controller
 * Handles all CRUD operations for documents and grammar/vocabulary analysis
 * Uses direct SQL queries!
 */

require_once __DIR__ . '/../../config/db_connect.php'; # Gives us the $conn variable

function createDocument($user_id, $title, $content) {
    global $conn;
    
    // Validate inputs
    if (empty($user_id) || empty($title)) {
        return false;
    }
    
    // Use default preview text (content may not exist yet)
    $preview_text = "New document - Start writing to see preview...";
    
    // Insert document
    $sql = "INSERT INTO documents (owner_id, title, content, preview_text, language) 
            VALUES ($user_id, '$title', '$content', '$preview_text', 'English')";
    
    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn); // Returns the document_id of the newly created document
    }
    
    return false; // Returns false if the document was not created
}

/**
 * Get a document by its ID
 */
function getDocumentById($document_id) {
    global $conn;
    
    $sql = "SELECT * FROM documents WHERE document_id = $document_id";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result); // Returns the document data as an associative array (JUST LIKE A PYTHON DICT)
    }
    
    return false; // Returns false if the document was not found
}

/**
 * Get all documents for a specific user
 */
function getDocumentsByUser($user_id) {
    global $conn;
    
    $sql = "SELECT * FROM documents WHERE owner_id = $user_id ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $documents = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $documents[] = $row;
        }
    }
    
    return $documents; // Returns the documents data as an array of associative arrays (JUST LIKE A PYTHON LIST)
}

/**
 * Update a document
 */
function updateDocument($document_id, $user_id, $title, $content) {
    global $conn;
    
    // Check database connection
    if (!$conn || mysqli_connect_errno()) {
        error_log("Database connection error: " . mysqli_connect_error());
        return false;
    }
    
    // Validate inputs
    if (empty($document_id) || empty($user_id) || empty($title)) {
        error_log("Update document validation failed: document_id=$document_id, user_id=$user_id, title=" . (empty($title) ? 'empty' : 'set'));
        return false;
    }
    
    // First verify the document belongs to the user
    $check_sql = "SELECT owner_id FROM documents WHERE document_id = $document_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (!$check_result) {
        error_log("Check document SQL error: " . mysqli_error($conn));
        return false;
    }
    
    if (mysqli_num_rows($check_result) === 0) {
        error_log("Document not found: document_id=$document_id");
        return false; // Returns false if the document was not found
    }
    
    $doc = mysqli_fetch_assoc($check_result); // Returns the document data as an associative array (JUST LIKE A PYTHON DICT)
    
    // Verify ownership
    if ($doc['owner_id'] != $user_id) {
        error_log("Ownership verification failed: document_id=$document_id, owner_id={$doc['owner_id']}, user_id=$user_id");
        return false; // Returns false if the document does not belong to the user
    }
    
    // Generate preview text (first 120 characters)
    $preview_text = mb_substr($content, 0, 120);
    if (mb_strlen($content) > 120) {
        $preview_text .= '...';
    }
    
    // Escape all values to prevent SQL injection (apostrophes within the title or content will break the query)
    $title_escaped = mysqli_real_escape_string($conn, $title);
    $content_escaped = mysqli_real_escape_string($conn, $content);
    $preview_text_escaped = mysqli_real_escape_string($conn, $preview_text);
    
    // Update the document (also update updated_at timestamp)
    $sql = "UPDATE documents SET title = '$title_escaped', content = '$content_escaped', preview_text = '$preview_text_escaped', updated_at = NOW() 
            WHERE document_id = $document_id";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        // Log the SQL error for debugging
        error_log("Update document SQL error: " . mysqli_error($conn));
        return false;
    }
    
    return true; // Returns true if the document was updated successfully
}

/**
 * Delete a document
 */
function deleteDocument($document_id, $user_id) {
    global $conn;
    
    // Validate inputs
    if (empty($document_id) || empty($user_id)) {
        return false;
    }
    
    // First verify the document belongs to the user (security check)
    $check_sql = "SELECT owner_id FROM documents WHERE document_id = $document_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (!$check_result || mysqli_num_rows($check_result) === 0) {
        return false; // Returns false if the document was not found
    }
    
    $doc = mysqli_fetch_assoc($check_result); // Returns the document data as an associative array (JUST LIKE A PYTHON DICT)
    
    // Verify ownership
    if ($doc['owner_id'] != $user_id) {
        return false; // Returns false if the document does not belong to the user
    }
    
    // Delete the document
    $sql = "DELETE FROM documents WHERE document_id = $document_id";
    
    return mysqli_query($conn, $sql); // Returns true if the document was deleted, false if it was not deleted
}

/**
 * Search documents by title or content
 */
function searchDocuments($user_id, $query) {
    global $conn;

    // If no query, return all documents for the user
    if (empty($query)) {
        return getDocumentsByUser($user_id);
    }
    
    // SEARCH FOR DOCUMENTS BY TITLE OR CONTENT
    // ORDER BY UPDATED AT IN DESCENDING ORDER (NEWEST FIRST)
    $sql = "SELECT * FROM documents 
            WHERE owner_id = $user_id 
            AND (title LIKE '%$query%' OR content LIKE '%$query%')  --
            ORDER BY updated_at DESC";
    
    $result = mysqli_query($conn, $sql); // Returns the result of the search query
    
    $documents = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $documents[] = $row; // Adds the document data to the list
        }
    }
    
    return $documents; // Returns the documents data as an array of associative arrays (JUST LIKE A PYTHON LIST)
}

/**
 * Analyze grammar in document content
 * Creates suggestions for grammar issues
 * Uses AI API if available, falls back to basic rules
 * Returns array with 'suggestions' and 'api_status' keys
 */
function analyzeGrammar($document_id, $content) {
    global $conn;
    
    // Validate inputs
    if (empty($document_id) || empty($content)) {
        return ['suggestions' => [], 'api_status' => 'error', 'error' => 'Invalid input'];
    }
    
    $suggestions = [];
    
    // Try AI-powered analysis first
    require_once __DIR__ . '/../services/AIGrammarService.php';
    $ai_service = new AIGrammarService();
    $ai_suggestions = $ai_service->analyzeContent($content, true, false);
    
    // Check API status
    if ($ai_suggestions === false) {
        // API failed - check if it's because API key is missing
        $api_key = getenv('GROQ_API_KEY');
        if (!$api_key) {
            return ['suggestions' => [], 'api_status' => 'no_key', 'error' => 'GROQ_API_KEY not configured in .env'];
        }
        return ['suggestions' => [], 'api_status' => 'failed', 'error' => 'Groq API call failed. Check error logs for details.'];
    }
    
    // Only use AI-powered analysis; if none returned, stop here
    if (!is_array($ai_suggestions) || count($ai_suggestions) === 0) {
        return ['suggestions' => [], 'api_status' => 'success', 'error' => null];
    }
    
    foreach ($ai_suggestions as $ai_suggestion) {
        if ($ai_suggestion['type'] === 'grammar' || $ai_suggestion['type'] === 'spelling') {
            $position_start = $ai_suggestion['position_start'];
            $position_end = $ai_suggestion['position_end'];
            $original_text = $ai_suggestion['original_text'];
            $suggested_text = $ai_suggestion['suggested_text'] ?? '';
            $explanation = $ai_suggestion['explanation'];
            
            // Check if suggestion already exists
            $check_sql = "SELECT suggestion_id FROM document_suggestions 
                         WHERE document_id = $document_id 
                         AND suggestion_type = 'grammar' 
                         AND position_start = $position_start 
                         AND position_end = $position_end";
            $check_result = mysqli_query($conn, $check_sql);
            
            // Only insert if there's an actual change (original_text != suggested_text)
            if ($original_text !== $suggested_text && $check_result && mysqli_num_rows($check_result) == 0) {
                $sql = "INSERT INTO document_suggestions 
                        (document_id, suggestion_type, original_text, suggested_text, position_start, position_end, explanation) 
                        VALUES ($document_id, 'grammar', '" . mysqli_real_escape_string($conn, $original_text) . "', 
                        '" . mysqli_real_escape_string($conn, $suggested_text) . "', $position_start, $position_end, 
                        '" . mysqli_real_escape_string($conn, $explanation) . "')";
                
                if (mysqli_query($conn, $sql)) {
                    $suggestions[] = mysqli_insert_id($conn);
                }
            }
        }
    }
    
    return ['suggestions' => $suggestions, 'api_status' => 'success', 'error' => null];
}

/**
 * Analyze vocabulary in document content
 * Creates suggestions for vocabulary improvements
 * Uses AI API if available, falls back to basic rules
 * Returns array with 'suggestions' and 'api_status' keys
 */
function analyzeVocabulary($document_id, $content) {
    global $conn;
    
    // Validate inputs
    if (empty($document_id) || empty($content)) {
        return ['suggestions' => [], 'api_status' => 'error', 'error' => 'Invalid input'];
    }
    
    $suggestions = [];
    
    // Try AI-powered analysis first
    require_once __DIR__ . '/../services/AIGrammarService.php';
    $ai_service = new AIGrammarService();
    $ai_suggestions = $ai_service->analyzeContent($content, false, true);
    
    // Check API status
    if ($ai_suggestions === false) {
        // API failed - check if it's because API key is missing
        $api_key = getenv('GROQ_API_KEY');
        if (!$api_key) {
            return ['suggestions' => [], 'api_status' => 'no_key', 'error' => 'GROQ_API_KEY not configured in .env'];
        }
        return ['suggestions' => [], 'api_status' => 'failed', 'error' => 'Groq API call failed. Check error logs for details.'];
    }
    
    // Only use AI-powered analysis; if none returned, stop here
    if (!is_array($ai_suggestions) || count($ai_suggestions) === 0) {
        return ['suggestions' => [], 'api_status' => 'success', 'error' => null];
    }
    
    foreach ($ai_suggestions as $ai_suggestion) {
        if ($ai_suggestion['type'] === 'vocabulary' || $ai_suggestion['type'] === 'clarity') {
            $position_start = $ai_suggestion['position_start'];
            $position_end = $ai_suggestion['position_end'];
            $original_text = $ai_suggestion['original_text'];
            $suggested_text = $ai_suggestion['suggested_text'] ?? null;
            $explanation = $ai_suggestion['explanation'];
            
            // Check if suggestion already exists for this word/position
            $word_escaped = mysqli_real_escape_string($conn, $original_text);
            $check_sql = "SELECT suggestion_id FROM document_suggestions 
                         WHERE document_id = $document_id 
                         AND suggestion_type = 'vocabulary' 
                         AND original_text = '$word_escaped'
                         AND position_start = $position_start";
            $check_result = mysqli_query($conn, $check_sql);
            
            if ($check_result && mysqli_num_rows($check_result) == 0) {
                $suggested_text_escaped = $suggested_text ? "'" . mysqli_real_escape_string($conn, $suggested_text) . "'" : "NULL";
                
                $sql = "INSERT INTO document_suggestions 
                        (document_id, suggestion_type, original_text, suggested_text, position_start, position_end, explanation) 
                        VALUES ($document_id, 'vocabulary', '" . mysqli_real_escape_string($conn, $original_text) . "', 
                        $suggested_text_escaped, $position_start, $position_end, 
                        '" . mysqli_real_escape_string($conn, $explanation) . "')";
                
                if (mysqli_query($conn, $sql)) {
                    $suggestions[] = mysqli_insert_id($conn);
                }
            }
        }
    }
    
    return ['suggestions' => $suggestions, 'api_status' => 'success', 'error' => null];
}

/**
 * Get all suggestions for a document
 */
function getSuggestions($document_id) {
    global $conn;
    
    // Validate inputs
    if (empty($document_id)) {
        return false;
    }
    
    // Get current document content
    $doc_sql = "SELECT content FROM documents WHERE document_id = $document_id";
    $doc_result = mysqli_query($conn, $doc_sql);
    
    if (!$doc_result || mysqli_num_rows($doc_result) === 0) {
        return [];
    }
    
    $document = mysqli_fetch_assoc($doc_result);
    $content = $document['content'];
    $content_length = strlen($content);
    
    // Get all suggestions
    $sql = "SELECT * FROM document_suggestions 
            WHERE document_id = $document_id 
            ORDER BY position_start ASC, created_at ASC";
    $result = mysqli_query($conn, $sql);
    
    $suggestions = [];
    $invalid_suggestion_ids = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $position_start = intval($row['position_start']);
            $position_end = intval($row['position_end']);
            $original_text = $row['original_text'];
            
            // Validate position bounds
            if ($position_start < 0 || $position_start > $content_length ||
                $position_end < $position_start || $position_end > $content_length) {
                // Invalid positions - mark for deletion
                $invalid_suggestion_ids[] = $row['suggestion_id'];
                continue;
            }
            
            // Validate text matches (only for non-insert suggestions)
            if ($position_start != $position_end) {
                $actual_text = substr($content, $position_start, $position_end - $position_start);
                
                if ($actual_text !== $original_text) {
                    // Text doesn't match - try to find it nearby
                    $search_start = max(0, $position_start - 50);
                    $found_position = strpos($content, $original_text, $search_start);
                    
                    if ($found_position === false) {
                        // Text not found - mark for deletion
                        $invalid_suggestion_ids[] = $row['suggestion_id'];
                        continue;
                    }
                }
            }
            
            // Suggestion is valid
            $suggestions[] = $row;
        }
    }
    
    return $suggestions;
}

/**
 * Apply a suggestion to update the document content
 */
function applySuggestion($suggestion_id) {
    global $conn;
    
    // Validate inputs
    if (empty($suggestion_id)) {
        return false;
    }
    
    // Get the suggestion
    $sql = "SELECT * FROM document_suggestions WHERE suggestion_id = $suggestion_id";
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        return false; // Returns false if the suggestion was not found
    }
    
    $suggestion = mysqli_fetch_assoc($result);
    
    // Get the document
    $doc_sql = "SELECT * FROM documents WHERE document_id = " . $suggestion['document_id'];
    $doc_result = mysqli_query($conn, $doc_sql);
    
    if (!$doc_result || mysqli_num_rows($doc_result) === 0) {
        return false; // Returns false if the document was not found
    }
    
    $document = mysqli_fetch_assoc($doc_result);
    $content = $document['content'];
    
    // Apply the suggestion
    $position_start = intval($suggestion['position_start']);
    $position_end = intval($suggestion['position_end']);
    $original_text = $suggestion['original_text'];
    $suggested_text = $suggestion['suggested_text'] ?? '';
    
    // Validate positions - ensure they're within content bounds
    $content_length = strlen($content);
    if ($position_start < 0 || $position_start > $content_length) {
        error_log("Invalid position_start: $position_start for content length $content_length");
        return false;
    }
    if ($position_end < $position_start || $position_end > $content_length) {
        error_log("Invalid position_end: $position_end for content length $content_length (start: $position_start)");
        return false;
    }
    
    // Verify text matches - check if text at stored position matches original_text
    if ($position_start != $position_end) {
        $actual_text_at_position = substr($content, $position_start, $position_end - $position_start);
        
        if ($actual_text_at_position !== $original_text) {
            // Find correct position - search for original_text near expected position
            $search_start = max(0, $position_start - 50);
            $found_position = strpos($content, $original_text, $search_start);
            
            if ($found_position !== false) {
                // Found it! Update positions
                $position_start = $found_position;
                $position_end = $found_position + strlen($original_text);
            } else {
                // Safe replacement - only replace if text is found and matches
                error_log("Original text '{$original_text}' not found at or near expected position {$suggestion['position_start']}");
                return false;
            }
        }
    }
    
    // If position_start equals position_end, it means we're inserting (not replacing)
    // This is used for punctuation suggestions where we just add "." at the end
    if ($position_start == $position_end) {
        // Insert the suggested text at the position (length 0 means insert, not replace)
        $new_content = substr_replace($content, $suggested_text, $position_start, 0);
    } else {
        // Replace the original text with suggested text
        $replacement_length = $position_end - $position_start;
        $new_content = substr_replace($content, $suggested_text, $position_start, $replacement_length);
    }
    
    // Update preview text (first 120 characters)
    $preview_text = mb_substr($new_content, 0, 120);
    if (mb_strlen($new_content) > 120) {
        $preview_text .= '...';
    }
    
    // Update the document
    $update_sql = "UPDATE documents SET content = '" . mysqli_real_escape_string($conn, $new_content) . "', 
                   preview_text = '" . mysqli_real_escape_string($conn, $preview_text) . "' 
                   WHERE document_id = " . $suggestion['document_id'];
    
    if (mysqli_query($conn, $update_sql)) {
        // Delete the applied suggestion
        $delete_sql = "DELETE FROM document_suggestions WHERE suggestion_id = $suggestion_id";
        mysqli_query($conn, $delete_sql);
        
        // Delete all remaining suggestions - their positions are now invalid after content change
        $delete_all_sql = "DELETE FROM document_suggestions WHERE document_id = " . $suggestion['document_id'];
        mysqli_query($conn, $delete_all_sql);
        
        return true; // Returns true if the suggestion was applied successfully
    }
    
    return false; // Returns false if the suggestion was not applied
}

?>
