<?php
// Fallback & Dokumentasi Variabel dari SuperAdminController
$currentUser = $currentUser ?? \Core\Guard::user();
$metrics     = $metrics ?? [
    'total'         => 0,
    'berplot'       => 0,
    'belum_berplot' => 0,
    'total_dosen'   => 0,
];
$matkulList  = $matkulList ?? [];
$dosenList   = $dosenList ?? [];
$asdosList   = $asdosList ?? [];
$filters     = $filters ?? [
    'search'   => '',
    'dosen_id' => '',
];

// Auto-fallback jika $asdosList belum terisi
if (empty($asdosList)) {
    try {
        $asdosList = \Core\Database::fetchAll("
            SELECT id_user, nama, identity_number, email 
            FROM users 
            WHERE role = 'asdos' AND is_active = 1 
            ORDER BY nama ASC
        ");
    } catch (\Throwable $e) {
        $asdosList = [];
    }
}

// Auto-fallback jika $dosenList belum terisi
if (empty($dosenList)) {
    try {
        $dosenList = \Core\Database::fetchAll("
            SELECT id_user, nama, identity_number, email 
            FROM users 
            WHERE role = 'dosen' AND is_active = 1 
            ORDER BY nama ASC
        ");
    } catch (\Throwable $e) {
        $dosenList = [];
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Kuliah & Plotting Asdos — Absensi Lab</title>

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

    <!-- Header / Navbar (Unified Desktop Navbar) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

        <!-- Page Header Banner -->
        <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Mata Kuliah & Plotting Asdos</h1>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openCreateMatkulModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 shadow-xs hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Mata Kuliah</span>
                </button>
            </div>
        </div>

        <!-- Metric Cards (4 Columns with Lift Hover) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-4">
            
            <!-- 1. Total Mata Kuliah -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Mata Kuliah</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= $metrics['total'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>

            <!-- 2. Matkul Berplot -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-emerald-200/80 bg-emerald-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-emerald-400">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Sudah Diplotkan</p>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-800 mt-1"><?= $metrics['berplot'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <!-- 3. Belum Diplotkan -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border <?= $metrics['belum_berplot'] > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200' ?> shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider <?= $metrics['belum_berplot'] > 0 ? 'text-amber-700' : 'text-slate-500' ?>">Belum Diplotkan</p>
                    <p class="text-2xl sm:text-3xl font-bold <?= $metrics['belum_berplot'] > 0 ? 'text-amber-800' : 'text-slate-700' ?> mt-1"><?= $metrics['belum_berplot'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl <?= $metrics['belum_berplot'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' ?> flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>

            <!-- 4. Total Dosen Pengampu -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-indigo-200/80 bg-indigo-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-indigo-400">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-indigo-700">Dosen Pengampu</p>
                    <p class="text-2xl sm:text-3xl font-bold text-indigo-800 mt-1"><?= $metrics['total_dosen'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>

        </div>

        <!-- Main Content Card (Table & Search) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            
            <!-- Filter & Search Toolbar -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3.5">
                
                <!-- Search Input -->
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="searchInput" value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Cari mata kuliah atau dosen..." 
                           class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-9 py-2 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Dosen Filter & Counter -->
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-between sm:justify-end">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan: <span id="displayedCount" class="font-bold text-slate-800"><?= count($matkulList) ?></span> mata kuliah
                    </div>

                    <div class="relative">
                        <select id="dosenFilter" onchange="applyFilters()" class="bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer shadow-2xs">
                            <option value="">Semua Dosen</option>
                            <?php foreach ($dosenList as $d): ?>
                                <option value="<?= $d['id_user'] ?>" <?= ($filters['dosen_id'] ?? '') == $d['id_user'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['nama'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 border-collapse" id="matkulTable">
                    <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5">Nama Mata Kuliah</th>
                            <th class="px-3.5 py-3.5">Dosen Pengampu</th>
                            <th class="px-3 py-3.5 text-center">Status Plotting Asdos</th>
                            <th class="px-3.5 py-3.5">Terdaftar</th>
                            <th class="px-3.5 py-3.5 text-center">Pilihan Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" id="matkulTableBody">
                        <?php if (!empty($matkulList)): ?>
                            <?php foreach ($matkulList as $m): ?>
                                <tr class="hover:bg-blue-50/40 transition-colors duration-150 matkul-row"
                                    data-id="<?= $m['id_matkul'] ?>"
                                    data-nama="<?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-deskripsi="<?= htmlspecialchars($m['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-dosen-id="<?= $m['dosen_id'] ?>"
                                    data-dosen-nama="<?= htmlspecialchars($m['nama_dosen'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    
                                    <!-- 1. Nama Mata Kuliah -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-start gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-200 text-[#1867c0] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                MK
                                            </div>
                                            <div class="min-w-0">
                                                <button type="button" 
                                                    onclick="openPlottingModal(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)"
                                                    title="Klik untuk kelola plotting asdos mata kuliah ini"
                                                    class="text-left font-bold text-slate-900 hover:text-[#1867c0] text-xs sm:text-sm leading-tight transition cursor-pointer">
                                                    <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                                </button>
                                                <?php if (!empty($m['deskripsi'])): ?>
                                                    <p class="text-[11px] text-slate-600 line-clamp-1 mt-1 max-w-md leading-relaxed">
                                                        <?= htmlspecialchars($m['deskripsi'], ENT_QUOTES, 'UTF-8') ?>
                                                    </p>
                                                <?php else: ?>
                                                    <p class="text-[11px] text-slate-400 italic mt-0.5">Belum ada catatan silabus.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Dosen Pengampu -->
                                    <td class="px-3.5 py-3 whitespace-nowrap">
                                        <?php if (!empty($m['nama_dosen'])): ?>
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-xs flex items-center justify-center shadow-2xs">
                                                    D
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900 text-xs sm:text-sm leading-tight"><?= htmlspecialchars($m['nama_dosen'], ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">NIDN: <?= htmlspecialchars($m['nidn_dosen'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 text-[11px] font-semibold bg-red-50 text-red-600 rounded border border-red-200">
                                                Belum Ditentukan
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 3. Status Plotting -->
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <?php $asdosAktifCount = (int)($m['total_asdos_aktif'] ?? 0); ?>
                                        <?php if ($asdosAktifCount > 0): ?>
                                            <button type="button"
                                                onclick="openPlottingModal(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)"
                                                title="Klik untuk melihat & kelola asdos"
                                                class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded-full border bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border-emerald-300 inline-flex items-center gap-1.5 shadow-2xs cursor-pointer transition active:scale-95">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span><?= $asdosAktifCount ?> Asdos Aktif</span>
                                            </button>
                                        <?php else: ?>
                                            <button type="button"
                                                onclick="openPlottingModal(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)"
                                                title="Klik untuk membuat penugasan asdos baru"
                                                class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded-full border bg-amber-50 hover:bg-amber-100 text-amber-800 border-amber-300 transition inline-flex items-center gap-1 shadow-2xs cursor-pointer active:scale-95">
                                                <span>+ Buat Plotting</span>
                                            </button>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 4. Terdaftar -->
                                    <td class="px-3.5 py-3 whitespace-nowrap text-slate-600 text-xs">
                                        <?= !empty($m['created_at']) ? date('d M Y', strtotime($m['created_at'])) : '-' ?>
                                    </td>

                                    <!-- 5. Aksi -->
                                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <!-- Kelola Plotting Asdos -->
                                            <button type="button" 
                                                onclick="openPlottingModal(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)" 
                                                title="Kelola Plotting Asisten Dosen"
                                                class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-bold border border-indigo-200 hover:border-indigo-600 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <span>Kelola Asdos</span>
                                            </button>

                                            <!-- Edit -->
                                            <button type="button" onclick="openEditMatkulModal(this.closest('tr'))" title="Edit Mata Kuliah" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-800 text-slate-700 text-xs font-bold border border-slate-300 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Hapus -->
                                            <button type="button" onclick="openDeleteMatkulModal(<?= $m['id_matkul'] ?>, '<?= htmlspecialchars(addslashes($m['nama_matkul']), ENT_QUOTES, 'UTF-8') ?>')" title="Hapus Mata Kuliah" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white text-red-600 text-xs font-bold border border-red-200 hover:border-red-600 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
            <div id="emptyState" class="<?= empty($matkulList) ? 'block' : 'hidden' ?> p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-4 border border-blue-200 shadow-2xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Data Mata Kuliah</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                    Mata kuliah praktikum belum didaftarkan ke sistem. Silakan klik tombol di bawah untuk membuat mata kuliah baru.
                </p>
                <button type="button" onclick="openCreateMatkulModal()" class="mt-4 px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Mata Kuliah Pertama</span>
                </button>
            </div>

        </div>

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: TAMBAH MATA KULIAH                                                 -->
    <!-- ========================================================================= -->
    <div id="createMatkulModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transition-all transform animate-in fade-in duration-200">
            
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Mata Kuliah Baru</h3>
                    </div>
                </div>
                <button type="button" onclick="closeCreateMatkulModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="<?= \Core\Guard::url('/superadmin/matkul/create') ?>" method="POST" class="p-6 space-y-4">
                <?= \Core\Guard::csrfField() ?>

                <div>
                    <label for="create_nama_matkul" class="block text-xs font-bold text-slate-800 mb-1">
                        Nama Mata Kuliah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="create_nama_matkul" name="nama_matkul" required maxlength="100"
                           class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition"
                           placeholder="Contoh: Pemrograman Berbasis Objek">
                </div>

                <div>
                    <label for="create_dosen_id" class="block text-xs font-bold text-slate-800 mb-1">
                        Pilih Dosen Pengampu / Pembimbing <span class="text-red-500">*</span>
                    </label>
                    <select id="create_dosen_id" name="dosen_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer">
                        <option value="">-- Pilih Dosen Pengampu --</option>
                        <?php foreach ($dosenList as $d): ?>
                            <option value="<?= $d['id_user'] ?>">
                                <?= htmlspecialchars($d['nama'], ENT_QUOTES, 'UTF-8') ?> (NIDN: <?= htmlspecialchars($d['identity_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="create_deskripsi" class="block text-xs font-bold text-slate-800 mb-1">
                        Deskripsi Silabus / Catatan Praktikum
                    </label>
                    <textarea id="create_deskripsi" name="deskripsi" rows="3"
                              class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition resize-none"
                              placeholder="Deskripsi singkat topik modul praktikum..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeCreateMatkulModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Mata Kuliah</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: EDIT MATA KULIAH                                                   -->
    <!-- ========================================================================= -->
    <div id="editMatkulModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transition-all transform animate-in fade-in duration-200">
            
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Perbarui Mata Kuliah</h3>
                        <p class="text-xs text-slate-500" id="edit_matkul_subtitle">Edit informasi kurikulum</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditMatkulModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editMatkulForm" action="" method="POST" class="p-6 space-y-4">
                <?= \Core\Guard::csrfField() ?>

                <div>
                    <label for="edit_nama_matkul" class="block text-xs font-bold text-slate-800 mb-1">
                        Nama Mata Kuliah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="edit_nama_matkul" name="nama_matkul" required maxlength="100"
                           class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                </div>

                <div>
                    <label for="edit_dosen_id" class="block text-xs font-bold text-slate-800 mb-1">
                        Dosen Pengampu / Pembimbing <span class="text-red-500">*</span>
                    </label>
                    <select id="edit_dosen_id" name="dosen_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer">
                        <option value="">-- Pilih Dosen Pengampu --</option>
                        <?php foreach ($dosenList as $d): ?>
                            <option value="<?= $d['id_user'] ?>">
                                <?= htmlspecialchars($d['nama'], ENT_QUOTES, 'UTF-8') ?> (NIDN: <?= htmlspecialchars($d['identity_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="edit_deskripsi" class="block text-xs font-bold text-slate-800 mb-1">
                        Deskripsi Silabus / Catatan Praktikum
                    </label>
                    <textarea id="edit_deskripsi" name="deskripsi" rows="3"
                              class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeEditMatkulModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: HAPUS MATA KULIAH                                                  -->
    <!-- ========================================================================= -->
    <div id="deleteMatkulModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transition-all transform animate-in fade-in duration-200">
            
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 border border-red-300 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                
                <h3 class="text-lg font-bold text-slate-900">Hapus Mata Kuliah?</h3>
                
                <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus mata kuliah <strong><span id="deleteMatkulName"></span></strong>?
                </p>

                <form id="deleteMatkulForm" action="" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    <?= \Core\Guard::csrfField() ?>
                    <button type="button" onclick="closeDeleteMatkulModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Ya, Hapus</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: KELOLA PLOTTING ASISTEN DOSEN KHUSUS MATA KULIAH                   -->
    <!-- ========================================================================= -->
    <div id="managePlottingModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden transition-all transform animate-in fade-in duration-200 my-6">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Kelola Plotting Asisten Dosen</h3>
                    </div>
                </div>
                <button type="button" onclick="closePlottingModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                
                <!-- Matkul & Dosen Context Card -->
                <div class="p-4 bg-indigo-50/40 border border-indigo-200/70 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 block">Mata Kuliah Praktikum</span>
                        <h4 id="manage_plotting_matkul_title" class="font-bold text-slate-900 text-sm sm:text-base leading-tight mt-0.5">-</h4>
                        <p id="manage_plotting_dosen_title" class="text-xs text-slate-600 mt-1">Dosen: -</p>
                    </div>
                    <button type="button" onclick="openCreatePlottingFromManage()" class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs font-bold rounded-xl transition shadow-xs hover:shadow-md cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Buat Plotting Baru</span>
                    </button>
                </div>

                <!-- Section Header -->
                <div class="flex items-center justify-between pt-1">
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Asisten Dosen Terplot</h5>
                    <span id="manage_plotting_count_badge" class="px-2 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-700 rounded-md border border-slate-200">0 Asdos</span>
                </div>

                <!-- Dynamic Asdos List Container -->
                <div id="manage_plotting_list_container" class="space-y-2.5">
                    <!-- Populated dynamically via JavaScript -->
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end">
                <button type="button" onclick="closePlottingModal()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: TAMBAH PLOTTING BARU (KHUSUS MATA KULIAH TERPILIH)                 -->
    <!-- ========================================================================= -->
    <div id="createPlottingModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transition-all transform animate-in fade-in duration-200 my-6">

            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Penugasan Asdos</h3>
                        <p class="text-xs text-slate-500" id="create_plotting_matkul_subtitle">Plotting khusus mata kuliah</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreatePlottingModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="<?= \Core\Guard::url('/superadmin/plotting/create') ?>" method="POST" class="p-6 space-y-4">
                <?= \Core\Guard::csrfField() ?>
                <input type="hidden" name="redirect_to" value="/superadmin/matkul">
                <input type="hidden" name="matkul_id" id="modal_create_plotting_matkul_id" value="">

                <!-- Locked Context Banner -->
                <div class="p-3.5 bg-blue-50/60 border border-blue-200 rounded-xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#1867c0] block">Mata Kuliah Target</span>
                    <p id="create_plotting_matkul_nama_display" class="font-bold text-slate-900 text-sm mt-0.5">-</p>
                    <p id="create_plotting_dosen_nama_display" class="text-xs text-slate-600 mt-0.5">Dosen: -</p>
                </div>

                <!-- Dropdown Pilih Asdos -->
                <div>
                    <label for="create_asdos_id" class="block text-xs font-bold text-slate-800 mb-1">
                        Pilih Asisten Dosen <span class="text-red-500">*</span>
                    </label>
                    <select id="create_asdos_id" name="asdos_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition cursor-pointer shadow-2xs">
                        <option value="">-- Pilih Asisten Dosen --</option>
                        <?php foreach ($asdosList as $a): ?>
                            <option value="<?= $a['id_user'] ?>">
                                <?= htmlspecialchars($a['nama'], ENT_QUOTES, 'UTF-8') ?> (NPM: <?= htmlspecialchars($a['identity_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Rentang Periode -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_periode_mulai" class="block text-xs font-bold text-slate-800 mb-1">
                            Tanggal Mulai Mengajar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="create_periode_mulai" name="periode_mulai" required value="<?= date('Y-m-d') ?>"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                    </div>
                    <div>
                        <label for="create_periode_selesai" class="block text-xs font-bold text-slate-800 mb-1">
                            Tanggal Selesai Mengajar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="create_periode_selesai" name="periode_selesai" required value="<?= date('Y-m-d', strtotime('+6 months')) ?>"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs">
                    </div>
                </div>

                <!-- Status Penugasan Langsung Aktif -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-slate-800">Status Penugasan Langsung Aktif</span>
                            <p class="text-xs text-slate-500 mt-0.5">Asdos dapat langsung mencatat absensi di portalnya.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer group m-0">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-10 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                        </label>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeCreatePlottingModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Penugasan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Bottom Navigation Bar (Mobile) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>

    <!-- JavaScript Logic -->
    <script>
        const BASE_URL = '<?= \Core\Guard::url('') ?>';
        let currentActiveMatkulData = null;

        // Filter Logic
        const searchInput    = document.getElementById('searchInput');
        const dosenFilter    = document.getElementById('dosenFilter');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const emptyState     = document.getElementById('emptyState');
        const displayedCount = document.getElementById('displayedCount');

        function applyFilters() {
            const query   = searchInput.value.toLowerCase().trim();
            const dosenId = dosenFilter.value;

            if (query.length > 0) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }

            const rows = document.querySelectorAll('.matkul-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const nama      = (row.dataset.nama || '').toLowerCase();
                const dosenNama = (row.dataset.dosenNama || '').toLowerCase();
                const rowDosen  = row.dataset.dosenId || '';

                const matchSearch = query === '' || nama.includes(query) || dosenNama.includes(query);
                const matchDosen  = dosenId === '' || rowDosen === dosenId;

                if (matchSearch && matchDosen) {
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
            dosenFilter.value = '';
            applyFilters();
        }

        // =========================================================================
        // Modal Create & Edit Mata Kuliah
        // =========================================================================
        function openCreateMatkulModal() {
            document.getElementById('createMatkulModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeCreateMatkulModal() {
            document.getElementById('createMatkulModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openEditMatkulModal(row) {
            if (!row) return;
            const id        = row.dataset.id;
            const nama      = row.dataset.nama;
            const deskripsi = row.dataset.deskripsi;
            const dosenId     = row.dataset.dosenId;

            document.getElementById('editMatkulForm').action = `${BASE_URL}/superadmin/matkul/${id}/update`;
            document.getElementById('edit_nama_matkul').value = nama;
            document.getElementById('edit_deskripsi').value   = deskripsi;
            document.getElementById('edit_dosen_id').value    = dosenId;
            document.getElementById('edit_matkul_subtitle').textContent = `Mengedit ${nama}`;

            document.getElementById('editMatkulModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeEditMatkulModal() {
            document.getElementById('editMatkulModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openDeleteMatkulModal(id, nama) {
            document.getElementById('deleteMatkulForm').action = `${BASE_URL}/superadmin/matkul/${id}/delete`;
            document.getElementById('deleteMatkulName').textContent = nama;
            document.getElementById('deleteMatkulModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteMatkulModal() {
            document.getElementById('deleteMatkulModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // =========================================================================
        // Modal Kelola Plotting Asdos per Mata Kuliah
        // =========================================================================
        function openPlottingModal(data) {
            if (!data) return;
            currentActiveMatkulData = data;

            document.getElementById('manage_plotting_matkul_title').textContent = data.nama_matkul || '-';
            document.getElementById('manage_plotting_dosen_title').textContent = `Dosen Pengampu: ${data.nama_dosen || 'Belum ditentukan'} ${data.nidn_dosen ? `(NIDN: ${data.nidn_dosen})` : ''}`;

            const listContainer = document.getElementById('manage_plotting_list_container');
            listContainer.innerHTML = '';

            const plottings = data.plottings || [];
            document.getElementById('manage_plotting_count_badge').textContent = `${plottings.length} Asdos Terplot`;

            if (plottings.length === 0) {
                listContainer.innerHTML = `
                    <div class="p-8 text-center bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-3 border border-blue-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Belum Ada Asisten Dosen yang Diplotkan</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                            Mata kuliah ini belum memiliki asdos pengampu. Tambahkan asdos sekarang agar asdos dapat melakukan presensi mengajar.
                        </p>
                        <button type="button" onclick="openCreatePlottingFromManage()" class="mt-3.5 inline-flex items-center gap-1.5 px-4 py-2 bg-[#1867c0] hover:bg-[#14529d] text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Plotkan Asisten Dosen Sekarang</span>
                        </button>
                    </div>
                `;
            } else {
                plottings.forEach(p => {
                    const isActive = parseInt(p.is_active) === 1;
                    const initial = (p.nama_asdos || 'A').charAt(0).toUpperCase();

                    const itemCard = document.createElement('div');
                    itemCard.className = `p-3.5 bg-white border rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs transition hover:border-slate-300 ${isActive ? 'border-slate-200' : 'border-slate-200 bg-slate-50/50 opacity-80'}`;

                    itemCard.innerHTML = `
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl ${isActive ? 'bg-blue-100 text-[#1867c0]' : 'bg-slate-200 text-slate-600'} font-bold text-xs flex items-center justify-center shrink-0">
                                ${initial}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-bold text-slate-900 text-xs sm:text-sm leading-tight">${escapeHtml(p.nama_asdos || '-')}</p>
                                    ${isActive 
                                        ? '<span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif</span>'
                                        : '<span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-slate-100 text-slate-600 border border-slate-300">Selesai / Nonaktif</span>'
                                    }
                                </div>
                                <p class="text-[11px] text-slate-500 font-mono mt-0.5">NPM: ${escapeHtml(p.npm_asdos || '-')} &bull; ${escapeHtml(p.email_asdos || '')}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Periode: ${p.periode_mulai || '-'} s/d ${p.periode_selesai || '-'}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-center">
                            <!-- Toggle Button Form -->
                            <form action="${BASE_URL}/superadmin/plotting/${p.id_plotting}/toggle" method="POST" class="inline">
                                <?= \Core\Guard::csrfField() ?>
                                <input type="hidden" name="redirect_to" value="/superadmin/matkul">
                                <button type="submit" 
                                    title="${isActive ? 'Nonaktifkan status asdos' : 'Aktifkan kembali status asdos'}"
                                    class="px-2.5 py-1.5 rounded-lg ${isActive ? 'bg-slate-100 hover:bg-amber-50 hover:text-amber-800 border-slate-300 text-slate-700' : 'bg-emerald-50 hover:bg-emerald-100 border-emerald-300 text-emerald-800'} text-xs font-bold border transition duration-150 cursor-pointer shadow-2xs active:scale-95">
                                    ${isActive ? 'Nonaktifkan' : 'Aktifkan'}
                                </button>
                            </form>

                            <!-- Delete Button Form -->
                            <form action="${BASE_URL}/superadmin/plotting/${p.id_plotting}/delete" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus plotting asdos ini?');">
                                <?= \Core\Guard::csrfField() ?>
                                <input type="hidden" name="redirect_to" value="/superadmin/matkul">
                                <button type="submit" 
                                    title="Hapus Plotting"
                                    class="p-1.5 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white text-red-600 border border-red-200 hover:border-red-600 text-xs font-bold transition duration-150 cursor-pointer shadow-2xs active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    `;
                    listContainer.appendChild(itemCard);
                });
            }

            document.getElementById('managePlottingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closePlottingModal() {
            document.getElementById('managePlottingModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // =========================================================================
        // Modal Tambah Plotting Baru Khusus Mata Kuliah
        // =========================================================================
        function openCreatePlottingFromManage() {
            if (!currentActiveMatkulData) return;
            openCreatePlottingModal(currentActiveMatkulData);
        }

        function openCreatePlottingModal(data) {
            if (!data) return;
            currentActiveMatkulData = data;

            document.getElementById('modal_create_plotting_matkul_id').value = data.id_matkul;
            document.getElementById('create_plotting_matkul_subtitle').textContent = `Plotting khusus: ${data.nama_matkul}`;
            document.getElementById('create_plotting_matkul_nama_display').textContent = data.nama_matkul;
            document.getElementById('create_plotting_dosen_nama_display').textContent = `Dosen Pengampu: ${data.nama_dosen || 'Belum ditentukan'}`;
            document.getElementById('create_asdos_id').value = '';

            // Tutup modal manage plotting sementara jika terbuka
            document.getElementById('managePlottingModal').classList.add('hidden');

            document.getElementById('createPlottingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreatePlottingModal() {
            document.getElementById('createPlottingModal').classList.add('hidden');
            if (currentActiveMatkulData) {
                // Buka kembali modal manage plotting
                document.getElementById('managePlottingModal').classList.remove('hidden');
            } else {
                document.body.style.overflow = '';
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Close on Escape Key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateMatkulModal();
                closeEditMatkulModal();
                closeDeleteMatkulModal();
                closeCreatePlottingModal();
                closePlottingModal();
            }
        });
    </script>
</body>
</html>
