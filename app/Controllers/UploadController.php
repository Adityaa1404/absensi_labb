<?php

namespace App\Controllers;

class UploadController
{
    /**
     * Menyajikan foto bukti absensi praktikum secara aman
     * Digunakan sebagai fallback jika web server (Apache/Nginx) meneruskan request statis ke index.php
     */
    public function serveAbsensi(string $filename): void
    {
        // Sanitasi nama file untuk mencegah directory traversal (LFI/Path Traversal)
        $cleanFilename = basename($filename);
        $filePath = dirname(__DIR__, 2) . '/public/uploads/absensi/' . $cleanFilename;

        if (!empty($cleanFilename) && file_exists($filePath) && is_file($filePath)) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png'         => 'image/png',
                'webp'        => 'image/webp',
                'gif'         => 'image/gif',
                default       => mime_content_type($filePath) ?: 'application/octet-stream',
            };

            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: public, max-age=86400');
            readfile($filePath);
            exit;
        }

        // Fallback SVG: Jika berkas fisik tidak ada di server, tampilkan visual placeholder elegan
        header('Content-Type: image/svg+xml; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        http_response_code(404);

        echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400" fill="none">
    <rect width="600" height="400" fill="#0f172a"/>
    <rect x="24" y="24" width="552" height="352" rx="16" stroke="#334155" stroke-width="2" stroke-dasharray="8 8"/>
    
    <!-- Icon Placeholder -->
    <circle cx="300" cy="160" r="44" fill="#1e293b" stroke="#475569" stroke-width="2"/>
    <path d="M284 172L296 156L308 168L316 160L328 172" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="292" cy="148" r="4" fill="#94a3b8"/>
    
    <!-- Text Labels -->
    <text x="300" y="235" fill="#f1f5f9" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="16" font-weight="700" text-anchor="middle">
        Foto Bukti Tidak Ditemukan
    </text>
    <text x="300" y="262" fill="#94a3b8" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="12" text-anchor="middle">
        Berkas fisik belum diunggah atau tidak tersimpan di server
    </text>
</svg>
SVG;
        exit;
    }

    /**
     * Menyajikan file statis assets (misal: /assets/img/logo_SI.jpeg)
     * Fallback ketika Apache/Nginx rewrite meneruskan request statis ke index.php
     */
    public function serveAsset(string $folder, string $filename): void
    {
        $cleanFolder   = basename($folder);
        $cleanFilename = basename($filename);
        $filePath      = dirname(__DIR__, 2) . '/public/assets/' . $cleanFolder . '/' . $cleanFilename;

        if (file_exists($filePath) && is_file($filePath)) {
            $this->outputAssetFile($filePath);
            return;
        }

        http_response_code(404);
        echo "Asset [{$cleanFilename}] tidak ditemukan.";
        exit;
    }

    /**
     * Output file statis dengan header MIME dan cache yang tepat
     */
    private function outputAssetFile(string $filePath): void
    {
        $ext  = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'webp'        => 'image/webp',
            'gif'         => 'image/gif',
            'svg'         => 'image/svg+xml',
            'ico'         => 'image/x-icon',
            'css'         => 'text/css; charset=utf-8',
            'js'          => 'application/javascript; charset=utf-8',
            default       => mime_content_type($filePath) ?: 'application/octet-stream',
        };

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=31536000, immutable');
        readfile($filePath);
        exit;
    }
}
