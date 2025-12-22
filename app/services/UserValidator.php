<?php
/**
 * UserValidator - Handles validation logic for user data
 * Follows Single Responsibility Principle
 */
class UserValidator {
    
    public function validateCreate(array $data): array {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    public function validateUpdate(array $data): array {
        $errors = [];
        
        if (empty($data['user_id'])) {
            $errors[] = 'User ID is required';
        }
        
        if (isset($data['name']) && empty($data['name'])) {
            $errors[] = 'Name cannot be empty';
        }
        
        if (isset($data['email'])) {
            if (empty($data['email'])) {
                $errors[] = 'Email cannot be empty';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
        }
        
        if (isset($data['role']) && !in_array($data['role'], ['student', 'tutor', 'admin'])) {
            $errors[] = 'Invalid role. Must be student, tutor, or admin';
        }
        
        if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
            $errors[] = 'Invalid status. Must be active or inactive';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    public function validateLogin(string $email, string $password): array {
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
