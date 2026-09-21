<?php
/**
 * Floating Bottom Navigation Bar untuk Mobile (Dosen Pengampu)
 */
$reqUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

$navItems = [
    [
        'label'  => 'Dashboard',
        'url'    => \Core\Guard::url('/dosen/dashboard'),
        'active' => str_contains($reqUri, '/dosen/dashboard') || str_contains($reqUri, '/dosen/matkul/'),
        'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'
    ],
];
?>

<!-- Floating Bottom Navbar (Tampilan Mobile / Smartphone) -->
<nav class="fixed bottom-3 left-3 right-3 sm:left-6 sm:right-6 md:hidden z-40 bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl shadow-xl shadow-slate-900/10 px-2 py-1.5 flex items-center justify-around" aria-label="Mobile Bottom Navigation Dosen">
    <?php foreach ($navItems as $item): ?>
        <?php if ($item['active']): ?>
            <a href="<?= $item['url'] ?>" 
               class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-xl bg-blue-50/90 text-[#1867c0] font-bold transition duration-150 active:scale-95">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <?= $item['icon'] ?>
                </svg>
                <span class="text-[11px] tracking-tight leading-tight font-bold"><?= $item['label'] ?></span>
            </a>
        <?php else: ?>
            <a href="<?= $item['url'] ?>" 
               class="flex flex-col items-center justify-center flex-1 py-2 px-1 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition duration-150 active:scale-95">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <?= $item['icon'] ?>
                </svg>
                <span class="text-[11px] tracking-tight leading-tight font-medium"><?= $item['label'] ?></span>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
