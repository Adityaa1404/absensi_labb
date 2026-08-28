<?php
// Fallback variabel dari AsdosController
$currentUser = $currentUser ?? \Core\Guard::user();

$metrics = $metrics ?? [
    'total'     => 0,
    'disetujui' => 0,
    'pending'   => 0,
    'ditolak'   => 0,
];

$absensiList   = $absensiList ?? [];
$matkulOptions = $matkulOptions ?? [];

$currentMatkul = $_GET['matkul'] ?? '';
$currentStatus = $_GET['status'] ?? '';
$currentStart  = $_GET['start'] ?? '';
$currentEnd    = $_GET['end'] ?? '';

$isActive = (int)($currentUser['is_active'] ?? 0) === 1;

$statusBadge = static function (string $status): string {
    return match ($status) {
        'disetujui' => '<span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200 uppercase tracking-wider">Disetujui</span>',
        'ditolak'   => '<span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full border bg-red-50 text-red-700 border-red-200 uppercase tracking-wider">Ditolak</span>',
        default     => '<span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full border bg-amber-50 text-amber-700 border-amber-200 uppercase tracking-wider">Pending</span>',
    };
};
?>

<!DOCTYPE html>

<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi — Absensi Lab</title>

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

    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#1867c0]">
                        Arsip Aktivitas
                    </p>

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">
                        Riwayat Absensi
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                        Seluruh riwayat absensi dan status verifikasi Anda tersimpan di halaman ini.
                    </p>
                </div>

                <?php if (!$isActive): ?>
                    <div class="px-3.5 py-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
                        <p class="text-[10px] font-bold uppercase tracking-wider">
                            Mode Lihat Saja
                        </p>

                        <p class="text-[11px] mt-0.5">
                            Akun Anda sedang nonaktif.
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Metrics -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">

            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-xs">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500">
                    Total
                </p>

                <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                    <?= (int)$metrics['total'] ?>
                </p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-emerald-700">
                    Disetujui
                </p>

                <p class="text-2xl sm:text-3xl font-bold text-emerald-800 mt-1">
                    <?= (int)$metrics['disetujui'] ?>
                </p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-xl border border-amber-200 bg-amber-50/20 shadow-xs">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-amber-700">
                    Pending
                </p>

                <p class="text-2xl sm:text-3xl font-bold text-amber-800 mt-1">
                    <?= (int)$metrics['pending'] ?>
                </p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-xl border border-red-200 bg-red-50/20 shadow-xs">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-red-700">
                    Ditolak
                </p>

                <p class="text-2xl sm:text-3xl font-bold text-red-800 mt-1">
                    <?= (int)$metrics['ditolak'] ?>
                </p>
            </div>

        </section>

        <!-- Filter -->
        <section class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">

            <div class="p-5 sm:p-6 border-b border-slate-200 bg-slate-50/60">
                <h2 class="text-base font-bold text-slate-900">
                    Filter Riwayat
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Gunakan filter untuk menemukan data absensi tertentu.
                </p>
            </div>

            <form
                method="GET"
                action="<?= \Core\Guard::url('/asdos/history') ?>"
                class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
            >

                <!-- Matkul -->
                <div>
                    <label for="matkul" class="block text-xs font-bold text-slate-700 mb-2">
                        Mata Kuliah
                    </label>

                    <select
                        id="matkul"
                        name="matkul"
                        class="w-full px-3 py-2.5 text-xs border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                    >
                        <option value="">Semua Mata Kuliah</option>

                        <?php foreach ($matkulOptions as $matkul): ?>
                            <option
                                value="<?= (int)$matkul['matkul_id'] ?>"
                                <?= (string)$currentMatkul === (string)$matkul['matkul_id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $matkul['nama_matkul'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 mb-2">
                        Status Verifikasi
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full px-3 py-2.5 text-xs border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="disetujui" <?= $currentStatus === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="ditolak" <?= $currentStatus === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>

                <!-- Start -->
                <div>
                    <label for="start" class="block text-xs font-bold text-slate-700 mb-2">
                        Dari Tanggal
                    </label>

                    <input
                        type="date"
                        id="start"
                        name="start"
                        value="<?= htmlspecialchars($currentStart, ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-3 py-2.5 text-xs border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                    >
                </div>

                <!-- End -->
                <div>
                    <label for="end" class="block text-xs font-bold text-slate-700 mb-2">
                        Sampai Tanggal
                    </label>

                    <input
                        type="date"
                        id="end"
                        name="end"
                        value="<?= htmlspecialchars($currentEnd, ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full px-3 py-2.5 text-xs border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                    >
                </div>

                <!-- Buttons -->
                <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row gap-3 pt-1">
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-[#1867c0] hover:bg-[#14529d] text-white text-xs font-semibold rounded-xl transition"
                    >
                        Terapkan Filter
                    </button>

                    <a
                        href="<?= \Core\Guard::url('/asdos/history') ?>"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl text-center transition"
                    >
                        Reset Filter
                    </a>
                </div>

            </form>
        </section>

        <!-- Data -->
        <section class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">

            <div class="p-5 sm:p-6 border-b border-slate-200 bg-slate-50/60 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Data Riwayat
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        <?= count($absensiList) ?> data ditemukan.
                    </p>
                </div>

                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Read Only
                </span>
            </div>

            <?php if (empty($absensiList)): ?>

                <div class="p-10 sm:p-14 text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z" />
                        </svg>
                    </div>

                    <h3 class="text-sm font-bold text-slate-700">
                        Tidak Ada Data
                    </h3>

                    <p class="text-xs text-slate-400 mt-1">
                        Belum ada riwayat absensi yang sesuai dengan filter.
                    </p>
                </div>

            <?php else: ?>

                <!-- Desktop Table -->
                <div class="hidden xl:block overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-5 py-4 font-bold">Tanggal</th>
                                <th class="px-5 py-4 font-bold">Mata Kuliah</th>
                                <th class="px-5 py-4 font-bold">Pertemuan</th>
                                <th class="px-5 py-4 font-bold">Waktu</th>
                                <th class="px-5 py-4 font-bold">Deskripsi</th>
                                <th class="px-5 py-4 font-bold">Status</th>
                                <th class="px-5 py-4 font-bold">Pesan Dosen</th>
                                <th class="px-5 py-4 font-bold">Dikirim</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            <?php foreach ($absensiList as $absensi): ?>
                                <tr class="align-top hover:bg-slate-50/70">

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <p class="text-xs font-semibold text-slate-700">
                                            <?= !empty($absensi['tanggal'])
                                                ? date('d M Y', strtotime($absensi['tanggal']))
                                                : '-' ?>
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="text-xs font-semibold text-slate-800 max-w-[180px]">
                                            <?= htmlspecialchars(
                                                $absensi['nama_matkul'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </p>

                                        <?php if (!empty($absensi['nama_dosen'])): ?>
                                            <p class="text-[10px] text-slate-400 mt-1">
                                                <?= htmlspecialchars(
                                                    $absensi['nama_dosen'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </p>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-5 py-4 text-xs text-slate-600">
                                        <?= htmlspecialchars(
                                            (string)($absensi['pertemuan_ke'] ?? '-'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-600">
                                        <?= htmlspecialchars(
                                            ($absensi['jam_mulai'] ?? '-') . ' - ' . ($absensi['jam_selesai'] ?? '-'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="text-xs text-slate-600 leading-relaxed max-w-xs">
                                            <?= nl2br(htmlspecialchars(
                                                $absensi['deskripsi_tugas'] ?? '-',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )) ?>
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <?= $statusBadge($absensi['status_verifikasi'] ?? 'pending') ?>
                                    </td>

                                    <td class="px-5 py-4">
                                        <?php if (!empty($absensi['pesan_dosen'])): ?>
                                            <p class="text-xs text-slate-600 leading-relaxed max-w-[200px]">
                                                <?= nl2br(htmlspecialchars(
                                                    $absensi['pesan_dosen'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )) ?>
                                            </p>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400">
                                                -
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <p class="text-[11px] text-slate-500">
                                            <?= !empty($absensi['created_at'])
                                                ? date('d M Y H:i', strtotime($absensi['created_at']))
                                                : '-' ?>
                                        </p>

                                        <?php if (!empty($absensi['updated_at'])): ?>
                                            <p class="text-[10px] text-slate-400 mt-1">
                                                Update:
                                                <?= date('d M Y H:i', strtotime($absensi['updated_at'])) ?>
                                            </p>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <!-- Mobile / Tablet Cards -->
                <div class="xl:hidden divide-y divide-slate-100">

                    <?php foreach ($absensiList as $absensi): ?>

                        <article class="p-4 sm:p-5">

                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-slate-800">
                                        <?= htmlspecialchars(
                                            $absensi['nama_matkul'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </h3>

                                    <p class="text-xs text-slate-500 mt-1">
                                        <?= !empty($absensi['tanggal'])
                                            ? date('d M Y', strtotime($absensi['tanggal']))
                                            : '-' ?>
                                        · Pertemuan
                                        <?= htmlspecialchars(
                                            (string)($absensi['pertemuan_ke'] ?? '-'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </p>
                                </div>

                                <?= $statusBadge($absensi['status_verifikasi'] ?? 'pending') ?>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 text-xs">

                                <div class="p-3 rounded-xl bg-slate-50">
                                    <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">
                                        Waktu
                                    </p>

                                    <p class="text-slate-700 mt-1">
                                        <?= htmlspecialchars(
                                            ($absensi['jam_mulai'] ?? '-') . ' - ' . ($absensi['jam_selesai'] ?? '-'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </p>
                                </div>

                                <div class="p-3 rounded-xl bg-slate-50">
                                    <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">
                                        Timestamp
                                    </p>

                                    <p class="text-slate-700 mt-1">
                                        <?= !empty($absensi['created_at'])
                                            ? date('d M Y H:i', strtotime($absensi['created_at']))
                                            : '-' ?>
                                    </p>
                                </div>

                            </div>

                            <div class="mt-4">
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">
                                    Deskripsi Tugas
                                </p>

                                <p class="text-xs text-slate-600 leading-relaxed mt-1.5">
                                    <?= nl2br(htmlspecialchars(
                                        $absensi['deskripsi_tugas'] ?? '-',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )) ?>
                                </p>
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">
                                    Pesan Dosen
                                </p>

                                <p class="text-xs leading-relaxed mt-1.5 <?= !empty($absensi['pesan_dosen']) ? 'text-slate-600' : 'text-slate-400' ?>">
                                    <?= !empty($absensi['pesan_dosen'])
                                        ? nl2br(htmlspecialchars(
                                            $absensi['pesan_dosen'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ))
                                        : 'Belum ada pesan dari dosen.' ?>
                                </p>
                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>
</div>

<?php require_once __DIR__ . '/../Templates/asdos_bottom_nav.php'; ?>

</body>
</html>
