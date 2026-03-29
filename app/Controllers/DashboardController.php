<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

class DashboardController
{
    /**
     * Dashboard principal con layout Canvas LMS.
     */
    public function index(Request $request, array $params = []): void
    {
        View::render('dashboard.index', [
            'title' => __('dashboard.title') . ' — ' . __('general.app_name'),
        ], 'layouts.dashboard');
    }

    /**
     * Dashboard con proyecto preseleccionado (URL limpia).
     */
    public function project(Request $request, array $params = []): void
    {
        View::render('dashboard.index', [
            'title' => __('dashboard.title') . ' — ' . __('general.app_name'),
        ], 'layouts.dashboard');
    }
}
