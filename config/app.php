<?php

declare(strict_types=1);

/**
 * Configuración general de la aplicación.
 */

return [
    'name'     => 'SkinLab',
    'version'  => '1.0.0',
    'locale'   => 'es',
    'timezone' => 'America/Bogota',
    'debug'    => App\Core\Env::get('APP_DEBUG', 'false') === 'true',
    'url'      => App\Core\Env::get('APP_URL', 'https://skinlab.test'),

    // Sesión
    'session' => [
        'lifetime' => 7200, // 2 horas
        'name'     => 'skinlab_session',
    ],

    // Seguridad
    'security' => [
        'csrf_exempt' => [],
        'rate_limit'  => [
            'login'            => ['max' => 5,  'window' => 60],
            'reset_password'   => ['max' => 3,  'window' => 60],
            'user_management'  => ['max' => 20, 'window' => 60],
            'api'              => ['max' => 60, 'window' => 60],
            'default'          => ['max' => 30, 'window' => 60],
        ],
    ],

    // Roles disponibles
    'roles' => [
        'admin'  => 'Administrador',
        'editor' => 'Editor',
        'guest'  => 'Invitado',
    ],

    // CDNs disponibles para proyectos
    'cdns' => [
        'bootstrap'       => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        'bootstrap-icons' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        'fontawesome'     => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        'animate'         => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
    ],

    // Colores por defecto
    'defaults' => [
        'color_primary'   => '#0374B5',
        'color_secondary' => '#2D3B45',
    ],
];
