<?php

namespace Core;

use PDO;
use PDOException;
use RuntimeException;

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
        $envPath = dirname(__DIR__) . '/.env';
        $this->loadEnv($envPath);


        $this->host     = $this->env('DB_HOST');
        $this->db_name  = $this->env('DB_NAME');
        $this->username = $this->env('DB_USERNAME');
        $this->password = $this->env('DB_PASSWORD');
        $this->port     = $this->env('DB_PORT');
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

    private function loadEnv(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File konfigurasi .env tidak ditemukan pada: {$filePath}");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            // Lewati komentar
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Pisahkan key dan value berdasarkan karakter '=' pertama
            list($key, $value) = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Bersihkan tanda kutip jika ada
            $value = trim($value, '"\'');

            // Masukkan ke superglobal environment PHP
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
    private function env(string $key, $default = null)
    {
        $value = getenv($key);
        return $value !== false ? $value : ($_ENV[$key] ?? $default);
    }
}
