# 📋 DBA INITIAL ASSESSMENT REPORT: DATABASE `absensi_lab`
**Mata Kuliah / Studi Kasus:** Basis Data / Sistem Informasi Absensi Laboratorium  
**Peran:** Database Administrator (DBA)  
**Target Output:** 1 Halaman DBA Initial Assessment (Pilar 01, 02, dan 03)  
**Versi Dokumen:** 1.0 (Final)  
**Tanggal:** 20 September 2026  

---

## 📌 PILAR 01: Analisis Skema Database & Relasi Antar Tabel

Sistem `absensi_lab` dirancang dengan arsitektur relasional yang memisahkan entitas master pengguna, entitas master akademik, relasi penugasan semesteran, dan log transaksi presensi kegiatan lab.

### 1. Daftar Tabel, Primary Key (PK), dan Foreign Key (FK)

| Nama Tabel | Fungsi Entitas | Primary Key (PK) | Foreign Key (FK) & Referensi | Alternate / Unique Key |
| :--- | :--- | :--- | :--- | :--- |
| **`users`** | Master pengguna (Dosen, Asdos, Super Admin) | `id_user` *(INT, Auto Increment)* | *- Tidak ada (Independen) -* | • `email` *(UK)*<br>• `identity_number` *(UK)* |
| **`mata_kuliah`** | Master mata kuliah praktikum | `id_matkul` *(INT, Auto Increment)* | • `dosen_id` $\rightarrow$ `users(id_user)` | *- Tidak ada -* |
| **`plotting`** | Junction table penugasan asdos ke matkul | `id_plotting` *(INT, Auto Increment)* | • `matkul_id` $\rightarrow$ `mata_kuliah(id_matkul)`<br>• `asdos_id` $\rightarrow$ `users(id_user)` | • `(matkul_id, asdos_id)` *(Composite UK: `uniq_plot`)* |
| **`absensi`** | Transaksi log kehadiran & bukti tugas lab | `id_absensi` *(INT, Auto Increment)* | • `plotting_id` $\rightarrow$ `plotting(id_plotting)` | *- Belum ada di skema fisik -* |

### 2. Pemetaan Relasi & Kardinalitas Antar Tabel
* **`users` (Dosen) $\xrightarrow{1 : N}$ `mata_kuliah`:** Satu dosen mengampu satu atau lebih mata kuliah praktikum. Satu mata kuliah diasuh oleh tepat 1 dosen penanggung jawab (`dosen_id`).
* **`mata_kuliah` $\xleftrightarrow{M : N}$ `users` (Asdos) via `plotting`:** Satu mata kuliah dapat memiliki banyak asisten laboratorium, dan satu asdos dapat bertugas di beberapa mata kuliah. Tabel bridge `plotting` mengurai relasi ini menjadi dua relasi $1 : N$ dengan proteksi komposit unik `(matkul_id, asdos_id)` agar tidak terjadi dobel plotting.
* **`plotting` $\xrightarrow{1 : N}$ `absensi`:** Setiap penugasan asdos memiliki banyak entri presensi pertemuan (pertemuan 1 s.d. 14). Setiap baris absensi terikat ke tepat satu ID penugasan (`plotting_id`).

---

## ⚠️ PILAR 02: Identifikasi Risiko Data, Prioritas Perlindungan, & Strategi Mitigasi

Berdasarkan tinjauan struktur fisik dan query sistem, DBA mengidentifikasi 4 risiko kritis data:

```
+---------------------------------------------------------------------------------------------------------+
| RISIKO DATA                         | TINGKAT RISIKO | DAMPAK BISNIS          | PRIORITAS PERLINDUNGAN  |
+---------------------------------------------------------------------------------------------------------+
| 1. Kehilangan Data (Cascade Delete) | KRITIS         | Hilangnya bukti honor  | Prioritas 1 (Mendesak)  |
| 2. Inkonsistensi & Dobel Presensi   | TINGGI         | Manipulasi jam kerja   | Prioritas 2 (Penting)   |
| 3. Desinkronisasi Skema & Redundansi| SEDANG         | Error sistem runtime   | Prioritas 3 (Operasional|
| 4. Orphan Files (Integritas Fisik)  | SEDANG         | Storage leak & broken  | Prioritas 4 (Maint.)    |
+---------------------------------------------------------------------------------------------------------+
```

### 1. Risiko Kehilangan Data Permanen (*Unintended Data Loss via Cascading Delete*)
* **Masalah:** Constraint FK `absensi.fk_absensi_plotting` menggunakan aksi `ON DELETE CASCADE`. Jika satu baris di tabel `plotting` dihapus, seluruh data historis kehadiran, jam kerja, dan foto bukti asdos di tabel `absensi` akan langsung terhapus permanen dari basis data.
* **Mitigasi Teknis DBA:**
  1. Ubah aksi integritas menjadi `ON DELETE RESTRICT` pada `plotting_id` agar data penugasan yang sudah memiliki presensi tidak bisa dihapus paksa.
  2. Implementasikan pola *Soft Delete* (kolom `deleted_at TIMESTAMP NULL`) di tabel `plotting` dan `absensi` alih-alih melakukan *Hard Delete* (`DELETE FROM`).
  3. Konfigurasi pencadangan otomatis harian (*Automated Daily Dump*) dan aktifkan MySQL Binary Logging (*binlog*) untuk kapabilitas *Point-In-Time Recovery (PITR)*.

### 2. Risiko Inkonsistensi & Duplikasi Presensi (*Data Inconsistency & Duplicate Insertion*)
* **Masalah:** Tidak adanya *Unique Constraint* pada pasangan kolom `(plotting_id, pertemuan_ke)`. Jika asdos mengalami *double-click submit* akibat latensi jaringan, data pertemuan yang sama dapat ter-insert ganda. Selain itu, status verifikasi (`pending`, `disetujui`, `ditolak`) berisiko inkonsisten jika terjadi update tanpa transaksi atomik.
* **Mitigasi Teknis DBA:**
  1. Pasang *Composite Unique Key* pada tabel `absensi`:
     ```sql
     ALTER TABLE absensi ADD UNIQUE KEY uq_plotting_pertemuan (plotting_id, pertemuan_ke);
     ```
  2. Wajibkan isolasi Database Transaction (`START TRANSACTION ... COMMIT / ROLLBACK`) pada level model/controller saat pembaruan status verifikasi absensi.

### 3. Risiko Desinkronisasi Skema (*Schema Drift & Unvalidated Redundancy*)
* **Masalah:** Kolom `tugas` sempat ditambahkan pada kode aplikasi dan skrip SQL dump tanpa migrasi fisik pada database server (menimbulkan SQLSTATE[42S22] Error 1054). Kolom `tugas` yang berupa format teks JSON array juga tidak memiliki constraint validasi format di level database.
* **Mitigasi Teknis DBA:**
  1. Standarisasi alur perubahan skema menggunakan *Database Migration Tool* version-controlled.
  2. Tambahkan validasi tipe data menggunakan `CHECK (JSON_VALID(tugas))` di MySQL 8.0 jika kolom tetap disimpan sebagai JSON.

### 4. Risiko Kerusakan Integritas Objek Gambar (*Orphan Physical Assets*)
* **Masalah:** Tabel `absensi` hanya menyimpan nama berkas (`foto_kegiatan`, `foto_selfie`). Database tidak menjamin keberadaan berkas fisik di direktori server `public/uploads/absensi/`. Jika berkas terhapus secara manual atau disk failure, link di database menjadi *broken reference*.
* **Mitigasi Teknis DBA:** Buat job terjadwal (cron maintenance) untuk melakukan rekonsiliasi antara entri tabel `absensi` dan filesystem.

---

## 🛡️ PILAR 03: Rekomendasi DBA, Aturan Integritas, & Langkah Perlindungan

### A. Kebutuhan Data Tambahan (Data Enhancement)
1. **Field Audit Trail Verifikasi:** Tambahkan kolom `verified_by (INT FK -> users.id_user)` dan `verified_at (TIMESTAMP)` pada tabel `absensi` untuk mencatat siapa dosen/admin yang menyetujui laporan dan waktu presisi verifikasi.
2. **Domain Range Constraint:** Tambahkan validasi batas nilai pertemuan: `CHECK (pertemuan_ke BETWEEN 1 AND 16)`.

### B. Aturan Integritas Data (Data Integrity Rules)
1. **Entity Integrity:** Mempertahankan Auto Increment Surrogate PK pada seluruh tabel serta memastikan keunikan Nomor Identitas (`NPM/NIDN`) dan `email`.
2. **Referential Integrity:** Mengubah relasi berisiko dari `CASCADE` ke `RESTRICT` pada relasi parent-child utama guna menjamin rekam jejak audit (*auditability*).
3. **Domain & Semantic Integrity:** 
   - Tanggal periode: `CHECK (periode_selesai >= periode_mulai)`.
   - Jam praktikum: `CHECK (jam_selesai > jam_mulai)`.
   - Enumerasi status: `enum('pending', 'disetujui', 'ditolak')` dengan default value `'pending'`.

### C. Langkah Perlindungan & Keamanan Database (Database Security & Hardening)
1. **Prinsip Hak Akses Terendah (*Principle of Least Privilege*):**
   * Jangan menggunakan user `root` untuk koneksi aplikasi web di `Database.php`.
   * Buat dedicated database user:
     ```sql
     CREATE USER 'app_absensi'@'localhost' IDENTIFIED BY 'KatasandiKuat_123!';
     GRANT SELECT, INSERT, UPDATE, DELETE ON absensi_lab.* TO 'app_absensi'@'localhost';
     FLUSH PRIVILEGES;
     ```
2. **Disaster Recovery & Backup Policy:**
   * **Full Backup:** Setiap malam pukul 01:00 WIB via `mysqldump` terkompresi.
   * **Retention Policy:** Penyimpanan backup 30 hari ke storage terpisah/cloud cold storage.
   * **RPO (Recovery Point Objective):** Maksimal 1 jam (dengan bantuan *binlog* aktif).
   * **RTO (Recovery Time Objective):** Maksimal 30 menit pemulihan instance.
3. **Injeksi SQL & Enkripsi:** Memastikan seluruh interaksi query tetap menggunakan *PDO Prepared Statements* dengan *Emulate Prepares = false* (sudah diterapkan dengan baik pada `Core/Database.php`), serta memastikan enkripsi kata sandi tetap menggunakan algoritma *Bcrypt*.
