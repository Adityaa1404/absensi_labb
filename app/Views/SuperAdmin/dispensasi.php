<?php
// Fallback & Dokumentasi Variabel dari DispensasiController
$currentUser    = $currentUser ?? \Core\Guard::user();
$metrics        = $metrics ?? ['total' => 0, 'disetujui' => 0, 'menunggu' => 0, 'ditolak' => 0];
$dispensasiList = $dispensasiList ?? [];
$matkulList     = $matkulList ?? [];
$statusFilter   = $_GET['status'] ?? '';
$matkulFilter   = (int)($_GET['matkul_id'] ?? 0);
$searchFilter   = $_GET['search'] ?? '';

$statusBadge = static function (string $status): string {
    return match ($status) {
        'disetujui' => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-emerald-50 text-emerald-800 border-emerald-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disetujui</span>',
        'ditolak'   => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-rose-50 text-rose-800 border-rose-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Ditolak</span>',
        default     => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-amber-50 text-amber-800 border-amber-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Menunggu</span>',
    };
};
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Surat Dispensasi — Super Admin</title>

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

    <!-- Top Popup Notifications -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Sidebar -->
    <?php require_once __DIR__ . '/../Templates/superadmin_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 space-y-6">

            <!-- Page Header Banner -->
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-blue-50 text-[#1867c0] border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Monitoring Surat Dispensasi Asdos</h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau seluruh pengajuan surat dispensasi mengajar, arsip dokumen, dan cetak PDF resmi</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="<?= \Core\Guard::url('/superadmin/settings') ?>"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold transition active:scale-95 shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Pengaturan Institusi & Kop</span>
                    </a>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="<?= \Core\Guard::url('/superadmin/dispensasi') ?>" 
                   class="bg-white border <?= empty($statusFilter) ? 'border-blue-300 ring-2 ring-blue-100' : 'border-slate-200/90' ?> rounded-2xl p-4 sm:p-5 shadow-xs hover:border-blue-200 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Surat</span>
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-[#1867c0] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2"><?= $metrics['total'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Seluruh pengajuan</p>
                </a>

                <a href="<?= \Core\Guard::url('/superadmin/dispensasi?status=menunggu') ?>" 
                   class="bg-white border <?= $statusFilter === 'menunggu' ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200/90' ?> rounded-2xl p-4 sm:p-5 shadow-xs hover:border-amber-200 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Menunggu</span>
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-amber-700 mt-2"><?= $metrics['menunggu'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Pending approval Dosen</p>
                </a>

                <a href="<?= \Core\Guard::url('/superadmin/dispensasi?status=disetujui') ?>" 
                   class="bg-white border <?= $statusFilter === 'disetujui' ? 'border-emerald-400 ring-2 ring-emerald-100' : 'border-slate-200/90' ?> rounded-2xl p-4 sm:p-5 shadow-xs hover:border-emerald-200 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Disetujui</span>
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-700 mt-2"><?= $metrics['disetujui'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Dapat diunduh PDF</p>
                </a>

                <a href="<?= \Core\Guard::url('/superadmin/dispensasi?status=ditolak') ?>" 
                   class="bg-white border <?= $statusFilter === 'ditolak' ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200/90' ?> rounded-2xl p-4 sm:p-5 shadow-xs hover:border-rose-200 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Ditolak</span>
                        <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-rose-700 mt-2"><?= $metrics['ditolak'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Ditolak oleh Dosen</p>
                </a>
            </div>

            <!-- Filters & Search Bar -->
            <form method="GET" action="<?= \Core\Guard::url('/superadmin/dispensasi') ?>" 
                  class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
                
                <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
                    <a href="<?= \Core\Guard::url('/superadmin/dispensasi') ?>"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= empty($statusFilter) ? 'bg-[#1867c0] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Semua
                    </a>
                    <a href="<?= \Core\Guard::url('/superadmin/dispensasi?status=menunggu') ?>"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'menunggu' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Menunggu
                    </a>
                    <a href="<?= \Core\Guard::url('/superadmin/dispensasi?status=disetujui') ?>"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'disetujui' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Disetujui
                    </a>
                    <a href="<?= \Core\Guard::url('/superadmin/dispensasi?status=ditolak') ?>"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'ditolak' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Ditolak
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
                    <?php if (!empty($statusFilter)): ?>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>

                    <!-- Dropdown Matkul -->
                    <select name="matkul_id" class="w-full sm:w-48 px-3 py-1.5 text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-[#1867c0] transition">
                        <option value="">-- Semua Matkul --</option>
                        <?php foreach ($matkulList as $m): ?>
                            <option value="<?= $m['id_matkul'] ?>" <?= $matkulFilter === (int)$m['id_matkul'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-56">
                        <input type="text" name="search" value="<?= htmlspecialchars($searchFilter, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Cari asdos / nomor surat..."
                            class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-4 py-1.5 rounded-xl bg-[#1867c0] text-white text-xs font-semibold shadow-xs hover:bg-[#1456a0] transition">
                        Filter
                    </button>
                    <?php if (!empty($statusFilter) || !empty($matkulFilter) || !empty($searchFilter)): ?>
                        <a href="<?= \Core\Guard::url('/superadmin/dispensasi') ?>" class="w-full sm:w-auto px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold text-center transition">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>

            </form>

            <!-- Table Card -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Seluruh Data Surat Dispensasi</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Monitoring global seluruh asisten dosen dan mata kuliah</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                        <?= count($dispensasiList) ?> Surat
                    </span>
                </div>

                <?php if (empty($dispensasiList)): ?>
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Tidak Ada Data Dispensasi</h3>
                        <p class="text-xs sm:text-sm text-slate-400 max-w-md mx-auto mt-1">
                            Belum ada permohonan surat dispensasi sesuai filter pencarian.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-4">Nomor & Tanggal</th>
                                    <th class="py-3.5 px-4">Asdos Pemohon</th>
                                    <th class="py-3.5 px-4">Mata Kuliah & Dosen</th>
                                    <th class="py-3.5 px-4">Jadwal & Tempat</th>
                                    <th class="py-3.5 px-4">Tujuan Surat</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($dispensasiList as $d): ?>
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <!-- Nomor & Tanggal -->
                                        <td class="py-3.5 px-4 align-top">
                                            <span class="font-mono text-[#1867c0] bg-blue-50/80 px-2 py-0.5 rounded border border-blue-100 text-[11px] inline-block mb-1">
                                                <?= htmlspecialchars($d['nomor_surat'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <div class="text-[11px] text-slate-400">
                                                Diajukan: <?= date('d M Y', strtotime($d['created_at'])) ?>
                                            </div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold <?= ($d['jenis_kegiatan'] ?? 'magang') === 'perkuliahan' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-[#1867c0] border border-blue-200' ?>">
                                                    <?= ($d['jenis_kegiatan'] ?? 'magang') === 'perkuliahan' ? 'Izin Perkuliahan' : 'Izin Magang' ?>
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Asdos -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-900">
                                                <?= htmlspecialchars($d['nama_asdos'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-mono">
                                                NPM: <?= htmlspecialchars($d['npm_asdos'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </td>

                                        <!-- Matkul & Dosen -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-900">
                                                <?= htmlspecialchars($d['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="text-[11px] text-slate-500 mt-0.5">
                                                Dosen: <?= htmlspecialchars($d['nama_dosen'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </td>

                                        <!-- Jadwal & Tempat -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-semibold text-slate-800">
                                                <?= \App\Services\PdfDispensasi::getHariIndo($d['tanggal_pelaksanaan']) ?>, <?= \App\Services\PdfDispensasi::formatTanggalIndo($d['tanggal_pelaksanaan']) ?>
                                            </div>
                                            <div class="text-[11px] text-slate-500">
                                                <?= \App\Services\PdfDispensasi::formatJam($d['jam_mulai'] ?? '') ?> - <?= \App\Services\PdfDispensasi::formatJam($d['jam_selesai'] ?? '') ?> WIB
                                            </div>
                                            <div class="text-[11px] text-slate-600 mt-1 truncate max-w-[170px]" title="<?= htmlspecialchars($d['tempat_pelaksanaan'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($d['tempat_pelaksanaan'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </td>

                                        <!-- Tujuan Surat -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="text-slate-800 font-medium max-w-[180px] whitespace-pre-line">
                                                <?= htmlspecialchars($d['tujuan_surat'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="py-3.5 px-4 align-top">
                                            <?= $statusBadge($d['status']) ?>
                                            <?php if ($d['status'] === 'ditolak' && !empty($d['catatan_dosen'])): ?>
                                                <div class="mt-1.5 p-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-[10px] max-w-[180px]">
                                                    <span class="font-bold">Alasan:</span> <?= htmlspecialchars($d['catatan_dosen'], ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-4 align-top text-center space-x-1 whitespace-nowrap">
                                            <!-- Download PDF -->
                                            <a href="<?= \Core\Guard::url('/superadmin/dispensasi/' . $d['id_dispensasi'] . '/download') ?>" target="_blank"
                                                title="Cetak / Download PDF Resmi"
                                                class="inline-flex p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition active:scale-95 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                            </a>

                                            <!-- Hapus Dispensasi -->
                                            <form method="POST" action="<?= \Core\Guard::url('/superadmin/dispensasi/' . $d['id_dispensasi'] . '/delete') ?>" 
                                                  class="inline-block"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengajuan dispensasi ini? Tindakan ini tidak dapat dibatalkan.');">
                                                <?= \Core\Guard::csrfField() ?>
                                                <button type="submit" title="Hapus Data Dispensasi"
                                                    class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition active:scale-95 cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>
</body>

</html>
