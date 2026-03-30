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
            "INSERT INTO projects (slug, name, description, user_id, color_primary, color_secondary, org_type, org_count, cdns)
             VALUES (:slug, :name, :desc, :uid, :cp, :cs, :ot, :oc, :cdns)",
            [
                ':slug'  => $data['slug'],
                ':name'  => $data['name'],
                ':desc'  => $data['description'] ?? null,
                ':uid'   => $data['user_id'] ?? null,
                ':cp'    => $data['color_primary'] ?? '#0374B5',
                ':cs'    => $data['color_secondary'] ?? '#2D3B45',
                ':ot'    => $data['org_type'] ?? 'none',
                ':oc'    => $data['org_count'] ?? 0,
                ':cdns'  => json_encode($data['cdns'] ?? []),
            ]
        );
    }

    public static function update(int $id, array $fields): void
    {
        $allowed = ['name', 'description', 'color_primary', 'color_secondary', 'org_type', 'org_count', 'cdns'];
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
     * Escanea las páginas de un proyecto desde el filesystem.
     */
    private static function scanPages(string $projectPath, string $slug): array
    {
        $pages = [];

        if (!is_dir($projectPath)) {
            return $pages;
        }

        // index.html siempre primero
        if (file_exists($projectPath . '/index.html')) {
            $pages[] = ['slug' => 'index', 'name' => 'Inicio'];
        }

        // Páginas organizativas (semana-01, modulo-02, unidad-03)
        $pagesDir = $projectPath . '/pages';
        if (is_dir($pagesDir)) {
            $orgPages = [];
            $actPages = [];

            $htmlFiles = glob($pagesDir . '/*.html') ?: [];
            foreach ($htmlFiles as $f) {
                $filename = basename($f, '.html');
                $page = [
                    'slug' => $filename,
                    'name' => ucwords(str_replace(['-', '_'], ' ', $filename)),
                ];
                if (preg_match('/^(semana|modulo|unidad)-\d+$/', $filename)) {
                    $orgPages[] = $page;
                } else {
                    $actPages[] = $page;
                }
            }

            $pages = array_merge($pages, $orgPages, $actPages);
        }

        // Palette
        if (file_exists($projectPath . '/palette.html')) {
            $pages[] = ['slug' => 'palette', 'name' => 'Paleta de colores'];
        }

        // Snippets al final
        if (file_exists($projectPath . '/snippets.html')) {
            $pages[] = ['slug' => 'snippets', 'name' => 'Snippets'];
        }

        return $pages;
    }
}
