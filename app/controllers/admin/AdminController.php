<?php
/**
 * Base AdminController class
 * Provides common functionality for all admin controllers
 */
abstract class AdminController {
    protected $conn;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->checkAuthentication();
    }
    
    /**
     * Check if user is authenticated as admin
     */
    protected function checkAuthentication(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
            exit();
        }
    }
    
    /**
     * Send JSON response
     * 
     * @param array $data Response data
     * @param int $statusCode HTTP status code
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Send success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     */
    protected function success($data = null, string $message = 'Success'): void {
        $response = [
            'status' => 'success',
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $this->jsonResponse($response);
    }
    
    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     */
    protected function error(string $message, int $statusCode = 400): void {
        $this->jsonResponse([
            'status' => 'error',
            'message' => $message
        ], $statusCode);
    }
    
    /**
     * Validate required fields
     * 
     * @param array $data Input data
     * @param array $required Required field names
     * @return bool True if all required fields present
     */
    protected function validateRequired(array $data, array $required): bool {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}
?>
