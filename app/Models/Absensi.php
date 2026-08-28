<?php

namespace App\Models;

use Core\Database;

class Absensi
{
    /**
     * Ambil data absensi terbaru untuk widget / preview
     */
    public static function getRecent(int $limit = 5): array
    {
        $sql = "
            SELECT a.*, 
                   u_asdos.nama as nama_asdos, u_asdos.identity_number as npm_asdos,
                   m.nama_matkul,
                   u_dosen.nama as nama_dosen
            FROM absensi a
            JOIN plotting p ON a.plotting_id = p.id_plotting
            JOIN users u_asdos ON p.asdos_id = u_asdos.id_user
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            LEFT JOIN users u_dosen ON m.dosen_id = u_dosen.id_user
            ORDER BY a.created_at DESC
            LIMIT :limit
        ";
        return Database::fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Ambil seluruh data absensi & verifikasi dengan filter lengkap (Monitoring Super Admin)
     */
    public static function getAllMonitoring(array $filters = []): array
    {
        $sql = "
            SELECT a.id_absensi, a.plotting_id, a.tanggal, a.pertemuan_ke, a.jam_mulai, a.jam_selesai,
                   a.deskripsi_tugas, a.foto_kegiatan, a.foto_selfie, a.status_verifikasi, a.pesan_dosen,
                   a.created_at, a.updated_at,
                   u_asdos.id_user as asdos_id, u_asdos.nama as nama_asdos, u_asdos.identity_number as npm_asdos, u_asdos.email as email_asdos,
                   m.id_matkul, m.nama_matkul,
                   u_dosen.id_user as dosen_id, u_dosen.nama as nama_dosen, u_dosen.identity_number as nidn_dosen
            FROM absensi a
            JOIN plotting p ON a.plotting_id = p.id_plotting
            JOIN users u_asdos ON p.asdos_id = u_asdos.id_user
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            LEFT JOIN users u_dosen ON m.dosen_id = u_dosen.id_user
            WHERE 1=1
        ";

        $params = [];

        // Filter Status Verifikasi
        if (!empty($filters['status_verifikasi'])) {
            $sql .= " AND a.status_verifikasi = :status_verifikasi";
            $params['status_verifikasi'] = $filters['status_verifikasi'];
        }

        // Filter Mata Kuliah
        if (!empty($filters['matkul_id'])) {
            $sql .= " AND m.id_matkul = :matkul_id";
            $params['matkul_id'] = (int)$filters['matkul_id'];
        }

        // Filter Rentang Tanggal
        if (!empty($filters['date_start'])) {
            $sql .= " AND a.tanggal >= :date_start";
            $params['date_start'] = $filters['date_start'];
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND a.tanggal <= :date_end";
            $params['date_end'] = $filters['date_end'];
        }

        // Filter Pencarian Teks (Search)
        if (!empty($filters['search'])) {
            $sql .= " AND (
                u_asdos.nama LIKE :search 
                OR u_asdos.identity_number LIKE :search 
                OR m.nama_matkul LIKE :search 
                OR u_dosen.nama LIKE :search 
                OR a.deskripsi_tugas LIKE :search
            )";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY a.tanggal DESC, a.created_at DESC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Ambil statistik metrik monitoring untuk Super Admin
     */
    public static function getMonitoringMetrics(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status_verifikasi = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN status_verifikasi = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status_verifikasi = 'ditolak' THEN 1 ELSE 0 END) as ditolak
            FROM absensi
        ";
        $row = Database::fetch($sql) ?? [];

        return [
            'total'     => (int)($row['total'] ?? 0),
            'disetujui' => (int)($row['disetujui'] ?? 0),
            'pending'   => (int)($row['pending'] ?? 0),
            'ditolak'   => (int)($row['ditolak'] ?? 0),
        ];
    }

    /**
     * Ambil detail 1 data absensi beserta relasinya
     */
    public static function findByIdWithDetails(int $id): ?array
    {
        $sql = "
            SELECT a.*,
                   u_asdos.id_user as asdos_id, u_asdos.nama as nama_asdos, u_asdos.identity_number as npm_asdos, u_asdos.email as email_asdos, u_asdos.no_hp as no_hp_asdos,
                   m.id_matkul, m.nama_matkul, m.deskripsi as deskripsi_matkul,
                   u_dosen.id_user as dosen_id, u_dosen.nama as nama_dosen, u_dosen.identity_number as nidn_dosen, u_dosen.email as email_dosen
            FROM absensi a
            JOIN plotting p ON a.plotting_id = p.id_plotting
            JOIN users u_asdos ON p.asdos_id = u_asdos.id_user
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            LEFT JOIN users u_dosen ON m.dosen_id = u_dosen.id_user
            WHERE a.id_absensi = :id
            LIMIT 1
        ";
        return Database::fetch($sql, ['id' => $id]);
    }
}
