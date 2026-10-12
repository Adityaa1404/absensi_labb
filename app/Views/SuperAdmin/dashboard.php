<?php
// Fallback & Dokumentasi Variabel dari SuperAdminController
$currentUser      = $currentUser ?? \Core\Guard::user();
$stats            = $stats ?? [
    'total_users'    => 0,
    'total_matkul'   => 0,
    'total_plotting' => 0,
];
$calendarAbsensi  = $calendarAbsensi ?? [];
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin — Absensi Asdos</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FullCalendar v6 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* FullCalendar Custom Theme Tuning (High Contrast, Crisp & Clean) */
        :root {
            --fc-border-color: #e2e8f0;
            --fc-page-bg-color: #ffffff;
            --fc-neutral-bg-color: #f8fafc;
            --fc-today-bg-color: transparent;
            --fc-highlight-color: rgba(24, 103, 192, 0.06);
            --fc-event-border-color: transparent;
            --fc-event-text-color: #ffffff;
        }

        .fc {
            font-family: inherit;
        }

        .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem !important;
            border-radius: 0.75rem;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .fc .fc-toolbar-title {
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .fc .fc-button {
            font-size: 0.725rem !important;
            font-weight: 600 !important;
            border-radius: 0.5rem !important;
            padding: 0.35rem 0.65rem !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            color: #1e293b !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
            transition: all 0.15s ease-in-out;
            text-transform: capitalize !important;
        }

        .fc .fc-button:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #1867c0 !important;
            border-color: #1867c0 !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px 0 rgba(24, 103, 192, 0.3) !important;
        }

        .fc .fc-button-primary:focus {
            box-shadow: 0 0 0 3px rgba(24, 103, 192, 0.25) !important;
        }

        .fc .fc-button-group {
            gap: 0.25rem;
        }

        .fc .fc-button-group>.fc-button {
            border-radius: 0.5rem !important;
        }

        .fc .fc-col-header-cell-cushion {
            font-size: 0.725rem;
            font-weight: 700;
            color: #334155;
            padding: 0.5rem 0.25rem !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Posisi Nomor Tanggal (Top-Right Rapi & Jelas) */
        .fc .fc-daygrid-day-top {
            justify-content: flex-end;
            padding: 0.3rem 0.4rem 0.1rem 0;
        }

        .fc .fc-daygrid-day-number {
            font-size: 0.8rem;
            font-weight: 700;
            color: #1e293b;
            /* Teks Gelap Jelas */
            min-width: 1.6rem;
            height: 1.6rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            transition: all 0.15s ease;
        }

        /* Hari di Luar Bulan Aktif (Sedikit Redup) */
        .fc .fc-day-other .fc-daygrid-day-number {
            color: #94a3b8 !important;
            font-weight: 500;
        }

        /* 1. Hari Ini (Saat TIDAK sedang dipilih) */
        .fc .fc-day-today:not(.fc-day-selected-custom) .fc-daygrid-day-number {
            background-color: #dbeafe !important;
            color: #1867c0 !important;
            border: 1.5px solid #3b82f6 !important;
            font-weight: 700;
        }

        /* 2. Hari yang Sedang DIPILIH (Aktif & Menonjol) */
        .fc .fc-day-selected-custom {
            background-color: #f0f7ff !important;
        }

        .fc .fc-day-selected-custom .fc-daygrid-day-number {
            background-color: #1867c0 !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(24, 103, 192, 0.4) !important;
            transform: scale(1.05);
        }

        .fc .fc-daygrid-day {
            cursor: pointer;
            transition: background-color 0.12s ease;
        }

        .fc .fc-daygrid-day:hover:not(.fc-day-selected-custom) {
            background-color: #f8fafc;
        }

        /* ========================================================================= */
        /* STYLING BLOK EVENT KHUSUS TAMPILAN GRID BULAN & MINGGU (SOLID & TEGAS)   */
        /* ========================================================================= */
        .fc-daygrid-event,
        .fc-timegrid-event {
            border-radius: 0.375rem !important;
            padding: 2.5px 5px !important;
            margin: 1.5px 2px !important;
            border: none !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
            transition: transform 0.12s ease, box-shadow 0.12s ease !important;
        }

        .fc-daygrid-event:hover,
        .fc-timegrid-event:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15) !important;
            filter: brightness(1.05) !important;
        }

        .fc-daygrid-event .fc-event-main,
        .fc-daygrid-event .fc-event-title,
        .fc-timegrid-event .fc-event-main,
        .fc-timegrid-event .fc-event-title {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 0.725rem !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* 1. Absensi Disetujui (Solid Emerald Green pada Grid) */
        .fc-daygrid-event.fc-event-disetujui,
        .fc-timegrid-event.fc-event-disetujui {
            background-color: #059669 !important;
            border-color: #059669 !important;
        }

        /* 2. Absensi Pending / Menunggu Review (Solid Dark Amber pada Grid) */
        .fc-daygrid-event.fc-event-pending,
        .fc-timegrid-event.fc-event-pending {
            background-color: #d97706 !important;
            border-color: #d97706 !important;
        }

        /* 3. Absensi Ditolak (Solid Crimson Red pada Grid) */
        .fc-daygrid-event.fc-event-ditolak,
        .fc-timegrid-event.fc-event-ditolak {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
        }

        .fc .fc-more-link {
            font-size: 0.7rem;
            font-weight: 700;
            color: #1867c0;
            padding: 1px 4px;
        }

        .fc-theme-standard th,
        .fc-theme-standard td {
            border-color: #e2e8f0;
        }

        /* ========================================================================= */
        /* KHUSUS VIEW AGENDA (LIST VIEW): SUPER CLEAN, LATAR PUTIH, DOT WARNA      */
        /* ========================================================================= */
        .fc-list {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            overflow: hidden !important;
            background-color: #ffffff !important;
        }

        .fc-list-table {
            border-collapse: separate !important;
        }

        .fc-list-day-cushion {
            background-color: #f8fafc !important;
            padding: 0.6rem 1rem !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            color: #334155 !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .fc-list-day-text {
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        .fc-list-day-side-text {
            font-weight: 600 !important;
            color: #64748b !important;
        }

        .fc-list-event {
            background-color: #ffffff !important;
            transition: background-color 0.15s ease !important;
            cursor: pointer !important;
        }

        .fc-list-event td {
            background-color: transparent !important;
            border-color: #f1f5f9 !important;
            padding: 0.65rem 1rem !important;
        }

        .fc-list-event:hover td {
            background-color: #f8fafc !important;
        }

        .fc-list-event-time {
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            color: #64748b !important;
            white-space: nowrap !important;
        }

        .fc-list-event-title,
        .fc-list-event-title a {
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            text-decoration: none !important;
            transition: color 0.15s ease !important;
        }

        .fc-list-event:hover .fc-list-event-title,
        .fc-list-event:hover .fc-list-event-title a {
            color: #1867c0 !important;
        }

        .fc-list-event-dot {
            border-width: 5px !important;
            border-radius: 9999px !important;
        }

        /* Warna Dot Lingkaran pada View Agenda */
        .fc-list-event.fc-event-disetujui .fc-list-event-dot {
            border-color: #059669 !important;
        }

        .fc-list-event.fc-event-pending .fc-list-event-dot {
            border-color: #d97706 !important;
        }

        .fc-list-event.fc-event-ditolak .fc-list-event-dot {
            border-color: #dc2626 !important;
        }

        .fc-list-empty {
            background-color: #ffffff !important;
            padding: 3rem 1rem !important;
            text-align: center !important;
            color: #94a3b8 !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
        }
    </style>
</head>

<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased">

    <!-- Top Popup Notifications (Auto-dismiss 4 detik) -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Navbar (Unified Desktop Navbar) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_header.php'; ?>

    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Main Content Container -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

            <!-- Top App Bar / Greeting Banner (Optime-Style) -->
            <div class="bg-white border border-slate-200/90 p-5 sm:p-6 rounded-2xl shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Selamat Datang Kembali, <?= htmlspecialchars(explode(' ', trim($currentUser['nama'] ?? 'Admin'))[0], ENT_QUOTES, 'UTF-8') ?>! 👋
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau absensi praktikum laboratorium, alokasi penugasan asdos, dan arsip persuratan resmi.</p>
                </div>

                
            </div>

            <!-- Optime 4-Stat Cards Row (Clean Pastel Icon Squircles + Micro-Trend Pills) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">

                <!-- 1. Card Total Pengguna -->
                <a href="<?= \Core\Guard::url('/superadmin/users') ?>"
                    class="group bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:border-[#1867c0] hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 block cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-[#1867c0] border border-blue-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500 truncate">Total Pengguna</p>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?= number_format($stats['total_users']) ?></p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span>Aktif</span>
                        </span>
                    </div>
                </a>

                <!-- 2. Card Total Mata Kuliah -->
                <a href="<?= \Core\Guard::url('/superadmin/matkul') ?>"
                    class="group bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:border-indigo-400 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 block cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500 truncate">Mata Kuliah</p>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?= number_format($stats['total_matkul']) ?></p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">
                            <span>Kurikulum</span>
                        </span>
                    </div>
                </a>

                <!-- 3. Card Plotting Aktif -->
                <a href="<?= \Core\Guard::url('/superadmin/plotting') ?>"
                    class="group bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:border-emerald-400 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 block cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500 truncate">Plotting Asdos</p>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?= number_format($stats['total_plotting']) ?></p>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Berjalan</span>
                        </span>
                    </div>
                </a>

                <!-- 4. Card Total Presensi -->
                <a href="<?= \Core\Guard::url('/superadmin/monitoring') ?>"
                    class="group bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs hover:border-amber-400 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 block cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500 truncate">Total Presensi</p>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"><?= number_format($stats['total_absensi'] ?? 0) ?></p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                            <span>Real-time</span>
                        </span>
                    </div>
                </a>

            </div>

            <!-- ========================================== -->
            <!-- CALENDAR & TRACKING (SPLIT VIEW 2-KOLOM)   -->
            <!-- ========================================== -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">

                <!-- Calendar Top Bar Header & Filter Controls -->
                <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#1867c0] text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">Tracking Absensi Asdos</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Monitoring riwayat dan kehadiran mengajar asisten laboratorium secara real-time</p>
                        </div>
                    </div>

                    <!-- Event Type Filter Pills -->
                    <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
                        <button type="button" onclick="filterCalendar('all')" id="btn-filter-all" class="filter-btn px-3 py-1.5 rounded-lg bg-[#1867c0] text-white shadow-2xs transition cursor-pointer">
                            Semua
                        </button>
                        <button type="button" onclick="filterCalendar('disetujui')" id="btn-filter-disetujui" class="filter-btn px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-600 mr-1"></span>Disetujui
                        </button>
                        <button type="button" onclick="filterCalendar('pending')" id="btn-filter-pending" class="filter-btn px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                            <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-1"></span>Pending
                        </button>
                        <button type="button" onclick="filterCalendar('ditolak')" id="btn-filter-ditolak" class="filter-btn px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                            <span class="inline-block w-2 h-2 rounded-full bg-red-600 mr-1"></span>Ditolak
                        </button>
                    </div>
                </div>

                <!-- Split Grid Layout (Left: FullCalendar, Right: List Tugas Tanggal Terpilih) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-slate-200">

                    <!-- Left: FullCalendar Engine (7-8 Kolom pada Desktop) -->
                    <div class="lg:col-span-7 xl:col-span-8 p-3 sm:p-5 bg-white">
                        <div id="fullcalendar-container" class="min-h-[540px]"></div>
                    </div>

                    <!-- Right: Panel Daftar Tugas Hari Terpilih (4-5 Kolom pada Desktop) -->
                    <div class="lg:col-span-5 xl:col-span-4 bg-slate-50/60 p-4 sm:p-5 flex flex-col justify-between">
                        <div>
                            <!-- Header Tanggal yang Diklik -->
                            <div class="flex items-center justify-between border-b border-slate-200 pb-3.5 mb-4">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jadwal Tanggal</span>
                                    <h4 id="selected-date-title" class="text-sm sm:text-base font-bold text-slate-900 mt-0.5">
                                        Memuat tanggal...
                                    </h4>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" id="btn-quick-today" onclick="selectToday()" class="hidden px-2 py-1 text-[10px] font-bold text-[#1867c0] bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200 transition cursor-pointer">
                                        Hari Ini
                                    </button>
                                    <span id="selected-tasks-count" class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-blue-100 text-[#1867c0]">
                                        0 Presensi
                                    </span>
                                </div>
                            </div>

                            <!-- Container List Tugas yang Berjalan di Tanggal Ini -->
                            <div id="selected-tasks-list" class="space-y-3.5 max-h-[460px] overflow-y-auto pr-1">
                                <!-- Injected dynamically by JavaScript -->
                            </div>
                        </div>

                        <!-- Shortcut Tautan Bawah -->
                        <div class="mt-4 pt-3.5 border-t border-slate-200 flex items-center justify-between text-xs">
                            <a href="<?= \Core\Guard::url('/superadmin/plotting') ?>" class="text-[#1867c0] hover:text-[#14529d] font-semibold inline-flex items-center gap-1 transition">
                                <span>Kelola Plotting</span> &rarr;
                            </a>
                            <a href="<?= \Core\Guard::url('/superadmin/monitoring') ?>" class="text-slate-600 hover:text-[#1867c0] font-semibold inline-flex items-center gap-1 transition">
                                <span>Monitoring Absensi</span> &rarr;
                            </a>
                        </div>
                    </div>

                </div>

            <!-- Layerz-Style Recent Presensi Table Section -->
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1867c0] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 tracking-tight">Presensi Praktikum Terbaru</h2>
                            <p class="text-xs text-slate-400 mt-0.5">5 laporan kehadiran asisten laboratorium yang masuk terakhir</p>
                        </div>
                    </div>

                    <a href="<?= \Core\Guard::url('/superadmin/monitoring') ?>" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-[#1867c0] hover:text-[#13529a] text-xs font-semibold border border-slate-200/80 transition active:scale-95">
                        <span>Lihat Semua di Monitoring</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <?php if (empty($recentAbsensi)): ?>
                    <div class="p-8 text-center text-xs text-slate-400">
                        Belum ada data presensi yang masuk.
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-4">Asisten Dosen</th>
                                    <th class="py-3.5 px-4">Mata Kuliah & Pertemuan</th>
                                    <th class="py-3.5 px-4">Tanggal Presensi</th>
                                    <th class="py-3.5 px-4 text-center">Bukti Foto</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($recentAbsensi as $ra): ?>
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <!-- Asdos -->
                                        <td class="py-3.5 px-4 align-middle">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100 text-[#1867c0] flex items-center justify-center font-bold text-xs shrink-0">
                                                    <?= strtoupper(substr($ra['nama_asdos'] ?? 'A', 0, 1)) ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-slate-900 truncate"><?= htmlspecialchars($ra['nama_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                    <p class="text-[11px] text-slate-400 font-mono">NPM: <?= htmlspecialchars($ra['npm_asdos'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Matkul & Pertemuan -->
                                        <td class="py-3.5 px-4 align-middle">
                                            <p class="font-semibold text-slate-800"><?= htmlspecialchars($ra['nama_matkul'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                            <span class="inline-block mt-0.5 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.2 rounded border border-blue-200">
                                                Pertemuan ke-<?= $ra['pertemuan_ke'] ?? '1' ?>
                                            </span>
                                        </td>

                                        <!-- Tanggal -->
                                        <td class="py-3.5 px-4 align-middle text-slate-600 font-medium">
                                            <?= date('d M Y', strtotime($ra['tanggal'] ?? 'now')) ?>
                                        </td>

                                        <!-- Bukti Foto -->
                                        <td class="py-3.5 px-4 align-middle text-center">
                                            <?php if (!empty($ra['foto_kegiatan'])): ?>
                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200">
                                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span>Terlampir</span>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-[11px]">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Status -->
                                        <td class="py-3.5 px-4 align-middle">
                                            <?php
                                            $st = $ra['status_verifikasi'] ?? 'menunggu';
                                            if ($st === 'disetujui') {
                                                echo '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-emerald-50 text-emerald-800 border-emerald-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disetujui</span>';
                                            } elseif ($st === 'ditolak') {
                                                echo '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-rose-50 text-rose-800 border-rose-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Ditolak</span>';
                                            } else {
                                                echo '<span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border bg-amber-50 text-amber-800 border-amber-300 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Pending</span>';
                                            }
                                            ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-4 align-middle text-right">
                                            <a href="<?= \Core\Guard::url('/superadmin/monitoring') ?>" 
                                                class="px-2.5 py-1 text-[11px] font-semibold text-[#1867c0] hover:text-white hover:bg-[#1867c0] rounded-lg border border-blue-200 transition">
                                                Tinjau
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </main>

        <!-- FullCalendar Logic Script -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Data dari Controller PHP
                const absensiList = <?= json_encode($calendarAbsensi, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];

                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

                const todayKey = formatDateKey(new Date());
                let currentFilter = 'all';
                let selectedDateStr = todayKey;

                const dateTitleEl = document.getElementById('selected-date-title');
                const tasksListEl = document.getElementById('selected-tasks-list');
                const countBadgeEl = document.getElementById('selected-tasks-count');
                const quickTodayBtn = document.getElementById('btn-quick-today');

                function formatDateKey(d) {
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }

                function formatIndonesianDate(d) {
                    const dayName = dayNames[d.getDay()];
                    const dayNumber = d.getDate();
                    const monthName = monthNames[d.getMonth()];
                    const year = d.getFullYear();
                    return `${dayName}, ${dayNumber} ${monthName} ${year}`;
                }

                // Ambil data absensi untuk tanggal tertentu
                function getEventsForDate(dateStr) {
                    const result = [];
                    absensiList.forEach(item => {
                        if (item.tanggal && item.tanggal.substring(0, 10) === dateStr) {
                            if (currentFilter === 'all' || item.status_verifikasi === currentFilter) {
                                result.push(item);
                            }
                        }
                    });
                    return result;
                }

                // Render Panel Samping Kanan (Daftar Presensi Hari Terpilih)
                function renderSelectedDateTasks() {
                    const parts = selectedDateStr.split('-');
                    const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                    dateTitleEl.textContent = formatIndonesianDate(d);

                    // Tombol pintas "Hari Ini" jika melihat tanggal lain
                    if (selectedDateStr !== todayKey) {
                        quickTodayBtn.classList.remove('hidden');
                    } else {
                        quickTodayBtn.classList.add('hidden');
                    }

                    const items = getEventsForDate(selectedDateStr);
                    countBadgeEl.textContent = `${items.length} Presensi`;

                    tasksListEl.innerHTML = '';

                    if (items.length === 0) {
                        tasksListEl.innerHTML = `
                    <div class="text-center py-12 text-slate-400">
                        <div class="w-12 h-12 mx-auto mb-2.5 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Tidak ada presensi pada tanggal ini</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-[220px] mx-auto">Klik tanggal lain pada kalender untuk melihat catatan kehadiran asdos yang mengajar.</p>
                    </div>
                `;
                        return;
                    }

                    // Render Riwayat / Sesi Absensi Praktikum
                    const sectionTitle = document.createElement('div');
                    sectionTitle.className = 'text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1.5';
                    sectionTitle.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span>Absensi Praktikum (${items.length})</span>`;
                    tasksListEl.appendChild(sectionTitle);

                    items.forEach(a => {
                        let statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-amber-50 text-amber-700 border border-amber-200">Pending Review</span>';
                        if (a.status_verifikasi === 'disetujui') {
                            statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Disetujui</span>';
                        } else if (a.status_verifikasi === 'ditolak') {
                            statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-red-50 text-red-700 border border-red-200">Ditolak</span>';
                        }

                        const jamText = (a.jam_mulai && a.jam_selesai) ? `${a.jam_mulai} - ${a.jam_selesai}` : (a.jam_mulai || '');

                        const card = document.createElement('div');
                        card.className = 'p-3.5 bg-white border border-slate-200/90 rounded-xl shadow-2xs hover:shadow-xs transition duration-150';
                        card.innerHTML = `
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="text-[11px] font-bold text-slate-800">Pertemuan Ke-${escapeHtml(String(a.pertemuan_ke || '1'))}</span>
                        ${statusBadge}
                    </div>
                    <h5 class="text-xs font-bold text-slate-900 leading-snug">${escapeHtml(a.nama_matkul || 'Praktikum Lab')}</h5>
                    
                    <div class="mt-2.5 pt-2 border-t border-slate-100 text-[11px] space-y-1.5 text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Asdos: <strong class="text-slate-800 font-semibold">${escapeHtml(a.nama_asdos || '—')}</strong></span>
                            ${jamText ? `<span class="font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">${escapeHtml(jamText)}</span>` : ''}
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-500">
                            <span>Dosen: <span class="text-slate-700 font-medium">${escapeHtml(a.nama_dosen || '—')}</span></span>
                        </div>
                        ${a.deskripsi_tugas ? `
                            <div class="p-2 bg-slate-50 rounded-lg border border-slate-100 text-[10px] text-slate-600 italic">
                                "${escapeHtml(a.deskripsi_tugas)}"
                            </div>
                        ` : ''}
                    </div>
                `;
                        tasksListEl.appendChild(card);
                    });
                }

                // Format data absensi ke standar FullCalendar Event Object (Solid Colors & Text Putih Jelas)
                function buildEvents() {
                    const events = [];

                    // Absensi Praktikum (Event Utama pada Kalender Harian)
                    absensiList.forEach(a => {
                        let statusClass = 'fc-event-pending';
                        let solidBg = '#d97706'; // Amber
                        if (a.status_verifikasi === 'disetujui') {
                            statusClass = 'fc-event-disetujui';
                            solidBg = '#059669'; // Emerald
                        } else if (a.status_verifikasi === 'ditolak') {
                            statusClass = 'fc-event-ditolak';
                            solidBg = '#dc2626'; // Red
                        }

                        const asdosName = a.nama_asdos ? ` (${a.nama_asdos})` : '';

                        events.push({
                            id: 'abs-' + a.id_absensi,
                            title: `Pert. ${a.pertemuan_ke || '1'}: ${a.nama_matkul}${asdosName}`,
                            start: a.tanggal,
                            allDay: true,
                            backgroundColor: solidBg,
                            borderColor: solidBg,
                            textColor: '#ffffff',
                            classNames: [statusClass],
                            extendedProps: {
                                type: 'absensi',
                                category: a.status_verifikasi,
                                data: a
                            }
                        });
                    });

                    return events;
                }

                const allCalendarEvents = buildEvents();

                // Highlight sel tanggal yang sedang dipilih
                function highlightCalendarDay(dateStr) {
                    document.querySelectorAll('.fc-day-selected-custom').forEach(el => {
                        el.classList.remove('fc-day-selected-custom');
                    });
                    const cell = document.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
                    if (cell) {
                        cell.classList.add('fc-day-selected-custom');
                    }
                }

                // Inisialisasi FullCalendar
                const calendarEl = document.getElementById('fullcalendar-container');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    firstDay: 1, // Mulai dari Senin
                    dayMaxEvents: 2,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listMonth'
                    },
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        list: 'Agenda'
                    },
                    events: function(fetchInfo, successCallback, failureCallback) {
                        if (currentFilter === 'all') {
                            successCallback(allCalendarEvents);
                        } else {
                            const filtered = allCalendarEvents.filter(ev => ev.extendedProps.category === currentFilter);
                            successCallback(filtered);
                        }
                    },
                    dateClick: function(info) {
                        selectedDateStr = info.dateStr;
                        highlightCalendarDay(info.dateStr);
                        renderSelectedDateTasks();
                    },
                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
                        const eventDate = info.event.startStr ? info.event.startStr.substring(0, 10) : selectedDateStr;
                        if (eventDate) {
                            selectedDateStr = eventDate;
                            highlightCalendarDay(eventDate);
                            renderSelectedDateTasks();
                        }
                    },
                    datesSet: function() {
                        setTimeout(() => {
                            highlightCalendarDay(selectedDateStr);
                        }, 40);
                    }
                });

                calendar.render();

                // Pilih Hari Ini
                window.selectToday = function() {
                    selectedDateStr = todayKey;
                    calendar.today();
                    highlightCalendarDay(todayKey);
                    renderSelectedDateTasks();
                };

                // Inisialisasi awal render tugas hari ini
                renderSelectedDateTasks();
                setTimeout(() => {
                    highlightCalendarDay(selectedDateStr);
                }, 100);

                // Global Filter Handler
                window.filterCalendar = function(category) {
                    currentFilter = category;

                    // Update UI Button Styles
                    document.querySelectorAll('.filter-btn').forEach(btn => {
                        btn.className = 'filter-btn px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer';
                    });
                    const activeBtn = document.getElementById('btn-filter-' + category);
                    if (activeBtn) {
                        activeBtn.className = 'filter-btn px-3 py-1.5 rounded-lg bg-[#1867c0] text-white shadow-2xs transition cursor-pointer';
                    }

                    calendar.refetchEvents();
                    renderSelectedDateTasks();
                };

                function escapeHtml(str) {
                    if (!str) return '';
                    return String(str)
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }
            });
        </script>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-6 mb-16 md:mb-0 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 font-medium">
                &copy; <?= date('Y') ?> Laboratorium Sistem Informasi. All rights reserved.
            </div>
        </footer>
    </div>

    <!-- Floating Bottom Navbar (Khusus Mobile) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>

</body>

</html>