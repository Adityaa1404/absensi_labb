<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private string $port;
    private string $charset;

    private function __construct()
    {
        $this->host     = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->db_name  = defined('DB_NAME') ? DB_NAME : 'absensi_lab';
        $this->username = defined('DB_USER') ? DB_USER : 'root';
        $this->password = defined('DB_PASS') ? DB_PASS : '';
        $this->port     = defined('DB_PORT') ? DB_PORT : '3306';
        $this->charset  = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
    }

    /**
     * Mendapatkan instance koneksi PDO tunggal (Singleton)
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $db = new self();
            $dsn = "mysql:host={$db->host};port={$db->port};dbname={$db->db_name};charset={$db->charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $db->username, $db->password, $options);
            } catch (PDOException $e) {
                // Log and throw clean exception for Global ErrorHandler
                throw new \RuntimeException("Gagal menghubungkan ke Database MySQL: " . $e->getMessage(), 500, $e);
            }
        }

        return self::$instance;
    }

    /**
     * Helper untuk eksekusi prepared query dengan proteksi try-catch
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \RuntimeException("Database Query Error: " . $e->getMessage() . " [SQL: {$sql}]", 500, $e);
        }
    }

    /**
     * Helper untuk mengambil satu baris data (single row)
     */
    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * Helper untuk mengambil semua baris data (multiple rows)
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Helper untuk mengambil ID terakhir yang di-insert
     */
    public static function lastInsertId(): string|false
    {
        return self::getConnection()->lastInsertId();
    }

    /**
     * Helper Transaksi Database
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }
}
