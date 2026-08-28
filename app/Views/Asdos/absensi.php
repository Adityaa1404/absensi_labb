<?php
// Fallback variabel dari AsdosController
$currentUser     = $currentUser ?? \Core\Guard::user();
$activePlottings = $activePlottings ?? [];
$absensiList     = $absensiList ?? [];

$currentMatkul = $_GET['matkul'] ?? '';
$currentStatus = $_GET['status'] ?? '';

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
    <title>Isi Absensi — Absensi Lab</title>
<script src="https://cdn.tailwindcss.com"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif;
    }

    .modal-hidden {
        display: none;
    }
</style>

</head>

<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased">
<?php require_once __DIR__ . '/../Templates/notifications.php'; ?>
<?php require_once __DIR__ . '/../Templates/asdos_header.php'; ?>

<div class="md:pl-64 flex flex-col flex-1 min-h-screen">

    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

        <!-- Page Header -->
        <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-[#1867c0]">
                Pelaksanaan Praktikum & Pengajaran
            </p>

            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">
                Isi Absensi
            </h1>

            <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                Catat pelaksanaan kegiatan dan unggah bukti kegiatan untuk diverifikasi oleh dosen pembimbing.
            </p>
        </div>

        <?php if (empty($activePlottings)): ?>

            <!-- Tidak ada plotting aktif -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-sm font-bold text-amber-900">
                            Belum Ada Mata Kuliah Aktif
                        </h2>

                        <p class="text-xs sm:text-sm text-amber-800 mt-1">
                            Anda belum memiliki plotting mata kuliah yang aktif. Absensi baru hanya dapat dibuat untuk mata kuliah tempat Anda sedang diplot.
                        </p>
                    </div>
                </div>
            </div>

        <?php else: ?>

            <!-- Form Absensi -->
            <section class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">

                <div class="p-5 sm:p-6 border-b border-slate-200 bg-slate-50/60">
                    <h2 class="text-base font-bold text-slate-900">
                        Tambah Absensi Baru
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Timestamp pengiriman dicatat otomatis oleh sistem.
                    </p>
                </div>

                <form
                    action="<?= \Core\Guard::url('/asdos/absensi/create') ?>"
                    method="POST"
                    enctype="multipart/form-data"
                    class="p-5 sm:p-6"
                >
                    <?= \Core\Guard::csrfField() ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Mata Kuliah -->
                        <div class="md:col-span-2">
                            <label for="plotting_id" class="block text-xs font-bold text-slate-700 mb-2">
                                Mata Kuliah <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="plotting_id"
                                name="plotting_id"
                                required
                                class="w-full px-3.5 py-3 text-sm border border-slate-300 rounded-xl bg-white text-slate-700 outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                                <option value="">Pilih mata kuliah</option>

                                <?php foreach ($activePlottings as $plotting): ?>
                                    <option value="<?= (int)$plotting['id_plotting'] ?>">
                                        <?= htmlspecialchars(
                                            $plotting['nama_matkul'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                        — <?= htmlspecialchars(
                                            $plotting['nama_dosen'] ?? 'Dosen belum ditentukan',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tanggal -->
                        <div>
                            <label for="tanggal" class="block text-xs font-bold text-slate-700 mb-2">
                                Tanggal Pelaksanaan <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                id="tanggal"
                                name="tanggal"
                                required
                                value="<?= date('Y-m-d') ?>"
                                class="w-full px-3.5 py-3 text-sm border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                        </div>

                        <!-- Pertemuan -->
                        <div>
                            <label for="pertemuan_ke" class="block text-xs font-bold text-slate-700 mb-2">
                                Pertemuan Ke-
                            </label>

                            <input
                                type="number"
                                id="pertemuan_ke"
                                name="pertemuan_ke"
                                min="1"
                                placeholder="Contoh: 1"
                                class="w-full px-3.5 py-3 text-sm border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                        </div>

                        <!-- Jam Mulai -->
                        <div>
                            <label for="jam_mulai" class="block text-xs font-bold text-slate-700 mb-2">
                                Jam Mulai
                            </label>

                            <input
                                type="time"
                                id="jam_mulai"
                                name="jam_mulai"
                                class="w-full px-3.5 py-3 text-sm border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                        </div>

                        <!-- Jam Selesai -->
                        <div>
                            <label for="jam_selesai" class="block text-xs font-bold text-slate-700 mb-2">
                                Jam Selesai
                            </label>

                            <input
                                type="time"
                                id="jam_selesai"
                                name="jam_selesai"
                                class="w-full px-3.5 py-3 text-sm border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                        </div>

                        <!-- Deskripsi -->
                        <div class="md:col-span-2">
                            <label for="deskripsi_tugas" class="block text-xs font-bold text-slate-700 mb-2">
                                Deskripsi Tugas / Kegiatan <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                id="deskripsi_tugas"
                                name="deskripsi_tugas"
                                rows="4"
                                minlength="5"
                                required
                                placeholder="Jelaskan kegiatan yang Anda lakukan..."
                                class="w-full px-3.5 py-3 text-sm border border-slate-300 rounded-xl outline-none resize-y focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            ></textarea>

                            <p class="text-[11px] text-slate-400 mt-1.5">
                                Minimal 5 karakter.
                            </p>
                        </div>

                        <!-- Foto Kegiatan -->
                        <div>
                            <label for="foto_kegiatan" class="block text-xs font-bold text-slate-700 mb-2">
                                Foto Kegiatan <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="file"
                                id="foto_kegiatan"
                                name="foto_kegiatan"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                required
                                class="w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-[#1867c0] file:font-semibold hover:file:bg-blue-100"
                            >

                            <p class="text-[11px] text-slate-400 mt-1.5">
                                JPG, PNG, atau WEBP. Maksimal 2 MB.
                            </p>
                        </div>

                        <!-- Foto Selfie -->
                        <div>
                            <label for="foto_selfie" class="block text-xs font-bold text-slate-700 mb-2">
                                Foto Selfie <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="file"
                                id="foto_selfie"
                                name="foto_selfie"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                required
                                class="w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-[#1867c0] file:font-semibold hover:file:bg-blue-100"
                            >

                            <p class="text-[11px] text-slate-400 mt-1.5">
                                JPG, PNG, atau WEBP. Maksimal 2 MB.
                            </p>
                        </div>

                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-6 pt-5 border-t border-slate-100">
                        <p class="text-[11px] text-slate-400">
                            <span class="text-red-500">*</span> Wajib diisi.
                        </p>

                        <button
                            type="submit"
                            class="inline-flex justify-center items-center gap-2 px-5 py-3 bg-[#1867c0] hover:bg-[#14529d] text-white text-sm font-semibold rounded-xl transition active:scale-[0.98]"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>

                            Kirim Absensi
                        </button>
                    </div>

                </form>
            </section>

        <?php endif; ?>

        <!-- Riwayat di Halaman Absensi -->
        <section class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">

            <div class="p-5 sm:p-6 border-b border-slate-200 bg-slate-50/60">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Data Absensi Saya
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            Anda hanya dapat mengubah atau menghapus absensi dengan status pending.
                        </p>
                    </div>

                    <a
                        href="<?= \Core\Guard::url('/asdos/history') ?>"
                        class="text-xs font-semibold text-[#1867c0] hover:underline"
                    >
                        Lihat Riwayat Lengkap →
                    </a>
                </div>
            </div>

            <!-- Filter -->
            <form
                method="GET"
                action="<?= \Core\Guard::url('/asdos/absensi') ?>"
                class="p-4 sm:p-5 border-b border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3"
            >
                <select
                    name="matkul"
                    class="px-3 py-2.5 text-xs border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                >
                    <option value="">Semua Mata Kuliah</option>

                    <?php foreach ($activePlottings as $plotting): ?>
                        <option
                            value="<?= (int)$plotting['matkul_id'] ?>"
                            <?= (string)$currentMatkul === (string)$plotting['matkul_id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($plotting['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select
                    name="status"
                    class="px-3 py-2.5 text-xs border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                >
                    <option value="">Semua Status</option>
                    <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="disetujui" <?= $currentStatus === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="ditolak" <?= $currentStatus === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition"
                    >
                        Filter
                    </button>

                    <a
                        href="<?= \Core\Guard::url('/asdos/absensi') ?>"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition"
                    >
                        Reset
                    </a>
                </div>
            </form>

            <?php if (empty($absensiList)): ?>

                <div class="p-10 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z" />
                        </svg>
                    </div>

                    <p class="text-sm font-bold text-slate-700">
                        Belum Ada Data Absensi
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Data absensi yang Anda kirim akan muncul di sini.
                    </p>
                </div>

            <?php else: ?>

                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-5 py-4 font-bold">Tanggal</th>
                                <th class="px-5 py-4 font-bold">Mata Kuliah</th>
                                <th class="px-5 py-4 font-bold">Pertemuan</th>
                                <th class="px-5 py-4 font-bold">Waktu</th>
                                <th class="px-5 py-4 font-bold">Status</th>
                                <th class="px-5 py-4 font-bold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($absensiList as $absensi): ?>
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-4">
                                        <p class="text-xs font-semibold text-slate-700">
                                            <?= !empty($absensi['tanggal'])
                                                ? date('d M Y', strtotime($absensi['tanggal']))
                                                : '-' ?>
                                        </p>
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="text-xs font-semibold text-slate-800">
                                            <?= htmlspecialchars($absensi['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </td>

                                    <td class="px-5 py-4 text-xs text-slate-600">
                                        <?= htmlspecialchars((string)($absensi['pertemuan_ke'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td class="px-5 py-4 text-xs text-slate-600">
                                        <?= htmlspecialchars(
                                            ($absensi['jam_mulai'] ?? '-') . ' - ' . ($absensi['jam_selesai'] ?? '-'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="px-5 py-4">
                                        <?= $statusBadge($absensi['status_verifikasi'] ?? 'pending') ?>
                                    </td>

                                    <td class="px-5 py-4">
                                        <?php if (($absensi['status_verifikasi'] ?? '') === 'pending'): ?>

                                            <div class="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    onclick="openEditModal(<?= (int)$absensi['id_absensi'] ?>)"
                                                    class="px-3 py-2 text-[11px] font-bold rounded-lg bg-blue-50 text-[#1867c0] hover:bg-blue-100 transition"
                                                >
                                                    Edit
                                                </button>

                                                <form
                                                    action="<?= \Core\Guard::url('/asdos/absensi/' . (int)$absensi['id_absensi'] . '/delete') ?>"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus absensi ini?');"
                                                >
                                                    <?= \Core\Guard::csrfField() ?>

                                                    <button
                                                        type="submit"
                                                        class="px-3 py-2 text-[11px] font-bold rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                                                    >
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>

                                        <?php else: ?>

                                            <span class="text-[11px] text-slate-400">
                                                Data terkunci
                                            </span>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden divide-y divide-slate-100">
                    <?php foreach ($absensiList as $absensi): ?>
                        <div class="p-4 space-y-3">

                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800">
                                        <?= htmlspecialchars($absensi['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>

                                    <p class="text-xs text-slate-500 mt-1">
                                        <?= !empty($absensi['tanggal'])
                                            ? date('d M Y', strtotime($absensi['tanggal']))
                                            : '-' ?>
                                        · Pertemuan <?= htmlspecialchars((string)($absensi['pertemuan_ke'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>

                                <?= $statusBadge($absensi['status_verifikasi'] ?? 'pending') ?>
                            </div>

                            <p class="text-xs text-slate-600">
                                <?= htmlspecialchars(
                                    ($absensi['jam_mulai'] ?? '-') . ' - ' . ($absensi['jam_selesai'] ?? '-'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </p>

                            <?php if (($absensi['status_verifikasi'] ?? '') === 'pending'): ?>
                                <div class="flex gap-2 pt-1">
                                    <button
                                        type="button"
                                        onclick="openEditModal(<?= (int)$absensi['id_absensi'] ?>)"
                                        class="flex-1 px-3 py-2.5 text-xs font-bold rounded-xl bg-blue-50 text-[#1867c0]"
                                    >
                                        Edit
                                    </button>

                                    <form
                                        action="<?= \Core\Guard::url('/asdos/absensi/' . (int)$absensi['id_absensi'] . '/delete') ?>"
                                        method="POST"
                                        class="flex-1"
                                        onsubmit="return confirm('Yakin ingin menghapus absensi ini?');"
                                    >
                                        <?= \Core\Guard::csrfField() ?>

                                        <button
                                            type="submit"
                                            class="w-full px-3 py-2.5 text-xs font-bold rounded-xl bg-red-50 text-red-600"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </section>

    </main>
</div>

<!-- Edit Modals -->
<?php foreach ($absensiList as $absensi): ?>
    <?php if (($absensi['status_verifikasi'] ?? '') !== 'pending') continue; ?>

    <div
        id="edit-modal-<?= (int)$absensi['id_absensi'] ?>"
        class="modal-hidden fixed inset-0 z-[60] overflow-y-auto"
    >
        <div
            class="fixed inset-0 bg-slate-900/50"
            onclick="closeEditModal(<?= (int)$absensi['id_absensi'] ?>)"
        ></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">

                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Edit Absensi
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            <?= htmlspecialchars($absensi['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick="closeEditModal(<?= (int)$absensi['id_absensi'] ?>)"
                        class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 flex items-center justify-center"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form
                    action="<?= \Core\Guard::url('/asdos/absensi/' . (int)$absensi['id_absensi'] . '/update') ?>"
                    method="POST"
                    enctype="multipart/form-data"
                    class="p-5 sm:p-6"
                >
                    <?= \Core\Guard::csrfField() ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Tanggal <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal"
                                required
                                value="<?= htmlspecialchars($absensi['tanggal'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Pertemuan Ke-
                            </label>

                            <input
                                type="number"
                                name="pertemuan_ke"
                                min="1"
                                value="<?= htmlspecialchars((string)($absensi['pertemuan_ke'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Jam Mulai
                            </label>

                            <input
                                type="time"
                                name="jam_mulai"
                                value="<?= htmlspecialchars(substr((string)($absensi['jam_mulai'] ?? ''), 0, 5), ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Jam Selesai
                            </label>

                            <input
                                type="time"
                                name="jam_selesai"
                                value="<?= htmlspecialchars(substr((string)($absensi['jam_selesai'] ?? ''), 0, 5), ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Deskripsi Tugas <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                name="deskripsi_tugas"
                                rows="4"
                                required
                                minlength="5"
                                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-xl outline-none resize-y focus:ring-2 focus:ring-blue-100 focus:border-[#1867c0]"
                            ><?= htmlspecialchars($absensi['deskripsi_tugas'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Ganti Foto Kegiatan
                            </label>

                            <input
                                type="file"
                                name="foto_kegiatan"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-blue-50 file:text-[#1867c0] file:font-semibold"
                            >

                            <p class="text-[10px] text-slate-400 mt-1">
                                Kosongkan jika tidak ingin mengganti.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Ganti Foto Selfie
                            </label>

                            <input
                                type="file"
                                name="foto_selfie"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-blue-50 file:text-[#1867c0] file:font-semibold"
                            >

                            <p class="text-[10px] text-slate-400 mt-1">
                                Kosongkan jika tidak ingin mengganti.
                            </p>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-slate-100">
                        <button
                            type="button"
                            onclick="closeEditModal(<?= (int)$absensi['id_absensi'] ?>)"
                            class="px-4 py-2.5 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="px-4 py-2.5 text-xs font-semibold rounded-xl bg-[#1867c0] hover:bg-[#14529d] text-white transition"
                        >
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../Templates/asdos_bottom_nav.php'; ?>

<script>
    function openEditModal(id) {
        const modal = document.getElementById('edit-modal-' + id);

        if (modal) {
            modal.classList.remove('modal-hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeEditModal(id) {
        const modal = document.getElementById('edit-modal-' + id);

        if (modal) {
            modal.classList.add('modal-hidden');
            document.body.style.overflow = '';
        }
    }
</script>

</body>
</html>
