<?php

namespace App\Controllers;

use Core\Guard;
use Core\Validator;
use App\Models\User;

class AuthController
{
    /* Menampilkan halaman Form Login */
    public function login(): void
    {
        Guard::requireGuest();

        require_once __DIR__ . '/../Views/Auth/login.php';
    }

    /* Memproses data form submit Login */
    public function processLogin(): void
    {
        // 1. Verifikasi CSRF token
        Guard::verifyCsrf();

        // 2. Validasi input
        $validator = new \Core\Validator($_POST);
        $validator->rules([
            'email'    => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.'
        ]);

        if ($validator->fails()) {
            $validator->flashErrors();
            Guard::redirect('/login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // 3. Cari user di database berdasarkan email
        $user = User::findByEmail($email);

        // 4. Verifikasi Password dengan password_verify
        if ($user && password_verify($password, $user['password'])) {
            // Login Berhasil -> Simpan ke Session
            $_SESSION['user'] = [
                'id_user'   => $user['id_user'],
                'nama'      => $user['nama'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'is_active' => (int)$user['is_active']
            ];

            Guard::setFlash('success', 'Selamat datang, ' . $user['nama'] . '!');

            // Redirect sesuai role
            if ($user['role'] === 'super_admin') {
                Guard::redirect('/superadmin/dashboard');
            } elseif ($user['role'] === 'asdos') {
                Guard::redirect('/asdos/dashboard');
            } elseif ($user['role'] === 'dosen') {
                Guard::redirect('/dosen/dashboard');
            } else {
                Guard::redirect('/');
            }
        } else {
            // Login Gagal
            Guard::setFlash('error', 'Kombinasi email atau kata sandi tidak cocok.');
            Guard::redirect('/login');
        }
    }

    /* Dashboard Placeholder untuk Asisten Dosen */
    // public function asdosDashboard(): void
    // {
    //     Guard::requireRole('asdos');
    //     $user = Guard::user();
    //     $this->renderPlaceholderDashboard('Asisten Dosen', $user ?? []);
    // }

    // /* Dashboard Placeholder untuk Dosen */
    // public function dosenDashboard(): void
    // {
    //     Guard::requireRole('dosen');
    //     $user = Guard::user();
    //     $this->renderPlaceholderDashboard('Dosen Pembimbing', $user ?? []);
    // }

    // private function renderPlaceholderDashboard(string $roleLabel, array $user): void
    // {
    //     $baseUrl = Guard::getBaseUrl();
    //     $nama = htmlspecialchars($user['nama'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8');
    //     $email = htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8');
    //     $isActive = (int)($user['is_active'] ?? 0) === 1;

    //     echo "<!DOCTYPE html>
    //     <html lang='id' class='h-full bg-slate-50'>
    //     <head>
    //         <meta charset='UTF-8'>
    //         <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    //         <title>Dashboard {$roleLabel} — Absensi Lab</title>
    //         <script src='https://cdn.tailwindcss.com'></script>
    //         <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
    //         <style>body { font-family: 'Inter', sans-serif; }</style>
    //     </head>
    //     <body class='min-h-full flex items-center justify-center p-4 text-slate-800 bg-slate-50'>
    //         <div class='max-w-md w-full bg-white border border-slate-200 rounded-3xl shadow-xl p-6 sm:p-8 text-center space-y-6'>
    //             <div class='w-16 h-16 rounded-2xl bg-blue-50 border border-blue-200 text-[#1867c0] mx-auto flex items-center justify-center font-bold text-2xl shadow-xs'>
    //                 LAB
    //             </div>
                
    //             <div class='space-y-1.5'>
    //                 <span class='px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-[#1867c0] inline-block mb-1'>
    //                     Role: {$roleLabel}
    //                 </span>
    //                 <h2 class='text-xl sm:text-2xl font-bold text-slate-900'>Selamat Datang, {$nama}!</h2>
    //                 <p class='text-xs text-slate-500'>{$email}</p>
    //             </div>

    //             <div class='bg-slate-50 border border-slate-200 rounded-2xl p-4 text-left space-y-2'>
    //                 <div class='flex items-center justify-between text-xs'>
    //                     <span class='text-slate-500 font-medium'>Status Akun:</span>
    //                     " . ($isActive 
    //                         ? "<span class='font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200'>Aktif</span>" 
    //                         : "<span class='font-bold text-slate-500 bg-slate-200 px-2 py-0.5 rounded-md'>Nonaktif</span>") . "
    //                 </div>
    //                 <p class='text-xs text-slate-600 leading-relaxed pt-1 border-t border-slate-200'>
    //                     Modul khusus <strong>{$roleLabel}</strong> sedang dalam tahap integrasi. Silakan login menggunakan akun <strong>Super Admin</strong> untuk mengelola sistem atau klik tombol keluar di bawah.
    //                 </p>
    //             </div>

    //             <div class='flex flex-col gap-2.5 pt-2'>
    //                 <a href='{$baseUrl}/logout' class='w-full py-3 bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-md transition flex items-center justify-center gap-2 cursor-pointer'>
    //                     <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
    //                         <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'/>
    //                     </svg>
    //                     <span>Keluar / Ganti Akun</span>
    //                 </a>
    //             </div>

    //             <p class='text-[11px] text-slate-400'>&copy; " . date('Y') . " Laboratorium Sistem Informasi</p>
    //         </div>
    //     </body>
    //     </html>";
    //     exit;
    // }

    /* Logout */
    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
        
        session_start();
        Guard::setFlash('info', 'Anda telah berhasil logout.');
        Guard::redirect('/login');
    }
}
