# 📚 Glosarium Domain Bisnis & Kamus Istilah (DOMAIN_GLOSSARY.md)

Dokumen ini memuat daftar istilah bisnis, domain akademik laboratorium, konvensi penamaan variabel, modul, dan kolom database yang digunakan di seluruh codebase **Absensi Lab**. Glosarium ini bertujuan memberikan konteks domain yang jelas bagi pengembang baru agar tidak terjadi salah tafsir saat membaca atau menulis kode.

---

## 👥 1. Peran Pengguna (Actors & Roles)

| Istilah Kode / Nilai | Nama Domain Bisnis | Definisi & Hak Akses | Konteks dalam Kode |
| :--- | :--- | :--- | :--- |
| `super_admin` | **Super Admin / Admin Lab** | Administrator sistem pengelola laboratorium yang memiliki wewenang penuh mengelola akun pengguna, master data mata kuliah, plotting penugasan asdos, memonitor seluruh absensi, serta memiliki hak akses overriding untuk mengubah status verifikasi absensi (`pending`, `disetujui`, `ditolak`). | Disimpan di kolom `users.role = 'super_admin'`. Rute dilindungi middleware `super_admin`. |
| `dosen` | **Dosen Pengampu / Pembimbing** | Tenaga pendidik yang memimpin mata kuliah praktikum tertentu dan bertugas meninjau serta memverifikasi (menyetujui/menolak) laporan absensi yang diajukan oleh asisten dosen. | Disimpan di kolom `users.role = 'dosen'`, berelasi di `mata_kuliah.dosen_id`. |
| `asdos` | **Asisten Dosen (Asdos)** | Mahasiswa yang diplot oleh Super Admin untuk membantu pelaksanaan praktikum/kegiatan belajar mengajar di laboratorium, bertugas mencatat absensi dan mengunggah bukti foto pelaksanaan tugas. | Disimpan di kolom `users.role = 'asdos'`, berelasi di `plotting.asdos_id`. |

---

## 🏛️ 2. Istilah Inti Domain Bisnis (Core Business Terms)

### A. Plotting (Penugasan Asdos)
* **Definisi:** Entitas relasi penugasan resmi yang menghubungkan seorang Asisten Dosen (`asdos_id`) ke satu Mata Kuliah tertentu (`matkul_id`) untuk rentang periode semester tertentu.
* **Variabel & Database:** Tabel `plotting`, kolom `id_plotting`, `periode_mulai`, `periode_selesai`, `is_active`.
* **Karakteristik:**
  - Satu asdos dapat diplot ke beberapa mata kuliah berbeda (*many-to-many*).
  - Satu mata kuliah dapat memiliki lebih dari satu asdos.
  - Terdapat *unique constraint* `uniq_plot(matkul_id, asdos_id)` agar asdos tidak terduplikasi pada mata kuliah yang sama.
  - Penugasan memiliki masa berlaku: otomatis berstatus nonaktif jika tanggal saat ini (`CURDATE()`) telah melewati `periode_selesai`.
  - Dikelola terpusat oleh Super Admin langsung dari menu **Mata Kuliah & Plotting Asdos** dengan modal interaktif per mata kuliah.

### B. Mata Kuliah (Matkul)
* **Definisi:** Subjek atau praktikum laboratorium yang diselenggarakan dalam periode perkuliahan, dipimpin oleh 1 orang dosen pengampu.
* **Variabel & Database:** Tabel `mata_kuliah`, kolom `id_matkul`, `nama_matkul`, `deskripsi`, `dosen_id`.

### C. Absensi (Pencatatan Kehadiran & Kegiatan)
* **Definisi:** Laporan pelaksanaan kegiatan/tugas praktikum harian yang diinput oleh Asdos sebagai bukti kerja di lab.
* **Variabel & Database:** Tabel `absensi`, kolom `id_absensi`, `plotting_id`, `tanggal`, `pertemuan_ke`, `jam_mulai`, `jam_selesai`, `deskripsi_tugas`.
* **Karakteristik:**
  - Terikat langsung ke entitas penugasan (`plotting_id`), bukan langsung ke user atau matkul.
  - Memiliki *immutable timestamps* (`created_at` dan `updated_at`) yang di-generate langsung oleh server/DB untuk keperluan audit.

### D. Bukti Pelaksanaan (Foto Kegiatan & Selfie)
* **Definisi:** Berkas citra otentik yang wajib diambil langsung dari kamera (*in-app live camera capture*) oleh asdos saat submit absensi untuk membuktikan keberadaan dan pelaksanaan praktikum.
* **Variabel & Database:** `foto_kegiatan` (foto suasana lab/praktikum via kamera belakang), `foto_selfie` (foto kehadiran asdos di lokasi via kamera depan).
* **Standar Keamanan:** Wajib ditangkap langsung via WebRTC MediaDevices tanpa akses memilih dari galeri file, disematkan *Dynamic Canvas Watermark* (stempel waktu real-time WIB, identitas asdos, nama matkul), validasi MIME type asli (JPG/PNG/WEBP), batas ukuran maksimum 2 MB, dan nama file diacak (*random hash*) saat disimpan ke folder `public/uploads/absensi/`.

---

## 🚦 3. Status, State, & Nilai Khusus (Flags & Enums)

### A. Status Akun Pengguna (`is_active` pada `users`)
| Nilai | Status | Dampak Perilaku Sistem (Behavior) |
| :--- | :--- | :--- |
| `1` | **Aktif** | Pengguna dapat login, mengakses menu navigasi penuh, dan melakukan seluruh operasi simpan, ubah, dan hapus data (Full CRUD). |
| `0` | **Nonaktif** | Pengguna tetap diizinkan login dan melihat riwayat data masa lalunya (*read-only history mode*), namun seluruh *request* mutasi data (POST/CRUD) diblokir di level middleware `active` dan controller (*BR2*). |

### B. Status Penugasan Plotting (`is_active` pada `plotting`)
| Nilai | Status | Dampak Perilaku Sistem |
| :--- | :--- | :--- |
| `1` | **Penugasan Aktif** | Asdos dapat memilih mata kuliah ini di formulir pengisian absensi baru. |
| `0` | **Penugasan Berakhir / Selesai** | Penugasan telah kedaluwarsa atau dinonaktifkan oleh Super Admin. Asdos tidak lagi dapat mengisi absensi baru untuk matkul ini, namun riwayat lama tetap tersimpan untuk keperluan audit (*audit trail*). |

### C. Status Verifikasi Absensi (`status_verifikasi` pada `absensi`)
| Nilai Enum | Status Label | Makna & Alur Bisnis |
| :--- | :--- | :--- |
| `'pending'` | **Menunggu Verifikasi** | Status awal saat absensi baru saja disubmit oleh Asdos. Data pada status ini masih dapat diubah/dihapus oleh asdos terkait (*BR4*). |
| `'disetujui'` | **Disetujui (Approved)** | Dosen pengampu telah memeriksa dan menyetujui laporan absensi. Data terkunci secara permanen dan tidak dapat diedit/dihapus lagi oleh Asdos. |
| `'ditolak'` | **Ditolak (Rejected)** | Dosen pengampu menolak laporan absensi karena ketidaksesuaian data/bukti. Wajib disertai catatan pada `pesan_dosen`. Data terkunci dari perubahan oleh Asdos. |

---

## 🔍 4. Variabel Identitas & Istilah Teknis Kode

| Variabel / Kolom | Istilah Lengkap | Penjelasan |
| :--- | :--- | :--- |
| `identity_number` | **Nomor Identitas Resmi** | Kolom unik fleksibel yang menampung **NPM** (Nomor Pokok Mahasiswa) jika rolenya `asdos`, **NIDN/NIP** jika rolenya `dosen`, atau `admin` jika `super_admin`. |
| `pesan_dosen` | **Catatan / Evaluasi Dosen** | Pesan umpan balik tekstual dari dosen pengampu saat memverifikasi absensi (wajib diisi terutama jika status ditolak). |
| `pertemuan_ke` | **Pertemuan Perkuliahan** | Angka urutan sesi pertemuan praktikum (misal: 1, 2, ..., 14). |
| `csrf_token` | **Cross-Site Request Forgery Token** | Token kriptografis acak 32-byte yang disimpan di sesi untuk memvalidasi keaslian *form submission*. |
| `flash` | **Flash Message Session** | Notifikasi sementara satu kali tayang bertipe `success`, `error`, `warning`, atau `info` yang dihapus dari sesi setelah ditampilkan ke pengguna. |

---

## 📜 5. Aturan Bisnis Domain (Business Rules Reference)

Dalam kode sumber, aturan bisnis didefinisikan dengan kode prefiks **BR** (sesuai dokumen PRD):

* **BR1 (Plotting Ownership Guard):** Asdos hanya dapat mengisi absensi pada mata kuliah tempat dirinya diplot aktif oleh Super Admin. Sistem memvalidasi kepemilikan relasi secara ketat pada `Plotting::findActiveForAsdos()`.
* **BR2 (Inactive Account Write-Protection):** Menonaktifkan akun user (`is_active = 0`) tidak menghapus data historis, namun langsung memblokir hak tulis/mutasi data pada level controller dan middleware `Guard::requireActiveAccount()`.
* **BR3 (Server-Side Immutable Timestamps):** Kolom waktu `created_at` dan `updated_at` dihasilkan dan dikelola otomatis oleh server/database (zona waktu `Asia/Jakarta`) dan tidak dapat dimanipulasi dari input formulir.
* **BR4 (Verification Lock):** Absensi yang telah memiliki status `disetujui` atau `ditolak` tidak dapat diubah atau dihapus oleh asisten dosen.
* **BR5 (Verification & Management Authority):** Verifikasi absensi dilakukan oleh Dosen Pengampu mata kuliah terkait, serta dapat dikelola penuh (ubah status verifikasi maupun hapus data laporan) langsung oleh Super Admin melalui menu Monitoring Absensi untuk keperluan administratif/koreksi.
* **BR6 (Audit Preservation):** Penonaktifan akun atau berakhirnya masa plotting tidak boleh menghapus (*hard delete*) data riwayat absensi.
* **BR7 (Mandatory Camera Trigger & Server-Side Watermark):** Submit absensi baru wajib menyertakan 2 bukti foto (foto kegiatan dan foto selfie) yang diambil langsung melalui pemicu kamera perangkat (`capture="environment"` dan `capture="user"`). Foto diproses dan distempel watermark waktu murni (*Time & Date*) di sisi server menggunakan PHP GD sebelum disimpan permanen. Sistem menolak penyimpanan data jika foto tidak lengkap atau tidak valid.
