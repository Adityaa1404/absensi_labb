<?php
// Fallback & Dokumentasi Variabel dari AsdosController
$currentUser  = $currentUser ?? \Core\Guard::user();
$isActive     = (int)($currentUser['is_active'] ?? 0) === 1;
$metrics      = $metrics ?? [
    'total'     => 0,
    'disetujui' => 0,
    'pending'   => 0,
    'ditolak'   => 0,
];
$plottingList = $plottingList ?? [];

$statusBadge = static function (string $status): string {
    return match ($status) {
        'disetujui' => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-emerald-50 text-emerald-800 border-emerald-300 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disetujui</span>',
        'ditolak'   => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-red-50 text-red-800 border-red-300 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Ditolak</span>',
        default     => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-amber-50 text-amber-800 border-amber-300 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>',
    };
};
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard & Presensi Asdos — Absensi Lab</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

    <!-- Top Popup Notifications (Auto-dismiss 4 detik) -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Sidebar (Unified Desktop Navbar) -->
    <?php require_once __DIR__ . '/../Templates/asdos_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-8 space-y-6">

            <!-- Page Header Banner -->
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <?php if (!$isActive): ?>
                            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-300 rounded-md">
                                Mode Lihat Saja (Akun Nonaktif)
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1.5">
                        Selamat Datang, <?= htmlspecialchars($currentUser['nama'] ?? 'Asdos', ENT_QUOTES, 'UTF-8') ?>!
                    </h1>
                </div>
            </div>

            <!-- Metric Cards (4 Columns) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-4">

                <!-- 1. Total Absensi -->
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Absensi</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= (int)$metrics['total'] ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                </div>

                <!-- 2. Disetujui -->
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-emerald-200/80 bg-emerald-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-emerald-400">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Disetujui</p>
                        <p class="text-2xl sm:text-3xl font-bold text-emerald-800 mt-1"><?= (int)$metrics['disetujui'] ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- 3. Pending -->
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-amber-200/80 bg-amber-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-amber-400">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Menunggu Review</p>
                        <p class="text-2xl sm:text-3xl font-bold text-amber-800 mt-1"><?= (int)$metrics['pending'] ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- 4. Ditolak -->
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-red-200/80 bg-red-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-red-400">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-red-700">Ditolak</p>
                        <p class="text-2xl sm:text-3xl font-bold text-red-800 mt-1"><?= (int)$metrics['ditolak'] ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-red-100 text-red-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>

            </div>

            <!-- Main Course Hub Section -->
            <div class="space-y-4">
                
                <!-- Section Title & Search -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">Mata Kuliah yang Diampu</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar penugasan praktikum aktif dan arsip riwayat penugasan Anda.</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <div class="relative w-full sm:w-64">
                            <input type="text" id="courseSearchInput" placeholder="Cari mata kuliah..." 
                                class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <span id="courseCountBadge" class="px-2.5 py-2 text-xs font-bold bg-white text-slate-700 border border-slate-300 rounded-xl shadow-2xs shrink-0">
                            <?= count($plottingList) ?> Matkul
                        </span>
                    </div>
                </div>

                <!-- Course Grid Cards -->
                <?php if (empty($plottingList)): ?>
                    <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-4 border border-blue-200 shadow-2xs">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Belum Ada Penugasan Mata Kuliah</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                            Anda belum diplotkan ke mata kuliah manapun oleh Super Admin. Silakan hubungi koordinator laboratorium untuk plotting asdos.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="courseGridContainer">
                        <?php foreach ($plottingList as $p): ?>
                            <?php 
                                $isPlotActive = (int)$p['is_active'] === 1;
                                $totalAbsensi = (int)($p['total_absensi'] ?? 0);
                            ?>
                            <div class="course-card bg-white border border-slate-200 rounded-2xl p-5 shadow-xs transition-all duration-200 hover:shadow-md hover:border-slate-300 flex flex-col justify-between"
                                data-nama="<?= htmlspecialchars(strtolower($p['nama_matkul']), ENT_QUOTES, 'UTF-8') ?>"
                                data-dosen="<?= htmlspecialchars(strtolower($p['nama_dosen'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                
                                <div>
                                    <!-- Top Row: Icon & Status -->
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200 text-[#1867c0] font-bold text-sm flex items-center justify-center shadow-2xs">
                                            MK
                                        </div>
                                        <?php if ($isPlotActive): ?>
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 inline-flex items-center gap-1 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-slate-100 text-slate-600 border border-slate-300">
                                                Selesai / Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Course Info -->
                                    <h3 class="font-bold text-slate-900 text-base leading-snug">
                                        <?= htmlspecialchars($p['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                    </h3>

                                    <?php if (!empty($p['deskripsi_matkul'])): ?>
                                        <p class="text-xs text-slate-500 line-clamp-2 mt-1 leading-relaxed">
                                            <?= htmlspecialchars($p['deskripsi_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    <?php endif; ?>

                                    <!-- Dosen Pengampu -->
                                    <div class="mt-3.5 pt-3 border-t border-slate-100 space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-[10px] flex items-center justify-center shrink-0">
                                                D
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-800 truncate">
                                                    <?= htmlspecialchars($p['nama_dosen'] ?? 'Dosen Belum Ditentukan', ENT_QUOTES, 'UTF-8') ?>
                                                </p>
                                            </div>
                                        </div>

                                        <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span><?= date('d M Y', strtotime($p['periode_mulai'])) ?> – <?= date('d M Y', strtotime($p['periode_selesai'])) ?></span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Bottom Action & Stats -->
                                <div class="mt-4 pt-3.5 border-t border-slate-100 flex flex-col gap-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-700 rounded-md border border-slate-200 inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span><?= $totalAbsensi ?> Pertemuan Dicatat</span>
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <!-- Tombol Absen Sekarang -->
                                        <?php if ($isPlotActive && $isActive): ?>
                                            <button type="button" 
                                                onclick="openAbsenModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)"
                                                class="px-3 py-2 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs font-bold rounded-xl transition shadow-xs hover:shadow-md flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>Absen</span>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" disabled class="px-3 py-2 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed flex items-center justify-center gap-1">
                                                <span>Nonaktif</span>
                                            </button>
                                        <?php endif; ?>

                                        <!-- Tombol Lihat Riwayat -->
                                        <button type="button" 
                                            onclick="openRiwayatModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)"
                                            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 active:scale-[0.98] text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            <span>Riwayat</span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: ISI ABSENSI KHUSUS MATA KULIAH                                   -->
    <!-- ========================================================================= -->
    <div id="modalIsiAbsensi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden transition-all transform animate-in fade-in duration-200 my-6">

            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Isi Presensi Praktikum</h3>
                        <p class="text-xs text-slate-500">Kamera live capture &amp; watermark otomatis</p>
                    </div>
                </div>
                <button type="button" onclick="closeAbsenModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form Content -->
            <form action="<?= \Core\Guard::url('/asdos/absensi/create') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto" id="formIsiAbsensi">
                <?= \Core\Guard::csrfField() ?>
                <input type="hidden" name="redirect_to" value="/asdos/dashboard">
                <input type="hidden" name="plotting_id" id="modal_absen_plotting_id" value="">

                <!-- Locked Course Context Banner -->
                <div class="p-3.5 bg-blue-50/60 border border-blue-200 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#1867c0] block">Mata Kuliah Praktikum</span>
                    <p id="modal_absen_matkul_title" class="font-bold text-slate-900 text-sm mt-0.5">-</p>
                    <p id="modal_absen_dosen_title" class="text-xs text-slate-600 mt-0.5">Dosen: -</p>
                </div>

                <!-- Baris: Tanggal & Pertemuan Ke -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="absen_tanggal" class="block text-xs font-bold text-slate-800 mb-1">
                            Tanggal Pelaksanaan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="absen_tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label for="absen_pertemuan_ke" class="block text-xs font-bold text-slate-800 mb-1">
                            Pertemuan Ke- <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="absen_pertemuan_ke" name="pertemuan_ke" min="1" required placeholder="Contoh: 1"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                    </div>
                </div>

                <!-- Baris: Jam Mulai & Jam Selesai -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="absen_jam_mulai" class="block text-xs font-bold text-slate-800 mb-1">
                            Jam Mulai
                        </label>
                        <input type="time" id="absen_jam_mulai" name="jam_mulai" value="<?= date('H:i') ?>"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                    </div>

                    <div>
                        <label for="absen_jam_selesai" class="block text-xs font-bold text-slate-800 mb-1">
                            Jam Selesai
                        </label>
                        <input type="time" id="absen_jam_selesai" name="jam_selesai"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                    </div>
                </div>

                <!-- Deskripsi Kegiatan -->
                <div>
                    <label for="absen_deskripsi_tugas" class="block text-xs font-bold text-slate-800 mb-1">
                        Deskripsi Tugas / Materi Praktikum <span class="text-red-500">*</span>
                    </label>
                    <textarea id="absen_deskripsi_tugas" name="deskripsi_tugas" rows="3" minlength="5" required
                        placeholder="Jelaskan ringkasan materi praktikum, modul yang dipelajari, dan aktivitas pendampingan..."
                        class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition resize-none shadow-2xs"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Minimal 5 karakter.</p>
                </div>

                <!-- Section Bukti Kamera (Native Camera Trigger) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    
                    <!-- 1. Foto Kegiatan (Kamera Belakang) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-800">
                                Foto Kegiatan <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] font-semibold text-[#1867c0] bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                Kamera Belakang
                            </span>
                        </div>

                        <input type="file" name="foto_kegiatan" id="foto_kegiatan" accept="image/*" capture="environment" required class="hidden" onchange="previewNativePhoto(this, 'kegiatan')">

                        <div id="camera_card_kegiatan" class="relative w-full h-44 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/50 hover:border-[#1867c0] hover:bg-white overflow-hidden flex items-center justify-center transition-all duration-200 cursor-pointer group" onclick="triggerNativeCamera('kegiatan')">
                            
                            <!-- Idle State -->
                            <div id="idle_kegiatan" class="text-center p-3 space-y-1.5">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-blue-50 text-[#1867c0] border border-blue-100 flex items-center justify-center group-hover:scale-105 transition shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Ambil Foto Kegiatan</p>
                                <span class="px-2.5 py-1 bg-[#1867c0] text-white text-[11px] font-bold rounded-lg shadow-xs inline-block">Buka Kamera</span>
                            </div>

                            <!-- Preview State -->
                            <div id="preview_kegiatan" class="hidden w-full h-full relative">
                                <img id="img_kegiatan" src="" alt="Preview Kegiatan" class="w-full h-full object-cover">
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-0.5 bg-emerald-600/90 text-white text-[10px] font-bold rounded-md shadow backdrop-blur-xs flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Foto Terpilih
                                    </span>
                                </div>
                                <div class="absolute bottom-2 right-2">
                                    <button type="button" onclick="event.stopPropagation(); triggerNativeCamera('kegiatan')" class="px-2.5 py-1 bg-slate-900/80 hover:bg-slate-900 text-white text-[11px] font-bold rounded-lg transition shadow backdrop-blur-xs inline-flex items-center gap-1 cursor-pointer">
                                        <span>Ambil Ulang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Foto Selfie (Kamera Depan) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-800">
                                Foto Selfie <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                Kamera Depan
                            </span>
                        </div>

                        <input type="file" name="foto_selfie" id="foto_selfie" accept="image/*" capture="user" required class="hidden" onchange="previewNativePhoto(this, 'selfie')">

                        <div id="camera_card_selfie" class="relative w-full h-44 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/50 hover:border-indigo-500 hover:bg-white overflow-hidden flex items-center justify-center transition-all duration-200 cursor-pointer group" onclick="triggerNativeCamera('selfie')">
                            
                            <!-- Idle State -->
                            <div id="idle_selfie" class="text-center p-3 space-y-1.5">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center group-hover:scale-105 transition shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Ambil Foto Selfie</p>
                                <span class="px-2.5 py-1 bg-indigo-600 text-white text-[11px] font-bold rounded-lg shadow-xs inline-block">Buka Kamera</span>
                            </div>

                            <!-- Preview State -->
                            <div id="preview_selfie" class="hidden w-full h-full relative">
                                <img id="img_selfie" src="" alt="Preview Selfie" class="w-full h-full object-cover">
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-0.5 bg-emerald-600/90 text-white text-[10px] font-bold rounded-md shadow backdrop-blur-xs flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Selfie Terpilih
                                    </span>
                                </div>
                                <div class="absolute bottom-2 right-2">
                                    <button type="button" onclick="event.stopPropagation(); triggerNativeCamera('selfie')" class="px-2.5 py-1 bg-slate-900/80 hover:bg-slate-900 text-white text-[11px] font-bold rounded-lg transition shadow backdrop-blur-xs inline-flex items-center gap-1 cursor-pointer">
                                        <span>Ambil Ulang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-500">
                    Kedua foto akan distempel *Watermark Waktu &amp; Tanggal* secara otomatis di server setelah absensi dikirimkan.
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeAbsenModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btn_submit_absensi" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kirim Absensi</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: RIWAYAT ABSENSI KHUSUS MATA KULIAH                               -->
    <!-- ========================================================================= -->
    <div id="modalRiwayatAbsensi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden transition-all transform animate-in fade-in duration-200 my-6">

            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Riwayat Absensi Mata Kuliah</h3>
                    </div>
                </div>
                <button type="button" onclick="closeRiwayatModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Content Area -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                
                <!-- Matkul Context Header Banner -->
                <div class="p-4 bg-indigo-50/40 border border-indigo-200/70 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 block">Mata Kuliah Praktikum</span>
                        <h4 id="modal_riwayat_matkul_title" class="font-bold text-slate-900 text-sm sm:text-base leading-tight mt-0.5">-</h4>
                        <p id="modal_riwayat_dosen_title" class="text-xs text-slate-600 mt-1">Dosen: -</p>
                    </div>
                    <button type="button" id="modal_riwayat_btn_absen_baru" onclick="openAbsenFromRiwayat()" class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs font-bold rounded-xl transition shadow-xs hover:shadow-md cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Isi Absensi Baru</span>
                    </button>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Kehadiran Praktikum</h5>
                    <span id="modal_riwayat_count_badge" class="px-2 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-700 rounded-md border border-slate-200">0 Pertemuan</span>
                </div>

                <!-- Dynamic Absensi Cards Container -->
                <div id="modal_riwayat_list_container" class="space-y-3">
                    <!-- Populated dynamically via JS -->
                </div>

            </div>

            <!-- Footer Modal -->
            <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end">
                <button type="button" onclick="closeRiwayatModal()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: PRATINJAU FOTO BUKTI (LIGHTBOX)                                  -->
    <!-- ========================================================================= -->
    <div id="modalFotoPreview" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeFotoPreview()">
        <div class="max-w-2xl w-full bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl p-2 relative" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-2 text-white border-b border-slate-800">
                <span id="modal_preview_caption" class="text-xs font-bold text-slate-200">Pratinjau Foto Bukti</span>
                <button type="button" onclick="closeFotoPreview()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-2 flex items-center justify-center bg-black/40 rounded-xl overflow-hidden min-h-[300px] max-h-[70vh]">
                <img id="modal_preview_image" src="" alt="Pratinjau Foto" class="max-w-full max-h-[65vh] object-contain rounded-lg shadow-lg">
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- JAVASCRIPT SINGLE-PAGE WORKSPACE LOGIC                                    -->
    <!-- ========================================================================= -->
    <script>
        const BASE_URL = '<?= \Core\Guard::getBaseUrl() ?>';
        const IS_USER_ACTIVE = <?= $isActive ? 'true' : 'false' ?>;
        let currentActivePlotData = null;

        // =========================================================================
        // Client-side Search Course Cards
        // =========================================================================
        const searchInput       = document.getElementById('courseSearchInput');
        const courseCards       = document.querySelectorAll('.course-card');
        const courseCountBadge  = document.getElementById('courseCountBadge');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                let count = 0;

                courseCards.forEach(card => {
                    const nama  = card.dataset.nama || '';
                    const dosen = card.dataset.dosen || '';

                    if (query === '' || nama.includes(query) || dosen.includes(query)) {
                        card.classList.remove('hidden');
                        count++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                if (courseCountBadge) {
                    courseCountBadge.textContent = `${count} Matkul`;
                }
            });
        }

        // =========================================================================
        // Modal 1: Isi Absensi Logic
        // =========================================================================
        function openAbsenModal(plotData) {
            if (!plotData) return;

            // Proteksi: Cegah akses jika akun nonaktif (Mode Lihat Saja)
            if (!IS_USER_ACTIVE) {
                alert('Akun Anda sedang berstatus nonaktif (Mode Lihat Saja). Anda tidak dapat melakukan pengisian absensi.');
                return;
            }

            currentActivePlotData = plotData;

            document.getElementById('modal_absen_plotting_id').value = plotData.id_plotting;
            document.getElementById('modal_absen_matkul_title').textContent = plotData.nama_matkul || '-';
            document.getElementById('modal_absen_dosen_title').textContent = `Dosen Pembimbing: ${plotData.nama_dosen || 'Belum ditentukan'}`;

            // Hitung nomor pertemuan otomatis berikutnya
            const pastCount = (plotData.absensi_list && Array.isArray(plotData.absensi_list)) ? plotData.absensi_list.length : 0;
            document.getElementById('absen_pertemuan_ke').value = pastCount + 1;

            // Reset form kamera & text
            resetCameraPreview('kegiatan');
            resetCameraPreview('selfie');
            document.getElementById('absen_deskripsi_tugas').value = '';

            // Tutup modal riwayat jika terbuka
            document.getElementById('modalRiwayatAbsensi').classList.add('hidden');

            document.getElementById('modalIsiAbsensi').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAbsenModal() {
            document.getElementById('modalIsiAbsensi').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openAbsenFromRiwayat() {
            if (!currentActivePlotData) return;
            openAbsenModal(currentActivePlotData);
        }

        // =========================================================================
        // Modal 2: Riwayat Absensi Logic
        // =========================================================================
        function openRiwayatModal(plotData) {
            if (!plotData) return;
            currentActivePlotData = plotData;

            document.getElementById('modal_riwayat_matkul_title').textContent = plotData.nama_matkul || '-';
            document.getElementById('modal_riwayat_dosen_title').textContent = `Dosen Pembimbing: ${plotData.nama_dosen || 'Belum ditentukan'}`;

            const isPlotActive = parseInt(plotData.is_active) === 1;
            const canAbsen     = isPlotActive && IS_USER_ACTIVE;

            const btnAbsenBaru = document.getElementById('modal_riwayat_btn_absen_baru');
            if (btnAbsenBaru) {
                if (canAbsen) {
                    btnAbsenBaru.classList.remove('hidden');
                } else {
                    btnAbsenBaru.classList.add('hidden');
                }
            }

            const listContainer = document.getElementById('modal_riwayat_list_container');
            listContainer.innerHTML = '';

            const absensiList = plotData.absensi_list || [];
            document.getElementById('modal_riwayat_count_badge').textContent = `${absensiList.length} Pertemuan Tercatat`;

            if (absensiList.length === 0) {
                listContainer.innerHTML = `
                    <div class="p-8 text-center bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-3 border border-blue-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Belum Ada Catatan Absensi</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                            ${canAbsen ? 'Anda belum pernah mengisi absensi untuk mata kuliah ini.' : 'Tidak ada riwayat absensi pada mata kuliah ini (Mode Lihat Saja).'}
                        </p>
                        ${canAbsen ? `
                            <button type="button" onclick="openAbsenFromRiwayat()" class="mt-3.5 inline-flex items-center gap-1.5 px-4 py-2 bg-[#1867c0] hover:bg-[#14529d] text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                                <span>Isi Absensi Sekarang</span>
                            </button>
                        ` : ''}
                    </div>
                `;
            } else {
                absensiList.forEach(item => {
                    const status = item.status_verifikasi || 'pending';
                    let badgeHtml = '';
                    if (status === 'disetujui') {
                        badgeHtml = '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-emerald-50 text-emerald-800 border-emerald-300 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disetujui</span>';
                    } else if (status === 'ditolak') {
                        badgeHtml = '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-red-50 text-red-800 border-red-300 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Ditolak</span>';
                    } else {
                        badgeHtml = '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-amber-50 text-amber-800 border-amber-300 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>';
                    }

                    const card = document.createElement('div');
                    card.className = 'p-4 bg-white border border-slate-200 rounded-xl shadow-2xs space-y-2.5 hover:border-slate-300 transition';

                    const fotoKegiatanUrl = item.foto_kegiatan ? `${BASE_URL}/uploads/absensi/${item.foto_kegiatan}` : '';
                    const fotoSelfieUrl   = item.foto_selfie ? `${BASE_URL}/uploads/absensi/${item.foto_selfie}` : '';

                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Pertemuan Ke-${escapeHtml(item.pertemuan_ke || '-')}</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    ${escapeHtml(item.tanggal || '-')} &bull; Jam: ${escapeHtml(item.jam_mulai || '-')} s/d ${escapeHtml(item.jam_selesai || '-')}
                                </p>
                            </div>
                            <div>
                                ${badgeHtml}
                            </div>
                        </div>

                        <div class="bg-slate-50 p-2.5 rounded-lg text-xs text-slate-700 leading-relaxed border border-slate-100">
                            <span class="font-bold text-slate-900 block text-[11px] mb-0.5">Aktivitas / Modul:</span>
                            ${escapeHtml(item.deskripsi_tugas || '-')}
                        </div>

                        ${item.pesan_dosen ? `
                            <div class="bg-amber-50/70 p-2.5 rounded-lg text-xs text-amber-900 border border-amber-200">
                                <span class="font-bold block text-[11px] mb-0.5">Catatan Dosen Pengampu:</span>
                                ${escapeHtml(item.pesan_dosen)}
                            </div>
                        ` : ''}

                        <!-- Foto Bukti Thumbnails -->
                        <div class="pt-2 border-t border-slate-100 flex items-center gap-3">
                            <span class="text-[11px] font-bold text-slate-600 shrink-0">Foto Bukti:</span>
                            <div class="flex items-center gap-2">
                                ${fotoKegiatanUrl ? `
                                    <button type="button" onclick="openFotoPreview('${fotoKegiatanUrl}', 'Foto Kegiatan Praktikum — Pertemuan ${item.pertemuan_ke}')" class="relative group rounded-lg overflow-hidden border border-slate-200 hover:border-[#1867c0] transition cursor-pointer shadow-2xs">
                                        <img src="${fotoKegiatanUrl}" alt="Foto Kegiatan" class="w-12 h-12 object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[9px] font-bold">
                                            Kegiatan
                                        </div>
                                    </button>
                                ` : ''}

                                ${fotoSelfieUrl ? `
                                    <button type="button" onclick="openFotoPreview('${fotoSelfieUrl}', 'Foto Selfie di Lab — Pertemuan ${item.pertemuan_ke}')" class="relative group rounded-lg overflow-hidden border border-slate-200 hover:border-indigo-500 transition cursor-pointer shadow-2xs">
                                        <img src="${fotoSelfieUrl}" alt="Foto Selfie" class="w-12 h-12 object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[9px] font-bold">
                                            Selfie
                                        </div>
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `;

                    listContainer.appendChild(card);
                });
            }

            document.getElementById('modalRiwayatAbsensi').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRiwayatModal() {
            document.getElementById('modalRiwayatAbsensi').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // =========================================================================
        // Modal 3: Foto Lightbox Preview Logic
        // =========================================================================
        function openFotoPreview(imgUrl, caption) {
            if (!imgUrl) return;
            document.getElementById('modal_preview_image').src = imgUrl;
            document.getElementById('modal_preview_caption').textContent = caption || 'Pratinjau Foto Bukti';
            document.getElementById('modalFotoPreview').classList.remove('hidden');
        }

        function closeFotoPreview() {
            document.getElementById('modalFotoPreview').classList.add('hidden');
        }

        // =========================================================================
        // Native Camera Trigger & Preview
        // =========================================================================
        function triggerNativeCamera(type) {
            const input = document.getElementById(`foto_${type}`);
            if (input) input.click();
        }

        function previewNativePhoto(input, type) {
            const file = input.files && input.files[0];
            if (!file) return;

            const imgEl      = document.getElementById(`img_${type}`);
            const idleEl     = document.getElementById(`idle_${type}`);
            const previewEl  = document.getElementById(`preview_${type}`);
            const cardEl     = document.getElementById(`camera_card_${type}`);

            imgEl.src = URL.createObjectURL(file);
            idleEl.classList.add('hidden');
            previewEl.classList.remove('hidden');
            cardEl.classList.remove('border-dashed', 'border-slate-300');
            cardEl.classList.add('border-solid', 'border-emerald-500');
        }

        function resetCameraPreview(type) {
            const input      = document.getElementById(`foto_${type}`);
            const imgEl      = document.getElementById(`img_${type}`);
            const idleEl     = document.getElementById(`idle_${type}`);
            const previewEl  = document.getElementById(`preview_${type}`);
            const cardEl     = document.getElementById(`camera_card_${type}`);

            if (input) input.value = '';
            if (imgEl) imgEl.src = '';
            if (idleEl) idleEl.classList.remove('hidden');
            if (previewEl) previewEl.classList.add('hidden');
            if (cardEl) {
                cardEl.classList.remove('border-solid', 'border-emerald-500');
                cardEl.classList.add('border-dashed', 'border-slate-300');
            }
        }

        // Form Submit Validation: Pastikan kedua foto kamera sudah diambil
        document.getElementById('formIsiAbsensi')?.addEventListener('submit', function(e) {
            const kegiatanFile = document.getElementById('foto_kegiatan')?.files[0];
            const selfieFile   = document.getElementById('foto_selfie')?.files[0];

            if (!kegiatanFile || !selfieFile) {
                e.preventDefault();
                alert("Harap ambil Foto Kegiatan (kamera belakang) dan Foto Selfie (kamera depan) sebelum mengirimkan absensi.");
            }
        });

        // Escape Key Listener to Close Modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFotoPreview();
                closeAbsenModal();
                closeRiwayatModal();
            }
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>

</html>