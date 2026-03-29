<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Middleware\RateLimitMiddleware;

class UserApiController
{
    /**
     * POST /api/users/create
     */
    public function create(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'user_management');

        $username = strtolower(trim($request->input('username', '')));
        $password = $request->input('password', '');
        $role = $request->input('role', 'guest');
        $email = strtolower(trim($request->input('email', '')));

        if (!in_array($role, ['editor', 'guest'], true)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $usernameError = User::validateUsername($username);
        if ($usernameError) {
            Response::json(['error' => $usernameError], 400);
        }

        if (User::usernameExists($username)) {
            Response::json(['error' => __('admin.username_taken')], 409);
        }

        $passwordError = User::validatePassword($password);
        if ($passwordError) {
            Response::json(['error' => $passwordError], 400);
        }

        // Email obligatorio
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json(['error' => __('admin.email_required')], 400);
        }

        $id = User::create($username, $password, $role, $email);
        $user = User::find($id);

        Response::json([
            'success' => true,
            'message' => __('admin.user_created'),
            'user'    => $user,
        ]);
    }

    /**
     * POST /api/users/update
     */
    public function update(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'user_management');

        $userId = (int) $request->input('user_id', 0);
        if ($userId <= 0) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        $fields = [];

        // Username
        $username = $request->input('username');
        if ($username !== null) {
            $username = strtolower(trim($username));
            if ($username !== $user['username']) {
                $usernameError = User::validateUsername($username);
                if ($usernameError) {
                    Response::json(['error' => $usernameError], 400);
                }
                if (User::usernameExists($username, $userId)) {
                    Response::json(['error' => __('admin.username_taken')], 409);
                }
                $fields['username'] = $username;
            }
        }

        // Email
        $email = $request->input('email');
        if ($email !== null) {
            $email = strtolower(trim($email));
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Response::json(['error' => __('auth.email_invalid')], 400);
            }
            $fields['email'] = $email ?: null;
        }

        // Rol (no puede cambiar propio ni dejar 0 admins)
        $role = $request->input('role');
        if ($role !== null && $role !== $user['role']) {
            if (!in_array($role, ['admin', 'editor', 'guest'], true)) {
                Response::json(['error' => __('general.invalid_format')], 400);
            }
            if ($userId === AuthMiddleware::userId()) {
                Response::json(['error' => __('admin.cannot_change_own_role')], 400);
            }
            if ($user['role'] === 'admin' && $role !== 'admin' && User::countByRole('admin') <= 1) {
                Response::json(['error' => __('admin.cannot_demote_last_admin')], 400);
            }
            $fields['role'] = $role;
        }

        if (empty($fields)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        User::update($userId, $fields);
        $updated = User::find($userId);

        Response::json([
            'success' => true,
            'message' => __('admin.user_updated'),
            'user'    => $updated,
        ]);
    }

    /**
     * POST /api/users/password
     * Requiere re-autenticación (contraseña actual del admin).
     */
    public function changePassword(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'user_management');

        $userId = (int) $request->input('user_id', 0);
        $password = $request->input('password', '');
        $currentPassword = $request->input('current_password', '');

        if ($userId <= 0) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        // Re-autenticación
        if (!AuthMiddleware::verifyCurrentPassword($currentPassword)) {
            Response::json(['error' => __('admin.invalid_current_password')], 403);
        }

        $passwordError = User::validatePassword($password);
        if ($passwordError) {
            Response::json(['error' => $passwordError], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        User::updatePassword($userId, $password);

        Response::json([
            'success' => true,
            'message' => __('admin.password_changed'),
        ]);
    }

    /**
     * POST /api/users/toggle
     */
    public function toggle(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'user_management');

        $userId = (int) $request->input('user_id', 0);

        if ($userId <= 0) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        if ($userId === AuthMiddleware::userId()) {
            Response::json(['error' => __('admin.cannot_deactivate_self')], 400);
        }

        $isActive = User::toggleActive($userId);
        $updated = User::find($userId);

        Response::json([
            'success'   => true,
            'message'   => $isActive ? __('admin.user_activated') : __('admin.user_deactivated'),
            'is_active' => $isActive,
            'user'      => $updated,
        ]);
    }

    /**
     * POST /api/users/delete
     */
    public function delete(Request $request, array $params = []): void
    {
        RateLimitMiddleware::check($request, 'user_management');

        $userId = (int) $request->input('user_id', 0);

        if ($userId <= 0) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        if ($userId === AuthMiddleware::userId()) {
            Response::json(['error' => __('admin.cannot_delete_self')], 400);
        }

        if ($user['role'] === 'admin' && User::countByRole('admin') <= 1) {
            Response::json(['error' => __('admin.cannot_delete_last_admin')], 400);
        }

        User::delete($userId);

        Response::json([
            'success' => true,
            'message' => __('admin.user_deleted'),
        ]);
    }

    /**
     * POST /api/settings/email
     */
    public function updateRecoveryEmail(Request $request, array $params = []): void
    {
        $email = strtolower(trim($request->input('email', '')));

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json(['error' => __('auth.email_invalid')], 400);
        }

        User::setSetting('recovery_email', $email);

        Response::json([
            'success' => true,
            'message' => __('admin.recovery_saved'),
        ]);
    }
}
