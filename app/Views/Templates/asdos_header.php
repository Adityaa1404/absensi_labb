<?php
/**
 * Global Desktop Sidebar Template untuk Asisten Dosen (Asdos)
 * Khusus untuk tampilan layar Desktop (md ke atas).
 * Navigasi mobile ditangani secara terpisah oleh asdos_bottom_nav.php.
 */
$currentUser = $currentUser ?? \Core\Guard::user();
$reqUri      = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$isActive    = (int)($currentUser['is_active'] ?? 0) === 1;

$desktopNavItems = [
    [
        'id'     => 'dashboard',
        'label'  => 'Dashboard',
        'url'    => \Core\Guard::url('/asdos/dashboard'),
        'active' => str_contains($reqUri, '/asdos/dashboard') || $reqUri === \Core\Guard::url('/') || $reqUri === \Core\Guard::url(''),
    ],
    [
        'id'     => 'matkul',
        'label'  => 'Matkul Saya',
        'url'    => \Core\Guard::url('/asdos/matkul'),
        'active' => str_contains($reqUri, '/asdos/matkul'),
    ],
    // [
    //     'id'     => 'absensi',
    //     'label'  => 'Isi Absensi',
    //     'url'    => \Core\Guard::url('/asdos/absensi'),
    //     'active' => str_contains($reqUri, '/asdos/absensi'),
    // ],
    // [
    //     'id'     => 'history',
    //     'label'  => 'Riwayat Absensi',
    //     'url'    => \Core\Guard::url('/asdos/history'),
    //     'active' => str_contains($reqUri, '/asdos/history'),
    // ],
];
?>

<!-- ========================================================================= -->
<!-- MOBILE TOP APP BAR / HEADER (KHUSUS MOBILE md:hidden)                      -->
<!-- ========================================================================= -->
<header class="md:hidden sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-2xs px-4 py-2.5 flex items-center justify-between select-none">
    <!-- Brand -->
    <a href="<?= \Core\Guard::url('/asdos/dashboard') ?>" class="flex items-center gap-2.5 group">
        <div class="w-8 h-8 rounded-lg bg-[#1867c0] flex items-center justify-center text-white font-bold text-xs shadow-2xs shrink-0 group-hover:scale-105 transition-transform">
            LAB
        </div>
        <div>
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-bold text-slate-900 leading-tight">Absensi Lab</span>
                <span class="px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wider bg-blue-50 text-[#1867c0] border border-blue-200 rounded">
                    ASDOS
                </span>
            </div>
            <p class="text-[10px] text-slate-400 leading-tight">Lab Sistem Informasi</p>
        </div>
    </a>

    <!-- User & Logout Action -->
    <div class="flex items-center gap-2">
        <div class="flex items-center gap-1.5 bg-slate-100/90 px-2.5 py-1 rounded-xl border border-slate-200/80 shadow-2xs">
            <div class="w-5 h-5 rounded-full bg-blue-100 text-[#1867c0] flex items-center justify-center text-[10px] font-bold shrink-0">
                <?= strtoupper(substr($currentUser['nama'] ?? 'A', 0, 1)) ?>
            </div>
            <span class="text-[11px] font-bold text-slate-700 max-w-[85px] truncate">
                <?= htmlspecialchars(explode(' ', trim($currentUser['nama'] ?? 'Asdos'))[0], ENT_QUOTES, 'UTF-8') ?>
            </span>
            <?php if (!$isActive): ?>
                <span class="px-1 py-0.2 text-[8px] font-bold uppercase bg-slate-200 text-slate-600 rounded">Off</span>
            <?php endif; ?>
        </div>

        <a href="<?= \Core\Guard::url('/logout') ?>" 
           title="Keluar dari sistem"
           aria-label="Keluar dari sistem"
           class="p-2 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 transition duration-150 flex items-center justify-center cursor-pointer active:scale-95 shadow-2xs hover:shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </a>
    </div>
</header>

<!-- ========================================================================= -->
<!-- DESKTOP SIDEBAR NAVIGATION (FIXED LEFT BAR KHUSUS DESKTOP)                -->
<!-- ========================================================================= -->
<aside class="hidden md:flex fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex-col justify-between shadow-xs select-none">
    <div class="flex flex-col flex-1 overflow-y-auto">

        <!-- Brand Header -->
        <a href="<?= \Core\Guard::url('/asdos/dashboard') ?>" class="p-5 border-b border-slate-100 flex items-center gap-3 group transition">
            <div class="w-10 h-10 rounded-xl bg-[#1867c0] group-hover:bg-[#14529d] flex items-center justify-center text-white font-bold text-sm shadow-xs shrink-0 transition-transform duration-200 group-hover:scale-105">
                LAB
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="text-sm font-bold text-slate-900 leading-tight group-hover:text-[#1867c0] transition truncate">Absensi Lab</span>
                    <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider bg-blue-50 text-[#1867c0] border border-blue-200 rounded-md shrink-0">
                        ASDOS
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 truncate mt-0.5">Lab Sistem Informasi</p>
            </div>
        </a>

        <?php if (!$isActive): ?>
            <!-- Banner Peringatan Akun Nonaktif (F1: mode lihat saja) -->
            <div class="mx-3.5 mt-3.5 p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-[11px] leading-relaxed flex items-start gap-2">
                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span><strong>Akun Nonaktif</strong> — mode lihat saja, hanya riwayat yang dapat diakses.</span>
            </div>
        <?php endif; ?>

        <!-- Navigation Section -->
        <div class="p-3.5 space-y-1">
            <p class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</p>

            <nav class="space-y-1" aria-label="Menu Asisten Dosen">
                <?php foreach ($desktopNavItems as $nav): ?>
                    <?php if ($nav['active']): ?>
                        <a href="<?= $nav['url'] ?>"
                           class="block px-3.5 py-2.5 rounded-xl text-xs font-bold bg-[#1867c0] text-white shadow-xs shadow-blue-500/20 transition duration-150">
                            <span class="truncate"><?= $nav['label'] ?></span>
                        </a>
                    <?php else: ?>
                        <a href="<?= $nav['url'] ?>"
                           class="block px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition duration-150">
                            <span class="truncate"><?= $nav['label'] ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        </div>

    </div>

    <!-- Bottom User Profile & Logout -->
    <div class="p-3.5 border-t border-slate-100 bg-slate-50/60 space-y-2.5">
        <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 text-[#1867c0] flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                <?= strtoupper(substr($currentUser['nama'] ?? 'A', 0, 1)) ?>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-800 truncate leading-tight"><?= htmlspecialchars($currentUser['nama'] ?? 'Asdos', ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-[10px] text-slate-400 truncate mt-0.5"><?= htmlspecialchars($currentUser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php if (!$isActive): ?>
                <span class="shrink-0 px-1.5 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-slate-200 text-slate-600" title="Akun nonaktif">Off</span>
            <?php endif; ?>
        </div>

        <a href="<?= \Core\Guard::url('/logout') ?>"
           title="Keluar dari sistem"
           class="w-full px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-bold transition duration-150 flex items-center justify-center gap-2 cursor-pointer shadow-2xs hover:shadow-xs active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Keluar Sistem</span>
        </a>
    </div>
</aside>