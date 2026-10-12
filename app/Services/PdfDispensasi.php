<?php

namespace App\Services;

use TCPDF;
use App\Models\Setting;

class PdfDispensasi
{
    private static array $namaHari = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    private static array $namaBulan = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public static function formatTanggalIndo(string $dateStr): string
    {
        $time = strtotime($dateStr);
        if (!$time) {
            return $dateStr;
        }
        $d = date('j', $time);
        $m = (int)date('n', $time);
        $y = date('Y', $time);
        $bulan = self::$namaBulan[$m] ?? '';
        return "{$d} {$bulan} {$y}";
    }

    public static function getHariIndo(string $dateStr): string
    {
        $time = strtotime($dateStr);
        if (!$time) {
            return '-';
        }
        $dayOfWeek = (int)date('w', $time);
        return self::$namaHari[$dayOfWeek] ?? '-';
    }

    public static function formatJam(?string $timeStr = null): string
    {
        if (empty($timeStr)) {
            return '';
        }
        $parts = explode(':', $timeStr);
        if (count($parts) >= 2) {
            return $parts[0] . '.' . $parts[1];
        }
        return $timeStr;
    }

    /**
     * Render Kop Surat Resmi pada halaman aktif TCPDF
     */
    private static function renderKopSurat(TCPDF $pdf, array $settings): void
    {
        $institusiNama     = $settings['institusi_nama'] ?? 'UNIVERSITAS PEMBANGUNAN NASIONAL “VETERAN” JAWA TIMUR';
        $institusiProdi    = $settings['institusi_prodi'] ?? $settings['institusi_himpunan'] ?? 'PROGRAM STUDI SISTEM INFORMASI';
        $institusiFakultas = $settings['institusi_fakultas'] ?? 'FAKULTAS ILMU KOMPUTER';
        $institusiAlamat   = $settings['institusi_alamat'] ?? 'Jalan Raya Rungkut Madya, Gunung Anyar, Surabaya, 60294';
        $institusiEmail    = $settings['institusi_email'] ?? 'fasilkom@upnjatim.ac.id';
        $institusiWebsite  = $settings['institusi_website'] ?? 'fasilkom.upnjatim.ac.id';

        // Path logo
        $logoUpnPath = dirname(__DIR__, 2) . '/public/assets/img/logo_upn.png';
        $logoSiPng   = dirname(__DIR__, 2) . '/public/assets/img/logo_SI.png';
        $logoSiJpg   = dirname(__DIR__, 2) . '/public/assets/img/logo_SI.jpeg';
        $logoSiPath  = file_exists($logoSiPng) ? $logoSiPng : $logoSiJpg;

        $hasLogoUpn = file_exists($logoUpnPath);
        $hasLogoSi  = file_exists($logoSiPath);

        // Logo UPN (Kiri Kop: X=20, Y=12, W=20)
        if ($hasLogoUpn) {
            $pdf->Image($logoUpnPath, 20, 12, 20, 0, 'PNG');
        }

        // Logo Lab SI (Kanan Kop: X=170, Y=12, W=20)
        if ($hasLogoSi) {
            $ext = strtoupper(pathinfo($logoSiPath, PATHINFO_EXTENSION));
            $type = ($ext === 'PNG') ? 'PNG' : 'JPEG';
            $pdf->Image($logoSiPath, 170, 12, 20, 0, $type);
        }

        // Teks Kop Surat di tengah
        $pdf->SetY(12);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(0, 4.8, $institusiNama, 0, 1, 'C');

        $pdf->SetFont('times', 'B', 10.5);
        $pdf->Cell(0, 4.4, $institusiProdi, 0, 1, 'C');
        $pdf->Cell(0, 4.4, $institusiFakultas, 0, 1, 'C');

        $pdf->SetFont('times', '', 8.5);
        $pdf->Cell(0, 4, $institusiAlamat, 0, 1, 'C');

        $pdf->SetFont('times', '', 8);
        $contactHtml = 'Email: <span style="color:#000000;">' . htmlspecialchars($institusiEmail) . '</span> &nbsp;Laman: <span style="color:#000000;">' . htmlspecialchars($institusiWebsite) . '</span>';
        $pdf->writeHTMLCell(0, 3.8, '', '', '<div style="text-align:center;">' . $contactHtml . '</div>', 0, 1);

        // Garis Pembatas Kop Surat (Ganda: tebal atas 0.6mm, tipis bawah 0.2mm)
        // Pastikan garis berada aman di bawah teks dan di bawah logo (minimal Y=35mm)
        $lineY = max($pdf->GetY() + 2, 35);
        $pdf->SetLineWidth(0.6);
        $pdf->Line(20, $lineY, 190, $lineY);
        $pdf->SetLineWidth(0.2);
        $pdf->Line(20, $lineY + 0.8, 190, $lineY + 0.8);

        // Spasi setelah kop
        $pdf->SetY($lineY + 4);
    }

    /**
     * Generate PDF Surat Dispensasi Resmi (2 Halaman) dan output ke browser / download stream
     */
    public static function stream(array $dispensasi, string $mode = 'I'): string
    {
        $settings = Setting::allMap();

        // Default setting fallbacks
        $institusiNama  = $settings['institusi_nama'] ?? 'UNIVERSITAS PEMBANGUNAN NASIONAL “VETERAN” JAWA TIMUR';
        $institusiKota  = $settings['institusi_kota'] ?? 'Surabaya';
        $kaprodiNama    = $settings['kaprodi_nama'] ?? 'Siti Mukaromah, S.Kom, M.Kom';
        $kaprodiNip     = $settings['kaprodi_nip'] ?? '19810704 2021212 011';
        $kaprodiJabatan = $settings['kaprodi_jabatan'] ?? 'Koordinator Program Studi Sistem Informasi';

        // Konfigurasi TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Metadata Dokumen
        $pdf->SetCreator('Sistem Informasi Absensi Lab SI');
        $pdf->SetAuthor($institusiNama);
        $pdf->SetTitle('Surat Keterangan Dispensasi - ' . ($dispensasi['nomor_surat'] ?? ''));
        $pdf->SetSubject('Surat Keterangan Dispensasi Asisten Laboratorium');

        // Nonaktifkan header & footer default TCPDF
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Margin halaman
        $pdf->SetMargins(20, 15, 20);
        $pdf->SetAutoPageBreak(true, 15);

        // =========================================================================
        // HALAMAN 1: SURAT PENGANTAR DISPENSASI RESMI
        // =========================================================================
        $pdf->AddPage();
        self::renderKopSurat($pdf, $settings);

        // 1. TANGGAL SURAT (Kanan Atas)
        $tanggalSurat = self::formatTanggalIndo($dispensasi['created_at'] ?? date('Y-m-d'));
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 5, "{$institusiKota}, {$tanggalSurat}", 0, 1, 'R');
        $pdf->Ln(1);

        // 2. NOMOR, LAMPIRAN & HAL
        $nomorSurat  = htmlspecialchars($dispensasi['nomor_surat'] ?? '-');
        $tujuanSurat = htmlspecialchars($dispensasi['tujuan_surat'] ?? '-');

        $metaHtml = '
        <table cellpadding="1.5" cellspacing="0" border="0" style="font-family:times;font-size:10pt;">
            <tr>
                <td width="70">Nomor</td>
                <td width="15">:</td>
                <td width="415">' . $nomorSurat . '</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>1 (satu) lembar</td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td><b>Surat Keterangan Dispensasi</b></td>
            </tr>
        </table>';
        $pdf->writeHTML($metaHtml, true, false, false, false, '');
        $pdf->Ln(2);

        // 3. TUJUAN SURAT
        $pdf->SetFont('times', '', 10);
        $tujuanLines = explode("\n", trim($tujuanSurat));
        $pdf->Write(5, "Yth. ");
        $pdf->SetFont('times', 'B', 10);
        $pdf->Write(5, $tujuanLines[0] ?? '');
        $pdf->Ln(5);

        $pdf->SetFont('times', '', 10);
        for ($i = 1; $i < count($tujuanLines); $i++) {
            $line = trim($tujuanLines[$i]);
            if (!empty($line)) {
                $pdf->Cell(0, 5, $line, 0, 1, 'L');
            }
        }
        $pdf->Cell(0, 5, 'di tempat', 0, 1, 'L');
        $pdf->Ln(3);

        // 4. SALAM PEMBUKA
        $pdf->SetFont('times', 'I', 10);
        $pdf->Cell(0, 5, "Assalamu’alaikum warahmatullahi wabarakatuh", 0, 1, 'L');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 5, "Dengan hormat,", 0, 1, 'L');
        $pdf->Ln(1.5);

        // 5. ISI SURAT
        $jamMulaiRaw   = $dispensasi['jam_mulai'] ?? null;
        $jamSelesaiRaw = $dispensasi['jam_selesai'] ?? null;
        $namaMatkulRaw = $dispensasi['nama_matkul'] ?? null;

        // Ambil data plotting dan mata kuliah terkait jika belum lengkap di array
        if ((empty($jamMulaiRaw) || empty($jamSelesaiRaw) || empty($namaMatkulRaw)) && !empty($dispensasi['plotting_id'])) {
            try {
                $plotData = \Core\Database::fetch(
                    "SELECT m.nama_matkul, m.jam_mulai, m.jam_selesai 
                     FROM plotting p 
                     JOIN mata_kuliah m ON p.matkul_id = m.id_matkul 
                     WHERE p.id_plotting = :pid LIMIT 1",
                    ['pid' => (int)$dispensasi['plotting_id']]
                );
                if ($plotData) {
                    if (empty($jamMulaiRaw) && !empty($plotData['jam_mulai'])) {
                        $jamMulaiRaw = $plotData['jam_mulai'];
                    }
                    if (empty($jamSelesaiRaw) && !empty($plotData['jam_selesai'])) {
                        $jamSelesaiRaw = $plotData['jam_selesai'];
                    }
                    if (empty($namaMatkulRaw) && !empty($plotData['nama_matkul'])) {
                        $namaMatkulRaw = $plotData['nama_matkul'];
                    }
                }
            } catch (\Throwable $e) {
                // Ignore fallback error
            }
        }

        $namaMatkul = htmlspecialchars($namaMatkulRaw ?? '-');
        $hari       = self::getHariIndo($dispensasi['tanggal_pelaksanaan'] ?? '');
        $tglPelaks  = self::formatTanggalIndo($dispensasi['tanggal_pelaksanaan'] ?? '');

        // Format waktu pelaksanaan sesuai jadwal plotting mata kuliah yang terkait
        $jamMulaiFmt   = self::formatJam($jamMulaiRaw);
        $jamSelesaiFmt = self::formatJam($jamSelesaiRaw);

        if (!empty($jamMulaiFmt) && !empty($jamSelesaiFmt)) {
            $waktuTeks = "{$jamMulaiFmt} - {$jamSelesaiFmt} WIB";
        } elseif (!empty($jamMulaiFmt)) {
            $waktuTeks = "{$jamMulaiFmt} WIB - Selesai";
        } else {
            $waktuTeks = "Sesuai Jadwal Perkuliahan";
        }

        $tempat = htmlspecialchars($dispensasi['tempat_pelaksanaan'] ?? '-');

        // Redaksi dinamis sesuai pilihan asdos: magang atau perkuliahan
        $jenisKegiatan = strtolower($dispensasi['jenis_kegiatan'] ?? 'magang');
        $kegiatanDitinggalkan = ($jenisKegiatan === 'perkuliahan') ? 'kegiatan perkuliahan' : 'kegiatan magang';

        $isiHtml = '
        <p style="text-align:justify;text-indent:25px;margin:0;line-height:1.45;font-family:times;font-size:10pt;">
            Sehubungan dengan pelaksanaan kegiatan Asistensi Dosen yang diselenggarakan oleh Laboratorium Sistem Informasi Fakultas Ilmu Komputer UPN &ldquo;Veteran&rdquo; Jawa Timur, kegiatan tersebut akan dilaksanakan pada:
        </p>
        <table cellpadding="2" cellspacing="0" border="0" style="margin-left:25px;font-family:times;font-size:10pt;">
            <tr>
                <td width="30"></td>
                <td width="90">Mata Kuliah</td>
                <td width="15">:</td>
                <td width="365"><b>' . $namaMatkul . '</b></td>
            </tr>
            <tr>
                <td></td>
                <td>Hari</td>
                <td>:</td>
                <td>' . $hari . '</td>
            </tr>
            <tr>
                <td></td>
                <td>Tanggal</td>
                <td>:</td>
                <td>' . $tglPelaks . '</td>
            </tr>
            <tr>
                <td></td>
                <td>Waktu</td>
                <td>:</td>
                <td>' . $waktuTeks . '</td>
            </tr>
            <tr>
                <td></td>
                <td>Tempat</td>
                <td>:</td>
                <td>' . $tempat . '</td>
            </tr>
        </table>
        <p style="text-align:justify;text-indent:25px;margin:0;line-height:1.45;font-family:times;font-size:10pt;">
            Bersama surat ini, kami memohonkan izin bagi mahasiswa dengan identitas terlampir untuk tidak mengikuti ' . $kegiatanDitinggalkan . ' pada jadwal tersebut dikarenakan menghadiri kegiatan dimaksud.
        </p>
        <p style="text-align:justify;text-indent:25px;margin:0;line-height:1.45;font-family:times;font-size:10pt;">
            Demikian surat permohonan izin ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.
        </p>
        <p style="font-style:italic;margin:0;font-family:times;font-size:10pt;">
            Wassalamu’alaikum warahmatullahi wabarakatuh
        </p>';

        $pdf->writeHTML($isiHtml, true, false, false, false, '');
        $pdf->Ln(2);

        // 6. BLOK TANDA TANGAN (DUA KOLOM SESUAI TEMPLATE DOCX)
        $namaDosen = htmlspecialchars($dispensasi['nama_dosen'] ?? 'Dosen Pengampu');
        $nipDosen  = htmlspecialchars($dispensasi['nip_dosen'] ?? '-');

        $ttdHtml = '
        <table cellpadding="0" cellspacing="0" border="0" style="font-family:times;font-size:10pt;width:100%;">
            <tr>
                <td width="100%" align="center" style="padding-bottom:5px;">Hormat Kami,</td>
            </tr>
            <tr>
                <td width="48%" align="center">
                    <b>Dosen Mata Kuliah</b><br><br><br><br><br>
                    <u><b>' . $namaDosen . '</b></u><br>
                    NIP. ' . $nipDosen . '
                </td>
                <td width="4%"></td>
                <td width="48%" align="center">
                    <b>Koordinator Program Studi<br>Sistem Informasi</b><br><br><br><br>
                    <u><b>' . htmlspecialchars($kaprodiNama) . '</b></u><br>
                    NIP. ' . htmlspecialchars($kaprodiNip) . '
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($ttdHtml, true, false, false, false, '');

        // =========================================================================
        // HALAMAN 2: LAMPIRAN 1 (IDENTITAS ASISTEN DOSEN)
        // =========================================================================
        $pdf->AddPage();
        self::renderKopSurat($pdf, $settings);

        $pdf->SetFont('times', 'BU', 11);
        $pdf->Cell(0, 6, 'Lampiran 1', 0, 1, 'L');
        $pdf->SetFont('times', '', 10);
        $pdf->Ln(2);

        $namaAsdos  = htmlspecialchars($dispensasi['nama_asdos'] ?? '-');
        $npmAsdos   = htmlspecialchars($dispensasi['npm_asdos'] ?? '-');
        $prodiAsdos = 'Sistem Informasi';

        $lampiranHtml = '
        <table cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;font-family:times;font-size:10pt;">
            <thead>
                <tr style="background-color:#f1f5f9;font-weight:bold;text-align:center;">
                    <th width="6%" align="center" style="font-weight:bold;">No</th>
                    <th width="34%" align="center" style="font-weight:bold;">Nama Mahasiswa</th>
                    <th width="20%" align="center" style="font-weight:bold;">NPM</th>
                    <th width="20%" align="center" style="font-weight:bold;">Program Studi</th>
                    <th width="20%" align="center" style="font-weight:bold;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="6%" align="center">1</td>
                    <td width="34%" align="left"> ' . $namaAsdos . '</td>
                    <td width="20%" align="center">' . $npmAsdos . '</td>
                    <td width="20%" align="center">' . $prodiAsdos . '</td>
                    <td width="20%" align="center">Asisten Laboratorium</td>
                </tr>
            </tbody>
        </table>';

        $pdf->writeHTML($lampiranHtml, true, false, false, false, '');

        // Output PDF
        $cleanFileName = 'Surat_Dispensasi_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $dispensasi['nomor_surat'] ?? 'surat') . '.pdf';
        return (string)$pdf->Output($cleanFileName, $mode);
    }
}
