<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class User
{
    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT id, username, email, role, is_active, created_at, updated_at
             FROM users WHERE id = :id",
            [':id' => $id]
        );
    }

    public static function findByUsername(string $username): ?array
    {
        return Database::fetch(
            "SELECT * FROM users WHERE username = :u",
            [':u' => $username]
        );
    }

    public static function all(): array
    {
        return Database::fetchAll(
            "SELECT id, username, email, role, is_active, created_at, updated_at
             FROM users ORDER BY FIELD(role, 'admin', 'editor', 'guest'), username"
        );
    }

    public static function create(string $username, string $password, string $role, ?string $email = null): int
    {
        return Database::insert(
            "INSERT INTO users (username, email, password, role)
             VALUES (:u, :e, :p, :r)",
            [
                ':u' => strtolower($username),
                ':e' => $email,
                ':p' => password_hash($password, PASSWORD_DEFAULT),
                ':r' => $role,
            ]
        );
    }

    public static function updatePassword(int $id, string $newPassword): void
    {
        Database::execute(
            "UPDATE users SET password = :p WHERE id = :id",
            [':p' => password_hash($newPassword, PASSWORD_DEFAULT), ':id' => $id]
        );
    }

    public static function toggleActive(int $id): bool
    {
        Database::execute(
            "UPDATE users SET is_active = 1 - is_active WHERE id = :id",
            [':id' => $id]
        );
        $user = self::find($id);
        return $user !== null && (bool) $user['is_active'];
    }

    public static function delete(int $id): void
    {
        Database::execute("DELETE FROM users WHERE id = :id", [':id' => $id]);
    }

    public static function count(): int
    {
        $r = Database::fetch("SELECT COUNT(*) as total FROM users");
        return (int) ($r['total'] ?? 0);
    }

    public static function countByRole(string $role): int
    {
        $r = Database::fetch(
            "SELECT COUNT(*) as total FROM users WHERE role = :r",
            [':r' => $role]
        );
        return (int) ($r['total'] ?? 0);
    }

    public static function usernameExists(string $username): bool
    {
        $r = Database::fetch(
            "SELECT 1 FROM users WHERE username = :u",
            [':u' => strtolower($username)]
        );
        return $r !== null;
    }

    // ── Settings helpers ──

    public static function getSetting(string $key): ?string
    {
        $r = Database::fetch(
            "SELECT setting_value FROM settings WHERE setting_key = :k",
            [':k' => $key]
        );
        return $r['setting_value'] ?? null;
    }

    public static function setSetting(string $key, string $value): void
    {
        Database::execute(
            "INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE setting_value = :v2",
            [':k' => $key, ':v' => $value, ':v2' => $value]
        );
    }
}
