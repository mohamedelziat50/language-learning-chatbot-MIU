<?php
require_once __DIR__ . '/../../../config/db_connect.php'; 

class UserController {

    public static function getUsers() {
        global $conn;

        $result = $conn->query("
            SELECT user_id, name, email, role, status, created_at, updated_at 
            FROM users
        ");

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getTotalUsersCount() {
        global $conn;

        $result = $conn->query("SELECT COUNT(*) as total FROM users");
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public static function addUser() {
        global $conn;

        $data = $_POST;

        if (!isset($data['name'], $data['email'], $data['password'], $data['role'],$data['status'])) {
            return ["status"=>"error", "message"=>"Missing required fields"];
        }

        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'];
        $status = $data['status'];

        $stmt = $conn->prepare("
            INSERT INTO users (name, email, password, role, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssss", $name, $email, $password, $role, $status);

        if ($stmt->execute()) {
            return ["status"=>"success", "message"=>"User created"];
        }

        return ["status"=>"error", "message"=>$stmt->error];
    }
}
