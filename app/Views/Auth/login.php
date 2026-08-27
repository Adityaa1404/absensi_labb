<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem — Absensi Lab</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50 antialiased text-slate-800">

    <div class="max-w-md w-full space-y-6">

        <!-- Brand Header / Logo -->
        <div class="text-center">
            <div class="w-12 h-12 rounded-xl bg-[#1867c0] flex items-center justify-center text-white font-bold text-lg shadow-xs mx-auto">
                LAB
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-4">
                Sistem Absensi Lab
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Laboratorium Sistem Informasi &bull; Masuk ke Akun Anda
            </p>
        </div>

        <!-- Top Popup Notifications (Auto-dismiss 4 detik) -->
        <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

        <!-- Main Login Card (Design Token: rounded-2xl, border-slate-200, shadow-xs) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs">
            <form action="<?= \Core\Guard::url('/login') ?>" method="POST" class="space-y-4">
                
                <!-- CSRF Protection Field -->
                <?= \Core\Guard::csrfField() ?>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required autofocus
                        class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150"
                        placeholder="contoh@labsi.ac.id">
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">
                            Kata Sandi <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150"
                        placeholder="••••••••">
                </div>

                <!-- Submit Button (Design Token: bg-[#1867c0] hover:bg-[#14529d]) -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-[#1867c0] hover:bg-[#14529d] active:bg-[#0f4482] text-white font-semibold py-2.5 px-5 text-sm rounded-lg transition duration-150 shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Masuk ke Akun</span>
                    </button>
                </div>
            </form>

            <!-- Bottom Info Note -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400">
                    Belum memiliki akun? Hubungi <span class="font-medium text-slate-600">Super Admin Lab</span> untuk pembuatan akun.
                </p>
            </div>
        </div>

        <!-- Footer Copyright -->
        <p class="text-center text-[11px] text-slate-400">
            &copy; <?= date('Y') ?> Laboratorium Sistem Informasi. All rights reserved.
        </p>

    </div>

</body>
</html>
