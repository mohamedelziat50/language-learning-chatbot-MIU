<?php
/**
 * ResponseHandler - Handles HTTP responses and redirects
 * Follows Single Responsibility Principle - separates presentation logic from business logic
 */
class ResponseHandler {
    
    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    public static function redirect(string $url, int $statusCode = 302): void {
        header("Location: $url", true, $statusCode);
        exit;
    }
    
    public static function alertAndBack(string $message): void {
        echo "<script>alert('" . addslashes($message) . "'); window.history.back();</script>";
        exit;
    }
    
    public static function alertAndRedirect(string $message, string $url): void {
        echo "<script>alert('" . addslashes($message) . "'); window.location.href='" . $url . "';</script>";
        exit;
    }
    
    public static function setSessionMessage(string $message, string $type = 'success'): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    
    public static function redirectWithMessage(string $url, string $message, string $type = 'success'): void {
        self::setSessionMessage($message, $type);
        self::redirect($url);
    }
}
