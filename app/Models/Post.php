<?php

namespace App\Models;

use Core\Database;

class Post
{
    private ?int $id = null;
    
    public function __construct(
        private string $title,
        private string $content,
        private int $userId
    ) {}

    public static function findById(int $id): ?self
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        
        if ($row = $stmt->fetch()) {
            $post = new self($row['title'], $row['content'], $row['user_id']);
            $post->id = $row['id'];
            return $post;
        }
        
        return null;
    }

    public static function all(): array
    {
        $db = Database::connect();
        $stmt = $db->query('
            SELECT p.*, u.username 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC
        ');
        return $stmt->fetchAll();
    }

    public function save(): void
    {
        $db = Database::connect();
        
        if (!isset($this->id)) {
            $stmt = $db->prepare('
                INSERT INTO posts (title, content, user_id)
                VALUES (:title, :content, :user_id)
            ');
        } else {
            $stmt = $db->prepare('
                UPDATE posts 
                SET title = :title, content = :content 
                WHERE id = :id
            ');
            $stmt->bindValue(':id', $this->id);
        }
        
        $stmt->execute([
            ':title' => $this->title,
            ':content' => $this->content,
            ':user_id' => $this->userId
        ]);

        if (!isset($this->id)) {
            $this->id = $db->lastInsertId();
        }
    }

    public function delete(): void
    {
        if (!isset($this->id)) {
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
