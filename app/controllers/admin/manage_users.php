<?php
require_once __DIR__ . '/../../../config/db_connect.php';
require_once __DIR__ . '/../../models/User.php';

class UserController {

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
    
    public static function addUser() {
        global $conn;

        $data = $_POST;

        if (!isset($data['name'], $data['email'], $data['password'], $data['role'], $data['status'])) {
            return ["status" => "error", "message" => "Missing required fields"];
        }

        $userModel = new User($conn);
        $userModel->name = $data['name'];
        $userModel->email = $data['email'];
        $userModel->password = $data['password'];
        $userModel->role = $data['role'];
        $userModel->status = $data['status'];

        return $userModel->create();
    }
    
    public static function updateUser() {
        global $conn;

        $data = $_POST;

        if (!isset($data['user_id'])) {
            return ["status" => "error", "message" => "Missing user_id"];
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
