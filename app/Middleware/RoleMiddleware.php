<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class RoleMiddleware
{
    /**
     * Verifica que el usuario tenga el rol mínimo requerido.
     *
     * Uso en rutas: 'role:admin' o 'role:editor'
     * Jerarquía: admin > editor > guest
     */
    public function handle(Request $request, ?string $param = null): void
    {
        if ($param === null) {
            return;
        }

        // Permitir múltiples roles separados por |
        // Ejemplo: 'role:admin|editor'
        $allowedRoles = explode('|', $param);

        $userRole = AuthMiddleware::role();

        // Verificar si el rol del usuario está en la lista permitida
        if (in_array($userRole, $allowedRoles, true)) {
            return;
        }

        // O verificar por jerarquía (tomar el rol más bajo como mínimo)
        $hierarchy = ['guest' => 1, 'editor' => 2, 'admin' => 3];
        $userLevel = $hierarchy[$userRole] ?? 0;

        foreach ($allowedRoles as $role) {
            if ($userLevel >= ($hierarchy[$role] ?? 0)) {
                return;
            }
        }

        Response::forbidden();
    }
}
