<?php
// Fallback & Dokumentasi Variabel dari DispensasiController
$currentUser = $currentUser ?? \Core\Guard::user();
$settings    = $settings ?? [];

// Helper untuk mengambil value setting berdasarkan key
$getVal = static function (array $settingsList, string $key, string $default = ''): string {
    foreach ($settingsList as $s) {
        if ($s['setting_key'] === $key) {
            return $s['setting_value'] ?? $default;
        }
    }
    return $default;
};

?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Institusi & Kop Surat — Super Admin</title>

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
        <main class="flex-1 max-w-5xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 space-y-6">

            <!-- Page Header Banner -->
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-blue-50 text-[#1867c0] border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Pengaturan Institusi & Kop Surat</h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola identitas institusi, kop surat resmi, dan pejabat penandatangan surat dispensasi</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?= \Core\Guard::url('/superadmin/dispensasi') ?>" 
                        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition active:scale-95">
                        ← Kembali ke Monitoring
                    </a>
                </div>
            </div>

            <!-- Form Edit Konfigurasi Settings -->
            <form id="settingsForm" action="<?= \Core\Guard::url('/superadmin/settings/update') ?>" method="POST" class="space-y-6">
                <?= \Core\Guard::csrfField() ?>

                <!-- Bagian 1: Identitas Institusi & Kop Surat -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">1. Identitas Institusi & Teks Kop Surat</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Teks yang akan dicetak pada kepala surat (kop) dokumen PDF resmi</p>
                    </div>

                    <div class="p-5 sm:p-6 space-y-4">
                        <!-- Nama Institusi -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nama Institusi / Universitas
                            </label>
                            <input type="text" name="settings[institusi_nama]" id="input_institusi_nama"
                                value="<?= htmlspecialchars($getVal($settings, 'institusi_nama', 'UNIVERSITAS PEMBANGUNAN NASIONAL "VETERAN" JAWA TIMUR'), ENT_QUOTES, 'UTF-8') ?>"
                                required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                        </div>

                        <!-- Baris 2: Himpunan & Fakultas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nama Himpunan / Unit Pelaksana
                                </label>
                                <input type="text" name="settings[institusi_himpunan]" id="input_institusi_himpunan"
                                    value="<?= htmlspecialchars($getVal($settings, 'institusi_himpunan', 'HIMPUNAN MAHASISWA SISTEM INFORMASI'), ENT_QUOTES, 'UTF-8') ?>"
                                    required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nama Fakultas
                                </label>
                                <input type="text" name="settings[institusi_fakultas]" id="input_institusi_fakultas"
                                    value="<?= htmlspecialchars($getVal($settings, 'institusi_fakultas', 'FAKULTAS ILMU KOMPUTER'), ENT_QUOTES, 'UTF-8') ?>"
                                    required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                            </div>
                        </div>

                        <!-- Alamat Institusi -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Alamat Lengkap Kampus
                            </label>
                            <input type="text" name="settings[institusi_alamat]" id="input_institusi_alamat"
                                value="<?= htmlspecialchars($getVal($settings, 'institusi_alamat', 'Jalan Raya Rungkut Madya, Gunung Anyar, Surabaya, 60294'), ENT_QUOTES, 'UTF-8') ?>"
                                required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                        </div>

                        <!-- Baris 3: Email, Website, Kota -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Email Resmi
                                </label>
                                <input type="email" name="settings[institusi_email]" id="input_institusi_email"
                                    value="<?= htmlspecialchars($getVal($settings, 'institusi_email', 'himasifo.fasilkom@upnjatim.ac.id'), ENT_QUOTES, 'UTF-8') ?>"
                                    required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Laman / Website Resmi
                                </label>
                                <input type="text" name="settings[institusi_website]" id="input_institusi_website"
                                    value="<?= htmlspecialchars($getVal($settings, 'institusi_website', 'http://himasifo.fasilkom.upnjatim.ac.id/'), ENT_QUOTES, 'UTF-8') ?>"
                                    required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Kota Penerbitan Surat
                                </label>
                                <input type="text" name="settings[institusi_kota]" id="input_institusi_kota"
                                    value="<?= htmlspecialchars($getVal($settings, 'institusi_kota', 'Surabaya'), ENT_QUOTES, 'UTF-8') ?>"
                                    required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                                <p class="text-[10px] text-slate-400 mt-0.5">Contoh: "Surabaya, 8 Oktober 2026"</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Pejabat Penandatangan Surat (Koordinator Prodi) -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">2. Koordinator Program Studi (Penandatangan Surat)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Data pejabat prodi yang dicetak pada kolom tanda tangan sebelah kanan surat</p>
                    </div>

                    <div class="p-5 sm:p-6 space-y-4">
                        <!-- Baris: Nama Kaprodi & NIP -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Nama Lengkap & Gelar Koordinator Prodi
                                </label>
                                <input type="text" name="settings[kaprodi_nama]" id="input_kaprodi_nama"
                                    value="<?= htmlspecialchars($getVal($settings, 'kaprodi_nama', 'Siti Mukaromah, S.Kom, M.Kom'), ENT_QUOTES, 'UTF-8') ?>"
                                    required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    NIP Koordinator Prodi
                                </label>
                                <input type="text" name="settings[kaprodi_nip]" id="input_kaprodi_nip"
                                    value="<?= htmlspecialchars($getVal($settings, 'kaprodi_nip', '19810704 2021212 011'), ENT_QUOTES, 'UTF-8') ?>"
                                    required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                            </div>
                        </div>

                        <!-- Jabatan Resmi -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Jabatan Resmi
                            </label>
                            <input type="text" name="settings[kaprodi_jabatan]" id="input_kaprodi_jabatan"
                                value="<?= htmlspecialchars($getVal($settings, 'kaprodi_jabatan', 'Koordinator Program Studi Sistem Informasi'), ENT_QUOTES, 'UTF-8') ?>"
                                required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#1867c0] text-xs sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center justify-end gap-3">
                    <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#1867c0] hover:bg-[#1456a0] text-white text-xs sm:text-sm font-bold shadow-sm transition active:scale-95 cursor-pointer">
                        Simpan Perubahan Pengaturan
                    </button>
                </div>

            </form>

        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const settingsForm = document.getElementById('settingsForm');
            if (!settingsForm) return;

            settingsForm.addEventListener('submit', function(e) {
                const nama = document.getElementById('input_institusi_nama');
                const himpunan = document.getElementById('input_institusi_himpunan');
                const fakultas = document.getElementById('input_institusi_fakultas');
                const alamat = document.getElementById('input_institusi_alamat');
                const email = document.getElementById('input_institusi_email');
                const website = document.getElementById('input_institusi_website');
                const kota = document.getElementById('input_institusi_kota');
                const kaprodiNama = document.getElementById('input_kaprodi_nama');
                const kaprodiNip = document.getElementById('input_kaprodi_nip');
                const kaprodiJabatan = document.getElementById('input_kaprodi_jabatan');

                if (!nama || nama.value.trim().length < 3) {
                    e.preventDefault();
                    window.highlightAndNotify(nama, 'Nama Institusi wajib diisi minimal 3 karakter.');
                    return false;
                }
                if (!himpunan || himpunan.value.trim().length < 2) {
                    e.preventDefault();
                    window.highlightAndNotify(himpunan, 'Nama Himpunan / Unit wajib diisi minimal 2 karakter.');
                    return false;
                }
                if (!fakultas || fakultas.value.trim().length < 2) {
                    e.preventDefault();
                    window.highlightAndNotify(fakultas, 'Nama Fakultas wajib diisi minimal 2 karakter.');
                    return false;
                }
                if (!alamat || alamat.value.trim().length < 5) {
                    e.preventDefault();
                    window.highlightAndNotify(alamat, 'Alamat Lengkap Kampus wajib diisi minimal 5 karakter.');
                    return false;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email || !email.value.trim() || !emailRegex.test(email.value.trim())) {
                    e.preventDefault();
                    window.highlightAndNotify(email, 'Format Email Resmi tidak valid.');
                    return false;
                }

                if (!website || website.value.trim().length < 3) {
                    e.preventDefault();
                    window.highlightAndNotify(website, 'Laman / Website resmi wajib diisi.');
                    return false;
                }
                if (!kota || kota.value.trim().length < 2) {
                    e.preventDefault();
                    window.highlightAndNotify(kota, 'Kota penerbitan surat wajib diisi.');
                    return false;
                }
                if (!kaprodiNama || kaprodiNama.value.trim().length < 3) {
                    e.preventDefault();
                    window.highlightAndNotify(kaprodiNama, 'Nama Koordinator Prodi wajib diisi minimal 3 karakter.');
                    return false;
                }
                if (!kaprodiNip || kaprodiNip.value.trim().length < 5) {
                    e.preventDefault();
                    window.highlightAndNotify(kaprodiNip, 'NIP Koordinator Prodi wajib diisi minimal 5 karakter.');
                    return false;
                }
                if (!kaprodiJabatan || kaprodiJabatan.value.trim().length < 3) {
                    e.preventDefault();
                    window.highlightAndNotify(kaprodiJabatan, 'Jabatan Koordinator Prodi wajib diisi minimal 3 karakter.');
                    return false;
                }
            });
        });
    </script>
</body>

</html>
