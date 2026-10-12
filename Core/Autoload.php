<?php
// Load Composer Autoloader (untuk library pihak ketiga seperti TCPDF)
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

/**
 * PSR-4 Autoloader Sederhana untuk Absensi Asdos
 * Otomatis me-load class dari namespace 'App\' dan 'Core\'
 */
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\'  => __DIR__ . '/../app/',
        'Core\\' => __DIR__ . '/../Core/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
