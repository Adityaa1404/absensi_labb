<?php
// Fallback & Dokumentasi Variabel dari SuperAdminController
$currentUser  = $currentUser ?? \Core\Guard::user();
$metrics      = $metrics ?? [
    'total'         => 0,
    'active'        => 0,
    'inactive'      => 0,
    'asdos_terplot' => 0,
];
$plottingList = $plottingList ?? [];
$matkulList   = $matkulList ?? [];
$asdosList    = $asdosList ?? [];
$filters      = $filters ?? [
    'search'    => '',
    'matkul_id' => '',
    'asdos_id'  => '',
    'is_active' => '',
];
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plotting Asisten Dosen — Absensi Lab</title>

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
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Plotting & Penugasan Asdos</h1>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openCreatePlottingModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 shadow-xs hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Plotting Baru</span>
                </button>
            </div>
        </div>

        <!-- Metric Cards (4 Columns with Lift Hover) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-4">

            <!-- 1. Total Penugasan -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Penugasan</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= $metrics['total'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>

            <!-- 2. Plotting Aktif -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-emerald-200/80 bg-emerald-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-emerald-400">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Plotting Aktif</p>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-800 mt-1"><?= $metrics['active'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- 3. Plotting Selesai / Nonaktif -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Plotting Selesai</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-700 mt-1"><?= $metrics['inactive'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- 4. Asdos Terplot -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-blue-200/80 bg-blue-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-400">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#1867c0]">Asdos Terplot</p>
                    <p class="text-2xl sm:text-3xl font-bold text-[#1867c0] mt-1"><?= $metrics['asdos_terplot'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

        </div>

        <!-- Content Card: Toolbar & Plotting Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">

            <!-- Toolbar -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/60 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Penugasan Asisten Dosen</h2>
                    </div>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
                    <!-- Search Input -->
                    <div class="sm:col-span-5 relative">
                        <label for="searchInput" class="block text-xs font-bold text-slate-700 mb-1">Cari Penugasan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="searchInput"
                                placeholder="Ketik nama asdos, NPM, kode matkul, atau nama matkul..."
                                value="<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 shadow-2xs">
                            <button type="button" id="clearSearchBtn" onclick="clearSearch()" title="Hapus teks pencarian" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Filter Matkul -->
                    <div class="sm:col-span-3">
                        <label for="matkulFilter" class="block text-xs font-bold text-slate-700 mb-1">Saring Berdasarkan Matkul</label>
                        <select id="matkulFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                            <option value="">Semua Mata Kuliah</option>
                            <?php foreach ($matkulList as $m): ?>
                                <option value="<?= $m['id_matkul'] ?>" <?= $filters['matkul_id'] == $m['id_matkul'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="sm:col-span-3">
                        <label for="statusFilter" class="block text-xs font-bold text-slate-700 mb-1">Status Penugasan</label>
                        <select id="statusFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                            <option value="">Semua Status Plotting</option>
                            <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : '' ?>>Sedang Aktif Mengajar</option>
                            <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : '' ?>>Sudah Selesai (Nonaktif)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 border-collapse" id="plottingTable">
                    <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5">Asisten Dosen (Asdos)</th>
                            <th class="px-3.5 py-3.5">Mata Kuliah & Dosen Pengampu</th>
                            <th class="px-3.5 py-3.5">Periode Mengajar</th>
                            <th class="px-3 py-3.5 text-center">Status Plotting</th>
                            <th class="px-3.5 py-3.5 text-center">Pilihan Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" id="plottingTableBody">
                        <?php if (!empty($plottingList)): ?>
                            <?php foreach ($plottingList as $p): ?>
                                <?php
                                $isActive = (int)$p['is_active'] === 1;
                                $words = explode(' ', trim($p['nama_asdos'] ?? 'Asdos'));
                                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                ?>
                                <tr class="hover:bg-blue-50/40 transition-colors duration-150 plotting-row"
                                    data-id="<?= $p['id_plotting'] ?>"
                                    data-asdos-id="<?= $p['asdos_id'] ?>"
                                    data-asdos-nama="<?= htmlspecialchars($p['nama_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-asdos-npm="<?= htmlspecialchars($p['npm_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-matkul-id="<?= $p['matkul_id'] ?>"
                                    data-matkul-nama="<?= htmlspecialchars($p['nama_matkul'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-dosen-nama="<?= htmlspecialchars($p['nama_dosen'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-mulai="<?= $p['periode_mulai'] ?>"
                                    data-selesai="<?= $p['periode_selesai'] ?>"
                                    data-active="<?= $p['is_active'] ?>">

                                    <!-- 1. Asdos -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 text-[#1867c0] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                <?= $initials ?>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 text-xs sm:text-sm leading-tight"><?= htmlspecialchars($p['nama_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                <p class="text-[11px] text-slate-500 font-mono mt-0.5">NPM: <?= htmlspecialchars($p['npm_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Matkul & Dosen -->
                                    <td class="px-3.5 py-3">
                                        <div class="space-y-0.5">
                                            <p class="font-bold text-slate-900 text-xs sm:text-sm"><?= htmlspecialchars($p['nama_matkul'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-[11px] text-slate-600">
                                                Dosen Pembimbing: <strong><?= htmlspecialchars($p['nama_dosen'] ?? 'Belum ditentukan', ENT_QUOTES, 'UTF-8') ?></strong>
                                            </p>
                                        </div>
                                    </td>

                                    <!-- 3. Periode Penugasan -->
                                    <td class="px-3.5 py-3 whitespace-nowrap text-xs text-slate-700">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="font-medium">
                                                <?= date('d M Y', strtotime($p['periode_mulai'])) ?> – <?= date('d M Y', strtotime($p['periode_selesai'])) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 4. Status -->
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <?php if ($isActive): ?>
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border bg-emerald-50 text-emerald-800 border-emerald-300 inline-flex items-center gap-1.5 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                AKTIF MENGAJAR
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border bg-slate-100 text-slate-700 border-slate-300 inline-flex items-center gap-1.5 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                SELESAI
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 5. Aksi -->
                                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1.5">

                                            <!-- Edit -->
                                            <button type="button" onclick="openEditPlottingModal(this.closest('tr'))" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-800 text-slate-700 text-xs font-bold border border-slate-300 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Hapus -->
                                            <button type="button" onclick="openDeletePlottingModal(<?= $p['id_plotting'] ?>, '<?= htmlspecialchars(addslashes($p['nama_asdos'] . ' - ' . $p['nama_matkul']), ENT_QUOTES, 'UTF-8') ?>')" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white text-red-600 text-xs font-bold border border-red-200 hover:border-red-600 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus</span>
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
            <div id="emptyState" class="<?= empty($plottingList) ? 'block' : 'hidden' ?> p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-4 border border-blue-200 shadow-2xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Penugasan Asdos</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                    Belum ada asisten dosen yang diplotkan ke mata kuliah. Silakan klik tombol di bawah untuk menugaskan asdos pertama.
                </p>
                <button type="button" onclick="openCreatePlottingModal()" class="mt-4 px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Plotting Asdos Pertama</span>
                </button>
            </div>
        </div>

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: TAMBAH PLOTTING                                                    -->
    <!-- ========================================================================= -->
    <div id="createPlottingModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transition-all transform animate-in fade-in duration-200">

            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Plotting Asisten Dosen Baru</h3>
                    </div>
                </div>
                <button type="button" onclick="closeCreatePlottingModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="<?= \Core\Guard::url('/superadmin/plotting/create') ?>" method="POST" class="p-6 space-y-4">
                <?= \Core\Guard::csrfField() ?>

                <div>
                    <label for="create_matkul_id" class="block text-xs font-bold text-slate-800 mb-1">
                        Pilih Mata Kuliah Praktikum <span class="text-red-500">*</span>
                    </label>
                    <select id="create_matkul_id" name="matkul_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php foreach ($matkulList as $m): ?>
                            <option value="<?= $m['id_matkul'] ?>">
                                <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="create_asdos_id" class="block text-xs font-bold text-slate-800 mb-1">
                        Pilih Asisten Dosen <span class="text-red-500">*</span>
                    </label>
                    <select id="create_asdos_id" name="asdos_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer">
                        <option value="">-- Pilih Asdos --</option>
                        <?php foreach ($asdosList as $a): ?>
                            <option value="<?= $a['id_user'] ?>">
                                <?= htmlspecialchars($a['nama'], ENT_QUOTES, 'UTF-8') ?> (NPM: <?= htmlspecialchars($a['identity_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_periode_mulai" class="block text-xs font-bold text-slate-800 mb-1">
                            Tanggal Mulai Mengajar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="create_periode_mulai" name="periode_mulai" required value="<?= date('Y-m-d') ?>"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                    </div>
                    <div>
                        <label for="create_periode_selesai" class="block text-xs font-bold text-slate-800 mb-1">
                            Tanggal Selesai Mengajar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="create_periode_selesai" name="periode_selesai" required value="<?= date('Y-m-d', strtotime('+6 months')) ?>"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-slate-800">Status Penugasan Langsung Aktif</span>
                            <p class="text-xs text-slate-500">Asdos dapat langsung mencatat absensi di portalnya.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeCreatePlottingModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Plotting</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: EDIT PLOTTING                                                      -->
    <!-- ========================================================================= -->
    <div id="editPlottingModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transition-all transform animate-in fade-in duration-200">

            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Perbarui Data Plotting</h3>
                        <p class="text-xs text-slate-500" id="edit_plotting_subtitle">Edit mata kuliah & periode</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditPlottingModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="editPlottingForm" action="" method="POST" class="p-6 space-y-4">
                <?= \Core\Guard::csrfField() ?>

                <div>
                    <label for="edit_matkul_id" class="block text-xs font-bold text-slate-800 mb-1">
                        Mata Kuliah Praktikum <span class="text-red-500">*</span>
                    </label>
                    <select id="edit_matkul_id" name="matkul_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php foreach ($matkulList as $m): ?>
                            <option value="<?= $m['id_matkul'] ?>">
                                <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="edit_asdos_id" class="block text-xs font-bold text-slate-800 mb-1">
                        Asisten Dosen <span class="text-red-500">*</span>
                    </label>
                    <select id="edit_asdos_id" name="asdos_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer">
                        <option value="">-- Pilih Asdos --</option>
                        <?php foreach ($asdosList as $a): ?>
                            <option value="<?= $a['id_user'] ?>">
                                <?= htmlspecialchars($a['nama'], ENT_QUOTES, 'UTF-8') ?> (NPM: <?= htmlspecialchars($a['identity_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_periode_mulai" class="block text-xs font-bold text-slate-800 mb-1">
                            Tanggal Mulai Mengajar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="edit_periode_mulai" name="periode_mulai" required
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                    </div>
                    <div>
                        <label for="edit_periode_selesai" class="block text-xs font-bold text-slate-800 mb-1">
                            Tanggal Selesai Mengajar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="edit_periode_selesai" name="periode_selesai" required
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                    </div>
                </div>



                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeEditPlottingModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: HAPUS PLOTTING                                                     -->
    <!-- ========================================================================= -->
    <div id="deletePlottingModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transition-all transform animate-in fade-in duration-200">

            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 border border-red-300 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-slate-900">Hapus Penugasan Plotting?</h3>

                <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus penugasan <strong><span id="deletePlottingName"></span></strong>?
                </p>

                <form id="deletePlottingForm" action="" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    <?= \Core\Guard::csrfField() ?>
                    <button type="button" onclick="closeDeletePlottingModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Ya, Hapus</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Floating Bottom Navigation Bar (Mobile) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>

    <!-- JavaScript Logic -->
    <script>
        const BASE_URL = '<?= \Core\Guard::url('') ?>';

        // Filter Logic
        const searchInput = document.getElementById('searchInput');
        const matkulFilter = document.getElementById('matkulFilter');
        const statusFilter = document.getElementById('statusFilter');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const emptyState = document.getElementById('emptyState');
        const displayedCount = document.getElementById('displayedCount');

        function applyFilters() {
            const query = searchInput.value.toLowerCase().trim();
            const matkulId = matkulFilter.value;
            const status = statusFilter.value;

            if (query.length > 0) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }

            const rows = document.querySelectorAll('.plotting-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const asdosNama = (row.dataset.asdosNama || '').toLowerCase();
                const asdosNpm = (row.dataset.asdosNpm || '').toLowerCase();
                const matkulNama = (row.dataset.matkulNama || '').toLowerCase();
                const rowMatkul = row.dataset.matkulId || '';
                const rowActive = row.dataset.active || '';

                const matchSearch = query === '' ||
                    asdosNama.includes(query) ||
                    asdosNpm.includes(query) ||
                    matkulNama.includes(query);

                const matchMatkul = matkulId === '' || rowMatkul === matkulId;
                const matchStatus = status === '' || rowActive === status;

                if (matchSearch && matchMatkul && matchStatus) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            displayedCount.textContent = visibleCount;

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
            matkulFilter.value = '';
            statusFilter.value = '';
            applyFilters();
        }

        // Modal Create
        function openCreatePlottingModal() {
            document.getElementById('createPlottingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreatePlottingModal() {
            document.getElementById('createPlottingModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Modal Edit
        function openEditPlottingModal(row) {
            if (!row) return;
            const id = row.dataset.id;
            const matkulId = row.dataset.matkulId;
            const asdosId = row.dataset.asdosId;
            const mulai = row.dataset.mulai;
            const selesai = row.dataset.selesai;
            const active = row.dataset.active === '1';

            document.getElementById('editPlottingForm').action = `${BASE_URL}/superadmin/plotting/${id}/update`;
            document.getElementById('edit_matkul_id').value = matkulId;
            document.getElementById('edit_asdos_id').value = asdosId;
            document.getElementById('edit_periode_mulai').value = mulai;
            document.getElementById('edit_periode_selesai').value = selesai;

            document.getElementById('edit_plotting_subtitle').textContent = `Mengedit Plotting #${id}`;

            document.getElementById('editPlottingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditPlottingModal() {
            document.getElementById('editPlottingModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Modal Delete
        function openDeletePlottingModal(id, nama) {
            document.getElementById('deletePlottingForm').action = `${BASE_URL}/superadmin/plotting/${id}/delete`;
            document.getElementById('deletePlottingName').textContent = nama;
            document.getElementById('deletePlottingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeletePlottingModal() {
            document.getElementById('deletePlottingModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Escape Key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreatePlottingModal();
                closeEditPlottingModal();
                closeDeletePlottingModal();
            }
        });
    </script>
</body>

</html>