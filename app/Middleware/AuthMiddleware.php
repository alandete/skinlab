<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class AuthMiddleware
{
    /**
     * Verifica que el usuario esté autenticado y activo.
     */
    public function handle(Request $request, ?string $param = null): void
    {
        if (!self::isLoggedIn()) {
            if ($request->expectsJson()) {
                Response::json(['error' => __('general.unauthenticated')], 401);
            }
            Response::redirect('/login');
        }

        // Verificar que el usuario sigue activo en BD (cache 60s)
        self::checkStillActive();
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id'], $_SESSION['user_role']);
    }

    /**
     * Verificar en BD que el usuario sigue activo.
     * Se cachea por 60 segundos para no consultar en cada request.
     */
    private static function checkStillActive(): void
    {
        $lastCheck = $_SESSION['_active_check'] ?? 0;

        if (time() - $lastCheck < 60) {
            return;
        }

        $userId = self::userId();
        if ($userId === null || !User::isStillActive($userId)) {
            self::logout();
            Response::redirect('/login');
        }

        $_SESSION['_active_check'] = time();
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
        $_SESSION['user_id']      = $user['id'];
        $_SESSION['user_name']    = $user['username'];
        $_SESSION['user_role']    = $user['role'];
        $_SESSION['_created']     = time();
        $_SESSION['_active_check'] = time();

        User::updateLastLogin((int) $user['id']);
    }

    /**
     * Verifica la contraseña del admin actual (re-autenticación).
     */
    public static function verifyCurrentPassword(string $password): bool
    {
        $userId = self::userId();
        if ($userId === null) {
            return false;
        }
        $user = User::findWithPassword($userId);
        if ($user === null) {
            return false;
        }
        return password_verify($password, $user['password']);
    }

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
