<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class HomeController
{
    /**
     * Página principal: redirige según estado de la app.
     */
    public function index(Request $request, array $params = []): void
    {
        if (!App::isInstalled()) {
            Response::redirect('/setup');
        }

        if (!auth_check()) {
            Response::redirect('/login');
        }

        Response::redirect('/dashboard');
    }
}
