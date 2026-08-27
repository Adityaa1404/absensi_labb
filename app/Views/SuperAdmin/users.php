<?php
// Fallback & Dokumentasi Variabel dari SuperAdminController
$currentUser = $currentUser ?? \Core\Guard::user();
$metrics     = $metrics ?? [
    'total'       => 0,
    'dosen'       => 0,
    'asdos'       => 0,
    'super_admin' => 0,
    'active'      => 0,
    'inactive'    => 0,
];
$users       = $users ?? [];
$filters     = $filters ?? [
    'role'      => '',
    'is_active' => '',
    'search'    => '',
];
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna — Absensi Lab</title>

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
                    <span class="text-xs text-slate-500 font-medium">Kelola Pengguna</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Kelola Data Pengguna</h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-3xl leading-relaxed">
                    Daftar akun civitas lab (Dosen, Asisten Dosen, dan Admin). Anda dapat menambah pengguna baru, mengubah data, serta mengaktifkan atau menonaktifkan status akun secara aman.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 shadow-xs hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Tambah Pengguna Baru</span>
                </button>
            </div>
        </div>

        <!-- Panduan Bantuan Cepat Ramah Pengguna (Help Accordion) -->
        <div class="bg-blue-50/70 border border-blue-200/90 p-4 rounded-xl flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1867c0] flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-xs text-slate-700 space-y-1">
                <p class="font-bold text-slate-900">Petunjuk Pengelolaan Akun:</p>
                <p class="text-slate-600 leading-relaxed">
                    • <strong>Status AKTIF (Hijau):</strong> Pengguna dapat login dan melakukan tugasnya (Dosen memverifikasi, Asdos mencatat absensi).<br>
                    • <strong>Status NONAKTIF (Abu-abu):</strong> Pengguna hanya dapat melihat riwayat masa lalu (Mode Lihat Saja). Tombol input absensi otomatis dikunci oleh sistem.
                </p>
            </div>
        </div>

        <!-- Metric / Stat Cards Grid (4 Columns with Lift Hover) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 sm:gap-4">
            
            <!-- 1. Total Pengguna -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pengguna</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= $metrics['total'] ?></p>
                    <p class="text-xs text-slate-400 mt-0.5">Seluruh civitas lab</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>

            <!-- 2. Dosen Pengampu -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-indigo-200/80 bg-indigo-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-indigo-400">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-indigo-700">Dosen Pengampu</p>
                    <p class="text-2xl sm:text-3xl font-bold text-indigo-900 mt-1"><?= $metrics['dosen'] ?></p>
                    <p class="text-xs text-indigo-600/70 mt-0.5">Penanggung jawab matkul</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>

            <!-- 3. Asisten Dosen -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-blue-200/80 bg-blue-50/20 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-400">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#1867c0]">Asisten Dosen</p>
                    <p class="text-2xl sm:text-3xl font-bold text-[#1867c0] mt-1"><?= $metrics['asdos'] ?></p>
                    <p class="text-xs text-blue-600/70 mt-0.5">Pelaksana praktikum</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>

            <!-- 4. Akun Nonaktif (Mode Read-Only) -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border <?= $metrics['inactive'] > 0 ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200' ?> shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider <?= $metrics['inactive'] > 0 ? 'text-amber-700' : 'text-slate-500' ?>">Akun Nonaktif</p>
                    <p class="text-2xl sm:text-3xl font-bold <?= $metrics['inactive'] > 0 ? 'text-amber-800' : 'text-slate-700' ?> mt-1"><?= $metrics['inactive'] ?></p>
                    <p class="text-xs <?= $metrics['inactive'] > 0 ? 'text-amber-600' : 'text-slate-400' ?> mt-0.5">Mode hanya lihat (BR2)</p>
                </div>
                <div class="w-11 h-11 rounded-xl <?= $metrics['inactive'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' ?> flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>

        </div>

        <!-- Content Card: Toolbar Filter & Users Data Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            
            <!-- Card Header with Filters -->
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/60 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Pengguna Sistem</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Gunakan kotak pencarian atau pilihan dropdown di bawah untuk menemukan pengguna tertentu.
                        </p>
                    </div>
                    <span id="userCountBadge" class="text-xs font-semibold px-3 py-1.5 bg-slate-100 text-slate-700 rounded-full border border-slate-200 self-start sm:self-auto flex items-center gap-1.5 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>Menampilkan <strong id="displayedCount" class="text-slate-900"><?= count($users) ?></strong> dari <?= $metrics['total'] ?> Pengguna</span>
                    </span>
                </div>

                <!-- Interactive Filters & Search Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">
                    
                    <!-- Search Input -->
                    <div class="sm:col-span-6 relative">
                        <label for="searchInput" class="block text-xs font-bold text-slate-700 mb-1">Cari Pengguna</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" id="searchInput" 
                                   placeholder="Ketik nama, NPM/NIDN, email, atau nomor HP..." 
                                   class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 shadow-2xs">
                            <button type="button" id="clearSearchBtn" onclick="clearSearch()" title="Hapus teks pencarian" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Role Filter -->
                    <div class="sm:col-span-3">
                        <label for="roleFilter" class="block text-xs font-bold text-slate-700 mb-1">Saring Berdasarkan Peran</label>
                        <select id="roleFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                            <option value="">Semua Peran (Dosen, Asdos, Admin)</option>
                            <option value="dosen">Khusus Dosen</option>
                            <option value="asdos">Khusus Asisten Dosen</option>
                            <option value="super_admin">Khusus Super Admin</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="sm:col-span-2">
                        <label for="statusFilter" class="block text-xs font-bold text-slate-700 mb-1">Status Akun</label>
                        <select id="statusFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif Saja</option>
                            <option value="0">Nonaktif Saja</option>
                        </select>
                    </div>

                    <!-- Reset Filter Button -->
                    <div class="sm:col-span-1 flex flex-col justify-end">
                        <button type="button" onclick="resetAllFilters()" title="Reset semua filter" class="w-full h-[42px] bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold flex items-center justify-center gap-1 transition-all active:scale-95 cursor-pointer shadow-2xs">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span class="sm:hidden font-medium">Reset Filter</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Table Responsive Container (Auto-fitting without horizontal scroll on desktop) -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 border-collapse" id="usersTable">
                    <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5">Pengguna & Nomor Identitas</th>
                            <th class="px-3.5 py-3.5">Kontak (Email / WA)</th>
                            <th class="px-3 py-3.5 text-center">Peran Akun</th>
                            <th class="px-3 py-3.5 text-center">Status Keaktifan</th>
                            <th class="px-3.5 py-3.5">Terdaftar</th>
                            <th class="px-3.5 py-3.5 text-center">Pilihan Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" id="usersTableBody">
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                                <?php 
                                    $isSelf = (int)$u['id_user'] === (int)($currentUser['id_user'] ?? 0);
                                    $role = $u['role'];
                                    $isActive = (int)$u['is_active'] === 1;

                                    // Role badge style
                                    $roleBadge = match ($role) {
                                        'dosen'       => ['label' => 'DOSEN', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-800', 'border' => 'border-indigo-300'],
                                        'asdos'       => ['label' => 'ASDOS', 'bg' => 'bg-blue-50', 'text' => 'text-[#1867c0]', 'border' => 'border-blue-300'],
                                        'super_admin' => ['label' => 'SUPER ADMIN', 'bg' => 'bg-amber-50', 'text' => 'text-amber-900', 'border' => 'border-amber-300'],
                                        default       => ['label' => strtoupper($role), 'bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-300']
                                    };

                                    // Initials for avatar
                                    $words = explode(' ', trim($u['nama'] ?? 'User'));
                                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                    $identityLabel = $role === 'dosen' ? 'NIDN' : ($role === 'asdos' ? 'NPM' : 'User');
                                ?>
                                <tr class="hover:bg-blue-50/40 transition-colors duration-150 user-row" 
                                    data-id="<?= $u['id_user'] ?>"
                                    data-nama="<?= htmlspecialchars($u['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-identity="<?= htmlspecialchars($u['identity_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-email="<?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-nohp="<?= htmlspecialchars($u['no_hp'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-role="<?= $u['role'] ?>"
                                    data-active="<?= $u['is_active'] ?>"
                                    data-is-self="<?= $isSelf ? '1' : '0' ?>">
                                    
                                    <!-- 1. Pengguna & Nomor Identitas (Avatar + Nama + Identity Badge) -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl <?= $roleBadge['bg'] ?> <?= $roleBadge['text'] ?> font-bold text-xs flex items-center justify-center shrink-0 border <?= $roleBadge['border'] ?> shadow-2xs">
                                                <?= $initials ?>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-slate-900 text-xs sm:text-sm leading-tight"><?= htmlspecialchars($u['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                                                    <?php if ($isSelf): ?>
                                                        <span class="px-1.5 py-0.5 text-[10px] font-bold bg-blue-100 text-[#1867c0] rounded border border-blue-300">
                                                            Akun Anda
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                                    <?php if (!empty($u['identity_number'])): ?>
                                                        <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                                            <?= $identityLabel ?>: <?= htmlspecialchars($u['identity_number'], ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span>#<?= $u['id_user'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Kontak (Email & No HP) -->
                                    <td class="px-3.5 py-3">
                                        <div class="space-y-0.5">
                                            <?php if (!empty($u['email'])): ?>
                                                <div class="flex items-center gap-1.5 text-slate-700 text-xs">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    <span class="truncate max-w-[200px] font-medium"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($u['no_hp'])): ?>
                                                <div class="flex items-center gap-1.5 text-slate-700 text-xs">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $u['no_hp']) ?>" target="_blank" class="text-emerald-700 hover:text-emerald-900 font-semibold hover:underline flex items-center gap-1">
                                                        <span><?= htmlspecialchars($u['no_hp'], ENT_QUOTES, 'UTF-8') ?></span>
                                                        <span class="text-[9px] bg-emerald-100 text-emerald-800 px-1 rounded">WA</span>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- 3. Peran (Role Badge) -->
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded-full border <?= $roleBadge['bg'] ?> <?= $roleBadge['text'] ?> <?= $roleBadge['border'] ?> shadow-2xs">
                                            <?= $roleBadge['label'] ?>
                                        </span>
                                    </td>

                                    <!-- 4. Status Akun (Interactive Toggle Switch) -->
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <?php if ($isActive): ?>
                                            <div class="inline-flex items-center gap-1.5">
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border bg-emerald-50 text-emerald-800 border-emerald-300 flex items-center gap-1.5 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    AKTIF
                                                </span>
                                                <?php if (!$isSelf): ?>
                                                    <button type="button" 
                                                            onclick="openToggleModal(<?= $u['id_user'] ?>, '<?= htmlspecialchars(addslashes($u['nama']), ENT_QUOTES, 'UTF-8') ?>', 1)"
                                                            title="Klik untuk menonaktifkan akun"
                                                            class="p-1 rounded-lg text-slate-400 hover:text-amber-700 hover:bg-amber-100/80 border border-transparent hover:border-amber-300 transition-all duration-150 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="inline-flex items-center gap-1.5">
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border bg-slate-100 text-slate-700 border-slate-300 flex items-center gap-1.5 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                    NONAKTIF
                                                </span>
                                                <button type="button" 
                                                        onclick="openToggleModal(<?= $u['id_user'] ?>, '<?= htmlspecialchars(addslashes($u['nama']), ENT_QUOTES, 'UTF-8') ?>', 0)"
                                                        title="Klik untuk mengaktifkan kembali akun"
                                                        class="p-1 rounded-lg text-slate-400 hover:text-emerald-700 hover:bg-emerald-100/80 border border-transparent hover:border-emerald-300 transition-all duration-150 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 5. Tanggal Terdaftar -->
                                    <td class="px-3.5 py-3 whitespace-nowrap text-slate-600 text-xs">
                                        <?= !empty($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '-' ?>
                                    </td>

                                    <!-- 6. Aksi (Edit & Hapus) -->
                                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            
                                            <!-- Tombol Edit -->
                                            <button type="button" 
                                                    onclick="openEditModal(this.closest('tr'))"
                                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 hover:border-amber-300 hover:text-amber-800 text-slate-700 text-xs font-bold border border-slate-300 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Tombol Hapus -->
                                            <?php if (!$isSelf): ?>
                                                <button type="button" 
                                                        onclick="openDeleteModal(<?= $u['id_user'] ?>, '<?= htmlspecialchars(addslashes($u['nama']), ENT_QUOTES, 'UTF-8') ?>')"
                                                        class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white text-red-600 text-xs font-bold border border-red-200 hover:border-red-600 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    <span>Hapus</span>
                                                </button>
                                            <?php else: ?>
                                                <span class="px-2 py-1 text-[11px] text-slate-400 italic">Akun Utama</span>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="<?= empty($users) ? 'block' : 'hidden' ?> p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-4 border border-blue-200 shadow-2xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak ada data pengguna yang sesuai</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                    Pencarian atau filter yang Anda pilih tidak menemukan hasil. Coba bersihkan filter untuk menampilkan seluruh data kembali.
                </p>
                <button type="button" onclick="resetAllFilters()" class="mt-4 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition border border-slate-300 shadow-2xs">
                    Tampilkan Semua Pengguna
                </button>
            </div>

            <!-- Table Footer info -->
            <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-600 gap-2">
                <p>
                    <span class="font-bold text-slate-800">Aturan Akun PRD (BR2):</span> Akun yang dinonaktifkan tetap dapat login untuk mengecek rekap riwayat kehadiran (mode hanya lihat).
                </p>
                <p class="text-xs text-slate-400">
                    Sistem Absensi Lab &copy; <?= date('Y') ?>
                </p>
            </div>

        </div>

    </main>

    <!-- ========================================================================= -->
    <!-- 1. MODAL: TAMBAH PENGGUNA BARU                                           -->
    <!-- ========================================================================= -->
    <div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transition-all transform animate-in fade-in duration-200">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah Pengguna Baru</h3>
                        <p class="text-xs text-slate-500">Daftarkan akun Dosen, Asisten Dosen, atau Admin Lab</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="<?= \Core\Guard::url('/superadmin/users/create') ?>" method="POST" class="p-6 space-y-4">
                <?= \Core\Guard::csrfField() ?>

                <!-- Peran / Role Selector -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Pilih Peran Akun (Role) <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        <label class="relative flex flex-col items-center justify-center p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-checked:border-[#1867c0] has-checked:bg-blue-50/60 has-checked:text-[#1867c0] transition-all duration-150 shadow-2xs">
                            <input type="radio" name="role" value="asdos" checked onchange="updateIdentityLabel('create', this.value)" class="sr-only">
                            <svg class="w-6 h-6 mb-1 text-slate-500 has-checked:text-[#1867c0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span class="text-xs sm:text-sm font-bold">Asdos</span>
                            <span class="text-xs text-slate-500">Praktikum</span>
                        </label>
                        <label class="relative flex flex-col items-center justify-center p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-checked:border-indigo-600 has-checked:bg-indigo-50/60 has-checked:text-indigo-800 transition-all duration-150 shadow-2xs">
                            <input type="radio" name="role" value="dosen" onchange="updateIdentityLabel('create', this.value)" class="sr-only">
                            <svg class="w-6 h-6 mb-1 text-slate-500 has-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-xs sm:text-sm font-bold">Dosen</span>
                            <span class="text-xs text-slate-500">Pengampu</span>
                        </label>
                        <label class="relative flex flex-col items-center justify-center p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-checked:border-amber-600 has-checked:bg-amber-50/60 has-checked:text-amber-900 transition-all duration-150 shadow-2xs">
                            <input type="radio" name="role" value="super_admin" onchange="updateIdentityLabel('create', this.value)" class="sr-only">
                            <svg class="w-6 h-6 mb-1 text-slate-500 has-checked:text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="text-xs sm:text-sm font-bold">Admin</span>
                            <span class="text-xs text-slate-500">Pengelola</span>
                        </label>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="create_nama" class="block text-xs font-bold text-slate-800 mb-1">
                        Nama Lengkap Beserta Gelar <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="create_nama" name="nama" required maxlength="50"
                           class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition"
                           placeholder="Contoh: Dr. Budi Santoso, S.Kom., M.Kom.">
                </div>

                <!-- Nomor Identitas (NPM / NIDN) -->
                <div>
                    <label id="create_identity_label" for="create_identity_number" class="block text-xs font-bold text-slate-800 mb-1">
                        Nomor Pokok Mahasiswa (NPM) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="create_identity_number" name="identity_number" required maxlength="100"
                           class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-mono text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition"
                           placeholder="Contoh: 21082010001">
                </div>

                <!-- Email & No. HP Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_email" class="block text-xs font-bold text-slate-800 mb-1">
                            Alamat Email Aktif <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="create_email" name="email" required maxlength="80"
                               class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition"
                               placeholder="nama@upnjatim.ac.id">
                    </div>
                    <div>
                        <label for="create_nohp" class="block text-xs font-bold text-slate-800 mb-1">
                            No. WhatsApp / HP
                        </label>
                        <input type="tel" id="create_nohp" name="no_hp" maxlength="20"
                               class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition"
                               placeholder="Contoh: 081234567890">
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div>
                    <label for="create_password" class="block text-xs font-bold text-slate-800 mb-1">
                        Kata Sandi Awal Akun <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="create_password" name="password" required minlength="6"
                               class="w-full bg-white border border-slate-300 rounded-xl pl-3.5 pr-10 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition"
                               placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePasswordVisibility('create_password')" title="Lihat/Sembunyikan Kata Sandi" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Pengguna dapat mengganti kata sandi ini setelah berhasil login.</p>
                </div>

                <!-- Status Akun Awal -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-slate-800">Status Akun Langsung Aktif</span>
                            <p class="text-xs text-slate-500">Pengguna dapat langsung login dan beraktivitas.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Pengguna</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. MODAL: EDIT DATA PENGGUNA                                              -->
    <!-- ========================================================================= -->
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transition-all transform animate-in fade-in duration-200">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Perbarui Data Pengguna</h3>
                        <p class="text-xs text-slate-500" id="edit_modal_subtitle">Edit data profil & hak akses</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="editForm" action="" method="POST" class="p-6 space-y-4">
                <?= \Core\Guard::csrfField() ?>

                <!-- Peran / Role Selector -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Peran Akun (Role) <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        <label id="edit_role_asdos_label" class="relative flex flex-col items-center justify-center p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-checked:border-[#1867c0] has-checked:bg-blue-50/60 has-checked:text-[#1867c0] transition shadow-2xs">
                            <input type="radio" id="edit_role_asdos" name="role" value="asdos" onchange="updateIdentityLabel('edit', this.value)" class="sr-only">
                            <span class="text-xs sm:text-sm font-bold">Asdos</span>
                            <span class="text-xs text-slate-500">Praktikum</span>
                        </label>
                        <label id="edit_role_dosen_label" class="relative flex flex-col items-center justify-center p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-checked:border-indigo-600 has-checked:bg-indigo-50/60 has-checked:text-indigo-800 transition shadow-2xs">
                            <input type="radio" id="edit_role_dosen" name="role" value="dosen" onchange="updateIdentityLabel('edit', this.value)" class="sr-only">
                            <span class="text-xs sm:text-sm font-bold">Dosen</span>
                            <span class="text-xs text-slate-500">Pengampu</span>
                        </label>
                        <label id="edit_role_admin_label" class="relative flex flex-col items-center justify-center p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-checked:border-amber-600 has-checked:bg-amber-50/60 has-checked:text-amber-900 transition shadow-2xs">
                            <input type="radio" id="edit_role_admin" name="role" value="super_admin" onchange="updateIdentityLabel('edit', this.value)" class="sr-only">
                            <span class="text-xs sm:text-sm font-bold">Admin</span>
                            <span class="text-xs text-slate-500">Pengelola</span>
                        </label>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="edit_nama" class="block text-xs font-bold text-slate-800 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="edit_nama" name="nama" required maxlength="50"
                           class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                </div>

                <!-- Nomor Identitas (NPM / NIDN) -->
                <div>
                    <label id="edit_identity_label" for="edit_identity_number" class="block text-xs font-bold text-slate-800 mb-1">
                        Nomor Identitas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="edit_identity_number" name="identity_number" required maxlength="100"
                           class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-mono text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                </div>

                <!-- Email & No. HP Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_email" class="block text-xs font-bold text-slate-800 mb-1">
                            Alamat Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="edit_email" name="email" required maxlength="80"
                               class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                    </div>
                    <div>
                        <label for="edit_nohp" class="block text-xs font-bold text-slate-800 mb-1">
                            No. WhatsApp / HP
                        </label>
                        <input type="tel" id="edit_nohp" name="no_hp" maxlength="20"
                               class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition">
                    </div>
                </div>

                <!-- Reset Kata Sandi (Opsional) -->
                <div>
                    <label for="edit_password" class="block text-xs font-bold text-slate-800 mb-1">
                        Kata Sandi Baru <span class="text-slate-500 font-normal">(Kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="edit_password" name="password" minlength="6"
                               class="w-full bg-white border border-slate-300 rounded-xl pl-3.5 pr-10 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition"
                               placeholder="Biarkan kosong jika tetap menggunakan kata sandi lama">
                        <button type="button" onclick="togglePasswordVisibility('edit_password')" title="Lihat/Sembunyikan Kata Sandi" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Status Akun Switch -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl" id="edit_status_wrapper">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-slate-800">Status Keaktifan Akun</span>
                            <p class="text-xs text-slate-500" id="edit_status_desc">Akun aktif dapat melakukan aksi sesuai perannya.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
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
    <!-- 3. MODAL: KONFIRMASI TOGGLE STATUS (ON / OFF)                             -->
    <!-- ========================================================================= -->
    <div id="toggleModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transition-all transform animate-in fade-in duration-200">
            
            <div class="p-6 text-center">
                <div id="toggleModalIcon" class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-4 border border-amber-300 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                
                <h3 class="text-lg font-bold text-slate-900" id="toggleModalTitle">Nonaktifkan Akun Pengguna?</h3>
                
                <div class="mt-3 text-xs sm:text-sm text-slate-700 leading-relaxed space-y-2 text-left bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <p id="toggleModalDesc">
                        Anda akan menonaktifkan akun untuk <strong><span id="toggleTargetName"></span></strong>.
                    </p>
                    <div id="toggleWarningBox" class="p-3 bg-amber-50/80 border border-amber-200 rounded-lg text-amber-900 text-xs">
                        <strong>Dampak Penonaktifan (PRD F1 / BR2):</strong>
                        <ul class="list-disc list-inside mt-1.5 space-y-1">
                            <li>Pengguna tetap bisa masuk sistem untuk melihat riwayat kehadiran (Mode Lihat Saja).</li>
                            <li>Pengguna tidak dapat menambah, mengubah, atau menghapus data absensi baru.</li>
                            <li>Seluruh data lama tetap aman dan tidak akan terhapus.</li>
                        </ul>
                    </div>
                </div>

                <form id="toggleForm" action="" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    <?= \Core\Guard::csrfField() ?>
                    <button type="button" onclick="closeToggleModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="toggleSubmitBtn" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <span>Konfirmasi</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. MODAL: KONFIRMASI HAPUS PENGGUNA                                       -->
    <!-- ========================================================================= -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transition-all transform animate-in fade-in duration-200">
            
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 border border-red-300 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                
                <h3 class="text-lg font-bold text-slate-900">Hapus Akun Pengguna?</h3>
                
                <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus akun <strong><span id="deleteTargetName"></span></strong>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="mt-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-left text-xs text-red-800 leading-relaxed">
                    <p class="font-bold flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Pencegahan Penghapusan Data:
                    </p>
                    <p>
                        Jika pengguna ini telah terdaftar sebagai pengampu mata kuliah atau memiliki riwayat absensi, sistem akan secara otomatis menolak penghapusan untuk menjaga keutuhan dokumen. Anda dapat menggunakan tombol <strong>Nonaktifkan</strong> sebagai pilihan yang aman.
                    </p>
                </div>

                <form id="deleteForm" action="" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    <?= \Core\Guard::csrfField() ?>
                    <button type="button" onclick="closeDeleteModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Ya, Hapus Akun</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Floating Bottom Navigation Bar (Khusus Tampilan Mobile) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>

    <!-- ========================================================================= -->
    <!-- JAVASCRIPT LOGIC: MODALS, LIVE FILTER & DYNAMIC BEHAVIOR                  -->
    <!-- ========================================================================= -->
    <script>
        const BASE_URL = '<?= \Core\Guard::url('') ?>';

        // ---------------------------------------------------------------------
        // 1. Live Filter & Search Functionality
        // ---------------------------------------------------------------------
        const searchInput    = document.getElementById('searchInput');
        const roleFilter     = document.getElementById('roleFilter');
        const statusFilter   = document.getElementById('statusFilter');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const emptyState     = document.getElementById('emptyState');
        const displayedCount = document.getElementById('displayedCount');

        function applyFilters() {
            const query  = searchInput.value.toLowerCase().trim();
            const role   = roleFilter.value;
            const status = statusFilter.value;

            // Toggle clear button
            if (query.length > 0) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }

            const rows = document.querySelectorAll('.user-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const nama     = (row.dataset.nama || '').toLowerCase();
                const identity = (row.dataset.identity || '').toLowerCase();
                const email    = (row.dataset.email || '').toLowerCase();
                const nohp     = (row.dataset.nohp || '').toLowerCase();
                const userRole = row.dataset.role || '';
                const userStat = row.dataset.active || '';

                // Matching search
                const matchSearch = query === '' || 
                                    nama.includes(query) || 
                                    identity.includes(query) || 
                                    email.includes(query) || 
                                    nohp.includes(query);

                // Matching role
                const matchRole = role === '' || userRole === role;

                // Matching status
                const matchStatus = status === '' || userStat === status;

                if (matchSearch && matchRole && matchStatus) {
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
            roleFilter.value = '';
            statusFilter.value = '';
            applyFilters();
        }

        // ---------------------------------------------------------------------
        // 2. Dynamic Label Helper (NPM vs NIDN)
        // ---------------------------------------------------------------------
        function updateIdentityLabel(prefix, role) {
            const labelEl = document.getElementById(`${prefix}_identity_label`);
            const inputEl = document.getElementById(`${prefix}_identity_number`);
            if (!labelEl || !inputEl) return;

            if (role === 'dosen') {
                labelEl.innerHTML = 'Nomor Induk Dosen Nasional (NIDN) <span class="text-red-500">*</span>';
                inputEl.placeholder = 'Contoh NIDN: 0012057801';
            } else if (role === 'asdos') {
                labelEl.innerHTML = 'Nomor Pokok Mahasiswa (NPM) <span class="text-red-500">*</span>';
                inputEl.placeholder = 'Contoh NPM: 21082010001';
            } else {
                labelEl.innerHTML = 'Username / Nomor Identitas Admin <span class="text-red-500">*</span>';
                inputEl.placeholder = 'Contoh ID: admin_lab';
            }
        }

        // ---------------------------------------------------------------------
        // 3. Password Toggle Visibility Helper
        // ---------------------------------------------------------------------
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // ---------------------------------------------------------------------
        // 4. Modal Handlers: CREATE
        // ---------------------------------------------------------------------
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            updateIdentityLabel('create', document.querySelector('input[name="role"]:checked')?.value || 'asdos');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ---------------------------------------------------------------------
        // 5. Modal Handlers: EDIT
        // ---------------------------------------------------------------------
        function openEditModal(row) {
            if (!row) return;

            const id       = row.dataset.id;
            const nama     = row.dataset.nama;
            const identity = row.dataset.identity;
            const email    = row.dataset.email;
            const nohp     = row.dataset.nohp;
            const role     = row.dataset.role;
            const active   = row.dataset.active === '1';
            const isSelf   = row.dataset.isSelf === '1';

            // Set Action URL
            document.getElementById('editForm').action = `${BASE_URL}/superadmin/users/${id}/update`;

            // Fill Form Values
            document.getElementById('edit_nama').value            = nama;
            document.getElementById('edit_identity_number').value = identity;
            document.getElementById('edit_email').value           = email;
            document.getElementById('edit_nohp').value            = nohp;
            document.getElementById('edit_password').value        = '';
            document.getElementById('edit_is_active').checked     = active;

            // Set Radio Role
            const roleRadio = document.getElementById(`edit_role_${role === 'super_admin' ? 'admin' : role}`);
            if (roleRadio) roleRadio.checked = true;

            updateIdentityLabel('edit', role);

            // Subtitle info
            document.getElementById('edit_modal_subtitle').textContent = `Mengedit ID #${id} — ${nama}`;

            // If editing self, disable status turning off and role changing
            const statusCheckbox = document.getElementById('edit_is_active');
            const statusDesc     = document.getElementById('edit_status_desc');

            if (isSelf) {
                statusCheckbox.disabled = true;
                statusDesc.textContent  = 'Anda tidak dapat menonaktifkan akun Anda sendiri saat sedang login.';
            } else {
                statusCheckbox.disabled = false;
                statusDesc.textContent  = 'Akun aktif dapat melakukan aksi sesuai perannya.';
            }

            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ---------------------------------------------------------------------
        // 6. Modal Handlers: TOGGLE STATUS (ON / OFF)
        // ---------------------------------------------------------------------
        function openToggleModal(id, nama, currentStatus) {
            const isDeactivating = currentStatus === 1;

            document.getElementById('toggleForm').action = `${BASE_URL}/superadmin/users/${id}/toggle`;
            document.getElementById('toggleTargetName').textContent = nama;

            const iconEl       = document.getElementById('toggleModalIcon');
            const titleEl      = document.getElementById('toggleModalTitle');
            const descEl       = document.getElementById('toggleModalDesc');
            const warningBox   = document.getElementById('toggleWarningBox');
            const submitBtn    = document.getElementById('toggleSubmitBtn');

            if (isDeactivating) {
                // Nonaktifkan
                iconEl.className = 'w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-4 border border-amber-300 shadow-2xs';
                titleEl.textContent = 'Nonaktifkan Akun Pengguna?';
                descEl.innerHTML = `Anda akan mengubah akun <strong>${nama}</strong> menjadi status <strong>Nonaktif</strong>.`;
                warningBox.classList.remove('hidden');
                submitBtn.className = 'px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer';
                submitBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    <span>Ya, Nonaktifkan Akun</span>
                `;
            } else {
                // Aktifkan Kembali
                iconEl.className = 'w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-4 border border-emerald-300 shadow-2xs';
                titleEl.textContent = 'Aktifkan Kembali Akun Pengguna?';
                descEl.innerHTML = `Anda akan mengaktifkan kembali akun <strong>${nama}</strong>. Pengguna akan kembali mendapatkan akses penuh untuk login dan mencatat/memverifikasi absensi.`;
                warningBox.classList.add('hidden');
                submitBtn.className = 'px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer';
                submitBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Ya, Aktifkan Akun</span>
                `;
            }

            document.getElementById('toggleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeToggleModal() {
            document.getElementById('toggleModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ---------------------------------------------------------------------
        // 7. Modal Handlers: DELETE
        // ---------------------------------------------------------------------
        function openDeleteModal(id, nama) {
            document.getElementById('deleteForm').action = `${BASE_URL}/superadmin/users/${id}/delete`;
            document.getElementById('deleteTargetName').textContent = nama;

            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close modal when pressing Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
                closeToggleModal();
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
