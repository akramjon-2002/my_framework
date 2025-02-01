<?php

namespace App\Middleware;

use App\Interfaces\AuthenticatorInterface;
use Core\Request;
use Core\Response;

class AuthMiddleware
{
    public function __construct(
        private AuthenticatorInterface $authenticator
    ) {}

    public function handle(): bool
    {
        $token = Request::getBearerToken();
        
        if (!$token) {
            Response::json(['error' => 'Authentication required'], 401);
            return false;
        }

        $userId = $this->authenticator->getUserIdByToken($token);
        if (!$userId) {
            Response::json(['error' => 'Invalid token'], 401);
            return false;
        }

        // Сохраняем ID пользователя в Request для дальнейшего использования
        Request::setUserId($userId);
        
        return true;
    }
}
