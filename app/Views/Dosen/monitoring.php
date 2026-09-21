<?php
// Fallback & Dokumentasi Variabel dari DosenController
$currentUser = $currentUser ?? \Core\Guard::user();
$metrics     = $metrics ?? [
    'total'     => 0,
    'disetujui' => 0,
    'pending'   => 0,
    'ditolak'   => 0,
];
$absensiList = $absensiList ?? [];
$matkulList  = $matkulList ?? [];
$filters     = $filters ?? [
    'search'            => '',
    'status_verifikasi' => '',
    'matkul_id'         => '',
    'date_start'        => '',
    'date_end'          => '',
];
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Absensi Praktikum — Dosen Pengampu</title>

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

    <!-- Top Popup Notifications (Auto-dismiss 4 detik) -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Navbar -->
    <?php require_once __DIR__ . '/../Templates/dosen_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

            <!-- Page Header Banner -->
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                            Dosen Pengampu
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Monitoring Absensi Praktikum</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Daftar dan verifikasi presensi asisten dosen khusus pada mata kuliah yang Anda ampu.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="resetAllFilters()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer border border-slate-300 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset Filter</span>
                    </button>
                </div>
            </div>

            
            <!-- Content Card: Toolbar Filter & Data Table -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">

                <!-- Card Header with Multi-Criteria Filters -->
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/60 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Rekapitulasi Kehadiran Praktikum Mahasiswa Asdos</h2>
                        </div>
                    </div>

                    <!-- Interactive Filters & Search Bar -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1">

                        <!-- Search Input -->
                        <div class="sm:col-span-6 relative">
                            <label for="searchInput" class="block text-xs font-bold text-slate-700 mb-1">Cari Absensi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput"
                                    placeholder="Ketik nama asdos, NPM, atau topik praktikum..."
                                    class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-9 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 shadow-2xs">
                                <button type="button" id="clearSearchBtn" onclick="clearSearch()" title="Hapus teks pencarian" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Status Verifikasi Filter -->
                        <div class="sm:col-span-3">
                            <label for="statusFilter" class="block text-xs font-bold text-slate-700 mb-1">Status Verifikasi</label>
                            <select id="statusFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                                <option value="">Semua Status</option>
                                <option value="disetujui" <?= ($filters['status_verifikasi'] === 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                                <option value="pending" <?= ($filters['status_verifikasi'] === 'pending') ? 'selected' : '' ?>>Menunggu Verifikasi (Pending)</option>
                                <option value="ditolak" <?= ($filters['status_verifikasi'] === 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        </div>

                        <!-- Matkul Filter (Khusus Matkul yang diampu dosen) -->
                        <div class="sm:col-span-3">
                            <label for="matkulFilter" class="block text-xs font-bold text-slate-700 mb-1">Mata Kuliah</label>
                            <select id="matkulFilter" onchange="applyFilters()" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 font-medium focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 cursor-pointer shadow-2xs">
                                <option value="">Semua Mata Kuliah Diampu</option>
                                <?php foreach ($matkulList as $m): ?>
                                    <option value="<?= $m['id_matkul'] ?>" <?= ($filters['matkul_id'] == $m['id_matkul']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['nama_matkul'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Table Responsive Container -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border-collapse" id="monitoringTable">
                        <thead class="bg-slate-100/80 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Tanggal & Pertemuan</th>
                                <th class="px-3.5 py-3.5">Asisten Dosen</th>
                                <th class="px-3.5 py-3.5">Mata Kuliah & Jam</th>
                                <th class="px-3 py-3.5 text-center">Bukti Foto</th>
                                <th class="px-3 py-3.5 text-center">Status Verifikasi</th>
                                <th class="px-3.5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200" id="monitoringTableBody">
                            <?php if (!empty($absensiList)): ?>
                                <?php foreach ($absensiList as $a): ?>
                                    <?php
                                    $status = $a['status_verifikasi'];
                                    $statusBadge = match ($status) {
                                        'disetujui' => ['label' => 'DISETUJUI', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300', 'dot' => 'bg-emerald-500'],
                                        'ditolak'   => ['label' => 'DITOLAK', 'bg' => 'bg-red-50', 'text' => 'text-red-800', 'border' => 'border-red-300', 'dot' => 'bg-red-500'],
                                        default     => ['label' => 'PENDING', 'bg' => 'bg-amber-50', 'text' => 'text-amber-900', 'border' => 'border-amber-300', 'dot' => 'bg-amber-500']
                                    };

                                    $words = explode(' ', trim($a['nama_asdos'] ?? 'Asdos'));
                                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                    ?>
                                    <tr class="hover:bg-blue-50/40 transition-colors duration-150 monitoring-row"
                                        data-id="<?= $a['id_absensi'] ?>"
                                        data-asdos-nama="<?= htmlspecialchars($a['nama_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-asdos-npm="<?= htmlspecialchars($a['npm_asdos'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-matkul-id="<?= $a['id_matkul'] ?? '' ?>"
                                        data-matkul-nama="<?= htmlspecialchars($a['nama_matkul'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-status="<?= $status ?>"
                                        data-tanggal="<?= $a['tanggal'] ?? '' ?>"
                                        data-deskripsi="<?= htmlspecialchars($a['deskripsi_tugas'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-tugas="<?= htmlspecialchars($a['tugas'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                                        <!-- 1. Tanggal & Pertemuan -->
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="flex items-center gap-1.5 font-bold text-slate-900 text-xs sm:text-sm">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span><?= date('d M Y', strtotime($a['tanggal'])) ?></span>
                                                </div>
                                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                                    <span class="font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">
                                                        Pertemuan ke-<?= $a['pertemuan_ke'] ?? '1' ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 2. Asisten Dosen -->
                                        <td class="px-3.5 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1867c0] font-bold text-xs flex items-center justify-center shrink-0 border border-blue-200 shadow-2xs">
                                                    <?= $initials ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-slate-900 text-xs sm:text-sm leading-tight"><?= htmlspecialchars($a['nama_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">NPM: <?= htmlspecialchars($a['npm_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 3. Mata Kuliah & Jam -->
                                        <td class="px-3.5 py-3">
                                            <div>
                                                <p class="font-bold text-slate-900 text-xs leading-tight"><?= htmlspecialchars($a['nama_matkul'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                <?php if (!empty($a['jam_mulai']) && !empty($a['jam_selesai'])): ?>
                                                    <p class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span><?= substr($a['jam_mulai'], 0, 5) ?> - <?= substr($a['jam_selesai'], 0, 5) ?> WIB</span>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- 4. Bukti Foto (Kegiatan & Selfie) -->
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center justify-center gap-1.5">
                                                <?php if (!empty($a['foto_kegiatan'])): ?>
                                                    <?php
                                                    $kegiatanSrc = (str_starts_with($a['foto_kegiatan'], 'http') || str_starts_with($a['foto_kegiatan'], '/'))
                                                        ? $a['foto_kegiatan']
                                                        : \Core\Guard::url('/uploads/absensi/' . $a['foto_kegiatan']);
                                                    ?>
                                                    <button type="button"
                                                        onclick="previewImage('<?= htmlspecialchars($kegiatanSrc, ENT_QUOTES, 'UTF-8') ?>', 'Bukti Praktikum — <?= htmlspecialchars(addslashes($a['nama_asdos']), ENT_QUOTES, 'UTF-8') ?>')"
                                                        title="Lihat Foto Kegiatan Praktikum"
                                                        class="px-2 py-1 rounded-md bg-slate-100 hover:bg-blue-50 hover:text-[#1867c0] border border-slate-300 text-[11px] font-semibold transition flex items-center gap-1 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span>Kegiatan</span>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-slate-400 text-xs italic">Tanpa Foto</span>
                                                <?php endif; ?>

                                                <?php if (!empty($a['foto_selfie'])): ?>
                                                    <?php
                                                    $selfieSrc = (str_starts_with($a['foto_selfie'], 'http') || str_starts_with($a['foto_selfie'], '/'))
                                                        ? $a['foto_selfie']
                                                        : \Core\Guard::url('/uploads/absensi/' . $a['foto_selfie']);
                                                    ?>
                                                    <button type="button"
                                                        onclick="previewImage('<?= htmlspecialchars($selfieSrc, ENT_QUOTES, 'UTF-8') ?>', 'Foto Selfie — <?= htmlspecialchars(addslashes($a['nama_asdos']), ENT_QUOTES, 'UTF-8') ?>')"
                                                        title="Lihat Foto Selfie Kehadiran"
                                                        class="px-2 py-1 rounded-md bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 border border-slate-300 text-[11px] font-semibold transition flex items-center gap-1 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        <span>Selfie</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- 5. Status Verifikasi -->
                                        <td class="px-3 py-3 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border <?= $statusBadge['bg'] ?> <?= $statusBadge['text'] ?> <?= $statusBadge['border'] ?> inline-flex items-center gap-1.5 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $statusBadge['dot'] ?>"></span>
                                                <?= $statusBadge['label'] ?>
                                            </span>
                                        </td>

                                        <!-- 6. Aksi Dosen (Verifikasi & Detail) -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center justify-center gap-1.5">
                                                <!-- Tombol Verifikasi / Ubah Status -->
                                                <button type="button"
                                                    onclick="openVerificationModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)"
                                                    title="Verifikasi Absensi Praktikum"
                                                    class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-800 text-xs font-bold border border-amber-300 hover:border-amber-600 transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span>Verifikasi</span>
                                                </button>

                                                <!-- Tombol Detail -->
                                                <button type="button"
                                                    onclick="openDetailModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)"
                                                    title="Lihat Detail Lengkap Absensi"
                                                    class="px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-[#1867c0] hover:text-white text-[#1867c0] text-xs font-bold border border-blue-200 hover:border-[#1867c0] transition-all duration-150 flex items-center gap-1 cursor-pointer shadow-2xs active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span>Detail</span>
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="<?= empty($absensiList) ? 'block' : 'hidden' ?> p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#1867c0] flex items-center justify-center mx-auto mb-4 border border-blue-200 shadow-2xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Laporan Absensi yang Sesuai</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                        Tidak ditemukan data absensi praktikum untuk kriteria filter yang Anda pilih. Silakan bersihkan filter untuk menampilkan seluruh riwayat.
                    </p>
                    <button type="button" onclick="resetAllFilters()" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition border border-slate-300 shadow-2xs cursor-pointer">
                        Bersihkan Filter
                    </button>
                </div>

            </div>

        </main>
    </div>

    <!-- Floating Bottom Navigation untuk Layar Smartphone -->
    <?php require_once __DIR__ . '/../Templates/dosen_bottom_nav.php'; ?>

    <!-- ========================================================================= -->
    <!-- MODAL: VERIFIKASI STATUS ABSENSI (DOSEN)                                  -->
    <!-- ========================================================================= -->
    <div id="verificationModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1867c0] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900" id="verify_modal_title">Verifikasi Kehadiran Praktikum</h3>
                </div>
                <button type="button" onclick="closeVerificationModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Info Singkat Praktikum -->
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Mata Kuliah:</span>
                    <span class="font-bold text-slate-800" id="verify_matkul_nama">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Asisten Dosen:</span>
                    <span class="font-bold text-slate-800" id="verify_asdos_nama">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pertemuan & Tanggal:</span>
                    <span class="font-bold text-slate-800" id="verify_pertemuan_tanggal">-</span>
                </div>
                <div class="pt-1.5 border-t border-slate-200">
                    <span class="text-slate-500 block mb-0.5">Deskripsi Kegiatan Asdos:</span>
                    <p class="font-medium text-slate-700 italic bg-white p-2 rounded border border-slate-200" id="verify_deskripsi">-</p>
                </div>
            </div>

            <form id="formVerification" method="POST" action="">
                <?= \Core\Guard::csrfField() ?>
                <input type="hidden" name="redirect_to" value="<?= \Core\Guard::url('/dosen/monitoring') ?>">

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Status Verifikasi <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex flex-col items-center p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-emerald-50/50 has-checked:border-emerald-500 has-checked:bg-emerald-50 has-checked:text-emerald-900 transition text-center">
                                <input type="radio" id="status_radio_disetujui" name="status_verifikasi" value="disetujui" class="accent-emerald-600 mb-1" required>
                                <span class="text-xs font-bold text-emerald-800">Disetujui</span>
                            </label>
                            <label class="flex flex-col items-center p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-amber-50/50 has-checked:border-amber-500 has-checked:bg-amber-50 has-checked:text-amber-900 transition text-center">
                                <input type="radio" id="status_radio_pending" name="status_verifikasi" value="pending" class="accent-amber-600 mb-1" required>
                                <span class="text-xs font-bold text-amber-800">Pending</span>
                            </label>
                            <label class="flex flex-col items-center p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-red-50/50 has-checked:border-red-500 has-checked:bg-red-50 has-checked:text-red-900 transition text-center">
                                <input type="radio" id="status_radio_ditolak" name="status_verifikasi" value="ditolak" class="accent-red-600 mb-1" required>
                                <span class="text-xs font-bold text-red-800">Ditolak</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="modalPesanDosen" class="block text-xs font-bold text-slate-700 mb-1">Catatan / Masukan untuk Asdos (Opsional)</label>
                        <textarea id="modalPesanDosen" name="pesan_dosen" rows="3"
                            placeholder="Tuliskan masukan atau alasan penolakan/persetujuan untuk asdos..."
                            class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 mt-4">
                    <button type="button" onclick="closeVerificationModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-[#1867c0] hover:bg-[#14529d] rounded-xl shadow-xs transition cursor-pointer">
                        Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: DETAIL LENGKAP ABSENSI                                             -->
    <!-- ========================================================================= -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1867c0] border border-blue-200 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900" id="detail_title">Detail Absensi Praktikum</h3>
                        <p class="text-xs text-slate-500" id="detail_subtitle">Informasi lengkap presensi asisten dosen</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body Scrollable -->
            <div class="p-5 overflow-y-auto space-y-5 text-xs">

                <!-- 1. Ringkasan Info Praktikum & Asdos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Asisten Dosen</p>
                        <p class="font-bold text-sm text-slate-900 mt-0.5" id="detail_asdos_nama">-</p>
                        <p class="text-slate-500 font-mono" id="detail_asdos_npm">NPM: -</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Mata Kuliah</p>
                        <p class="font-bold text-sm text-slate-900 mt-0.5" id="detail_matkul_nama">-</p>
                        <p class="text-slate-500" id="detail_pertemuan_tanggal">-</p>
                    </div>
                </div>

                <!-- 2. Status Verifikasi & Catatan Dosen -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">Status Verifikasi Saat Ini:</span>
                        <span id="detail_status_badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider">
                            -
                        </span>
                    </div>

                    <!-- Pesan Dosen Display -->
                    <div id="detail_pesan_wrapper" class="hidden p-3 rounded-xl bg-amber-50 border border-amber-200">
                        <p class="font-bold text-amber-800 text-[11px] uppercase tracking-wider mb-1">Catatan / Masukan dari Dosen:</p>
                        <p class="text-amber-900 italic leading-relaxed" id="detail_pesan_text">-</p>
                    </div>
                </div>

                <!-- 3. Checklist Tugas Praktikum -->
                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Tugas & Aktivitas yang Diselesaikan:</label>
                    <div id="detail_tugas_text" class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-800 leading-relaxed font-normal">
                        -
                    </div>
                </div>

                <!-- 4. Deskripsi Kegiatan -->
                <div>
                    <label class="block font-bold text-slate-800 mb-1.5">Deskripsi Pelaksanaan Praktikum:</label>
                    <div id="detail_deskripsi_text" class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-800 leading-relaxed whitespace-pre-wrap">
                        -
                    </div>
                </div>

                <!-- 5. Bukti Foto (Kegiatan & Selfie) -->
                <div>
                    <label class="block font-bold text-slate-800 mb-2">Dokumentasi Bukti Kehadiran:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Foto Kegiatan -->
                        <div>
                            <p class="text-[11px] font-semibold text-slate-500 mb-1">Foto Kegiatan Praktikum</p>
                            <div id="detail_kegiatan_img_wrapper" class="w-full h-44 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                                <span class="text-slate-400">Tidak ada foto</span>
                            </div>
                        </div>
                        <!-- Foto Selfie -->
                        <div>
                            <p class="text-[11px] font-semibold text-slate-500 mb-1">Foto Selfie Kehadiran</p>
                            <div id="detail_selfie_img_wrapper" class="w-full h-44 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                                <span class="text-slate-400">Tidak ada foto selfie</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-[11px] text-slate-400" id="detail_timestamp">Tercatat pada: -</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openVerificationFromDetail()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        Verifikasi Laporan Ini
                    </button>
                    <button type="button" onclick="closeDetailModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: IMAGE PREVIEW LIGHTBOX                                             -->
    <!-- ========================================================================= -->
    <div id="imagePreviewModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative max-w-2xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h4 id="previewTitle" class="text-xs font-bold text-slate-800 truncate">Preview Foto</h4>
                <button type="button" onclick="closePreviewImage()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 flex items-center justify-center bg-slate-900 min-h-[300px]">
                <img id="previewImg" src="" alt="Preview" class="max-h-[70vh] max-w-full object-contain rounded-lg">
            </div>
        </div>
    </div>

    <!-- Interactive Client Script -->
    <script>
        const BASE_URL = '<?= \Core\Guard::url('') ?>';
        let currentDetailData = null;

        // ---------------------------------------------------------------------
        // 1. Live Filters & Search Logic
        // ---------------------------------------------------------------------
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const statusFilter = document.getElementById('statusFilter');
        const matkulFilter = document.getElementById('matkulFilter');
        const emptyState = document.getElementById('emptyState');
        const rows = document.querySelectorAll('.monitoring-row');

        searchInput.addEventListener('input', function() {
            clearSearchBtn.classList.toggle('hidden', !this.value.trim());
            applyFilters();
        });

        function clearSearch() {
            searchInput.value = '';
            clearSearchBtn.classList.add('hidden');
            applyFilters();
        }

        function filterByMetric(status) {
            statusFilter.value = status;
            applyFilters();
        }

        function applyFilters() {
            const query = (searchInput.value || '').toLowerCase().trim();
            const status = (statusFilter.value || '').toLowerCase().trim();
            const matkulId = (matkulFilter.value || '').trim();

            let visibleCount = 0;

            rows.forEach(row => {
                const asdosNama = (row.getAttribute('data-asdos-nama') || '').toLowerCase();
                const asdosNpm = (row.getAttribute('data-asdos-npm') || '').toLowerCase();
                const matkulNama = (row.getAttribute('data-matkul-nama') || '').toLowerCase();
                const deskripsi = (row.getAttribute('data-deskripsi') || '').toLowerCase();
                const tugas = (row.getAttribute('data-tugas') || '').toLowerCase();
                const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
                const rowMatkulId = (row.getAttribute('data-matkul-id') || '').trim();

                const matchQuery = !query ||
                    asdosNama.includes(query) ||
                    asdosNpm.includes(query) ||
                    matkulNama.includes(query) ||
                    deskripsi.includes(query) ||
                    tugas.includes(query);

                const matchStatus = !status || (rowStatus === status);
                const matchMatkul = !matkulId || (rowMatkulId === matkulId);

                if (matchQuery && matchStatus && matchMatkul) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            emptyState.classList.toggle('hidden', visibleCount > 0);
        }

        function resetAllFilters() {
            searchInput.value = '';
            clearSearchBtn.classList.add('hidden');
            statusFilter.value = '';
            matkulFilter.value = '';
            applyFilters();
        }

        // ---------------------------------------------------------------------
        // 2. Verification Modal Logic
        // ---------------------------------------------------------------------
        function openVerificationModal(data) {
            if (!data) return;

            const form = document.getElementById('formVerification');
            form.action = `${BASE_URL}/dosen/absensi/${data.id_absensi}/status`;

            document.getElementById('verify_modal_title').textContent = `Verifikasi Absensi #${data.id_absensi}`;
            document.getElementById('verify_matkul_nama').textContent = data.nama_matkul || '-';
            document.getElementById('verify_asdos_nama').textContent = (data.nama_asdos || '-') + ' (' + (data.npm_asdos || '-') + ')';
            document.getElementById('verify_pertemuan_tanggal').textContent = `Pertemuan ke-${data.pertemuan_ke || '1'} • ${data.tanggal || ''}`;
            document.getElementById('verify_deskripsi').textContent = data.deskripsi_tugas || '-';

            const status = data.status_verifikasi || 'pending';
            if (status === 'disetujui') {
                document.getElementById('status_radio_disetujui').checked = true;
            } else if (status === 'ditolak') {
                document.getElementById('status_radio_ditolak').checked = true;
            } else {
                document.getElementById('status_radio_pending').checked = true;
            }

            document.getElementById('modalPesanDosen').value = data.pesan_dosen || '';

            // Tutup detail modal jika terbuka
            closeDetailModal();

            document.getElementById('verificationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeVerificationModal() {
            document.getElementById('verificationModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openVerificationFromDetail() {
            if (currentDetailData) {
                openVerificationModal(currentDetailData);
            }
        }

        // ---------------------------------------------------------------------
        // 3. Detail Modal Logic
        // ---------------------------------------------------------------------
        function openDetailModal(data) {
            if (!data) return;
            currentDetailData = data;

            document.getElementById('detail_title').textContent = `Detail Absensi #${data.id_absensi}`;
            document.getElementById('detail_asdos_nama').textContent = data.nama_asdos || '-';
            document.getElementById('detail_asdos_npm').textContent = `NPM: ${data.npm_asdos || '-'}`;
            document.getElementById('detail_matkul_nama').textContent = data.nama_matkul || '-';
            document.getElementById('detail_pertemuan_tanggal').textContent = `Pertemuan ke-${data.pertemuan_ke || '1'} • ${data.tanggal || '-'}`;

            // Status badge
            const badge = document.getElementById('detail_status_badge');
            const status = data.status_verifikasi || 'pending';
            if (status === 'disetujui') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-300';
                badge.textContent = 'Disetujui';
            } else if (status === 'ditolak') {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-red-50 text-red-800 border border-red-300';
                badge.textContent = 'Ditolak';
            } else {
                badge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-amber-50 text-amber-900 border border-amber-300';
                badge.textContent = 'Menunggu Verifikasi (Pending)';
            }

            // Pesan dosen
            const pesanWrapper = document.getElementById('detail_pesan_wrapper');
            const pesanText = document.getElementById('detail_pesan_text');
            if (data.pesan_dosen && data.pesan_dosen.trim() !== '') {
                pesanWrapper.classList.remove('hidden');
                pesanText.textContent = data.pesan_dosen;
            } else {
                pesanWrapper.classList.add('hidden');
            }

            // Tugas checklist
            const tugasRaw = data.tugas || '';
            const tugasEl = document.getElementById('detail_tugas_text');
            if (tugasRaw) {
                try {
                    const parsed = JSON.parse(tugasRaw);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        tugasEl.innerHTML = '';
                        const ul = document.createElement('ul');
                        ul.className = 'list-disc pl-5 space-y-1 text-slate-800';
                        parsed.forEach(item => {
                            const li = document.createElement('li');
                            li.textContent = item;
                            ul.appendChild(li);
                        });
                        tugasEl.appendChild(ul);
                    } else {
                        tugasEl.textContent = tugasRaw;
                    }
                } catch (e) {
                    tugasEl.textContent = tugasRaw;
                }
            } else {
                tugasEl.textContent = '-';
            }

            // Deskripsi
            document.getElementById('detail_deskripsi_text').textContent = data.deskripsi_tugas || '-';

            // Foto Kegiatan
            const kegiatanWrapper = document.getElementById('detail_kegiatan_img_wrapper');
            if (data.foto_kegiatan) {
                const imgPath = data.foto_kegiatan.startsWith('http') ? data.foto_kegiatan : `${BASE_URL}/uploads/absensi/${data.foto_kegiatan}`;
                kegiatanWrapper.innerHTML = `
                    <img src="${imgPath}" alt="Foto Kegiatan" class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition"
                         onclick="previewImage('${imgPath}', 'Bukti Praktikum — ${data.nama_asdos || ''}')">
                `;
            } else {
                kegiatanWrapper.innerHTML = '<span class="text-xs text-slate-400">Tidak ada foto kegiatan</span>';
            }

            // Foto Selfie
            const selfieWrapper = document.getElementById('detail_selfie_img_wrapper');
            if (data.foto_selfie) {
                const imgPath = data.foto_selfie.startsWith('http') ? data.foto_selfie : `${BASE_URL}/uploads/absensi/${data.foto_selfie}`;
                selfieWrapper.innerHTML = `
                    <img src="${imgPath}" alt="Foto Selfie" class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition"
                         onclick="previewImage('${imgPath}', 'Foto Selfie — ${data.nama_asdos || ''}')">
                `;
            } else {
                selfieWrapper.innerHTML = '<span class="text-xs text-slate-400">Tidak ada foto selfie</span>';
            }

            // Timestamp
            document.getElementById('detail_timestamp').textContent = `Tercatat pada: ${data.created_at || '-'}`;

            document.getElementById('detailModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ---------------------------------------------------------------------
        // 4. Image Preview Lightbox
        // ---------------------------------------------------------------------
        function previewImage(src, title) {
            document.getElementById('previewImg').src = src;
            document.getElementById('previewTitle').textContent = title || 'Preview Foto';
            document.getElementById('imagePreviewModal').classList.remove('hidden');
        }

        function closePreviewImage() {
            document.getElementById('imagePreviewModal').classList.add('hidden');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVerificationModal();
                closeDetailModal();
                closePreviewImage();
            }
        });
    </script>
</body>
</html>
