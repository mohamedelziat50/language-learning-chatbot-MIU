<?php
/**
 * Documents Controller
 * Handles all CRUD operations for documents
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
    
    // Validate inputs
    if (empty($document_id) || empty($user_id) || empty($title)) {
        return false;
    }
    
    // First verify the document belongs to the user
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
    
    // Generate preview text (first 120 characters)
    $preview_text = mb_substr($content, 0, 120);
    if (mb_strlen($content) > 120) {
        $preview_text .= '...';
    }
    
    // Update the document
    $sql = "UPDATE documents SET title = '$title', content = '$content', preview_text = '$preview_text' 
            WHERE document_id = $document_id";
    
    return mysqli_query($conn, $sql); // Returns true if the document was updated, false if it was not updated
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

?>
