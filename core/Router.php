<?php

namespace Core;

class Router {
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler): void {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(string $method, string $uri): void {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                if (is_array($route['handler'])) {
                    [$class, $method] = $route['handler'];
                    (new $class())->$method();
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
