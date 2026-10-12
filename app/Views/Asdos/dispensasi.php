<?php
// Fallback & Dokumentasi Variabel dari DispensasiController
$currentUser    = $currentUser ?? \Core\Guard::user();
$isActive       = (int)($currentUser['is_active'] ?? 0) === 1;
$metrics        = $metrics ?? ['total' => 0, 'disetujui' => 0, 'menunggu' => 0, 'ditolak' => 0];
$plottingList   = $plottingList ?? [];
$dispensasiList = $dispensasiList ?? [];
$nomorSuratNext = $nomorSuratNext ?? '-';

$statusBadge = static function (string $status): string {
    return match ($status) {
        'disetujui' => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-emerald-50 text-emerald-800 border-emerald-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disetujui</span>',
        'ditolak'   => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-rose-50 text-rose-800 border-rose-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Ditolak</span>',
        default     => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-amber-50 text-amber-800 border-amber-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Menunggu Approval</span>',
    };
};
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Dispensasi Mengajar — Asisten Dosen</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

    <!-- Top Popup Notifications -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Sidebar -->
    <?php require_once __DIR__ . '/../Templates/asdos_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 space-y-6">

            <!-- Page Header Banner -->
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-blue-50 text-[#1867c0] border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Surat Keterangan Dispensasi</h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pengajuan izin dispensasi mengajar asistensi laboratorium untuk kegiatan luar / magang</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <?php if ($isActive): ?>
                        <button type="button" onclick="openModalAjukan()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#1867c0] hover:bg-[#1456a0] text-white text-xs sm:text-sm font-semibold shadow-sm hover:shadow-md transition active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Ajukan Surat Baru</span>
                        </button>
                    <?php else: ?>
                        <div class="px-3 py-2 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs font-medium">
                            Mode Lihat Saja (Akun Nonaktif)
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Diajukan</span>
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-[#1867c0] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2"><?= $metrics['total'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Keseluruhan pengajuan surat</p>
                </div>

                <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Menunggu</span>
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-amber-700 mt-2"><?= $metrics['menunggu'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Perlu persetujuan Dosen</p>
                </div>

                <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Disetujui</span>
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-700 mt-2"><?= $metrics['disetujui'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Surat siap dicetak / PDF</p>
                </div>

                <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Ditolak</span>
                        <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-rose-700 mt-2"><?= $metrics['ditolak'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Lihat catatan penolakan</p>
                </div>
            </div>

            <!-- Data Table Section -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Riwayat Pengajuan Surat</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar permohonan surat dispensasi Anda</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                        <?= count($dispensasiList) ?> Pengajuan
                    </span>
                </div>

                <?php if (empty($dispensasiList)): ?>
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Belum Ada Pengajuan Surat</h3>
                        <p class="text-xs sm:text-sm text-slate-400 max-w-md mx-auto mt-1">
                            Anda belum pernah mengajukan surat dispensasi. Klik tombol di bawah untuk membuat pengajuan baru.
                        </p>
                        <?php if ($isActive): ?>
                            <button type="button" onclick="openModalAjukan()"
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#1867c0] text-white text-xs font-semibold shadow-xs hover:bg-[#1456a0] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Ajukan Surat Sekarang</span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-4">Nomor & Tanggal</th>
                                    <th class="py-3.5 px-4">Mata Kuliah & Dosen</th>
                                    <th class="py-3.5 px-4">Jadwal & Tempat</th>
                                    <th class="py-3.5 px-4">Tujuan Surat</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4 text-center">Aksi / PDF</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($dispensasiList as $d): ?>
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-4 align-top">
                                            <span class="font-mono font-bold text-[#1867c0] bg-blue-50/80 px-2 py-0.5 rounded border border-blue-100 text-[11px] inline-block mb-1">
                                                <?= htmlspecialchars($d['nomor_surat'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <div class="text-[11px] text-slate-400">
                                                Diajukan: <?= date('d M Y', strtotime($d['created_at'])) ?>
                                            </div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold <?= ($d['jenis_kegiatan'] ?? 'magang') === 'perkuliahan' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-[#1867c0] border border-blue-200' ?>">
                                                    <?= ($d['jenis_kegiatan'] ?? 'magang') === 'perkuliahan' ? 'Izin Perkuliahan' : 'Izin Magang' ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-900">
                                                <?= htmlspecialchars($d['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="text-[11px] text-slate-500 mt-0.5">
                                                Dosen: <?= htmlspecialchars($d['nama_dosen'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-semibold text-slate-800">
                                                <?= \App\Services\PdfDispensasi::getHariIndo($d['tanggal_pelaksanaan']) ?>, <?= \App\Services\PdfDispensasi::formatTanggalIndo($d['tanggal_pelaksanaan']) ?>
                                            </div>
                                            <div class="text-[11px] text-slate-500">
                                                <?= \App\Services\PdfDispensasi::formatJam($d['jam_mulai'] ?? '') ?> - <?= \App\Services\PdfDispensasi::formatJam($d['jam_selesai'] ?? '') ?> WIB
                                            </div>
                                            <div class="text-[11px] text-slate-600 mt-1 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate max-w-[180px]" title="<?= htmlspecialchars($d['tempat_pelaksanaan'], ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= htmlspecialchars($d['tempat_pelaksanaan'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="text-slate-800 font-medium max-w-[220px] whitespace-pre-line">
                                                <?= htmlspecialchars($d['tujuan_surat'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 align-top">
                                            <?= $statusBadge($d['status']) ?>
                                            <?php if ($d['status'] === 'ditolak' && !empty($d['catatan_dosen'])): ?>
                                                <div class="mt-2 p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-[11px] max-w-[220px]">
                                                    <strong class="font-bold">Catatan Dosen:</strong>
                                                    <p class="mt-0.5 whitespace-pre-line"><?= htmlspecialchars($d['catatan_dosen'], ENT_QUOTES, 'UTF-8') ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3.5 px-4 align-top text-center">
                                            <?php if ($d['status'] === 'disetujui'): ?>
                                                <a href="<?= \Core\Guard::url('/asdos/dispensasi/' . $d['id_dispensasi'] . '/download') ?>" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs hover:shadow transition active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    <span>Unduh PDF</span>
                                                </a>
                                            <?php elseif ($d['status'] === 'menunggu'): ?>
                                                <span class="text-slate-400 text-[11px] italic">Menunggu dosen...</span>
                                            <?php else: ?>
                                                <span class="text-rose-400 text-[11px] italic">Tidak tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <!-- MODAL FORM PENGAJUAN SURAT DISPENSASI BARU -->
    <div id="modalAjukanDispensasi" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" onclick="closeModalAjukan()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
                
                <!-- Header Modal -->
                <div class="bg-gradient-to-r from-blue-600 to-[#1867c0] px-5 py-4 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-white/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-base font-bold">Ajukan Surat Dispensasi</h3>
                            <p class="text-xs text-blue-100">Lengkapi data tujuan dan tempat pelaksanaan</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModalAjukan()" class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form Body -->
                <form id="formAjukanDispensasi" action="<?= \Core\Guard::url('/asdos/dispensasi/create') ?>" method="POST" class="p-5 sm:p-6 space-y-4">
                    <?= \Core\Guard::csrfField() ?>

                    <!-- Info Nomor Surat Otomatis -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Preview Nomor Surat:</span>
                        <span class="font-mono font-bold text-[#1867c0] bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                            <?= htmlspecialchars($nomorSuratNext, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <!-- Pilih Penugasan Mata Kuliah -->
                    <div>
                        <label for="plotting_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Pilih Mata Kuliah (Plotting Aktif) <span class="text-rose-500">*</span>
                        </label>
                        <?php if (empty($plottingList)): ?>
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                                Anda belum memiliki penugasan mata kuliah aktif. Hubungi Super Admin untuk plotting.
                            </div>
                        <?php else: ?>
                            <select name="plotting_id" id="plotting_id" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] focus:border-[#1867c0] text-xs sm:text-sm bg-white transition">
                                <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                                <?php foreach ($plottingList as $p): ?>
                                    <option value="<?= $p['id_plotting'] ?>" 
                                            data-jam-mulai="<?= $p['jam_mulai'] ?? '' ?>" 
                                            data-jam-selesai="<?= $p['jam_selesai'] ?? '' ?>"
                                            data-dosen="<?= htmlspecialchars($p['nama_dosen'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($p['nama_matkul'], ENT_QUOTES, 'UTF-8') ?> (Dosen: <?= htmlspecialchars($p['nama_dosen'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p id="infoMatkulWaktu" class="text-[11px] text-slate-500 mt-1 hidden"></p>
                        <?php endif; ?>
                    </div>

                    <!-- Jenis Kegiatan yang Ditinggalkan (Magang vs Perkuliahan) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kegiatan yang Ditinggalkan (Izin Dari) <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-[#1867c0] has-[:checked]:bg-blue-50/50">
                                <input type="radio" name="jenis_kegiatan" value="magang" checked class="w-4 h-4 text-[#1867c0] focus:ring-[#1867c0] border-slate-300">
                                <div class="ml-2.5">
                                    <span class="block text-xs font-bold text-slate-800">Kegiatan Magang</span>
                                    <span class="block text-[11px] text-slate-500">Izin tidak mengikuti magang</span>
                                </div>
                            </label>
                            <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-[#1867c0] has-[:checked]:bg-blue-50/50">
                                <input type="radio" name="jenis_kegiatan" value="perkuliahan" class="w-4 h-4 text-[#1867c0] focus:ring-[#1867c0] border-slate-300">
                                <div class="ml-2.5">
                                    <span class="block text-xs font-bold text-slate-800">Kegiatan Perkuliahan</span>
                                    <span class="block text-[11px] text-slate-500">Izin tidak masuk kelas kuliah</span>
                                </div>
                            </label>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Menentukan redaksi resmi kalimat izin pada lembar surat dispensasi.</p>
                    </div>

                    <!-- Tanggal Pelaksanaan -->
                    <div>
                        <label for="tanggal_pelaksanaan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] focus:border-[#1867c0] text-xs sm:text-sm bg-white transition">
                        <p class="text-[11px] text-slate-400 mt-0.5">Hari akan otomatis dihitung berdasarkan tanggal ini.</p>
                    </div>

                    <!-- Tujuan Surat (Input Manual Asdos) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="tujuan_surat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Tujuan Surat (Kepada Siapa) <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-rose-600 font-semibold bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">
                                Input Manual
                            </span>
                        </div>
                        <textarea name="tujuan_surat" id="tujuan_surat" rows="2" required
                            placeholder="Contoh: Kepala Bagian Dinas Pendidikan UPT-PTKK&#10;atau: HRD PT Teknologi Nusantara"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] focus:border-[#1867c0] text-xs sm:text-sm transition resize-none"></textarea>
                        <p class="text-[11px] text-slate-400 mt-0.5">Akan dicetak di bawah "Yth." pada surat dispensasi.</p>
                    </div>

                    <!-- Tempat Pelaksanaan (Input Manual Asdos) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="tempat_pelaksanaan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Tempat Pelaksanaan Mengajar <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-rose-600 font-semibold bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">
                                Input Manual
                            </span>
                        </div>
                        <input type="text" name="tempat_pelaksanaan" id="tempat_pelaksanaan" required
                            placeholder="Contoh: Laboratorium Solusi Sistem Informasi, Gedung Fasilkom Lt. 2"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] focus:border-[#1867c0] text-xs sm:text-sm transition">
                        <p class="text-[11px] text-slate-400 mt-0.5">Lokasi Anda melaksanakan asistensi mengajar.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModalAjukan()"
                            class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" <?= empty($plottingList) ? 'disabled' : '' ?>
                            class="px-5 py-2 rounded-xl bg-[#1867c0] hover:bg-[#1456a0] text-white text-xs font-bold shadow-sm transition active:scale-95 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <?php require_once __DIR__ . '/../Templates/asdos_bottom_nav.php'; ?>

    <script>
        function openModalAjukan() {
            const modal = document.getElementById('modalAjukanDispensasi');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModalAjukan() {
            const modal = document.getElementById('modalAjukanDispensasi');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Tampilkan info waktu saat matkul dipilih
        document.getElementById('plotting_id')?.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const jamMulai = opt.getAttribute('data-jam-mulai');
            const jamSelesai = opt.getAttribute('data-jam-selesai');
            const infoEl = document.getElementById('infoMatkulWaktu');

            if (infoEl && jamMulai && jamSelesai) {
                infoEl.textContent = `Jam perkuliahan: ${jamMulai.substring(0, 5)} - ${jamSelesai.substring(0, 5)} WIB (Otomatis tercetak di surat)`;
                infoEl.classList.remove('hidden');
            } else if (infoEl) {
                infoEl.classList.add('hidden');
            }
        });

        // Validasi Submit Form Pengajuan Dispensasi (Mencegah Modal Tertutup Saat Error)
        document.getElementById('formAjukanDispensasi')?.addEventListener('submit', function(e) {
            const plotting = document.getElementById('plotting_id');
            const tgl = document.getElementById('tanggal_pelaksanaan');
            const tujuan = document.getElementById('tujuan_surat');
            const tempat = document.getElementById('tempat_pelaksanaan');

            if (!plotting || !plotting.value) {
                e.preventDefault();
                window.highlightAndNotify(plotting, 'Silakan pilih mata kuliah penugasan praktikum.');
                return;
            }

            if (!tgl || !tgl.value) {
                e.preventDefault();
                window.highlightAndNotify(tgl, 'Tanggal pelaksanaan asistensi wajib diisi.');
                return;
            }

            if (!tujuan || !tujuan.value.trim() || tujuan.value.trim().length < 5) {
                e.preventDefault();
                window.highlightAndNotify(tujuan, 'Tujuan surat (kepada instansi/pimpinan) wajib diisi minimal 5 karakter.');
                return;
            }

            if (!tempat || !tempat.value.trim() || tempat.value.trim().length < 3) {
                e.preventDefault();
                window.highlightAndNotify(tempat, 'Tempat pelaksanaan mengajar wajib diisi minimal 3 karakter.');
                return;
            }
        });

        // ESC to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalAjukan();
            }
        });
    </script>
</body>

</html>
