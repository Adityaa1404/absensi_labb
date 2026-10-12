<?php

namespace App\Controllers;

use Core\Guard;
use App\Models\Dispensasi;
use App\Models\Plotting;
use App\Models\MataKuliah;
use App\Models\Setting;
use App\Services\PdfDispensasi;

class DispensasiController
{
    // =========================================================================
    // MODUL: ASISTEN DOSEN (ASDOS)
    // =========================================================================

    /**
     * Halaman index pengajuan surat dispensasi asdos
     */
    public function asdosIndex(): void
    {
        Guard::requireRole('asdos');
        Guard::requireActiveAccount();

        $currentUser   = Guard::user();
        $asdosId       = (int)Guard::id();
        $metrics       = Dispensasi::getMetricsByAsdos($asdosId);
        $plottingList  = Plotting::getActiveForAsdos($asdosId);
        $dispensasiList = Dispensasi::getByAsdos($asdosId);
        $nomorSuratNext = Dispensasi::generateNomorSurat();

        require_once __DIR__ . '/../Views/Asdos/dispensasi.php';
    }

    /**
     * Submit pengajuan surat dispensasi baru oleh asdos
     */
    public function asdosCreate(): void
    {
        Guard::requireRole('asdos');
        Guard::requireActiveAccount();

        $asdosId            = (int)Guard::id();
        $plottingId         = (int)($_POST['plotting_id'] ?? 0);
        $tanggalPelaksanaan = trim($_POST['tanggal_pelaksanaan'] ?? '');
        $tujuanSurat        = trim($_POST['tujuan_surat'] ?? '');
        $tempatPelaksanaan  = trim($_POST['tempat_pelaksanaan'] ?? '');
        $jenisKegiatan      = trim($_POST['jenis_kegiatan'] ?? 'magang');
        if (!in_array($jenisKegiatan, ['magang', 'perkuliahan'], true)) {
            $jenisKegiatan = 'magang';
        }

        // Validasi input form
        $errors = [];
        if (empty($plottingId)) {
            $errors[] = 'Silakan pilih penugasan mata kuliah.';
        } else {
            // Pastikan plotting tersebut memang aktif dan milik asdos yang sedang login
            $plotting = Plotting::findActiveForAsdos($plottingId, $asdosId);
            if (!$plotting) {
                $errors[] = 'Penugasan mata kuliah yang dipilih tidak valid atau sudah kadaluarsa.';
            }
        }

        if (empty($tanggalPelaksanaan) || !strtotime($tanggalPelaksanaan)) {
            $errors[] = 'Tanggal pelaksanaan harus diisi dengan format tanggal yang valid.';
        }

        if (empty($tujuanSurat) || mb_strlen($tujuanSurat) < 3) {
            $errors[] = 'Tujuan surat wajib diisi (minimal 3 karakter).';
        }

        if (empty($tempatPelaksanaan) || mb_strlen($tempatPelaksanaan) < 3) {
            $errors[] = 'Tempat pelaksanaan wajib diisi (minimal 3 karakter).';
        }

        if (!empty($errors)) {
            Guard::setFlash('error', implode('<br>', $errors));
            Guard::redirect('/asdos/dispensasi');
            return;
        }

        // Generate nomor surat dan simpan
        $nomorSurat = Dispensasi::generateNomorSurat();

        Dispensasi::create([
            'plotting_id'         => $plottingId,
            'nomor_surat'         => $nomorSurat,
            'tanggal_pelaksanaan' => $tanggalPelaksanaan,
            'tujuan_surat'        => $tujuanSurat,
            'tempat_pelaksanaan'  => $tempatPelaksanaan,
            'jenis_kegiatan'      => $jenisKegiatan,
        ]);

        Guard::setFlash('success', "Pengajuan Surat Dispensasi berhasil dikirim dengan Nomor [{$nomorSurat}]. Menunggu persetujuan Dosen Pengampu.");
        Guard::redirect('/asdos/dispensasi');
    }

    /**
     * Download / preview PDF surat dispensasi yang telah disetujui (Asdos)
     */
    public function asdosDownload(string $id): void
    {
        Guard::requireRole('asdos');

        $asdosId    = (int)Guard::id();
        $dispensasi = Dispensasi::findById((int)$id);

        if (!$dispensasi) {
            Guard::setFlash('error', 'Data pengajuan dispensasi tidak ditemukan.');
            Guard::redirect('/asdos/dispensasi');
            return;
        }

        if ((int)$dispensasi['asdos_id'] !== $asdosId) {
            Guard::setFlash('error', 'Akses ditolak: Anda tidak memiliki akses ke surat dispensasi ini.');
            Guard::redirect('/asdos/dispensasi');
            return;
        }

        if ($dispensasi['status'] !== 'disetujui') {
            Guard::setFlash('error', 'Surat dispensasi belum dapat diunduh karena belum disetujui oleh Dosen Pengampu.');
            Guard::redirect('/asdos/dispensasi');
            return;
        }

        PdfDispensasi::stream($dispensasi, 'I');
    }


    // =========================================================================
    // MODUL: DOSEN PENGAMPU
    // =========================================================================

    /**
     * Halaman daftar approval surat dispensasi dosen
     */
    public function dosenIndex(): void
    {
        Guard::requireRole('dosen');
        Guard::requireActiveAccount();

        $currentUser = Guard::user();
        $dosenId     = (int)Guard::id();

        $statusFilter  = trim($_GET['status'] ?? '');
        $matkulFilter  = (int)($_GET['matkul_id'] ?? 0);

        $filters = [];
        if (in_array($statusFilter, ['menunggu', 'disetujui', 'ditolak'], true)) {
            $filters['status'] = $statusFilter;
        }
        if ($matkulFilter > 0) {
            $filters['matkul_id'] = $matkulFilter;
        }

        $dispensasiList = Dispensasi::getByDosen($dosenId, $filters);
        $matkulList     = MataKuliah::all(['dosen_id' => $dosenId]);
        $metrics        = Dispensasi::getMetricsByDosen($dosenId);

        require_once __DIR__ . '/../Views/Dosen/dispensasi.php';
    }

    /**
     * Proses approval / rejection surat dispensasi oleh dosen
     */
    public function dosenUpdateStatus(string $id): void
    {
        Guard::requireRole('dosen');
        Guard::requireActiveAccount();

        $dosenId    = (int)Guard::id();
        $dispensasi = Dispensasi::findById((int)$id);

        if (!$dispensasi) {
            Guard::setFlash('error', 'Data pengajuan dispensasi tidak ditemukan.');
            Guard::redirect('/dosen/dispensasi');
            return;
        }

        // Pastikan dosen ini benar-benar pengampu mata kuliah terkait
        if ((int)$dispensasi['dosen_id'] !== $dosenId) {
            Guard::setFlash('error', 'Akses ditolak: Anda bukan dosen pengampu mata kuliah ini.');
            Guard::redirect('/dosen/dispensasi');
            return;
        }

        $status  = trim($_POST['status'] ?? '');
        $catatan = trim($_POST['catatan_dosen'] ?? '');

        if (!in_array($status, ['disetujui', 'ditolak'], true)) {
            Guard::setFlash('error', 'Pilihan tindakan approval tidak valid.');
            Guard::redirect('/dosen/dispensasi');
            return;
        }

        // Jika ditolak, catatan wajib diisi
        if ($status === 'ditolak' && empty($catatan)) {
            Guard::setFlash('error', 'Harap masukkan alasan/catatan jika menolak pengajuan surat dispensasi.');
            Guard::redirect('/dosen/dispensasi');
            return;
        }

        Dispensasi::updateStatus((int)$id, $status, $catatan !== '' ? $catatan : null);

        $pesan = ($status === 'disetujui') 
            ? "Surat dispensasi nomor [{$dispensasi['nomor_surat']}] berhasil disetujui."
            : "Surat dispensasi nomor [{$dispensasi['nomor_surat']}] telah ditolak.";

        Guard::setFlash('success', $pesan);
        Guard::redirect('/dosen/dispensasi');
    }

    /**
     * Download / preview PDF surat dispensasi yang telah disetujui (Dosen)
     */
    public function dosenDownload(string $id): void
    {
        Guard::requireRole('dosen');

        $dosenId    = (int)Guard::id();
        $dispensasi = Dispensasi::findById((int)$id);

        if (!$dispensasi) {
            Guard::setFlash('error', 'Data pengajuan dispensasi tidak ditemukan.');
            Guard::redirect('/dosen/dispensasi');
            return;
        }

        if ((int)$dispensasi['dosen_id'] !== $dosenId) {
            Guard::setFlash('error', 'Akses ditolak: Anda bukan dosen pengampu surat dispensasi ini.');
            Guard::redirect('/dosen/dispensasi');
            return;
        }

        if ($dispensasi['status'] !== 'disetujui') {
            Guard::setFlash('error', 'Surat dispensasi belum dapat diunduh karena belum disetujui.');
            Guard::redirect('/dosen/dispensasi');
            return;
        }

        PdfDispensasi::stream($dispensasi, 'I');
    }


    // =========================================================================
    // MODUL: SUPER ADMIN (MONITORING & SETTINGS)
    // =========================================================================

    /**
     * Halaman monitoring seluruh surat dispensasi (Super Admin)
     */
    public function adminIndex(): void
    {
        Guard::requireRole('super_admin');

        $currentUser   = Guard::user();
        $statusFilter  = trim($_GET['status'] ?? '');
        $matkulFilter  = (int)($_GET['matkul_id'] ?? 0);
        $searchFilter  = trim($_GET['search'] ?? '');

        $filters = [];
        if (in_array($statusFilter, ['menunggu', 'disetujui', 'ditolak'], true)) {
            $filters['status'] = $statusFilter;
        }
        if ($matkulFilter > 0) {
            $filters['matkul_id'] = $matkulFilter;
        }
        if ($searchFilter !== '') {
            $filters['search'] = $searchFilter;
        }

        $dispensasiList = Dispensasi::getAll($filters);
        $matkulList     = MataKuliah::all();
        $metrics        = Dispensasi::getMetricsAll();

        require_once __DIR__ . '/../Views/SuperAdmin/dispensasi.php';
    }

    /**
     * Download PDF surat dispensasi oleh Super Admin
     */
    public function adminDownload(string $id): void
    {
        Guard::requireRole('super_admin');

        $dispensasi = Dispensasi::findById((int)$id);

        if (!$dispensasi) {
            Guard::setFlash('error', 'Data pengajuan dispensasi tidak ditemukan.');
            Guard::redirect('/superadmin/dispensasi');
            return;
        }

        PdfDispensasi::stream($dispensasi, 'I');
    }

    /**
     * Hapus record surat dispensasi (Super Admin)
     */
    public function adminDelete(string $id): void
    {
        Guard::requireRole('super_admin');

        $dispensasi = Dispensasi::findById((int)$id);
        if (!$dispensasi) {
            Guard::setFlash('error', 'Data pengajuan dispensasi tidak ditemukan.');
            Guard::redirect('/superadmin/dispensasi');
            return;
        }

        Dispensasi::delete((int)$id);

        Guard::setFlash('success', "Pengajuan dispensasi nomor [{$dispensasi['nomor_surat']}] berhasil dihapus.");
        Guard::redirect('/superadmin/dispensasi');
    }

    /**
     * Halaman manajemen konfigurasi institusi & kop surat (Super Admin)
     */
    public function adminSettings(): void
    {
        Guard::requireRole('super_admin');

        $currentUser = Guard::user();
        $settings    = Setting::all();

        require_once __DIR__ . '/../Views/SuperAdmin/settings.php';
    }

    /**
     * Simpan pembaruan data pengaturan institusi
     */
    public function adminUpdateSettings(): void
    {
        Guard::requireRole('super_admin');

        $submittedSettings = $_POST['settings'] ?? [];

        if (is_array($submittedSettings) && !empty($submittedSettings)) {
            Setting::updateBatch($submittedSettings);
            Guard::setFlash('success', 'Konfigurasi institusi dan surat resmi berhasil diperbarui.');
        } else {
            Guard::setFlash('warning', 'Tidak ada data pengaturan yang dikirim.');
        }

        Guard::redirect('/superadmin/settings');
    }
}
