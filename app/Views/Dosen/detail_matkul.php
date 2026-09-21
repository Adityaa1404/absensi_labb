<?php
// Fallback & Dokumentasi Variabel dari DosenController
$currentUser = $currentUser ?? \Core\Guard::user();
$matkul      = $matkul ?? [];
$plottings   = $plottings ?? [];
$absensiList = $absensiList ?? [];
$metrics     = $metrics ?? [
    'total'     => 0,
    'disetujui' => 0,
    'pending'   => 0,
    'ditolak'   => 0,
];
$filters     = $filters ?? [
    'search'            => '',
    'status_verifikasi' => '',
    'date_start'        => '',
    'date_end'          => '',
];

$matkulId   = (int)($matkul['id_matkul'] ?? 0);
$namaMatkul = $matkul['nama_matkul'] ?? 'Mata Kuliah';
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($namaMatkul, ENT_QUOTES, 'UTF-8') ?> — Detail & Absensi Praktikum</title>

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

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="<?= \Core\Guard::url('/dosen/dashboard') ?>" class="hover:text-[#1867c0] transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <span>/</span>
                <span class="text-slate-400">Mata Kuliah</span>
                <span>/</span>
                <span class="text-slate-800 truncate font-bold"><?= htmlspecialchars($namaMatkul, ENT_QUOTES, 'UTF-8') ?></span>
            </nav>

            <!-- Course Profile & Header Card -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs relative overflow-hidden transition-all hover:shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="<?= \Core\Guard::url('/dosen/dashboard') ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer border border-slate-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <span>Kembali ke Dashboard</span>
                            </a>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-blue-50 text-[#1867c0] border border-blue-200">
                                Mata Kuliah yang Diampu
                            </span>
                            <?php if ($metrics['pending'] > 0): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <?= $metrics['pending'] ?> Perlu Verifikasi
                                </span>
                            <?php endif; ?>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                            <?= htmlspecialchars($namaMatkul, ENT_QUOTES, 'UTF-8') ?>
                        </h1>

                        <?php if (!empty($matkul['jam_mulai']) && !empty($matkul['jam_selesai'])): ?>
                            <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-600">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Jadwal Praktikum: <strong><?= substr($matkul['jam_mulai'], 0, 5) ?> - <?= substr($matkul['jam_selesai'], 0, 5) ?> WIB</strong></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($matkul['deskripsi'])): ?>
                            <p class="text-xs sm:text-sm text-slate-500 max-w-3xl leading-relaxed pt-1">
                                <?= htmlspecialchars($matkul['deskripsi'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Absensi Table Card with Filters -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <!-- Toolbar Filter -->
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/60 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Rekapitulasi Presensi Praktikum</h2>
                        </div>
                    </div>

                    <!-- Interactive Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
                        <!-- Search Input -->
                        <div class="sm:col-span-8 relative">
                            <label for="searchInput" class="block text-xs font-bold text-slate-700 mb-1">Cari Absensi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput"
                                    placeholder="Ketik nama asdos, NPM, atau topik tugas praktikum..."
                                    class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                                <button type="button" id="clearSearchBtn" onclick="clearSearch()" title="Hapus pencarian" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="sm:col-span-4">
                            <label for="statusFilter" class="block text-xs font-bold text-slate-700 mb-1">Status Verifikasi</label>
                            <select id="statusFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer shadow-2xs">
                                <option value="">Semua Status</option>
                                <option value="pending" <?= ($filters['status_verifikasi'] === 'pending') ? 'selected' : '' ?>>Menunggu Verifikasi (Pending)</option>
                                <option value="disetujui" <?= ($filters['status_verifikasi'] === 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                                <option value="ditolak" <?= ($filters['status_verifikasi'] === 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm text-slate-700 border-collapse">
                        <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200 select-none">
                            <tr>
                                <th class="px-4 py-3.5">Tanggal & Pertemuan</th>
                                <th class="px-3.5 py-3.5">Asisten Dosen</th>
                                <th class="px-3.5 py-3.5">Deskripsi Kegiatan</th>
                                <th class="px-3 py-3.5 text-center">Bukti Foto</th>
                                <th class="px-3 py-3.5 text-center">Status</th>
                                <th class="px-3.5 py-3.5 text-center">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200" id="absensiTableBody">
                            <?php if (!empty($absensiList)): ?>
                                <?php foreach ($absensiList as $a): ?>
                                    <?php
                                    $status = $a['status_verifikasi'];
                                    $statusBadge = match ($status) {
                                        'disetujui' => ['label' => 'DISETUJUI', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300', 'dot' => 'bg-emerald-500'],
                                        'ditolak'   => ['label' => 'DITOLAK', 'bg' => 'bg-red-50', 'text' => 'text-red-800', 'border' => 'border-red-300', 'dot' => 'bg-red-500'],
                                        default     => ['label' => 'PENDING', 'bg' => 'bg-amber-50', 'text' => 'text-amber-900', 'border' => 'border-amber-300', 'dot' => 'bg-amber-500']
                                    };

                                    $words = explode(' ', trim($a['nama_asdos'] ?? 'Asdos'));
                                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                    ?>
                                    <tr class="hover:bg-blue-50/40 transition-colors duration-150 absensi-row"
                                        data-id="<?= $a['id_absensi'] ?>"
                                        data-asdos-nama="<?= htmlspecialchars($a['nama_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-asdos-npm="<?= htmlspecialchars($a['npm_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-status="<?= $status ?>"
                                        data-tanggal="<?= $a['tanggal'] ?? '' ?>"
                                        data-deskripsi="<?= htmlspecialchars($a['deskripsi_tugas'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                                        <!-- Tanggal & Pertemuan -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5 font-bold text-slate-900 text-xs sm:text-sm">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span><?= date('d M Y', strtotime($a['tanggal'])) ?></span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                                <span class="font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">
                                                    Pertemuan ke-<?= $a['pertemuan_ke'] ?? '1' ?>
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Asisten Dosen -->
                                        <td class="px-3.5 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1867c0] font-bold text-xs flex items-center justify-center shrink-0 border border-blue-200">
                                                    <?= $initials ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-slate-900 text-xs sm:text-sm leading-tight truncate"><?= htmlspecialchars($a['nama_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">NPM: <?= htmlspecialchars($a['npm_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Deskripsi Kegiatan -->
                                        <td class="px-3.5 py-3 max-w-xs">
                                            <p class="text-xs text-slate-700 line-clamp-2" title="<?= htmlspecialchars($a['deskripsi_tugas'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($a['deskripsi_tugas'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                            <?php if (!empty($a['pesan_dosen'])): ?>
                                                <div class="mt-1 flex items-start gap-1 text-[10px] text-blue-700 bg-blue-50/70 p-1 rounded border border-blue-100">
                                                    <span class="font-bold shrink-0">Catatan Dosen:</span>
                                                    <span class="italic truncate"><?= htmlspecialchars($a['pesan_dosen'], ENT_QUOTES, 'UTF-8') ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Bukti Foto -->
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center justify-center gap-1.5">
                                                <?php if (!empty($a['foto_kegiatan'])): ?>
                                                    <?php
                                                    $kegiatanSrc = (str_starts_with($a['foto_kegiatan'], 'http') || str_starts_with($a['foto_kegiatan'], '/'))
                                                        ? $a['foto_kegiatan']
                                                        : \Core\Guard::url('/uploads/absensi/' . $a['foto_kegiatan']);
                                                    ?>
                                                    <button type="button"
                                                        onclick="previewImage('<?= htmlspecialchars($kegiatanSrc, ENT_QUOTES, 'UTF-8') ?>', 'Bukti Kegiatan Praktikum — <?= htmlspecialchars(addslashes($a['nama_asdos']), ENT_QUOTES, 'UTF-8') ?>')"
                                                        title="Lihat Foto Kegiatan"
                                                        class="px-2 py-1 rounded-md bg-slate-100 hover:bg-blue-50 hover:text-[#1867c0] border border-slate-300 text-[11px] font-semibold transition flex items-center gap-1 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span>Kegiatan</span>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-slate-400 text-xs italic">Tanpa Foto</span>
                                                <?php endif; ?>

                                                <?php if (!empty($a['foto_selfie'])): ?>
                                                    <?php
                                                    $selfieSrc = (str_starts_with($a['foto_selfie'], 'http') || str_starts_with($a['foto_selfie'], '/'))
                                                        ? $a['foto_selfie']
                                                        : \Core\Guard::url('/uploads/absensi/' . $a['foto_selfie']);
                                                    ?>
                                                    <button type="button"
                                                        onclick="previewImage('<?= htmlspecialchars($selfieSrc, ENT_QUOTES, 'UTF-8') ?>', 'Foto Selfie — <?= htmlspecialchars(addslashes($a['nama_asdos']), ENT_QUOTES, 'UTF-8') ?>')"
                                                        title="Lihat Foto Selfie Kehadiran"
                                                        class="px-2 py-1 rounded-md bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 border border-slate-300 text-[11px] font-semibold transition flex items-center gap-1 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        <span>Selfie</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Status Verifikasi -->
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border <?= $statusBadge['bg'] ?> <?= $statusBadge['text'] ?> <?= $statusBadge['border'] ?> inline-flex items-center gap-1.5 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $statusBadge['dot'] ?>"></span>
                                                <?= $statusBadge['label'] ?>
                                            </span>
                                        </td>

                                        <!-- Aksi Dosen -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center justify-center gap-1.5">
                                                <button type="button"
                                                    onclick="openVerificationModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)"
                                                    title="Verifikasi Absensi Praktikum"
                                                    class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-800 text-xs font-bold border border-amber-300 hover:border-amber-600 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span>Verifikasi</span>
                                                </button>

                                                <button type="button"
                                                    onclick="openDetailModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)"
                                                    title="Lihat Detail Lengkap Absensi"
                                                    class="px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-[#1867c0] hover:text-white text-[#1867c0] text-xs font-bold border border-blue-200 hover:border-[#1867c0] transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span>Detail</span>
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="<?= empty($absensiList) ? 'block' : 'hidden' ?> p-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-3 border border-blue-200 shadow-2xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Belum Ada Presensi yang Sesuai Filter</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Tidak ditemukan presensi praktikum sesuai kata kunci atau status yang Anda cari.
                    </p>
                    <button type="button" onclick="resetAllFilters()" class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition border border-slate-300 cursor-pointer shadow-2xs">
                        Bersihkan Filter
                    </button>
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
                    <span class="font-bold text-slate-800"><?= htmlspecialchars($namaMatkul, ENT_QUOTES, 'UTF-8') ?></span>
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
                <input type="hidden" name="redirect_to" value="<?= \Core\Guard::url('/dosen/matkul/' . $matkulId) ?>">

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
                            placeholder="Tuliskan masukan atau alasan persetujuan/penolakan untuk asdos..."
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
    <!-- MODAL: DETAIL LENGKAP ABSENSI                                             -->
    <!-- ========================================================================= -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1867c0] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Rincian Lengkap Presensi</h3>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Asisten Dosen</span>
                        <p class="font-bold text-slate-900 mt-0.5" id="detailAsdos">-</p>
                        <p class="text-[11px] text-slate-500 font-mono" id="detailNpm">-</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Pertemuan & Tanggal</span>
                        <p class="font-bold text-slate-900 mt-0.5" id="detailPertemuan">-</p>
                        <p class="text-[11px] text-slate-500" id="detailTanggal">-</p>
                    </div>
                </div>

                <div>
                    <span class="text-slate-500 font-bold block mb-1">Deskripsi Tugas / Kegiatan Praktikum:</span>
                    <div class="p-3 bg-white rounded-xl border border-slate-200 text-slate-700 leading-relaxed max-h-32 overflow-y-auto" id="detailDeskripsi">
                        -
                    </div>
                </div>

                <div id="detailCatatanDosenWrap" class="hidden">
                    <span class="text-slate-500 font-bold block mb-1">Catatan dari Dosen:</span>
                    <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-200 text-blue-900 text-xs italic" id="detailCatatanDosen">
                        -
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-xl shadow-xs transition cursor-pointer">
                    Tutup Rincian
                </button>
            </div>
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

    <!-- Interactive Filter & Modal Scripts -->
    <script>
        // Modal Verifikasi
        function openVerificationModal(data) {
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

        // Modal Detail
        function openDetailModal(data) {
            document.getElementById('detailAsdos').textContent = data.nama_asdos || '-';
            document.getElementById('detailNpm').textContent = 'NPM: ' + (data.npm_asdos || '-');
            document.getElementById('detailPertemuan').textContent = 'Pertemuan ke-' + (data.pertemuan_ke || '1');
            document.getElementById('detailTanggal').textContent = data.tanggal || '-';
            document.getElementById('detailDeskripsi').textContent = data.deskripsi_tugas || 'Tidak ada catatan tugas/deskripsi.';

            const catatanWrap = document.getElementById('detailCatatanDosenWrap');
            const catatanText = document.getElementById('detailCatatanDosen');
            if (data.pesan_dosen && data.pesan_dosen.trim() !== '') {
                catatanText.textContent = data.pesan_dosen;
                catatanWrap.classList.remove('hidden');
            } else {
                catatanWrap.classList.add('hidden');
            }

            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Image Preview Modal
        function previewImage(src, title) {
            document.getElementById('previewImg').src = src;
            document.getElementById('previewTitle').textContent = title || 'Preview Foto';
            document.getElementById('imagePreviewModal').classList.remove('hidden');
        }

        function closePreviewImage() {
            document.getElementById('imagePreviewModal').classList.add('hidden');
        }

        // Client-side Instant Filtering
        const searchInput  = document.getElementById('searchInput');
        const clearBtn     = document.getElementById('clearSearchBtn');
        const statusFilter = document.getElementById('statusFilter');
        const tableBody    = document.getElementById('absensiTableBody');
        const emptyState   = document.getElementById('emptyState');
        const rows         = document.querySelectorAll('.absensi-row');

        function applyFilters() {
            const query       = (searchInput.value || '').toLowerCase().trim();
            const selStatus   = (statusFilter.value || '').toLowerCase().trim();

            if (clearBtn) {
                clearBtn.classList.toggle('hidden', query === '');
            }

            let visibleCount = 0;

            rows.forEach(row => {
                const asdosNama = (row.getAttribute('data-asdos-nama') || '').toLowerCase();
                const asdosNpm  = (row.getAttribute('data-asdos-npm') || '').toLowerCase();
                const deskripsi = (row.getAttribute('data-deskripsi') || '').toLowerCase();
                const status    = (row.getAttribute('data-status') || '').toLowerCase();

                const matchQuery  = query === '' || asdosNama.includes(query) || asdosNpm.includes(query) || deskripsi.includes(query);
                const matchStatus = selStatus === '' || status === selStatus;

                if (matchQuery && matchStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (emptyState) {
                emptyState.classList.toggle('hidden', visibleCount > 0);
            }
        }

        function clearSearch() {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        }

        function resetAllFilters() {
            searchInput.value = '';
            statusFilter.value = '';
            applyFilters();
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            const verifyModal = document.getElementById('verificationModal');
            const detailModal = document.getElementById('detailModal');
            const previewModal = document.getElementById('imagePreviewModal');

            if (e.target === verifyModal) closeVerificationModal();
            if (e.target === detailModal) closeDetailModal();
            if (e.target === previewModal) closePreviewImage();
        });

        // Close on Escape key
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVerificationModal();
                closeDetailModal();
                closePreviewImage();
            }
        });
    </script>
</body>
</html>
