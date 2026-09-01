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
    private const MAX_UPLOAD_SIZE = 10 * 1024 * 1024; // 10 MB (Mendukung foto kamera sensor asli HP)
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function dashboard(): void
    {
        Guard::requireRole('asdos');

        $currentUser = Guard::user();
        $asdosId     = Guard::id();
        $metrics     = Absensi::getMetricsByAsdos($asdosId);

        // Ambil seluruh plotting penugasan mata kuliah milik asdos ini
        $plottingList = Plotting::getAllForAsdos($asdosId);

        // Ambil seluruh riwayat absensi asdos ini
        $allAbsensi = Absensi::getByAsdos($asdosId);

        // Kelompokkan riwayat absensi per plotting_id
        $absensiByPlotting = [];
        foreach ($allAbsensi as $abs) {
            $absensiByPlotting[$abs['plotting_id']][] = $abs;
        }

        foreach ($plottingList as &$p) {
            $p['absensi_list']  = $absensiByPlotting[$p['id_plotting']] ?? [];
            $p['total_absensi'] = count($p['absensi_list']);
        }
        unset($p);

        require_once __DIR__ . '/../Views/Asdos/dashboard.php';
    }

    // =========================================================================
    // LEGACY ROUTES: DIALIHKAN KE DASHBOARD SINGLE-PAGE
    // =========================================================================

    public function matkul(): void
    {
        Guard::redirect('/asdos/dashboard');
    }

    public function absensi(): void
    {
        Guard::redirect('/asdos/dashboard');
    }

    /** Tambah Absensi Baru (Dipanggil via Modal di Single-Page Dashboard) */
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
        $redirectTo  = $_POST['redirect_to'] ?? '/asdos/dashboard';

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
            Guard::redirect($redirectTo);
        }

        // BR1: Validasi kepemilikan - plotting harus AKTIF dan benar milik asdos yang sedang login
        $plotting = Plotting::findActiveForAsdos($plottingId, $asdosId);
        if (!$plotting) {
            Guard::setFlash('error', 'Mata kuliah yang dipilih tidak valid, atau Anda tidak (lagi) terplot aktif pada mata kuliah tersebut.');
            Guard::redirect($redirectTo);
        }

        if ($jamMulai !== '' && $jamSelesai !== '' && $jamSelesai <= $jamMulai) {
            Guard::setFlash('error', 'Jam selesai harus lebih besar dari jam mulai.');
            Guard::redirect($redirectTo);
        }

        // BR7: Foto bukti wajib diambil langsung dari kamera saat submit
        $fotoKegiatan = null;
        $fotoSelfie   = null;

        try {
            $fotoKegiatan = $this->handleImageUpload($_FILES['foto_kegiatan'] ?? null, 'foto kegiatan');
            $fotoSelfie   = $this->handleImageUpload($_FILES['foto_selfie'] ?? null, 'foto selfie');
        } catch (\RuntimeException $e) {
            Guard::setFlash('error', $e->getMessage());
            Guard::redirect($redirectTo);
        }

        if (empty($fotoKegiatan) || empty($fotoSelfie)) {
            Guard::setFlash('error', 'Foto kegiatan dan foto selfie wajib diambil langsung melalui kamera sebagai bukti pelaksanaan (BR7).');
            Guard::redirect($redirectTo);
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

        Guard::setFlash('success', "Presensi pertemuan ke-{$pertemuanKe} untuk [{$plotting['nama_matkul']}] berhasil dikirim dan menunggu verifikasi dosen.");
        Guard::redirect($redirectTo);
    }

    // =========================================================================
    // MODUL: HISTORY (F6) - Dialihkan ke Single-Page Workspace
    // =========================================================================

    public function history(): void
    {
        Guard::redirect('/asdos/dashboard');
    }

    // ==========================================================================
    // HELPER: UPLOAD & VALIDASI FOTO (Keamanan §9 PRD)
    // =========================================================================

    /**
     * Validasi & proses 1 file foto: Koreksi rotasi, stempel watermark Time & Date murni via PHP GD, dan simpan ke disk.
     * Mengembalikan nama file acak (.jpg) atau null jika tidak ada file.
     */
    private function handleImageUpload(?array $file, string $label): ?string
    {
        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Gagal mengunggah {$label}: terjadi kesalahan saat proses unggah (Error code: {$file['error']}).");
        }

        if ($file['size'] <= 0) {
            throw new \RuntimeException("File {$label} kosong atau rusak.");
        }

        if ($file['size'] > self::MAX_UPLOAD_SIZE) {
            throw new \RuntimeException("Ukuran {$label} melebihi batas maksimal 10 MB.");
        }

        // Validasi MIME asli file menggunakan finfo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_MIME[$detectedMime])) {
            throw new \RuntimeException("Format {$label} tidak didukung. Hanya menerima format gambar JPG, PNG, atau WEBP.");
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        $randomName  = bin2hex(random_bytes(16)) . '.jpg';
        $destination = self::UPLOAD_DIR . $randomName;

        // Sematkan watermark waktu & tanggal murni ke file foto
        if (!$this->applyTimestampWatermark($file['tmp_name'], $detectedMime, $destination)) {
            throw new \RuntimeException("Gagal memproses dan menyimpan {$label}. Silakan coba lagi.");
        }

        return $randomName;
    }

    /**
     * Menyematkan watermark Time & Date murni pada foto menggunakan PHP GD
     */
    private function applyTimestampWatermark(string $sourcePath, string $mimeType, string $destinationPath): bool
    {
        if (!extension_loaded('gd')) {
            return move_uploaded_file($sourcePath, $destinationPath) || copy($sourcePath, $destinationPath);
        }

        // 1. Buat image resource sesuai format
        $image = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png'  => @imagecreatefrompng($sourcePath),
            'image/webp' => @imagecreatefromwebp($sourcePath),
            default      => null,
        };

        if (!$image) {
            return move_uploaded_file($sourcePath, $destinationPath) || copy($sourcePath, $destinationPath);
        }

        // 2. Koreksi orientasi EXIF (jika foto diambil vertikal/portrait di kamera HP)
        if ($mimeType === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                $image = match ((int)$exif['Orientation']) {
                    3 => imagerotate($image, 180, 0),
                    6 => imagerotate($image, -90, 0),
                    8 => imagerotate($image, 90, 0),
                    default => $image
                };
            }
        }

        $width  = imagesx($image);
        $height = imagesy($image);

        // 3. Format Time & Date Murni (Contoh: 30-08-2026 18:15:00 WIB)
        $watermarkText = date('d-m-Y H:i:s') . ' WIB';

        // 4. Cari font TTF yang tersedia di sistem
        $fontFile = null;
        $possibleFonts = [
            'C:/Windows/Fonts/arialbd.ttf',
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/segoeui.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf'
        ];
        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                $fontFile = $font;
                break;
            }
        }

        $paddingX = (int) max(14, round($width * 0.015));
        $paddingY = (int) max(8, round($height * 0.012));
        $margin   = (int) max(16, round($width * 0.02));

        if ($fontFile && function_exists('imagettftext')) {
            $fontSize   = (float) max(14, round($height * 0.028));
            $bbox       = imagettfbbox($fontSize, 0, $fontFile, $watermarkText);
            $textWidth  = abs($bbox[4] - $bbox[0]);
            $textHeight = abs($bbox[5] - $bbox[1]);

            $badgeWidth  = $textWidth + ($paddingX * 2);
            $badgeHeight = $textHeight + ($paddingY * 2);
            $badgeX      = $margin;
            $badgeY      = $height - $badgeHeight - $margin;

            // Background Badge Hitam Semi-Transparan Rapi
            imagealphablending($image, true);
            imagesavealpha($image, true);
            $bgBadge = imagecolorallocatealpha($image, 15, 23, 42, 35);
            imagefilledrectangle($image, (int)$badgeX, (int)$badgeY, (int)($badgeX + $badgeWidth), (int)($badgeY + $badgeHeight), $bgBadge);

            // Teks Putih Bersih
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $textX     = $badgeX + $paddingX;
            $textY     = $badgeY + $paddingY + $textHeight;
            imagettftext($image, $fontSize, 0, (int)$textX, (int)$textY, $textColor, $fontFile, $watermarkText);
        } else {
            // Fallback GD Built-in Font (Font 5)
            $font       = 5;
            $fontWidth  = imagefontwidth($font);
            $fontHeight = imagefontheight($font);
            $textWidth  = strlen($watermarkText) * $fontWidth;
            $textHeight = $fontHeight;

            $badgeWidth  = $textWidth + ($paddingX * 2);
            $badgeHeight = $textHeight + ($paddingY * 2);
            $badgeX      = $margin;
            $badgeY      = $height - $badgeHeight - $margin;

            $bgBadge = imagecolorallocatealpha($image, 15, 23, 42, 35);
            imagefilledrectangle($image, (int)$badgeX, (int)$badgeY, (int)($badgeX + $badgeWidth), (int)($badgeY + $badgeHeight), $bgBadge);

            $textColor = imagecolorallocate($image, 255, 255, 255);
            imagestring($image, $font, (int)($badgeX + $paddingX), (int)($badgeY + $paddingY), $watermarkText, $textColor);
        }

        // 5. Simpan gambar hasil watermark ke JPEG berkualitas tinggi (92%)
        $saved = imagejpeg($image, $destinationPath, 92);
        imagedestroy($image);

        return $saved;
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