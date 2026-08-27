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
$filters     = $filters ?? [
    'search'   => '',
    'dosen_id' => '',
];
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Mata Kuliah — Absensi Lab</title>

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

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

        <!-- Page Header Banner -->
        <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="<?= \Core\Guard::url('/superadmin/dashboard') ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-[#1867c0] hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Dashboard
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs text-slate-500 font-medium">Mata Kuliah</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Master Data Mata Kuliah</h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-3xl leading-relaxed">
                    Daftar kurikulum praktikum laboratorium. Anda dapat menambah mata kuliah baru, mengubah silabus, serta menentukan Dosen Pengampu yang bertanggung jawab memverifikasi absensi.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openCreateMatkulModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 shadow-xs hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Mata Kuliah</span>
                </button>
            </div>
        </div>

        <!-- Panduan Bantuan Cepat Ramah Pengguna -->
        <div class="bg-indigo-50/70 border border-indigo-200/90 p-4 rounded-xl flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-xs text-slate-700 space-y-1">
                <p class="font-bold text-slate-900">Petunjuk Mata Kuliah Praktikum:</p>
                <p class="text-slate-600 leading-relaxed">
                    • Setiap mata kuliah praktikum <strong>wajib memiliki 1 Dosen Pengampu</strong>.<br>
                    • Setelah mata kuliah dibuat, Anda dapat menugaskan Asisten Dosen pada menu <strong>Plotting Asdos</strong>.
                </p>
            </div>
        </div>

        <!-- Metric Cards (4 Columns with Lift Hover) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-4">
            
            <!-- 1. Total Mata Kuliah -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Mata Kuliah</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= $metrics['total'] ?></p>
                    <p class="text-xs text-slate-400 mt-0.5">Kurikulum praktikum</p>
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
                    <p class="text-xs text-emerald-600/70 mt-0.5">Memiliki asdos aktif</p>
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
                    <p class="text-xs text-slate-400 mt-0.5">Perlu penugasan asdos</p>
                </div>
                <div class="w-11 h-11 rounded-xl <?= $metrics['belum_berplot'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' ?> flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>

            <!-- 4. Total Dosen Pengampu -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-indigo-200/80 bg-indigo-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-indigo-400">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-indigo-700">Dosen Pengampu</p>
                    <p class="text-2xl sm:text-3xl font-bold text-indigo-900 mt-1"><?= $metrics['total_dosen'] ?></p>
                    <p class="text-xs text-indigo-600/70 mt-0.5">Terdaftar di matkul</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>

        </div>

        <!-- Content Card: Toolbar & Matkul Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            
            <!-- Toolbar -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/60 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Mata Kuliah</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Menampilkan seluruh kurikulum mata kuliah praktikum dan dosen penanggung jawab.
                        </p>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1.5 bg-slate-100 text-slate-700 rounded-full border border-slate-200 self-start sm:self-auto flex items-center gap-1.5 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>Total: <strong id="displayedCount" class="text-slate-900"><?= count($matkulList) ?></strong> Mata Kuliah</span>
                    </span>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
                    <!-- Search Input -->
                    <div class="sm:col-span-7 relative">
                        <label for="searchInput" class="block text-xs font-bold text-slate-700 mb-1">Cari Mata Kuliah</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" id="searchInput" 
                                   placeholder="Ketik kode matkul, nama mata kuliah, atau nama dosen pengampu..." 
                                   value="<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 shadow-2xs">
                            <button type="button" id="clearSearchBtn" onclick="clearSearch()" title="Hapus teks pencarian" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Dosen Filter -->
                    <div class="sm:col-span-4">
                        <label for="dosenFilter" class="block text-xs font-bold text-slate-700 mb-1">Saring Berdasarkan Dosen</label>
                        <select id="dosenFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                            <option value="">Semua Dosen Pengampu</option>
                            <?php foreach ($dosenList as $d): ?>
                                <option value="<?= $d['id_user'] ?>" <?= $filters['dosen_id'] == $d['id_user'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['nama'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="sm:col-span-1 flex flex-col justify-end">
                        <button type="button" onclick="resetAllFilters()" title="Reset semua filter" class="w-full h-[42px] bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold flex items-center justify-center gap-1 transition-all active:scale-95 cursor-pointer shadow-2xs">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span class="sm:hidden font-medium">Reset Filter</span>
                        </button>
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
                                                <p class="font-bold text-slate-900 text-xs sm:text-sm leading-tight"><?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?></p>
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
                                        <?php if ((int)($m['total_asdos_aktif'] ?? 0) > 0): ?>
                                            <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded-full border bg-emerald-50 text-emerald-800 border-emerald-300 inline-flex items-center gap-1.5 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <?= $m['total_asdos_aktif'] ?> Asdos Aktif
                                            </span>
                                        <?php else: ?>
                                            <a href="<?= \Core\Guard::url('/superadmin/plotting') ?>" class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded-full border bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100 transition inline-flex items-center gap-1 shadow-2xs">
                                                <span>+ Buat Plotting</span>
                                            </a>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 4. Terdaftar -->
                                    <td class="px-3.5 py-3 whitespace-nowrap text-slate-600 text-xs">
                                        <?= !empty($m['created_at']) ? date('d M Y', strtotime($m['created_at'])) : '-' ?>
                                    </td>

                                    <!-- 5. Aksi -->
                                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <!-- Edit -->
                                            <button type="button" onclick="openEditMatkulModal(this.closest('tr'))" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-800 text-slate-700 text-xs font-bold border border-slate-300 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Hapus -->
                                            <button type="button" onclick="openDeleteMatkulModal(<?= $m['id_matkul'] ?>, '<?= htmlspecialchars(addslashes($m['nama_matkul']), ENT_QUOTES, 'UTF-8') ?>')" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white text-red-600 text-xs font-bold border border-red-200 hover:border-red-600 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
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

            <!-- Footer -->
            <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-600 gap-2">
                <p>
                    <span class="font-bold text-slate-800">Aturan PRD (F2):</span> Setiap mata kuliah wajib memiliki 1 dosen pengampu yang bertugas menyetujui atau menolak absensi asdos.
                </p>
                <p class="text-xs text-slate-400">
                    Sistem Absensi Lab &copy; <?= date('Y') ?>
                </p>
            </div>

        </div>

    </main>

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
                        <p class="text-xs text-slate-500">Daftarkan kurikulum praktikum laboratorium</p>
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

                <div class="mt-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-left text-xs text-red-800 leading-relaxed">
                    <p class="font-bold flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Pencegahan:
                    </p>
                    <p>
                        Mata kuliah yang masih memiliki asisten dosen aktif tidak dapat dihapus demi menjaga konsistensi jadwal praktikum.
                    </p>
                </div>

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

    <!-- Floating Bottom Navigation Bar (Mobile) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>

    <!-- JavaScript Logic -->
    <script>
        const BASE_URL = '<?= \Core\Guard::url('') ?>';

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

        // Modal Create
        function openCreateMatkulModal() {
            document.getElementById('createMatkulModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeCreateMatkulModal() {
            document.getElementById('createMatkulModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Modal Edit
        function openEditMatkulModal(row) {
            if (!row) return;
            const id        = row.dataset.id;
            const nama      = row.dataset.nama;
            const deskripsi = row.dataset.deskripsi;
            const dosenId   = row.dataset.dosenId;

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

        // Modal Delete
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

        // Escape Key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateMatkulModal();
                closeEditMatkulModal();
                closeDeleteMatkulModal();
            }
        });
    </script>
</body>
</html>
