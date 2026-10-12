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
    <title>Dashboard & Presensi Asdos — Absensi Asdos</title>

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

    <!-- Top Popup Notifications (Auto-dismiss 4 detik) -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Sidebar (Unified Desktop Navbar) -->
    <?php require_once __DIR__ . '/../Templates/asdos_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-8 space-y-6">

            <!-- Page Header / Top App Bar (Optime-Style) -->
            <div class="bg-white border border-slate-200/90 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?= $isActive ? 'bg-blue-50 text-[#1867c0] border border-blue-200/70' : 'bg-amber-50 text-amber-800 border border-amber-300' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $isActive ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' ?>"></span>
                            <?= $isActive ? 'Asisten Dosen Aktif • Lab SI' : 'Mode Lihat Saja (Akun Nonaktif)' ?>
                        </span>
                        <span class="text-xs text-slate-400 font-medium font-mono">NPM: <?= htmlspecialchars($currentUser['identity_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Halo, <?= htmlspecialchars(explode(' ', trim($currentUser['nama'] ?? 'Asisten'))[0], ENT_QUOTES, 'UTF-8') ?>! 👋
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola kehadiran praktikum lab, pelaporan aktivitas modul, dan surat dispensasi mengajar.</p>
                </div>

                <!-- Right Action Cluster (Optime-Style) -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="<?= \Core\Guard::url('/asdos/dispensasi') ?>" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition active:scale-95 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Surat Dispensasi</span>
                    </a>

                    <a href="#daftar-penugasan" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#1867c0] hover:bg-[#14529d] text-white text-xs font-bold transition active:scale-95 shadow-sm hover:shadow">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Isi Presensi</span>
                    </a>
                </div>
            </div>

            <!-- Optime 4-Stat Metric Cards Row (Clean Pastel Icon Squircles + Micro-Trend Pills) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">

                <!-- 1. Total Absensi -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:border-blue-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-[#1867c0] border border-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500 truncate">Total Presensi</p>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?= (int)$metrics['total'] ?></p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                            <span>Seluruh Sesi</span>
                        </span>
                    </div>
                </div>

                <!-- 2. Disetujui -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:border-emerald-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-emerald-800 truncate">Disetujui</p>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?= (int)$metrics['disetujui'] ?></p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            <span>Tervalidasi Dosen</span>
                        </span>
                    </div>
                </div>

                <!-- 3. Pending (Menunggu Review) -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border <?= $metrics['pending'] > 0 ? 'border-amber-300 ring-2 ring-amber-100/60' : 'border-slate-200/90' ?> shadow-xs hover:border-amber-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-amber-800 truncate">Menunggu Review</p>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?= (int)$metrics['pending'] ?></p>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                            <?php if ($metrics['pending'] > 0): ?>
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>Menunggu</span>
                            <?php else: ?>
                                <span>Tuntas</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- 4. Surat Dispensasi -->
                <a href="<?= \Core\Guard::url('/asdos/dispensasi') ?>" 
                    class="group bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:border-purple-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 block cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500 truncate">Surat Dispensasi</p>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?= (int)($totalDispensasi ?? 0) ?></p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200">
                            <span><?= (int)($dispensasiDisetujui ?? 0) ?> Disetujui</span>
                        </span>
                    </div>
                </a>

            </div>            
            
            <!-- Main Course Hub Section -->
            <div id="daftar-penugasan" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden scroll-mt-6">

                <!-- Section Header Toolbar -->
                <div class="p-4 sm:p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/70 via-white to-blue-50/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#1867c0]"></div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">Mata Kuliah Praktikum yang Diampu</h2>
                            <span id="courseCountBadge" class="px-2 py-0.5 text-xs font-bold bg-blue-50 text-[#1867c0] border border-blue-200/70 rounded-full">
                                <?= count($plottingList) ?> Matkul
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar penugasan praktikum aktif laboratorium untuk pengisian absensi & modul.</p>
                    </div>

                    <div class="flex items-center gap-2.5 w-full sm:w-auto">
                        <div class="relative w-full sm:w-72">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="courseSearchInput" placeholder="Cari mata kuliah atau dosen..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-3.5 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                        </div>
                    </div>
                </div>

                <!-- Section Body Content -->
                <div class="p-4 sm:p-6 bg-slate-50/40">
                    <?php if (empty($plottingList)): ?>
                        <div class="bg-white border border-slate-200 rounded-2xl p-10 sm:p-12 text-center shadow-xs">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5" id="courseGridContainer">
                            <?php foreach ($plottingList as $p): ?>
                                <?php
                                $isPlotActive = (int)$p['is_active'] === 1;
                                $totalAbsensi = (int)($p['total_absensi'] ?? 0);
                                ?>
                                <div class="course-card group bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs transition-all duration-200 hover:shadow-md hover:border-blue-400 hover:-translate-y-0.5 flex flex-col justify-between relative"
                                    data-nama="<?= htmlspecialchars(strtolower($p['nama_matkul']), ENT_QUOTES, 'UTF-8') ?>"
                                    data-dosen="<?= htmlspecialchars(strtolower($p['nama_dosen'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

                                    <div>
                                        <!-- Top Row: Icon & Status -->
                                        <div class="flex items-start justify-between gap-3 mb-3">
                                            <div class="w-11 h-11 rounded-2xl bg-blue-50 border border-blue-100 text-[#1867c0] font-extrabold text-xs flex items-center justify-center shadow-2xs group-hover:scale-105 transition-transform duration-150">
                                                MK
                                            </div>
                                            <?php if ($isPlotActive): ?>
                                                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 inline-flex items-center gap-1 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-slate-100 text-slate-600 border border-slate-300">
                                                    Selesai / Nonaktif
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Course Info -->
                                        <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-[#1867c0] transition">
                                            <?= htmlspecialchars($p['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                        </h3>

                                        <?php if (!empty($p['jam_mulai']) && !empty($p['jam_selesai'])): ?>
                                            <div class="mt-2">
                                                <span class="inline-flex items-center gap-1 font-mono text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-lg">
                                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <?= substr($p['jam_mulai'], 0, 5) ?> - <?= substr($p['jam_selesai'], 0, 5) ?> WIB
                                                </span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($p['deskripsi_matkul'])): ?>
                                            <p class="text-xs text-slate-500 line-clamp-2 mt-2 leading-relaxed">
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
                                            <span class="px-2.5 py-0.5 text-[11px] font-bold bg-slate-50 text-slate-700 rounded-lg border border-slate-200 inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <span><?= $totalAbsensi ?> Sesi Dicatat</span>
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <!-- Tombol Absen Sekarang -->
                                            <?php if ($isPlotActive && $isActive): ?>
                                                <button type="button"
                                                    onclick="openAbsenModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)"
                                                    class="px-3 py-2 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs font-bold rounded-xl transition shadow-xs hover:shadow flex items-center justify-center gap-1.5 cursor-pointer">
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
                                                class="px-3 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 active:scale-[0.98] text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <span>Riwayat</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Empty State Search Not Found -->
                        <div id="courseNoResults" class="hidden p-10 sm:p-12 text-center bg-white border border-slate-200 rounded-2xl shadow-xs">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">Mata Kuliah Tidak Ditemukan</h4>
                            <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                        </div>
                    <?php endif; ?>
                </div>

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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Isi Presensi Praktikum</h3>
                        <p class="text-[11px] text-slate-500 font-medium" id="modal_absen_step_label">Langkah 1 dari 3: Jadwal & Sesi</p>
                    </div>
                </div>
                <button type="button" onclick="closeAbsenModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer" title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Visual Stepper Bar (Progressive Flow) -->
            <div class="px-6 py-3 bg-slate-50/70 border-b border-slate-200">
                <div class="flex items-center justify-between relative max-w-sm mx-auto">
                    <!-- Base Track Line -->
                    <div class="absolute left-6 right-6 top-4 -translate-y-1/2 h-0.5 bg-slate-200 z-0"></div>
                    <!-- Active Fill Line -->
                    <div id="wizard_progress_line" class="absolute left-6 top-4 -translate-y-1/2 h-0.5 bg-[#1867c0] z-0 transition-all duration-300 w-0"></div>

                    <!-- Step 1 Node -->
                    <button type="button" onclick="goToWizardStep(1)" id="step_node_1" class="relative z-10 flex flex-col items-center group cursor-pointer focus:outline-none">
                        <div id="step_indicator_1" class="w-8 h-8 rounded-full bg-[#1867c0] text-white flex items-center justify-center text-xs font-bold ring-4 ring-blue-100 transition shadow-2xs">
                            1
                        </div>
                        <span id="step_text_1" class="text-[11px] font-bold text-[#1867c0] mt-1 transition">Sesi</span>
                    </button>

                    <!-- Step 2 Node -->
                    <button type="button" onclick="goToWizardStep(2)" id="step_node_2" class="relative z-10 flex flex-col items-center group cursor-pointer focus:outline-none">
                        <div id="step_indicator_2" class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold ring-4 ring-transparent transition">
                            2
                        </div>
                        <span id="step_text_2" class="text-[11px] font-semibold text-slate-500 mt-1 transition">Tugas</span>
                    </button>

                    <!-- Step 3 Node -->
                    <button type="button" onclick="goToWizardStep(3)" id="step_node_3" class="relative z-10 flex flex-col items-center group cursor-pointer focus:outline-none">
                        <div id="step_indicator_3" class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold ring-4 ring-transparent transition">
                            3
                        </div>
                        <span id="step_text_3" class="text-[11px] font-semibold text-slate-500 mt-1 transition">Bukti Foto</span>
                    </button>
                </div>
            </div>

            <!-- Form Content -->
            <form action="<?= \Core\Guard::url('/asdos/absensi/create') ?>" method="POST" enctype="multipart/form-data" id="formIsiAbsensi">
                <?= \Core\Guard::csrfField() ?>
                <input type="hidden" name="redirect_to" value="/asdos/dashboard">
                <input type="hidden" name="plotting_id" id="modal_absen_plotting_id" value="">

                <!-- ============================================== -->
                <!-- STEP 1: INFORMASI MATA KULIAH & SESI            -->
                <!-- ============================================== -->
                <div id="wizard_step_1" class="p-6 space-y-4">
                    <!-- Locked Course Context Banner -->
                    <div class="p-3.5 bg-blue-50/70 border border-blue-200/80 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#1867c0] block">Mata Kuliah Praktikum</span>
                        <p id="modal_absen_matkul_title" class="font-bold text-slate-900 text-sm leading-tight">-</p>
                        <p id="modal_absen_dosen_title" class="text-xs text-slate-600">Dosen: -</p>
                    </div>

                    <!-- Info Jadwal Praktikum Mata Kuliah -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1867c0] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Jadwal Praktikum</span>
                                <span id="absen_jadwal_text" class="text-xs font-bold text-slate-900">-</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">
                            Kurikulum Lab
                        </span>
                    </div>

                    <!-- Baris: Tanggal & Pertemuan Ke -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label for="absen_tanggal" class="block text-xs font-bold text-slate-800 mb-1">
                                Tanggal Pelaksanaan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="absen_tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>"
                                class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                            <p class="text-[10px] text-slate-500 mt-1">Tanggal saat praktikum berlangsung</p>
                        </div>

                        <div>
                            <label for="absen_pertemuan_ke" class="block text-xs font-bold text-slate-800 mb-1">
                                Pertemuan Ke- <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="absen_pertemuan_ke" name="pertemuan_ke" min="1" required placeholder="Contoh: 1"
                                class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                            <p class="text-[10px] text-slate-500 mt-1">Nomor sesi pertemuan praktikum</p>
                        </div>
                    </div>

                    <!-- Footer Step 1 -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                        <button type="button" onclick="closeAbsenModal()" class="px-4 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="button" onclick="nextWizardStep(1)" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                            <span>Lanjut: Checklist Tugas</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 2: CHECKLIST TUGAS PRAKTIKUM              -->
                <!-- ============================================== -->
                <div id="wizard_step_2" class="hidden p-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Pilih Aktivitas & Tugas Praktikum</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih minimal satu tugas yang Anda laksanakan pada sesi ini:</p>
                    </div>

                    <!-- Task Cards Checklist -->
                    <div id="taskChecklistContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <label class="task-pill flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-[#1867c0]/60 hover:bg-blue-50/30 transition cursor-pointer group">
                            <input type="checkbox" name="tugas[]" value="Mengajar" class="w-4 h-4 text-[#1867c0] rounded border-slate-300 focus:ring-[#1867c0] cursor-pointer" onchange="updateTaskPillStyle(this)">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block group-hover:text-[#1867c0] transition">Mengajar & Asistensi</span>
                                <span class="text-[10px] text-slate-500 block">Mendampingi mahasiswa di lab</span>
                            </div>
                        </label>

                        <label class="task-pill flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-[#1867c0]/60 hover:bg-blue-50/30 transition cursor-pointer group">
                            <input type="checkbox" name="tugas[]" value="Koreksi" class="w-4 h-4 text-[#1867c0] rounded border-slate-300 focus:ring-[#1867c0] cursor-pointer" onchange="updateTaskPillStyle(this)">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block group-hover:text-[#1867c0] transition">Koreksi Tugas / Lapres</span>
                                <span class="text-[10px] text-slate-500 block">Menilai laporan praktikan</span>
                            </div>
                        </label>

                        <label class="task-pill flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-[#1867c0]/60 hover:bg-blue-50/30 transition cursor-pointer group">
                            <input type="checkbox" name="tugas[]" value="Membuat Modul" class="w-4 h-4 text-[#1867c0] rounded border-slate-300 focus:ring-[#1867c0] cursor-pointer" onchange="updateTaskPillStyle(this)">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block group-hover:text-[#1867c0] transition">Membuat Modul Praktikum</span>
                                <span class="text-[10px] text-slate-500 block">Penyusunan bahan praktikum</span>
                            </div>
                        </label>

                        <label class="task-pill flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-[#1867c0]/60 hover:bg-blue-50/30 transition cursor-pointer group">
                            <input type="checkbox" name="tugas[]" value="Menjaga UTS/UAS" class="w-4 h-4 text-[#1867c0] rounded border-slate-300 focus:ring-[#1867c0] cursor-pointer" onchange="updateTaskPillStyle(this)">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block group-hover:text-[#1867c0] transition">Menjaga UTS / UAS</span>
                                <span class="text-[10px] text-slate-500 block">Pengawasan ujian praktikum</span>
                            </div>
                        </label>

                        <label class="task-pill flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white hover:border-[#1867c0]/60 hover:bg-blue-50/30 transition cursor-pointer group sm:col-span-2">
                            <input type="checkbox" name="tugas[]" value="Membuat Materi" class="w-4 h-4 text-[#1867c0] rounded border-slate-300 focus:ring-[#1867c0] cursor-pointer" onchange="updateTaskPillStyle(this)">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block group-hover:text-[#1867c0] transition">Menyiapkan Materi / Demo</span>
                                <span class="text-[10px] text-slate-500 block">Slide presentasi & source code praktikum</span>
                            </div>
                        </label>
                    </div>

                    <!-- Custom Task Input -->
                    <div class="pt-1">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Tambah Tugas Lainnya (Opsional)</label>
                        <div class="flex gap-2 items-center">
                            <input type="text" id="taskOtherInput" placeholder="Ketik tugas lainnya lalu klik Tambah..." class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                            <button type="button" id="btnAddOtherTask" onclick="addOtherTask()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shrink-0 transition cursor-pointer">
                                + Tambah
                            </button>
                        </div>
                    </div>

                    <!-- Footer Step 2 -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                        <button type="button" onclick="goToWizardStep(1)" class="px-4 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </button>
                        <button type="button" onclick="nextWizardStep(2)" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                            <span>Lanjut: Bukti Foto</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 3: BUKTI FOTO & CATATAN PELAKSANAAN        -->
                <!-- ============================================== -->
                <div id="wizard_step_3" class="hidden p-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Unggah Bukti Dokumentasi & Selfie</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Kedua foto wajib diunggah (JPG/PNG, ukuran maks. 2 MB per foto):</p>
                    </div>

                    <!-- Section Bukti Kamera (Native Camera Trigger) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- 1. Foto Kegiatan -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-800">
                                1. Foto Kegiatan di Lab <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="foto_kegiatan" id="foto_kegiatan" accept="image/*" class="hidden" onchange="previewNativePhoto(this, 'kegiatan')">

                            <div id="camera_card_kegiatan" class="relative w-full h-40 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/60 hover:border-[#1867c0] hover:bg-blue-50/20 overflow-hidden flex items-center justify-center transition-all duration-200 cursor-pointer group" onclick="triggerNativeCamera('kegiatan')">
                                <!-- Idle State -->
                                <div id="idle_kegiatan" class="text-center p-3 space-y-1.5">
                                    <div class="w-9 h-9 mx-auto rounded-xl bg-blue-50 text-[#1867c0] border border-blue-100 flex items-center justify-center group-hover:scale-105 transition shadow-2xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Foto Suasana Lab</p>
                                    <span class="px-2.5 py-1 bg-[#1867c0] text-white text-[10px] font-bold rounded-lg shadow-xs inline-block">Pilih File</span>
                                </div>

                                <!-- Preview State -->
                                <div id="preview_kegiatan" class="hidden w-full h-full relative">
                                    <img id="img_kegiatan" src="" alt="Preview Kegiatan" class="w-full h-full object-cover">
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2 py-0.5 bg-emerald-600/90 text-white text-[10px] font-bold rounded-md shadow backdrop-blur-xs flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Foto Terpilih
                                        </span>
                                    </div>
                                    <div class="absolute bottom-2 right-2">
                                        <button type="button" onclick="event.stopPropagation(); triggerNativeCamera('kegiatan')" class="px-2.5 py-1 bg-slate-900/80 hover:bg-slate-900 text-white text-[10px] font-bold rounded-lg transition shadow backdrop-blur-xs inline-flex items-center gap-1 cursor-pointer">
                                            <span>Ganti File</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Foto Selfie -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-800">
                                2. Foto Selfie Asisten <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="foto_selfie" id="foto_selfie" accept="image/*" class="hidden" onchange="previewNativePhoto(this, 'selfie')">

                            <div id="camera_card_selfie" class="relative w-full h-40 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/60 hover:border-indigo-500 hover:bg-indigo-50/20 overflow-hidden flex items-center justify-center transition-all duration-200 cursor-pointer group" onclick="triggerNativeCamera('selfie')">
                                <!-- Idle State -->
                                <div id="idle_selfie" class="text-center p-3 space-y-1.5">
                                    <div class="w-9 h-9 mx-auto rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center group-hover:scale-105 transition shadow-2xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">Selfie di Lab</p>
                                    <span class="px-2.5 py-1 bg-indigo-600 text-white text-[10px] font-bold rounded-lg shadow-xs inline-block">Pilih File</span>
                                </div>

                                <!-- Preview State -->
                                <div id="preview_selfie" class="hidden w-full h-full relative">
                                    <img id="img_selfie" src="" alt="Preview Selfie" class="w-full h-full object-cover">
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2 py-0.5 bg-emerald-600/90 text-white text-[10px] font-bold rounded-md shadow backdrop-blur-xs flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Selfie Terpilih
                                        </span>
                                    </div>
                                    <div class="absolute bottom-2 right-2">
                                        <button type="button" onclick="event.stopPropagation(); triggerNativeCamera('selfie')" class="px-2.5 py-1 bg-slate-900/80 hover:bg-slate-900 text-white text-[10px] font-bold rounded-lg transition shadow backdrop-blur-xs inline-flex items-center gap-1 cursor-pointer">
                                            <span>Ganti File</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes kendala -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="absen_deskripsi_tugas" class="block text-xs font-bold text-slate-800">
                                Catatan / Ringkasan Praktikum <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] text-slate-500">Min. 5 karakter</span>
                        </div>
                        <textarea id="absen_deskripsi_tugas" name="deskripsi_tugas" rows="2" minlength="5" required
                            placeholder="Materi yang dibahas atau kendala di lab (misal: Praktikum Modul 2 berjalan lancar)..."
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition resize-none shadow-2xs"></textarea>
                    </div>

                    <!-- Footer Step 3 -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                        <button type="button" onclick="goToWizardStep(2)" class="px-4 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </button>
                        <button type="submit" id="btn_submit_absensi" class="px-6 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Kirim Presensi</span>
                        </button>
                    </div>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
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
        const searchInput = document.getElementById('courseSearchInput');
        const courseCards = document.querySelectorAll('.course-card');
        const courseCountBadge = document.getElementById('courseCountBadge');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                let count = 0;

                courseCards.forEach(card => {
                    const nama = card.dataset.nama || '';
                    const dosen = card.dataset.dosen || '';

                    if (query === '' || nama.includes(query) || dosen.includes(query)) {
                        card.classList.remove('hidden');
                        count++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                const noResults = document.getElementById('courseNoResults');
                if (noResults) {
                    if (count === 0 && query !== '') {
                        noResults.classList.remove('hidden');
                    } else {
                        noResults.classList.add('hidden');
                    }
                }

                if (courseCountBadge) {
                    courseCountBadge.textContent = `${count} Matkul`;
                }
            });
        }

        // =========================================================================
        // Modal 1: Multi-Step Wizard Absensi Logic (Langkah 1, 2, 3)
        // =========================================================================
        let currentWizardStep = 1;

        function goToWizardStep(step) {
            step = parseInt(step);
            if (isNaN(step) || step < 1 || step > 3) step = 1;

            // Jika melompat maju dari langkah 1, validasi langkah 1 dulu
            if (currentWizardStep === 1 && step > 1) {
                const tgl = document.getElementById('absen_tanggal');
                const pert = document.getElementById('absen_pertemuan_ke');
                if (!tgl || !tgl.value) {
                    window.highlightAndNotify(tgl, 'Tanggal pelaksanaan praktikum wajib diisi.');
                    return;
                }
                if (!pert || !pert.value || parseInt(pert.value) < 1) {
                    window.highlightAndNotify(pert, 'Pertemuan ke- wajib diisi angka minimal 1.');
                    return;
                }
            }

            // Jika melompat ke langkah 3, validasi checklist tugas dulu
            if (step === 3) {
                const checkedTasks = document.querySelectorAll('#taskChecklistContainer input[type="checkbox"]:checked');
                if (checkedTasks.length === 0) {
                    window.highlightAndNotify(null, 'Pilih minimal satu aktivitas tugas praktikum sebelum ke bukti foto.');
                    return;
                }
                // Auto-fill catatan ringkasan jika belum diisi user
                const descEl = document.getElementById('absen_deskripsi_tugas');
                if (descEl && (!descEl.value.trim() || descEl.value.trim().length < 5)) {
                    const taskNames = Array.from(checkedTasks).map(c => c.value);
                    const pertVal = document.getElementById('absen_pertemuan_ke')?.value || '1';
                    descEl.value = `Praktikum Pertemuan ${pertVal}: Melaksanakan ${taskNames.join(', ')}`;
                }
            }

            currentWizardStep = step;

            // Toggle Tampilan Step Container
            const s1 = document.getElementById('wizard_step_1');
            const s2 = document.getElementById('wizard_step_2');
            const s3 = document.getElementById('wizard_step_3');

            if (s1) s1.classList.toggle('hidden', step !== 1);
            if (s2) s2.classList.toggle('hidden', step !== 2);
            if (s3) s3.classList.toggle('hidden', step !== 3);

            // Update Label Header Step
            const labelEl = document.getElementById('modal_absen_step_label');
            if (labelEl) {
                if (step === 1) labelEl.textContent = 'Langkah 1 dari 3: Jadwal & Sesi';
                else if (step === 2) labelEl.textContent = 'Langkah 2 dari 3: Aktivitas & Tugas';
                else if (step === 3) labelEl.textContent = 'Langkah 3 dari 3: Bukti Foto';
            }

            // Update Progress Bar Track Line
            const progressLine = document.getElementById('wizard_progress_line');
            if (progressLine) {
                if (step === 1) progressLine.style.width = '0%';
                else if (step === 2) progressLine.style.width = '50%';
                else if (step === 3) progressLine.style.width = '100%';
            }

            // Update Node Stepper Visuals (1, 2, 3)
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById(`step_indicator_${i}`);
                const text = document.getElementById(`step_text_${i}`);
                if (!indicator || !text) continue;

                if (i < step) {
                    // Selesai (Completed State)
                    indicator.className = 'w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold ring-4 ring-emerald-100 transition shadow-2xs';
                    indicator.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
                    text.className = 'text-[11px] font-bold text-emerald-700 mt-1 transition';
                } else if (i === step) {
                    // Sedang Aktif (Active State)
                    indicator.className = 'w-8 h-8 rounded-full bg-[#1867c0] text-white flex items-center justify-center text-xs font-bold ring-4 ring-blue-100 transition shadow-2xs';
                    indicator.textContent = i;
                    text.className = 'text-[11px] font-bold text-[#1867c0] mt-1 transition';
                } else {
                    // Belum Aktif (Upcoming State)
                    indicator.className = 'w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold ring-4 ring-transparent transition';
                    indicator.textContent = i;
                    text.className = 'text-[11px] font-semibold text-slate-500 mt-1 transition';
                }
            }
        }

        function nextWizardStep(fromStep) {
            if (fromStep === 1) {
                const tgl = document.getElementById('absen_tanggal');
                const pert = document.getElementById('absen_pertemuan_ke');
                if (!tgl || !tgl.value) {
                    window.highlightAndNotify(tgl, 'Tanggal pelaksanaan praktikum wajib diisi.');
                    return;
                }
                if (!pert || !pert.value || parseInt(pert.value) < 1) {
                    window.highlightAndNotify(pert, 'Pertemuan ke- wajib diisi angka minimal 1.');
                    return;
                }
                goToWizardStep(2);
            } else if (fromStep === 2) {
                const checkedTasks = document.querySelectorAll('#taskChecklistContainer input[type="checkbox"]:checked');
                if (checkedTasks.length === 0) {
                    window.highlightAndNotify(null, 'Pilih minimal satu aktivitas tugas praktikum yang dilaksanakan.');
                    return;
                }
                goToWizardStep(3);
            }
        }

        function updateTaskPillStyle(checkbox) {
            if (!checkbox) return;
            const parentLabel = checkbox.closest('.task-pill');
            if (!parentLabel) return;

            if (checkbox.checked) {
                parentLabel.classList.add('border-[#1867c0]', 'bg-blue-50/70');
                parentLabel.classList.remove('border-slate-200', 'bg-white');
            } else {
                parentLabel.classList.remove('border-[#1867c0]', 'bg-blue-50/70');
                parentLabel.classList.add('border-slate-200', 'bg-white');
            }
        }

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
            document.getElementById('modal_absen_dosen_title').textContent = `Dosen: ${plotData.nama_dosen || 'Belum ditentukan'}`;

            // Tampilkan jadwal praktikum mata kuliah
            if (plotData.jam_mulai && plotData.jam_selesai) {
                document.getElementById('absen_jadwal_text').textContent = `${plotData.jam_mulai.substring(0, 5)} - ${plotData.jam_selesai.substring(0, 5)} WIB`;
            } else {
                document.getElementById('absen_jadwal_text').textContent = 'Jam praktikum belum diatur oleh Admin';
            }

            // Reset nomor pertemuan agar user input manual
            document.getElementById('absen_pertemuan_ke').value = '';

            // Reset form kamera & text
            resetCameraPreview('kegiatan');
            resetCameraPreview('selfie');
            document.getElementById('absen_deskripsi_tugas').value = '';

            // Reset checklist style
            document.querySelectorAll('#taskChecklistContainer input[type="checkbox"]').forEach(cb => {
                updateTaskPillStyle(cb);
            });

            // Selalu mulai dari Langkah 1
            goToWizardStep(1);

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
        // Task Checklist Helpers
        // =========================================================================
        function addOtherTask() {
            const input = document.getElementById('taskOtherInput');
            if (!input) return;
            const raw = input.value.trim();
            if (raw === '') return;

            const container = document.getElementById('taskChecklistContainer');
            if (!container) return;

            const wrapper = document.createElement('label');
            wrapper.className = 'task-pill flex items-center justify-between gap-2.5 p-3 rounded-xl border border-[#1867c0] bg-blue-50/70 cursor-pointer group transition';

            const leftDiv = document.createElement('div');
            leftDiv.className = 'flex items-center gap-2.5 min-w-0';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'tugas[]';
            checkbox.value = raw;
            checkbox.checked = true;
            checkbox.className = 'w-4 h-4 text-[#1867c0] rounded border-slate-300 focus:ring-[#1867c0] cursor-pointer';
            checkbox.onchange = function() { updateTaskPillStyle(this); };

            const textDiv = document.createElement('div');
            textDiv.className = 'min-w-0';

            const span = document.createElement('span');
            span.className = 'text-xs font-bold text-slate-800 block truncate';
            span.textContent = raw;

            const subSpan = document.createElement('span');
            subSpan.className = 'text-[10px] text-slate-500 block';
            subSpan.textContent = 'Tugas tambahan kustom';

            textDiv.appendChild(span);
            textDiv.appendChild(subSpan);
            leftDiv.appendChild(checkbox);
            leftDiv.appendChild(textDiv);

            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.className = 'p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition shrink-0';
            btnRemove.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            btnRemove.title = 'Hapus tugas kustom';
            btnRemove.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                wrapper.remove();
            };

            wrapper.appendChild(leftDiv);
            wrapper.appendChild(btnRemove);
            container.appendChild(wrapper);

            input.value = '';
            input.focus();
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
            const canAbsen = isPlotActive && IS_USER_ACTIVE;

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

                    const cleanKegiatan = (item.foto_kegiatan || '').replace(/^(\/|uploads\/absensi\/)+/, '');
                    const fotoKegiatanUrl = item.foto_kegiatan ? (item.foto_kegiatan.startsWith('http') ? item.foto_kegiatan : `${BASE_URL}/uploads/absensi/${cleanKegiatan}`) : '';

                    const cleanSelfie = (item.foto_selfie || '').replace(/^(\/|uploads\/absensi\/)+/, '');
                    const fotoSelfieUrl = item.foto_selfie ? (item.foto_selfie.startsWith('http') ? item.foto_selfie : `${BASE_URL}/uploads/absensi/${cleanSelfie}`) : '';

                    const jamInfo = (item.jam_mulai && item.jam_selesai) ? ` &bull; Jam: ${escapeHtml(item.jam_mulai.substring(0, 5))} - ${escapeHtml(item.jam_selesai.substring(0, 5))} WIB` : '';

                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Pertemuan Ke-${escapeHtml(item.pertemuan_ke || '-')}</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    ${escapeHtml(item.tanggal || '-')}${jamInfo}
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

            const imgEl = document.getElementById(`img_${type}`);
            const idleEl = document.getElementById(`idle_${type}`);
            const previewEl = document.getElementById(`preview_${type}`);
            const cardEl = document.getElementById(`camera_card_${type}`);

            imgEl.src = URL.createObjectURL(file);
            idleEl.classList.add('hidden');
            previewEl.classList.remove('hidden');
            cardEl.classList.remove('border-dashed', 'border-slate-300');
            cardEl.classList.add('border-solid', 'border-emerald-500');
        }

        function resetCameraPreview(type) {
            const input = document.getElementById(`foto_${type}`);
            const imgEl = document.getElementById(`img_${type}`);
            const idleEl = document.getElementById(`idle_${type}`);
            const previewEl = document.getElementById(`preview_${type}`);
            const cardEl = document.getElementById(`camera_card_${type}`);

            if (input) input.value = '';
            if (imgEl) imgEl.src = '';
            if (idleEl) idleEl.classList.remove('hidden');
            if (previewEl) previewEl.classList.add('hidden');
            if (cardEl) {
                cardEl.classList.remove('border-solid', 'border-emerald-500');
                cardEl.classList.add('border-dashed', 'border-slate-300');
            }
        }

        // Form Submit Validation: Pastikan syarat input terpenuhi tanpa menutup modal
        document.getElementById('formIsiAbsensi')?.addEventListener('submit', function(e) {
            const tgl = document.getElementById('absen_tanggal');
            const pert = document.getElementById('absen_pertemuan_ke');
            const checkedTasks = document.querySelectorAll('#taskChecklistContainer input[type="checkbox"]:checked');
            const kegiatanFile = document.getElementById('foto_kegiatan')?.files[0];
            const selfieFile = document.getElementById('foto_selfie')?.files[0];
            const descEl = document.getElementById('absen_deskripsi_tugas');

            // 1. Validasi Langkah 1: Sesi & Tanggal
            if (!tgl || !tgl.value) {
                e.preventDefault();
                goToWizardStep(1);
                window.highlightAndNotify(tgl, 'Tanggal pelaksanaan praktikum wajib diisi.');
                return;
            }

            if (!pert || !pert.value || parseInt(pert.value) < 1) {
                e.preventDefault();
                goToWizardStep(1);
                window.highlightAndNotify(pert, 'Pertemuan ke- wajib diisi angka minimal 1.');
                return;
            }

            // 2. Validasi Langkah 2: Tugas Praktikum
            if (checkedTasks.length === 0) {
                e.preventDefault();
                goToWizardStep(2);
                window.highlightAndNotify(null, 'Pilih minimal satu aktivitas tugas praktikum yang dilaksanakan.');
                return;
            }

            // 3. Validasi Langkah 3: Bukti Foto & Catatan
            if (!kegiatanFile) {
                e.preventDefault();
                goToWizardStep(3);
                window.highlightAndNotify(null, 'Foto kegiatan praktikum di lab wajib diunggah.');
                return;
            }

            if (kegiatanFile.size > 2 * 1024 * 1024) {
                e.preventDefault();
                goToWizardStep(3);
                window.highlightAndNotify(null, 'Ukuran file foto kegiatan maksimal 2 MB.');
                return;
            }

            if (!selfieFile) {
                e.preventDefault();
                goToWizardStep(3);
                window.highlightAndNotify(null, 'Foto selfie kehadiran asisten wajib diunggah.');
                return;
            }

            if (selfieFile.size > 2 * 1024 * 1024) {
                e.preventDefault();
                goToWizardStep(3);
                window.highlightAndNotify(null, 'Ukuran file foto selfie maksimal 2 MB.');
                return;
            }

            // Pastikan catatan tugas minimal 5 karakter (jika belum ada, buat ringkasan otomatis)
            if (descEl && (!descEl.value.trim() || descEl.value.trim().length < 5)) {
                const taskNames = Array.from(checkedTasks).map(c => c.value);
                descEl.value = `Praktikum Pertemuan ${pert.value}: Melaksanakan ${taskNames.join(', ')}`;
            }

            // Feedback visual tombol submit
            const btnSubmit = document.getElementById('btn_submit_absensi');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-80', 'cursor-not-allowed');
                btnSubmit.innerHTML = `
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Mengirim Presensi...</span>
                `;
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

    <!-- Mobile Floating Action Button (FAB) untuk Akses Cepat Isi Absensi (Fitts's Law / Thumb Zone) -->
    <?php
    $activePlotList = array_values(array_filter($plottingList ?? [], fn($item) => (int)($item['is_active'] ?? 0) === 1));
    ?>
    <?php if (!empty($activePlotList) && $isActive): ?>
        <div class="md:hidden fixed bottom-20 right-4 z-40">
            <?php if (count($activePlotList) === 1): ?>
                <button type="button" 
                    onclick="openAbsenModal(<?= htmlspecialchars(json_encode($activePlotList[0]), ENT_QUOTES, 'UTF-8') ?>)"
                    class="flex items-center gap-2 bg-[#1867c0] hover:bg-[#14529d] active:scale-95 text-white font-bold text-xs px-4 py-3 rounded-full shadow-lg shadow-blue-900/30 border border-blue-400 cursor-pointer transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Isi Absensi</span>
                </button>
            <?php else: ?>
                <button type="button" 
                    onclick="document.getElementById('daftar-penugasan')?.scrollIntoView({behavior: 'smooth'})"
                    class="flex items-center gap-2 bg-[#1867c0] hover:bg-[#14529d] active:scale-95 text-white font-bold text-xs px-4 py-3 rounded-full shadow-lg shadow-blue-900/30 border border-blue-400 cursor-pointer transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Pilih Absen MK</span>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Mobile Bottom Navigation -->
    <?php require_once __DIR__ . '/../Templates/asdos_bottom_nav.php'; ?>
</body>

</html>