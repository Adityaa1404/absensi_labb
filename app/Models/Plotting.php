<?php

namespace App\Models;

use Core\Database;

class Plotting
{
    /**
     * Otomatis nonaktifkan (is_active = 0) penugasan plotting yang sudah melewati periode selesai mengajar
     */
    public static function syncExpiredStatus(): void
    {
        Database::query("UPDATE plotting SET is_active = 0 WHERE is_active = 1 AND periode_selesai < CURDATE()");
    }

    /**
     * Ambil semua data plotting dengan relasi mata kuliah, asdos, dan dosen
     */
    public static function all(array $filters = []): array
    {
        self::syncExpiredStatus();

        $sql = "
            SELECT p.*, m.nama_matkul, 
                   u.nama as nama_asdos, u.email as email_asdos, u.identity_number as npm_asdos, u.no_hp as nohp_asdos,
                   d.nama as nama_dosen, d.email as email_dosen, d.identity_number as nidn_dosen
            FROM plotting p
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            JOIN users u ON p.asdos_id = u.id_user
            LEFT JOIN users d ON m.dosen_id = d.id_user
            WHERE 1=1
        ";
        $params = [];

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $sql .= " AND p.is_active = :is_active";
            $params['is_active'] = (int)$filters['is_active'];
        }

        if (!empty($filters['matkul_id'])) {
            $sql .= " AND p.matkul_id = :matkul_id";
            $params['matkul_id'] = (int)$filters['matkul_id'];
        }

        if (!empty($filters['asdos_id'])) {
            $sql .= " AND p.asdos_id = :asdos_id";
            $params['asdos_id'] = (int)$filters['asdos_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (m.nama_matkul LIKE :search OR u.nama LIKE :search OR u.identity_number LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY p.is_active DESC, p.created_at DESC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Ambil plotting aktif
     */
    public static function getActive(): array
    {
        self::syncExpiredStatus();
        return self::all(['is_active' => 1]);
    }

    /**
     * Cari plotting berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        self::syncExpiredStatus();

        $sql = "
            SELECT p.*, m.nama_matkul, 
                   u.nama as nama_asdos, u.email as email_asdos, u.identity_number as npm_asdos,
                   d.nama as nama_dosen, d.email as email_dosen
            FROM plotting p
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            JOIN users u ON p.asdos_id = u.id_user
            LEFT JOIN users d ON m.dosen_id = d.id_user
            WHERE p.id_plotting = :id 
            LIMIT 1
        ";
        return Database::fetch($sql, ['id' => $id]);
    }

    /**
     * Cek apakah kombinasi matkul_id dan asdos_id sudah pernah diplot
     */
    public static function exists(int $matkulId, int $asdosId, ?int $ignoreId = null): bool
    {
        $sql = "SELECT id_plotting FROM plotting WHERE matkul_id = :matkul_id AND asdos_id = :asdos_id";
        $params = [
            'matkul_id' => $matkulId,
            'asdos_id'  => $asdosId,
        ];

        if ($ignoreId !== null) {
            $sql .= " AND id_plotting != :id";
            $params['id'] = $ignoreId;
        }

        $sql .= " LIMIT 1";
        return Database::fetch($sql, $params) !== null;
    }

    /**
     * Metrik statistik plotting
     */
    public static function getMetrics(): array
    {
        self::syncExpiredStatus();

        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
                COUNT(DISTINCT CASE WHEN is_active = 1 THEN asdos_id END) as asdos_terplot
            FROM plotting
        ";
        $row = Database::fetch($sql) ?? [];

        return [
            'total'          => (int)($row['total'] ?? 0),
            'active'         => (int)($row['active'] ?? 0),
            'inactive'       => (int)($row['inactive'] ?? 0),
            'asdos_terplot'  => (int)($row['asdos_terplot'] ?? 0),
        ];
    }

    /**
     * Tambah Plotting Baru
     */
    public static function create(array $data): int
    {
        $today = date('Y-m-d');
        $isActive = ($data['periode_selesai'] < $today) ? 0 : (isset($data['is_active']) ? (int)$data['is_active'] : 1);

        $sql = "INSERT INTO plotting (matkul_id, asdos_id, periode_mulai, periode_selesai, is_active)
                VALUES (:matkul_id, :asdos_id, :periode_mulai, :periode_selesai, :is_active)";

        Database::query($sql, [
            'matkul_id'       => (int)$data['matkul_id'],
            'asdos_id'        => (int)$data['asdos_id'],
            'periode_mulai'   => $data['periode_mulai'],
            'periode_selesai' => $data['periode_selesai'],
            'is_active'       => $isActive,
        ]);

        return (int)Database::lastInsertId();
    }

    /**
     * Perbarui Data Plotting
     */
    public static function update(int $id, array $data): bool
    {
        $today = date('Y-m-d');
        $isActive = ($data['periode_selesai'] < $today) ? 0 : (isset($data['is_active']) ? (int)$data['is_active'] : 1);

        $sql = "UPDATE plotting 
                SET matkul_id = :matkul_id, asdos_id = :asdos_id, 
                    periode_mulai = :periode_mulai, periode_selesai = :periode_selesai, is_active = :is_active
                WHERE id_plotting = :id";

        Database::query($sql, [
            'id'              => $id,
            'matkul_id'       => (int)$data['matkul_id'],
            'asdos_id'        => (int)$data['asdos_id'],
            'periode_mulai'   => $data['periode_mulai'],
            'periode_selesai' => $data['periode_selesai'],
            'is_active'       => $isActive,
        ]);

        return true;
    }

    /**
     * Toggle Status Plotting (Aktif <-> Nonaktif)
     */
    public static function toggleStatus(int $id): bool
    {
        $sql = "UPDATE plotting SET is_active = 1 - is_active WHERE id_plotting = :id";
        Database::query($sql, ['id' => $id]);
        return true;
    }

    /**
     * Hapus Plotting
     */
    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM plotting WHERE id_plotting = :id";
        Database::query($sql, ['id' => $id]);
        return true;
    }

    /**
     * Cek keterikatan absensi
     */
    public static function checkRelations(int $id): array
    {
        $relations = [];
        $absensi = Database::fetch("SELECT COUNT(*) as total FROM absensi WHERE plotting_id = :id", ['id' => $id]);
        if (!empty($absensi['total']) && (int)$absensi['total'] > 0) {
            $relations['absensi'] = (int)$absensi['total'];
        }
        return $relations;
    }
}
