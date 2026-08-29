<?php
// Fallback variabel dari AsdosController
$currentUser  = $currentUser ?? \Core\Guard::user();
$plottingList = $plottingList ?? [];
?>

<!DOCTYPE html>

<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Kuliah Saya — Absensi Lab</title>

<script src="https://cdn.tailwindcss.com"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif;
    }
</style>
</head>

<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased">

<?php require_once __DIR__ . '/../Templates/notifications.php'; ?>
<?php require_once __DIR__ . '/../Templates/asdos_header.php'; ?>

<div class="md:pl-64 flex flex-col flex-1 min-h-screen">

    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8">

        <!-- Page Header -->
        <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#1867c0]">
                        Penugasan Akademik
                    </p>

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">
                        Mata Kuliah Saya
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                        Daftar seluruh mata kuliah yang pernah atau sedang diplot kepada Anda sebagai Asisten Dosen.
                    </p>
                </div>

                <div class="shrink-0 px-3 py-2 rounded-xl bg-blue-50 border border-blue-100">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[#1867c0]">
                        Total Penugasan
                    </p>
                    <p class="text-xl font-bold text-slate-800">
                        <?= count($plottingList) ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if (empty($plottingList)): ?>

            <!-- Empty State -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs p-10 sm:p-14 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>

                <h2 class="text-base font-bold text-slate-800">
                    Belum Ada Penugasan Mata Kuliah
                </h2>

                <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">
                    Anda belum diplot ke mata kuliah mana pun. Hubungi Super Admin Laboratorium untuk informasi penugasan.
                </p>
            </div>

        <?php else: ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <?php foreach ($plottingList as $plotting): ?>

                    <?php
                    $isPlottingActive = (int)($plotting['is_active'] ?? 0) === 1;

                    $periodeMulai = !empty($plotting['periode_mulai'])
                        ? date('d M Y', strtotime($plotting['periode_mulai']))
                        : '-';

                    $periodeSelesai = !empty($plotting['periode_selesai'])
                        ? date('d M Y', strtotime($plotting['periode_selesai']))
                        : '-';
                    ?>

                    <article class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden transition-all duration-200 hover:shadow-md hover:border-blue-200 flex flex-col justify-between">

                        <div>
                            <!-- Card Header -->
                            <div class="p-5 sm:p-6 border-b border-slate-100">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2.5 mb-2">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1867c0] flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>

                                            <?php if ($isPlottingActive): ?>
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-emerald-50 text-emerald-700 border-emerald-200">
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-slate-100 text-slate-500 border-slate-200">
                                                    Riwayat
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <h2 class="text-lg font-bold text-slate-900 leading-snug">
                                            <?= htmlspecialchars(
                                                $plotting['nama_matkul'] ?? 'Mata Kuliah',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="p-5 sm:p-6">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                                    Deskripsi Mata Kuliah
                                </p>

                                <p class="text-sm text-slate-600 leading-relaxed">
                                    <?= nl2br(htmlspecialchars(
                                        !empty($plotting['deskripsi_matkul'])
                                            ? $plotting['deskripsi_matkul']
                                            : 'Belum ada deskripsi mata kuliah.',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )) ?>
                                </p>
                            </div>

                            <!-- Detail -->
                            <div class="border-t border-slate-100 bg-slate-50/60">
                                <!-- Dosen -->
                                <div class="p-4 sm:p-5 flex items-start gap-3 border-b border-slate-100">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5.121 17.804A7 7 0 0112 15c2.21 0 4.18 1.025 5.459 2.625M15 11a3 3 0 11-6 0 3 3 0 016 0zm6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                            Dosen Pembimbing
                                        </p>

                                        <p class="text-sm font-semibold text-slate-800 mt-0.5">
                                            <?= htmlspecialchars(
                                                $plotting['nama_dosen'] ?? 'Belum ditentukan',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>

                                        <?php if (!empty($plotting['email_dosen'])): ?>
                                            <p class="text-xs text-slate-500 mt-0.5 break-all">
                                                <?= htmlspecialchars(
                                                    $plotting['email_dosen'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Periode -->
                                <div class="p-4 sm:p-5 grid grid-cols-2 gap-4 border-b border-slate-100">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                            Periode Mulai
                                        </p>
                                        <p class="text-sm font-semibold text-slate-700 mt-1">
                                            <?= htmlspecialchars($periodeMulai, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                            Periode Selesai
                                        </p>
                                        <p class="text-sm font-semibold text-slate-700 mt-1">
                                            <?= htmlspecialchars($periodeSelesai, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer / Actions -->
                        <div class="p-4 sm:p-5 bg-slate-50/60 border-t border-slate-100">
                            <div class="grid grid-cols-2 gap-3">
                                <a href="<?= \Core\Guard::url('/asdos/absensi') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#1867c0] hover:bg-[#14529d] active:scale-[0.98] text-white text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 shadow-xs hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Isi Absen</span>
                                </a>
                                
                                <a href="<?= \Core\Guard::url('/asdos/history') ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 active:scale-[0.98] text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 shadow-xs hover:shadow-md">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Riwayat</span>
                                </a>
                            </div>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>
</div>

<?php require_once __DIR__ . '/../Templates/asdos_bottom_nav.php'; ?>

</body>
</html>
