<?php

namespace App\Models;

use Core\Database;

class Post {
    public function __construct(private string $title, private string $content) {}

    public static function all(): array {
        $db = Database::connect();
        $stmt = $db->query('SELECT * FROM posts');
        return $stmt->fetchAll();
    }

    public function save(): void {
        $db = Database::connect();
        $stmt = $db->prepare('INSERT INTO posts (title, content) VALUES (:title, :content)');
        $stmt->execute([':title' => $this->title, ':content' => $this->content]);
    }
}
