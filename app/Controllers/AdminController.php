<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\User;

class AdminController
{
    /**
     * Página principal admin: redirige a usuarios.
     */
    public function index(Request $request, array $params = []): void
    {
        Response::redirect('/admin/users');
    }

    /**
     * Gestión de usuarios.
     */
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
}
