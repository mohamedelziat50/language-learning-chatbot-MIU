<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Observer Interface
interface Observer {
    public function update($email);
}

// Email Observer - sends emails to subscribers
class EmailObserver implements Observer {
    public function update($email) {
        $subject = "Welcome to LinguaBot Newsletter!";
        $message = "<html><body>";
        $message .= "<h2>Thank you for subscribing!</h2>";
        $message .= "<p>You will now receive updates and tips about language learning.</p>";
        $message .= "<p>Best regards,<br>LinguaBot Team</p>";
        $message .= "</body></html>";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: LinguaBot <noreply@miuegypt.edu.eg>" . "\r\n";
        
        @mail($email, $subject, $message, $headers);
    }
}

// Newsletter Service - Subject that manages subscribers
class NewsletterService {
    private static $subscribers = array();
    private $observers = array();
    
    public function __construct() {
        $this->attach(new EmailObserver());
    }
    
    public function attach($observer) {
        $this->observers[] = $observer;
    }
    
    public function subscribe($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        if (in_array($email, self::$subscribers)) {
            return false;
        }
        
        self::$subscribers[] = $email;
        
        // Notify all observers
        foreach ($this->observers as $observer) {
            $observer->update($email);
        }
        
        return true;
    }
    
    public static function getSubscribers() {
        return self::$subscribers;
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    
    $service = new NewsletterService();
    
    if ($service->subscribe($email)) {
        echo json_encode([
            'success' => true,
            'message' => 'Successfully subscribed! Check your email.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or already subscribed.'
        ]);
    }
}
