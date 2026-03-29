<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class UserApiController
{
    /**
     * POST /api/users/create
     * Crear usuario (editor o guest). Solo admin.
     */
    public function create(Request $request, array $params = []): void
    {
        $username = strtolower(trim($request->input('username', '')));
        $password = $request->input('password', '');
        $role = $request->input('role', 'guest');

        // Validar rol
        if (!in_array($role, ['editor', 'guest'], true)) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        // Validar username
        if ($username === '' || mb_strlen($username) < 3 || mb_strlen($username) > 30) {
            Response::json(['error' => __('auth.username_length')], 400);
        }

        if (!preg_match('/^[a-z0-9_]+$/', $username)) {
            Response::json(['error' => __('auth.username_format')], 400);
        }

        if (User::usernameExists($username)) {
            Response::json(['error' => __('admin.username_taken')], 409);
        }

        // Validar password
        if (mb_strlen($password) < 6) {
            Response::json(['error' => __('auth.password_min')], 400);
        }

        $id = User::create($username, $password, $role);

        Response::json([
            'success' => true,
            'message' => __('admin.user_created'),
            'user_id' => $id,
        ]);
    }

    /**
     * POST /api/users/password
     * Cambiar contraseña de un usuario. Solo admin.
     */
    public function changePassword(Request $request, array $params = []): void
    {
        $userId = (int) $request->input('user_id', 0);
        $password = $request->input('password', '');

        if ($userId <= 0) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        if (mb_strlen($password) < 6) {
            Response::json(['error' => __('auth.password_min')], 400);
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
     * Activar/desactivar un usuario. Solo admin.
     * No permite desactivar al propio admin.
     */
    public function toggle(Request $request, array $params = []): void
    {
        $userId = (int) $request->input('user_id', 0);

        if ($userId <= 0) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        // No permitir desactivarse a sí mismo
        if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
            Response::json(['error' => __('admin.cannot_deactivate_self')], 400);
        }

        $isActive = User::toggleActive($userId);

        Response::json([
            'success'  => true,
            'message'  => $isActive ? __('admin.user_activated') : __('admin.user_deactivated'),
            'is_active' => $isActive,
        ]);
    }

    /**
     * POST /api/users/delete
     * Eliminar un usuario. Solo admin.
     * No permite eliminar al propio admin ni al último admin.
     */
    public function delete(Request $request, array $params = []): void
    {
        $userId = (int) $request->input('user_id', 0);

        if ($userId <= 0) {
            Response::json(['error' => __('general.invalid_format')], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            Response::json(['error' => __('general.not_found')], 404);
        }

        // No permitir eliminarse a sí mismo
        if ($userId === (int) ($_SESSION['user_id'] ?? 0)) {
            Response::json(['error' => __('admin.cannot_delete_self')], 400);
        }

        // No permitir eliminar el último admin
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
     * Actualizar correo de recuperación. Solo admin.
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
