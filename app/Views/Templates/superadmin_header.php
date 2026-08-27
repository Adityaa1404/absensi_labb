<?php
/**
 * Global Header / Desktop Navbar Template untuk Super Admin
 * Menyediakan layout, sizing, dan interaksi yang 100% konsisten di semua halaman.
 */
$currentUser = $currentUser ?? \Core\Guard::user();
$reqUri      = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

$desktopNavItems = [
    [
        'id'     => 'dashboard',
        'label'  => 'Dashboard',
        'url'    => \Core\Guard::url('/superadmin/dashboard'),
        'active' => str_contains($reqUri, '/superadmin/dashboard') || $reqUri === \Core\Guard::url('/') || $reqUri === \Core\Guard::url(''),
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'
    ],
    [
        'id'     => 'users',
        'label'  => 'Kelola Pengguna',
        'url'    => \Core\Guard::url('/superadmin/users'),
        'active' => str_contains($reqUri, '/superadmin/users'),
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'
    ],
    [
        'id'     => 'matkul',
        'label'  => 'Mata Kuliah',
        'url'    => \Core\Guard::url('/superadmin/matkul'),
        'active' => str_contains($reqUri, '/superadmin/matkul'),
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'
    ],
    [
        'id'     => 'plotting',
        'label'  => 'Plotting Asdos',
        'url'    => \Core\Guard::url('/superadmin/plotting'),
        'active' => str_contains($reqUri, '/superadmin/plotting'),
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'
    ],
    [
        'id'     => 'monitoring',
        'label'  => 'Monitoring Absensi',
        'url'    => \Core\Guard::url('/superadmin/monitoring'),
        'active' => str_contains($reqUri, '/superadmin/monitoring'),
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
    ],
];
?>
<header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">

        <!-- Brand / Logo -->
        <a href="<?= \Core\Guard::url('/superadmin/dashboard') ?>" class="flex items-center gap-3 group cursor-pointer select-none">
            <div class="w-10 h-10 rounded-xl bg-[#1867c0] group-hover:bg-[#14529d] flex items-center justify-center text-white font-bold text-sm shadow-xs transition-all duration-200 group-hover:scale-105">
                LAB
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-slate-900 leading-tight group-hover:text-[#1867c0] transition">Absensi Lab</span>
                    <span class="px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider bg-blue-50 text-[#1867c0] border border-blue-200 rounded-md">
                        SUPER ADMIN
                    </span>
                </div>
                <p class="text-xs text-slate-500 hidden sm:block">Laboratorium Sistem Informasi</p>
            </div>
        </a>

        <!-- Navigation Links (Desktop) -->
        <nav class="hidden md:flex items-center gap-1.5" aria-label="Menu Utama Super Admin">
            <?php foreach ($desktopNavItems as $nav): ?>
                <?php if ($nav['active']): ?>
                    <a href="<?= $nav['url'] ?>" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold bg-[#1867c0] text-white shadow-xs flex items-center gap-1.5 transition-all duration-150 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $nav['icon'] ?>
                        </svg>
                        <span><?= $nav['label'] ?></span>
                    </a>
                <?php else: ?>
                    <a href="<?= $nav['url'] ?>" 
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition-all duration-150 hover:-translate-y-0.5 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <?= $nav['icon'] ?>
                        </svg>
                        <span><?= $nav['label'] ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <!-- User Info & Logout Button -->
        <div class="flex items-center gap-3">
            <div class="hidden sm:block text-right">
                <p class="text-xs font-bold text-slate-800 leading-tight"><?= htmlspecialchars($currentUser['nama'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-xs text-slate-500"><?= htmlspecialchars($currentUser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <a href="<?= \Core\Guard::url('/logout') ?>" 
               title="Keluar dari sistem"
               class="px-3.5 py-2 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-bold transition-all duration-150 hover:shadow-xs active:scale-95 flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar</span>
            </a>
        </div>

    </div>
</header>
