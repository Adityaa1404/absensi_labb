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
                u_asdos.nama LIKE :s1 
                OR u_asdos.identity_number LIKE :s2 
                OR m.nama_matkul LIKE :s3 
                OR u_dosen.nama LIKE :s4 
                OR a.deskripsi_tugas LIKE :s5
            )";
            $searchTerm = '%' . $filters['search'] . '%';
            $params['s1'] = $searchTerm;
            $params['s2'] = $searchTerm;
            $params['s3'] = $searchTerm;
            $params['s4'] = $searchTerm;
            $params['s5'] = $searchTerm;
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

    // =========================================================================
    // MODUL: KHUSUS ASISTEN DOSEN (F4 & F6 - PRD)
    // =========================================================================

    /**
     * Ambil riwayat absensi milik seorang asdos dengan filter (F6 - History Asdos)
     * Selalu bisa dipanggil walau akun nonaktif (read-only history tetap terbuka - BR6)
     */
    public static function getByAsdos(int $asdosId, array $filters = []): array
    {
        $sql = "
            SELECT a.*, m.id_matkul, m.nama_matkul, u_dosen.nama as nama_dosen
            FROM absensi a
            JOIN plotting p ON a.plotting_id = p.id_plotting
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            LEFT JOIN users u_dosen ON m.dosen_id = u_dosen.id_user
            WHERE p.asdos_id = :asdos_id
        ";
        $params = ['asdos_id' => $asdosId];

        if (!empty($filters['matkul_id'])) {
            $sql .= " AND m.id_matkul = :matkul_id";
            $params['matkul_id'] = (int)$filters['matkul_id'];
        }

        if (!empty($filters['status_verifikasi'])) {
            $sql .= " AND a.status_verifikasi = :status_verifikasi";
            $params['status_verifikasi'] = $filters['status_verifikasi'];
        }

        if (!empty($filters['date_start'])) {
            $sql .= " AND a.tanggal >= :date_start";
            $params['date_start'] = $filters['date_start'];
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND a.tanggal <= :date_end";
            $params['date_end'] = $filters['date_end'];
        }

        $sql .= " ORDER BY a.tanggal DESC, a.created_at DESC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Cari 1 data absensi TAPI wajib milik asdos ybs (validasi kepemilikan sebelum edit/hapus)
     */
    public static function findByIdForAsdos(int $id, int $asdosId): ?array
    {
        $sql = "
            SELECT a.*, p.asdos_id, p.matkul_id, p.is_active as plotting_is_active, m.nama_matkul
            FROM absensi a
            JOIN plotting p ON a.plotting_id = p.id_plotting
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            WHERE a.id_absensi = :id AND p.asdos_id = :asdos_id
            LIMIT 1
        ";
        return Database::fetch($sql, ['id' => $id, 'asdos_id' => $asdosId]);
    }

    /**
     * Statistik ringkas absensi milik seorang asdos (widget dashboard)
     */
    public static function getMetricsByAsdos(int $asdosId): array
    {
        $sql = "
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN a.status_verifikasi = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN a.status_verifikasi = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN a.status_verifikasi = 'ditolak' THEN 1 ELSE 0 END) as ditolak
            FROM absensi a
            JOIN plotting p ON a.plotting_id = p.id_plotting
            WHERE p.asdos_id = :asdos_id
        ";
        $row = Database::fetch($sql, ['asdos_id' => $asdosId]) ?? [];

        return [
            'total'     => (int)($row['total'] ?? 0),
            'disetujui' => (int)($row['disetujui'] ?? 0),
            'pending'   => (int)($row['pending'] ?? 0),
            'ditolak'   => (int)($row['ditolak'] ?? 0),
        ];
    }

    /**
     * Ambil absensi terbaru milik seorang asdos (widget dashboard)
     */
    public static function getRecentByAsdos(int $asdosId, int $limit = 5): array
    {
        $sql = "
            SELECT a.*, m.nama_matkul
            FROM absensi a
            JOIN plotting p ON a.plotting_id = p.id_plotting
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            WHERE p.asdos_id = :asdos_id
            ORDER BY a.created_at DESC
            LIMIT :limit
        ";
        return Database::fetchAll($sql, ['asdos_id' => $asdosId, 'limit' => $limit]);
    }

    /**
     * Tambah data absensi baru (F4). Timestamp created_at diisi otomatis oleh server/DB (BR3).
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO absensi
                (plotting_id, tanggal, pertemuan_ke, jam_mulai, jam_selesai, deskripsi_tugas, foto_kegiatan, foto_selfie, status_verifikasi)
                VALUES
                (:plotting_id, :tanggal, :pertemuan_ke, :jam_mulai, :jam_selesai, :deskripsi_tugas, :foto_kegiatan, :foto_selfie, 'pending')";

        Database::query($sql, [
            'plotting_id'     => (int)$data['plotting_id'],
            'tanggal'         => $data['tanggal'],
            'pertemuan_ke'    => !empty($data['pertemuan_ke']) ? (int)$data['pertemuan_ke'] : null,
            'jam_mulai'       => !empty($data['jam_mulai']) ? $data['jam_mulai'] : null,
            'jam_selesai'     => !empty($data['jam_selesai']) ? $data['jam_selesai'] : null,
            'deskripsi_tugas' => trim($data['deskripsi_tugas']),
            'foto_kegiatan'   => $data['foto_kegiatan'],
            'foto_selfie'     => $data['foto_selfie'],
        ]);

        return (int)Database::lastInsertId();
    }

    /**
     * Perbarui data absensi. Hanya boleh dipanggil Controller selama status masih 'pending' (BR4).
     * updated_at otomatis terisi oleh DB (ON UPDATE CURRENT_TIMESTAMP) - BR3.
     */
    public static function update(int $id, array $data): bool
    {
        $fields = [
            'tanggal = :tanggal',
            'pertemuan_ke = :pertemuan_ke',
            'jam_mulai = :jam_mulai',
            'jam_selesai = :jam_selesai',
            'deskripsi_tugas = :deskripsi_tugas',
        ];

        $params = [
            'id'              => $id,
            'tanggal'         => $data['tanggal'],
            'pertemuan_ke'    => !empty($data['pertemuan_ke']) ? (int)$data['pertemuan_ke'] : null,
            'jam_mulai'       => !empty($data['jam_mulai']) ? $data['jam_mulai'] : null,
            'jam_selesai'     => !empty($data['jam_selesai']) ? $data['jam_selesai'] : null,
            'deskripsi_tugas' => trim($data['deskripsi_tugas']),
        ];

        // Foto bersifat opsional saat update - hanya diganti jika ada file baru diunggah
        if (!empty($data['foto_kegiatan'])) {
            $fields[] = 'foto_kegiatan = :foto_kegiatan';
            $params['foto_kegiatan'] = $data['foto_kegiatan'];
        }
        if (!empty($data['foto_selfie'])) {
            $fields[] = 'foto_selfie = :foto_selfie';
            $params['foto_selfie'] = $data['foto_selfie'];
        }

        $sql = "UPDATE absensi SET " . implode(', ', $fields) . " WHERE id_absensi = :id";
        Database::query($sql, $params);

        return true;
    }

    /**
     * Hapus data absensi. Hanya boleh dipanggil Controller selama status masih 'pending' (BR4).
     */
    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM absensi WHERE id_absensi = :id";
        Database::query($sql, ['id' => $id]);
        return true;
    }

    /**
     * Ubah status verifikasi absensi dan catatan/pesan (oleh Super Admin atau Dosen Pengampu)
     */
    public static function updateStatusVerifikasi(int $id, string $status, ?string $pesanDosen = null): bool
    {
        $sql = "UPDATE absensi SET status_verifikasi = :status, pesan_dosen = :pesan_dosen WHERE id_absensi = :id";
        Database::query($sql, [
            'id'          => $id,
            'status'      => $status,
            'pesan_dosen' => !empty($pesanDosen) ? trim($pesanDosen) : null,
        ]);
        return true;
    }
}