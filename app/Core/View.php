<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    /**
     * Renderizar una vista con layout.
     *
     * Las vistas pueden definir variables (como $title) que se propagan al layout.
     *
     * @param string      $view   Ruta con punto: 'auth.login' -> views/auth/login.php
     * @param array       $data   Variables disponibles en la vista
     * @param string|null $layout Layout a usar: 'layouts.app' -> views/layouts/app.php
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layouts.app'): void
    {
        $viewPath = self::resolvePath($view);

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Vista no encontrada: {$view} ({$viewPath})");
        }

        // Renderizar vista y capturar variables definidas en ella
        $content = self::captureWithVars($viewPath, $data);

        // Si hay layout, envolver el contenido
        if ($layout !== null) {
            $layoutPath = self::resolvePath($layout);

            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout no encontrado: {$layout}");
            }

            $data['content'] = $content;
            echo self::capture($layoutPath, $data);
        } else {
            echo $content;
        }
    }

    /**
     * Renderizar una vista parcial (sin layout).
     */
    public static function partial(string $view, array $data = []): string
    {
        $path = self::resolvePath($view);

        if (!file_exists($path)) {
            return '';
        }

        return self::capture($path, $data);
    }

    /**
     * Captura el output de un archivo PHP y actualiza $data con
     * variables nuevas definidas en la vista (ej: $title).
     */
    private static function captureWithVars(string $filePath, array &$data): string
    {
        $__filePath = $filePath;
        $__data = $data;

        extract($__data, EXTR_SKIP);
        ob_start();
        require $__filePath;
        $output = ob_get_clean();

        // Capturar variables nuevas definidas en la vista
        $defined = get_defined_vars();
        unset($defined['__filePath'], $defined['__data'], $defined['output']);

        foreach ($defined as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = $value;
            }
        }

        return $output;
    }

    /**
     * Captura el output de un archivo PHP (sin propagación de variables).
     */
    private static function capture(string $filePath, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $filePath;
        return ob_get_clean();
    }

    /**
     * Convertir notación de punto a ruta de archivo.
     * 'auth.login' -> BASE_PATH/views/auth/login.php
     */
    private static function resolvePath(string $view): string
    {
        $relative = str_replace('.', '/', $view);
        return BASE_PATH . '/views/' . $relative . '.php';
    }
}
