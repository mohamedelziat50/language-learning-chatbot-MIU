<?php
require_once __DIR__ . '/../../../config/db_connect.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Analytics.php';
require_once __DIR__ . '/../../services/UserValidator.php';

/**
 * AdminUserController
 * Handles user management operations for admin (refactored from manage_users.php)
 */
class AdminUserController {
    
    public static function getUsers() {
        global $conn;
        
        $userModel = new User($conn);
        return $userModel->getAll();
    }
    
    public static function getTotalUsersCount() {
        global $conn;
        
        $userModel = new User($conn);
        return $userModel->getTotalCount();
    }
    
    public static function getUsersQuizAverages() {
        global $conn;

        $userModel = new User($conn);
        return $userModel->getUsersQuizAverages();
    }

    public static function getGlobalQuizPerformanceSummary() {
        global $conn;

        $userModel = new User($conn);
        return $userModel->getGlobalQuizPerformanceSummary();
    }
    
    public static function addUser() {
        global $conn;

        $data = $_POST;
        
        // Validate input
        $validator = new UserValidator();
        $validation = $validator->validateCreate($data);
        
        if (!$validation['valid']) {
            return ["status" => "error", "message" => implode(', ', $validation['errors'])];
        }
        
        // Check if email exists
        $userModel = new User($conn);
        if ($userModel->emailExists($data['email'])) {
            return ["status" => "error", "message" => "Email already exists"];
        }

        $userModel->name = $data['name'];
        $userModel->email = $data['email'];
        $userModel->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $userModel->role = $data['role'];
        $userModel->status = $data['status'];

        return $userModel->create();
    }
    
    public static function updateUser() {
        global $conn;

        $data = $_POST;
        
        // Validate input
        $validator = new UserValidator();
        $validation = $validator->validateUpdate($data);
        
        if (!$validation['valid']) {
            return ["status" => "error", "message" => implode(', ', $validation['errors'])];
        }

        $userModel = new User($conn);
        $userModel->user_id = $data['user_id'];
        $userModel->name = $data['name'] ?? null;
        $userModel->email = $data['email'] ?? null;
        $userModel->role = $data['role'] ?? null;
        $userModel->status = $data['status'] ?? null;

        return $userModel->update();
    }
    
    public static function deleteUser() {
        global $conn;

        if (!isset($_POST['user_id'])) {
            return ["status" => "error", "message" => "Missing user_id"];
        }

        $id = $_POST['user_id'];
        
        $userModel = new User($conn);
        return $userModel->delete($id);
    }
    
}
?>

