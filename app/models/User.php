<?php
/**
 * User Model
 * 
 * Handles all user-related database operations
 */
class User {
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
    
    /**
     * Constructor with database connection
     * @param object $db Database connection object
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all users
     * @return array Array of users
     */
    public function getAll() {
        $query = "SELECT user_id, name, email, role, status, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  ORDER BY created_at DESC";
        
        $result = mysqli_query($this->conn, $query);
        
        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Get user by ID
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public function getById($id) {
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
    
    /**
     * Get user by email
     * @param string $email User email
     * @return array|null User data or null if not found
     */
    public function getByEmail($email) {
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
    
    /**
     * Check if email exists
     * @param string $email Email to check
     * @return bool True if exists, false otherwise
     */
    public function emailExists($email) {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_num_rows($result) > 0;
    }
    
    /**
     * Create new user
     * @return array Response with status and message
     */
    public function create() {
        // Validate required fields
        if (empty($this->name) || empty($this->email) || empty($this->password)) {
            return ["status" => "error", "message" => "Missing required fields"];
        }
        
        // Check if email already exists
        if ($this->emailExists($this->email)) {
            return ["status" => "error", "message" => "Email already exists"];
        }
        
        // Set defaults
        if (empty($this->role)) {
            $this->role = 'student';
        }
        if (empty($this->status)) {
            $this->status = 'active';
        }
        
        // Hash password if not already hashed
        if (!password_get_info($this->password)['algo']) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
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
    
    /**
     * Update user
     * @return array Response with status and message
     */
    public function update() {
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
    
    /**
     * Update user password
     * @param string $newPassword New password
     * @return array Response with status and message
     */
    public function updatePassword($newPassword) {
        if (empty($this->user_id)) {
            return ["status" => "error", "message" => "User ID is required"];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE " . $this->table_name . " 
                  SET password = ? 
                  WHERE user_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $this->user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "Password updated successfully"];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
    
    /**
     * Delete user
     * @param int $id User ID to delete
     * @return array Response with status and message
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "User deleted successfully"];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
    
    /**
     * Get total users count
     * @return int Total number of users
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = mysqli_query($this->conn, $query);
        
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return (int) $row['total'];
        }
        
        return 0;
    }
    
    /**
     * Verify user login credentials
     * @param string $email User email
     * @param string $password Plain text password
     * @return array|null User data if valid, null otherwise
     */
    public function verifyLogin($email, $password) {
        $user = $this->getByEmail($email);
        
        if ($user && isset($user['password'])) {
            if (password_verify($password, $user['password'])) {
                // Remove password from returned data
                unset($user['password']);
                return $user;
            }
        }
        
        return null;
    }
    
    /**
     * Get users by role
     * @param string $role User role (student, tutor, admin)
     * @return array Array of users
     */
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
    
    /**
     * Get users by status
     * @param string $status User status (active, inactive)
     * @return array Array of users
     */
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
