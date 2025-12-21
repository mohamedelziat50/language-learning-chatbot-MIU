<?php

#Interface Segregation Principle applied here.
#This interface defines user-related data operations.
#as operations related to users are separated from other services like quizzes and authentication.
interface UserRepositoryInterface {
    public function getAll(): array;
    public function getById(int $id): ?array;
    public function getByEmail(string $email): ?array;
    public function create(): array;
    public function update(): array;
    public function delete(int $id): array;
    public function getTotalCount(): int;
}
