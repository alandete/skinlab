<?php

declare(strict_types=1);

namespace App\Middleware;

class SecurityMiddleware
{
    /**
     * Aplica headers de seguridad HTTP a toda respuesta.
     */
    public static function apply(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

        // CSP: permitir CDNs necesarios para los proyectos
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "img-src 'self' data: https:", // https: necesario para imágenes en contenido de proyectos Canvas
            "connect-src 'self' https://www.thecolorapi.com https://cdn.jsdelivr.net",
            "frame-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        header('Content-Security-Policy: ' . $csp);
    }
}
