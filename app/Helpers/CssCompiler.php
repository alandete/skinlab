<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Compila CSS master → mobile + desktop.
 *
 * Mobile: elimina html[data-theme="dark"] y @media >= 1200px. Conserva prefers-color-scheme.
 * Desktop: elimina html[data-theme="dark"] y @media (prefers-color-scheme: dark).
 *
 * El dark mode automático se mantiene solo en móvil porque la app de Canvas
 * soporta dark mode nativo. En web desktop, Canvas no tiene dark mode y dejar
 * prefers-color-scheme genera inconsistencia visual (contenido oscuro, UI Canvas claro).
 */
class CssCompiler
{
    public static function compile(string $projectPath, string $slug): bool
    {
        $masterFile = $projectPath . '/css/' . $slug . '-master.css';
        if (!file_exists($masterFile)) {
            return false;
        }

        $master = file_get_contents($masterFile);

        // Eliminar bloque de ambiente de pruebas (html[data-theme="dark"])
        $clean = preg_replace(
            '/\/\*[═\s]*DARK MODE\s*—\s*Ambiente de pruebas[^*]*\*\/\s*html\[data-theme="dark"\]\s*\{[^}]*\}/s',
            '',
            $master
        );
        $clean = preg_replace(
            '/html\[data-theme="dark"\]\s+[^{]+\{[^}]*\}\s*/s',
            '',
            $clean
        );

        // ── Desktop: conserva todo excepto dark-mode automático ──
        $desktop = $clean;

        // Comentario "DARK MODE — Canvas real" + bloque @media
        $desktop = preg_replace(
            '!/\*[═\s]*DARK MODE\s*—\s*Canvas real[^*]*\*/\s*@media\s*\(\s*prefers-color-scheme\s*:\s*dark\s*\)\s*\{(?:[^{}]*|\{[^{}]*\})*\}\s*!s',
            '',
            $desktop
        );
        // Fallback: cualquier @media (prefers-color-scheme: dark) sin el comentario
        $desktop = preg_replace(
            '/@media\s*\(\s*prefers-color-scheme\s*:\s*dark\s*\)\s*\{(?:[^{}]*|\{[^{}]*\})*\}\s*/s',
            '',
            $desktop
        );

        $desktop = preg_replace('/\n{3,}/', "\n\n", $desktop);
        $desktop = trim($desktop) . "\n";

        // ── Mobile: elimina @media >= 1200px (conserva prefers-color-scheme) ──
        $mobile = $clean;

        // Con comentario (X-Large, XX-Large)
        $mobile = preg_replace(
            '!/\*[^*]*(?:X-Large|XX-Large)[^*]*\*/\s*@media\s*\(\s*min-width\s*:\s*(1200|1400)px\s*\)\s*\{(?:[^{}]*|\{[^{}]*\})*\}\s*!s',
            '',
            $mobile
        );

        // Sin comentario
        $mobile = preg_replace(
            '/@media\s*\(\s*min-width\s*:\s*(1[2-9]\d{2}|[2-9]\d{3,})px\s*\)\s*\{(?:[^{}]*|\{[^{}]*\})*\}\s*/s',
            '',
            $mobile
        );

        $mobile = preg_replace('/\n{3,}/', "\n\n", $mobile);
        $mobile = trim($mobile) . "\n";

        // Escribir
        file_put_contents($projectPath . '/css/' . $slug . '-mobile.css', $mobile);
        file_put_contents($projectPath . '/css/' . $slug . '-desktop.css', $desktop);

        return true;
    }
}
