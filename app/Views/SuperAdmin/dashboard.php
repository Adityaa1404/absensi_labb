<?php
// Fallback & Dokumentasi Variabel dari SuperAdminController
$currentUser      = $currentUser ?? \Core\Guard::user();
$stats            = $stats ?? [
    'total_users'    => 0,
    'total_matkul'   => 0,
    'total_plotting' => 0,
];
$calendarAbsensi  = $calendarAbsensi ?? [];
$calendarPlotting = $calendarPlotting ?? [];
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

        /* 1. Plotting Aktif (Solid Royal Blue pada Grid) */
        .fc-daygrid-event.fc-event-plotting,
        .fc-timegrid-event.fc-event-plotting {
            background-color: #1867c0 !important;
            border-color: #1867c0 !important;
        }

        /* 2. Absensi Disetujui (Solid Emerald Green pada Grid) */
        .fc-daygrid-event.fc-event-disetujui,
        .fc-timegrid-event.fc-event-disetujui {
            background-color: #059669 !important;
            border-color: #059669 !important;
        }

        /* 3. Absensi Pending / Menunggu Review (Solid Dark Amber pada Grid) */
        .fc-daygrid-event.fc-event-pending,
        .fc-timegrid-event.fc-event-pending {
            background-color: #d97706 !important;
            border-color: #d97706 !important;
        }

        /* 4. Absensi Ditolak (Solid Crimson Red pada Grid) */
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
        .fc-list-event.fc-event-plotting .fc-list-event-dot {
            border-color: #1867c0 !important;
        }

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

            <!-- Page Header Banner -->
            <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Dashboard Super Admin</h2>
                </div>
            </div>

            <!-- Metric / Stat Cards Grid (3 Columns with Lift Hover) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">

                <!-- 1. Card Total Pengguna -> Kelola Pengguna -->
                <a href="<?= \Core\Guard::url('/superadmin/users') ?>"
                    class="group block bg-white p-4 sm:p-5 rounded-xl border border-blue-200/80 bg-blue-50/20 shadow-xs hover:border-[#1867c0] hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-[#1867c0] group-hover:underline">Total Pengguna</p>
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1867c0] flex items-center justify-center group-hover:bg-[#1867c0] group-hover:text-white transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-[#1867c0] mt-1 sm:mt-2"><?= $stats['total_users'] ?></p>
                    <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-blue-100/80">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#1867c0] bg-blue-100/70 px-2 py-0.5 rounded-full group-hover:bg-[#1867c0] group-hover:text-white transition-all duration-150">
                            <span>Kelola Pengguna</span>
                            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- 2. Card Total Mata Kuliah -> Kelola Mata Kuliah -->
                <a href="<?= \Core\Guard::url('/superadmin/matkul') ?>"
                    class="group block bg-white p-4 sm:p-5 rounded-xl border border-indigo-200/80 bg-indigo-50/20 shadow-xs hover:border-indigo-500 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 group-hover:underline">Mata Kuliah</p>
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-indigo-700 mt-1 sm:mt-2"><?= $stats['total_matkul'] ?></p>
                    <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-indigo-100/80">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-100/80 px-2 py-0.5 rounded-full group-hover:bg-indigo-600 group-hover:text-white transition-all duration-150">
                            <span>Kelola Matkul</span>
                            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- 3. Card Plotting Aktif -> Plotting Asdos -->
                <a href="<?= \Core\Guard::url('/superadmin/plotting') ?>"
                    class="group block bg-white p-4 sm:p-5 rounded-xl border border-emerald-200 bg-emerald-50/20 shadow-xs hover:border-emerald-500 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 group-hover:underline">Plotting Aktif</p>
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-bold text-emerald-700 mt-1 sm:mt-2"><?= $stats['total_plotting'] ?></p>
                    <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-emerald-100/80">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full group-hover:bg-emerald-600 group-hover:text-white transition-all duration-150">
                            <span>Kelola Plotting</span>
                            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
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
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">Tracking Penugasan & Absensi Asdos</h3>
                        </div>
                    </div>

                    <!-- Event Type Filter Pills -->
                    <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
                        <button type="button" onclick="filterCalendar('all')" id="btn-filter-all" class="filter-btn px-3 py-1.5 rounded-lg bg-[#1867c0] text-white shadow-2xs transition cursor-pointer">
                            Semua
                        </button>
                        <button type="button" onclick="filterCalendar('plotting')" id="btn-filter-plotting" class="filter-btn px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                            <span class="inline-block w-2 h-2 rounded-full bg-[#1867c0] mr-1"></span>Plotting
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
                                        0 Tugas
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

            </div>

        </main>

        <!-- FullCalendar Logic Script -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Data dari Controller PHP
                const absensiList = <?= json_encode($calendarAbsensi, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];
                const plottingList = <?= json_encode($calendarPlotting, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];

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

                // Ambil data aktivitas/tugas untuk tanggal tertentu
                function getEventsForDate(dateStr) {
                    const result = {
                        absensi: [],
                        plotting: []
                    };

                    // 1. Cek Absensi pada tanggal ini
                    absensiList.forEach(item => {
                        if (item.tanggal && item.tanggal.substring(0, 10) === dateStr) {
                            if (currentFilter === 'all' || item.status_verifikasi === currentFilter) {
                                result.absensi.push(item);
                            }
                        }
                    });

                    // 2. Cek Plotting aktif yang mencakup tanggal ini
                    plottingList.forEach(item => {
                        if (item.periode_mulai && item.periode_selesai) {
                            const start = item.periode_mulai.substring(0, 10);
                            const end = item.periode_selesai.substring(0, 10);
                            if (dateStr >= start && dateStr <= end) {
                                if (currentFilter === 'all' || currentFilter === 'plotting') {
                                    result.plotting.push(item);
                                }
                            }
                        }
                    });

                    return result;
                }

                // Render Panel Samping Kanan (Tugas Hari Terpilih)
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

                    const events = getEventsForDate(selectedDateStr);
                    const totalCount = events.absensi.length + events.plotting.length;
                    countBadgeEl.textContent = `${totalCount} Tugas`;

                    tasksListEl.innerHTML = '';

                    if (totalCount === 0) {
                        tasksListEl.innerHTML = `
                    <div class="text-center py-12 text-slate-400">
                        <div class="w-12 h-12 mx-auto mb-2.5 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Tidak ada jadwal pada tanggal ini</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-[220px] mx-auto">Klik tanggal lain pada kalender untuk melihat riwayat absensi atau plotting aktif.</p>
                    </div>
                `;
                        return;
                    }

                    // 1. Render Riwayat / Sesi Absensi Praktikum
                    if (events.absensi.length > 0) {
                        const sectionTitle = document.createElement('div');
                        sectionTitle.className = 'text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1.5';
                        sectionTitle.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span>Absensi Praktikum (${events.absensi.length})</span>`;
                        tasksListEl.appendChild(sectionTitle);

                        events.absensi.forEach(a => {
                            let statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-amber-50 text-amber-700 border border-amber-200">Pending Review</span>';
                            if (a.status_verifikasi === 'disetujui') {
                                statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Disetujui</span>';
                            } else if (a.status_verifikasi === 'ditolak') {
                                statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-red-50 text-red-700 border border-red-200">Ditolak</span>';
                            }

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
                                ${a.jam_mulai ? `<span class="font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">${a.jam_mulai}</span>` : ''}
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

                    // 2. Render Plotting Penugasan Aktif
                    if (events.plotting.length > 0) {
                        const sectionTitle = document.createElement('div');
                        sectionTitle.className = 'text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-3 mb-1 flex items-center gap-1.5';
                        sectionTitle.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-[#1867c0]"></span><span>Plotting Aktif (${events.plotting.length})</span>`;
                        tasksListEl.appendChild(sectionTitle);

                        events.plotting.forEach(p => {
                            const card = document.createElement('div');
                            card.className = 'p-3.5 bg-white border border-blue-200/80 bg-blue-50/10 rounded-xl shadow-2xs hover:shadow-xs transition duration-150';
                            card.innerHTML = `
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-blue-50 text-[#1867c0] border border-blue-200">
                                Plotting Aktif
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">${escapeHtml(p.periode_mulai || '')} s/d ${escapeHtml(p.periode_selesai || '')}</span>
                        </div>
                        <h5 class="text-xs font-bold text-slate-900 leading-snug">${escapeHtml(p.nama_matkul || 'Mata Kuliah')}</h5>
                        <div class="mt-2.5 pt-2 border-t border-blue-100/60 text-[11px] space-y-1 text-slate-600">
                            <p class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Asdos: <strong class="text-slate-800 font-semibold">${escapeHtml(p.nama_asdos || '—')}</strong></span>
                            </p>
                            <p class="text-[10px] text-slate-500">
                                Dosen Pengampu: <span class="font-medium text-slate-700">${escapeHtml(p.nama_dosen || '—')}</span>
                            </p>
                        </div>
                    `;
                            tasksListEl.appendChild(card);
                        });
                    }
                }

                // Format SEMUA data ke standar FullCalendar Event Object (Solid Colors & Text Putih Jelas)
                function buildEvents() {
                    const events = [];

                    // 1. Absensi Praktikum (Event Utama pada Kalender Harian)
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

                        events.push({
                            id: 'abs-' + a.id_absensi,
                            title: `Pert. ${a.pertemuan_ke || '1'}: ${a.nama_matkul}`,
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

                    // 2. Plotting Aktif (Solid Royal Blue)
                    plottingList.forEach(p => {
                        let endDate = p.periode_selesai;
                        if (endDate && endDate.length === 10) {
                            const d = new Date(endDate);
                            d.setDate(d.getDate() + 1);
                            const y = d.getFullYear();
                            const m = String(d.getMonth() + 1).padStart(2, '0');
                            const day = String(d.getDate()).padStart(2, '0');
                            endDate = `${y}-${m}-${day}`;
                        }

                        events.push({
                            id: 'plot-' + p.id_plotting,
                            title: `Plot: ${p.nama_matkul} (${p.nama_asdos})`,
                            start: p.periode_mulai,
                            end: endDate,
                            allDay: true,
                            backgroundColor: '#1867c0',
                            borderColor: '#1867c0',
                            textColor: '#ffffff',
                            classNames: ['fc-event-plotting'],
                            extendedProps: {
                                type: 'plotting',
                                category: 'plotting',
                                data: p
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