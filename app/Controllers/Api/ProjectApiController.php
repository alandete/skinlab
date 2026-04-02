<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Models\Project;
use App\Middleware\AuthMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Helpers\CssCompiler;

class ProjectApiController
{
    /**
     * GET /api/projects
     * Listar proyectos (activos para todos, todos para admin).
     */
    public function list(Request $request, array $params = []): void
    {
        $activeOnly = !AuthMiddleware::isAdmin();
        $projects = Project::allWithPages($activeOnly);

        Response::json(['projects' => $projects]);
    }

    /**
     * POST /api/projects/create
     */
    public function create(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'api');

        $name    = trim($request->input('name', ''));
        $slug    = strtolower(trim($request->input('slug', '')));
        $cdns    = $request->input('cdns', []);
        $colors  = $request->input('colors', []);
        $orgType = $request->input('orgType', 'none');
        $orgCount = (int) ($request->input('orgCount', 0));

        // Validaciones
        if ($name === '' || $slug === '') {
            Response::json(['error' => __('general.field_required')], 400);
        }

        if (!is_valid_slug($slug) || mb_strlen($slug) > 64) {
            Response::json(['error' => __('admin.invalid_slug')], 400);
        }

        if (mb_strlen($name) > 100) {
            Response::json(['error' => __('general.field_too_long', ['max' => 100])], 400);
        }

        if (Project::slugExists($slug)) {
            Response::json(['error' => __('admin.project_exists')], 409);
        }

        if ($orgCount < 0 || $orgCount > 30) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $colorPrimary = $colors['primary'] ?? '#0374B5';
        $colorSecondary = $colors['secondary'] ?? '#2D3B45';
        $navColors = $request->input('navColors', []);
        $navBgColor = $navColors['bg'] ?? '#394B58';
        $navTextColor = $navColors['text'] ?? '#FFFFFF';

        if (!is_hex_color($colorPrimary) || !is_hex_color($colorSecondary)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }
        if (!is_hex_color($navBgColor) || !is_hex_color($navTextColor)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        // Crear directorio del proyecto
        $projectPath = STORAGE_PATH . '/projects/' . $slug;
        $dirs = [$projectPath, $projectPath . '/pages', $projectPath . '/css', $projectPath . '/js', $projectPath . '/img'];

        foreach ($dirs as $dir) {
            if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                Response::json(['error' => __('admin.project_create_error')], 500);
            }
        }

        // CDN imports para el CSS
        $cdnUrls = App::config('cdns', []);
        $imports = '';
        if (is_array($cdns)) {
            foreach ($cdns as $cdn) {
                if (isset($cdnUrls[$cdn])) {
                    $imports .= '@import url("' . $cdnUrls[$cdn] . '");' . "\n";
                }
            }
        }

        $escName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $templatesDir = BASE_PATH . '/templates';

        // Generar archivos desde templates
        $tpl = function (string $file, array $vars = []) use ($templatesDir): string {
            $content = file_get_contents($templatesDir . '/' . $file);
            foreach ($vars as $key => $val) {
                $content = str_replace('{{' . $key . '}}', $val, $content);
            }
            return $content;
        };

        // HTML
        file_put_contents($projectPath . '/index.html', $tpl('index.html', ['PROJECT_NAME' => $escName]));
        file_put_contents($projectPath . '/pages/tarea.html', $tpl('pages/tarea.html'));
        file_put_contents($projectPath . '/pages/quiz.html', $tpl('pages/quiz.html'));
        file_put_contents($projectPath . '/pages/foros.html', $tpl('pages/foros.html'));
        file_put_contents($projectPath . '/palette.html', $tpl('palette.html'));
        file_put_contents($projectPath . '/snippets.html', $tpl('snippets.html'));

        // CSS master
        $masterContent = $tpl('css/master.css', ['IMPORTS' => $imports]);

        // Reemplazar colores base
        $masterContent = preg_replace(
            '/--ct-primary-base:\s*#[0-9A-Fa-f]{6}/',
            '--ct-primary-base: ' . $colorPrimary,
            $masterContent
        );
        $masterContent = preg_replace(
            '/--ct-secondary-base:\s*#[0-9A-Fa-f]{6}/',
            '--ct-secondary-base: ' . $colorSecondary,
            $masterContent
        );
        file_put_contents($projectPath . '/css/' . $slug . '-master.css', $masterContent);

        // Compilar mobile + desktop
        CssCompiler::compile($projectPath, $slug);

        // JS
        file_put_contents($projectPath . '/js/' . $slug . '-scripts.js', $tpl('js/scripts.js', ['PROJECT_NAME' => $escName]));

        // Páginas organizativas
        if ($orgType !== 'none' && $orgCount > 0) {
            $orgLabels = ['semanas' => 'Semana', 'modulos' => 'Módulo', 'unidades' => 'Unidad'];
            $orgLabel = $orgLabels[$orgType] ?? '';
            $orgTpl = file_get_contents($templatesDir . '/pages/organization.html');

            for ($i = 1; $i <= $orgCount; $i++) {
                $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                $label = $orgLabel . ' ' . $num;
                $fileName = to_slug($orgLabel) . '-' . $num;
                $content = str_replace('{{ORG_LABEL}}', $label, $orgTpl);
                file_put_contents($projectPath . '/pages/' . $fileName . '.html', $content);
            }
        }

        // Guardar en BD
        $id = Project::create([
            'slug'            => $slug,
            'name'            => $name,
            'user_id'         => AuthMiddleware::userId(),
            'color_primary'   => $colorPrimary,
            'color_secondary' => $colorSecondary,
            'nav_bg_color'    => $navBgColor,
            'nav_text_color'  => $navTextColor,
            'org_type'        => $orgType,
            'org_count'       => $orgCount,
            'cdns'            => $cdns,
        ]);

        $project = Project::find($id);

        Response::json([
            'success' => true,
            'message' => __('admin.project_created', ['name' => $name]),
            'project' => $project,
        ]);
    }

    /**
     * POST /api/projects/edit
     */
    public function edit(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'api');

        $projectId = (int) $request->input('project_id', 0);
        if ($projectId <= 0) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $project = Project::find($projectId);
        if (!$project) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        $fields = [];
        $name = $request->input('name');
        if ($name !== null) {
            $name = trim($name);
            if ($name === '' || mb_strlen($name) > 100) {
                Response::json(['error' => __('general.field_too_long', ['max' => 100])], 400);
            }
            $fields['name'] = $name;
        }

        $colors = $request->input('colors');
        if ($colors !== null) {
            if (!empty($colors['primary']) && is_hex_color($colors['primary'])) {
                $fields['color_primary'] = $colors['primary'];
            }
            if (!empty($colors['secondary']) && is_hex_color($colors['secondary'])) {
                $fields['color_secondary'] = $colors['secondary'];
            }
        }

        // Colores del nav Canvas
        $navColors = $request->input('navColors');
        if ($navColors !== null) {
            if (!empty($navColors['bg']) && is_hex_color($navColors['bg'])) {
                $fields['nav_bg_color'] = $navColors['bg'];
            }
            if (!empty($navColors['text']) && is_hex_color($navColors['text'])) {
                $fields['nav_text_color'] = $navColors['text'];
            }
        }

        // Agregar páginas organizativas si se solicita
        $orgType = $request->input('orgType');
        $orgCount = (int) ($request->input('orgCount', 0));

        if ($orgType !== null && $orgType !== 'none' && $orgCount > 0) {
            $orgLabels = ['semanas' => 'Semana', 'modulos' => 'Módulo', 'unidades' => 'Unidad'];
            $orgLabel = $orgLabels[$orgType] ?? '';

            if ($orgLabel) {
                $projectPath = STORAGE_PATH . '/projects/' . $project['slug'];
                $pagesDir = $projectPath . '/pages';
                $orgTpl = file_get_contents(BASE_PATH . '/templates/pages/organization.html');

                for ($i = 1; $i <= $orgCount; $i++) {
                    $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                    $fileName = to_slug($orgLabel) . '-' . $num . '.html';
                    if (!file_exists($pagesDir . '/' . $fileName)) {
                        $content = str_replace('{{ORG_LABEL}}', $orgLabel . ' ' . $num, $orgTpl);
                        file_put_contents($pagesDir . '/' . $fileName, $content);
                    }
                }

                $fields['org_type'] = $orgType;
                $fields['org_count'] = $orgCount;
            }
        }

        // CDNs
        $newCdns = $request->input('cdns');
        if (is_array($newCdns)) {
            $fields['cdns'] = $newCdns;
        }

        // Actualizar master CSS (colores + imports)
        $slug = $project['slug'];
        $masterFile = STORAGE_PATH . '/projects/' . $slug . '/css/' . $slug . '-master.css';
        if (file_exists($masterFile)) {
            $css = file_get_contents($masterFile);

            // Colores
            if (isset($fields['color_primary'])) {
                $css = preg_replace('/--ct-primary-base:\s*#[0-9A-Fa-f]{6}/', '--ct-primary-base: ' . $fields['color_primary'], $css);
            }
            if (isset($fields['color_secondary'])) {
                $css = preg_replace('/--ct-secondary-base:\s*#[0-9A-Fa-f]{6}/', '--ct-secondary-base: ' . $fields['color_secondary'], $css);
            }

            // Imports de CDNs
            if (is_array($newCdns)) {
                // Eliminar TODOS los @import existentes
                $css = preg_replace('/^@import\s+url\(.+?\);\s*$/m', '', $css);
                // Limpiar líneas vacías extras después del @charset
                $css = preg_replace('/(@charset\s+"[^"]+";)\s*\n+/', '$1' . "\n", $css);

                // Regenerar imports
                $cdnUrls = App::config('cdns', []);
                $imports = '';
                foreach ($newCdns as $cdn) {
                    if (isset($cdnUrls[$cdn])) {
                        $imports .= '@import url("' . $cdnUrls[$cdn] . '");' . "\n";
                    }
                }

                // Insertar después de @charset
                if ($imports) {
                    $css = preg_replace('/(@charset\s+"[^"]+";)\n/', '$1' . "\n" . $imports, $css);
                }
            }

            file_put_contents($masterFile, $css);
        }

        if (!empty($fields)) {
            Project::update($projectId, $fields);
        }

        Response::json([
            'success' => true,
            'message' => __('admin.project_updated'),
        ]);
    }

    /**
     * POST /api/projects/delete
     */
    public function delete(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'api');

        $projectId = (int) $request->input('project_id', 0);
        $project = Project::find($projectId);
        if (!$project) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        if ($project['is_protected']) {
            Response::json(['error' => __('admin.project_protected')], 400);
        }

        // Eliminar directorio
        $projectPath = STORAGE_PATH . '/projects/' . $project['slug'];
        if (is_dir($projectPath)) {
            self::deleteDirectory($projectPath);
        }

        Project::delete($projectId);

        Response::json([
            'success' => true,
            'message' => __('admin.project_deleted'),
        ]);
    }

    /**
     * POST /api/projects/toggle
     */
    public function toggle(Request $request, array $params = []): void
    {
        $projectId = (int) $request->input('project_id', 0);
        $project = Project::find($projectId);
        if (!$project) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        $isActive = Project::toggleActive($projectId);

        Response::json([
            'success'   => true,
            'message'   => $isActive ? __('admin.project_activated') : __('admin.project_deactivated'),
            'is_active' => $isActive,
        ]);
    }

    /**
     * POST /api/projects/compile
     */
    public function compile(Request $request, array $params = []): void
    {
        $slug = $request->input('project', '');
        if ($slug === '') {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $project = Project::findBySlug($slug);
        if (!$project) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        $projectPath = STORAGE_PATH . '/projects/' . $slug;
        $success = CssCompiler::compile($projectPath, $slug);

        if ($success) {
            Response::json(['success' => true, 'message' => __('admin.compile_success')]);
        } else {
            Response::json(['error' => __('admin.compile_error')], 500);
        }
    }

    /**
     * GET /api/content
     */
    public function content(Request $request, array $params = []): void
    {
        $projectSlug = $request->query('project', '');
        $page = $request->query('page', 'index');

        if (!preg_match('/^[a-z0-9\-_]+$/i', $projectSlug) || !preg_match('/^[a-z0-9\-_]+$/i', $page)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $projectPath = STORAGE_PATH . '/projects/' . $projectSlug;
        if (!is_dir($projectPath)) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        // Páginas dinámicas (herramientas del proyecto)
        $project = Project::findBySlug($projectSlug);
        if ($page === 'colors' || $page === 'accessibility') {
            $html = '';
            $toolPage = true;
            $projectData = $project;
        } else {
            $toolPage = false;
            $projectData = null;

            // Resolver ruta del archivo
            if ($page === 'index') {
                $filePath = $projectPath . '/index.html';
            } elseif ($page === 'snippets') {
                $filePath = $projectPath . '/snippets.html';
            } elseif ($page === 'palette') {
                $filePath = $projectPath . '/palette.html';
            } else {
                $filePath = $projectPath . '/pages/' . $page . '.html';
            }

            if (!file_exists($filePath)) {
                Response::json(['error' => __('general.not_found')], 404);
            }

            $html = file_get_contents($filePath);
        }
        $basePath = '/storage/projects/' . $projectSlug;
        $masterFile = $projectPath . '/css/' . $projectSlug . '-master.css';
        $mobileFile = $projectPath . '/css/' . $projectSlug . '-mobile.css';
        $desktopFile = $projectPath . '/css/' . $projectSlug . '-desktop.css';

        // En desarrollo carga master (tiene dark mode); si no, mobile + desktop
        if (file_exists($masterFile)) {
            $cssPath = $basePath . '/css/' . $projectSlug . '-master.css';
            $cssDesktopPath = null;
        } else {
            $cssPath = file_exists($mobileFile) ? $basePath . '/css/' . $projectSlug . '-mobile.css' : null;
            $cssDesktopPath = file_exists($desktopFile) ? $basePath . '/css/' . $projectSlug . '-desktop.css' : null;
        }

        $jsFile = $projectPath . '/js/' . $projectSlug . '-scripts.js';

        $result = [
            'html'           => $html,
            'project'        => $projectSlug,
            'page'           => $page,
            'cssPath'        => $cssPath,
            'cssDesktopPath' => $cssDesktopPath,
            'jsPath'         => file_exists($jsFile) ? $basePath . '/js/' . $projectSlug . '-scripts.js' : null,
            'toolPage'       => $toolPage,
        ];

        // Datos extra para páginas de herramientas
        if ($toolPage && $projectData) {
            $result['projectData'] = [
                'name'            => $projectData['name'],
                'color_primary'   => $projectData['color_primary'],
                'color_secondary' => $projectData['color_secondary'],
                'nav_bg_color'    => $projectData['nav_bg_color'],
                'nav_text_color'  => $projectData['nav_text_color'],
            ];
            // Para accesibilidad: lista de páginas con contenido HTML
            if ($page === 'accessibility') {
                $contentPages = [];
                $pagesDir = $projectPath . '/pages';
                if (file_exists($projectPath . '/index.html')) {
                    $contentPages[] = ['slug' => 'index', 'name' => 'Inicio'];
                }
                if (is_dir($pagesDir)) {
                    foreach (glob($pagesDir . '/*.html') ?: [] as $f) {
                        $fn = basename($f, '.html');
                        $contentPages[] = ['slug' => $fn, 'name' => ucwords(str_replace(['-', '_'], ' ', $fn))];
                    }
                }
                if (file_exists($projectPath . '/snippets.html')) {
                    $contentPages[] = ['slug' => 'snippets', 'name' => 'Snippets'];
                }
                $result['contentPages'] = $contentPages;
            }
        }

        Response::json($result);
    }

    /**
     * GET /api/source
     */
    public function source(Request $request, array $params = []): void
    {
        $projectSlug = $request->query('project', '');
        $page = $request->query('page', 'index');

        $projectPath = STORAGE_PATH . '/projects/' . $projectSlug;
        if (!is_dir($projectPath)) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        $htmlFile = $page === 'index'
            ? $projectPath . '/index.html'
            : ($page === 'snippets' || $page === 'palette'
                ? $projectPath . '/' . $page . '.html'
                : $projectPath . '/pages/' . $page . '.html');

        Response::json([
            'html'       => file_exists($htmlFile) ? file_get_contents($htmlFile) : '',
            'cssMaster'  => self::readFile($projectPath . '/css/' . $projectSlug . '-master.css'),
            'css'        => self::readFile($projectPath . '/css/' . $projectSlug . '-mobile.css'),
            'cssDesktop' => self::readFile($projectPath . '/css/' . $projectSlug . '-desktop.css'),
            'js'         => self::readFile($projectPath . '/js/' . $projectSlug . '-scripts.js'),
        ]);
    }

    private static function readFile(string $path): string
    {
        return file_exists($path) ? file_get_contents($path) : '';
    }

    private static function deleteDirectory(string $dir): void
    {
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? self::deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
