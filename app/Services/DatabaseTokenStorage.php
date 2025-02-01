<?php

namespace App\Services;

use App\Interfaces\TokenStorageInterface;
use Core\Database;

class DatabaseTokenStorage implements TokenStorageInterface
{
    public function createToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32)); // Генерируем случайный токен
        $db = Database::connect();
        
        $stmt = $db->prepare('INSERT INTO tokens (user_id, token) VALUES (:user_id, :token)');
        $stmt->execute([
            ':user_id' => $userId,
            ':token' => $token
        ]);
        
        return $token;
    }

    public function validateToken(string $token): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT id FROM tokens WHERE token = :token');
        $stmt->execute([':token' => $token]);
        
        return $stmt->fetch() !== false;
    }

    public function deleteToken(string $token): void
    {
        $db = Database::connect();
        $stmt = $db->prepare('DELETE FROM tokens WHERE token = :token');
        $stmt->execute([':token' => $token]);
    }

    public function getUserIdByToken(string $token): ?int
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT user_id FROM tokens WHERE token = :token');
        $stmt->execute([':token' => $token]);
        
        if ($row = $stmt->fetch()) {
            return (int) $row['user_id'];
        }
        
        return null;
    }
}
