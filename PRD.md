# PRD — Sistem Absensi & Plotting Asisten Dosen (Absensi Lab)

**Versi:** 1.0
**Tanggal:** 24 Agustus 2026
**Status:** Draft
**Teknologi:** PHP Native (MVC), MySQL/MariaDB, XAMPP

---

## 1. Ringkasan Produk

Sistem internal Laboratorium Jurusan Sistem Informasi untuk mengelola **plotting mata kuliah asisten dosen**, **pencatatan absensi pelaksanaan praktikum/pengajaran beserta bukti kegiatan (foto)** dengan **timestamp otomatis**, serta **verifikasi oleh dosen pembimbing**. Super admin bertanggung jawab memplot asdos ke mata kuliah dan dapat **mengaktifkan/menonaktifkan akun**. Asdos nonaktif tetap dapat melihat **history** absensinya, namun tidak lagi dapat membuat/mengubah/menghapus (CRUD) absensi dan bukti kegiatan.

## 2. Latar Belakang & Masalah

- Saat ini belum ada penugasan (plotting) resmi asdos ke mata kuliah tertentu.
- Pencatatan pelaksanaan praktikum/pengajaran asdos tidak terstandar (tidak ada bukti foto maupun timestamp).
- Verifikasi kehadiran/tugas asdos oleh dosen dilakukan manual.
- Tidak ada kontrol status akun; asdos yang sudah lulus/berhenti masih bisa mengubah data.

## 3. Tujuan & Metrik Keberhasilan

| Tujuan | Metrik |
|---|---|
| Plotting matkul terpusat | 100% asdos aktif memiliki penugasan dari super admin sebelum periode praktikum dimulai |
| Absensi terverifikasi | ≥ 90% absensi diverifikasi dosen ≤ 3 hari setelah diajukan |
| Jejak audit | Setiap absensi memiliki timestamp created_at/updated_at yang tidak dapat diubah user |
| Kontrol akses | Akun nonaktif 100% diblokir dari operasi CRUD, tetapi tetap bisa login-read history |

## 4. Aktor & Peran (Roles)

### 4.1 Super Admin
- Mengelola data pengguna (dosen & asdos): create, edit, **aktifkan/nonaktifkan akun (on/off)**.
- Membuat & mengelola data **mata kuliah** (nama, kode, deskripsi).
- **Memplot (assign) asdos ke mata kuliah** beserta dosen pengampu.
- Melihat seluruh absensi & verifikasi di seluruh sistem (read-only / monitoring).

### 4.2 Dosen
- Melihat daftar asdos yang diplot pada matkul yang dia pimpin.
- Memverifikasi absensi asdos: **setujui / tolak dengan catatan**.
- Melihat riwayat absensi per asdos per matkul.

### 4.3 Asisten Dosen (Asdos)
- Melihat matkul tempatnya diplot, deskripsi matkul, dan dosen pembimbing.
- Mengisi **absensi pelaksanaan** (tanggal, pertemuan, jam mulai–selesai, deskripsi tugas) + upload **foto kegiatan** (praktikum/mengajar) dan foto selfie.
- Timestamp dicatat otomatis oleh sistem saat submit.
- Melihat status verifikasi dan pesan dosen.
- Jika akun dinonaktifkan: **hanya bisa melihat history** (read-only), tidak bisa CRUD.

## 5. Ruang Lingkup Fitur

### F1 — Autentikasi & Manajemen Akun
- Login (email/identity_number + password, `password_hash`/`password_verify`).
- Registrasi asdos (dosen dibuat oleh super admin).
- **Status akun**: `users.is_active` (`1` = aktif, `0` = nonaktif).
  - Nonaktif → login berhasil, session ditandai `readonly`, banner "Akun nonaktif — mode lihat saja".
  - Semua endpoint POST/CRUD menolak request dari akun nonaktif (server-side guard, bukan hanya UI).

### F2 — Manajemen Mata Kuliah (Super Admin)
- CRUD mata kuliah: kode, nama, **deskripsi matkul**, semester/periode.
- Penetapan dosen pengampu per matkul.

### F3 — Plotting Asdos (Super Admin)
- Assign asdos → matkul (+ periode mulai–selesai).
- Satu asdos bisa diplot di >1 matkul; satu matkul bisa >1 asdos.
- Un-plot / ganti plot (riwayat penugasan tetap tersimpan untuk audit).

### F4 — Absensi & Bukti Kegiatan (Asdos)
- Hanya untuk asdos **aktif** dan terplot pada matkul terkait.
- Form absensi: tanggal, pertemuan_ke, jam_mulai, jam_selesai, deskripsi_tugas.
- Upload: foto_kegiatan (wajib), foto_selfie (opsional/wajib sesuai kebijakan lab).
- Validasi upload: ekstensi/MIME gambar (jpg/png/webp), max 2 MB, nama file acak.
- **Timestamp**: `created_at` & `updated_at` diisi server (UTC+7), tidak dapat diedit user.
- Edit/hapus absensi hanya diizinkan selama status `pending`.

### F5 — Verifikasi Dosen
- Daftar absensi masuk (status `pending`) pada matkul yang dipimpin.
- Aksi: `disetujui` / `ditolak` + `pesan_dosen` (wajib jika menolak).
- Notifikasi status berubah bagi asdos (in-app).

### F6 — History & Laporan
- Asdos: riwayat absensi pribadi (semua status), filter per matkul/periode — **selalu bisa diakses, termasuk saat nonaktif**.
- Dosen: rekap absensi per matkul/asdos, export sederhana (CSV).
- Super admin: dashboard monitoring seluruh aktivitas.

## 6. User Flow Utama

```
SUPER ADMIN : Login → Kelola user (on/off akun) → Buat matkul + deskripsi
            → Plot asdos ke matkul → Monitor

DOSEN       : Login → Lihat asdos terplot → Review absensi + foto bukti
            → Setujui/Tolak (dengan pesan)

ASDOS       : Login → Lihat plot matkul & deskripsi → Isi absensi + upload foto
            → Submit (timestamp otomatis) → Pantau status verifikasi
            → [Jika nonaktif] Login → Lihat history saja (read-only)
```

## 7. Rancangan Data (perubahan dari skema saat ini)

Skema existing (`users`, `kegiatan`, `pendaftaran_kegiatan`, `absensi`) dipertahankan/diadaptasi:

```sql
-- 1. Tambah kolom status akun & role baru
ALTER TABLE users
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER role,
  MODIFY COLUMN role ENUM('dosen','asdos','super_admin') NOT NULL;

-- 2. Tabel mata kuliah
CREATE TABLE mata_kuliah (
  id_matkul INT AUTO_INCREMENT PRIMARY KEY,
  nama_matkul VARCHAR(100) NOT NULL,
  deskripsi TEXT,
  dosen_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (dosen_id) REFERENCES users(id_user)
);

-- 3. Tabel plotting asdos
CREATE TABLE plotting (
  id_plotting INT AUTO_INCREMENT PRIMARY KEY,
  matkul_id INT NOT NULL,
  asdos_id INT NOT NULL,
  periode_mulai DATE NOT NULL,
  periode_selesai DATE NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_plot (matkul_id, asdos_id),
  FOREIGN KEY (matkul_id) REFERENCES mata_kuliah(id_matkul),
  FOREIGN KEY (asdos_id) REFERENCES users(id_user)
);

-- 4. Absensi merujuk plotting (bukan pendaftaran marketplace)
ALTER TABLE absensi
  ADD COLUMN plotting_id INT AFTER id_absensi,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;
```

> Catatan migrasi: tabel `kegiatan` & `pendaftaran_kegiatan` (fitur marketplace lama) dapat dipertahankan sebagai modul terpisah atau di-deprecate — putuskan sebelum Tahap 1 implementasi.

## 8. Aturan Bisnis (Business Rules)

1. BR1: Asdos hanya dapat membuat absensi pada matkul tempatnya diplot dan akunnya aktif.
2. BR2: Akun nonaktif: semua aksi tulis (create/update/delete/upload) ditolak di level controller + middleware; akses baca history diizinkan.
3. BR3: Timestamp (`created_at`, `updated_at`) dihasilkan server dan immutable.
4. BR4: Absensi berstatus `ditolak` atau `disetujui` tidak dapat diedit oleh asdos.
5. BR5: Verifikasi hanya boleh dilakukan dosen pengampu matkul terkait.
6. BR6: Menonaktifkan akun tidak menghapus data absensi/history.
7. BR7: Foto bukti wajib; tanpa foto, absensi tidak dapat disubmit.

## 9. Keamanan

- Prepared statements (PDO) untuk semua query.
- `htmlspecialchars()` pada semua output (XSS).
- CSRF token pada semua form.
- Session guard: `requireLogin()`, `requireRole('super_admin'|'dosen'|'asdos')`, `requireActiveAccount()`.
- Upload divalidasi server-side (MIME + ekstensi + ukuran), disimpan di luar root publik atau dengan nama acak.
- Password di-hash (`bcrypt` via `password_hash`).

## 10. Struktur Tambahan (MVC)

```
app/
├── Controllers/  AuthController, SuperAdminController, DosenController, AsdosController
├── Models/       User, MataKuliah, Plotting, Absensi
└── Views/        Auth/, SuperAdmin/(users, matkul, plotting), Dosen/(verifikasi),
                  Asdos/(absensi, history), Templates/
core/             Database, Router, Guard(middleware)
public/           index.php (front controller), assets/
```