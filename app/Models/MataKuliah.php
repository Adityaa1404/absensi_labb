<?php

namespace App\Models;

use Core\Database;

class MataKuliah
{
    /**
     * Ambil semua data mata kuliah beserta dosen pengampu dan jumlah asdos terplot
     */
    public static function all(array $filters = []): array
    {
        $sql = "
            SELECT m.*, u.nama as nama_dosen, u.email as email_dosen, u.identity_number as nidn_dosen,
                   (SELECT COUNT(*) FROM plotting p WHERE p.matkul_id = m.id_matkul AND p.is_active = 1) as total_asdos_aktif
            FROM mata_kuliah m 
            LEFT JOIN users u ON m.dosen_id = u.id_user 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (m.nama_matkul LIKE :s1 OR u.nama LIKE :s2)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params['s1'] = $searchTerm;
            $params['s2'] = $searchTerm;
        }

        if (!empty($filters['dosen_id'])) {
            $sql .= " AND m.dosen_id = :dosen_id";
            $params['dosen_id'] = (int)$filters['dosen_id'];
        }

        $sql .= " ORDER BY m.nama_matkul ASC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Cari mata kuliah berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $sql = "
            SELECT m.*, u.nama as nama_dosen, u.email as email_dosen 
            FROM mata_kuliah m 
            LEFT JOIN users u ON m.dosen_id = u.id_user 
            WHERE m.id_matkul = :id 
            LIMIT 1
        ";
        return Database::fetch($sql, ['id' => $id]);
    }

    /**
     * Metrik statistik mata kuliah
     */
    public static function getMetrics(): array
    {
        $totalMatkul   = (int)(Database::fetch("SELECT COUNT(*) as total FROM mata_kuliah")['total'] ?? 0);
        $matkulBerplot = (int)(Database::fetch("SELECT COUNT(DISTINCT matkul_id) as total FROM plotting WHERE is_active = 1")['total'] ?? 0);
        $totalDosen    = (int)(Database::fetch("SELECT COUNT(DISTINCT dosen_id) as total FROM mata_kuliah")['total'] ?? 0);

        return [
            'total'          => $totalMatkul,
            'berplot'        => $matkulBerplot,
            'belum_berplot'  => max(0, $totalMatkul - $matkulBerplot),
            'total_dosen'    => $totalDosen,
        ];
    }

    /**
     * Tambah Mata Kuliah Baru
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO mata_kuliah (nama_matkul, deskripsi, dosen_id)
                VALUES (:nama_matkul, :deskripsi, :dosen_id)";

        Database::query($sql, [
            'nama_matkul' => trim($data['nama_matkul']),
            'deskripsi'   => !empty($data['deskripsi']) ? trim($data['deskripsi']) : null,
            'dosen_id'    => (int)$data['dosen_id'],
        ]);

        return (int)Database::lastInsertId();
    }

    /**
     * Perbarui Data Mata Kuliah
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE mata_kuliah 
                SET nama_matkul = :nama_matkul, deskripsi = :deskripsi, dosen_id = :dosen_id
                WHERE id_matkul = :id";

        Database::query($sql, [
            'id'          => $id,
            'nama_matkul' => trim($data['nama_matkul']),
            'deskripsi'   => !empty($data['deskripsi']) ? trim($data['deskripsi']) : null,
            'dosen_id'    => (int)$data['dosen_id'],
        ]);

        return true;
    }

    /**
     * Hapus Mata Kuliah
     */
    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM mata_kuliah WHERE id_matkul = :id";
        Database::query($sql, ['id' => $id]);
        return true;
    }

    /**
     * Cek apakah mata kuliah memiliki plotting aktif atau absensi
     */
    public static function checkRelations(int $id): array
    {
        $relations = [];
        $plotting = Database::fetch("SELECT COUNT(*) as total FROM plotting WHERE matkul_id = :id", ['id' => $id]);
        if (!empty($plotting['total']) && (int)$plotting['total'] > 0) {
            $relations['plotting'] = (int)$plotting['total'];
        }
        return $relations;
    }
}
