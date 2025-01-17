<?php

namespace App\Controllers;

use App\Models\Post;

class PostController {
    public function index(): void {
        $posts = Post::all(); // Получить все посты
        header('Content-Type: application/json');
        echo json_encode($posts);
    }

    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        $post = new Post($data['title'], $data['content']);
        $post->save();

        http_response_code(201);
        echo json_encode(['message' => 'Post created']);
    }
}
