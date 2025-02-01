<?php

namespace Core;

class Router {
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler, ?callable $middleware = null): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(string $method, string $uri): void {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                // Если есть middleware, выполняем его
                if (isset($route['middleware'])) {
                    $middlewareResult = call_user_func($route['middleware']);
                    if ($middlewareResult === false) {
                        return;
                    }
                }

                if (is_array($route['handler'])) {
                    [$controller, $method] = $route['handler'];
                    $controller->$method();
                } else {
                    call_user_func($route['handler']);
                }
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
