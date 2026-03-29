<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    /**
     * Obtener conexión PDO (singleton).
     */
    public static function connection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        self::$config = require BASE_PATH . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            self::$config['host'],
            self::$config['port'],
            self::$config['database'],
            self::$config['charset']
        );

        try {
            self::$pdo = new PDO(
                $dsn,
                self::$config['username'],
                self::$config['password'],
                self::$config['options']
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Error de conexión a base de datos: ' . $e->getMessage());
        }

        return self::$pdo;
    }

    /**
     * Verificar si las tablas del schema existen.
     */
    public static function isSchemaReady(): bool
    {
        try {
            // Asegurar conexión para cargar config
            self::connection();
            $result = self::fetch(
                "SELECT COUNT(*) as total FROM information_schema.tables
                 WHERE table_schema = :db AND table_name = 'users'",
                [':db' => self::$config['database']]
            );
            return ($result['total'] ?? 0) > 0;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Ejecutar el schema SQL para crear las tablas.
     */
    public static function initSchema(): void
    {
        $schemaFile = BASE_PATH . '/database/schema.sql';
        if (!file_exists($schemaFile)) {
            throw new \RuntimeException('Archivo de schema no encontrado: ' . $schemaFile);
        }

        $sql = file_get_contents($schemaFile);

        // Ejecutar cada statement por separado
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn(string $s) => $s !== ''
        );

        foreach ($statements as $statement) {
            self::connection()->exec($statement);
        }
    }

    /**
     * Ejecutar query con parámetros y devolver el statement.
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Obtener una fila.
     */
    public static function fetch(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * Obtener todas las filas.
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Insertar y devolver el ID generado.
     */
    public static function insert(string $sql, array $params = []): int
    {
        self::query($sql, $params);
        return (int) self::connection()->lastInsertId();
    }

    /**
     * Ejecutar un statement (UPDATE, DELETE).
     */
    public static function execute(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    /**
     * Ejecutar dentro de una transacción.
     */
    public static function transaction(callable $callback): mixed
    {
        $pdo = self::connection();
        $pdo->beginTransaction();

        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
