<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\App;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class RateLimitMiddleware
{
    /**
     * Verificar rate limit por IP y acción.
     * Almacenado en base de datos (no en sesión) para evitar bypass.
     */
    public static function check(Request $request, string $action = 'default'): void
    {
        $config = App::config("security.rate_limit.{$action}", [
            'max'    => 30,
            'window' => 60,
        ]);

        $ip = $request->ip();
        $maxAttempts = $config['max'];
        $window = $config['window'];

        $count = Database::fetch(
            "SELECT COUNT(*) as total FROM rate_limits
             WHERE ip = :ip AND action = :action
             AND attempted_at > DATE_SUB(NOW(), INTERVAL :window SECOND)",
            [
                ':ip'     => $ip,
                ':action' => $action,
                ':window' => $window,
            ]
        );

        if (($count['total'] ?? 0) >= $maxAttempts) {
            Response::tooManyRequests();
        }

        // Registrar intento
        Database::insert(
            "INSERT INTO rate_limits (ip, action) VALUES (:ip, :action)",
            [':ip' => $ip, ':action' => $action]
        );
    }
}
