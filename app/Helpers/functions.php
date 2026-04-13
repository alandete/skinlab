<?php

declare(strict_types=1);

/**
 * Helpers globales de SkinLab.
 * Funciones cortas para uso frecuente en vistas y controladores.
 */

// ── i18n ──

/**
 * Obtener texto traducido.
 * Ejemplo: __('auth.login_title') o __('auth.welcome', ['name' => 'Juan'])
 */
function __(string $key, array $replace = []): string
{
    return App\Core\Lang::get($key, $replace);
}

// ── Seguridad ──

/**
 * Escapar HTML para prevenir XSS.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generar campo hidden CSRF para formularios.
 */
function csrf_field(): string
{
    return App\Middleware\CsrfMiddleware::field();
}

/**
 * Obtener el token CSRF actual.
 */
function csrf_token(): string
{
    return App\Middleware\CsrfMiddleware::generateToken();
}

// ── URLs ──

/**
 * Generar URL absoluta.
 */
function url(string $path = '/'): string
{
    return rtrim(App\Core\App::config('url', ''), '/') . '/' . ltrim($path, '/');
}

/**
 * URL a un asset público.
 */
function asset(string $path): string
{
    $file = PUBLIC_PATH . '/assets/' . ltrim($path, '/');
    $version = file_exists($file) ? filemtime($file) : time();
    return '/assets/' . ltrim($path, '/') . '?v=' . $version;
}

// ── Sesión ──

/**
 * Obtener y eliminar un mensaje flash de sesión.
 */
function flash(string $key): ?string
{
    $sessionKey = '_flash_' . $key;
    $value = $_SESSION[$sessionKey] ?? null;
    unset($_SESSION[$sessionKey]);
    return $value;
}

/**
 * Establecer un mensaje flash.
 */
function set_flash(string $key, string $value): void
{
    $_SESSION['_flash_' . $key] = $value;
}

// ── Auth shortcuts ──

/**
 * Verificar si hay usuario autenticado.
 */
function auth_check(): bool
{
    return App\Middleware\AuthMiddleware::isLoggedIn();
}

/**
 * Nombre del usuario actual.
 */
function auth_user(): ?string
{
    return App\Middleware\AuthMiddleware::username();
}

/**
 * Rol del usuario actual.
 */
function auth_role(): ?string
{
    return App\Middleware\AuthMiddleware::role();
}

/**
 * Verificar si el usuario es admin.
 */
function is_admin(): bool
{
    return App\Middleware\AuthMiddleware::isAdmin();
}

/**
 * Verificar si el usuario tiene al menos el rol dado.
 */
function has_role(string $role): bool
{
    return App\Middleware\AuthMiddleware::hasRole($role);
}

// ── Utilidades ──

/**
 * Convertir texto a slug seguro.
 */
function to_slug(string $text): string
{
    $slug = mb_strtolower($text);
    $slug = preg_replace('/[áàäâ]/u', 'a', $slug);
    $slug = preg_replace('/[éèëê]/u', 'e', $slug);
    $slug = preg_replace('/[íìïî]/u', 'i', $slug);
    $slug = preg_replace('/[óòöô]/u', 'o', $slug);
    $slug = preg_replace('/[úùüû]/u', 'u', $slug);
    $slug = preg_replace('/ñ/u', 'n', $slug);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Validar formato de color hexadecimal.
 */
function is_hex_color(string $color): bool
{
    return (bool) preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
}

/**
 * Validar formato de slug.
 */
function is_valid_slug(string $slug): bool
{
    return (bool) preg_match('/^[a-z0-9][a-z0-9\-]{0,63}$/', $slug);
}

/**
 * Generar prefijo CSS corto a partir de un slug de proyecto.
 * Ejemplo: 'upcanvas-30' → 'uc30', 'mi-curso' → 'mc', 'identificati' → 'iden'
 */
function css_prefix(string $slug): string
{
    $parts = explode('-', $slug);
    if (count($parts) >= 2) {
        // Tomar primera letra de cada parte + números
        $prefix = '';
        foreach ($parts as $part) {
            if (is_numeric($part)) {
                $prefix .= $part;
            } else {
                $prefix .= $part[0] ?? '';
            }
        }
        return $prefix ?: substr($slug, 0, 4);
    }
    return substr($slug, 0, 4);
}
