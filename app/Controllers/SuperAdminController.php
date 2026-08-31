<?php

namespace App\Controllers;

use Core\Guard;
use Core\Database;
use Core\Validator;
use App\Models\User;
use App\Models\MataKuliah;
use App\Models\Plotting;
use App\Models\Absensi;

class SuperAdminController
{
    /**
     * Dashboard Utama Super Admin
     */
    public function dashboard(): void
    {
        Guard::requireRole('super_admin');

        Plotting::syncExpiredStatus();

        $currentUser = Guard::user();

        // Ambil data statistik ringkasan
        $stats = [
            'total_users'    => (int)(Database::fetch("SELECT COUNT(*) as total FROM users")['total'] ?? 0),
            'total_matkul'   => (int)(Database::fetch("SELECT COUNT(*) as total FROM mata_kuliah")['total'] ?? 0),
            'total_plotting' => (int)(Database::fetch("SELECT COUNT(*) as total FROM plotting WHERE is_active = 1")['total'] ?? 0),
        ];

        // Ambil riwayat absensi terbaru untuk monitoring ringkas
        $recentAbsensi = Database::fetchAll("
            SELECT a.*, u.nama as nama_asdos, m.nama_matkul 
            FROM absensi a
            JOIN plotting p ON a.plotting_id = p.id_plotting
            JOIN users u ON p.asdos_id = u.id_user
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            ORDER BY a.created_at DESC
            LIMIT 5
        ");

        // Ambil data penugasan (plotting) & absensi untuk kalender tracking
        $calendarAbsensi = Database::fetchAll("
            SELECT a.id_absensi, a.tanggal, a.pertemuan_ke, a.jam_mulai, a.jam_selesai, a.status_verifikasi, a.deskripsi_tugas,
                   u.nama as nama_asdos, m.nama_matkul, d.nama as nama_dosen
            FROM absensi a
            LEFT JOIN plotting p ON a.plotting_id = p.id_plotting
            LEFT JOIN users u ON p.asdos_id = u.id_user
            LEFT JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            LEFT JOIN users d ON m.dosen_id = d.id_user
            ORDER BY a.tanggal DESC
        ");

        $calendarPlotting = Database::fetchAll("
            SELECT p.id_plotting, p.periode_mulai, p.periode_selesai, p.is_active,
                   u.nama as nama_asdos, m.nama_matkul, d.nama as nama_dosen
            FROM plotting p
            JOIN users u ON p.asdos_id = u.id_user
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            LEFT JOIN users d ON m.dosen_id = d.id_user
            WHERE p.is_active = 1
        ");

        require_once __DIR__ . '/../Views/SuperAdmin/dashboard.php';
    }

    // =========================================================================
    // MODUL: KELOLA PENGGUNA (USERS)
    // =========================================================================

    /**
     * Halaman Kelola Pengguna (Dosen, Asdos, & Super Admin)
     */
    public function users(): void
    {
        Guard::requireRole('super_admin');
        $currentUser = Guard::user();

        // Ambil metrik ringkasan
        $metrics = User::getMetrics();

        // Ambil seluruh daftar pengguna (filter dilakukan secara live di UI)
        $users = User::all();

        require_once __DIR__ . '/../Views/SuperAdmin/users.php';
    }

    /**
     * Tambah Pengguna Baru
     */
    public function createUser(): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $nama           = trim($_POST['nama'] ?? '');
        $identityNumber = trim($_POST['identity_number'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $noHp           = trim($_POST['no_hp'] ?? '');
        $role           = trim($_POST['role'] ?? '');
        $password       = $_POST['password'] ?? '';
        $isActive       = !empty($_POST['is_active']) ? 1 : 0;

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

        // Validasi input
        $validator = new Validator($_POST);
        $validator->rules([
            'nama'            => 'required|min:2|max:50',
            'identity_number' => 'required|min:2|max:100',
            'email'           => 'required|email|max:80',
            'role'            => 'required|in:dosen,asdos,super_admin',
            'password'        => 'required|min:6',
        ], [
            'nama.required'            => 'Nama lengkap wajib diisi.',
            'nama.min'                 => 'Nama lengkap minimal 2 karakter.',
            'identity_number.required' => 'Nomor Identitas (NIDN/NPM/ID) wajib diisi.',
            'email.required'           => 'Alamat email wajib diisi.',
            'email.email'              => 'Format alamat email tidak valid.',
            'role.required'            => 'Peran/Role wajib dipilih.',
            'role.in'                  => 'Peran yang dipilih tidak valid.',
            'password.required'        => 'Kata sandi wajib diisi.',
            'password.min'             => 'Kata sandi minimal terdiri dari 6 karakter.',
        ]);

        if ($validator->fails()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => $validator->firstError(),
                    'errors'  => $validator->errors()
                ]);
                exit;
            }
            $validator->flashErrors();
            Guard::redirect('/superadmin/users');
        }

        // Cek Keunikan Email
        if (!User::isUniqueEmail($email)) {
            $msg = 'Alamat email "' . htmlspecialchars($email) . '" sudah digunakan oleh pengguna lain.';
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Guard::setFlash('error', $msg);
            Guard::redirect('/superadmin/users');
        }

        // Cek Keunikan Nomor Identitas
        if (!User::isUniqueIdentityNumber($identityNumber)) {
            $msg = 'Nomor identitas "' . htmlspecialchars($identityNumber) . '" sudah terdaftar.';
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Guard::setFlash('error', $msg);
            Guard::redirect('/superadmin/users');
        }

        // Simpan Data Pengguna
        User::create([
            'nama'            => $nama,
            'identity_number' => $identityNumber,
            'email'           => $email,
            'no_hp'           => $noHp !== '' ? $noHp : null,
            'password'        => $password,
            'role'            => $role,
            'is_active'       => $isActive,
        ]);

        $roleLabel = match ($role) {
            'dosen'       => 'Dosen',
            'asdos'       => 'Asisten Dosen',
            'super_admin' => 'Super Admin',
            default       => 'Pengguna'
        };

        $successMsg = "Pengguna baru [{$nama}] ({$roleLabel}) berhasil ditambahkan ke sistem.";
        Guard::setFlash('success', $successMsg);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $successMsg
            ]);
            exit;
        }

        Guard::redirect('/superadmin/users');
    }

    /**
     * Perbarui Data Pengguna
     */
    public function updateUser(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $userId = (int)$id;
        $user = User::findById($userId);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

        if (!$user) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Pengguna tidak ditemukan dalam sistem.']);
                exit;
            }
            Guard::setFlash('error', 'Pengguna tidak ditemukan dalam sistem.');
            Guard::redirect('/superadmin/users');
        }

        $nama           = trim($_POST['nama'] ?? '');
        $identityNumber = trim($_POST['identity_number'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $noHp           = trim($_POST['no_hp'] ?? '');
        $role           = trim($_POST['role'] ?? '');
        $password       = $_POST['password'] ?? '';
        $isActive       = !empty($_POST['is_active']) ? 1 : 0;

        // Validasi input
        $rules = [
            'nama'            => 'required|min:2|max:50',
            'identity_number' => 'required|min:2|max:100',
            'email'           => 'required|email|max:80',
            'role'            => 'required|in:dosen,asdos,super_admin',
        ];

        // Jika password diisi, validasi panjangnya
        if (!empty($password)) {
            $rules['password'] = 'min:6';
        }

        $validator = new Validator($_POST);
        $validator->rules($rules, [
            'nama.required'            => 'Nama lengkap wajib diisi.',
            'identity_number.required' => 'Nomor Identitas (NIDN/NPM/ID) wajib diisi.',
            'email.required'           => 'Alamat email wajib diisi.',
            'email.email'              => 'Format alamat email tidak valid.',
            'role.required'            => 'Peran/Role wajib dipilih.',
            'password.min'             => 'Kata sandi baru minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => $validator->firstError(),
                    'errors'  => $validator->errors()
                ]);
                exit;
            }
            $validator->flashErrors();
            Guard::redirect('/superadmin/users');
        }

        // Proteksi: Super admin tidak boleh menonaktifkan akun miliknya sendiri
        if ($userId === Guard::id() && $isActive === 0) {
            $msg = 'Aksi ditolak: Anda tidak dapat menonaktifkan akun Super Admin Anda sendiri yang sedang aktif.';
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Guard::setFlash('error', $msg);
            Guard::redirect('/superadmin/users');
        }

        // Proteksi: Super admin tidak boleh mengubah rolenya sendiri jika hanya dia yang aktif
        if ($userId === Guard::id() && $role !== 'super_admin') {
            $msg = 'Aksi ditolak: Anda tidak dapat mengubah peran akun Anda sendiri saat sedang login.';
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Guard::setFlash('error', $msg);
            Guard::redirect('/superadmin/users');
        }

        // Cek Keunikan Email
        if (!User::isUniqueEmail($email, $userId)) {
            $msg = 'Alamat email "' . htmlspecialchars($email) . '" sudah digunakan oleh pengguna lain.';
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Guard::setFlash('error', $msg);
            Guard::redirect('/superadmin/users');
        }

        // Cek Keunikan Nomor Identitas
        if (!User::isUniqueIdentityNumber($identityNumber, $userId)) {
            $msg = 'Nomor identitas "' . htmlspecialchars($identityNumber) . '" sudah terdaftar.';
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            Guard::setFlash('error', $msg);
            Guard::redirect('/superadmin/users');
        }

        // Update ke Database
        User::update($userId, [
            'nama'            => $nama,
            'identity_number' => $identityNumber,
            'email'           => $email,
            'no_hp'           => $noHp !== '' ? $noHp : null,
            'role'            => $role,
            'is_active'       => $isActive,
            'password'        => $password,
        ]);

        // Jika user yang diedit adalah user yang sedang login, perbarui data di session
        if ($userId === Guard::id()) {
            $_SESSION['user']['nama']  = $nama;
            $_SESSION['user']['email'] = $email;
        }

        $successMsg = "Data pengguna [{$nama}] berhasil diperbarui.";
        Guard::setFlash('success', $successMsg);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $successMsg
            ]);
            exit;
        }

        Guard::redirect('/superadmin/users');
    }

    /**
     * Toggle Status Akun (Aktif / Nonaktif On/Off)
     */
    public function toggleUserStatus(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $userId = (int)$id;
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

        // Proteksi: Tidak boleh menonaktifkan akun sendiri
        if ($userId === Guard::id()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Aksi ditolak: Anda tidak dapat menonaktifkan akun Anda sendiri.'
                ]);
                exit;
            }
            Guard::setFlash('error', 'Aksi ditolak: Anda tidak dapat menonaktifkan akun Anda sendiri.');
            Guard::redirect('/superadmin/users');
        }

        $user = User::findById($userId);
        if (!$user) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan.'
                ]);
                exit;
            }
            Guard::setFlash('error', 'Pengguna tidak ditemukan.');
            Guard::redirect('/superadmin/users');
        }

        User::toggleStatus($userId);

        $newStatus = (int)$user['is_active'] === 1 ? 0 : 1;
        $statusText = $newStatus === 1 ? 'diaktifkan kembali' : 'dinonaktifkan';

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'    => true,
                'new_status' => $newStatus,
                'message'    => "Akun pengguna [{$user['nama']}] berhasil {$statusText}."
            ]);
            exit;
        }

        Guard::setFlash('success', "Akun pengguna [{$user['nama']}] berhasil {$statusText}.");
        Guard::redirect('/superadmin/users');
    }

    /**
     * Hapus Pengguna
     */
    public function deleteUser(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $userId = (int)$id;

        // Proteksi: Tidak boleh menghapus akun sendiri
        if ($userId === Guard::id()) {
            Guard::setFlash('error', 'Aksi ditolak: Anda tidak dapat menghapus akun Anda sendiri.');
            Guard::redirect('/superadmin/users');
        }

        $user = User::findById($userId);
        if (!$user) {
            Guard::setFlash('error', 'Pengguna tidak ditemukan.');
            Guard::redirect('/superadmin/users');
        }

        // Cek Keterikatan Relasi Database (Foreign Key)
        $relations = User::checkRelations($userId);
        if (!empty($relations)) {
            $reasons = [];
            if (!empty($relations['mata_kuliah'])) {
                $reasons[] = "pengampu pada {$relations['mata_kuliah']} mata kuliah";
            }
            if (!empty($relations['plotting'])) {
                $reasons[] = "terdaftar pada {$relations['plotting']} plotting asdos";
            }

            Guard::setFlash('error', "Pengguna [{$user['nama']}] tidak dapat dihapus karena masih menjadi " . implode(' dan ', $reasons) . ". Anda disarankan untuk menonaktifkan status akunnya sebagai gantinya.");
            Guard::redirect('/superadmin/users');
        }

        User::delete($userId);

        Guard::setFlash('success', "Pengguna [{$user['nama']}] berhasil dihapus dari sistem.");
        Guard::redirect('/superadmin/users');
    }

    // =========================================================================
    // MODUL: MASTER MATA KULIAH (MATKUL)
    // =========================================================================

    /**
     * Halaman Master Mata Kuliah
     */
    public function matkul(): void
    {
        Guard::requireRole('super_admin');
        $currentUser = Guard::user();

        $filters = [
            'search'   => trim($_GET['q'] ?? ''),
            'dosen_id' => $_GET['dosen'] ?? '',
        ];

        // Ambil metrik & data mata kuliah
        $metrics    = MataKuliah::getMetrics();
        $matkulList = MataKuliah::all($filters);

        // Ambil seluruh plotting untuk dikelompokkan per mata kuliah
        $allPlottings = Plotting::all();
        $plottingsByMatkul = [];
        foreach ($allPlottings as $p) {
            $plottingsByMatkul[$p['matkul_id']][] = $p;
        }

        foreach ($matkulList as &$m) {
            $m['plottings'] = $plottingsByMatkul[$m['id_matkul']] ?? [];
        }
        unset($m);

        // Ambil daftar dosen untuk dropdown pengampu
        $dosenList  = Database::fetchAll("
            SELECT id_user, nama, identity_number, email 
            FROM users 
            WHERE role = 'dosen' AND is_active = 1 
            ORDER BY nama ASC
        ");

        // Ambil daftar asdos aktif untuk form plotting baru khusus matkul
        $asdosList  = Database::fetchAll("
            SELECT id_user, nama, identity_number, email 
            FROM users 
            WHERE role = 'asdos' AND is_active = 1 
            ORDER BY nama ASC
        ");

        require_once __DIR__ . '/../Views/SuperAdmin/matkul.php';
    }

    /**
     * Tambah Mata Kuliah Baru
     */
    public function createMatkul(): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $nama      = trim($_POST['nama_matkul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $dosenId   = (int)($_POST['dosen_id'] ?? 0);

        $validator = new Validator($_POST);
        $validator->rules([
            'nama_matkul' => 'required|min:3|max:100',
            'dosen_id'    => 'required|numeric',
        ], [
            'nama_matkul.required' => 'Nama mata kuliah wajib diisi.',
            'dosen_id.required'    => 'Dosen pengampu wajib dipilih.',
        ]);

        if ($validator->fails()) {
            $validator->flashErrors();
            Guard::redirect('/superadmin/matkul');
        }

        MataKuliah::create([
            'nama_matkul' => $nama,
            'deskripsi'   => $deskripsi,
            'dosen_id'    => $dosenId,
        ]);

        Guard::setFlash('success', "Mata kuliah [{$nama}] berhasil ditambahkan.");
        Guard::redirect('/superadmin/matkul');
    }

    /**
     * Perbarui Mata Kuliah
     */
    public function updateMatkul(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $matkulId  = (int)$id;
        $nama      = trim($_POST['nama_matkul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $dosenId   = (int)($_POST['dosen_id'] ?? 0);

        $validator = new Validator($_POST);
        $validator->rules([
            'nama_matkul' => 'required|min:3|max:100',
            'dosen_id'    => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $validator->flashErrors();
            Guard::redirect('/superadmin/matkul');
        }

        MataKuliah::update($matkulId, [
            'nama_matkul' => $nama,
            'deskripsi'   => $deskripsi,
            'dosen_id'    => $dosenId,
        ]);

        Guard::setFlash('success', "Mata kuliah [{$nama}] berhasil diperbarui.");
        Guard::redirect('/superadmin/matkul');
    }

    /**
     * Hapus Mata Kuliah
     */
    public function deleteMatkul(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $matkulId = (int)$id;
        $matkul = MataKuliah::findById($matkulId);

        if (!$matkul) {
            Guard::setFlash('error', 'Mata kuliah tidak ditemukan.');
            Guard::redirect('/superadmin/matkul');
        }

        $relations = MataKuliah::checkRelations($matkulId);
        if (!empty($relations['plotting'])) {
            Guard::setFlash('error', "Mata kuliah [{$matkul['nama_matkul']}] tidak dapat dihapus karena masih memiliki {$relations['plotting']} plotting asdos aktif.");
            Guard::redirect('/superadmin/matkul');
        }

        MataKuliah::delete($matkulId);

        Guard::setFlash('success', "Mata kuliah [{$matkul['nama_matkul']}] berhasil dihapus.");
        Guard::redirect('/superadmin/matkul');
    }

    // =========================================================================
    // MODUL: PLOTTING ASISTEN DOSEN
    // =========================================================================

    /**
     * Halaman Plotting Asisten Dosen
     */
    public function plotting(): void
    {
        Guard::requireRole('super_admin');
        Guard::redirect('/superadmin/matkul');
    }

    /**
     * Tambah Plotting Baru
     */
    public function createPlotting(): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $redirectTo     = $_POST['redirect_to'] ?? '/superadmin/matkul';
        $matkulId       = (int)($_POST['matkul_id'] ?? 0);
        $asdosId        = (int)($_POST['asdos_id'] ?? 0);
        $periodeMulai   = trim($_POST['periode_mulai'] ?? '');
        $periodeSelesai = trim($_POST['periode_selesai'] ?? '');
        $today          = date('Y-m-d');
        $isActive       = ($periodeSelesai < $today) ? 0 : 1;

        $validator = new Validator($_POST);
        $validator->rules([
            'matkul_id'       => 'required|numeric',
            'asdos_id'        => 'required|numeric',
            'periode_mulai'   => 'required',
            'periode_selesai' => 'required',
        ], [
            'matkul_id.required'       => 'Mata kuliah wajib dipilih.',
            'asdos_id.required'        => 'Asisten dosen wajib dipilih.',
            'periode_mulai.required'   => 'Tanggal periode mulai wajib diisi.',
            'periode_selesai.required' => 'Tanggal periode selesai wajib diisi.',
        ]);

        if ($validator->fails()) {
            $validator->flashErrors();
            Guard::redirect($redirectTo);
        }

        // Cek apakah kombinasi matkul & asdos sudah pernah diplot
        if (Plotting::exists($matkulId, $asdosId)) {
            Guard::setFlash('error', 'Asisten dosen yang dipilih sudah pernah diplot pada mata kuliah ini.');
            Guard::redirect($redirectTo);
        }

        Plotting::create([
            'matkul_id'       => $matkulId,
            'asdos_id'        => $asdosId,
            'periode_mulai'   => $periodeMulai,
            'periode_selesai' => $periodeSelesai,
            'is_active'       => $isActive,
        ]);

        Guard::setFlash('success', 'Penugasan plotting asisten dosen berhasil dibuat.');
        Guard::redirect($redirectTo);
    }

    /**
     * Perbarui Data Plotting
     */
    public function updatePlotting(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $redirectTo     = $_POST['redirect_to'] ?? '/superadmin/matkul';
        $plottingId     = (int)$id;
        $matkulId       = (int)($_POST['matkul_id'] ?? 0);
        $asdosId        = (int)($_POST['asdos_id'] ?? 0);
        $periodeMulai   = trim($_POST['periode_mulai'] ?? '');
        $periodeSelesai = trim($_POST['periode_selesai'] ?? '');
        $today          = date('Y-m-d');
        $isActive       = ($periodeSelesai < $today) ? 0 : 1;

        $validator = new Validator($_POST);
        $validator->rules([
            'matkul_id'       => 'required|numeric',
            'asdos_id'        => 'required|numeric',
            'periode_mulai'   => 'required',
            'periode_selesai' => 'required',
        ]);

        if ($validator->fails()) {
            $validator->flashErrors();
            Guard::redirect($redirectTo);
        }

        if (Plotting::exists($matkulId, $asdosId, $plottingId)) {
            Guard::setFlash('error', 'Kombinasi mata kuliah dan asisten dosen ini sudah ada di penugasan lain.');
            Guard::redirect($redirectTo);
        }

        Plotting::update($plottingId, [
            'matkul_id'       => $matkulId,
            'asdos_id'        => $asdosId,
            'periode_mulai'   => $periodeMulai,
            'periode_selesai' => $periodeSelesai,
            'is_active'       => $isActive,
        ]);

        Guard::setFlash('success', 'Data plotting asisten dosen berhasil diperbarui.');
        Guard::redirect($redirectTo);
    }

    /**
     * Toggle Status Plotting (Aktif <-> Nonaktif)
     */
    public function togglePlottingStatus(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $redirectTo = $_POST['redirect_to'] ?? $_GET['redirect_to'] ?? '/superadmin/matkul';
        $plottingId = (int)$id;
        $plotting   = Plotting::findById($plottingId);

        if (!$plotting) {
            Guard::setFlash('error', 'Data plotting tidak ditemukan.');
            Guard::redirect($redirectTo);
        }

        Plotting::toggleStatus($plottingId);

        $newStatus = (int)$plotting['is_active'] === 1 ? 0 : 1;
        $statusText = $newStatus === 1 ? 'diaktifkan kembali' : 'dinonaktifkan (selesai)';

        Guard::setFlash('success', "Status penugasan asdos [{$plotting['nama_asdos']}] pada [{$plotting['nama_matkul']}] berhasil {$statusText}.");
        Guard::redirect($redirectTo);
    }

    /**
     * Hapus Plotting
     */
    public function deletePlotting(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $redirectTo = $_POST['redirect_to'] ?? $_GET['redirect_to'] ?? '/superadmin/matkul';
        $plottingId = (int)$id;
        $plotting   = Plotting::findById($plottingId);

        if (!$plotting) {
            Guard::setFlash('error', 'Data plotting tidak ditemukan.');
            Guard::redirect($redirectTo);
        }

        $relations = Plotting::checkRelations($plottingId);
        if (!empty($relations['absensi'])) {
            Guard::setFlash('error', "Plotting tidak dapat dihapus karena sudah memiliki {$relations['absensi']} data absensi praktikum tersimpan. Anda disarankan untuk menonaktifkan status plotting.");
            Guard::redirect($redirectTo);
        }

        Plotting::delete($plottingId);

        Guard::setFlash('success', 'Data plotting berhasil dihapus.');
        Guard::redirect($redirectTo);
    }

    // =========================================================================
    // MODUL: MONITORING SELURUH ABSENSI & VERIFIKASI (READ-ONLY / AUDIT)
    // =========================================================================

    /**
     * Halaman Monitoring Seluruh Absensi & Verifikasi di Seluruh Sistem
     */
    public function monitoring(): void
    {
        Guard::requireRole('super_admin');

        $currentUser = Guard::user();

        // Ambil metrik ringkasan
        $metrics = Absensi::getMonitoringMetrics();

        // Ambil seluruh data absensi (filter dilakukan secara live di UI tanpa reload)
        $absensiList = Absensi::getAllMonitoring();

        // Ambil daftar mata kuliah untuk opsi filter
        $matkulList = MataKuliah::all();

        require_once __DIR__ . '/../Views/SuperAdmin/monitoring.php';
    }

    /**
     * Ubah Status Verifikasi Absensi (Hak Akses Super Admin)
     */
    public function updateAbsensiStatus(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $absensiId = (int)$id;
        $absensi   = Absensi::findByIdWithDetails($absensiId);

        if (!$absensi) {
            Guard::setFlash('error', 'Data absensi tidak ditemukan.');
            Guard::redirect('/superadmin/monitoring');
        }

        $status     = trim($_POST['status_verifikasi'] ?? '');
        $pesanDosen = trim($_POST['pesan_dosen'] ?? '');

        $validator = new Validator($_POST);
        $validator->rules([
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
        ], [
            'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
            'status_verifikasi.in'       => 'Pilihan status verifikasi tidak valid.',
        ]);

        if ($validator->fails()) {
            $validator->flashErrors();
            Guard::redirect('/superadmin/monitoring');
        }

        Absensi::updateStatusVerifikasi($absensiId, $status, $pesanDosen);

        $statusLabel = match ($status) {
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => 'Menunggu Verifikasi (Pending)'
        };

        Guard::setFlash('success', "Status verifikasi absensi [#{$absensiId}] ({$absensi['nama_asdos']} - {$absensi['nama_matkul']}) berhasil diubah menjadi: {$statusLabel}.");
        Guard::redirect('/superadmin/monitoring');
    }

    /**
     * Hapus Data Absensi (Hak Akses Super Admin)
     */
    public function deleteAbsensi(string $id): void
    {
        Guard::requireRole('super_admin');
        Guard::verifyCsrf();

        $absensiId = (int)$id;
        $absensi   = Absensi::findByIdWithDetails($absensiId);

        if (!$absensi) {
            Guard::setFlash('error', 'Data absensi tidak ditemukan.');
            Guard::redirect('/superadmin/monitoring');
        }

        // Hapus berkas foto fisik dari disk jika ada
        $uploadDir = __DIR__ . '/../../public/uploads/absensi/';
        if (!empty($absensi['foto_kegiatan'])) {
            $path = $uploadDir . basename($absensi['foto_kegiatan']);
            if (is_file($path)) {
                @unlink($path);
            }
        }
        if (!empty($absensi['foto_selfie'])) {
            $path = $uploadDir . basename($absensi['foto_selfie']);
            if (is_file($path)) {
                @unlink($path);
            }
        }

        Absensi::delete($absensiId);

        Guard::setFlash('success', "Data absensi [#{$absensiId}] ({$absensi['nama_asdos']} - {$absensi['nama_matkul']}) berhasil dihapus secara permanen.");
        Guard::redirect('/superadmin/monitoring');
    }
}
