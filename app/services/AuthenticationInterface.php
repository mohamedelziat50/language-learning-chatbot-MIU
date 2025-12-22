<?php

#solid Principle: Single Responsibility Principle applied here.
#This interface defines authentication-related operations.
#This keeps authentication logic separate from user data operations.
interface AuthenticationInterface {
    public function verifyLogin(string $email, string $password): ?array;
    public function updatePassword(int $userId, string $newPassword): array;
}

#Dependency Inversion Principle applied here.
#High-level modules (like controllers) depend on this abstraction rather than concrete implementations.
#login.php can use any class implementing this interface for authentication.