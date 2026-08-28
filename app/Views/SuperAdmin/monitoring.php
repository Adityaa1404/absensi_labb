<?php
// Fallback & Dokumentasi Variabel dari SuperAdminController
$currentUser = $currentUser ?? \Core\Guard::user();
$metrics     = $metrics ?? [
    'total'     => 0,
    'disetujui' => 0,
    'pending'   => 0,
    'ditolak'   => 0,
];
$absensiList = $absensiList ?? [];
$matkulList  = $matkulList ?? [];
$filters     = $filters ?? [
    'search'            => '',
    'status_verifikasi' => '',
    'matkul_id'         => '',
    'date_start'        => '',
    'date_end'          => '',
];
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Absensi & Verifikasi — Absensi Lab</title>

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

    <!-- Header / Navbar (Unified Desktop Navbar) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

        <!-- Page Header Banner -->
        <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Monitoring Seluruh Absensi & Verifikasi</h1>
            </div>
        </div>

        <!-- Metric / Stat Cards Grid (4 Columns with Lift Hover) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-4">

            <!-- 1. Total Absensi -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Absensi</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= $metrics['total'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#1867c0] flex items-center justify-center border border-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>

            <!-- 2. Disetujui Dosen -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-emerald-200/80 bg-emerald-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-emerald-400">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Disetujui Dosen</p>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-800 mt-1"><?= $metrics['disetujui'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- 3. Menunggu Verifikasi (Pending) -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border <?= $metrics['pending'] > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200' ?> shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider <?= $metrics['pending'] > 0 ? 'text-amber-700' : 'text-slate-500' ?>">Menunggu Review</p>
                    <p class="text-2xl sm:text-3xl font-bold <?= $metrics['pending'] > 0 ? 'text-amber-800' : 'text-slate-700' ?> mt-1"><?= $metrics['pending'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl <?= $metrics['pending'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' ?> flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- 4. Ditolak Dosen -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border <?= $metrics['ditolak'] > 0 ? 'border-red-200 bg-red-50/20' : 'border-slate-200' ?> shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider <?= $metrics['ditolak'] > 0 ? 'text-red-700' : 'text-slate-500' ?>">Ditolak Dosen</p>
                    <p class="text-2xl sm:text-3xl font-bold <?= $metrics['ditolak'] > 0 ? 'text-red-800' : 'text-slate-700' ?> mt-1"><?= $metrics['ditolak'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl <?= $metrics['ditolak'] > 0 ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' ?> flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

        </div>

        <!-- Content Card: Toolbar Filter & Monitoring Data Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">

            <!-- Card Header with Multi-Criteria Filters -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/60 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Rekapitulasi Seluruh Absensi Praktikum</h2>
                    </div>
                </div>

                <!-- Interactive Filters & Search Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">

                    <!-- Search Input -->
                    <div class="sm:col-span-5 relative">
                        <label for="searchInput" class="block text-xs font-bold text-slate-700 mb-1">Cari Absensi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="searchInput"
                                placeholder="Ketik nama asdos, NPM, matkul, atau dosen..."
                                class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 shadow-2xs">
                            <button type="button" id="clearSearchBtn" onclick="clearSearch()" title="Hapus teks pencarian" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Status Verifikasi Filter -->
                    <div class="sm:col-span-3">
                        <label for="statusFilter" class="block text-xs font-bold text-slate-700 mb-1">Status Verifikasi</label>
                        <select id="statusFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                            <option value="">Semua Status Verifikasi</option>
                            <option value="disetujui">Disetujui Dosen</option>
                            <option value="pending">Menunggu Verifikasi (Pending)</option>
                            <option value="ditolak">Ditolak Dosen</option>
                        </select>
                    </div>

                    <!-- Matkul Filter -->
                    <div class="sm:col-span-3">
                        <label for="matkulFilter" class="block text-xs font-bold text-slate-700 mb-1">Mata Kuliah</label>
                        <select id="matkulFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                            <option value="">Semua Mata Kuliah</option>
                            <?php foreach ($matkulList as $m): ?>
                                <option value="<?= $m['id_matkul'] ?>">
                                    <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Table Responsive Container (Auto-fitting without horizontal scroll on desktop) -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 border-collapse" id="monitoringTable">
                    <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5">Tanggal & Pertemuan</th>
                            <th class="px-3.5 py-3.5">Asisten Dosen</th>
                            <th class="px-3.5 py-3.5">Mata Kuliah & Dosen Pengampu</th>
                            <th class="px-3 py-3.5 text-center">Bukti Foto</th>
                            <th class="px-3 py-3.5 text-center">Status Verifikasi</th>
                            <th class="px-3.5 py-3.5 text-center">Detail Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" id="monitoringTableBody">
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
                                <tr class="hover:bg-blue-50/40 transition-colors duration-150 monitoring-row"
                                    data-id="<?= $a['id_absensi'] ?>"
                                    data-asdos-nama="<?= htmlspecialchars($a['nama_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-asdos-npm="<?= htmlspecialchars($a['npm_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-matkul-id="<?= $a['id_matkul'] ?? '' ?>"
                                    data-matkul-nama="<?= htmlspecialchars($a['nama_matkul'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-dosen-nama="<?= htmlspecialchars($a['nama_dosen'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-status="<?= $status ?>"
                                    data-tanggal="<?= $a['tanggal'] ?? '' ?>"
                                    data-deskripsi="<?= htmlspecialchars($a['deskripsi_tugas'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                                    <!-- 1. Tanggal & Pertemuan -->
                                    <td class="px-4 py-3">
                                        <div>
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
                                                <?php if (!empty($a['jam_mulai']) && !empty($a['jam_selesai'])): ?>
                                                    <span><?= substr($a['jam_mulai'], 0, 5) ?> - <?= substr($a['jam_selesai'], 0, 5) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Asisten Dosen -->
                                    <td class="px-3.5 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1867c0] font-bold text-xs flex items-center justify-center shrink-0 border border-blue-200 shadow-2xs">
                                                <?= $initials ?>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 text-xs sm:text-sm leading-tight"><?= htmlspecialchars($a['nama_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                <p class="text-[11px] text-slate-500 font-mono mt-0.5">NPM: <?= htmlspecialchars($a['npm_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 3. Mata Kuliah & Dosen Pengampu -->
                                    <td class="px-3.5 py-3">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-slate-900 text-xs sm:text-sm"><?= htmlspecialchars($a['nama_matkul'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                            </div>
                                            <p class="text-[11px] text-slate-600 mt-1">
                                                Dosen: <strong><?= htmlspecialchars($a['nama_dosen'] ?? 'Belum ditentukan', ENT_QUOTES, 'UTF-8') ?></strong>
                                            </p>
                                        </div>
                                    </td>

                                    <!-- 4. Bukti Foto (Kegiatan & Selfie) -->
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <?php if (!empty($a['foto_kegiatan'])): ?>
                                                <button type="button"
                                                    onclick="previewImage('<?= htmlspecialchars($a['foto_kegiatan'], ENT_QUOTES, 'UTF-8') ?>', 'Bukti Praktikum — <?= htmlspecialchars(addslashes($a['nama_asdos']), ENT_QUOTES, 'UTF-8') ?>')"
                                                    title="Lihat Foto Kegiatan Praktikum"
                                                    class="px-2 py-1 rounded-md bg-slate-100 hover:bg-blue-50 hover:text-[#1867c0] border border-slate-300 text-[11px] font-semibold transition flex items-center gap-1 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>Foto</span>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-xs italic">Tanpa Foto</span>
                                            <?php endif; ?>

                                            <?php if (!empty($a['foto_selfie'])): ?>
                                                <button type="button"
                                                    onclick="previewImage('<?= htmlspecialchars($a['foto_selfie'], ENT_QUOTES, 'UTF-8') ?>', 'Foto Selfie — <?= htmlspecialchars(addslashes($a['nama_asdos']), ENT_QUOTES, 'UTF-8') ?>')"
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

                                    <!-- 5. Status Verifikasi Dosen -->
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border <?= $statusBadge['bg'] ?> <?= $statusBadge['text'] ?> <?= $statusBadge['border'] ?> inline-flex items-center gap-1.5 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $statusBadge['dot'] ?>"></span>
                                            <?= $statusBadge['label'] ?>
                                        </span>
                                    </td>

                                    <!-- 6. Detail Audit -->
                                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                        <button type="button"
                                            onclick="openDetailModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)"
                                            class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-[#1867c0] hover:text-white text-[#1867c0] text-xs font-bold border border-blue-200 hover:border-[#1867c0] transition-all duration-150 inline-flex items-center gap-1.5 cursor-pointer shadow-2xs active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Detail</span>
                                        </button>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="<?= empty($absensiList) ? 'block' : 'hidden' ?> p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-4 border border-blue-200 shadow-2xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Laporan Absensi yang Sesuai</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                    Tidak ditemukan data absensi praktikum untuk kriteria filter yang Anda pilih. Silakan bersihkan filter untuk menampilkan seluruh riwayat.
                </p>
                <button type="button" onclick="resetAllFilters()" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition border border-slate-300 shadow-2xs cursor-pointer">
                    <span>Tampilkan Semua Absensi</span>
                </button>
            </div>
        </div>

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: DETAIL LENGKAP ABSENSI & VERIFIKASI                                -->
    <!-- ========================================================================= -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden transition-all transform animate-in fade-in duration-200 my-8">

            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900" id="detail_modal_title">Detail Absensi Praktikum</h3>
                        <p class="text-xs text-slate-500" id="detail_modal_subtitle">Audit dokumen absensi & verifikasi</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                <!-- Status & Pertemuan Header Box -->
                <div class="p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 border-slate-200" id="detail_status_box">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">Status Verifikasi Dosen:</p>
                        <p class="text-sm font-bold text-slate-900 uppercase mt-0.5" id="detail_status_text">-</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs text-slate-500 font-medium">Pertemuan & Tanggal:</p>
                        <p class="text-sm font-bold text-slate-900 mt-0.5" id="detail_pertemuan_text">-</p>
                    </div>
                </div>

                <!-- Info Asdos & Dosen Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Box Asdos -->
                    <div class="p-3.5 bg-white border border-slate-200 rounded-xl space-y-1.5 shadow-2xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Asisten Dosen Pelaksana</span>
                        <p class="font-bold text-slate-900 text-sm" id="detail_asdos_nama">-</p>
                        <p class="text-xs text-slate-500" id="detail_asdos_npm">NPM: -</p>
                    </div>

                    <!-- Box Dosen & Matkul -->
                    <div class="p-3.5 bg-white border border-slate-200 rounded-xl space-y-1.5 shadow-2xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700">Mata Kuliah & Dosen</span>
                        <p class="font-bold text-slate-900 text-sm" id="detail_matkul_nama">-</p>
                        <p class="text-xs text-slate-500" id="detail_dosen_nama">Dosen: -</p>
                    </div>

                </div>

                <!-- Jam Pelaksanaan & Jam Submit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                        <span class="font-bold text-slate-700">Jam Pelaksanaan Praktikum:</span>
                        <p class="text-slate-900 font-medium" id="detail_jam_text">-</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                        <span class="font-bold text-slate-700">Waktu Submit (Server UTC+7):</span>
                        <p class="text-slate-900 font-mono" id="detail_timestamp_text">-</p>
                    </div>
                </div>

                <!-- Deskripsi Tugas Praktikum -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1">Deskripsi Materi & Pelaksanaan Tugas:</label>
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line" id="detail_deskripsi_text">
                        -
                    </div>
                </div>

                <!-- Catatan / Pesan Dosen -->
                <div id="detail_pesan_wrapper" class="hidden">
                    <label class="block text-xs font-bold text-slate-800 mb-1">Catatan / Evaluasi dari Dosen Pengampu:</label>
                    <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs sm:text-sm text-amber-900 leading-relaxed" id="detail_pesan_text">
                        -
                    </div>
                </div>

                <!-- Bukti Foto Kegiatan & Selfie -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-2">Dokumentasi Bukti Foto:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <!-- Foto Kegiatan -->
                        <div class="border border-slate-200 rounded-xl p-3 bg-slate-50 text-center">
                            <p class="text-xs font-bold text-slate-700 mb-2">Foto Bukti Praktikum (Wajib)</p>
                            <div id="detail_kegiatan_img_wrapper" class="w-full h-44 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center border border-slate-300">
                                <span class="text-xs text-slate-400">Tidak ada foto</span>
                            </div>
                        </div>

                        <!-- Foto Selfie -->
                        <div class="border border-slate-200 rounded-xl p-3 bg-slate-50 text-center">
                            <p class="text-xs font-bold text-slate-700 mb-2">Foto Selfie / Presensi</p>
                            <div id="detail_selfie_img_wrapper" class="w-full h-44 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center border border-slate-300">
                                <span class="text-xs text-slate-400">Tidak ada foto</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer shadow-xs">
                    Tutup Detail
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: IMAGE PREVIEW FULL RESOLUTION                                      -->
    <!-- ========================================================================= -->
    <div id="imagePreviewModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeImagePreview()">
        <div class="relative max-w-3xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <h4 class="text-xs sm:text-sm font-bold text-slate-800" id="imagePreviewTitle">Pratinjau Foto Bukti</h4>
                <button type="button" onclick="closeImagePreview()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 flex items-center justify-center bg-slate-900 max-h-[75vh] overflow-auto">
                <img id="imagePreviewSrc" src="" alt="Pratinjau Foto" class="max-h-[70vh] w-auto object-contain rounded-lg shadow-md">
            </div>
        </div>
    </div>

    <!-- Floating Bottom Navigation Bar (Mobile) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>

    <!-- JavaScript Handlers -->
    <script>
        const BASE_URL = '<?= \Core\Guard::url('') ?>';

        // ---------------------------------------------------------------------
        // 1. Live Filter & Instant Search Logic (Tanpa Reload / Scroll Jump)
        // ---------------------------------------------------------------------
        const searchInput    = document.getElementById('searchInput');
        const statusFilter   = document.getElementById('statusFilter');
        const matkulFilter   = document.getElementById('matkulFilter');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const emptyState     = document.getElementById('emptyState');

        function applyFilters() {
            const query  = searchInput.value.toLowerCase().trim();
            const status = statusFilter.value;
            const matkul = matkulFilter.value;

            if (query.length > 0) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }

            const rows = document.querySelectorAll('.monitoring-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const asdosNama  = (row.dataset.asdosNama || '').toLowerCase();
                const asdosNpm   = (row.dataset.asdosNpm || '').toLowerCase();
                const matkulNama = (row.dataset.matkulNama || '').toLowerCase();
                const dosenNama  = (row.dataset.dosenNama || '').toLowerCase();
                const deskripsi  = (row.dataset.deskripsi || '').toLowerCase();
                const rowStatus  = row.dataset.status || '';
                const rowMatkul  = row.dataset.matkulId || '';

                const matchSearch = query === '' || 
                    asdosNama.includes(query) || 
                    asdosNpm.includes(query) || 
                    matkulNama.includes(query) || 
                    dosenNama.includes(query) ||
                    deskripsi.includes(query);

                const matchStatus = status === '' || rowStatus === status;
                const matchMatkul = matkul === '' || rowMatkul === matkul;

                if (matchSearch && matchStatus && matchMatkul) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        searchInput.addEventListener('input', applyFilters);

        function clearSearch() {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        }

        function resetAllFilters() {
            searchInput.value = '';
            statusFilter.value = '';
            matkulFilter.value = '';
            applyFilters();
        }

        // ---------------------------------------------------------------------
        // 2. Detail Modal Logic
        // ---------------------------------------------------------------------
        function openDetailModal(data) {
            if (!data) return;

            document.getElementById('detail_modal_title').textContent = `Absensi #${data.id_absensi} — ${data.nama_asdos || ''}`;
            document.getElementById('detail_modal_subtitle').textContent = data.nama_matkul || '';

            // Status Badge
            const statusTextEl = document.getElementById('detail_status_text');
            const statusBoxEl = document.getElementById('detail_status_box');
            if (data.status_verifikasi === 'disetujui') {
                statusTextEl.textContent = 'DISETUJUI DOSEN';
                statusTextEl.className = 'text-sm font-bold text-emerald-800 uppercase mt-0.5';
                statusBoxEl.className = 'p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-emerald-50 border-emerald-300';
            } else if (data.status_verifikasi === 'ditolak') {
                statusTextEl.textContent = 'DITOLAK DOSEN';
                statusTextEl.className = 'text-sm font-bold text-red-800 uppercase mt-0.5';
                statusBoxEl.className = 'p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-red-50 border-red-300';
            } else {
                statusTextEl.textContent = 'MENUNGGU VERIFIKASI (PENDING)';
                statusTextEl.className = 'text-sm font-bold text-amber-900 uppercase mt-0.5';
                statusBoxEl.className = 'p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-amber-50 border-amber-300';
            }

            document.getElementById('detail_pertemuan_text').textContent = `Pertemuan ke-${data.pertemuan_ke || '1'}, ${data.tanggal || ''}`;
            document.getElementById('detail_asdos_nama').textContent = data.nama_asdos || '-';
            document.getElementById('detail_asdos_npm').textContent = `NPM: ${data.npm_asdos || '-'}`;
            document.getElementById('detail_matkul_nama').textContent = data.nama_matkul || '-';
            document.getElementById('detail_dosen_nama').textContent = `Dosen Pengampu: ${data.nama_dosen || '-'}`;

            const jamMulai = data.jam_mulai ? data.jam_mulai.substring(0, 5) : '-';
            const jamSelesai = data.jam_selesai ? data.jam_selesai.substring(0, 5) : '-';
            document.getElementById('detail_jam_text').textContent = `${jamMulai} s/d ${jamSelesai} WIB`;

            document.getElementById('detail_timestamp_text').textContent = data.created_at ? data.created_at : '-';
            document.getElementById('detail_deskripsi_text').textContent = data.deskripsi_tugas || '-';

            // Pesan Dosen
            const pesanWrapper = document.getElementById('detail_pesan_wrapper');
            const pesanText = document.getElementById('detail_pesan_text');
            if (data.pesan_dosen && data.pesan_dosen.trim() !== '') {
                pesanWrapper.classList.remove('hidden');
                pesanText.textContent = data.pesan_dosen;
            } else {
                pesanWrapper.classList.add('hidden');
            }

            // Foto Kegiatan
            const kegiatanWrapper = document.getElementById('detail_kegiatan_img_wrapper');
            if (data.foto_kegiatan) {
                const imgPath = data.foto_kegiatan.startsWith('http') ? data.foto_kegiatan : `${BASE_URL}/uploads/absensi/${data.foto_kegiatan}`;
                kegiatanWrapper.innerHTML = `
                    <img src="${imgPath}" alt="Foto Kegiatan" class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition"
                         onclick="previewImage('${imgPath}', 'Bukti Praktikum — ${data.nama_asdos || ''}')">
                `;
            } else {
                kegiatanWrapper.innerHTML = '<span class="text-xs text-slate-400">Tidak ada foto</span>';
            }

            // Foto Selfie
            const selfieWrapper = document.getElementById('detail_selfie_img_wrapper');
            if (data.foto_selfie) {
                const imgPath = data.foto_selfie.startsWith('http') ? data.foto_selfie : `${BASE_URL}/uploads/absensi/${data.foto_selfie}`;
                selfieWrapper.innerHTML = `
                    <img src="${imgPath}" alt="Foto Selfie" class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition"
                         onclick="previewImage('${imgPath}', 'Foto Selfie — ${data.nama_asdos || ''}')">
                `;
            } else {
                selfieWrapper.innerHTML = '<span class="text-xs text-slate-400">Tidak ada foto selfie</span>';
            }

            document.getElementById('detailModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ---------------------------------------------------------------------
        // 3. Full Image Preview Modal Logic
        // ---------------------------------------------------------------------
        function previewImage(src, title) {
            document.getElementById('imagePreviewSrc').src = src;
            document.getElementById('imagePreviewTitle').textContent = title || 'Pratinjau Foto';
            document.getElementById('imagePreviewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImagePreview() {
            document.getElementById('imagePreviewModal').classList.add('hidden');
            if (document.getElementById('detailModal').classList.contains('hidden')) {
                document.body.style.overflow = '';
            }
        }

        // Close on Escape Key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImagePreview();
                closeDetailModal();
            }
        });
    </script>
</body>

</html>