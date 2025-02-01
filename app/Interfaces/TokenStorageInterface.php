<?php

namespace App\Interfaces;

interface TokenStorageInterface
{
    public function createToken(int $userId): string;
    public function validateToken(string $token): bool;
    public function deleteToken(string $token): void;
    public function getUserIdByToken(string $token): ?int;
}
