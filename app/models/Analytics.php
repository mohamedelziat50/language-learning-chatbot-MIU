<?php
/**
 * Analytics Model
 * Handles all analytics-related database operations
 */
class Analytics {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get overview statistics for dashboard
     * Uses only existing tables, gracefully handles missing tables
     */
    public function getOverviewStats(): array {
        $stats = [
            'total_users' => 0,
            'chat_sessions' => 0,
            'active_today' => 0,
            'forum_posts' => 0
        ];
        
        // Total users - always available
        try {
            $query = "SELECT COUNT(*) as total FROM users";
            $result = mysqli_query($this->conn, $query);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $stats['total_users'] = (int)$row['total'];
            }
        } catch (Exception $e) {
            // Keep default value
        }
        
        // Chat sessions - use quizzes as proxy if chat_sessions table doesn't exist
        try {
            // First try chat_sessions table
            $query = "SELECT COUNT(*) as total FROM chat_sessions";
            $result = @mysqli_query($this->conn, $query);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $stats['chat_sessions'] = (int)$row['total'];
            } else {
                // Fallback to quizzes count as proxy
                $query = "SELECT COUNT(*) as total FROM quizzes";
                $result = mysqli_query($this->conn, $query);
                if ($result) {
                    $row = mysqli_fetch_assoc($result);
                    $stats['chat_sessions'] = (int)$row['total'];
                }
            }
        } catch (Exception $e) {
            // Keep default value
        }
        
        // Active users today - from users table updated_at
        try {
            // First try activity_logs table
            $query = "SELECT COUNT(DISTINCT user_id) as total 
                      FROM activity_logs 
                      WHERE DATE(created_at) = CURDATE()";
            $result = @mysqli_query($this->conn, $query);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $stats['active_today'] = (int)$row['total'];
            } else {
                // Fallback to users updated today
                $query = "SELECT COUNT(*) as total 
                          FROM users 
                          WHERE DATE(updated_at) = CURDATE()";
                $result = mysqli_query($this->conn, $query);
                if ($result) {
                    $row = mysqli_fetch_assoc($result);
                    $stats['active_today'] = (int)$row['total'];
                }
            }
        } catch (Exception $e) {
            // Keep default value
        }
        
        // Forum posts - try table, if doesn't exist keep as 0
        try {
            $query = "SELECT COUNT(*) as total FROM forum_posts";
            $result = @mysqli_query($this->conn, $query);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $stats['forum_posts'] = (int)$row['total'];
            }
        } catch (Exception $e) {
            // Keep default value (0)
        }
        
        return $stats;
    }
    
    /**
     * Get user activity data for charts (last 7 days)
     * Uses existing tables if activity_logs doesn't exist
     */
    public function getUserActivityData(int $days = 7): array {
        // Try activity_logs table first
        $query = "SELECT 
                    DATE(created_at) as activity_date,
                    DAYNAME(created_at) as day_name,
                    COUNT(*) as activity_count
                  FROM activity_logs
                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  GROUP BY DATE(created_at), DAYNAME(created_at)
                  ORDER BY activity_date ASC";
        
        $stmt = @mysqli_prepare($this->conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $days);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result) {
                return mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
        
        // Fallback: use quizzes table
        $query = "SELECT 
                    DATE(created_at) as activity_date,
                    DAYNAME(created_at) as day_name,
                    COUNT(*) as activity_count
                  FROM quizzes
                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  GROUP BY DATE(created_at), DAYNAME(created_at)
                  ORDER BY activity_date ASC";
        
        $stmt = mysqli_prepare($this->conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $days);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result) {
                return mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
        
        return [];
    }
    
    /**
     * Get common queries/mistakes
     * Returns static data if table doesn't exist
     */
    public function getCommonQueries(int $limit = 10): array {
        $query = "SELECT 
                    query_id,
                    query_text,
                    category,
                    times_asked,
                    trend_direction,
                    trend_percentage,
                    suggested_action,
                    action_type
                  FROM common_queries
                  ORDER BY times_asked DESC
                  LIMIT ?";
        
        $stmt = @mysqli_prepare($this->conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $limit);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                if (!empty($data)) {
                    return $data;
                }
            }
        }
        
        // Fallback: return static sample data if table doesn't exist
        return [
            [
                'query_id' => 1,
                'query_text' => "How do I use 'there is' vs 'there are'?",
                'category' => 'Grammar',
                'times_asked' => 123,
                'trend_direction' => 'increasing',
                'trend_percentage' => 15,
                'suggested_action' => 'Add to Grammar quiz',
                'action_type' => 'vocabulary'
            ],
            [
                'query_id' => 2,
                'query_text' => 'Present perfect vs past simple confusion',
                'category' => 'Grammar',
                'times_asked' => 110,
                'trend_direction' => 'decreasing',
                'trend_percentage' => -8,
                'suggested_action' => 'Add grammar exercise',
                'action_type' => 'grammar'
            ],
            [
                'query_id' => 3,
                'query_text' => "Pronunciation of 'th' sounds",
                'category' => 'Pronunciation',
                'times_asked' => 89,
                'trend_direction' => 'stable',
                'trend_percentage' => 2,
                'suggested_action' => 'Add pronunciation clip',
                'action_type' => 'pronunciation'
            ]
        ];
    }
    
    /**
     * Get recent activity for dashboard
     * Returns empty array if table doesn't exist
     */
    public function getRecentActivity(int $limit = 5): array {
        $query = "SELECT 
                    a.activity_id,
                    a.activity_type,
                    a.activity_description,
                    a.created_at,
                    u.name as user_name,
                    u.email as user_email
                  FROM activity_logs a
                  JOIN users u ON a.user_id = u.user_id
                  ORDER BY a.created_at DESC
                  LIMIT ?";
        
        $stmt = @mysqli_prepare($this->conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $limit);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result) {
                return mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
        
        // Return empty array if table doesn't exist
        return [];
    }
    
    /**
     * Get growth statistics (percentage change from previous period)
     */
    public function getGrowthStats(): array {
        $stats = [
            'users_growth' => 0,
            'sessions_growth' => 0,
            'active_growth' => 0,
            'posts_growth' => 0
        ];
        
        // Users growth (this month vs last month)
        $query = "SELECT 
                    (SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())) as current_month,
                    (SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))) as last_month";
        
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $current = (int)$row['current_month'];
            $last = (int)$row['last_month'];
            if ($last > 0) {
                $stats['users_growth'] = round((($current - $last) / $last) * 100, 1);
            }
        }
        
        // Similar calculations for other metrics can be added here
        
        return $stats;
    }
}
?>
