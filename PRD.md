# PRD — Sistem Absensi & Plotting Asisten Dosen (Absensi Lab)

**Versi:** 1.2  
**Tanggal:** 30 Agustus 2026  
**Status:** Implemented & Verified  
**Teknologi:** PHP Native (MVC), MySQL/MariaDB, Tailwind CSS  

---

## 1. Ringkasan Produk

Sistem internal Laboratorium Jurusan Sistem Informasi untuk mengelola **plotting mata kuliah asisten dosen**, **pencatatan absensi pelaksanaan praktikum/pengajaran beserta bukti kegiatan (kamera live)** dengan **watermark & timestamp server otomatis**, serta **verifikasi oleh dosen pembimbing & monitoring Super Admin**. Super admin bertanggung jawab mengelola master mata kuliah terpadu dengan penugasan plotting, mengelola akun pengguna, dan dapat **mengaktifkan/menonaktifkan akun**. Asdos nonaktif tetap dapat melihat **history** absensinya, namun diblokir dari seluruh aksi mutasi data (CRUD) di level backend dan frontend.

---

## 2. Latar Belakang & Masalah

- Penugasan (plotting) asdos ke mata kuliah praktikum perlu terintegrasi langsung dengan data mata kuliah.
- Pencatatan pelaksanaan praktikum/pengajaran asdos membutuhkan bukti otentik kamera langsung (*live camera trigger*) dengan *server watermark* waktu nyata WIB guna mencegah pemalsuan foto galeri.
- Verifikasi kehadiran dan evaluasi asdos dilakukan langsung oleh dosen pengampu, disertai hak pengawasan administratif oleh Super Admin.
- Kontrol status akun: Asdos yang telah selesai masa pengabdian dapat dinonaktifkan dengan aman tanpa kehilangan rekam jejak audit (*read-only history mode*).

---

## 3. Tujuan & Metrik Keberhasilan

| Tujuan | Metrik |
|---|---|
| Plotting matkul terpadu | 100% asdos aktif memiliki penugasan kontekstual per mata kuliah dari super admin sebelum periode praktikum dimulai |
| Absensi terverifikasi | Presensi diverifikasi dosen pengampu atau dimonitor Super Admin secara real-time |
| Jejak audit | Setiap absensi memiliki timestamp `created_at`/`updated_at` dan foto ber-watermark permanen dari server |
| Kontrol akses | Akun nonaktif 100% diblokir dari operasi CRUD, tetapi tetap bisa login melihat history (*read-only*) |

---

## 4. Aktor & Peran (Roles)

### 4.1 Super Admin
- Mengelola data pengguna (dosen, asdos, super admin): create, edit, **aktifkan/nonaktifkan akun (on/off)**.
- Mengelola **Mata Kuliah & Plotting Asdos Terpadu**: Buat matkul, tentukan dosen pengampu, buat/kelola plotting asdos langsung via modal interaktif per mata kuliah.
- **Monitoring Absensi**: Memantau seluruh aktivitas kehadiran, mengubah status verifikasi (*override*), dan menghapus catatan absensi yang keliru/batal (*delete action*).

### 4.2 Dosen
- Melihat daftar asdos yang diplot pada matkul yang dipimpin.
- Memverifikasi absensi asdos: **setujui / tolak dengan catatan (pesan dosen)**.
- Melihat riwayat absensi per asdos per matkul.

### 4.3 Asisten Dosen (Asdos)
- **Single-Page Workspace**: Seluruh aktivitas dipusatkan di halaman Dashboard utama.
- Melihat kartu-kartu mata kuliah praktikum yang diampu beserta dosen pembimbing dan ringkasan pertemuan.
- **Isi Presensi Praktikum**: Mengisi form tanggal, jam, dan deskripsi tugas + ambil **Foto Kegiatan (kamera belakang)** dan **Foto Selfie (kamera depan)** langsung via *native camera capture*. Foto distempel watermark tanggal & jam otomatis oleh server (PHP GD).
- **Riwayat Absensi**: Meninjau riwayat kehadiran per mata kuliah, status verifikasi, catatan dosen, dan lightbox foto bukti.
- Jika akun dinonaktifkan: Otomatis masuk **Mode Lihat Saja** (bisa login dan lihat riwayat, namun seluruh tombol dan endpoint mutasi data diblokir).

---

## 5. Ruang Lingkup Fitur

### F1 — Autentikasi & Manajemen Akun
- Login multi-identitas (`email` atau `identity_number` + password `bcrypt`).
- **Status akun**: `users.is_active` (`1` = aktif, `0` = nonaktif).
  - Nonaktif $\rightarrow$ login berhasil, session mode `readonly`, badge *"Akun Nonaktif — mode lihat saja"*.
  - Seluruh endpoint POST/CRUD menolak request dari akun nonaktif via `Guard::requireActiveAccount()`.

### F2 — Manajemen Mata Kuliah & Plotting Terpadu (Super Admin)
- CRUD master mata kuliah dan penetapan dosen pengampu.
- **Integrated Plotting Modal**: Tombol kelola plotting dan buat plotting baru tersemat langsung pada kartu mata kuliah terkait di `/superadmin/matkul`.
- Sinkronisasi otomatis status expired jika melewati `periode_selesai`.

### F3 — Single-Page Workspace & Presensi Kamera Live (Asdos)
- Antarmuka satu halaman berbasis kartu mata kuliah di `/asdos/dashboard`.
- Formulir modal presensi dengan matkul terkunci otomatis & nomor pertemuan otomatis terisi (*auto-increment*).
- Pemicu kamera native ganda (`capture="environment"` untuk suasana lab dan `capture="user"` untuk selfie asdos).
- Watermark waktu server otomatis menggunakan pustaka PHP GD.

### F4 — Verifikasi & Monitoring Laporan
- Peninjauan kehadiran oleh Dosen Pengampu (`disetujui` / `ditolak` + `pesan_dosen`).
- Pengawasan terpusat oleh Super Admin di `/superadmin/monitoring` dengan filter multi-kriteria, fitur ubah status verifikasi cepat, dan fitur hapus absensi.

---

## 6. User Flow Utama

```
SUPER ADMIN : Login → Kelola user (on/off akun) 
            → Menu Matkul & Plotting Terpadu (Buat matkul & plot asdos langsung dari kartu matkul)
            → Monitoring Absensi (Tinjau, ubah status, atau hapus catatan absensi)

DOSEN       : Login → Lihat asdos terplot → Review absensi + foto bukti
            → Setujui / Tolak (dengan catatan pesan dosen)

ASDOS       : Login → Single-Page Workspace Dashboard (Kartu Matkul yang Diampu)
            → Klik [Absen] → Form modal terkunci → Ambil foto kamera live (kegiatan + selfie)
            → Submit (watermark & timestamp server otomatis)
            → Klik [Riwayat] → Tinjau kehadiran per matkul & status verifikasi
            → [Jika Nonaktif] Login → Masuk Mode Lihat Saja (hanya bisa lihat riwayat)
```

---

## 7. Aturan Bisnis (Business Rules Reference)

1. **BR1 (Plotting Ownership Guard):** Asdos hanya dapat membuat absensi pada mata kuliah tempat dirinya diplot aktif oleh Super Admin.
2. **BR2 (Inactive Account Write-Protection):** Akun nonaktif (`is_active = 0`) diblokir dari seluruh aksi tulis/mutasi data di level middleware, controller, dan antarmuka JavaScript.
3. **BR3 (Server-Side Immutable Timestamps):** Kolom waktu `created_at` dan `updated_at` dihasilkan murni oleh server/database (zona waktu `Asia/Jakarta`).
4. **BR4 (Verification Lock):** Absensi yang telah diverifikasi (`disetujui` atau `ditolak`) tidak dapat diubah lagi oleh asdos.
5. **BR5 (Verification & Management Authority):** Verifikasi dilakukan oleh Dosen Pengampu mata kuliah terkait. Super Admin memiliki wewenang administratif penuh memonitor, mengubah status, serta menghapus absensi.
6. **BR6 (Audit Preservation):** Penonaktifan akun atau berakhirnya masa plotting tidak menghapus data historis absensi.
7. **BR7 (Mandatory Camera Trigger & Server Watermark):** Submit absensi wajib menyertakan 2 foto kamera live yang distempel watermark waktu server permanen melalui PHP GD.

---

## 8. Keamanan Sistem

- 100% Prepared statements (PDO) untuk semua query database (anti-SQL Injection).
- Sanitasi `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` pada seluruh output presentasi (anti-XSS).
- CSRF token validation pada setiap transaksi POST formulir.
- Session Guard berlapis: `Guard::requireRole()`, `Guard::requireActiveAccount()`, `Guard::verifyCsrf()`.
- Validasi MIME Type asli melalui PHP `finfo` (bukan hanya ekstensi file), batas ukuran maksimal 2 MB, dan pengacakan nama file simpanan.
- Password di-hash menggunakan algoritma `bcrypt` (`PASSWORD_DEFAULT`).