<?php

namespace App\Services;

use App\Interfaces\AuthenticatorInterface;
use App\Interfaces\TokenStorageInterface;
use App\Models\User;

class Authenticator implements AuthenticatorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage
    ) {}

    public function authenticate(string $username, string $password): bool
    {
        $user = User::findByUsername($username);
        if (!$user) {
            return false;
        }

        return $user->verifyPassword($password);
    }

    public function login(int $userId): string
    {
        $user = User::findById($userId);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        return $this->tokenStorage->createToken($userId);
    }

    public function logout(string $token): void
    {
        $this->tokenStorage->deleteToken($token);
    }

    public function getUserIdByToken(string $token): ?int
    {
        if (!$this->tokenStorage->validateToken($token)) {
            return null;
        }

        return $this->tokenStorage->getUserIdByToken($token);
    }
}
