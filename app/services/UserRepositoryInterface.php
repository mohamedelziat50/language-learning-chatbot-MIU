<?php

interface UserRepositoryInterface {
    public function getAll(): array;
    public function getById(int $id): ?array;
    public function getByEmail(string $email): ?array;
    public function create(): array;
    public function update(): array;
    public function delete(int $id): array;
    public function getTotalCount(): int;
}
