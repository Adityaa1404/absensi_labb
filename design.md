# Design System & UI Specification Guide

**Project Source:** Sistem Informasi & Absensi Asdosoratorium (Absensi Asdos)  
**Version:** 1.0.0  
**Stack Framework:** HTML5, Tailwind CSS (Utility-First), Vanilla JavaScript / PHP Native MVC  
**Target Purpose:** Portable Design System Guide untuk di-import dan diterapkan pada proyek-proyek web aplikasi lainnya.

---

## 📑 Daftar Isi

1. [Filosofi Desain & Karakteristik](#1-filosofi-desain--karakteristik)
2. [Design Tokens (Warna, Tipografi, Elevasi, Radius)](#2-design-tokens)
3. [Layout & Grid System](#3-layout--grid-system)
4. [Komponen Inti (Core Components)](#4-komponen-inti-core-components)
   - [Navbar & Header Bar](#navbar--header-bar)
   - [Page Header & Breadcrumbs](#page-header--breadcrumbs)
   - [Stat & Metric Cards](#stat--metric-cards)
   - [Content Cards & Panels](#content-cards--panels)
   - [Tables & Data Lists](#tables--data-lists)
   - [Forms, Inputs & Uploaders](#forms-inputs--uploaders)
   - [Buttons & Actions](#buttons--actions)
   - [Badges & Status Pills](#badges--status-pills)
   - [Alerts & Notification Banners](#alerts--notification-banners)
   - [Empty States](#empty-states)
   - [Modal & Dialogs](#modal--dialogs)
   - [Media & Proof Image Preview](#media--proof-image-preview)
5. [Struktur Template Boilerplate (HTML / PHP)](#5-struktur-template-boilerplate)
6. [Panduan Import & Reusability ke Proyek Lain](#6-panduan-import--reusability-ke-proyek-lain)
7. [Prinsip & UX Best Practices (Do's & Don'ts)](#7-prinsip--ux-best-practices)

---

## 1. Filosofi Desain & Karakteristik

Desain sistem ini mengusung pendekatan **Modern Clean Academic & Enterprise Dashboard**:

- **High Utility & Zero Clutter:** Tata letak mengutamakan kejelasan informasi, hierarki visual yang tegas, dan kemudahan navigasi bagi pengguna.
- **Micro-SaaS Aesthetic:** Perpaduan latar belakang netral bersih (`Slate 50`), kartu putih bergaris tipis (`Slate 200`), bayangan mikro (`shadow-xs` / `shadow-sm`), serta aksen biru kobalt yang profesional.
- **Clear Status Feedback:** Indikator status (Pending, Diterima, Ditolak, Open) menggunakan palet warna semantik dengan kontras tinggi dan kontur lembut (_tinted pills_).
- **Responsive-First:** Navigasi desktop penuh yang beradaptasi menjadi _sub-nav pill bar_ yang ergonomis pada perangkat seluler.

---

## 2. Design Tokens

### A. Palet Warna (Color Palette)

| Kategori            | Nama Token      | Kode Warna (HEX)      | Tailwind Class                          | Penggunaan Utama                                     |
| :------------------ | :-------------- | :-------------------- | :-------------------------------------- | :--------------------------------------------------- |
| **Primary Brand**   | Primary Blue    | `#1867c0`             | `bg-[#1867c0]`, `text-[#1867c0]`        | Brand logo, tombol utama, tautan aktif, border fokus |
| **Primary Hover**   | Primary Hover   | `#14529d`             | `hover:bg-[#14529d]`                    | Hover state tombol utama                             |
| **Primary Active**  | Primary Active  | `#0f4482`             | `active:bg-[#0f4482]`                   | Active/click state tombol utama                      |
| **Primary Soft**    | Primary Tint    | `#eff6ff`             | `bg-blue-50`, `text-[#1867c0]`          | Tag ID (NPM/NIDN), icon badge info, upload button    |
| **Neutral Canvas**  | Body Background | `#f8fafc`             | `bg-slate-50`                           | Latar belakang halaman aplikasi                      |
| **Neutral Surface** | Surface Card    | `#ffffff`             | `bg-white`                              | Kontainer kartu, header, modal, tabel                |
| **Neutral Border**  | Border Light    | `#e2e8f0`             | `border-slate-200`                      | Border pemisah kartu, tabel, navigasi                |
| **Neutral Divider** | Border Subtle   | `#f1f5f9`             | `border-slate-100` / `divide-slate-100` | Garis pemisah internal form/list                     |
| **Text Primary**    | Heading & Dark  | `#0f172a` / `#1e293b` | `text-slate-900` / `text-slate-800`     | Judul, angka metrik, label utama                     |
| **Text Secondary**  | Subtext & Meta  | `#64748b`             | `text-slate-500` / `text-slate-600`     | Deskripsi, keterangan tanggal, placeholder           |
| **Text Muted**      | Caption & Note  | `#94a3b8`             | `text-slate-400`                        | Overline uppercase, instruksi kecil                  |

#### Semantik Status Tokens

| Status Semantik        | Background Tint | Text Color                 | Border Color                 | Makna & Penggunaan                                   |
| :--------------------- | :-------------- | :------------------------- | :--------------------------- | :--------------------------------------------------- |
| **Success / Approved** | `bg-emerald-50` | `text-emerald-700` / `800` | `border-emerald-200` / `300` | Status "DITERIMA", "DISETUJUI", "OPEN", Alert Sukses |
| **Warning / Pending**  | `bg-amber-50`   | `text-amber-700` / `800`   | `border-amber-200` / `300`   | Status "PENDING", "MENUNGGU SELEKSI", Catatan khusus |
| **Danger / Rejected**  | `bg-red-50`     | `text-red-700` / `800`     | `border-red-200` / `300`     | Status "DITOLAK", Tombol Hapus, Alert Error          |
| **Neutral / Inactive** | `bg-slate-100`  | `text-slate-600` / `700`   | `border-slate-200`           | Status "CLOSED", Tombol Batal/Kembali                |

---

### B. Tipografi (Typography)

- **Font Utama:** `'Inter', sans-serif` (Google Fonts: weights 400, 500, 600, 700)
- **Font Data / Monospace:** `ui-monospace, monospace` (`font-mono`) untuk NPM, NIDN, ID Transaksi, dan Nominal Uang (`Rp 150.000`).

| Tingkat                  | Kelas Tailwind                                                                     | Ukuran / Weight  | Contoh Penggunaan                           |
| :----------------------- | :--------------------------------------------------------------------------------- | :--------------- | :------------------------------------------ |
| **Display / Page Title** | `text-xl sm:text-2xl font-bold tracking-tight text-slate-900`                      | 20px–24px / Bold | Judul halaman utama, header banner          |
| **Card / Section Title** | `text-base sm:text-lg font-bold text-slate-800`                                    | 16px–18px / Bold | Judul kartu data, judul tabel               |
| **Item Title**           | `text-base font-bold text-slate-900`                                               | 16px / Bold      | Nama kegiatan, nama pengguna di list        |
| **Body Standard**        | `text-sm text-slate-700 leading-relaxed`                                           | 14px / Regular   | Deskripsi tugas, paragraf penjelasan        |
| **Body Compact**         | `text-xs text-slate-600`                                                           | 12px / Regular   | Deskripsi singkat, metadata, tabel body     |
| **Form Label**           | `text-xs font-semibold text-slate-700`                                             | 12px / SemiBold  | Label input field                           |
| **Overline / Category**  | `text-[10px]` atau `text-[11px] font-bold uppercase tracking-wider text-slate-400` | 10px–11px / Bold | Label statistik, header tabel, tag kategori |

---

### C. Elevasi, Border Radius & Spacing

- **Border Radius:**
  - Komponen Tombol, Input, Badge, Item List: `rounded-lg` (8px)
  - Kartu (Cards), Panels, Modal, Wrapper: `rounded-xl` (12px)
  - Auth Container (Login/Register Card): `rounded-2xl` (16px)
  - Status Pills / Badges bulat: `rounded-full` (9999px)
- **Shadows:**
  - Kartu Standar & Tombol: `shadow-xs` (`0 1px 2px 0 rgba(0, 0, 0, 0.05)`)
  - Hover / Modal Card: `shadow-md` atau `shadow-xl`
- **Layout Spacing:**
  - Jarak antar kartu: `space-y-6`
  - Grid Gap: `gap-4` atau `gap-5`
  - Card Inner Padding: `p-5 sm:p-6`

---

## 3. Layout & Grid System

```
+-------------------------------------------------------------------------------+
| Header / Navbar (Sticky, h-16, bg-white, border-b border-slate-200, z-40)     |
| [Brand Logo: LAB] [App Title & Role Badge]       [Nav Links]     [User Profile & Logout] |
+-------------------------------------------------------------------------------+
| Mobile Sub-Navigation (md:hidden, border-t border-slate-200, bg-slate-50/90)  |
+-------------------------------------------------------------------------------+
|                                                                               |
| Main Content Container (max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 flex-1)           |
|                                                                               |
|  [ Page Header Banner / Title + Action Button ]                              |
|                                                                               |
|  [ Stat Cards Grid (4 Kolom / 2 Kolom Mobile) ]                               |
|                                                                               |
|  [ Main Data Section (Table / Cards / Form Multi-column) ]                   |
|                                                                               |
+-------------------------------------------------------------------------------+
| Footer (bg-white, border-t border-slate-200, py-6, text-center, text-xs)      |
+-------------------------------------------------------------------------------+
```

### Standar Lebar Kontainer (Container Widths):

1. **Full Dashboard / Marketplace / Tables:** `max-w-7xl mx-auto`
2. **Form Profil / Multi-Card Settings:** `max-w-4xl mx-auto`
3. **Form Transaksional / Upload Absensi:** `max-w-3xl mx-auto`
4. **Form Konfirmasi / Kartu Tunggal:** `max-w-2xl mx-auto`
5. **Autentikasi (Login / Register):** `max-w-md` atau `max-w-lg mx-auto`

---

## 4. Komponen Inti (Core Components)

### Navbar & Header Bar

```html
<header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
  <div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16"
  >
    <!-- Brand / Logo -->
    <div class="flex items-center gap-3">
      <div
        class="w-9 h-9 rounded-lg bg-[#1867c0] flex items-center justify-center text-white font-bold text-sm shadow-xs"
      >
        APP
      </div>
      <div>
        <div class="flex items-center gap-2">
          <span class="text-sm font-bold text-slate-900 leading-tight"
            >Nama Aplikasi</span
          >
          <span
            class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider bg-blue-50 text-[#1867c0] border border-blue-200 rounded"
          >
            ROLE
          </span>
        </div>
        <p class="text-xs text-slate-500">Subjudul Aplikasi</p>
      </div>
    </div>

    <!-- Navigation Links (Desktop) -->
    <nav class="hidden md:flex items-center gap-1">
      <!-- Active State -->
      <a
        href="#"
        class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-[#1867c0] text-white shadow-xs flex items-center gap-1.5 transition"
      >
        <svg
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
          />
        </svg>
        <span>Dashboard</span>
      </a>
      <!-- Inactive State -->
      <a
        href="#"
        class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center gap-1.5 transition"
      >
        <svg
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
          />
        </svg>
        <span>Menu Item</span>
      </a>
    </nav>

    <!-- User Info & Logout Button -->
    <div class="flex items-center gap-3">
      <div class="hidden sm:block text-right">
        <p class="text-xs font-bold text-slate-800 leading-tight">
          Nama Pengguna
        </p>
        <p class="text-[11px] text-slate-500 font-mono">ID: 12345678</p>
      </div>
      <a
        href="#"
        class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-semibold transition flex items-center gap-1.5"
      >
        <svg
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
          />
        </svg>
        <span>Logout</span>
      </a>
    </div>
  </div>
</header>
```

---

### Page Header & Breadcrumbs

```html
<div
  class="bg-white border border-slate-200 p-5 sm:p-6 rounded-xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4"
>
  <div>
    <!-- Optional Back Link -->
    <a
      href="#"
      class="inline-flex items-center gap-1 text-xs font-semibold text-[#1867c0] hover:underline mb-1"
    >
      <svg
        class="w-3.5 h-3.5"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M10 19l-7-7m0 0l7-7m-7 7h18"
        />
      </svg>
      Kembali ke Halaman Sebelumnya
    </a>
    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
      Judul Halaman
    </h2>
    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
      Penjelasan ringkas fungsi halaman untuk membantu pengguna.
    </p>
  </div>
  <div class="shrink-0">
    <a
      href="#"
      class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#1867c0] hover:bg-[#14529d] text-white text-xs font-semibold rounded-lg transition duration-150 shadow-xs"
    >
      <svg
        class="w-4 h-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 4v16m8-8H4"
        />
      </svg>
      <span>Aksi Utama</span>
    </a>
  </div>
</div>
```

---

### Stat & Metric Cards

```html
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
  <!-- Total Pelamar (Neutral) -->
  <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
      Total Data
    </p>
    <p class="text-2xl font-bold text-slate-800 mt-1">42</p>
  </div>

  <!-- Menunggu / Pending (Amber) -->
  <div
    class="bg-white p-4 rounded-xl border border-amber-200 bg-amber-50/20 shadow-xs"
  >
    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700">
      Menunggu (Pending)
    </p>
    <p class="text-2xl font-bold text-amber-700 mt-1">12</p>
  </div>

  <!-- Diterima / Success (Emerald) -->
  <div
    class="bg-white p-4 rounded-xl border border-emerald-200 bg-emerald-50/20 shadow-xs"
  >
    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">
      Disetujui
    </p>
    <p class="text-2xl font-bold text-emerald-700 mt-1">28</p>
  </div>

  <!-- Ditolak / Danger (Red) -->
  <div
    class="bg-white p-4 rounded-xl border border-red-200 bg-red-50/20 shadow-xs"
  >
    <p class="text-[11px] font-bold uppercase tracking-wider text-red-700">
      Ditolak
    </p>
    <p class="text-2xl font-bold text-red-700 mt-1">2</p>
  </div>
</div>
```

---

### Content Cards & Panels

```html
<div
  class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden"
>
  <!-- Card Header -->
  <div
    class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50"
  >
    <div>
      <h3 class="text-base font-bold text-slate-900">Judul Bagian</h3>
      <p class="text-xs text-slate-500 mt-0.5">
        Keterangan tambahan untuk bagian ini
      </p>
    </div>
    <span class="text-xs font-semibold text-slate-500">Badge/Counter</span>
  </div>

  <!-- Card Body -->
  <div class="p-5 sm:p-6">Konten utama...</div>
</div>
```

---

### Tables & Data Lists

```html
<div class="overflow-x-auto">
  <table class="w-full text-left text-xs text-slate-700 border-collapse">
    <thead
      class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200"
    >
      <tr>
        <th class="px-5 py-3.5">Nama Item</th>
        <th class="px-5 py-3.5">Periode / Waktu</th>
        <th class="px-5 py-3.5 text-center">Jumlah</th>
        <th class="px-5 py-3.5">Nominal</th>
        <th class="px-5 py-3.5">Status</th>
        <th class="px-5 py-3.5 text-center">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <tr class="hover:bg-slate-50/80 transition">
        <td class="px-5 py-4 font-semibold text-slate-900">
          Praktikum Algoritma & Pemrograman
          <p
            class="text-xs text-slate-500 font-normal truncate max-w-sm mt-0.5"
          >
            Deskripsi singkat tugas atau kegiatan...
          </p>
        </td>
        <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-600">
          01 Sep 2026 – 30 Des 2026
        </td>
        <td
          class="px-5 py-4 text-center whitespace-nowrap font-bold text-slate-800"
        >
          3 <span class="text-[11px] font-normal text-slate-400">Org</span>
        </td>
        <td
          class="px-5 py-4 whitespace-nowrap font-mono text-xs text-[#1867c0] font-bold"
        >
          Rp 350.000
        </td>
        <td class="px-5 py-4 whitespace-nowrap">
          <span
            class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-emerald-50 text-emerald-700 border-emerald-200"
          >
            OPEN
          </span>
        </td>
        <td class="px-5 py-4 text-center whitespace-nowrap">
          <div class="flex items-center justify-center gap-1.5">
            <a
              href="#"
              class="px-2.5 py-1.5 rounded-md bg-amber-50 text-amber-700 hover:bg-amber-100 text-xs font-semibold border border-amber-200 transition"
            >
              Edit
            </a>
            <a
              href="#"
              class="px-2.5 py-1.5 rounded-md bg-red-50 text-red-600 hover:bg-red-100 text-xs font-semibold border border-red-200 transition"
            >
              Hapus
            </a>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

---

### Forms, Inputs & Uploaders

#### 1. Input Teks & Select

```html
<div>
  <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1.5">
    Nama Kegiatan <span class="text-red-500">*</span>
  </label>
  <input
    type="text"
    id="nama"
    name="nama"
    required
    class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150"
    placeholder="Masukkan nama lengkap..."
  />
</div>
```

#### 2. Textarea

```html
<div>
  <label
    for="deskripsi"
    class="block text-xs font-semibold text-slate-700 mb-1.5"
  >
    Deskripsi Pekerjaan <span class="text-red-500">*</span>
  </label>
  <textarea
    id="deskripsi"
    name="deskripsi"
    rows="3"
    required
    class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150 resize-none"
    placeholder="Tuliskan rincian..."
  ></textarea>
</div>
```

#### 3. Modern File Upload Box

```html
<div class="bg-slate-50/80 p-4 border border-slate-200 rounded-xl space-y-2">
  <div>
    <label
      for="foto_kegiatan"
      class="block text-xs font-semibold text-slate-800"
    >
      Foto Bukti Kegiatan <span class="text-red-500">*</span>
    </label>
    <p class="text-[11px] text-slate-500">
      Dokumentasi suasana pelaksanaan pekerjaan.
    </p>
  </div>

  <input
    type="file"
    id="foto_kegiatan"
    name="foto_kegiatan"
    accept="image/jpeg,image/png,image/webp"
    required
    class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1867c0] hover:file:bg-blue-100 cursor-pointer"
  />
  <p class="text-[10px] text-slate-400">Format: JPG, PNG, WEBP (Maks. 5 MB)</p>
</div>
```

---

### Buttons & Actions

```html
<!-- 1. Primary Button -->
<button
  type="submit"
  class="bg-[#1867c0] hover:bg-[#14529d] active:bg-[#0f4482] text-white font-semibold py-2 px-5 text-xs rounded-lg transition duration-150 shadow-xs flex items-center justify-center gap-1.5"
>
  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M5 13l4 4L19 7"
    />
  </svg>
  <span>Simpan Data</span>
</button>

<!-- 2. Secondary / Outline Button -->
<a
  href="#"
  class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold rounded-lg transition duration-150"
>
  Batal
</a>

<!-- 3. Success Action (e.g., Terima / Setujui) -->
<button
  type="button"
  class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs transition duration-150 flex items-center gap-1.5"
>
  <svg
    class="w-3.5 h-3.5"
    fill="none"
    stroke="currentColor"
    viewBox="0 0 24 24"
  >
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M5 13l4 4L19 7"
    />
  </svg>
  <span>Terima</span>
</button>

<!-- 4. Danger Action (e.g., Tolak / Hapus) -->
<button
  type="button"
  class="px-3.5 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-semibold rounded-lg shadow-xs transition duration-150 flex items-center gap-1.5"
>
  <svg
    class="w-3.5 h-3.5"
    fill="none"
    stroke="currentColor"
    viewBox="0 0 24 24"
  >
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M6 18L18 6M6 6l12 12"
    />
  </svg>
  <span>Tolak</span>
</button>
```

---

### Badges & Status Pills

```html
<!-- Status Success / Diterima / Open -->
<span
  class="px-2.5 py-0.5 text-[10px] font-bold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-300"
>
  DITERIMA
</span>

<!-- Status Pending / Menunggu -->
<span
  class="px-2.5 py-0.5 text-[10px] font-bold rounded-full border bg-amber-50 text-amber-700 border-amber-300"
>
  PENDING
</span>

<!-- Status Danger / Ditolak -->
<span
  class="px-2.5 py-0.5 text-[10px] font-bold rounded-full border bg-red-50 text-red-700 border-red-300"
>
  DITOLAK
</span>

<!-- Identity Badge (NPM / NIDN) -->
<span
  class="px-2 py-0.5 text-xs font-mono bg-blue-50 text-[#1867c0] border border-blue-200 rounded font-semibold"
>
  NPM: 2110511001
</span>
```

---

### Alerts & Notification Banners

```html
<!-- Alert Sukses (Emerald) -->
<div
  class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start gap-2.5"
>
  <svg
    class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"
    fill="none"
    stroke="currentColor"
    viewBox="0 0 24 24"
  >
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M5 13l4 4L19 7"
    />
  </svg>
  <div>
    <span class="font-bold">Berhasil!</span>
    <p class="mt-0.5">Operasi data telah berhasil diproses.</p>
  </div>
</div>

<!-- Alert Error (Red) -->
<div
  class="p-3.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm flex items-start gap-2.5"
>
  <svg
    class="w-4 h-4 text-red-500 shrink-0 mt-0.5"
    fill="none"
    stroke="currentColor"
    viewBox="0 0 24 24"
  >
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
    />
  </svg>
  <div>
    <span class="font-bold">Terjadi Kesalahan</span>
    <p class="mt-0.5">Mohon periksa kembali form isian Anda.</p>
  </div>
</div>
```

---

### Empty States

```html
<div
  class="p-12 text-center text-slate-500 border border-dashed border-slate-200 rounded-xl bg-white"
>
  <div
    class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400"
  >
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
      />
    </svg>
  </div>
  <p class="text-sm font-semibold text-slate-700">Belum Ada Data Tersedia</p>
  <p class="text-xs text-slate-400 mt-1">
    Data yang dibuat atau didaftarkan akan ditampilkan di sini.
  </p>
  <a
    href="#"
    class="inline-flex items-center gap-1.5 mt-4 px-3.5 py-1.5 bg-[#1867c0] hover:bg-[#14529d] text-white text-xs font-semibold rounded-lg transition duration-150 shadow-xs"
  >
    Buat Data Baru
  </a>
</div>
```

---

### Modal & Dialogs

```html
<!-- Modal Overlay Backdrop -->
<div
  id="modal-sample"
  class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
>
  <!-- Modal Card Box -->
  <div
    class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200 space-y-4"
  >
    <h4 class="text-base font-bold text-slate-900">Konfirmasi Tindakan</h4>
    <p class="text-xs text-slate-500">
      Apakah Anda yakin ingin melakukan tindakan ini? Tindakan ini tidak dapat
      dibatalkan.
    </p>

    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
      <button
        type="button"
        class="px-3.5 py-1.5 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-100 transition"
      >
        Batal
      </button>
      <button
        type="submit"
        class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow-xs transition"
      >
        Konfirmasi
      </button>
    </div>
  </div>
</div>
```

---

### Media & Proof Image Preview

```html
<div>
  <span
    class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2"
    >Foto Dokumentasi</span
  >
  <div
    class="aspect-video bg-slate-100 rounded-lg overflow-hidden border border-slate-200 flex items-center justify-center relative group"
  >
    <img
      src="path/to/image.jpg"
      alt="Foto Dokumentasi"
      class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
      onerror="this.onerror=null; this.src='https://placehold.co/600x400/f8fafc/94a3b8?text=Foto+Tidak+Ditemukan';"
    />
  </div>
</div>
```

---

## 5. Struktur Template Boilerplate

Berikut adalah struktur dasar kerangka layout (`Header` dan `Footer`) yang dapat disalin ke dalam template proyek Anda:

### `Header.php` / `Layout.html` (Bagian Atas)

```html
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?? 'Panel Aplikasi' ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: "Inter", sans-serif;
      }
    </style>
  </head>
  <body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased">
    <!-- Header / Navbar -->
    <header
      class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs"
    >
      <!-- (Isi Navbar Sesuai Komponen di Atas) -->
    </header>

    <!-- Main Content Container -->
    <main
      class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 space-y-6"
    ></main>
  </body>
</html>
```

### `Footer.php` (Bagian Bawah)

```html
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 font-medium">
            &copy; <?= date('Y') ?> Nama Organisasi / Sistem Anda. All rights reserved.
        </div>
    </footer>
</body>
</html>
```

---

## 6. Panduan Import & Reusability ke Proyek Lain

Jika Anda ingin mengimpor atau menggunakan desain sistem ini ke proyek baru:

1. **Sertakan Dependensi Esensial:**
   - CDN Tailwind CSS: `<script src="https://cdn.tailwindcss.com"></script>`
   - Font Inter: `<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">`
   - Heroicons (SVG inline yang ringan dan tajam).
2. **Kustomisasi Brand Color (Opsional):**
   - Jika proyek baru memiliki brand color yang berbeda (misal warna ungu `#7c3aed` atau hijau `#059669`), Anda cukup mengganti hex `#1867c0` dengan warna brand proyek Anda.
3. **Standarisasi Kelas Kartu & Input:**
   - Semua kartu menggunakan: `bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-xs`
   - Semua form input menggunakan: `w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-[#1867c0] focus:ring-2 focus:ring-[#1867c0]/20 transition duration-150`
   - Semua tombol submit utama menggunakan: `bg-[#1867c0] hover:bg-[#14529d] text-white font-semibold py-2 px-5 text-xs rounded-lg transition duration-150 shadow-xs`

---

## 7. Prinsip & UX Best Practices

### ✅ DO:

1. **Gunakan Elemen Semantik Status:** Selalu padukan warna badge dengan label teks yang jelas (misal teks "DITERIMA" dengan background hijau muda, bukan hanya titik warna).
2. **Gunakan Font Monospace untuk Data Teknis:** Selalu gunakan `font-mono` untuk NIM/NPM, NIDN, Kode Transaksi, dan Nominal Angka agar mudah dipindai mata.
3. **Empty State Informatif:** Jangan biarkan halaman kosong jika belum ada data. Selalu sediakan ilustrasi ikon sederhana, pesan penjelasan, dan tombol pemicu aksi (_call to action_).
4. **Sediakan Image Fallback:** Selalu sediakan atribut `onerror` untuk gambar unggahan agar tata letak tidak rusak jika URL gambar gagal dimuat.

### ❌ DON'T:

1. **Hindari Border Terlalu Tebal/Gelap:** Jangan gunakan `border-black` atau border lebih dari 1px kecuali untuk elemen avatar/fokus spesifik.
2. **Hindari Shadow Terlalu Pekat:** Gunakan `shadow-xs` atau `shadow-sm` halus agar antarmuka tetap terlihat bersih dan modern.
3. **Hindari Layout Terlalu Padat (_Crowded_):** Selalu berikan ruang bernapas dengan `p-5 sm:p-6` dan jarak vertikal antar kartu `space-y-6`.
