<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Project
{
    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT * FROM projects WHERE id = :id",
            [':id' => $id]
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch(
            "SELECT * FROM projects WHERE slug = :slug",
            [':slug' => $slug]
        );
    }

    public static function all(?bool $activeOnly = null): array
    {
        $sql = "SELECT * FROM projects";
        $params = [];

        if ($activeOnly !== null) {
            $sql .= " WHERE is_active = :active";
            $params[':active'] = $activeOnly ? 1 : 0;
        }

        $sql .= " ORDER BY name ASC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Listar proyectos activos con sus páginas (para el dashboard).
     */
    public static function allWithPages(bool $activeOnly = true): array
    {
        $projects = $activeOnly ? self::all(true) : self::all();
        $basePath = STORAGE_PATH . '/projects';

        foreach ($projects as &$proj) {
            $proj['cdns'] = json_decode($proj['cdns'] ?: '[]', true);
            $proj['pages'] = self::scanPages($basePath . '/' . $proj['slug'], $proj['slug']);
            $proj['hasCss'] = file_exists($basePath . '/' . $proj['slug'] . '/css/' . $proj['slug'] . '-master.css');
            $proj['hasJs'] = file_exists($basePath . '/' . $proj['slug'] . '/js/' . $proj['slug'] . '-scripts.js');
        }

        return $projects;
    }

    public static function create(array $data): int
    {
        return Database::insert(
            "INSERT INTO projects (slug, name, description, user_id, color_primary, color_secondary, nav_bg_color, nav_text_color, org_type, org_count, cdns)
             VALUES (:slug, :name, :desc, :uid, :cp, :cs, :nbg, :ntx, :ot, :oc, :cdns)",
            [
                ':slug'  => $data['slug'],
                ':name'  => $data['name'],
                ':desc'  => $data['description'] ?? null,
                ':uid'   => $data['user_id'] ?? null,
                ':cp'    => $data['color_primary'] ?? '#0374B5',
                ':cs'    => $data['color_secondary'] ?? '#2D3B45',
                ':nbg'   => $data['nav_bg_color'] ?? '#394B58',
                ':ntx'   => $data['nav_text_color'] ?? '#FFFFFF',
                ':ot'    => $data['org_type'] ?? 'none',
                ':oc'    => $data['org_count'] ?? 0,
                ':cdns'  => json_encode($data['cdns'] ?? []),
            ]
        );
    }

    public static function update(int $id, array $fields): void
    {
        $allowed = ['name', 'description', 'color_primary', 'color_secondary', 'nav_bg_color', 'nav_text_color', 'org_type', 'org_count', 'cdns'];
        $sets = [];
        $params = [':id' => $id];

        foreach ($fields as $key => $value) {
            if (in_array($key, $allowed, true)) {
                if ($key === 'cdns' && is_array($value)) {
                    $value = json_encode($value);
                }
                $sets[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
        }

        if (empty($sets)) {
            return;
        }

        Database::execute(
            "UPDATE projects SET " . implode(', ', $sets) . " WHERE id = :id",
            $params
        );
    }

    public static function toggleActive(int $id): bool
    {
        Database::execute(
            "UPDATE projects SET is_active = 1 - is_active WHERE id = :id",
            [':id' => $id]
        );
        $proj = self::find($id);
        return $proj !== null && (bool) $proj['is_active'];
    }

    public static function delete(int $id): void
    {
        Database::execute("DELETE FROM projects WHERE id = :id", [':id' => $id]);
    }

    public static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT 1 FROM projects WHERE slug = :slug";
        $params = [':slug' => $slug];

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        return Database::fetch($sql, $params) !== null;
    }

    /**
     * Escanea las páginas de un proyecto clasificadas por tipo.
     * Orden: inicio → organización → adicionales → actividades → snippets → herramientas
     */
    private static function scanPages(string $projectPath, string $slug): array
    {
        $classified = self::getProjectPages($projectPath);

        $pages = [];

        // Inicio
        if (file_exists($projectPath . '/index.html')) {
            $pages[] = ['slug' => 'index', 'name' => 'Inicio'];
        }

        // Organización (sin categoría, siempre arriba)
        $pages = array_merge($pages, $classified['organization']);

        // Páginas adicionales (con categoría)
        if (!empty($classified['custom'])) {
            $classified['custom'][0]['separator'] = true;
            $classified['custom'][0]['separatorLabel'] = 'Páginas';
            $pages = array_merge($pages, $classified['custom']);
        }

        // Actividades (con categoría)
        if (!empty($classified['activities'])) {
            $classified['activities'][0]['separator'] = true;
            $classified['activities'][0]['separatorLabel'] = 'Actividades';
            $pages = array_merge($pages, $classified['activities']);
        }

        // Herramientas (con línea separadora)
        $tools = [];
        if (file_exists($projectPath . '/snippets.html')) {
            $tools[] = ['slug' => 'snippets', 'name' => 'Snippets', 'type' => 'tool', 'separator' => true, 'separatorLabel' => 'Herramientas', 'separatorLine' => true];
        } else {
            $tools[] = ['slug' => 'colors', 'name' => 'Colores', 'type' => 'tool', 'separator' => true, 'separatorLabel' => 'Herramientas', 'separatorLine' => true];
        }
        if (file_exists($projectPath . '/snippets.html')) {
            $tools[] = ['slug' => 'colors', 'name' => 'Colores', 'type' => 'tool'];
        }
        $tools[] = ['slug' => 'accessibility', 'name' => 'Accesibilidad', 'type' => 'tool'];

        return array_merge($pages, $tools);
    }

    /**
     * Devuelve las páginas del proyecto clasificadas por tipo.
     * Usado por scanPages (dashboard) y por la página de edición (admin).
     */
    public static function getProjectPages(string $projectPath): array
    {
        $result = [
            'organization' => [],
            'custom'       => [],
            'activities'   => [],
        ];

        $pagesDir = $projectPath . '/pages';
        if (!is_dir($pagesDir)) {
            return $result;
        }

        $activitySlugs = ['tarea', 'foros', 'quiz'];

        $htmlFiles = glob($pagesDir . '/*.html') ?: [];
        foreach ($htmlFiles as $f) {
            $filename = basename($f, '.html');
            $page = [
                'slug' => $filename,
                'name' => ucwords(str_replace(['-', '_'], ' ', $filename)),
            ];

            if (preg_match('/^(semana|modulo|unidad)-\d+$/', $filename)) {
                $page['type'] = 'organization';
                $result['organization'][] = $page;
            } elseif (in_array($filename, $activitySlugs, true)) {
                $page['type'] = 'activity';
                $result['activities'][] = $page;
            } else {
                $page['type'] = 'custom';
                $result['custom'][] = $page;
            }
        }

        return $result;
    }
}
