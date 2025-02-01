<?php

namespace Core;

class Request
{
    private static ?int $userId = null;

    public static function getBearerToken(): ?string
    {
        $headers = self::getAuthorizationHeader();
        if (!$headers) {
            return null;
        }

        // HEADER: Get the access token from the header
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private static function getAuthorizationHeader(): ?string
    {
        $headers = null;
        
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        return $headers;
    }

    public static function setUserId(int $userId): void
    {
        self::$userId = $userId;
    }

    public static function getUserId(): ?int
    {
        return self::$userId;
    }

    public static function getJson(): ?array
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    public static function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        
        return $path;
    }
}
