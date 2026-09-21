<?php
// Fallback & Dokumentasi Variabel dari DosenController
$currentUser       = $currentUser ?? \Core\Guard::user();
$metrics           = $metrics ?? ['total' => 0, 'disetujui' => 0, 'pending' => 0, 'ditolak' => 0];
$myMatkul          = $myMatkul ?? [];
$pendingAbsensi    = $pendingAbsensi ?? [];
$recentAbsensi     = $recentAbsensi ?? [];
$totalAsdosAktif   = $totalAsdosAktif ?? 0;
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen — Absensi Asdos</title>

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

    <!-- Header / Navbar -->
    <?php require_once __DIR__ . '/../Templates/dosen_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

            <!-- Page Header / Greeting Banner -->
            <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-[#1867c0] rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-blue-200 text-xs font-semibold mb-3 border border-white/10">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Dosen Pengampu Mata Kuliah
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Selamat Datang, <?= htmlspecialchars($currentUser['nama'] ?? 'Bapak/Ibu Dosen', ENT_QUOTES, 'UTF-8') ?>!</h1>
                    </div>

                    <div class="hidden sm:flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-xs text-blue-100">
                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span><strong><?= count($myMatkul) ?></strong> Mata Kuliah Diampu</span>
                    </div>
                </div>

                <!-- Decorative Background Pattern -->
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <!-- Metric / Stat Cards Grid (4 Columns) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-4">

                <!-- 1. Total Absensi -->
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Absensi</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= $metrics['total'] ?></p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Seluruh praktikum</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#1867c0] flex items-center justify-center border border-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                </div>

                <!-- 2. Menunggu Verifikasi (Pending) -->
                <a href="#laporan-pending" class="bg-white p-4 sm:p-5 rounded-xl border <?= $metrics['pending'] > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200' ?> shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-amber-400 block group">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-xs font-bold uppercase tracking-wider <?= $metrics['pending'] > 0 ? 'text-amber-700' : 'text-slate-500' ?>">Perlu Review</p>
                            <?php if ($metrics['pending'] > 0): ?>
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-2xl sm:text-3xl font-bold <?= $metrics['pending'] > 0 ? 'text-amber-800' : 'text-slate-700' ?> mt-1"><?= $metrics['pending'] ?></p>
                        <p class="text-[11px] text-amber-600 mt-0.5 group-hover:underline">Lihat laporan di bawah &darr;</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl <?= $metrics['pending'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' ?> flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>

                <!-- 3. Disetujui -->
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-emerald-200/80 bg-emerald-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-emerald-400">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Disetujui</p>
                        <p class="text-2xl sm:text-3xl font-bold text-emerald-800 mt-1"><?= $metrics['disetujui'] ?></p>
                        <p class="text-[11px] text-emerald-600 mt-0.5">Tervalidasi dosen</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- 4. Mata Kuliah & Asdos Aktif -->
                <a href="#daftar-matkul" class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-purple-300 block group">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-purple-700 transition">Matkul Diampu</p>
                        <p class="text-2xl sm:text-3xl font-bold text-purple-900 mt-1"><?= count($myMatkul) ?></p>
                        <p class="text-[11px] text-purple-600 mt-0.5 group-hover:underline">Pilih matkul di bawah &darr;</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </a>

            </div>

            <!-- SECTION 1: Laporan Absensi yang Perlu Verifikasi Segera -->
            <div id="laporan-pending" class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden scroll-mt-6">
                <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                        <h2 class="text-base font-bold text-slate-900">Laporan Absensi Perlu Verifikasi Segera</h2>
                        <?php if (count($pendingAbsensi) > 0): ?>
                            <span class="px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-800 rounded-full">
                                <?= count($pendingAbsensi) ?> Menunggu
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if (count($pendingAbsensi) > 5): ?>
                        <span class="text-xs text-slate-500 font-medium">Menampilkan 5 dari <?= count($pendingAbsensi) ?> laporan</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($pendingAbsensi)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 border-collapse">
                            <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Tanggal & Pertemuan</th>
                                    <th class="px-3.5 py-3">Asisten Dosen</th>
                                    <th class="px-3.5 py-3">Mata Kuliah</th>
                                    <th class="px-3 py-3 text-center">Bukti Foto</th>
                                    <th class="px-3.5 py-3 text-center">Aksi Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <?php foreach (array_slice($pendingAbsensi, 0, 5) as $pa): ?>
                                    <tr class="hover:bg-amber-50/30 transition duration-150">
                                        <td class="px-4 py-3">
                                            <p class="font-bold text-slate-900"><?= date('d M Y', strtotime($pa['tanggal'])) ?></p>
                                            <span class="text-[11px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">
                                                Pertemuan ke-<?= $pa['pertemuan_ke'] ?? '1' ?>
                                            </span>
                                        </td>
                                        <td class="px-3.5 py-3">
                                            <p class="font-bold text-slate-900"><?= htmlspecialchars($pa['nama_asdos'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-[11px] text-slate-500 font-mono">NPM: <?= htmlspecialchars($pa['npm_asdos'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </td>
                                        <td class="px-3.5 py-3">
                                            <p class="font-bold text-slate-800"><?= htmlspecialchars($pa['nama_matkul'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <?php if (!empty($pa['jam_mulai']) && !empty($pa['jam_selesai'])): ?>
                                                <p class="text-[11px] text-slate-500"><?= substr($pa['jam_mulai'], 0, 5) ?> - <?= substr($pa['jam_selesai'], 0, 5) ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <?php if (!empty($pa['foto_kegiatan'])): ?>
                                                    <?php
                                                    $kegiatanSrc = (str_starts_with($pa['foto_kegiatan'], 'http') || str_starts_with($pa['foto_kegiatan'], '/'))
                                                        ? $pa['foto_kegiatan']
                                                        : \Core\Guard::url('/uploads/absensi/' . $pa['foto_kegiatan']);
                                                    ?>
                                                    <button type="button"
                                                        onclick="previewImage('<?= htmlspecialchars($kegiatanSrc, ENT_QUOTES, 'UTF-8') ?>', 'Bukti Kegiatan Praktikum')"
                                                        class="px-2 py-1 rounded bg-slate-100 hover:bg-blue-50 text-blue-700 border border-slate-300 text-[11px] font-semibold cursor-pointer">
                                                        Kegiatan
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (!empty($pa['foto_selfie'])): ?>
                                                    <?php
                                                    $selfieSrc = (str_starts_with($pa['foto_selfie'], 'http') || str_starts_with($pa['foto_selfie'], '/'))
                                                        ? $pa['foto_selfie']
                                                        : \Core\Guard::url('/uploads/absensi/' . $pa['foto_selfie']);
                                                    ?>
                                                    <button type="button"
                                                        onclick="previewImage('<?= htmlspecialchars($selfieSrc, ENT_QUOTES, 'UTF-8') ?>', 'Foto Selfie Kehadiran')"
                                                        class="px-2 py-1 rounded bg-slate-100 hover:bg-indigo-50 text-indigo-700 border border-slate-300 text-[11px] font-semibold cursor-pointer">
                                                        Selfie
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <button type="button"
                                                onclick="openVerificationModal(<?= htmlspecialchars(json_encode($pa), ENT_QUOTES, 'UTF-8') ?>)"
                                                class="px-3 py-1.5 rounded-lg bg-[#1867c0] hover:bg-[#14529d] text-white text-xs font-bold transition shadow-xs cursor-pointer active:scale-95">
                                                Verifikasi Sekarang
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 border border-emerald-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-800">Semua Laporan Telah Diverifikasi!</p>
                        <p class="text-xs text-slate-500 mt-0.5">Tidak ada laporan absensi praktikum yang menunggu review Anda saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- SECTION 2: Daftar Mata Kuliah & Plotting Asdos yang Diampu -->
            <div id="daftar-matkul" class="space-y-4 scroll-mt-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Mata Kuliah yang Diampu</h2>
                        <p class="text-xs text-slate-500">Klik pada mata kuliah untuk langsung membuka dan mengelola seluruh presensi asdos</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 bg-white px-3 py-1 rounded-lg border border-slate-200 shadow-2xs self-start sm:self-auto">
                        Total <?= count($myMatkul) ?> Kelas
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php if (!empty($myMatkul)): ?>
                        <?php foreach ($myMatkul as $m): ?>
                            <?php
                            $mMetrics       = $m['metrics'] ?? ['total' => 0, 'disetujui' => 0, 'pending' => 0, 'ditolak' => 0];
                            $pendingCount   = (int)($mMetrics['pending'] ?? 0);
                            $disetujuiCount = (int)($mMetrics['disetujui'] ?? 0);
                            $totalAbsensi   = (int)($mMetrics['total'] ?? 0);
                            ?>
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col justify-between hover:shadow-md hover:border-blue-300 transition duration-150 relative group">
                                <div>
                                    <!-- Header Card: Category & Status Badge -->
                                    <div class="flex items-start justify-between gap-2 mb-2.5">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-[#1867c0] border border-blue-200">
                                            Mata Kuliah
                                        </span>

                                        <?php if ($pendingCount > 0): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <?= $pendingCount ?> Perlu Verifikasi
                                            </span>
                                        <?php elseif ($totalAbsensi > 0): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Semua Disetujui
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                Belum ada sesi
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Nama Matkul -->
                                    <h3 class="text-base font-bold text-slate-900 group-hover:text-[#1867c0] transition">
                                        <a href="<?= \Core\Guard::url('/dosen/matkul/' . $m['id_matkul']) ?>" class="focus:outline-none">
                                            <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </h3>

                                    <!-- Jadwal Praktikum -->
                                    <?php if (!empty($m['jam_mulai']) && !empty($m['jam_selesai'])): ?>
                                        <div class="flex items-center gap-1.5 mt-1.5 text-xs text-slate-500">
                                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Jadwal: <?= substr($m['jam_mulai'], 0, 5) ?> - <?= substr($m['jam_selesai'], 0, 5) ?> WIB</span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Deskripsi -->
                                    <?php if (!empty($m['deskripsi'])): ?>
                                        <p class="text-xs text-slate-600 mt-2 line-clamp-2"><?= htmlspecialchars($m['deskripsi'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>

                                    <!-- Mini Summary Chips (Keterangan Absensi) -->
                                    <div class="grid grid-cols-3 gap-2 mt-3.5 pt-3 border-t border-slate-100 text-center">
                                        <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total</span>
                                            <span class="text-xs font-bold text-slate-800"><?= $totalAbsensi ?> Sesi</span>
                                        </div>
                                        <div class="p-1.5 rounded-lg bg-emerald-50/60 border border-emerald-100">
                                            <span class="block text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Disetujui</span>
                                            <span class="text-xs font-bold text-emerald-800"><?= $disetujuiCount ?></span>
                                        </div>
                                        <div class="p-1.5 rounded-lg <?= $pendingCount > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-100' ?>">
                                            <span class="block text-[10px] <?= $pendingCount > 0 ? 'text-amber-700' : 'text-slate-400' ?> font-bold uppercase tracking-wider">Pending</span>
                                            <span class="text-xs font-bold <?= $pendingCount > 0 ? 'text-amber-800' : 'text-slate-700' ?>"><?= $pendingCount ?></span>
                                        </div>
                                    </div>

                                    <!-- Daftar Asdos Terplot -->
                                    <div class="mt-3.5 pt-3 border-t border-slate-100">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Asisten Dosen Bertugas:</p>
                                        <?php if (!empty($m['plottings'])): ?>
                                            <div class="space-y-1.5">
                                                <?php foreach ($m['plottings'] as $pl): ?>
                                                    <div class="flex items-center justify-between text-xs p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-6 h-6 rounded-md bg-blue-100 text-[#1867c0] font-bold text-[10px] flex items-center justify-center shrink-0">
                                                                <?= strtoupper(substr($pl['nama_asdos'] ?? 'A', 0, 1)) ?>
                                                            </div>
                                                            <div>
                                                                <p class="font-bold text-slate-800 leading-tight"><?= htmlspecialchars($pl['nama_asdos'], ENT_QUOTES, 'UTF-8') ?></p>
                                                                <p class="text-[10px] text-slate-400 font-mono">NPM: <?= htmlspecialchars($pl['npm_asdos'], ENT_QUOTES, 'UTF-8') ?></p>
                                                            </div>
                                                        </div>
                                                        <?php if ((int)$pl['is_active'] === 1): ?>
                                                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                                        <?php else: ?>
                                                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-100 text-slate-500">Selesai</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-xs text-slate-400 italic">Belum ada asdos yang diplotkan.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Action Button: Direct ke Absensi Matkul -->
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <a href="<?= \Core\Guard::url('/dosen/matkul/' . $m['id_matkul']) ?>" 
                                       class="w-full inline-flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-[#1867c0] hover:bg-[#14529d] text-white font-bold text-xs shadow-xs transition duration-150 active:scale-95 group/btn cursor-pointer">
                                        <span>Buka Absensi Kelas Ini</span>
                                        <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full bg-white border border-slate-200 rounded-2xl p-8 text-center">
                            <p class="text-sm font-bold text-slate-800">Belum Ada Mata Kuliah yang Diampu</p>
                            <p class="text-xs text-slate-500 mt-1">Silakan hubungi Super Admin untuk penugasan mata kuliah.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>

    <!-- Floating Bottom Navigation untuk Layar Smartphone -->
    <?php require_once __DIR__ . '/../Templates/dosen_bottom_nav.php'; ?>

    <!-- ========================================================================= -->
    <!-- MODAL: VERIFIKASI STATUS ABSENSI (DOSEN)                                  -->
    <!-- ========================================================================= -->
    <div id="verificationModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1867c0] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Verifikasi Kehadiran Praktikum</h3>
                </div>
                <button type="button" onclick="closeVerificationModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Info Singkat Praktikum -->
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Mata Kuliah:</span>
                    <span class="font-bold text-slate-800" id="modalMatkul">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Asisten Dosen:</span>
                    <span class="font-bold text-slate-800" id="modalAsdos">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pertemuan & Tanggal:</span>
                    <span class="font-bold text-slate-800" id="modalPertemuan">-</span>
                </div>
                <div class="pt-1.5 border-t border-slate-200">
                    <span class="text-slate-500 block mb-0.5">Deskripsi Kegiatan Asdos:</span>
                    <p class="font-medium text-slate-700 italic bg-white p-2 rounded border border-slate-200" id="modalDeskripsi">-</p>
                </div>
            </div>

            <form id="formVerification" method="POST" action="">
                <?= \Core\Guard::csrfField() ?>
                <input type="hidden" name="redirect_to" value="<?= \Core\Guard::url('/dosen/dashboard') ?>">

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Status Verifikasi <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-emerald-50/50 has-checked:border-emerald-500 has-checked:bg-emerald-50 has-checked:text-emerald-900 transition">
                                <input type="radio" name="status_verifikasi" value="disetujui" class="accent-emerald-600" required>
                                <span class="text-xs font-bold text-emerald-800">Disetujui</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-red-50/50 has-checked:border-red-500 has-checked:bg-red-50 has-checked:text-red-900 transition">
                                <input type="radio" name="status_verifikasi" value="ditolak" class="accent-red-600" required>
                                <span class="text-xs font-bold text-red-800">Ditolak</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="modalPesanDosen" class="block text-xs font-bold text-slate-700 mb-1">Catatan / Pesan untuk Asdos (Opsional)</label>
                        <textarea id="modalPesanDosen" name="pesan_dosen" rows="3"
                            placeholder="Tuliskan masukan atau alasan penolakan/persetujuan untuk asdos..."
                            class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 mt-4">
                    <button type="button" onclick="closeVerificationModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-[#1867c0] hover:bg-[#14529d] rounded-xl shadow-xs transition cursor-pointer">
                        Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: IMAGE PREVIEW LIGHTBOX                                             -->
    <!-- ========================================================================= -->
    <div id="imagePreviewModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative max-w-2xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h4 id="previewTitle" class="text-xs font-bold text-slate-800 truncate">Preview Foto</h4>
                <button type="button" onclick="closePreviewImage()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 flex items-center justify-center bg-slate-900 min-h-[300px]">
                <img id="previewImg" src="" alt="Preview" class="max-h-[70vh] max-w-full object-contain rounded-lg">
            </div>
        </div>
    </div>

    <script>
        function openVerificationModal(data) {
            document.getElementById('modalMatkul').textContent = data.nama_matkul || '-';
            document.getElementById('modalAsdos').textContent = (data.nama_asdos || '-') + ' (' + (data.npm_asdos || '-') + ')';
            document.getElementById('modalPertemuan').textContent = 'Pertemuan ke-' + (data.pertemuan_ke || '1') + ' • ' + (data.tanggal || '-');
            document.getElementById('modalDeskripsi').textContent = data.deskripsi_tugas || '-';
            document.getElementById('modalPesanDosen').value = data.pesan_dosen || '';

            const radios = document.getElementsByName('status_verifikasi');
            for (let r of radios) {
                r.checked = (r.value === data.status_verifikasi);
            }

            const form = document.getElementById('formVerification');
            form.action = '<?= \Core\Guard::url('/dosen/absensi/') ?>' + data.id_absensi + '/status';

            document.getElementById('verificationModal').classList.remove('hidden');
        }

        function closeVerificationModal() {
            document.getElementById('verificationModal').classList.add('hidden');
        }

        function previewImage(src, title) {
            document.getElementById('previewImg').src = src;
            document.getElementById('previewTitle').textContent = title || 'Preview Foto';
            document.getElementById('imagePreviewModal').classList.remove('hidden');
        }

        function closePreviewImage() {
            document.getElementById('imagePreviewModal').classList.add('hidden');
        }
    </script>
</body>
</html>
