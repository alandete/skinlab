<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class AuthMiddleware
{
    /**
     * Verifica que el usuario esté autenticado.
     * Si no lo está, redirige a /login o devuelve 401 en API.
     */
    public function handle(Request $request, ?string $param = null): void
    {
        if (self::isLoggedIn()) {
            return;
        }

        if ($request->expectsJson()) {
            Response::json(['error' => __('general.unauthenticated')], 401);
        }

        Response::redirect('/login');
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id'], $_SESSION['user_role']);
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function username(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isEditor(): bool
    {
        return self::role() === 'editor';
    }

    public static function isGuest(): bool
    {
        return self::role() === 'guest';
    }

    /**
     * Verifica si el usuario tiene al menos el rol especificado.
     * Jerarquía: admin > editor > guest
     */
    public static function hasRole(string $minimumRole): bool
    {
        $hierarchy = ['guest' => 1, 'editor' => 2, 'admin' => 3];
        $userLevel = $hierarchy[self::role()] ?? 0;
        $requiredLevel = $hierarchy[$minimumRole] ?? 0;

        return $userLevel >= $requiredLevel;
    }

    /**
     * Establece la sesión del usuario tras login exitoso.
     */
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['_created']  = time();
    }

    /**
     * Cierra la sesión.
     */
    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }

        session_destroy();
    }
}
