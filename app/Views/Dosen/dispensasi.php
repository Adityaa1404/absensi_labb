<?php
// Fallback & Dokumentasi Variabel dari DispensasiController
$currentUser    = $currentUser ?? \Core\Guard::user();
$metrics        = $metrics ?? ['total' => 0, 'disetujui' => 0, 'menunggu' => 0, 'ditolak' => 0];
$dispensasiList = $dispensasiList ?? [];
$matkulList     = $matkulList ?? [];
$statusFilter   = $_GET['status'] ?? '';
$matkulFilter   = (int)($_GET['matkul_id'] ?? 0);

$statusBadge = static function (string $status): string {
    return match ($status) {
        'disetujui' => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-emerald-50 text-emerald-800 border-emerald-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disetujui</span>',
        'ditolak'   => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-rose-50 text-rose-800 border-rose-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Ditolak</span>',
        default     => '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-amber-50 text-amber-800 border-amber-300 inline-flex items-center gap-1.5 animate-pulse"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Menunggu Approval</span>',
    };
};
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Surat Dispensasi — Dosen Pengampu</title>

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

<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-purple-500 selection:text-white">

    <!-- Top Popup Notifications -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Sidebar -->
    <?php require_once __DIR__ . '/../Templates/dosen_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 space-y-6">

            <!-- Page Header Banner -->
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-purple-50 text-purple-700 border border-purple-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Approval Surat Dispensasi Asdos</h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tinjau dan setujui permohonan surat izin dispensasi mengajar dari asisten laboratorium Anda</p>
                        </div>
                    </div>
                </div>

                <?php if ($metrics['menunggu'] > 0): ?>
                    <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        <span>Ada <strong><?= $metrics['menunggu'] ?></strong> pengajuan menunggu approval Anda!</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="<?= \Core\Guard::url('/dosen/dispensasi') ?>" 
                   class="bg-white border <?= empty($statusFilter) ? 'border-purple-300 ring-2 ring-purple-100' : 'border-slate-200/90' ?> rounded-2xl p-4 sm:p-5 shadow-xs hover:border-purple-200 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Semua Pengajuan</span>
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2"><?= $metrics['total'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Seluruh riwayat surat</p>
                </a>

                <a href="<?= \Core\Guard::url('/dosen/dispensasi?status=menunggu') ?>" 
                   class="bg-white border <?= $statusFilter === 'menunggu' ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200/90' ?> rounded-2xl p-4 sm:p-5 shadow-xs hover:border-amber-200 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Menunggu Review</span>
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-amber-700 mt-2"><?= $metrics['menunggu'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Perlu tindakan persetujuan</p>
                </a>

                <a href="<?= \Core\Guard::url('/dosen/dispensasi?status=disetujui') ?>" 
                   class="bg-white border <?= $statusFilter === 'disetujui' ? 'border-emerald-400 ring-2 ring-emerald-100' : 'border-slate-200/90' ?> rounded-2xl p-4 sm:p-5 shadow-xs hover:border-emerald-200 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Telah Disetujui</span>
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-700 mt-2"><?= $metrics['disetujui'] ?></p>
                    <p class="text-[11px] text-slate-400 mt-1">Surat resmi terbit</p>
                </a>

                <a href="<?= \Core\Guard::url('/dosen/dispensasi?status=ditolak') ?>" 
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
                    <p class="text-[11px] text-slate-400 mt-1">Disertai alasan penolakan</p>
                </a>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
                    <a href="<?= \Core\Guard::url('/dosen/dispensasi' . ($matkulFilter ? '?matkul_id=' . $matkulFilter : '')) ?>"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= empty($statusFilter) ? 'bg-purple-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Semua
                    </a>
                    <a href="<?= \Core\Guard::url('/dosen/dispensasi?status=menunggu' . ($matkulFilter ? '&matkul_id=' . $matkulFilter : '')) ?>"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'menunggu' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Menunggu (<?= $metrics['menunggu'] ?>)
                    </a>
                    <a href="<?= \Core\Guard::url('/dosen/dispensasi?status=disetujui' . ($matkulFilter ? '&matkul_id=' . $matkulFilter : '')) ?>"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'disetujui' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Disetujui
                    </a>
                    <a href="<?= \Core\Guard::url('/dosen/dispensasi?status=ditolak' . ($matkulFilter ? '&matkul_id=' . $matkulFilter : '')) ?>"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition <?= $statusFilter === 'ditolak' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Ditolak
                    </a>
                </div>

                <!-- Filter Mata Kuliah Dropdown -->
                <?php if (!empty($matkulList)): ?>
                    <form method="GET" action="<?= \Core\Guard::url('/dosen/dispensasi') ?>" class="w-full sm:w-auto flex items-center gap-2">
                        <?php if (!empty($statusFilter)): ?>
                            <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <select name="matkul_id" onchange="this.form.submit()" 
                            class="w-full sm:w-64 px-3 py-1.5 text-xs rounded-xl border border-slate-300 bg-white focus:ring-2 focus:ring-purple-600 transition">
                            <option value="">-- Semua Mata Kuliah --</option>
                            <?php foreach ($matkulList as $m): ?>
                                <option value="<?= $m['id_matkul'] ?>" <?= $matkulFilter === (int)$m['id_matkul'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Table Card -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Pengajuan Surat Masuk</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Surat dispensasi mengajar dari asdos mata kuliah Anda</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                        <?= count($dispensasiList) ?> Data
                    </span>
                </div>

                <?php if (empty($dispensasiList)): ?>
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Tidak Ada Data Pengajuan</h3>
                        <p class="text-xs sm:text-sm text-slate-400 max-w-md mx-auto mt-1">
                            Belum ada permohonan surat dispensasi sesuai filter yang dipilih.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-4">Asdos Pemohon</th>
                                    <th class="py-3.5 px-4">Mata Kuliah & Nomor</th>
                                    <th class="py-3.5 px-4">Jadwal & Tempat</th>
                                    <th class="py-3.5 px-4">Tujuan Surat</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4 text-center">Tindakan / Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($dispensasiList as $d): ?>
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <!-- Asdos -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-900">
                                                <?= htmlspecialchars($d['nama_asdos'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-mono">
                                                NPM: <?= htmlspecialchars($d['npm_asdos'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                <?= htmlspecialchars($d['email_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </td>

                                        <!-- Mata Kuliah & Nomor Surat -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-slate-900">
                                                <?= htmlspecialchars($d['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <span class="font-mono text-[#1867c0] bg-blue-50/80 px-2 py-0.5 rounded border border-blue-100 text-[11px] inline-block mt-1">
                                                <?= htmlspecialchars($d['nomor_surat'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold <?= ($d['jenis_kegiatan'] ?? 'magang') === 'perkuliahan' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-[#1867c0] border border-blue-200' ?>">
                                                    <?= ($d['jenis_kegiatan'] ?? 'magang') === 'perkuliahan' ? 'Izin Perkuliahan' : 'Izin Magang' ?>
                                                </span>
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
                                            <div class="text-[11px] text-slate-600 mt-1 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate max-w-[180px]" title="<?= htmlspecialchars($d['tempat_pelaksanaan'], ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= htmlspecialchars($d['tempat_pelaksanaan'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Tujuan Surat -->
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="text-slate-800 font-medium max-w-[200px] whitespace-pre-line">
                                                <?= htmlspecialchars($d['tujuan_surat'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </td>

                                        <!-- Status & Catatan -->
                                        <td class="py-3.5 px-4 align-top">
                                            <?= $statusBadge($d['status']) ?>
                                            <?php if ($d['status'] === 'ditolak' && !empty($d['catatan_dosen'])): ?>
                                                <div class="mt-2 p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-[11px] max-w-[200px]">
                                                    <strong class="font-bold">Alasan Ditolak:</strong>
                                                    <p class="mt-0.5 whitespace-pre-line"><?= htmlspecialchars($d['catatan_dosen'], ENT_QUOTES, 'UTF-8') ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-4 align-top text-center space-y-1.5">
                                            <?php if ($d['status'] === 'menunggu'): ?>
                                                <button type="button" 
                                                    onclick="openReviewModal(<?= htmlspecialchars(json_encode([
                                                        'id'          => $d['id_dispensasi'],
                                                        'asdos'       => $d['nama_asdos'],
                                                        'npm'         => $d['npm_asdos'],
                                                        'matkul'      => $d['nama_matkul'],
                                                        'nomor_surat' => $d['nomor_surat'],
                                                        'tanggal'     => \App\Services\PdfDispensasi::formatTanggalIndo($d['tanggal_pelaksanaan']),
                                                        'tempat'      => $d['tempat_pelaksanaan'],
                                                        'tujuan'      => $d['tujuan_surat'],
                                                        'jenis'       => ($d['jenis_kegiatan'] ?? 'magang') === 'perkuliahan' ? 'Kegiatan Perkuliahan' : 'Kegiatan Magang',
                                                        'waktu'       => (!empty($d['jam_mulai']) && !empty($d['jam_selesai'])) ? (\App\Services\PdfDispensasi::formatJam($d['jam_mulai']) . ' - ' . \App\Services\PdfDispensasi::formatJam($d['jam_selesai']) . ' WIB') : 'Sesuai Jadwal',
                                                    ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs shadow-xs hover:shadow transition active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    <span>Review</span>
                                                </button>
                                            <?php elseif ($d['status'] === 'disetujui'): ?>
                                                <a href="<?= \Core\Guard::url('/dosen/dispensasi/' . $d['id_dispensasi'] . '/download') ?>" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs hover:shadow transition active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    <span>Unduh PDF</span>
                                                </a>
                                            <?php else: ?>
                                                <button type="button"
                                                    onclick="openReviewModal(<?= htmlspecialchars(json_encode([
                                                        'id'          => $d['id_dispensasi'],
                                                        'asdos'       => $d['nama_asdos'],
                                                        'npm'         => $d['npm_asdos'],
                                                        'matkul'      => $d['nama_matkul'],
                                                        'nomor_surat' => $d['nomor_surat'],
                                                        'tanggal'     => \App\Services\PdfDispensasi::formatTanggalIndo($d['tanggal_pelaksanaan']),
                                                        'tempat'      => $d['tempat_pelaksanaan'],
                                                        'tujuan'      => $d['tujuan_surat'],
                                                        'jenis'       => ($d['jenis_kegiatan'] ?? 'magang') === 'perkuliahan' ? 'Kegiatan Perkuliahan' : 'Kegiatan Magang',
                                                        'waktu'       => (!empty($d['jam_mulai']) && !empty($d['jam_selesai'])) ? (\App\Services\PdfDispensasi::formatJam($d['jam_mulai']) . ' - ' . \App\Services\PdfDispensasi::formatJam($d['jam_selesai']) . ' WIB') : 'Sesuai Jadwal',
                                                    ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                    class="text-slate-500 hover:text-slate-800 text-[11px] underline cursor-pointer">
                                                    Ubah Status
                                                </button>
                                            <?php endif; ?>
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

    <!-- MODAL REVIEW APPROVAL / REJECTION SURAT DISPENSASI -->
    <div id="modalReviewDispensasi" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" onclick="closeReviewModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-purple-700 to-indigo-700 px-5 py-4 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-white/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-base font-bold">Review Pengajuan Surat Dispensasi</h3>
                            <p class="text-xs text-purple-200">Persetujuan dosen pengampu mata kuliah</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeReviewModal()" class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Review Content -->
                <div class="p-5 sm:p-6 space-y-4 text-xs">
                    <!-- Detail Ringkasan Surat -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Asdos Pemohon:</span>
                            <span id="revAsdos" class="font-bold text-slate-900"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">NPM Asdos:</span>
                            <span id="revNpm" class="font-mono text-slate-700"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Mata Kuliah:</span>
                            <span id="revMatkul" class="font-semibold text-slate-800"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Jenis Kegiatan Izin:</span>
                            <span id="revJenis" class="font-bold text-[#1867c0]"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Tanggal Pelaksanaan:</span>
                            <span id="revTanggal" class="font-medium text-slate-800"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Waktu Perkuliahan:</span>
                            <span id="revWaktu" class="font-bold text-slate-800"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Tempat Pelaksanaan:</span>
                            <span id="revTempat" class="font-medium text-slate-800"></span>
                        </div>
                        <div class="border-t border-slate-200 pt-2">
                            <span class="text-slate-500 font-medium block mb-0.5">Tujuan Surat:</span>
                            <p id="revTujuan" class="text-slate-800 font-medium italic whitespace-pre-line bg-white p-2 rounded border border-slate-200"></p>
                        </div>
                    </div>

                    <!-- Form Action -->
                    <form id="formReviewDispensasi" method="POST" action="" class="space-y-4">
                        <?= \Core\Guard::csrfField() ?>
                        <input type="hidden" name="status" id="inputStatusAction" value="disetujui">

                        <!-- Area Catatan Dosen (Wajib jika tolak) -->
                        <div id="areaCatatanDosen" class="space-y-1 hidden">
                            <label for="catatan_dosen" class="block font-bold text-rose-700 uppercase tracking-wider">
                                Catatan / Alasan Penolakan <span class="text-rose-500">* (Wajib diisi jika menolak)</span>
                            </label>
                            <textarea name="catatan_dosen" id="catatan_dosen" rows="2" 
                                placeholder="Jelaskan alasan penolakan pengajuan surat ini kepada asisten..."
                                class="w-full px-3 py-2 rounded-xl border border-rose-300 focus:ring-2 focus:ring-rose-500 text-xs resize-none"></textarea>
                        </div>

                        <!-- Tombol Setujui vs Tolak -->
                        <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <button type="button" onclick="closeReviewModal()"
                                class="w-full sm:w-auto px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition cursor-pointer">
                                Batal
                            </button>

                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                <button type="button" id="btnRejectToggle" onclick="handleRejectMode()"
                                    class="flex-1 sm:flex-none px-4 py-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 font-bold hover:bg-rose-100 transition active:scale-95 cursor-pointer">
                                    Tolak Pengajuan
                                </button>
                                <button type="submit" id="btnApproveSubmit" onclick="handleApproveMode(event)"
                                    class="flex-1 sm:flex-none px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-sm transition active:scale-95 cursor-pointer">
                                    Setujui Pengajuan
                                </button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <?php require_once __DIR__ . '/../Templates/dosen_bottom_nav.php'; ?>

    <script>
        let currentReviewId = null;

        function openReviewModal(data) {
            currentReviewId = data.id;
            document.getElementById('formReviewDispensasi').action = '<?= \Core\Guard::url('/dosen/dispensasi/') ?>' + data.id + '/status';
            
            document.getElementById('revAsdos').textContent = data.asdos || '-';
            document.getElementById('revNpm').textContent = data.npm || '-';
            document.getElementById('revMatkul').textContent = data.matkul || '-';
            document.getElementById('revJenis').textContent = data.jenis || 'Kegiatan Magang';
            document.getElementById('revTanggal').textContent = data.tanggal || '-';
            document.getElementById('revWaktu').textContent = data.waktu || '-';
            document.getElementById('revTempat').textContent = data.tempat || '-';
            document.getElementById('revTujuan').textContent = data.tujuan || '-';

            // Reset state form
            document.getElementById('areaCatatanDosen').classList.add('hidden');
            document.getElementById('catatan_dosen').value = '';
            document.getElementById('inputStatusAction').value = 'disetujui';
            document.getElementById('btnRejectToggle').textContent = 'Tolak Pengajuan';

            const modal = document.getElementById('modalReviewDispensasi');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeReviewModal() {
            const modal = document.getElementById('modalReviewDispensasi');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function handleApproveMode(e) {
            document.getElementById('inputStatusAction').value = 'disetujui';
            // Allow form submission normally
        }

        function handleRejectMode() {
            const areaCatatan = document.getElementById('areaCatatanDosen');
            const statusInput = document.getElementById('inputStatusAction');
            const catatanInput = document.getElementById('catatan_dosen');

            if (areaCatatan.classList.contains('hidden')) {
                areaCatatan.classList.remove('hidden');
                statusInput.value = 'ditolak';
                catatanInput.focus();
                document.getElementById('btnRejectToggle').textContent = 'Konfirmasi Penolakan';
                document.getElementById('btnApproveSubmit').classList.add('opacity-50');
            } else {
                // Submit form rejection
                if (!catatanInput.value.trim()) {
                    window.highlightAndNotify(catatanInput, 'Alasan penolakan pengajuan surat wajib diisi.');
                    return;
                }
                statusInput.value = 'ditolak';
                document.getElementById('formReviewDispensasi').submit();
            }
        }

        // Form submit safety validation
        document.getElementById('formReviewDispensasi')?.addEventListener('submit', function(e) {
            const statusInput = document.getElementById('inputStatusAction');
            const catatanInput = document.getElementById('catatan_dosen');
            if (statusInput && statusInput.value === 'ditolak' && (!catatanInput || !catatanInput.value.trim())) {
                e.preventDefault();
                window.highlightAndNotify(catatanInput, 'Alasan penolakan pengajuan surat wajib diisi.');
            }
        });

        // ESC to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReviewModal();
            }
        });
    </script>
</body>

</html>
