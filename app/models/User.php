<?php
require_once __DIR__ . '/../services/AuthenticationInterface.php';
require_once __DIR__ . '/../services/UserRepositoryInterface.php';

class User implements AuthenticationInterface, UserRepositoryInterface {
    private $conn;
    private $table_name = "users";
    
    // User properties
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $status;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll(): array {
        $query = "SELECT user_id, name, email, role, status, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  ORDER BY created_at DESC";
        
        $result = mysqli_query($this->conn, $query);
        
        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    public function getById(int $id): ?array {
        $query = "SELECT user_id, name, email, role, status, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE user_id = ? 
                  LIMIT 1";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
    
    public function getByEmail(string $email): ?array {
        $query = "SELECT user_id, name, email, password, role, status, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE email = ? 
                  LIMIT 1";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
    
    public function emailExists($email) {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_num_rows($result) > 0;
    }
    
    public function create(): array {
        if (empty($this->role)) {
            $this->role = 'student';
        }
        if (empty($this->status)) {
            $this->status = 'active';
        }
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, email, password, role, status) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", 
            $this->name, 
            $this->email, 
            $this->password, 
            $this->role, 
            $this->status
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $this->user_id = mysqli_insert_id($this->conn);
            return ["status" => "success", "message" => "User created successfully", "user_id" => $this->user_id];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
    
    public function update(): array {
        if (empty($this->user_id)) {
            return ["status" => "error", "message" => "User ID is required"];
        }
        
        $query = "UPDATE " . $this->table_name . " 
                  SET name = ?, email = ?, role = ?, status = ? 
                  WHERE user_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssi", 
            $this->name, 
            $this->email, 
            $this->role, 
            $this->status, 
            $this->user_id
        );
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "User updated successfully"];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
    
    public function updatePassword(int $userId, string $newPassword): array {
        if (empty($userId)) {
            return ["status" => "error", "message" => "User ID is required"];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE " . $this->table_name . " 
                  SET password = ? 
                  WHERE user_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $userId);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "Password updated successfully"];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
    
    public function delete(int $id): array {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "User deleted successfully"];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
    
    public function getTotalCount(): int {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = mysqli_query($this->conn, $query);
        
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return (int) $row['total'];
        }
        
        return 0;
    }
    
    public function verifyLogin(string $email, string $password): ?array {
        $user = $this->getByEmail($email);
        
        if ($user && isset($user['password'])) {
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        
        return null;
    }
    
    public function getByRole($role) {
        $query = "SELECT user_id, name, email, role, status, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE role = ? 
                  ORDER BY created_at DESC";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    public function getByStatus($status) {
        $query = "SELECT user_id, name, email, role, status, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE status = ? 
                  ORDER BY created_at DESC";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $status);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        return [];
    }
}
?>
