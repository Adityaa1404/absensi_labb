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
