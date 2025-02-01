<?php

use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Middleware\AuthMiddleware;
use App\Services\Authenticator;
use App\Services\DatabaseTokenStorage;

// Инициализация сервисов
$tokenStorage = new DatabaseTokenStorage();
$authenticator = new Authenticator($tokenStorage);
$authMiddleware = new AuthMiddleware($authenticator);

return [
    // Открытые маршруты
    ['method' => 'POST', 'path' => '/register', 'handler' => [new AuthController($authenticator), 'register']],
    ['method' => 'POST', 'path' => '/login', 'handler' => [new AuthController($authenticator), 'login']],
    
    // Защищенные маршруты
    [
        'method' => 'GET',
        'path' => '/posts',
        'handler' => [new PostController($authenticator), 'index'],
        'middleware' => [$authMiddleware, 'handle']
    ],
    [
        'method' => 'POST',
        'path' => '/posts',
        'handler' => [new PostController($authenticator), 'store'],
        'middleware' => [$authMiddleware, 'handle']
    ],
    [
        'method' => 'POST',
        'path' => '/logout',
        'handler' => [new AuthController($authenticator), 'logout'],
        'middleware' => [$authMiddleware, 'handle']
    ]
];
