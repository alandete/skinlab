<?php

declare(strict_types=1);

/**
 * SkinLab - Front Controller
 * Todas las peticiones pasan por aquí.
 */

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');

// Autoloader PSR-4
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $len = strlen($prefix);

    if (strncmp($class, $prefix, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Cargar variables de entorno
App\Core\Env::load(BASE_PATH);

// Helpers globales
require BASE_PATH . '/app/Helpers/functions.php';

// Arrancar aplicación
$app = new App\Core\App();
$app->run();
