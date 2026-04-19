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
        file_put_contents($projectPath . '/snippets.html', $tpl('snippets.html'));

        // Actividades (solo las seleccionadas)
        $activities = $request->input('activities', []);
        $activityTemplates = ['tarea' => 'pages/tarea.html', 'foros' => 'pages/foros.html', 'quiz' => 'pages/quiz.html'];
        if (is_array($activities)) {
            foreach ($activities as $act) {
                if (isset($activityTemplates[$act])) {
                    file_put_contents($projectPath . '/pages/' . $act . '.html', $tpl($activityTemplates[$act]));
                }
            }
        }

        // Páginas adicionales — acepta formato legacy (strings) o nuevo (objetos {name})
        $customPages = $request->input('customPages', []);
        if (is_array($customPages)) {
            foreach ($customPages as $item) {
                $pageName = is_array($item) ? trim((string)($item['name'] ?? '')) : trim((string)$item);
                if ($pageName === '') continue;
                self::createCustomPageFile($projectPath . '/pages', $pageName);
            }
        }

        // CSS master — prefijo personalizado por proyecto
        $cssPrefix = css_prefix($slug);
        $masterContent = $tpl('css/master.css', ['IMPORTS' => $imports]);

        // Reemplazar prefijo --ct- por --{prefijo}-
        $masterContent = str_replace('--ct-', '--' . $cssPrefix . '-', $masterContent);

        // Reemplazar colores base
        $masterContent = preg_replace(
            '/--' . preg_quote($cssPrefix, '/') . '-primary-base:\s*#[0-9A-Fa-f]{6}/',
            '--' . $cssPrefix . '-primary-base: ' . $colorPrimary,
            $masterContent
        );
        $masterContent = preg_replace(
            '/--' . preg_quote($cssPrefix, '/') . '-secondary-base:\s*#[0-9A-Fa-f]{6}/',
            '--' . $cssPrefix . '-secondary-base: ' . $colorSecondary,
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
     * Actualización unificada: metadata + diff de páginas (organización, custom, actividades).
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

        $slug = $project['slug'];
        $projectPath = STORAGE_PATH . '/projects/' . $slug;
        $pagesDir = $projectPath . '/pages';

        $fields = [];

        // ── Metadata ──
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

        $navColors = $request->input('navColors');
        if ($navColors !== null) {
            if (!empty($navColors['bg']) && is_hex_color($navColors['bg'])) {
                $fields['nav_bg_color'] = $navColors['bg'];
            }
            if (!empty($navColors['text']) && is_hex_color($navColors['text'])) {
                $fields['nav_text_color'] = $navColors['text'];
            }
        }

        $newCdns = $request->input('cdns');
        if (is_array($newCdns)) {
            $fields['cdns'] = $newCdns;
        }

        // Solo admin puede cambiar el flag de protección
        $isProtected = $request->input('is_protected');
        if ($isProtected !== null && AuthMiddleware::isAdmin()) {
            $fields['is_protected'] = $isProtected ? 1 : 0;
        }

        // ── Diff de páginas de organización ──
        // Cliente envía la lista de las que deben permanecer (oldSlug).
        // Las que no estén en la lista se eliminan. Luego orgType+orgCount crea las faltantes.
        $orgType = $request->input('orgType', 'none');
        $orgCount = (int) $request->input('orgCount', 0);
        $organization = $request->input('organization', null);

        if (is_array($organization) && is_dir($pagesDir)) {
            $keepOrg = [];
            $orgPrefixLabel = ['semana' => 'Semana', 'modulo' => 'Módulo', 'unidad' => 'Unidad'];
            $orgTpl = null;

            foreach ($organization as $item) {
                $os = is_array($item) ? ($item['oldSlug'] ?? null) : null;
                if (!$os || !preg_match('/^(semana|modulo|unidad)-(\d+)$/', $os, $m)) continue;
                $keepOrg[$os] = true;

                // Crear archivo si la lista lo incluye pero no existe (páginas agregadas con "+")
                $path = $pagesDir . '/' . $os . '.html';
                if (!file_exists($path)) {
                    if ($orgTpl === null) {
                        $orgTpl = file_get_contents(BASE_PATH . '/templates/pages/organization.html');
                    }
                    $label = ($orgPrefixLabel[$m[1]] ?? ucfirst($m[1])) . ' ' . $m[2];
                    file_put_contents($path, str_replace('{{ORG_LABEL}}', $label, $orgTpl));
                }
            }
            foreach (glob($pagesDir . '/*.html') ?: [] as $f) {
                $fn = basename($f, '.html');
                if (preg_match('/^(semana|modulo|unidad)-\d+$/', $fn) && !isset($keepOrg[$fn])) {
                    self::safeUnlink($f, $pagesDir);
                }
            }
        }

        if ($orgType !== 'none' && $orgCount > 0) {
            $orgLabels = ['semanas' => 'Semana', 'modulos' => 'Módulo', 'unidades' => 'Unidad'];
            $orgLabel = $orgLabels[$orgType] ?? '';
            if ($orgLabel && is_dir($pagesDir)) {
                $prefix = to_slug($orgLabel);
                // Calcular el número máximo existente del tipo actual (después del diff de delete)
                $existingMax = 0;
                foreach (glob($pagesDir . '/' . $prefix . '-*.html') ?: [] as $f) {
                    $fn = basename($f, '.html');
                    if (preg_match('/^' . preg_quote($prefix, '/') . '-(\d+)$/', $fn, $m)) {
                        $existingMax = max($existingMax, (int) $m[1]);
                    }
                }
                // Solo crear páginas nuevas más allá del máximo actual (no revive las eliminadas).
                if ($orgCount > $existingMax) {
                    $orgTpl = file_get_contents(BASE_PATH . '/templates/pages/organization.html');
                    for ($i = $existingMax + 1; $i <= $orgCount; $i++) {
                        $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                        $fileName = $prefix . '-' . $num . '.html';
                        if (!file_exists($pagesDir . '/' . $fileName)) {
                            $content = str_replace('{{ORG_LABEL}}', $orgLabel . ' ' . $num, $orgTpl);
                            file_put_contents($pagesDir . '/' . $fileName, $content);
                        }
                    }
                }
                $fields['org_type'] = $orgType;
                $fields['org_count'] = $orgCount;
            }
        } elseif ($orgType === 'none') {
            $fields['org_type'] = 'none';
            $fields['org_count'] = 0;
        }

        // ── Diff de páginas adicionales (rename + add + delete) ──
        $customPages = $request->input('customPages', null);
        if (is_array($customPages) && is_dir($pagesDir)) {
            $activitySlugs = ['tarea', 'foros', 'quiz'];
            $keepCustom = [];  // slugs finales que deben quedar
            $reserved = [];    // evitar colisiones intra-request

            foreach ($customPages as $item) {
                if (!is_array($item)) continue;
                $pageName = trim((string)($item['name'] ?? ''));
                $oldSlug = isset($item['oldSlug']) && $item['oldSlug'] !== '' ? (string)$item['oldSlug'] : null;
                if ($pageName === '') continue;

                $newSlug = to_slug($pageName);
                if ($newSlug === '' || !is_valid_slug($newSlug)) continue;

                // Bloquear colisiones con organización y actividades
                if (preg_match('/^(semana|modulo|unidad)-\d+$/', $newSlug) || in_array($newSlug, $activitySlugs, true)) {
                    continue;
                }

                // Evitar duplicados intra-request
                if (isset($reserved[$newSlug])) continue;
                $reserved[$newSlug] = true;

                if ($oldSlug === null) {
                    // Nueva página
                    $path = $pagesDir . '/' . $newSlug . '.html';
                    if (!file_exists($path)) {
                        self::createCustomPageFile($pagesDir, $pageName);
                    }
                    $keepCustom[$newSlug] = true;
                } else {
                    // Posible rename (o sin cambios)
                    $oldPath = $pagesDir . '/' . $oldSlug . '.html';
                    $newPath = $pagesDir . '/' . $newSlug . '.html';
                    if ($oldSlug !== $newSlug && file_exists($oldPath) && !file_exists($newPath)) {
                        rename($oldPath, $newPath);
                    }
                    $keepCustom[$newSlug] = true;
                }
            }

            // Eliminar archivos custom que no están en la lista final
            foreach (glob($pagesDir . '/*.html') ?: [] as $f) {
                $fn = basename($f, '.html');
                if (preg_match('/^(semana|modulo|unidad)-\d+$/', $fn)) continue;
                if (in_array($fn, $activitySlugs, true)) continue;
                if (!isset($keepCustom[$fn])) {
                    self::safeUnlink($f, $pagesDir);
                }
            }
        }

        // ── Diff de actividades (add/remove por checkbox) ──
        $activities = $request->input('activities', null);
        if (is_array($activities) && is_dir($pagesDir)) {
            $activityTemplates = ['tarea' => 'pages/tarea.html', 'foros' => 'pages/foros.html', 'quiz' => 'pages/quiz.html'];
            $templatesDir = BASE_PATH . '/templates';
            $desired = array_values(array_intersect($activities, array_keys($activityTemplates)));

            foreach ($activityTemplates as $actSlug => $tplPath) {
                $filePath = $pagesDir . '/' . $actSlug . '.html';
                $shouldExist = in_array($actSlug, $desired, true);
                if ($shouldExist && !file_exists($filePath)) {
                    $tplFull = $templatesDir . '/' . $tplPath;
                    if (file_exists($tplFull)) {
                        file_put_contents($filePath, file_get_contents($tplFull));
                    }
                } elseif (!$shouldExist && file_exists($filePath)) {
                    self::safeUnlink($filePath, $pagesDir);
                }
            }
        }

        // ── Actualizar master CSS (colores + imports) ──
        $masterFile = $projectPath . '/css/' . $slug . '-master.css';
        if (file_exists($masterFile)) {
            $css = file_get_contents($masterFile);

            if (isset($fields['color_primary'])) {
                $css = preg_replace('/--[a-z0-9]+-primary-base:\s*#[0-9A-Fa-f]{6}/', '--' . css_prefix($slug) . '-primary-base: ' . $fields['color_primary'], $css);
            }
            if (isset($fields['color_secondary'])) {
                $css = preg_replace('/--[a-z0-9]+-secondary-base:\s*#[0-9A-Fa-f]{6}/', '--' . css_prefix($slug) . '-secondary-base: ' . $fields['color_secondary'], $css);
            }

            if (is_array($newCdns)) {
                $css = preg_replace('/^@import\s+url\(.+?\);\s*$/m', '', $css);
                $css = preg_replace('/(@charset\s+"[^"]+";)\s*\n+/', '$1' . "\n", $css);

                $cdnUrls = App::config('cdns', []);
                $imports = '';
                foreach ($newCdns as $cdn) {
                    if (isset($cdnUrls[$cdn])) {
                        $imports .= '@import url("' . $cdnUrls[$cdn] . '");' . "\n";
                    }
                }

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
     * Genera un archivo HTML vacío para una página adicional.
     */
    private static function createCustomPageFile(string $pagesDir, string $pageName): void
    {
        $pageSlug = to_slug($pageName);
        if ($pageSlug === '' || !is_valid_slug($pageSlug)) return;
        $filePath = $pagesDir . '/' . $pageSlug . '.html';
        if (file_exists($filePath)) return;

        $esc = htmlspecialchars($pageName, ENT_QUOTES, 'UTF-8');
        $html = '<div class="custom-page">' . "\n" .
            '  <h2>' . $esc . '</h2>' . "\n" .
            '  <div class="page-content">' . "\n" .
            '    <p>Contenido de ' . $esc . '.</p>' . "\n" .
            '  </div>' . "\n" .
            '</div>' . "\n";
        file_put_contents($filePath, $html);
    }

    /**
     * Elimina un archivo solo si está contenido dentro del directorio esperado (anti path-traversal).
     */
    private static function safeUnlink(string $filePath, string $allowedDir): void
    {
        $real = realpath($filePath);
        $base = realpath($allowedDir);
        if ($real && $base && strpos($real, $base) === 0 && is_file($real)) {
            unlink($real);
        }
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
        RateLimitMiddleware::check($request, 'api');
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
        RateLimitMiddleware::check($request, 'api');
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

        // Protección contra path traversal
        $realPath = realpath($projectPath);
        $allowedBase = realpath(STORAGE_PATH . '/projects');
        if (!$realPath || !$allowedBase || strpos($realPath, $allowedBase) !== 0 || !is_dir($projectPath)) {
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

        // Validar inputs contra path traversal
        if (!preg_match('/^[a-z0-9\-_]+$/i', $projectSlug) || !preg_match('/^[a-z0-9\-_]+$/i', $page)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $projectPath = STORAGE_PATH . '/projects/' . $projectSlug;

        // Verificar que la ruta resuelta está dentro de storage/projects
        $realPath = realpath($projectPath);
        $allowedBase = realpath(STORAGE_PATH . '/projects');
        if (!$realPath || !$allowedBase || strpos($realPath, $allowedBase) !== 0) {
            Response::json(['error' => __('general.not_found')], 404);
        }

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

    /**
     * POST /api/projects/pages/add
     * Agregar páginas adicionales a un proyecto.
     */
    public function addPages(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'api');

        $slug = $request->input('slug', '');
        $pages = $request->input('pages', []);

        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $project = Project::findBySlug($slug);
        if (!$project) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        $projectPath = STORAGE_PATH . '/projects/' . $slug;
        $pagesDir = $projectPath . '/pages';
        $created = [];

        foreach ($pages as $pageName) {
            $pageName = trim($pageName);
            if ($pageName === '') {
                continue;
            }
            $pageSlug = to_slug($pageName);
            if ($pageSlug === '' || !is_valid_slug($pageSlug)) {
                continue;
            }
            $filePath = $pagesDir . '/' . $pageSlug . '.html';
            if (!file_exists($filePath)) {
                $html = '<div class="custom-page">' . "\n" .
                    '  <h2>' . htmlspecialchars($pageName, ENT_QUOTES, 'UTF-8') . '</h2>' . "\n" .
                    '  <div class="page-content">' . "\n" .
                    '    <p>Contenido de ' . htmlspecialchars($pageName, ENT_QUOTES, 'UTF-8') . '.</p>' . "\n" .
                    '  </div>' . "\n" .
                    '</div>' . "\n";
                file_put_contents($filePath, $html);
                $created[] = $pageSlug;
            }
        }

        Response::json([
            'success' => true,
            'message' => count($created) . ' página(s) creada(s)',
            'created' => $created,
        ]);
    }

    /**
     * POST /api/projects/pages/delete
     * Eliminar una página de un proyecto.
     */
    public function deletePage(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'api');

        $slug = $request->input('slug', '');
        $pageSlug = $request->input('page', '');

        if (!preg_match('/^[a-z0-9\-]+$/', $slug) || !preg_match('/^[a-z0-9\-]+$/', $pageSlug)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $project = Project::findBySlug($slug);
        if (!$project) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        // No permitir eliminar index ni snippets
        if (in_array($pageSlug, ['index', 'snippets'], true)) {
            Response::json(['error' => 'No se puede eliminar esta página'], 400);
        }

        $projectPath = STORAGE_PATH . '/projects/' . $slug;
        $filePath = $projectPath . '/pages/' . $pageSlug . '.html';

        // Validar path traversal
        $realFile = realpath($filePath);
        $allowedBase = realpath($projectPath . '/pages');
        if (!$realFile || !$allowedBase || strpos($realFile, $allowedBase) !== 0) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        Response::json([
            'success' => true,
            'message' => 'Página eliminada',
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
