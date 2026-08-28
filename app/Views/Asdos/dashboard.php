<?php
// Fallback & Dokumentasi Variabel dari AsdosController
$currentUser     = $currentUser ?? \Core\Guard::user();
$isActive        = (int)($currentUser['is_active'] ?? 0) === 1;
$metrics         = $metrics ?? [
    'total'     => 0,
    'disetujui' => 0,
    'pending'   => 0,
    'ditolak'   => 0,
];
$activePlottings = $activePlottings ?? [];
$recentAbsensi   = $recentAbsensi ?? [];

$statusBadge = static function (string $status): string {
    return match ($status) {
        'disetujui' => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-300">DISETUJUI</span>',
        'ditolak'   => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full border bg-red-50 text-red-700 border-red-300">DITOLAK</span>',
        default     => '<span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full border bg-amber-50 text-amber-700 border-amber-300">PENDING</span>',
    };
};
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Asisten Dosen — Absensi Lab</title>

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

    <!-- Top Popup Notifications (Auto-dismiss) -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Sidebar (Unified Desktop Navbar) -->
    <?php require_once __DIR__ . '/../Templates/asdos_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

            <!-- Page Header Banner -->
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        Selamat Datang, <?= htmlspecialchars($currentUser['nama'] ?? 'Asdos', ENT_QUOTES, 'UTF-8') ?>!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Ringkasan aktivitas absensi &amp; penugasan Anda sebagai Asisten Dosen.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <?php if ($isActive): ?>
                        <a href="<?= \Core\Guard::url('/asdos/absensi') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 shadow-xs hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Isi Absensi Sekarang</span>
                        </a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200 rounded-lg">
                            Akun Nonaktif — Mode Lihat Saja
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Metric Cards (4 Columns) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-4">

                <!-- 1. Total Absensi -->
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Absensi</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= $metrics['total'] ?></p>
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
                        <p class="text-2xl sm:text-3xl font-bold text-emerald-800 mt-1"><?= $metrics['disetujui'] ?></p>
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
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Menunggu Verifikasi</p>
                        <p class="text-2xl sm:text-3xl font-bold text-amber-800 mt-1"><?= $metrics['pending'] ?></p>
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
                        <p class="text-2xl sm:text-3xl font-bold text-red-800 mt-1"><?= $metrics['ditolak'] ?></p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-red-100 text-red-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-6">

                <!-- Matkul Saya (Aktif) -->
                <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/60">
                        <h2 class="text-base font-bold text-slate-900">Matkul Saya (Aktif)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Mata kuliah yang sedang Anda diplot oleh Super Admin.</p>
                    </div>

                    <?php if (empty($activePlottings)): ?>
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Belum Ada Penugasan</p>
                            <p class="text-xs text-slate-400 mt-1">Hubungi Super Admin Lab untuk diplot ke mata kuliah.</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-slate-100">
                            <?php foreach ($activePlottings as $p): ?>
                                <div class="p-4 sm:p-5 hover:bg-blue-50/30 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($p['nama_matkul'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-xs text-slate-600 mt-0.5">Dosen Pembimbing: <strong><?= htmlspecialchars($p['nama_dosen'] ?? 'Belum ditentukan', ENT_QUOTES, 'UTF-8') ?></strong></p>
                                        </div>
                                        <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-emerald-50 text-emerald-800 border-emerald-300">
                                            Aktif
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-2 text-[11px] text-slate-500">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span><?= date('d M Y', strtotime($p['periode_mulai'])) ?> – <?= date('d M Y', strtotime($p['periode_selesai'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Aktivitas Absensi Terbaru -->
                <div class="lg:col-span-3 bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Aktivitas Absensi Terbaru</h2>
                            <p class="text-xs text-slate-500 mt-0.5">5 catatan absensi terakhir yang Anda kirimkan.</p>
                        </div>
                        <a href="<?= \Core\Guard::url('/asdos/history') ?>" class="text-xs font-semibold text-[#1867c0] hover:underline shrink-0">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <?php if (empty($recentAbsensi)): ?>
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Belum Ada Data Absensi</p>
                            <p class="text-xs text-slate-400 mt-1">Riwayat absensi yang Anda catat akan muncul di sini.</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-slate-100">
                            <?php foreach ($recentAbsensi as $a): ?>
                                <div class="p-4 sm:p-5 flex items-center justify-between gap-3 hover:bg-blue-50/30 transition-colors">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-xs sm:text-sm truncate"><?= htmlspecialchars($a['nama_matkul'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            Pertemuan <?= htmlspecialchars((string)($a['pertemuan_ke'] ?? '-'), ENT_QUOTES, 'UTF-8') ?> &middot; <?= date('d M Y', strtotime($a['tanggal'])) ?>
                                        </p>
                                    </div>
                                    <?= $statusBadge($a['status_verifikasi']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </main>
    </div>

    <!-- Floating Bottom Navigation (Mobile) -->
    <?php require_once __DIR__ . '/../Templates/asdos_bottom_nav.php'; ?>

</body>

</html>