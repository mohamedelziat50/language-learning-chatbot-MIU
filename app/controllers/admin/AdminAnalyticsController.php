<?php
require_once __DIR__ . '/AdminController.php';
require_once __DIR__ . '/../../models/Analytics.php';
require_once __DIR__ . '/../../models/User.php';

/**
 * AdminAnalyticsController
 * Handles all analytics-related endpoints for admin dashboard
 */
class AdminAnalyticsController extends AdminController {
    private $analyticsModel;
    private $userModel;
    
    public function __construct($db) {
        parent::__construct($db);
        $this->analyticsModel = new Analytics($db);
        $this->userModel = new User($db);
    }
    
    /**
     * Get overview statistics for dashboard cards
     */
    public function getOverviewStats(): void {
        try {
            $stats = $this->analyticsModel->getOverviewStats();
            $growth = $this->analyticsModel->getGrowthStats();
            
            $this->success([
                'stats' => $stats,
                'growth' => $growth
            ]);
        } catch (Exception $e) {
            $this->error('Failed to fetch overview stats: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get user activity data for charts
     */
    public function getUserActivity(): void {
        try {
            $days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
            $activityData = $this->analyticsModel->getUserActivityData($days);
            
            $this->success([
                'activity' => $activityData
            ]);
        } catch (Exception $e) {
            $this->error('Failed to fetch user activity: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get quiz analytics (already exists in User model)
     */
    public function getQuizAnalytics(): void {
        try {
            $users = $this->userModel->getUsersQuizAverages();
            $summary = $this->userModel->getGlobalQuizPerformanceSummary();
            
            $this->success([
                'users' => $users,
                'summary' => $summary
            ]);
        } catch (Exception $e) {
            $this->error('Failed to fetch quiz analytics: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get common queries/mistakes data
     */
    public function getCommonQueries(): void {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $queries = $this->analyticsModel->getCommonQueries($limit);
            
            $this->success([
                'queries' => $queries
            ]);
        } catch (Exception $e) {
            $this->error('Failed to fetch common queries: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get recent activity feed
     */
    public function getRecentActivity(): void {
        try {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $activities = $this->analyticsModel->getRecentActivity($limit);
            
            $this->success([
                'activities' => $activities
            ]);
        } catch (Exception $e) {
            $this->error('Failed to fetch recent activity: ' . $e->getMessage(), 500);
        }
    }
}
?>
