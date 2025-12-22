<?php
/**
 * ActivityLog Model
 * Handles activity logging for user actions
 */
class ActivityLog {
    private $conn;
    private $table_name = "activity_logs";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Log a user activity
     * 
     * @param int $userId User ID
     * @param string $activityType Type of activity (login, logout, document_create, etc.)
     * @param string $description Optional description
     * @return array Status result
     */
    public function logActivity(int $userId, string $activityType, string $description = ''): array {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, activity_type, activity_description) 
                  VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "iss", $userId, $activityType, $description);
        
        if (mysqli_stmt_execute($stmt)) {
            return ["status" => "success", "message" => "Activity logged"];
        }
        
        return ["status" => "error", "message" => mysqli_error($this->conn)];
    }
    
    /**
     * Get activity logs by user
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of records
     * @return array Activity logs
     */
    public function getByUser(int $userId, int $limit = 50): array {
        $query = "SELECT 
                    activity_id,
                    activity_type,
                    activity_description,
                    created_at
                  FROM " . $this->table_name . "
                  WHERE user_id = ?
                  ORDER BY created_at DESC
                  LIMIT ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Get activity logs by date range
     * 
     * @param string $startDate Start date (Y-m-d format)
     * @param string $endDate End date (Y-m-d format)
     * @return array Activity logs
     */
    public function getByDateRange(string $startDate, string $endDate): array {
        $query = "SELECT 
                    a.activity_id,
                    a.user_id,
                    u.name as user_name,
                    a.activity_type,
                    a.activity_description,
                    a.created_at
                  FROM " . $this->table_name . " a
                  JOIN users u ON a.user_id = u.user_id
                  WHERE DATE(a.created_at) BETWEEN ? AND ?
                  ORDER BY a.created_at DESC";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Get activity summary for charts
     * 
     * @param int $days Number of days to look back
     * @return array Daily activity counts
     */
    public function getActivitySummary(int $days = 7): array {
        $query = "SELECT 
                    DATE(created_at) as activity_date,
                    activity_type,
                    COUNT(*) as count
                  FROM " . $this->table_name . "
                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  GROUP BY DATE(created_at), activity_type
                  ORDER BY activity_date ASC";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $days);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        return [];
    }
}
?>
