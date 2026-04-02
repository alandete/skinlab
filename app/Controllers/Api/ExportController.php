<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Models\Project;

class ExportController
{
    /**
     * GET /api/export/{slug}
     * Descarga el proyecto como ZIP.
     */
    public function download(Request $request, array $params = []): void
    {
        $slug = $params['slug'] ?? '';

        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            http_response_code(400);
            exit('Proyecto inválido');
        }

        $project = Project::findBySlug($slug);
        if (!$project) {
            http_response_code(404);
            exit('Proyecto no encontrado');
        }

        $projectPath = STORAGE_PATH . '/projects/' . $slug;
        if (!is_dir($projectPath)) {
            http_response_code(404);
            exit('Directorio del proyecto no encontrado');
        }

        $date = date('Y-m-d_H-i-s');
        $filename = $slug . '_' . $date . '.zip';
        $tmpFile = tempnam(sys_get_temp_dir(), 'skinlab_export_');

        $zip = new \ZipArchive();
        if ($zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            http_response_code(500);
            exit('No se pudo crear el ZIP');
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($projectPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $slug . '/' . str_replace('\\', '/', substr($filePath, strlen($projectPath) + 1));

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tmpFile));
        header('Cache-Control: no-cache');
        readfile($tmpFile);
        unlink($tmpFile);
        exit;
    }
}
