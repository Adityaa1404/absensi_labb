<?php

namespace App\Controllers;

use Core\Guard;
use Core\Validator;
use App\Models\Absensi;
use App\Models\MataKuliah;
use App\Models\Plotting;

class DosenController
{
    /**
     * Halaman Dashboard Utama Dosen Pengampu
     */
    public function dashboard(): void
    {
        Guard::requireRole('dosen');
        $currentUser = Guard::user();
        $dosenId = (int)$currentUser['id_user'];

        // Metrik statistik absensi khusus mata kuliah yang diampu dosen
        $metrics = Absensi::getMonitoringMetricsByDosen($dosenId);

        // Mata kuliah yang diampu beserta plotting asdosnya
        $myMatkul = MataKuliah::all(['dosen_id' => $dosenId]);
        $myPlottings = Plotting::getByDosen($dosenId);

        $plottingsByMatkul = [];
        $totalAsdosAktif = 0;
        $uniqueAsdosIds = [];

        foreach ($myPlottings as $p) {
            $plottingsByMatkul[$p['matkul_id']][] = $p;
            if ((int)$p['is_active'] === 1 && !empty($p['asdos_id'])) {
                $uniqueAsdosIds[$p['asdos_id']] = true;
            }
        }
        $totalAsdosAktif = count($uniqueAsdosIds);

        // Ambil statistik absensi per mata kuliah (pending, disetujui, total)
        $matkulMetrics = Absensi::getMetricsGroupedByMatkulForDosen($dosenId);

        foreach ($myMatkul as &$m) {
            $m['plottings'] = $plottingsByMatkul[$m['id_matkul']] ?? [];
            $m['metrics']   = $matkulMetrics[(int)$m['id_matkul']] ?? [
                'total'     => 0,
                'disetujui' => 0,
                'pending'   => 0,
                'ditolak'   => 0,
            ];
        }
        unset($m);

        // Absensi yang masih pending dan perlu verifikasi segera
        $pendingAbsensi = Absensi::getAllMonitoring([
            'dosen_id'          => $dosenId,
            'status_verifikasi' => 'pending'
        ]);

        // Riwayat absensi terbaru
        $recentAbsensi = Absensi::getRecentByDosen($dosenId, 5);

        require_once __DIR__ . '/../Views/Dosen/dashboard.php';
    }

    /**
     * Halaman Khusus Detail Mata Kuliah & Rekapitulasi Absensi Praktikum
     */
    public function detailMatkul(string $id): void
    {
        Guard::requireRole('dosen');
        $currentUser = Guard::user();
        $dosenId = (int)$currentUser['id_user'];
        $matkulId = (int)$id;

        // Ambil data mata kuliah
        $matkul = MataKuliah::findById($matkulId);

        // Validasi Otorisasi Kepemilikan: Pastikan mata kuliah ini benar diampu oleh dosen ybs
        if (!$matkul || (int)$matkul['dosen_id'] !== $dosenId) {
            Guard::setFlash('error', 'Akses Ditolak: Anda tidak memiliki akses ke mata kuliah tersebut.');
            Guard::redirect('/dosen/dashboard');
        }

        // Ambil daftar plotting asdos untuk mata kuliah ini
        $plottings = Plotting::all(['matkul_id' => $matkulId]);

        // Filter absensi khusus mata kuliah ini
        $filters = [
            'matkul_id'         => $matkulId,
            'dosen_id'          => $dosenId,
            'search'            => trim($_GET['q'] ?? ''),
            'status_verifikasi' => trim($_GET['status'] ?? ''),
            'date_start'        => trim($_GET['start'] ?? ''),
            'date_end'          => trim($_GET['end'] ?? ''),
        ];

        // Metrik statistik khusus mata kuliah ini
        $metrics = Absensi::getMetricsByMatkul($matkulId);

        // Daftar seluruh absensi praktikum mata kuliah ini sesuai filter
        $absensiList = Absensi::getAllMonitoring($filters);

        require_once __DIR__ . '/../Views/Dosen/detail_matkul.php';
    }

    /**
     * Halaman Monitoring Absensi Praktikum Khusus Mata Kuliah yang Diampu
     */
    public function monitoring(): void
    {
        Guard::requireRole('dosen');
        $currentUser = Guard::user();
        $dosenId = (int)$currentUser['id_user'];

        // Ambil filter dari request jika ada
        $filters = [
            'dosen_id'          => $dosenId,
            'search'            => trim($_GET['q'] ?? ''),
            'status_verifikasi' => trim($_GET['status'] ?? ''),
            'matkul_id'         => trim($_GET['matkul'] ?? ''),
            'date_start'        => trim($_GET['start'] ?? ''),
            'date_end'          => trim($_GET['end'] ?? ''),
        ];

        // Ambil metrik ringkasan
        $metrics = Absensi::getMonitoringMetricsByDosen($dosenId);

        // Ambil seluruh data absensi sesuai mata kuliah yang diampu
        $absensiList = Absensi::getAllMonitoring($filters);

        // Ambil daftar mata kuliah khusus milik dosen untuk dropdown filter
        $matkulList = MataKuliah::all(['dosen_id' => $dosenId]);

        require_once __DIR__ . '/../Views/Dosen/monitoring.php';
    }

    /**
     * Ubah Status Verifikasi Absensi (Hak Akses Dosen Pengampu Mata Kuliah)
     */
    public function updateStatus(string $id): void
    {
        Guard::requireRole('dosen');
        Guard::verifyCsrf();

        $currentUser = Guard::user();
        $dosenId = (int)$currentUser['id_user'];
        $absensiId = (int)$id;
        $redirectTo = $_POST['redirect_to'] ?? '/dosen/dashboard';

        // Validasi Otorisasi Kepemilikan: Pastikan absensi ini benar milik mata kuliah dosen ybs
        $absensi = Absensi::findByIdForDosen($absensiId, $dosenId);

        if (!$absensi) {
            Guard::setFlash('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk memverifikasi absensi pada mata kuliah ini.');
            Guard::redirect('/dosen/dashboard');
        }

        $status     = trim($_POST['status_verifikasi'] ?? '');
        $pesanDosen = trim($_POST['pesan_dosen'] ?? '');

        $validator = new Validator($_POST);
        $validator->rules([
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
        ], [
            'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
            'status_verifikasi.in'       => 'Pilihan status verifikasi tidak valid.',
        ]);

        if ($validator->fails()) {
            $validator->flashErrors();
            Guard::redirect($redirectTo);
        }

        Absensi::updateStatusVerifikasi($absensiId, $status, $pesanDosen);

        $statusLabel = match ($status) {
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => 'Menunggu Verifikasi (Pending)'
        };

        Guard::setFlash('success', "Status verifikasi absensi Pertemuan ke-{$absensi['pertemuan_ke']} ({$absensi['nama_asdos']} - {$absensi['nama_matkul']}) berhasil diubah menjadi: {$statusLabel}.");
        Guard::redirect($redirectTo);
    }
}
