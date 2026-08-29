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

            <!-- <p class="text-xs sm:text-sm text-slate-500 mt-1.5">
                Catat pelaksanaan kegiatan dan unggah bukti kegiatan untuk diverifikasi oleh dosen pembimbing.
            </p> -->
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
