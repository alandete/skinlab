<?php

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\AdminController;
use App\Controllers\Api\UserApiController;

/**
 * Definición de rutas de la aplicación.
 *
 * Middleware disponibles:
 *   'auth'              - Requiere usuario autenticado
 *   'role:admin'        - Requiere rol admin
 *   'role:editor'       - Requiere rol editor o superior
 *   'role:admin|editor' - Requiere rol admin O editor
 *
 * CSRF se valida automáticamente en todas las peticiones POST.
 */

return function (Router $router): void {

    // ── Públicas ──
    $router->get('/', [HomeController::class, 'index']);

    // ── Auth ──
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/setup', [AuthController::class, 'showSetup']);
    $router->post('/setup', [AuthController::class, 'setup']);
    $router->get('/logout', [AuthController::class, 'logout']);
    $router->get('/reset-password', [AuthController::class, 'showResetPassword']);
    $router->post('/reset-password', [AuthController::class, 'resetPassword']);

    // ── Dashboard ──
    $router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

    // ── Admin ──
    $router->get('/admin', [AdminController::class, 'index'], ['auth', 'role:admin']);
    $router->get('/admin/users', [AdminController::class, 'users'], ['auth', 'role:admin']);

    // ── API: Usuarios ──
    $router->post('/api/users/create', [UserApiController::class, 'create'], ['auth', 'role:admin']);
    $router->post('/api/users/password', [UserApiController::class, 'changePassword'], ['auth', 'role:admin']);
    $router->post('/api/users/toggle', [UserApiController::class, 'toggle'], ['auth', 'role:admin']);
    $router->post('/api/users/delete', [UserApiController::class, 'delete'], ['auth', 'role:admin']);
    $router->post('/api/settings/email', [UserApiController::class, 'updateRecoveryEmail'], ['auth', 'role:admin']);

    // ── Dashboard: URL limpia de proyecto ──
    $router->get('/project/{slug}', [DashboardController::class, 'project'], ['auth']);

    // ── Fase 4: Proyectos ──
    // $router->get('/admin/projects', [AdminController::class, 'projects'], ['auth', 'role:admin']);
    // $router->post('/api/projects',         [ApiProjectController::class, 'create'],  ['auth', 'role:editor']);
    // $router->post('/api/projects/edit',    [ApiProjectController::class, 'edit'],    ['auth', 'role:editor']);
    // $router->post('/api/projects/delete',  [ApiProjectController::class, 'delete'],  ['auth', 'role:admin']);
    // $router->post('/api/projects/toggle',  [ApiProjectController::class, 'toggle'],  ['auth', 'role:admin']);
    // $router->get('/api/projects',          [ApiProjectController::class, 'list'],    ['auth']);
    // $router->get('/api/content',           [ApiProjectController::class, 'content'], ['auth']);
    // $router->get('/api/source',            [ApiProjectController::class, 'source'],  ['auth']);
    // $router->post('/api/compile',          [ApiProjectController::class, 'compile'], ['auth', 'role:editor']);
    // $router->get('/api/export/{slug}',     [ApiProjectController::class, 'export'],  ['auth']);
};
