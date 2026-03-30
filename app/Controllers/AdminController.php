<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\User;
use App\Models\Project;

class AdminController
{
    public function index(Request $request, array $params = []): void
    {
        Response::redirect('/admin/projects');
    }

    public function users(Request $request, array $params = []): void
    {
        $users = User::all();
        $recoveryEmail = User::getSetting('recovery_email') ?? '';

        View::render('admin.users', [
            'title'         => __('admin.users_title') . ' — ' . __('admin.title'),
            'users'         => $users,
            'recoveryEmail' => $recoveryEmail,
            'activeTab'     => 'users',
            'breadcrumb'    => __('admin.tab_users'),
        ], 'layouts.admin');
    }

    public function projects(Request $request, array $params = []): void
    {
        $projects = Project::allWithPages(false);

        View::render('admin.projects', [
            'title'      => __('admin.tab_projects') . ' — ' . __('admin.title'),
            'projects'   => $projects,
            'activeTab'  => 'projects',
            'breadcrumb' => __('admin.tab_projects'),
            'cdns'       => App::config('cdns', []),
        ], 'layouts.admin');
    }
}
