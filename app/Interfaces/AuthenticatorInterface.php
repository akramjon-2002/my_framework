<?php

namespace App\Interfaces;

interface AuthenticatorInterface
{
    public function authenticate(string $username, string $password): bool;
    public function login(int $userId): string; // Возвращает токен
    public function logout(string $token): void;
    public function getUserIdByToken(string $token): ?int;
}
