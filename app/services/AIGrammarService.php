<?php
/**
 * AI Grammar Service
 * Uses OpenAI API to provide intelligent grammar and vocabulary suggestions
 */

require_once __DIR__ . '/../../config/load_env.php';

class AIGrammarService {
    private $api_key;
    private $api_url = "https://api.openai.com/v1/chat/completions";
    private $model = "gpt-4o-mini"; // Use mini for cost efficiency
    
    public function __construct() {
        $this->api_key = getenv('OPENAI_API_KEY');
        if (!$this->api_key) {
            error_log("Warning: OPENAI_API_KEY not set in .env file");
        }
    }
    
    /**
     * Analyze document content using AI
     * Returns array of suggestions in our format
     */
    public function analyzeContent($content, $analyze_grammar = true, $analyze_vocabulary = true) {
        if (!$this->api_key) {
            return false; // API key not configured
        }
        
        if (empty($content) || strlen(trim($content)) < 10) {
            return [];
        }
        
        $prompt = $this->buildPrompt($content, $analyze_grammar, $analyze_vocabulary);
        
        try {
            $response = $this->callOpenAI($prompt);
            return $this->parseAIResponse($response, $content);
        } catch (Exception $e) {
            error_log("AI Grammar Service Error: " . $e->getMessage());
            return false; // Return false to fallback to basic rules
        }
    }
    
    /**
     * Build the prompt for OpenAI
     */
    /**
 * Build the prompt for OpenAI
 */
private function buildPrompt($content, $analyze_grammar, $analyze_vocabulary) {
    
    // --- START: MODIFIED INSTRUCTIONS ---
    $instructions = "You are a professional grammar and writing assistant. 
    **Prioritize comprehensive spelling checks, especially for common typos and easily confused words.**
     Analyze the following text for ";
    
    if ($analyze_grammar && $analyze_vocabulary) {
        $instructions .= "grammar errors, clarity issues, and vocabulary improvements.";
    } elseif ($analyze_grammar) {
        $instructions .= "grammar errors, clarity issues, and spelling errors only.";
    } else {
        $instructions .= "vocabulary and clarity improvements only.";
    }
    
    $prompt = $instructions . "\n\nText to analyze:\n" . $content . "\n\n";
    // --- END: MODIFIED INSTRUCTIONS ---
    
    $prompt .= "Return a JSON array of suggestions. Each suggestion should have:
- type: 'grammar', 'vocabulary', 'spelling', or 'clarity'
- position_start: character position where the issue starts (0-indexed)
- position_end: character position where the issue ends
- original_text: the text that has the issue
- suggested_text: the corrected text (or null if just a suggestion)
- explanation: brief explanation of the issue

Example format:
[
  {
    \"type\": \"grammar\",
    \"position_start\": 25,
    \"position_end\": 27,
    \"original_text\": \"  \",
    \"suggested_text\": \" \",
    \"explanation\": \"Multiple spaces detected. Use a single space.\"
  },
  {
    \"type\": \"spelling\",
    \"position_start\": 0,
    \"position_end\": 4,
    \"original_text\": \"nome\",
    \"suggested_text\": \"name\",
    \"explanation\": \"Spelling error. 'Nome' should be 'name' in this context.\"
  },
  {
    \"type\": \"vocabulary\",
    \"position_start\": 100,
    \"position_end\": 109,
    \"original_text\": \"important\",
    \"suggested_text\": null,
    \"explanation\": \"Word 'important' appears 5 times. Consider using synonyms like 'significant', 'crucial', or 'vital'.\"
  }
]

Return ONLY valid JSON, no other text.";

    return $prompt;
}
    
    /**
     * Call OpenAI API
     */
    private function callOpenAI($prompt) {
        $data = [
            "model" => $this->model,
            "messages" => [
                [
                    "role" => "system",
                    "content" => "You are a professional grammar and writing assistant. Analyze text and provide suggestions in the exact JSON format requested."
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            "temperature" => 0.3, // Lower temperature for more consistent results
            "max_tokens" => 2000
        ];
        
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->api_key
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        if ($http_code !== 200) {
            $error_data = json_decode($response, true);
            $error_msg = $error_data['error']['message'] ?? "HTTP Error $http_code";
            throw new Exception("OpenAI API Error: " . $error_msg);
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception("Invalid response format from OpenAI");
        }
        
        return $result['choices'][0]['message']['content'];
    }
    
    /**
     * Parse AI response and convert to our suggestion format
     */
    private function parseAIResponse($ai_response, $content) {
        // Extract JSON from response (in case there's extra text)
        preg_match('/\[.*\]/s', $ai_response, $matches);
        if (empty($matches)) {
            return [];
        }
        
        $suggestions = json_decode($matches[0], true);
        
        if (!is_array($suggestions)) {
            return [];
        }
        
        // Validate and clean suggestions
        $valid_suggestions = [];
        foreach ($suggestions as $suggestion) {
            // Validate required fields
            if (!isset($suggestion['type']) || 
                !isset($suggestion['position_start']) || 
                !isset($suggestion['position_end']) ||
                !isset($suggestion['explanation'])) {
                continue;
            }
            
            // Validate positions are within content bounds
            $pos_start = intval($suggestion['position_start']);
            $pos_end = intval($suggestion['position_end']);
            
            if ($pos_start < 0 || $pos_end > strlen($content) || $pos_start >= $pos_end) {
                continue;
            }
            
            // Get actual text at position for validation
            $actual_text = substr($content, $pos_start, $pos_end - $pos_start);
            
            $valid_suggestions[] = [
                'type' => $suggestion['type'],
                'position_start' => $pos_start,
                'position_end' => $pos_end,
                'original_text' => $suggestion['original_text'] ?? $actual_text,
                'suggested_text' => $suggestion['suggested_text'] ?? null,
                'explanation' => $suggestion['explanation']
            ];
        }
        
        return $valid_suggestions;
    }
}

?>

