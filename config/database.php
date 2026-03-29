<?php

declare(strict_types=1);

/**
 * Configuración de base de datos MySQL.
 */

return [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'skinlab',
    'username' => 'skinlab_user',
    'password' => 'SkinLab2026',
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',

    // Opciones PDO
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
    ],
];
