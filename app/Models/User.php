<?php

namespace App\Models;

use Core\Database;

class User
{
    /**
     * Ambil seluruh data pengguna dengan opsi filter dan pencarian
     */
    public static function all(array $filters = []): array
    {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if (!empty($filters['role']) && in_array($filters['role'], ['dosen', 'asdos', 'super_admin'], true)) {
            $sql .= " AND role = :role";
            $params['role'] = $filters['role'];
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = (int)$filters['is_active'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (nama LIKE :s1 OR identity_number LIKE :s2 OR email LIKE :s3 OR no_hp LIKE :s4)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params['s1'] = $searchTerm;
            $params['s2'] = $searchTerm;
            $params['s3'] = $searchTerm;
            $params['s4'] = $searchTerm;
        }

        $sql .= " ORDER BY CASE role WHEN 'super_admin' THEN 1 WHEN 'dosen' THEN 2 WHEN 'asdos' THEN 3 ELSE 4 END, nama ASC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Ambil data metrik ringkasan pengguna
     */
    public static function getMetrics(): array
    {
        $totalUsers    = (int)(Database::fetch("SELECT COUNT(*) as total FROM users")['total'] ?? 0);
        $totalDosen    = (int)(Database::fetch("SELECT COUNT(*) as total FROM users WHERE role = 'dosen'")['total'] ?? 0);
        $totalAsdos    = (int)(Database::fetch("SELECT COUNT(*) as total FROM users WHERE role = 'asdos'")['total'] ?? 0);
        $totalAdmin    = (int)(Database::fetch("SELECT COUNT(*) as total FROM users WHERE role = 'super_admin'")['total'] ?? 0);
        $totalActive   = (int)(Database::fetch("SELECT COUNT(*) as total FROM users WHERE is_active = 1")['total'] ?? 0);
        $totalInactive = (int)(Database::fetch("SELECT COUNT(*) as total FROM users WHERE is_active = 0")['total'] ?? 0);

        return [
            'total'       => $totalUsers,
            'dosen'       => $totalDosen,
            'asdos'       => $totalAsdos,
            'super_admin' => $totalAdmin,
            'active'      => $totalActive,
            'inactive'    => $totalInactive,
        ];
    }

    /**
     * Cari user berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id_user = :id LIMIT 1";
        return Database::fetch($sql, ['id' => $id]);
    }

    /**
     * Cari user berdasarkan Email
     */
    public static function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        return Database::fetch($sql, ['email' => $email]);
    }

    /**
     * Cari user berdasarkan Identity Number (NIM/NIDN)
     */
    public static function findByIdentityNumber(string $identityNumber): ?array
    {
        $sql = "SELECT * FROM users WHERE identity_number = :identity LIMIT 1";
        return Database::fetch($sql, ['identity' => $identityNumber]);
    }

    /**
     * Cek keunikan email (dengan opsi abaikan ID tertentu untuk mode edit)
     */
    public static function isUniqueEmail(string $email, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id_user FROM users WHERE email = :email";
        $params = ['email' => $email];

        if ($ignoreId !== null) {
            $sql .= " AND id_user != :id";
            $params['id'] = $ignoreId;
        }

        $sql .= " LIMIT 1";
        return Database::fetch($sql, $params) === null;
    }

    /**
     * Cek keunikan nomor identitas (NIM/NIDN)
     */
    public static function isUniqueIdentityNumber(string $identityNumber, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id_user FROM users WHERE identity_number = :identity";
        $params = ['identity' => $identityNumber];

        if ($ignoreId !== null) {
            $sql .= " AND id_user != :id";
            $params['id'] = $ignoreId;
        }

        $sql .= " LIMIT 1";
        return Database::fetch($sql, $params) === null;
    }

    /**
     * Buat Pengguna Baru
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO users (nama, identity_number, email, no_hp, password, role, is_active)
                VALUES (:nama, :identity_number, :email, :no_hp, :password, :role, :is_active)";

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        Database::query($sql, [
            'nama'            => $data['nama'],
            'identity_number' => $data['identity_number'] ?? null,
            'email'           => !empty($data['email']) ? $data['email'] : null,
            'no_hp'           => $data['no_hp'] ?? null,
            'password'        => $hashedPassword,
            'role'            => $data['role'],
            'is_active'       => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ]);

        return (int)Database::lastInsertId();
    }

    /**
     * Perbarui Data Pengguna
     */
    public static function update(int $id, array $data): bool
    {
        $fields = [
            'nama = :nama',
            'identity_number = :identity_number',
            'email = :email',
            'no_hp = :no_hp',
            'role = :role',
            'is_active = :is_active',
        ];

        $params = [
            'id'              => $id,
            'nama'            => $data['nama'],
            'identity_number' => $data['identity_number'] ?? null,
            'email'           => !empty($data['email']) ? $data['email'] : null,
            'no_hp'           => $data['no_hp'] ?? null,
            'role'            => $data['role'],
            'is_active'       => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ];

        // Jika password diisi, perbarui password hash
        if (!empty($data['password'])) {
            $fields[] = 'password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id_user = :id";
        Database::query($sql, $params);

        return true;
    }

    /**
     * Toggle Status Akun (Aktif <-> Nonaktif)
     */
    public static function toggleStatus(int $id): bool
    {
        $sql = "UPDATE users SET is_active = 1 - is_active WHERE id_user = :id";
        Database::query($sql, ['id' => $id]);
        return true;
    }

    /**
     * Hapus Pengguna
     */
    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id_user = :id";
        Database::query($sql, ['id' => $id]);
        return true;
    }

    /**
     * Periksa Keterikatan Relasi Pengguna (Foreign Keys)
     */
    public static function checkRelations(int $id): array
    {
        $relations = [];

        // 1. Cek pengampu mata kuliah (jika dosen)
        $matkul = Database::fetch("SELECT COUNT(*) as total FROM mata_kuliah WHERE dosen_id = :id", ['id' => $id]);
        if (!empty($matkul['total']) && (int)$matkul['total'] > 0) {
            $relations['mata_kuliah'] = (int)$matkul['total'];
        }

        // 2. Cek plotting asisten (jika asdos)
        $plotting = Database::fetch("SELECT COUNT(*) as total FROM plotting WHERE asdos_id = :id", ['id' => $id]);
        if (!empty($plotting['total']) && (int)$plotting['total'] > 0) {
            $relations['plotting'] = (int)$plotting['total'];
        }

        return $relations;
    }
}
