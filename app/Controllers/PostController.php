<?php

namespace App\Controllers;

use App\Interfaces\AuthenticatorInterface;
use App\Models\Post;
use Core\Request;
use Core\Response;

class PostController
{
    public function __construct(
        private AuthenticatorInterface $authenticator
    ) {}

    public function index(): void
    {
        $posts = Post::all();
        Response::json($posts);
    }

    public function store(): void
    {
        $data = Request::getJson();
        
        // Валидация входных данных
        if (!isset($data['title'], $data['content'])) {
            Response::json(['error' => 'Missing required fields'], 400);
            return;
        }

        // Получаем ID пользователя из Request (установлен в AuthMiddleware)
        $userId = Request::getUserId();
        
        $post = new Post(
            $data['title'],
            $data['content'],
            $userId
        );
        
        $post->save();
        
        Response::json(['message' => 'Post created successfully'], 201);
    }

    public function update(int $id): void
    {
        $post = Post::findById($id);
        if (!$post) {
            Response::json(['error' => 'Post not found'], 404);
            return;
        }

        // Проверка прав доступа
        if ($post->getUserId() !== Request::getUserId()) {
            Response::json(['error' => 'Unauthorized to modify this post'], 403);
            return;
        }

        $data = Request::getJson();
        
        if (isset($data['title'])) {
            $post->setTitle($data['title']);
        }
        
        if (isset($data['content'])) {
            $post->setContent($data['content']);
        }
        
        $post->save();
        
        Response::json(['message' => 'Post updated successfully']);
    }

    public function delete(int $id): void
    {
        $post = Post::findById($id);
        if (!$post) {
            Response::json(['error' => 'Post not found'], 404);
            return;
        }

        // Проверка прав доступа
        if ($post->getUserId() !== Request::getUserId()) {
            Response::json(['error' => 'Unauthorized to delete this post'], 403);
            return;
        }

        $post->delete();
        Response::json(['message' => 'Post deleted successfully']);
    }
}
