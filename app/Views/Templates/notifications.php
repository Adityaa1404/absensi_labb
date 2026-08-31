<?php
/**
 * Global Toast / Notification Component
 * Menampilkan flash messages dari session dengan auto-dismiss 4 detik
 */

$flashTypes = [
    'success' => [
        'title'      => 'Berhasil',
        'border'     => 'border-emerald-200',
        'bg'         => 'bg-white',
        'badge_bg'   => 'bg-emerald-50',
        'badge_text' => 'text-emerald-600',
        'title_color'=> 'text-emerald-950',
        'text_color' => 'text-emerald-800',
        'bar_color'  => 'bg-emerald-500',
        'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
    ],
    'error' => [
        'title'      => 'Gagal',
        'border'     => 'border-red-200',
        'bg'         => 'bg-white',
        'badge_bg'   => 'bg-red-50',
        'badge_text' => 'text-red-600',
        'title_color'=> 'text-red-950',
        'text_color' => 'text-red-800',
        'bar_color'  => 'bg-red-500',
        'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    ],
    'info' => [
        'title'      => 'Informasi',
        'border'     => 'border-blue-200',
        'bg'         => 'bg-white',
        'badge_bg'   => 'bg-blue-50',
        'badge_text' => 'text-[#1867c0]',
        'title_color'=> 'text-slate-900',
        'text_color' => 'text-slate-700',
        'bar_color'  => 'bg-[#1867c0]',
        'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    ],
    'warning' => [
        'title'      => 'Peringatan',
        'border'     => 'border-amber-200',
        'bg'         => 'bg-white',
        'badge_bg'   => 'bg-amber-50',
        'badge_text' => 'text-amber-600',
        'title_color'=> 'text-amber-950',
        'text_color' => 'text-amber-800',
        'bar_color'  => 'bg-amber-500',
        'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
    ]
];

$allMessages = [];
foreach ($flashTypes as $type => $config) {
    $messages = \Core\Guard::getFlash($type);
    foreach ($messages as $msg) {
        $allMessages[] = [
            'type'   => $type,
            'config' => $config,
            'text'   => $msg
        ];
    }
}
?>

<!-- Toast Container (Fixed di bagian atas tengah layar) -->
<div id="toast-container" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 flex flex-col items-center gap-2.5 w-full max-w-md px-4 pointer-events-none" style="perspective: 1000px;">
    <?php foreach ($allMessages as $idx => $item): ?>
        <?php $cfg = $item['config']; ?>
        <div id="toast-item-<?= $idx ?>" 
             class="toast-item pointer-events-auto relative w-full bg-white/95 backdrop-blur-md border <?= $cfg['border'] ?> rounded-xl shadow-lg shadow-slate-200/50 overflow-hidden transform transition-all duration-300 ease-out translate-y-0 opacity-100 scale-100"
             role="alert">
            
            <div class="p-3.5 flex items-start gap-3">
                <!-- Icon Badge -->
                <div class="shrink-0 w-8 h-8 rounded-lg <?= $cfg['badge_bg'] ?> flex items-center justify-center <?= $cfg['badge_text'] ?> mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?= $cfg['icon'] ?>
                    </svg>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0 pr-2">
                    <h4 class="text-xs font-bold <?= $cfg['title_color'] ?> tracking-tight">
                        <?= $cfg['title'] ?>
                    </h4>
                    <p class="text-xs <?= $cfg['text_color'] ?> mt-0.5 leading-relaxed break-words">
                        <?= htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <!-- Close Button -->
                <button type="button" 
                        onclick="dismissToast('toast-item-<?= $idx ?>')" 
                        class="shrink-0 text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition-colors cursor-pointer"
                        title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Progress Bar (2 detik) -->
            <div class="w-full bg-slate-100 h-1 overflow-hidden">
                <div class="toast-progress h-full <?= $cfg['bar_color'] ?> w-full" style="transition: width 2000ms linear;"></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
/**
 * Auto-dismiss script untuk Toast Notification (2 Detik)
 */
function dismissToast(id) {
    const el = document.getElementById(id);
    if (!el) return;
    
    // Animasi keluar: slide up, fade out, scale down
    el.classList.add('opacity-0', '-translate-y-4', 'scale-95');
    setTimeout(() => {
        el.remove();
    }, 300);
}

document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.toast-item');
    
    toasts.forEach((toast) => {
        const progressBar = toast.querySelector('.toast-progress');
        const id = toast.id;

        // Mulai animasi progress bar mengecil
        requestAnimationFrame(() => {
            if (progressBar) {
                progressBar.style.width = '0%';
            }
        });

        // Auto dismiss setelah 2 detik (2000ms)
        let timer = setTimeout(() => {
            dismissToast(id);
        }, 2000);

        // Pause countdown saat hover mouse
        toast.addEventListener('mouseenter', () => {
            clearTimeout(timer);
            if (progressBar) {
                const computedWidth = window.getComputedStyle(progressBar).width;
                progressBar.style.transition = 'none';
                progressBar.style.width = computedWidth;
            }
        });

        // Resume countdown saat mouse leave
        toast.addEventListener('mouseleave', () => {
            timer = setTimeout(() => {
                dismissToast(id);
            }, 800); // beri sisa 0.8 detik setelah mouse pergi
            if (progressBar) {
                progressBar.style.transition = 'width 800ms linear';
                progressBar.style.width = '0%';
            }
        });
    });
});
</script>
