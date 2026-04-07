<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware
{
    /**
     * Genera un token CSRF y lo almacena en sesión.
     */
    public static function generateToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Devuelve el input hidden HTML para formularios.
     */
    public static function field(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida el token CSRF del request.
     * Se ejecuta automáticamente en todas las peticiones POST.
     */
    public static function validate(Request $request): void
    {
        $token = $request->csrfToken();

        if (empty($token) || !isset($_SESSION['_csrf_token'])) {
            self::fail($request);
        }

        if (!hash_equals($_SESSION['_csrf_token'], $token)) {
            self::fail($request);
        }

        // Rotar token después de validación exitosa
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    private static function fail(Request $request): never
    {
        if ($request->expectsJson()) {
            Response::json(['error' => __('general.csrf_invalid')], 403);
        }

        // Redirigir a la URI actual (como GET) para evitar loops
        set_flash('error', __('general.csrf_invalid'));
        Response::redirect($request->uri());
    }
}
