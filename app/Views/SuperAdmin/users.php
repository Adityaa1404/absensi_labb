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

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

        <!-- Page Header Banner -->
        <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Kelola Data Pengguna</h1>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 shadow-xs hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Tambah Pengguna Baru</span>
                </button>
            </div>
        </div>

        <!-- Metric / Stat Cards Grid (3 Columns with Lift Hover) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
            
            <!-- 1. Total Pengguna -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-blue-300">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pengguna</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1"><?= $metrics['total'] ?></p>
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
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
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
                    </div>
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
                            <option value="">Semua Peran</option>
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
                                                <!-- <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                                    <?php if (!empty($u['identity_number'])): ?>
                                                        <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                                            <?= $identityLabel ?>: <?= htmlspecialchars($u['identity_number'], ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span>#<?= $u['id_user'] ?></span>
                                                </div> -->
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

                                    <!-- 4. Status Akun (Interactive Slider On/Off Switch - Zero Reload) -->
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        <?php if ($isSelf): ?>
                                            <div class="inline-flex items-center gap-2 opacity-60 cursor-not-allowed" title="Akun Anda sendiri (Tidak dapat dinonaktifkan)">
                                                <div class="w-10 h-5 bg-emerald-500 rounded-full relative shadow-xs">
                                                    <div class="absolute top-[2px] right-[2px] bg-white rounded-full h-4 w-4 shadow-2xs"></div>
                                                </div>
                                                <span class="text-xs font-bold text-emerald-700">Aktif</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="inline-flex items-center m-0">
                                                <label class="relative inline-flex items-center cursor-pointer group m-0" title="<?= $isActive ? 'Klik untuk menonaktifkan akun' : 'Klik untuk mengaktifkan akun' ?>">
                                                    <input type="checkbox" 
                                                           id="user_status_switch_<?= $u['id_user'] ?>"
                                                           onchange="toggleUserStatusAsync(<?= $u['id_user'] ?>, this)" 
                                                           <?= $isActive ? 'checked' : '' ?> 
                                                           class="sr-only peer">
                                                    <div class="w-10 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                                    <span id="user_status_text_<?= $u['id_user'] ?>" class="ml-2 text-xs font-bold transition-colors <?= $isActive ? 'text-emerald-700 group-hover:text-emerald-800' : 'text-slate-400 group-hover:text-slate-600' ?>">
                                                        <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                                                    </span>
                                                </label>
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
        </div>

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. MODAL: TAMBAH PENGGUNA BARU                                           -->
    <!-- ========================================================================= -->
    <div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden transition-all transform animate-in fade-in duration-200 my-6">
            
            <!-- Modal Header -->
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/90">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#1867c0] flex items-center justify-center shrink-0 shadow-2xs border border-blue-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Tambah Pengguna Baru</h3>
                    </div>
                </div>
                <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="createForm" action="<?= \Core\Guard::url('/superadmin/users/create') ?>" method="POST">
                <?= \Core\Guard::csrfField() ?>

                <div class="p-5 sm:p-6 space-y-5 max-h-[75vh] overflow-y-auto">

                    <!-- Alert Box untuk Error Validasi Form -->
                    <div id="create_error_alert" class="hidden p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl flex items-start gap-2.5 shadow-2xs">
                        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="flex-1 min-w-0" id="create_error_message"></div>
                    </div>

                    <!-- SECTION 1: PERAN AKUN (ROLE) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                1. Pilih Peran Akun (Role) <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
                            <label class="group relative flex flex-col items-center justify-center p-3 sm:p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 has-checked:border-[#1867c0] has-checked:bg-blue-50/80 transition-all duration-150 shadow-2xs">
                                <input type="radio" name="role" value="asdos" checked onchange="updateIdentityLabel('create', this.value)" class="sr-only">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1867c0] flex items-center justify-center mb-1.5 group-has-checked:bg-[#1867c0] group-has-checked:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-800 group-has-checked:text-[#1867c0]">Asdos</span>
                            </label>
                            <label class="group relative flex flex-col items-center justify-center p-3 sm:p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 has-checked:border-indigo-600 has-checked:bg-indigo-50/80 transition-all duration-150 shadow-2xs">
                                <input type="radio" name="role" value="dosen" onchange="updateIdentityLabel('create', this.value)" class="sr-only">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center mb-1.5 group-has-checked:bg-indigo-700 group-has-checked:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-800 group-has-checked:text-indigo-900">Dosen</span>
                            </label>
                            <label class="group relative flex flex-col items-center justify-center p-3 sm:p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-amber-400 hover:bg-amber-50/40 has-checked:border-amber-600 has-checked:bg-amber-50/80 transition-all duration-150 shadow-2xs">
                                <input type="radio" name="role" value="super_admin" onchange="updateIdentityLabel('create', this.value)" class="sr-only">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center mb-1.5 group-has-checked:bg-amber-700 group-has-checked:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-800 group-has-checked:text-amber-950">Admin</span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 2: IDENTITAS PENGGUNA -->
                    <div class="space-y-3.5 pt-1">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            2. Informasi Identitas
                        </label>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Nama Lengkap -->
                            <div>
                                <label for="create_nama" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <input type="text" id="create_nama" name="nama" required maxlength="50"
                                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                           placeholder="Nama lengkap...">
                                </div>
                            </div>

                            <!-- Nomor Identitas (NPM / NIDN) -->
                            <div>
                                <label for="create_identity_number" id="create_identity_label" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor Pokok Mahasiswa (NPM) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                    </div>
                                    <input type="text" id="create_identity_number" name="identity_number" required maxlength="100"
                                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm font-mono text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                           placeholder="Contoh NPM: 21082010001">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: KONTAK & KOMUNIKASI -->
                    <div class="space-y-3.5 pt-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Email -->
                            <div>
                                <label for="create_email" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Alamat Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input type="email" id="create_email" name="email" required maxlength="80"
                                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                           placeholder="nama@upnjatim.ac.id">
                                </div>
                            </div>

                            <!-- No WhatsApp / HP -->
                            <div>
                                <label for="create_nohp" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor WhatsApp / HP
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <input type="tel" id="create_nohp" name="no_hp" maxlength="20"
                                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                           placeholder="081234567890">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: KEAMANAN & STATUS AKUN -->
                    <div class="space-y-3.5 pt-1">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            3. Keamanan & Status Keaktifan
                        </label>
                        
                        <!-- Kata Sandi -->
                        <div>
                            <label for="create_password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Kata Sandi Awal <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" id="create_password" name="password" required minlength="6"
                                       class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-10 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                       placeholder="Masukkan kata sandi awal akun...">
                                <button type="button" onclick="togglePasswordVisibility('create_password')" title="Lihat/Sembunyikan Kata Sandi" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Status Akun Awal Switch -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs sm:text-sm font-bold text-slate-800">Status Akun Awal</span>
                                    <p class="text-xs text-slate-500 mt-0.5" id="create_status_desc">Pengguna dapat langsung login dan beraktivitas.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer group m-0">
                                    <input type="checkbox" id="create_is_active" name="is_active" value="1" checked onchange="updateModalStatusLabel('create', this.checked)" class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                    <span id="create_status_badge" class="ml-2.5 text-xs font-bold text-emerald-700">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-5 sm:px-6 py-4 bg-slate-50/90 border-t border-slate-200 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="createSubmitBtn" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
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
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden transition-all transform animate-in fade-in duration-200 my-6">
            
            <!-- Modal Header -->
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/90">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 shadow-2xs border border-amber-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Perbarui Data Pengguna</h3>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="editForm" action="" method="POST">
                <?= \Core\Guard::csrfField() ?>

                <div class="p-5 sm:p-6 space-y-5 max-h-[75vh] overflow-y-auto">

                    <!-- Alert Box untuk Error Validasi Form -->
                    <div id="edit_error_alert" class="hidden p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl flex items-start gap-2.5 shadow-2xs">
                        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="flex-1 min-w-0" id="edit_error_message"></div>
                    </div>

                    <!-- SECTION 1: PERAN AKUN (ROLE) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                1. Pilih Peran Akun (Role) <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
                            <label id="edit_role_asdos_label" class="group relative flex flex-col items-center justify-center p-3 sm:p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 has-checked:border-[#1867c0] has-checked:bg-blue-50/80 transition-all duration-150 shadow-2xs">
                                <input type="radio" id="edit_role_asdos" name="role" value="asdos" onchange="updateIdentityLabel('edit', this.value)" class="sr-only">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1867c0] flex items-center justify-center mb-1.5 group-has-checked:bg-[#1867c0] group-has-checked:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-800 group-has-checked:text-[#1867c0]">Asdos</span>
                            </label>
                            <label id="edit_role_dosen_label" class="group relative flex flex-col items-center justify-center p-3 sm:p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 has-checked:border-indigo-600 has-checked:bg-indigo-50/80 transition-all duration-150 shadow-2xs">
                                <input type="radio" id="edit_role_dosen" name="role" value="dosen" onchange="updateIdentityLabel('edit', this.value)" class="sr-only">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center mb-1.5 group-has-checked:bg-indigo-700 group-has-checked:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-800 group-has-checked:text-indigo-900">Dosen</span>
                            </label>
                            <label id="edit_role_admin_label" class="group relative flex flex-col items-center justify-center p-3 sm:p-3.5 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-amber-400 hover:bg-amber-50/40 has-checked:border-amber-600 has-checked:bg-amber-50/80 transition-all duration-150 shadow-2xs">
                                <input type="radio" id="edit_role_admin" name="role" value="super_admin" onchange="updateIdentityLabel('edit', this.value)" class="sr-only">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center mb-1.5 group-has-checked:bg-amber-700 group-has-checked:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-800 group-has-checked:text-amber-950">Admin</span>
                            </label>

                        </div>
                    </div>

                    <!-- SECTION 2: IDENTITAS PENGGUNA -->
                    <div class="space-y-3.5 pt-1">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            2. Informasi Identitas
                        </label>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Nama Lengkap -->
                            <div>
                                <label for="edit_nama" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <input type="text" id="edit_nama" name="nama" required maxlength="50"
                                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                           placeholder="Nama lengkap...">
                                </div>
                            </div>

                            <!-- Nomor Identitas (NPM / NIDN) -->
                            <div>
                                <label for="edit_identity_number" id="edit_identity_label" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor Identitas <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                    </div>
                                    <input type="text" id="edit_identity_number" name="identity_number" required maxlength="100"
                                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm font-mono text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                           placeholder="Contoh NPM: 21082010001">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: KONTAK & KOMUNIKASI -->
                    <div class="space-y-3.5 pt-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Email -->
                            <div>
                                <label for="edit_email" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Alamat Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input type="email" id="edit_email" name="email" required maxlength="80"
                                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                           placeholder="nama@upnjatim.ac.id">
                                </div>
                            </div>

                            <!-- No WhatsApp / HP -->
                            <div>
                                <label for="edit_nohp" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor WhatsApp / HP
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <input type="tel" id="edit_nohp" name="no_hp" maxlength="20"
                                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                           placeholder="081234567890">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: KEAMANAN & STATUS AKUN -->
                    <div class="space-y-3.5 pt-1">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            3. Keamanan & Status Keaktifan
                        </label>
                        
                        <!-- Reset Kata Sandi (Opsional) -->
                        <div>
                            <label for="edit_password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Ubah Kata Sandi <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" id="edit_password" name="password" minlength="6"
                                       class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-10 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition shadow-2xs"
                                       placeholder="Kata sandi baru (minimal 6 karakter)...">
                                <button type="button" onclick="togglePasswordVisibility('edit_password')" title="Lihat/Sembunyikan Kata Sandi" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Status Akun Switch -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl" id="edit_status_wrapper">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs sm:text-sm font-bold text-slate-800">Status Keaktifan Akun</span>
                                    <p class="text-xs text-slate-500 mt-0.5" id="edit_status_desc">Akun aktif dapat melakukan aksi sesuai perannya.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer group m-0">
                                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" onchange="updateModalStatusLabel('edit', this.checked)" class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                    <span id="edit_status_badge" class="ml-2.5 text-xs font-bold text-emerald-700">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-5 sm:px-6 py-4 bg-slate-50/90 border-t border-slate-200 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="editSubmitBtn" class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. MODAL: KONFIRMASI HAPUS PENGGUNA                                       -->
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

                <form id="deleteForm" action="" method="POST" class="mt-6 flex flex-row items-center justify-center gap-3 w-full">
                    <?= \Core\Guard::csrfField() ?>
                    <button type="button" onclick="closeDeleteModal()" class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer shrink-0">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow-md transition inline-flex items-center justify-center gap-2 cursor-pointer shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Ya, Hapus Akun</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Floating Bottom Navigation Bar (Khusus Tampilan Mobile) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>
x
    <!-- ========================================================================= -->
    <!-- JAVASCRIPT LOGIC: MODALS, LIVE FILTER & DYNAMIC BEHAVIOR                  -->
    <!-- ========================================================================= -->
    <script>
        const BASE_URL = '<?= \Core\Guard::getBaseUrl() ?>';

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
        // 2. Dynamic Label & Placeholder Helper (NPM vs NIDN)
        // ---------------------------------------------------------------------
        function updateIdentityLabel(prefix, role) {
            const labelEl = document.getElementById(`${prefix}_identity_label`);
            const inputEl = document.getElementById(`${prefix}_identity_number`);
            
            if (inputEl) {
                if (role === 'dosen') {
                    inputEl.placeholder = 'Contoh NIDN: 0012057801';
                } else if (role === 'asdos') {
                    inputEl.placeholder = 'Contoh NPM: 21082010001';
                } else {
                    inputEl.placeholder = 'Contoh ID: admin_lab';
                }
            }

            if (labelEl) {
                if (role === 'dosen') {
                    labelEl.innerHTML = 'Nomor Induk Dosen Nasional (NIDN) <span class="text-red-500">*</span>';
                } else if (role === 'asdos') {
                    labelEl.innerHTML = 'Nomor Pokok Mahasiswa (NPM) <span class="text-red-500">*</span>';
                } else {
                    labelEl.innerHTML = 'Username / Nomor Identitas Admin <span class="text-red-500">*</span>';
                }
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
        // 4. Modal Status Switch Label Sync Helper
        // ---------------------------------------------------------------------
        function updateModalStatusLabel(prefix, isChecked) {
            const badgeEl = document.getElementById(`${prefix}_status_badge`);
            const descEl  = document.getElementById(`${prefix}_status_desc`);
            if (badgeEl) {
                badgeEl.textContent = isChecked ? 'Aktif' : 'Nonaktif';
                badgeEl.className = `ml-2.5 text-xs font-bold ${isChecked ? 'text-emerald-700' : 'text-slate-400'}`;
            }
            if (descEl) {
                if (isChecked) {
                    descEl.textContent = 'Akun aktif dapat melakukan aksi sesuai perannya.';
                } else {
                    descEl.textContent = 'Akun nonaktif hanya dapat melihat riwayat absensi (read-only).';
                }
            }
        }

        // ---------------------------------------------------------------------
        // 5. Modal Handlers: CREATE
        // ---------------------------------------------------------------------
        function openCreateModal() {
            const alertEl = document.getElementById('create_error_alert');
            if (alertEl) alertEl.classList.add('hidden');

            document.getElementById('createModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Set default checked to asdos if none selected
            const checkedRole = document.querySelector('#createModal input[name="role"]:checked');
            if (!checkedRole) {
                const defaultRadio = document.getElementById('create_role_asdos');
                if (defaultRadio) defaultRadio.checked = true;
            }
            updateIdentityLabel('create', document.querySelector('#createModal input[name="role"]:checked')?.value || 'asdos');
            updateModalStatusLabel('create', true);
        }

        function closeCreateModal() {
            const alertEl = document.getElementById('create_error_alert');
            if (alertEl) alertEl.classList.add('hidden');

            document.getElementById('createModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ---------------------------------------------------------------------
        // 6. Modal Handlers: EDIT
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

        // Set Radio Role & Trigger Change Event
        const radioId = `edit_role_${role === 'super_admin' ? 'admin' : role}`;
        const roleRadio = document.getElementById(radioId);
        
        if (roleRadio) {
            roleRadio.checked = true;
            // Pemicu event 'change' agar Tailwind peer-checked langsung merespon & menyalakan border
            roleRadio.dispatchEvent(new Event('change', { bubbles: true }));
        }

        updateIdentityLabel('edit', role);
        updateModalStatusLabel('edit', active);

        // Subtitle info
        const subTitleEl = document.getElementById('edit_modal_subtitle');
        if (subTitleEl) subTitleEl.textContent = `Mengedit ID #${id} — ${nama}`;

            // If editing self, disable status turning off and role changing
            const statusCheckbox = document.getElementById('edit_is_active');
            const statusDesc     = document.getElementById('edit_status_desc');

            if (isSelf) {
                statusCheckbox.disabled = true;
                statusDesc.textContent  = 'Anda tidak dapat menonaktifkan akun Anda sendiri saat sedang login.';
            } else {
                statusCheckbox.disabled = false;
            }

        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

        function closeEditModal() {
            const alertEl = document.getElementById('edit_error_alert');
            if (alertEl) alertEl.classList.add('hidden');

            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ---------------------------------------------------------------------
        // 6. Async Toggle Status (On / Off Slider - Tanpa Reload / Scroll Jump)
        // ---------------------------------------------------------------------
        const CSRF_TOKEN = '<?= \Core\Guard::csrfToken() ?>';

        async function toggleUserStatusAsync(userId, checkboxEl) {
            const isChecked = checkboxEl.checked;
            const statusTextEl = document.getElementById(`user_status_text_${userId}`);
            const row = checkboxEl.closest('tr');

            // Optimistic UI Update
            if (statusTextEl) {
                statusTextEl.textContent = isChecked ? 'Aktif' : 'Nonaktif';
                statusTextEl.className = `ml-2 text-xs font-bold transition-colors ${isChecked ? 'text-emerald-700 group-hover:text-emerald-800' : 'text-slate-400 group-hover:text-slate-600'}`;
            }
            if (row) {
                row.dataset.active = isChecked ? '1' : '0';
            }

            try {
                const formData = new FormData();
                formData.append('csrf_token', CSRF_TOKEN);

                const response = await fetch(`${BASE_URL}/superadmin/users/${userId}/toggle`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showToastNotification('success', 'Status Berhasil Diubah', result.message);
                } else {
                    throw new Error(result.message || 'Gagal mengubah status akun.');
                }
            } catch (error) {
                // Revert UI on error
                checkboxEl.checked = !isChecked;
                if (statusTextEl) {
                    statusTextEl.textContent = !isChecked ? 'Aktif' : 'Nonaktif';
                    statusTextEl.className = `ml-2 text-xs font-bold transition-colors ${!isChecked ? 'text-emerald-700 group-hover:text-emerald-800' : 'text-slate-400 group-hover:text-slate-600'}`;
                }
                if (row) {
                    row.dataset.active = !isChecked ? '1' : '0';
                }
                showToastNotification('error', 'Gagal Mengubah Status', error.message || 'Terjadi kesalahan sistem.');
            }
        }

        function showToastNotification(type, title, message) {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-50 flex flex-col items-center gap-2.5 w-full max-w-md px-4 pointer-events-none';
                document.body.appendChild(container);
            }

            const toastId = 'toast-async-' + Date.now();
            const isSuccess = type === 'success';
            const borderClass = isSuccess ? 'border-emerald-200' : 'border-red-200';
            const badgeBg = isSuccess ? 'bg-emerald-50' : 'bg-red-50';
            const badgeText = isSuccess ? 'text-emerald-600' : 'text-red-600';
            const titleColor = isSuccess ? 'text-emerald-950' : 'text-red-950';
            const textColor = isSuccess ? 'text-emerald-800' : 'text-red-800';
            const barColor = isSuccess ? 'bg-emerald-500' : 'bg-red-500';
            const iconSvg = isSuccess 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' 
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';

            const toastEl = document.createElement('div');
            toastEl.id = toastId;
            toastEl.className = `toast-item pointer-events-auto relative w-full bg-white/95 backdrop-blur-md border ${borderClass} rounded-xl shadow-lg shadow-slate-200/50 overflow-hidden transform transition-all duration-300 ease-out translate-y-0 opacity-100 scale-100`;
            toastEl.setAttribute('role', 'alert');
            toastEl.innerHTML = `
                <div class="p-3.5 flex items-start gap-3">
                    <div class="shrink-0 w-8 h-8 rounded-lg ${badgeBg} flex items-center justify-center ${badgeText} mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">${iconSvg}</svg>
                    </div>
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="text-xs font-bold ${titleColor} tracking-tight">${title}</h4>
                        <p class="text-xs ${textColor} mt-0.5 leading-relaxed break-words">${message}</p>
                    </div>
                    <button type="button" onclick="dismissToast('${toastId}')" class="shrink-0 text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition-colors cursor-pointer" title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="w-full bg-slate-100 h-1 overflow-hidden">
                    <div class="toast-progress h-full ${barColor} w-full" style="transition: width 2000ms linear;"></div>
                </div>
            `;

            container.appendChild(toastEl);

            const progressBar = toastEl.querySelector('.toast-progress');
            requestAnimationFrame(() => {
                if (progressBar) progressBar.style.width = '0%';
            });

            setTimeout(() => {
                dismissToast(toastId);
            }, 2000);
        }

        // ---------------------------------------------------------------------
        // 7. Form Submit Handlers with In-Modal Error Validation (No form close on error)
        // ---------------------------------------------------------------------
        const createForm = document.getElementById('createForm');
        if (createForm) {
            createForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const submitBtn = document.getElementById('createSubmitBtn');
                const alertEl   = document.getElementById('create_error_alert');
                const msgEl     = document.getElementById('create_error_message');

                alertEl.classList.add('hidden');
                submitBtn.disabled = true;
                const originalBtnContent = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menyimpan...</span>
                `;

                try {
                    const formData = new FormData(createForm);
                    const response = await fetch(createForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        window.location.reload();
                    } else {
                        throw new Error(result.message || 'Terjadi kesalahan saat menyimpan data pengguna.');
                    }
                } catch (err) {
                    msgEl.textContent = err.message || 'Terjadi kesalahan pada sistem.';
                    alertEl.classList.remove('hidden');
                    const modalBody = alertEl.closest('.overflow-y-auto');
                    if (modalBody) modalBody.scrollTo({ top: 0, behavior: 'smooth' });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                }
            });
        }

        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const submitBtn = document.getElementById('editSubmitBtn');
                const alertEl   = document.getElementById('edit_error_alert');
                const msgEl     = document.getElementById('edit_error_message');

                alertEl.classList.add('hidden');
                submitBtn.disabled = true;
                const originalBtnContent = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menyimpan...</span>
                `;

                try {
                    const formData = new FormData(editForm);
                    const response = await fetch(editForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        window.location.reload();
                    } else {
                        throw new Error(result.message || 'Terjadi kesalahan saat memperbarui data pengguna.');
                    }
                } catch (err) {
                    msgEl.textContent = err.message || 'Terjadi kesalahan pada sistem.';
                    alertEl.classList.remove('hidden');
                    const modalBody = alertEl.closest('.overflow-y-auto');
                    if (modalBody) modalBody.scrollTo({ top: 0, behavior: 'smooth' });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                }
            });
        }

        // ---------------------------------------------------------------------
        // 8. Modal Handlers: DELETE
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
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
