<?php

namespace App\Models;

use Core\Database;

class User
{
    private ?int $id;
    
    public function __construct(
        private string $username,
        private string $email,
        private string $password,
        private string $role = 'user'
    ) {}

    public static function findByUsername(string $username): ?self
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute([':username' => $username]);
        
        if ($row = $stmt->fetch()) {
            $user = new self($row['username'], $row['email'], $row['password'], $row['role']);
            $user->id = $row['id'];
            return $user;
        }
        
        return null;
    }

    public static function findById(int $id): ?self
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        
        if ($row = $stmt->fetch()) {
            $user = new self($row['username'], $row['email'], $row['password'], $row['role']);
            $user->id = $row['id'];
            return $user;
        }
        
        return null;
    }

    public function save(): void
    {
        $db = Database::connect();
        
        if (!isset($this->id)) {
            $stmt = $db->prepare('
                INSERT INTO users (username, email, password, role)
                VALUES (:username, :email, :password, :role)
            ');
        } else {
            $stmt = $db->prepare('
                UPDATE users 
                SET username = :username, email = :email, password = :password, role = :role 
                WHERE id = :id
            ');
            $stmt->bindValue(':id', $this->id);
        }
        
        $stmt->execute([
            ':username' => $this->username,
            ':email' => $this->email,
            ':password' => $this->password,
            ':role' => $this->role
        ]);

        if (!isset($this->id)) {
            $this->id = $db->lastInsertId();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
