<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array $handler, array $middleware): self
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'handler'    => $handler,
            'middleware'  => $middleware,
        ];

        return $this;
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['path'], $uri);

            if ($params === false) {
                continue;
            }

            // Ejecutar middleware de la ruta
            $this->runMiddleware($route['middleware'], $request);

            // Ejecutar controlador
            [$controllerClass, $action] = $route['handler'];
            $controller = new $controllerClass();
            $controller->$action($request, $params);
            return;
        }

        // Ninguna ruta coincidió
        Response::notFound();
    }

    /**
     * Compara un patrón de ruta con la URI actual.
     * Soporta parámetros: /project/{slug} -> ['slug' => 'valor']
     *
     * @return array|false Parámetros extraídos o false si no coincide
     */
    private function matchRoute(string $pattern, string $uri): array|false
    {
        // Ruta estática (sin parámetros)
        if (!str_contains($pattern, '{')) {
            return $pattern === $uri ? [] : false;
        }

        // Convertir {param} a regex con grupo nombrado
        $regex = preg_replace(
            '/\{([a-zA-Z_]+)\}/',
            '(?P<$1>[a-z0-9][a-z0-9\-_]*)',
            $pattern
        );
        $regex = '#^' . $regex . '$#i';

        if (preg_match($regex, $uri, $matches)) {
            // Solo devolver los grupos nombrados (strings)
            return array_filter($matches, fn($key) => is_string($key), ARRAY_FILTER_USE_KEY);
        }

        return false;
    }

    /**
     * Ejecuta la cadena de middleware.
     * Formato: ['auth', 'role:admin', 'role:editor']
     */
    private function runMiddleware(array $middlewareList, Request $request): void
    {
        $map = [
            'auth' => \App\Middleware\AuthMiddleware::class,
            'role' => \App\Middleware\RoleMiddleware::class,
        ];

        foreach ($middlewareList as $entry) {
            $parts = explode(':', $entry, 2);
            $name = $parts[0];
            $param = $parts[1] ?? null;

            if (!isset($map[$name])) {
                continue;
            }

            $middleware = new $map[$name]();
            $middleware->handle($request, $param);
        }
    }
}
