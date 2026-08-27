<?php

namespace Core;

use Throwable;
use ErrorException;

class ErrorHandler
{
    private static string $logDir = __DIR__ . '/../app/logs';

    /**
     * Daftarkan Global Error & Exception Handler
     */
    public static function register(): void
    {
        // 1. Tangani Error PHP biasa (Notice, Warning, dll)
        set_error_handler([self::class, 'handleError']);

        // 2. Tangani Unhandled Exceptions
        set_exception_handler([self::class, 'handleException']);

        // 3. Tangani Fatal Error saat shutdown
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Ubah PHP Error menjadi ErrorException
     */
    public static function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
        return false;
    }

    /**
     * Tangani Exception dan tampilkan halaman error yang sesuai
     */
    public static function handleException(Throwable $e): void
    {
        // Catat ke file log
        self::log($e);

        $httpCode = 500;
        if ($e->getCode() >= 400 && $e->getCode() < 600) {
            $httpCode = $e->getCode();
        }

        if (!headers_sent()) {
            http_response_code($httpCode);
        }

        $isDebug = defined('APP_DEBUG') && APP_DEBUG === true;

        if ($isDebug) {
            self::renderDebugView($e, $httpCode);
        } else {
            self::renderProductionView($httpCode);
        }
        exit;
    }

    /**
     * Tangani Fatal Error saat script selesai dieksekusi
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            self::handleException($exception);
        }
    }

    /**
     * Catat error ke file app/logs/error.log
     */
    public static function log(Throwable $e): void
    {
        if (!is_dir(self::$logDir)) {
            @mkdir(self::$logDir, 0755, true);
        }

        $logFile = self::$logDir . '/error.log';
        $time    = date('Y-m-d H:i:s');
        $uri     = $_SERVER['REQUEST_URI'] ?? 'CLI';
        $method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $msg     = "[{$time}] [{$method} {$uri}] " . get_class($e) . ": {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\n" .
                   "Stack trace:\n" . $e->getTraceAsString() . "\n" . str_repeat('-', 80) . "\n";

        @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
    }

    /**
     * Tampilan Error Khusus Mode Development (Debug Info Lengkap)
     */
    private static function renderDebugView(Throwable $e, int $httpCode): void
    {
        $errorClass   = get_class($e);
        $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        $errorFile    = htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8');
        $errorLine    = $e->getLine();
        $stackTrace   = htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');

        // Baca beberapa baris kode di sekitar line error jika file ada
        $codeSnippet = self::getCodeSnippet($e->getFile(), $errorLine);

        echo "<!DOCTYPE html>
        <html lang='id' class='bg-slate-900 text-slate-100'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Error {$httpCode} — {$errorClass}</title>
            <script src='https://cdn.tailwindcss.com'></script>
            <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap' rel='stylesheet'>
            <style>
                body { font-family: 'Inter', sans-serif; }
                code, pre { font-family: 'JetBrains Mono', monospace; }
            </style>
        </head>
        <body class='p-6 sm:p-10 max-w-6xl mx-auto space-y-6'>
            <div class='bg-red-950/80 border border-red-800/80 rounded-2xl p-6 shadow-2xl backdrop-blur-md'>
                <div class='flex items-center gap-3 mb-2'>
                    <span class='px-2.5 py-1 text-xs font-bold uppercase tracking-wider bg-red-500 text-white rounded-md'>
                        HTTP {$httpCode} Exception
                    </span>
                    <span class='text-xs text-red-300 font-mono'>{$errorClass}</span>
                </div>
                <h1 class='text-xl sm:text-2xl font-bold text-red-100 leading-snug'>{$errorMessage}</h1>
                <p class='text-xs text-red-300/80 mt-2 font-mono break-all'>
                    Terjadi pada file <strong>{$errorFile}</strong> di baris <strong>{$errorLine}</strong>
                </p>
            </div>

            {$codeSnippet}

            <div class='bg-slate-800/90 border border-slate-700 rounded-2xl p-6 shadow-xl'>
                <h3 class='text-sm font-bold uppercase tracking-wider text-slate-300 mb-3'>Stack Trace</h3>
                <pre class='text-xs text-slate-300 bg-slate-950/80 p-4 rounded-xl overflow-x-auto leading-relaxed border border-slate-800'>{$stackTrace}</pre>
            </div>

            <p class='text-center text-xs text-slate-500'>
                Laboratorium Sistem Informasi &bull; Mode Debug Aktif (Nonaktifkan <code>APP_DEBUG = false</code> di production)
            </p>
        </body>
        </html>";
    }

    /**
     * Tampilan Error Khusus Mode Production (Aman & Ramah Pengguna)
     */
    public static function renderProductionView(int $httpCode = 500, string $customMessage = ''): void
    {
        $title = match ($httpCode) {
            403 => 'Akses Ditolak',
            404 => 'Halaman Tidak Ditemukan',
            default => 'Terjadi Kesalahan Server'
        };

        $desc = match ($httpCode) {
            403 => 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.',
            404 => 'Halaman atau rute yang Anda tuju tidak ditemukan di sistem.',
            default => 'Sistem sedang mengalami kendala teknis internal. Tim kami telah mencatat masalah ini.'
        };

        if (!empty($customMessage)) {
            $desc = htmlspecialchars($customMessage, ENT_QUOTES, 'UTF-8');
        }

        $baseUrl = \Core\Guard::getBaseUrl();

        echo "<!DOCTYPE html>
        <html lang='id' class='h-full bg-slate-50'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$httpCode} - {$title}</title>
            <script src='https://cdn.tailwindcss.com'></script>
            <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
            <style> body { font-family: 'Inter', sans-serif; } </style>
        </head>
        <body class='h-full flex items-center justify-center p-4 text-slate-800'>
            <div class='max-w-md w-full text-center bg-white border border-slate-200 p-8 sm:p-10 rounded-2xl shadow-xs space-y-5'>
                <div class='w-14 h-14 mx-auto rounded-2xl bg-red-50 border border-red-200 text-red-600 flex items-center justify-center font-bold text-xl'>
                    {$httpCode}
                </div>
                <div>
                    <h1 class='text-xl font-bold text-slate-900'>{$title}</h1>
                    <p class='text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed'>{$desc}</p>
                </div>
                <div class='pt-2 flex flex-col sm:flex-row gap-2 justify-center'>
                    <a href='{$baseUrl}/login' class='px-4 py-2 bg-[#1867c0] hover:bg-[#14529d] text-white text-xs font-semibold rounded-lg transition shadow-xs'>
                        &larr; Ke Halaman Utama
                    </a>
                    <button onclick='window.history.back()' class='px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition'>
                        Kembali
                    </button>
                </div>
                <p class='text-[11px] text-slate-400 pt-2'>&copy; " . date('Y') . " Laboratorium Sistem Informasi</p>
            </div>
        </body>
        </html>";
    }

    /**
     * Helper untuk mengambil baris kode di sekitar error
     */
    private static function getCodeSnippet(string $file, int $errorLine): string
    {
        if (!file_exists($file) || !is_readable($file)) {
            return '';
        }

        $lines = file($file);
        $start = max(0, $errorLine - 5);
        $end   = min(count($lines), $errorLine + 5);

        $output = "<div class='bg-slate-800/90 border border-slate-700 rounded-2xl p-6 shadow-xl'>
            <h3 class='text-sm font-bold uppercase tracking-wider text-slate-300 mb-3'>Cuplikan Kode</h3>
            <div class='bg-slate-950 text-xs font-mono rounded-xl p-4 overflow-x-auto border border-slate-800 leading-relaxed'>";

        for ($i = $start; $i < $end; $i++) {
            $lineNum   = $i + 1;
            $lineCode  = htmlspecialchars($lines[$i] ?? '', ENT_QUOTES, 'UTF-8');
            $isTarget  = ($lineNum === $errorLine);
            $bgClass   = $isTarget ? 'bg-red-500/20 text-red-200 font-bold border-l-2 border-red-500 pl-2' : 'text-slate-400 pl-2';
            $numClass  = $isTarget ? 'text-red-400 font-bold' : 'text-slate-600';

            $output .= "<div class='flex items-center {$bgClass}'>
                <span class='w-8 shrink-0 text-right pr-3 select-none {$numClass}'>{$lineNum}</span>
                <span class='whitespace-pre'>{$lineCode}</span>
            </div>";
        }

        $output .= "</div></div>";
        return $output;
    }
}
