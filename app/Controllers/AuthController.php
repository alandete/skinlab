<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Middleware\RateLimitMiddleware;

class AuthController
{
    // ── Login ──

    public function showLogin(Request $request, array $params = []): void
    {
        if (!App::isInstalled()) {
            Response::redirect('/setup');
        }
        if (auth_check()) {
            Response::redirect('/dashboard');
        }

        View::render('auth.login', [], 'layouts.auth');
    }

    public function login(Request $request, array $params = []): void
    {
        if (!App::isInstalled()) {
            Response::redirect('/setup');
        }

        RateLimitMiddleware::check($request, 'login');

        $username = strtolower(trim($request->input('username', '')));
        $password = $request->input('password', '');

        if ($username === '' || $password === '') {
            set_flash('error', __('auth.invalid_credentials'));
            Response::redirect('/login');
        }

        $user = User::findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            set_flash('error', __('auth.invalid_credentials'));
            Response::redirect('/login');
        }

        if (!$user['is_active']) {
            set_flash('error', __('auth.account_disabled'));
            Response::redirect('/login');
        }

        AuthMiddleware::login($user);
        Response::redirect('/dashboard');
    }

    // ── Logout ──

    public function logout(Request $request, array $params = []): void
    {
        AuthMiddleware::logout();
        Response::redirect('/login');
    }

    // ── Setup ──

    public function showSetup(Request $request, array $params = []): void
    {
        if (App::isInstalled()) {
            Response::redirect('/login');
        }

        View::render('auth.setup', [], 'layouts.auth');
    }

    public function setup(Request $request, array $params = []): void
    {
        if (App::isInstalled()) {
            Response::redirect('/login');
        }

        // Inicializar schema si no existe
        if (!Database::isSchemaReady()) {
            Database::initSchema();
        }

        $adminUser = strtolower(trim($request->input('admin_user', '')));
        $adminPass = $request->input('admin_password', '');
        $adminConfirm = $request->input('admin_confirm', '');
        $createGuest = $request->input('create_guest') !== null;
        $email = strtolower(trim($request->input('recovery_email', '')));

        // Validaciones
        $error = $this->validateSetup($adminUser, $adminPass, $adminConfirm, $email);

        if ($error !== null) {
            set_flash('error', $error);
            set_flash('old_admin_user', $adminUser);
            set_flash('old_recovery_email', $email);
            Response::redirect('/setup');
        }

        // Crear admin
        Database::transaction(function () use ($adminUser, $adminPass, $createGuest, $email) {
            User::create($adminUser, $adminPass, 'admin', $email ?: null);

            if ($createGuest) {
                $guestPass = bin2hex(random_bytes(4)); // 8 chars aleatorios
                User::create('invitado', $guestPass, 'guest');
                User::setSetting('guest_initial_password', $guestPass);
            }

            User::setSetting('recovery_email', $email);
            User::setSetting('app_installed', '1');
        });

        $guestInfo = '';
        if ($createGuest) {
            $savedGuestPass = User::getSetting('guest_initial_password');
            $guestInfo = ' | Invitado: invitado / ' . $savedGuestPass;
        }
        set_flash('success', __('auth.setup_complete', ['name' => $adminUser]) . $guestInfo);
        Response::redirect('/login');
    }

    private function validateSetup(string $user, string $pass, string $confirm, string $email): ?string
    {
        if ($user === '' || $pass === '') {
            return __('auth.username_required');
        }
        if (mb_strlen($user) < 3 || mb_strlen($user) > 30) {
            return __('auth.username_length');
        }
        if (!preg_match('/^[a-z0-9_]+$/', $user)) {
            return __('auth.username_format');
        }
        if (mb_strlen($pass) < 6) {
            return __('auth.password_min');
        }
        if ($pass !== $confirm) {
            return __('auth.password_mismatch');
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return __('auth.email_invalid');
        }
        return null;
    }

    // ── Reset Password ──

    public function showResetPassword(Request $request, array $params = []): void
    {
        if (!App::isInstalled()) {
            Response::redirect('/setup');
        }

        View::render('auth.reset-password', [], 'layouts.auth');
    }

    public function resetPassword(Request $request, array $params = []): void
    {
        if (!App::isInstalled()) {
            Response::redirect('/setup');
        }

        RateLimitMiddleware::check($request, 'reset_password');

        $email = strtolower(trim($request->input('email', '')));
        $username = strtolower(trim($request->input('username', '')));
        $newPass = $request->input('new_password', '');
        $confirm = $request->input('confirm_password', '');

        $storedEmail = User::getSetting('recovery_email');

        if (!$storedEmail || $storedEmail === '') {
            set_flash('error', __('auth.reset_no_email'));
            Response::redirect('/reset-password');
        }

        if ($email !== $storedEmail) {
            set_flash('error', __('auth.reset_email_mismatch'));
            Response::redirect('/reset-password');
        }

        if ($username === '') {
            set_flash('error', __('auth.username_required'));
            Response::redirect('/reset-password');
        }

        if (mb_strlen($newPass) < 6) {
            set_flash('error', __('auth.password_min'));
            Response::redirect('/reset-password');
        }

        if ($newPass !== $confirm) {
            set_flash('error', __('auth.password_mismatch'));
            Response::redirect('/reset-password');
        }

        $user = User::findByUsername($username);
        if (!$user) {
            set_flash('error', __('auth.invalid_credentials'));
            Response::redirect('/reset-password');
        }

        User::updatePassword($user['id'], $newPass);
        set_flash('success', __('auth.reset_success'));
        Response::redirect('/login');
    }
}
