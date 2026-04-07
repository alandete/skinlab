<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Middleware\AuthMiddleware;

/**
 * Renderiza una página del proyecto como HTML completo para el iframe del simulador.
 */
class PreviewController
{
    public function render(Request $request, array $params = []): void
    {
        $project = $request->query('project', '');
        $page = $request->query('page', 'index');
        $theme = $request->query('theme', 'light');

        if (!preg_match('/^[a-z0-9\-_]+$/i', $project) || !preg_match('/^[a-z0-9\-_]+$/i', $page)) {
            http_response_code(400);
            echo 'Parámetros inválidos';
            exit;
        }

        $projectPath = STORAGE_PATH . '/projects/' . $project;
        $realPath = realpath($projectPath);
        $allowedBase = realpath(STORAGE_PATH . '/projects');
        if (!$realPath || !$allowedBase || strpos($realPath, $allowedBase) !== 0 || !is_dir($projectPath)) {
            http_response_code(404);
            echo 'Proyecto no encontrado';
            exit;
        }

        // Resolver archivo HTML
        if ($page === 'index') {
            $filePath = $projectPath . '/index.html';
        } elseif ($page === 'snippets') {
            $filePath = $projectPath . '/snippets.html';
        } else {
            $filePath = $projectPath . '/pages/' . $page . '.html';
        }

        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'Página no encontrada';
            exit;
        }

        $html = file_get_contents($filePath);
        $basePath = '/storage/projects/' . $project;

        $masterFile = $projectPath . '/css/' . $project . '-master.css';
        $mobileFile = $projectPath . '/css/' . $project . '-mobile.css';
        $desktopFile = $projectPath . '/css/' . $project . '-desktop.css';
        $jsFile = $projectPath . '/js/' . $project . '-scripts.js';

        $dataTheme = ($theme === 'dark') ? ' data-theme="dark"' : '';

        header('Content-Type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html>' . "\n";
        echo '<html lang="es"' . $dataTheme . '>' . "\n";
        echo '<head>' . "\n";
        echo '  <meta charset="UTF-8">' . "\n";
        echo '  <meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
        echo '  <title>Preview</title>' . "\n";

        if (file_exists($masterFile)) {
            echo '  <link rel="stylesheet" href="' . $basePath . '/css/' . basename($masterFile) . '">' . "\n";
        } else {
            if (file_exists($mobileFile)) {
                echo '  <link rel="stylesheet" href="' . $basePath . '/css/' . basename($mobileFile) . '">' . "\n";
            }
            if (file_exists($desktopFile)) {
                echo '  <link rel="stylesheet" href="' . $basePath . '/css/' . basename($desktopFile) . '">' . "\n";
            }
        }

        echo '  <style>body { margin: 0; padding: 0 1rem; }</style>' . "\n";
        echo '</head>' . "\n";
        echo '<body>' . "\n";
        echo $html . "\n";

        if (file_exists($jsFile)) {
            echo '  <script src="' . $basePath . '/js/' . basename($jsFile) . '"></script>' . "\n";
        }

        echo '</body>' . "\n";
        echo '</html>';
        exit;
    }
}
