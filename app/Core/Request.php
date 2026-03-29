<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $query;
    private array $body;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri = $this->parseUri();
        $this->query = $_GET;
        $this->body = $this->parseBody();
    }

    private function parseUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = strtok($uri, '?');       // Quitar query string
        $uri = rawurldecode($uri);
        $uri = '/' . trim($uri, '/');    // Normalizar: siempre inicia con /

        return $uri === '' ? '/' : $uri;
    }

    private function parseBody(): array
    {
        if ($this->method !== 'POST') {
            return [];
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // JSON body (API requests)
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        // Form data
        return $_POST;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isJson(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_contains($contentType, 'application/json');
    }

    public function isApi(): bool
    {
        return str_starts_with($this->uri, '/api/');
    }

    public function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return $this->isApi() || str_contains($accept, 'application/json');
    }

    /**
     * Obtener valor del query string.
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Obtener valor del body (POST/JSON).
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * Obtener todos los datos del body.
     */
    public function all(): array
    {
        return $this->body;
    }

    /**
     * Obtener un subconjunto de campos del body.
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->body, array_flip($keys));
    }

    /**
     * Obtener el token CSRF del request.
     */
    public function csrfToken(): string
    {
        // Primero del header (para peticiones AJAX)
        $header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if ($header !== '') {
            return $header;
        }

        // Luego del body (para formularios)
        return $this->input('_csrf_token', '');
    }

    /**
     * IP del cliente (considerando proxies).
     */
    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
