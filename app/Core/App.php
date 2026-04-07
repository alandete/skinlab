<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\SecurityMiddleware;
use App\Middleware\CsrfMiddleware;

class App
{
    private static array $config = [];
    private static bool $booted = false;

    public function __construct()
    {
        if (self::$booted) {
            return;
        }

        self::$config = require BASE_PATH . '/config/app.php';
        date_default_timezone_set(self::config('timezone', 'America/Bogota'));

        $this->initSession();
        Lang::setLocale(self::config('locale', 'es'));

        self::$booted = true;
    }

    public function run(): void
    {
        SecurityMiddleware::apply();

        $request = new Request();

        // CSRF automático en todas las peticiones POST
        if ($request->isPost()) {
            $exempt = self::config('security.csrf_exempt', []);
            if (!in_array($request->uri(), $exempt, true)) {
                CsrfMiddleware::validate($request);
            }
        }

        // Cargar rutas y despachar
        $router = new Router();
        $routeLoader = require BASE_PATH . '/config/routes.php';
        $routeLoader($router);
        $router->dispatch($request);
    }

    private function initSession(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $sessionName = self::config('session.name', 'skinlab_session');
        $lifetime = self::config('session.lifetime', 7200);

        session_name($sessionName);
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'secure'   => str_starts_with(self::config('url', ''), 'https'),
            'httponly'  => true,
            'samesite'  => 'Lax',
        ]);
        session_start();

        // Regenerar ID periódicamente para evitar fijación de sesión
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
    }

    /**
     * Acceder a configuración con notación de punto.
     * Ejemplo: App::config('security.rate_limit.login.max')
     */
    public static function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Verificar si la aplicación está instalada (schema + admin creado).
     */
    public static function isInstalled(): bool
    {
        try {
            if (!Database::isSchemaReady()) {
                return false;
            }
            $setting = Database::fetch(
                "SELECT setting_value FROM settings WHERE setting_key = 'app_installed'"
            );
            return $setting !== null && $setting['setting_value'] === '1';
        } catch (\Exception) {
            return false;
        }
    }

    public static function isDebug(): bool
    {
        return (bool) self::config('debug', false);
    }
}
