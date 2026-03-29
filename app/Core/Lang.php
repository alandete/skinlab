<?php

declare(strict_types=1);

namespace App\Core;

class Lang
{
    private static string $locale = 'es';
    private static string $fallback = 'es';
    private static array $loaded = [];

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
    }

    public static function locale(): string
    {
        return self::$locale;
    }

    /**
     * Obtener texto traducido.
     *
     * Formato: 'archivo.clave' -> lang/{locale}/archivo.php ['clave']
     * Soporta reemplazos: __('auth.welcome', ['name' => 'Juan']) -> 'Bienvenido, :name'
     *
     * @param string $key     Clave con formato 'archivo.clave'
     * @param array  $replace Reemplazos [:clave => valor]
     */
    public static function get(string $key, array $replace = []): string
    {
        $dotPos = strpos($key, '.');
        if ($dotPos === false) {
            return $key;
        }

        $file = substr($key, 0, $dotPos);
        $item = substr($key, $dotPos + 1);

        // Cargar archivo del idioma actual
        self::loadFile($file, self::$locale);

        // Buscar la clave
        $value = self::$loaded[self::$locale][$file][$item] ?? null;

        // Fallback al idioma base si no se encontró
        if ($value === null && self::$locale !== self::$fallback) {
            self::loadFile($file, self::$fallback);
            $value = self::$loaded[self::$fallback][$file][$item] ?? null;
        }

        // Si no existe en ningún idioma, devolver la clave
        if ($value === null) {
            return $key;
        }

        // Aplicar reemplazos
        if (!empty($replace)) {
            foreach ($replace as $search => $replacement) {
                $value = str_replace(':' . $search, (string) $replacement, $value);
            }
        }

        return $value;
    }

    /**
     * Obtener todas las traducciones de un archivo.
     * Útil para pasar al frontend como JSON.
     */
    public static function getGroup(string $file): array
    {
        self::loadFile($file, self::$locale);
        return self::$loaded[self::$locale][$file] ?? [];
    }

    private static function loadFile(string $file, string $locale): void
    {
        if (isset(self::$loaded[$locale][$file])) {
            return;
        }

        $path = BASE_PATH . '/lang/' . $locale . '/' . $file . '.php';

        if (file_exists($path)) {
            self::$loaded[$locale][$file] = require $path;
        } else {
            self::$loaded[$locale][$file] = [];
        }
    }
}
