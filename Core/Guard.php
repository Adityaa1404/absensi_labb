<?php

namespace Core;

class Guard
{
    /**
     * Memastikan session aktif
     */
    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Cek apakah user sedang login
     */
    public static function isLoggedIn(): bool
    {
        self::ensureSession();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id_user']);
    }

    /**
     * Mendapatkan data user yang sedang login
     */
    public static function user(): ?array
    {
        self::ensureSession();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Mendapatkan ID user login
     */
    public static function id(): ?int
    {
        return self::user()['id_user'] ?? null;
    }

    /**
     * Mendapatkan Role user login
     */
    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    /**
     * Cek apakah akun berstatus aktif (is_active = 1)
     */
    public static function isActive(): bool
    {
        $user = self::user();
        return $user !== null && (int)($user['is_active'] ?? 0) === 1;
    }

    /**
     * Guard: Wajib Login
     */
    public static function requireLogin(string $redirect = '/login'): void
    {
        if (!self::isLoggedIn()) {
            self::setFlash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
            self::redirect($redirect);
        }
    }

    /**
     * Guard: Wajib Guest (belum login, misal untuk halaman /login atau /register)
     */
    public static function requireGuest(): void
    {
        if (self::isLoggedIn()) {
            $role = self::role();
            $target = match ($role) {
                'super_admin' => '/superadmin/dashboard',
                'dosen'       => '/dosen/dashboard',
                'asdos'       => '/asdos/dashboard',
                default       => '/'
            };
            self::redirect($target);
        }
    }

    /**
     * Guard: Wajib Memiliki Role Tertentu
     */
    public static function requireRole(string|array $allowedRoles): void
    {
        self::requireLogin();

        $allowedRoles = (array) $allowedRoles;
        $currentRole  = self::role();

        if (!in_array($currentRole, $allowedRoles, true)) {
            http_response_code(403);
            self::setFlash('error', 'Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.');
            
            // Arahkan ke dashboard sesuai role yang dimiliki
            $target = match ($currentRole) {
                'super_admin' => '/superadmin/dashboard',
                'dosen'       => '/dosen/dashboard',
                'asdos'       => '/asdos/dashboard',
                default       => '/login'
            };
            self::redirect($target);
        }
    }

    public static function requireAsdos() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !=='asdos') {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Guard: Wajib Akun Aktif (Sesuai Aturan Bisnis BR2 PRD)
     * Akun nonaktif diblokir dari semua aksi tulis (POST/CRUD/Upload)
     */
    public static function requireActiveAccount(): void
    {
        self::requireLogin();

        if (!self::isActive()) {
            http_response_code(403);
            self::setFlash('error', 'Akses Ditolak: Akun Anda sedang dinonaktifkan (mode hanya lihat). Aksi simpan/ubah data diblokir.');

            $currentRole = self::role();
            $target = match ($currentRole) {
                'asdos' => '/asdos/history',
                default => '/'
            };
            self::redirect($target);
        }
    }

    // =========================================================================
    // CSRF PROTECTION
    // =========================================================================

    /**
     * Generate CSRF token baru jika belum ada
     */
    public static function generateCsrfToken(): string
    {
        self::ensureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Ambil token CSRF saat ini
     */
    public static function csrfToken(): string
    {
        return self::generateCsrfToken();
    }

    /**
     * Helper HTML input field untuk CSRF
     */
    public static function csrfField(): string
    {
        $token = htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Verifikasi token CSRF pada request POST
     */
    public static function verifyCsrf(): void
    {
        self::ensureSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            $sessionToken = $_SESSION['csrf_token'] ?? null;

            if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
                http_response_code(403);
                self::setFlash('error', 'Sesi keamanan (CSRF) Anda telah kadaluarsa atau tidak valid. Silakan coba kembali.');
                
                $referer = $_SERVER['HTTP_REFERER'] ?? null;
                if ($referer) {
                    header('Location: ' . $referer);
                    exit;
                }
                
                require_once __DIR__ . '/ErrorHandler.php';
                ErrorHandler::renderProductionView(403, 'Token CSRF tidak valid atau sesi formulir telah kadaluarsa. Silakan refresh halaman dan coba kembali.');
                exit;
            }
        }
    }

    // =========================================================================
    // FLASH MESSAGE HELPER
    // =========================================================================

    /**
     * Set pesan flash session (success, error, warning, info)
     */
    public static function setFlash(string $type, string $message): void
    {
        self::ensureSession();
        $_SESSION['flash'][$type][] = $message;
    }

    /**
     * Ambil dan hapus pesan flash berdasarkan tipe
     */
    public static function getFlash(string $type): array
    {
        self::ensureSession();
        $messages = $_SESSION['flash'][$type] ?? [];
        unset($_SESSION['flash'][$type]);
        return $messages;
    }

    /**
     * Cek apakah ada pesan flash bertipe tertentu
     */
    public static function hasFlash(string $type): bool
    {
        self::ensureSession();
        return !empty($_SESSION['flash'][$type]);
    }

    /**
     * Mendapatkan Base URL proyek tanpa akhiran /public
     */
    public static function getBaseUrl(): string
    {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = str_replace('\\', '/', $scriptDir);

        // Hapus akhiran /public agar URL bersih
        if (str_ends_with($scriptDir, '/public')) {
            $scriptDir = substr($scriptDir, 0, -7);
        }

        return ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/');
    }

    /**
     * Helper URL Generator (Mendukung Subfolder Laragon/XAMPP tanpa /public)
     */
    public static function url(string $path = ''): string
    {
        $cleanPath = '/' . ltrim($path, '/');
        return self::getBaseUrl() . $cleanPath;
    }

    /**
     * Helper Redirect URL
     */
    public static function redirect(string $path): void
    {
        header('Location: ' . self::url($path));
        exit;
    }
}
