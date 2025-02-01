<?php

namespace App\Controllers;

use App\Interfaces\AuthenticatorInterface;
use App\Models\User;
use Core\Request;
use Core\Response;

class AuthController
{
    public function __construct(
        private AuthenticatorInterface $authenticator
    ) {}

    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Валидация входных данных
        if (!isset($data['username'], $data['email'], $data['password'])) {
            Response::json(['error' => 'Missing required fields'], 400);
            return;
        }

        // Проверка существования пользователя
        if (User::findByUsername($data['username'])) {
            Response::json(['error' => 'Username already exists'], 400);
            return;
        }

        // Создание пользователя
        $user = new User(
            $data['username'],
            $data['email'],
            User::hashPassword($data['password'])
        );
        
        $user->save();
        
        Response::json(['message' => 'User registered successfully'], 201);
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Валидация входных данных
        if (!isset($data['username'], $data['password'])) {
            Response::json(['error' => 'Missing credentials'], 400);
            return;
        }

        // Аутентификация
        if (!$this->authenticator->authenticate($data['username'], $data['password'])) {
            Response::json(['error' => 'Invalid credentials'], 401);
            return;
        }

        $user = User::findByUsername($data['username']);
        $token = $this->authenticator->login($user->getId());
        
        Response::json(['token' => $token]);
    }

    public function logout(): void
    {
        $token = Request::getBearerToken();
        if (!$token) {
            Response::json(['error' => 'No token provided'], 401);
            return;
        }

        $this->authenticator->logout($token);
        Response::json(['message' => 'Logged out successfully']);
    }
}
