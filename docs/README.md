# 📘 Sistem Informasi Absensi & Penugasan Asisten Laboratorium (Absensi Lab)

Selamat datang di repositori **Absensi Lab**! Dokumentasi ini dirancang khusus untuk memandu pengembang (terutama Junior Developer) dalam memahami, mengonfigurasi, dan mengembangkan aplikasi web ini secara cepat, aman, dan terstruktur.

---

## 📌 1. Ringkasan Proyek & Tech Stack

**Absensi Lab** adalah sistem informasi manajemen internal Laboratorium Jurusan Sistem Informasi. Aplikasi ini digunakan untuk:
1. **Plotting (Penugasan) Terpadu:** Pengelolaan master mata kuliah sekaligus plotting asisten dosen (asdos) dalam satu kesatuan alur kerja terpadu.
2. **Single-Page Workspace Asdos:** Asisten dosen mengelola kehadiran langsung dari kartu mata kuliah yang diampu, melakukan pencatatan presensi live camera (kamera depan & belakang) dengan stempel *watermark timestamp server*, serta memantau riwayat verifikasi.
3. **Verifikasi & Monitoring Multi-Peran:** Verifikasi kehadiran oleh Dosen Pengampu serta pengawasan administratif penuh (*monitoring*, ubah status, dan hapus laporan) oleh Super Admin.
4. **Active/Inactive Account Guard (BR2):** Asdos atau pengguna yang dinonaktifkan tetap dapat login untuk melihat riwayat (*read-only history mode*), namun seluruh hak mutasi data (CRUD) diblokir secara otomatis di level backend dan frontend.

### 🛠️ Tech Stack & Library

| Lapisan / Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Bahasa Pemrograman** | PHP 8.1+ | Pemrograman berorientasi objek (OOP) Native MVC tanpa *heavy framework* pihak ketiga |
| **Database** | MySQL 8.0+ / MariaDB | Relational Database Management System (RDBMS) via PDO (*Prepared Statements*) |
| **Web Server** | Apache (Laragon / XAMPP) | URL Rewriting menggunakan `.htaccess` (*Front Controller Pattern*) |
| **Frontend Styling** | Tailwind CSS (CDN) | *Utility-first styling* dengan kustomisasi tema enterprise dashboard |
| **Typography & Icons** | Google Fonts (Inter) & Inline SVG | Desain modern, bersih, responsif, dan ramah pengguna |
| **Image Processing** | PHP GD Extension | Koreksi orientasi EXIF & Watermark waktu/tanggal otomatis di sisi server |
| **Security & Autoload** | PSR-4 Standard (`Core\Autoload`) | Dilengkapi CSRF Token Protection, Session Guard, Role-Based Access Control (RBAC), dan MIME File Validation |

---

## ⚙️ 2. Prasyarat Sistem (Prerequisites)

Sebelum memulai instalasi, pastikan lingkungan pengembangan lokal Anda telah memenuhi spesifikasi berikut:

1. **PHP**: Versi **8.1.0** atau lebih tinggi (ekstensi `pdo_mysql`, `gd`, `fileinfo`, `mbstring`, `openssl`, `session` harus aktif).
2. **Database Server**: **MySQL 8.0+** atau **MariaDB 10.4+**.
3. **Web Server / Local Development Environment**:
   - Direkomendasikan: **Laragon** (Windows) atau **XAMPP**.
   - Alternatif: **PHP Built-in Web Server** (`php -S localhost:8000 -t public`).
4. **Git**: Untuk *version control* (`git clone`, `git pull`, dll).
5. **Browser Modern**: Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari.

---

## 🚀 3. Panduan Setup Lokal Langkah demi Langkah

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal:

### Langkah 1: Clone Repositori
Buka terminal / Git Bash di folder web server Anda (misalnya `C:/laragon/www/` atau `C:/xampp/htdocs/`):
```bash
cd C:/laragon/www
git clone https://github.com/Adityaa1404/absensi_labb.git
cd absensi_labb
```

### Langkah 2: Setup Konfigurasi Database
Buka file konfigurasi database di [`config/database.php`](file:///c:/laragon/www/absensi_labb/config/database.php) dan sesuaikan kredensial database lokal Anda:

```php
<?php
// Konfigurasi Aplikasi & Environment
define('APP_ENV', 'development'); // 'development' atau 'production'
define('APP_DEBUG', true);        // true = tampilkan detail error, false = mode aman di production

// Konfigurasi Database MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'absensi_lab');
define('DB_USER', 'root');        // Username database MySQL lokal Anda
define('DB_PASS', '');            // Password database MySQL lokal Anda (default Laragon/XAMPP kosong)
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');
```

> [!TIP]
> Pada mode `development` dengan `APP_DEBUG = true`, sistem akan menampilkan pesan error visual lengkap beserta *stack trace* dan *code snippet* saat terjadi *unhandled exception*.

### Langkah 3: Import / Migrasi Skema Database
1. Buka phpMyAdmin (`http://localhost/phpmyadmin`) atau GUI database client favorit Anda (DBeaver, HeidiSQL, TablePlus).
2. Buat database baru dengan nama: `absensi_lab` (dengan collation `utf8mb4_unicode_ci`).
3. Import file SQL yang telah disediakan di root proyek: [`absensi_lab.sql`](file:///c:/laragon/www/absensi_labb/absensi_lab.sql).

Atau via command line (CLI):
```bash
mysql -u root -p absensi_lab < absensi_lab.sql
```

### Langkah 4: Buat Direktori Upload & Atur Hak Akses
Pastikan folder upload bukti absensi tersedia dan memiliki izin tulis (*writeable*):
- Path folder: `public/uploads/absensi/`
*(Secara default, jika folder belum ada, sistem controller akan membuatnya secara otomatis dengan permission 0755).*

### Langkah 5: Jalankan Aplikasi di Server Lokal

#### Opsi A: Menggunakan Laragon / XAMPP (Sangat Direkomendasikan)
- Jalankan service Apache & MySQL pada control panel Laragon/XAMPP.
- Buka browser dan akses URL:
  - Laragon: `http://absensi_labb.test/` atau `http://localhost/absensi_labb/`
  - XAMPP: `http://localhost/absensi_labb/`

#### Opsi B: Menggunakan PHP Built-in Server
Jika ingin menjalankan langsung dari root folder `public/`:
```bash
php -S localhost:8000 -t public
```
Lalu buka browser di: `http://localhost:8000/login`

---

## 🔑 4. Akun Pengguna Bawaan (Default Seed Credentials)

File database [`absensi_lab.sql`](file:///c:/laragon/www/absensi_labb/absensi_lab.sql) telah menyediakan beberapa akun pengujian bawaan:

| Peran (Role) | Email / Identity Number | Password Default | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@labsi.ac.id` / `admin` | *(Sesuai hash database)* | Kelola Pengguna, Mata Kuliah & Plotting Terpadu, Monitoring & Hapus Absensi |
| **Dosen** | `cozuu101@edumail.edu.rs` / `1` | *(Sesuai hash database)* | Verifikasi absensi asdos pada matkul pengampu |
| **Asdos (Aktif)** | `ola@gmail.com` / `25082010001` | *(Sesuai hash database)* | Single-Page Workspace: Absen Kamera Live, Riwayat Per Matkul |

---

## 📂 5. Struktur Folder Aplikasi

Kode sumber aplikasi diatur dengan arsitektur MVC yang rapi, ramping, dan bebas redundansi:

```text
absensi_labb/
├── .htaccess                 # Rule Apache untuk meneruskan semua request ke /public
├── composer.json             # Konfigurasi autoload PSR-4 namespace App\ dan Core\
├── config/                   # Konfigurasi aplikasi & database
│   └── database.php          # Definisi konstanta koneksi DB & mode environment
├── Core/                     # Engine inti framework MVC internal
│   ├── Autoload.php          # PSR-4 dynamic class autoloader
│   ├── Database.php          # Database Singleton & PDO query builder helper
│   ├── ErrorHandler.php      # Global error & exception handler + auto-logger
│   ├── Guard.php             # Security guard (Session, CSRF, RBAC, Active State, Idempotent URL)
│   ├── Router.php            # HTTP Router & middleware pipeline runner
│   └── Validator.php         # Server-side input validation engine
├── app/                      # Logika aplikasi (Model-View-Controller)
│   ├── Controllers/          # Pengendali logika alur request
│   │   ├── AuthController.php        # Otentikasi login, logout, & redirect role
│   │   ├── SuperAdminController.php  # CRUD User, Matkul & Plotting Terpadu, Monitoring & Hapus Absensi
│   │   └── AsdosController.php       # Single-Page Dashboard, Presensi Kamera Live & Watermark GD
│   ├── Models/               # Layer manipulasi data database
│   │   ├── User.php                  # Model tabel users (CRUD & Status Toggle)
│   │   ├── MataKuliah.php            # Model tabel mata_kuliah
│   │   ├── Plotting.php              # Model tabel plotting (Auto Sync Expired Status)
│   │   └── Absensi.php               # Model tabel absensi
│   ├── Views/                # Template presentasi antarmuka (UI)
│   │   ├── Auth/             # View formulir login
│   │   ├── SuperAdmin/       # View dashboard, users, matkul (terpadu plotting), monitoring
│   │   ├── Asdos/            # View dashboard (Single-Page Workspace Terpadu)
│   │   └── Templates/        # Reusable component (header, superadmin_bottom_nav, notifications)
│   └── logs/                 # Folder catatan error log otomatis (error.log)
├── public/                   # Public Web Root (Directory yang diekspos ke browser)
│   ├── .htaccess             # Apache rewrite ke index.php
│   ├── index.php             # Front Controller (Entry Point aplikasi & Route Definitions)
│   └── uploads/              # Folder penyimpanan upload file foto absensi
│       └── absensi/          # Foto kegiatan & foto selfie asdos ber-watermark
├── docs/                     # Dokumentasi arsitektur, domain, & database
│   ├── ARCHITECTURE.md       # Arsitektur sistem, lifecycle, dan Mermaid diagram
│   ├── DOMAIN_GLOSSARY.md    # Glosarium istilah bisnis, aturan domain (BR1-BR6)
│   └── DATABASE_OVERVIEW.md  # ERD, skema tabel, dan kamus kolom
├── absensi_lab.sql           # File dump skema & data awal database MySQL
├── PRD.md                    # Dokumen Product Requirement Document (PRD) resmi
└── design.md                 # Design System & UI Specification Guide
```

---

## 💻 6. Perintah CLI yang Sering Digunakan

### Memeriksa Sintaks PHP (Linting)
```bash
php -l app/Controllers/SuperAdminController.php
php -l app/Controllers/AsdosController.php
php -l app/Views/Asdos/dashboard.php
php -l app/Views/SuperAdmin/matkul.php
php -l public/index.php
```

### Memeriksa Error Log
```bash
# Windows PowerShell
Get-Content -Tail 50 app/logs/error.log

# Linux / Git Bash
tail -n 50 app/logs/error.log
```

---

## 📖 7. Dokumentasi Terkait

- 🏗️ [**Dokumentasi Arsitektur Sistem (`docs/ARCHITECTURE.md`)**](file:///c:/laragon/www/absensi_labb/docs/ARCHITECTURE.md) — Alur siklus hidup *request*, pola desain, dan diagram Mermaid.
- 📚 [**Glosarium Domain Bisnis (`docs/DOMAIN_GLOSSARY.md`)**](file:///c:/laragon/www/absensi_labb/docs/DOMAIN_GLOSSARY.md) — Aturan bisnis inti (BR1 hingga BR6), alur status, dan kamus variabel.
- 🗄️ [**Struktur & ERD Database (`docs/DATABASE_OVERVIEW.md`)**](file:///c:/laragon/www/absensi_labb/docs/DATABASE_OVERVIEW.md) — Diagram relasi entitas, kamus kolom, dan batasan integritas.
- 🎨 [**Design System & UI Guide (`design.md`)**](file:///c:/laragon/www/absensi_labb/design.md) — Standar estetika, palet warna, dan panduan antarmuka pengguna.
