<?php

#solid Principle: Single Responsibility Principle applied here.
#This interface defines authentication-related operations.
#This keeps authentication logic separate from user data operations.
interface AuthenticationInterface {
    public function verifyLogin(string $email, string $password): ?array;
    public function updatePassword(int $userId, string $newPassword): array;
}
