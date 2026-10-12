<?php

namespace App\Models;

use Core\Database;

class Dispensasi
{
    /**
     * Konversi angka bulan (1-12) ke angka romawi
     */
    public static function toRoman(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romans[$month] ?? 'I';
    }

    /**
     * Generate format nomor surat resmi secara otomatis:
     * {urut}/DISPEN/LAB-SI/{bulan_romawi}/{tahun}
     * Contoh: 001/DISPEN/LAB-SI/X/2026
     */
    public static function generateNomorSurat(): string
    {
        $currentYear  = (int)date('Y');
        $currentMonth = (int)date('n');

        $row = Database::fetch(
            "SELECT COUNT(*) as total FROM dispensasi WHERE YEAR(created_at) = :year AND MONTH(created_at) = :month",
            ['year' => $currentYear, 'month' => $currentMonth]
        );

        $seq = (int)($row['total'] ?? 0) + 1;
        $paddedSeq = str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
        $romanMonth = self::toRoman($currentMonth);

        return "{$paddedSeq}/DISPEN/LAB-SI/{$romanMonth}/{$currentYear}";
    }

    /**
     * Simpan pengajuan surat dispensasi baru
     */
    public static function create(array $data): int
    {
        $nomorSurat = $data['nomor_surat'] ?? self::generateNomorSurat();

        $jenisKegiatan = in_array($data['jenis_kegiatan'] ?? '', ['magang', 'perkuliahan'], true)
            ? $data['jenis_kegiatan']
            : 'magang';

        $sql = "
            INSERT INTO dispensasi (
                plotting_id, nomor_surat, tanggal_pelaksanaan, 
                tujuan_surat, tempat_pelaksanaan, jenis_kegiatan, status
            ) VALUES (
                :plotting_id, :nomor_surat, :tanggal_pelaksanaan, 
                :tujuan_surat, :tempat_pelaksanaan, :jenis_kegiatan, 'menunggu'
            )
        ";

        Database::query($sql, [
            'plotting_id'         => (int)$data['plotting_id'],
            'nomor_surat'         => $nomorSurat,
            'tanggal_pelaksanaan' => $data['tanggal_pelaksanaan'],
            'tujuan_surat'        => trim($data['tujuan_surat']),
            'tempat_pelaksanaan'  => trim($data['tempat_pelaksanaan']),
            'jenis_kegiatan'      => $jenisKegiatan,
        ]);

        return (int)Database::lastInsertId();
    }

    /**
     * Ambil 1 data dispensasi lengkap beserta relasi plotting, matkul, asdos, dan dosen
     */
    public static function findById(int $id): ?array
    {
        $sql = "
            SELECT 
                d.*,
                p.asdos_id, p.matkul_id, p.periode_mulai, p.periode_selesai, p.is_active as plotting_active,
                m.nama_matkul, m.jam_mulai, m.jam_selesai, m.dosen_id,
                asdos.nama as nama_asdos, asdos.identity_number as npm_asdos, asdos.email as email_asdos, asdos.no_hp as nohp_asdos,
                dosen.nama as nama_dosen, dosen.identity_number as nip_dosen, dosen.email as email_dosen
            FROM dispensasi d
            JOIN plotting p ON d.plotting_id = p.id_plotting
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            JOIN users asdos ON p.asdos_id = asdos.id_user
            LEFT JOIN users dosen ON m.dosen_id = dosen.id_user
            WHERE d.id_dispensasi = :id
            LIMIT 1
        ";

        return Database::fetch($sql, ['id' => $id]);
    }

    /**
     * Update status dispensasi (disetujui / ditolak) dan catatan dosen
     */
    public static function updateStatus(int $id, string $status, ?string $catatan = null): bool
    {
        $sql = "UPDATE dispensasi SET status = :status, catatan_dosen = :catatan WHERE id_dispensasi = :id";
        $stmt = Database::query($sql, [
            'status'  => $status,
            'catatan' => $catatan !== null ? trim($catatan) : null,
            'id'      => $id
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Hapus record dispensasi
     */
    public static function delete(int $id): bool
    {
        $stmt = Database::query("DELETE FROM dispensasi WHERE id_dispensasi = :id", ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Ambil seluruh riwayat pengajuan dispensasi milik Asisten Dosen tertentu
     */
    public static function getByAsdos(int $asdosId, array $filters = []): array
    {
        $sql = "
            SELECT 
                d.*,
                p.asdos_id, p.matkul_id,
                m.nama_matkul, m.jam_mulai, m.jam_selesai,
                dosen.nama as nama_dosen, dosen.identity_number as nip_dosen
            FROM dispensasi d
            JOIN plotting p ON d.plotting_id = p.id_plotting
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            LEFT JOIN users dosen ON m.dosen_id = dosen.id_user
            WHERE p.asdos_id = :asdos_id
        ";
        $params = ['asdos_id' => $asdosId];

        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['matkul_id'])) {
            $sql .= " AND p.matkul_id = :matkul_id";
            $params['matkul_id'] = (int)$filters['matkul_id'];
        }

        $sql .= " ORDER BY d.created_at DESC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Ambil pengajuan dispensasi untuk mata kuliah yang diampu seorang Dosen
     */
    public static function getByDosen(int $dosenId, array $filters = []): array
    {
        $sql = "
            SELECT 
                d.*,
                p.asdos_id, p.matkul_id,
                m.nama_matkul, m.jam_mulai, m.jam_selesai,
                asdos.nama as nama_asdos, asdos.identity_number as npm_asdos, asdos.email as email_asdos
            FROM dispensasi d
            JOIN plotting p ON d.plotting_id = p.id_plotting
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            JOIN users asdos ON p.asdos_id = asdos.id_user
            WHERE m.dosen_id = :dosen_id
        ";
        $params = ['dosen_id' => $dosenId];

        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['matkul_id'])) {
            $sql .= " AND p.matkul_id = :matkul_id";
            $params['matkul_id'] = (int)$filters['matkul_id'];
        }

        $sql .= " ORDER BY CASE WHEN d.status = 'menunggu' THEN 0 ELSE 1 END, d.created_at DESC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Ambil seluruh data dispensasi untuk Super Admin (monitoring global)
     */
    public static function getAll(array $filters = []): array
    {
        $sql = "
            SELECT 
                d.*,
                p.asdos_id, p.matkul_id,
                m.nama_matkul, m.jam_mulai, m.jam_selesai,
                asdos.nama as nama_asdos, asdos.identity_number as npm_asdos,
                dosen.nama as nama_dosen, dosen.identity_number as nip_dosen
            FROM dispensasi d
            JOIN plotting p ON d.plotting_id = p.id_plotting
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            JOIN users asdos ON p.asdos_id = asdos.id_user
            LEFT JOIN users dosen ON m.dosen_id = dosen.id_user
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND d.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['matkul_id'])) {
            $sql .= " AND p.matkul_id = :matkul_id";
            $params['matkul_id'] = (int)$filters['matkul_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (d.nomor_surat LIKE :s1 OR asdos.nama LIKE :s2 OR asdos.identity_number LIKE :s3 OR d.tujuan_surat LIKE :s4)";
            $term = '%' . $filters['search'] . '%';
            $params['s1'] = $term;
            $params['s2'] = $term;
            $params['s3'] = $term;
            $params['s4'] = $term;
        }

        $sql .= " ORDER BY d.created_at DESC";

        return Database::fetchAll($sql, $params);
    }

    /**
     * Statistik ringkasan dispensasi untuk Asisten Dosen
     */
    public static function getMetricsByAsdos(int $asdosId): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN d.status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN d.status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
                SUM(CASE WHEN d.status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
            FROM dispensasi d
            JOIN plotting p ON d.plotting_id = p.id_plotting
            WHERE p.asdos_id = :asdos_id
        ";
        $row = Database::fetch($sql, ['asdos_id' => $asdosId]);

        return [
            'total'     => (int)($row['total'] ?? 0),
            'disetujui' => (int)($row['disetujui'] ?? 0),
            'menunggu'   => (int)($row['menunggu'] ?? 0),
            'ditolak'   => (int)($row['ditolak'] ?? 0),
        ];
    }

    /**
     * Statistik ringkasan dispensasi untuk Dosen Pengampu
     */
    public static function getMetricsByDosen(int $dosenId): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN d.status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN d.status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
                SUM(CASE WHEN d.status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
            FROM dispensasi d
            JOIN plotting p ON d.plotting_id = p.id_plotting
            JOIN mata_kuliah m ON p.matkul_id = m.id_matkul
            WHERE m.dosen_id = :dosen_id
        ";
        $row = Database::fetch($sql, ['dosen_id' => $dosenId]);

        return [
            'total'     => (int)($row['total'] ?? 0),
            'disetujui' => (int)($row['disetujui'] ?? 0),
            'menunggu'   => (int)($row['menunggu'] ?? 0),
            'ditolak'   => (int)($row['ditolak'] ?? 0),
        ];
    }

    /**
     * Statistik ringkasan seluruh dispensasi (Super Admin)
     */
    public static function getMetricsAll(): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
                SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
            FROM dispensasi
        ";
        $row = Database::fetch($sql);

        return [
            'total'     => (int)($row['total'] ?? 0),
            'disetujui' => (int)($row['disetujui'] ?? 0),
            'menunggu'   => (int)($row['menunggu'] ?? 0),
            'ditolak'   => (int)($row['ditolak'] ?? 0),
        ];
    }
}
