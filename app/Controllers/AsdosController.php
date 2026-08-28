<?php

namespace App\Controllers;

use Core\Guard;
use Core\Validator;
use App\Models\Absensi;
use App\Models\Plotting;

class AsdosController
{
    /** Folder penyimpanan foto bukti (nama file acak - opsi keamanan §9 PRD) */
    private const UPLOAD_DIR = __DIR__ . '/../../public/uploads/absensi/';
    private const MAX_UPLOAD_SIZE = 2 * 1024 * 1024; // 2 MB (BR7 / F4)
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function dashboard(): void
    {
        Guard::requireRole('asdos');

        $currentUser     = Guard::user();
        $asdosId         = Guard::id();
        $metrics         = Absensi::getMetricsByAsdos($asdosId);
        $activePlottings = Plotting::getActiveForAsdos($asdosId);
        $recentAbsensi   = Absensi::getRecentByAsdos($asdosId, 5);

        require_once __DIR__ . '/../Views/Asdos/dashboard.php';
    }

    // =========================================================================
    // MODUL: MATKUL SAYA (Read-only, tetap bisa diakses walau nonaktif)
    // =========================================================================

    public function matkul(): void
    {
        Guard::requireRole('asdos');

        $currentUser  = Guard::user();
        $asdosId      = Guard::id();
        $plottingList = Plotting::getAllForAsdos($asdosId);

        require_once __DIR__ . '/../Views/Asdos/matkul.php';
    }

    // =========================================================================
    // MODUL: ABSENSI & BUKTI KEGIATAN (F4) - Hanya untuk asdos AKTIF
    // =========================================================================

    public function absensi(): void
    {
        Guard::requireRole('asdos');
        Guard::requireActiveAccount(); // F4: hanya asdos aktif yang bisa mengisi/mengelola absensi

        $currentUser     = Guard::user();
        $asdosId         = Guard::id();
        $activePlottings = Plotting::getActiveForAsdos($asdosId);

        $filters = [
            'matkul_id'         => $_GET['matkul'] ?? '',
            'status_verifikasi' => $_GET['status'] ?? '',
        ];
        $absensiList = Absensi::getByAsdos($asdosId, $filters);

        if (empty($activePlottings)) {
            Guard::setFlash('warning', 'Anda belum diplot ke mata kuliah manapun oleh Super Admin. Silakan hubungi admin lab untuk penugasan matkul.');
        }

        require_once __DIR__ . '/../Views/Asdos/absensi.php';
    }

    /** Tambah Absensi Baru */
    public function createAbsensi(): void
    {
        Guard::requireRole('asdos');
        Guard::requireActiveAccount();
        Guard::verifyCsrf();

        $asdosId     = Guard::id();
        $plottingId  = (int)($_POST['plotting_id'] ?? 0);
        $tanggal     = trim($_POST['tanggal'] ?? '');
        $pertemuanKe = trim($_POST['pertemuan_ke'] ?? '');
        $jamMulai    = trim($_POST['jam_mulai'] ?? '');
        $jamSelesai  = trim($_POST['jam_selesai'] ?? '');
        $deskripsi   = trim($_POST['deskripsi_tugas'] ?? '');

        $validator = new Validator($_POST);
        $validator->rules([
            'plotting_id'     => 'required|numeric',
            'tanggal'         => 'required',
            'deskripsi_tugas' => 'required|min:5',
        ], [
            'plotting_id.required'     => 'Mata kuliah wajib dipilih.',
            'tanggal.required'         => 'Tanggal pelaksanaan wajib diisi.',
            'deskripsi_tugas.required' => 'Deskripsi tugas/kegiatan wajib diisi.',
            'deskripsi_tugas.min'      => 'Deskripsi tugas minimal 5 karakter.',
        ]);

        if ($validator->fails()) {
            $validator->flashErrors();
            Guard::redirect('/asdos/absensi');
        }

        // BR1: Validasi kepemilikan - plotting harus AKTIF dan benar milik asdos yang sedang login
        $plotting = Plotting::findActiveForAsdos($plottingId, $asdosId);
        if (!$plotting) {
            Guard::setFlash('error', 'Mata kuliah yang dipilih tidak valid, atau Anda tidak (lagi) terplot aktif pada mata kuliah tersebut.');
            Guard::redirect('/asdos/absensi');
        }

        if ($jamMulai !== '' && $jamSelesai !== '' && $jamSelesai <= $jamMulai) {
            Guard::setFlash('error', 'Jam selesai harus lebih besar dari jam mulai.');
            Guard::redirect('/asdos/absensi');
        }

        // BR7: Foto bukti wajib ada saat submit pertama kali
        try {
            $fotoKegiatan = $this->handleImageUpload($_FILES['foto_kegiatan'] ?? null, 'foto kegiatan');
            $fotoSelfie   = $this->handleImageUpload($_FILES['foto_selfie'] ?? null, 'foto selfie');
        } catch (\RuntimeException $e) {
            Guard::setFlash('error', $e->getMessage());
            Guard::redirect('/asdos/absensi');
        }

        if (empty($fotoKegiatan) || empty($fotoSelfie)) {
            Guard::setFlash('error', 'Foto kegiatan dan foto selfie wajib diunggah sebagai bukti pelaksanaan (BR7).');
            Guard::redirect('/asdos/absensi');
        }

        Absensi::create([
            'plotting_id'     => $plottingId,
            'tanggal'         => $tanggal,
            'pertemuan_ke'    => $pertemuanKe,
            'jam_mulai'       => $jamMulai,
            'jam_selesai'     => $jamSelesai,
            'deskripsi_tugas' => $deskripsi,
            'foto_kegiatan'   => $fotoKegiatan,
            'foto_selfie'     => $fotoSelfie,
        ]);

        Guard::setFlash('success', "Absensi untuk [{$plotting['nama_matkul']}] tanggal " . date('d M Y', strtotime($tanggal)) . " berhasil disimpan dan menunggu verifikasi dosen.");
        Guard::redirect('/asdos/absensi');
    }

    // /** Perbarui Absensi (hanya selama status masih 'pending' - BR4) */
    // public function updateAbsensi(string $id): void
    // {
    //     Guard::requireRole('asdos');
    //     Guard::requireActiveAccount();
    //     Guard::verifyCsrf();

    //     $absensiId = (int)$id;
    //     $asdosId   = Guard::id();

    //     $absensi = Absensi::findByIdForAsdos($absensiId, $asdosId);
    //     if (!$absensi) {
    //         Guard::setFlash('error', 'Data absensi tidak ditemukan atau bukan milik Anda.');
    //         Guard::redirect('/asdos/absensi');
    //     }

    //     if ($absensi['status_verifikasi'] !== 'pending') {
    //         Guard::setFlash('error', 'Absensi yang sudah diverifikasi (disetujui/ditolak) tidak dapat diubah lagi.');
    //         Guard::redirect('/asdos/absensi');
    //     }

    //     $tanggal     = trim($_POST['tanggal'] ?? '');
    //     $pertemuanKe = trim($_POST['pertemuan_ke'] ?? '');
    //     $jamMulai    = trim($_POST['jam_mulai'] ?? '');
    //     $jamSelesai  = trim($_POST['jam_selesai'] ?? '');
    //     $deskripsi   = trim($_POST['deskripsi_tugas'] ?? '');

    //     $validator = new Validator($_POST);
    //     $validator->rules([
    //         'tanggal'         => 'required',
    //         'deskripsi_tugas' => 'required|min:5',
    //     ], [
    //         'tanggal.required'         => 'Tanggal pelaksanaan wajib diisi.',
    //         'deskripsi_tugas.required' => 'Deskripsi tugas/kegiatan wajib diisi.',
    //         'deskripsi_tugas.min'      => 'Deskripsi tugas minimal 5 karakter.',
    //     ]);

    //     if ($validator->fails()) {
    //         $validator->flashErrors();
    //         Guard::redirect('/asdos/absensi');
    //     }

    //     if ($jamMulai !== '' && $jamSelesai !== '' && $jamSelesai <= $jamMulai) {
    //         Guard::setFlash('error', 'Jam selesai harus lebih besar dari jam mulai.');
    //         Guard::redirect('/asdos/absensi');
    //     }

    //     // Foto bersifat opsional saat edit - hanya diganti jika asdos mengunggah file baru
    //     try {
    //         $fotoKegiatanBaru = $this->handleImageUpload($_FILES['foto_kegiatan'] ?? null, 'foto kegiatan');
    //         $fotoSelfieBaru   = $this->handleImageUpload($_FILES['foto_selfie'] ?? null, 'foto selfie');
    //     } catch (\RuntimeException $e) {
    //         Guard::setFlash('error', $e->getMessage());
    //         Guard::redirect('/asdos/absensi');
    //     }

    //     Absensi::update($absensiId, [
    //         'tanggal'         => $tanggal,
    //         'pertemuan_ke'    => $pertemuanKe,
    //         'jam_mulai'       => $jamMulai,
    //         'jam_selesai'     => $jamSelesai,
    //         'deskripsi_tugas' => $deskripsi,
    //         'foto_kegiatan'   => $fotoKegiatanBaru,
    //         'foto_selfie'     => $fotoSelfieBaru,
    //     ]);

    //     // Hapus file foto lama dari disk jika sudah digantikan foto baru
    //     if (!empty($fotoKegiatanBaru)) {
    //         $this->deletePhotoFile($absensi['foto_kegiatan']);
    //     }
    //     if (!empty($fotoSelfieBaru)) {
    //         $this->deletePhotoFile($absensi['foto_selfie']);
    //     }

    //     Guard::setFlash('success', 'Data absensi berhasil diperbarui.');
    //     Guard::redirect('/asdos/absensi');
    // }

    // /** Hapus Absensi (hanya selama status masih 'pending' - BR4) */
    // public function deleteAbsensi(string $id): void
    // {
    //     Guard::requireRole('asdos');
    //     Guard::requireActiveAccount();
    //     Guard::verifyCsrf();

    //     $absensiId = (int)$id;
    //     $asdosId   = Guard::id();

    //     $absensi = Absensi::findByIdForAsdos($absensiId, $asdosId);
    //     if (!$absensi) {
    //         Guard::setFlash('error', 'Data absensi tidak ditemukan atau bukan milik Anda.');
    //         Guard::redirect('/asdos/absensi');
    //     }

    //     if ($absensi['status_verifikasi'] !== 'pending') {
    //         Guard::setFlash('error', 'Absensi yang sudah diverifikasi (disetujui/ditolak) tidak dapat dihapus.');
    //         Guard::redirect('/asdos/absensi');
    //     }

    //     Absensi::delete($absensiId);

    //     $this->deletePhotoFile($absensi['foto_kegiatan']);
    //     $this->deletePhotoFile($absensi['foto_selfie']);

    //     Guard::setFlash('success', 'Data absensi berhasil dihapus.');
    //     Guard::redirect('/asdos/absensi');
    // }

    // =========================================================================
    // MODUL: HISTORY (F6) - SELALU bisa diakses, termasuk saat akun nonaktif
    // =========================================================================

    public function history(): void
    {
        Guard::requireRole('asdos');
        // Sengaja TIDAK memanggil Guard::requireActiveAccount() di sini.
        // F6 & BR6 PRD: riwayat absensi asdos wajib tetap bisa diakses meski akun dinonaktifkan.

        $currentUser = Guard::user();
        $asdosId     = Guard::id();

        $filters = [
            'matkul_id'         => $_GET['matkul'] ?? '',
            'status_verifikasi' => $_GET['status'] ?? '',
            'date_start'        => $_GET['start'] ?? '',
            'date_end'          => $_GET['end'] ?? '',
        ];

        $metrics       = Absensi::getMetricsByAsdos($asdosId);
        $absensiList   = Absensi::getByAsdos($asdosId, $filters);
        $matkulOptions = Plotting::getAllForAsdos($asdosId);

        require_once __DIR__ . '/../Views/Asdos/history.php';
    }

    // =========================================================================
    // HELPER: UPLOAD & VALIDASI FOTO (Keamanan §9 PRD)
    // =========================================================================

    /**
     * Validasi & simpan 1 file foto ke disk dengan nama acak.
     * Mengembalikan null jika tidak ada file baru diunggah (dipakai saat mode edit).
     * Melempar RuntimeException (pesan Bahasa Indonesia) jika file tidak valid.
     */
    private function handleImageUpload(?array $file, string $label): ?string
    {
        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Gagal mengunggah {$label}: terjadi kesalahan saat proses unggah.");
        }

        if ($file['size'] <= 0) {
            throw new \RuntimeException("File {$label} kosong atau rusak.");
        }

        if ($file['size'] > self::MAX_UPLOAD_SIZE) {
            throw new \RuntimeException("Ukuran {$label} melebihi batas maksimal 2 MB.");
        }

        // Validasi MIME asli file (bukan hanya ekstensi) menggunakan finfo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_MIME[$detectedMime])) {
            throw new \RuntimeException("Format {$label} tidak didukung. Hanya menerima gambar JPG, PNG, atau WEBP.");
        }

        $ext = self::ALLOWED_MIME[$detectedMime];
        $randomName = bin2hex(random_bytes(16)) . '.' . $ext;

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        $destination = self::UPLOAD_DIR . $randomName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException("Gagal menyimpan {$label} ke server. Silakan coba lagi.");
        }

        return $randomName;
    }

    /** Hapus file foto lama dari disk (dipanggil saat foto diganti atau data dihapus) */
    private function deletePhotoFile(?string $filename): void
    {
        if (empty($filename)) {
            return;
        }

        $path = self::UPLOAD_DIR . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}