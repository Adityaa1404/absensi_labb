<?php

namespace App\Controllers;

use Core\Guard;
use Core\Database;

class AsdosController
{
    /**
     * Dashboard Asisten Dosen
     */
    public function dashboard(): void
    {
        Guard::requireRole('asdos');
        $currentUser = Guard::user();

        // Implementasi dashboard asdos
        require_once __DIR__ . '/../Views/Asdos/absensi.php';
    }

    /**
     * Riwayat Absensi Praktikum
     */
    public function history(): void
    {
        Guard::requireRole('asdos');
        $currentUser = Guard::user();

        require_once __DIR__ . '/../Views/Asdos/history.php';
    }
}
