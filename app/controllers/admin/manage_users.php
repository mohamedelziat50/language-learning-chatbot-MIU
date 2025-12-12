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
      public static function updateUser() {
        global $conn;

        $data = $_POST;

        if (!isset($data['user_id'])) {
            return ["status"=>"error", "message"=>"Missing user_id"];
        }

        $id     = $data['user_id'];
        $name   = $data['name']   ?? null;
        $email  = $data['email']  ?? null;
        $role   = $data['role']   ?? null;
        $status = $data['status'] ?? null;

        $stmt = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, role=?, status=?
            WHERE user_id=?
        ");
        $stmt->bind_param("ssssi", $name, $email, $role, $status, $id);

        if ($stmt->execute()) {
            return ["status"=>"success", "message"=>"User updated"];
        }

        return ["status"=>"error", "message"=>$stmt->error];
    }
    public static function deleteUser() {
        global $conn;

        if (!isset($_POST['user_id'])) {
            return ["status"=>"error", "message"=>"Missing user_id"];
        }

        $id = $_POST['user_id'];

        $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return ["status"=>"success", "message"=>"User deleted"];
        }

        return ["status"=>"error", "message"=>$stmt->error];
    }
    
}
