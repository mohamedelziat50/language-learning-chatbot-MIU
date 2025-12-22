<?php
/**
 * Groq AI Assistant Service
 * Handles AI chat/conversation for document editing assistance
 */

require_once __DIR__ . '/../../config/load_env.php';

class GroqAIAssistant {
    private $api_key;
    private $api_url = "https://api.groq.com/openai/v1/chat/completions";
    private $model = "llama-3.1-8b-instant";
    
    public function __construct() {
        $this->api_key = getenv('GROQ_API_KEY');
        if (!$this->api_key) {
            error_log("Warning: GROQ_API_KEY not set in .env file");
        }
    }
    
    /**
     * Generate AI response for document assistant chat
     * @param string $userMessage The user's message/request
     * @param string $documentContent Optional: current document content for context
     * @param array $conversationHistory Optional: previous messages in the conversation
     * @return array Response with 'success', 'message', and optional 'error'
     */
    public function generateResponse($userMessage, $documentContent = '', $conversationHistory = []) {
        if (!$this->api_key) {
            return [
                'success' => false,
                'error' => 'GROQ_API_KEY not configured in .env'
            ];
        }
        
        if (empty($userMessage)) {
            return [
                'success' => false,
                'error' => 'User message is required'
            ];
        }
        
        // Build messages array with system prompt and conversation history
        $messages = [
            [
                'role' => 'system',
                'content' => "You are a helpful writing assistant for a language learning platform. Help users improve their writing, answer questions about grammar and vocabulary, suggest improvements, and provide writing tips. Be concise, friendly, and educational. If the user provides document content, use it as context for your suggestions."
            ]
        ];
        
        // Add conversation history
        foreach ($conversationHistory as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }
        
        // Add document content as context if provided
        $contextMessage = $userMessage;
        if (!empty($documentContent)) {
            $contextMessage = "Document content:\n" . substr($documentContent, 0, 2000) . "\n\nUser request: " . $userMessage;
        }
        
        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $contextMessage
        ];
        
        // Prepare API request
        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];
        
        // Call Groq API
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("Groq AI Assistant cURL Error: " . $error);
            return [
                'success' => false,
                'error' => 'Failed to connect to AI service: ' . $error
            ];
        }
        
        if ($http_code !== 200) {
            $error_data = json_decode($response, true);
            $error_msg = $error_data['error']['message'] ?? "HTTP Error $http_code";
            error_log("Groq AI Assistant API Error ($http_code): " . $error_msg);
            return [
                'success' => false,
                'error' => 'AI service error: ' . $error_msg
            ];
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0]['message']['content'])) {
            error_log("Groq AI Assistant: Invalid response format");
            return [
                'success' => false,
                'error' => 'Invalid response format from AI service'
            ];
        }
        
        return [
            'success' => true,
            'message' => $result['choices'][0]['message']['content']
        ];
    }
}

