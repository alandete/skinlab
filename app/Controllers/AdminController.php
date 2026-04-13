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

    public function projectNew(Request $request, array $params = []): void
    {
        View::render('admin.project-edit', [
            'title'      => __('admin.create_project') . ' — ' . __('admin.title'),
            'project'    => null,
            'isNew'      => true,
            'activeTab'  => 'projects',
            'breadcrumb' => __('admin.create_project'),
            'cdns'       => App::config('cdns', []),
        ], 'layouts.admin');
    }

    public function projectEdit(Request $request, array $params = []): void
    {
        $slug = $params['slug'] ?? '';
        $project = Project::findBySlug($slug);

        if (!$project) {
            Response::notFound();
        }

        $projectPath = STORAGE_PATH . '/projects/' . $slug;
        $pages = Project::getProjectPages($projectPath);

        View::render('admin.project-edit', [
            'title'      => $project['name'] . ' — ' . __('admin.title'),
            'project'    => $project,
            'pages'      => $pages,
            'isNew'      => false,
            'activeTab'  => 'projects',
            'breadcrumb' => $project['name'],
            'cdns'       => App::config('cdns', []),
        ], 'layouts.admin');
    }

    public function docs(Request $request, array $params = []): void
    {
        View::render('admin.docs', [
            'title'      => __('admin.tab_docs') . ' — ' . __('admin.title'),
            'activeTab'  => 'docs',
            'breadcrumb' => __('admin.tab_docs'),
        ], 'layouts.admin');
    }
}
