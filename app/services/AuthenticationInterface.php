<?php

interface AuthenticationInterface {
    public function verifyLogin(string $email, string $password): ?array;
    public function updatePassword(int $userId, string $newPassword): array;
}
