<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function redirect(string $url, int $status = 302): never
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    public static function notFound(): never
    {
        http_response_code(404);
        if (self::expectsJson()) {
            self::json(['error' => __('general.not_found')], 404);
        }
        View::render('errors.404', [], 'layouts.error');
        exit;
    }

    public static function forbidden(?string $message = null): never
    {
        $msg = $message ?? __('general.forbidden');
        http_response_code(403);
        if (self::expectsJson()) {
            self::json(['error' => $msg], 403);
        }
        View::render('errors.403', ['message' => $msg], 'layouts.error');
        exit;
    }

    public static function error(string $message, int $status = 500): never
    {
        http_response_code($status);
        if (self::expectsJson()) {
            self::json(['error' => $message], $status);
        }
        View::render('errors.500', ['message' => $message], 'layouts.error');
        exit;
    }

    public static function tooManyRequests(): never
    {
        http_response_code(429);
        if (self::expectsJson()) {
            self::json(['error' => __('general.too_many_requests')], 429);
        }
        echo __('general.too_many_requests');
        exit;
    }

    private static function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        return str_contains($accept, 'application/json')
            || str_contains($contentType, 'application/json')
            || str_starts_with(strtok($uri, '?'), '/api/');
    }
}
