<?php
// app/models/UserModel.php

require_once __DIR__ . '/../../config/db_connect.php';

class UserModel {
    private int $user_id;
    private string $name;
    private string $email;
    private string $role;
    private string $status;

    public function __construct(int $user_id = 0, string $name = '', string $email = '', string $role = 'student', string $status = 'active') {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->status = $status;
    }

    // ===== Getters =====
    public function getUserId(): int { return $this->user_id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getStatus(): string { return $this->status; }

    /**
     * Save selected language for a user
     * 
     * @param int $user_id User ID
     * @param string $language_code Language code (e.g., 'en', 'es', 'fr')
     * @return bool True if successful, false otherwise
     */
    public static function saveSelectedLanguage(int $user_id, string $language_code): bool {
        global $conn;

        if (!$conn) {
            return false;
        }

        $user_id = (int)$user_id;
        $language_code = trim($language_code);

        $query = "UPDATE users SET selected_language = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $language_code, $user_id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // Add this method to UserModel.php
public static function getSelectedLanguage(int $user_id): ?string {
    global $conn;
    if (!$conn) {
        return null;
    }
    
    $query = "SELECT selected_language FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($selected_language);
    $stmt->fetch();
    $stmt->close();
    
    return $selected_language;
}

// Add this method to get user with language
public static function getUserWithLanguage(int $user_id): ?array {
    global $conn;
    if (!$conn) {
        return null;
    }
    
    $query = "SELECT u.*, l.name as language_name, l.flag 
            FROM users u 
            LEFT JOIN languages l ON u.selected_language = l.code 
            WHERE u.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user;
}
}
?>
