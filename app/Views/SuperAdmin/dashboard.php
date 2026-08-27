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
    <title>Dashboard Super Admin — Absensi Lab</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased">

    <!-- Top Popup Notifications (Auto-dismiss 4 detik) -->
    <?php require_once __DIR__ . '/../Templates/notifications.php'; ?>

    <!-- Header / Navbar (Unified Desktop Navbar) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_header.php'; ?>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">

        <!-- Page Header Banner -->
        <div class="bg-white border border-slate-200 p-5 sm:p-6 rounded-xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Dashboard Super Admin</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Selamat datang kembali, <strong><?= htmlspecialchars($currentUser['nama'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></strong>. Kelola pengguna, plotting asisten, dan pantau aktivitas praktikum.
                </p>
            </div>
        </div>        <!-- Metric / Stat Cards Grid (Clickable to Respective Pages) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
            
            <!-- 1. Card Total Pengguna -> Kelola Pengguna -->
            <a href="<?= \Core\Guard::url('/superadmin/users') ?>" 
               class="group block bg-white p-4 sm:p-5 rounded-xl border border-blue-200/80 bg-blue-50/20 shadow-xs hover:border-[#1867c0] hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 cursor-pointer">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#1867c0] group-hover:underline">Total Pengguna</p>
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1867c0] flex items-center justify-center group-hover:bg-[#1867c0] group-hover:text-white transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-[#1867c0] mt-1 sm:mt-2"><?= $stats['total_users'] ?></p>
                <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-blue-100/80">
                    <p class="text-[11px] text-slate-500">Dosen, Asdos, & Admin</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#1867c0] bg-blue-100/70 px-2 py-0.5 rounded-full group-hover:bg-[#1867c0] group-hover:text-white transition-all duration-150">
                        <span>Buka</span>
                        <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>

            <!-- 2. Card Total Mata Kuliah -> Kelola Mata Kuliah -->
            <a href="<?= \Core\Guard::url('/superadmin/matkul') ?>" 
               class="group block bg-white p-4 sm:p-5 rounded-xl border border-indigo-200/80 bg-indigo-50/20 shadow-xs hover:border-indigo-500 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 cursor-pointer">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 group-hover:underline">Mata Kuliah</p>
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-indigo-700 mt-1 sm:mt-2"><?= $stats['total_matkul'] ?></p>
                <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-indigo-100/80">
                    <p class="text-[11px] text-slate-500">Praktikum Terdaftar</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-100/80 px-2 py-0.5 rounded-full group-hover:bg-indigo-600 group-hover:text-white transition-all duration-150">
                        <span>Buka</span>
                        <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>

            <!-- 3. Card Plotting Aktif -> Plotting Asdos -->
            <a href="<?= \Core\Guard::url('/superadmin/plotting') ?>" 
               class="group block bg-white p-4 sm:p-5 rounded-xl border border-emerald-200 bg-emerald-50/20 shadow-xs hover:border-emerald-500 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-150 cursor-pointer">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 group-hover:underline">Plotting Aktif</p>
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-emerald-700 mt-1 sm:mt-2"><?= $stats['total_plotting'] ?></p>
                <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-emerald-100/80">
                    <p class="text-[11px] text-slate-500">Penugasan Berjalan</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full group-hover:bg-emerald-600 group-hover:text-white transition-all duration-150">
                        <span>Buka</span>
                        <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>

        </div>

        <!-- ========================================== -->
        <!-- CALENDAR & TRACKING PENUGASAN LAB          -->
        <!-- ========================================== -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            
            <!-- Calendar Top Bar Header -->
            <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#1867c0] text-white flex items-center justify-center shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">Tracking Penugasan & Absensi Lab</h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Pantau periode penugasan asdos aktif dan riwayat absensi pelaksanaan praktikum per tanggal.
                    </p>
                </div>

                <!-- Month Navigation Controls -->
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <button type="button" id="cal-prev-btn" 
                            class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition shadow-2xs cursor-pointer" 
                            title="Bulan Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    
                    <span id="cal-month-year" class="text-sm font-bold text-slate-800 min-w-[140px] text-center px-2">
                        <!-- Dynamic Month Year -->
                    </span>

                    <button type="button" id="cal-next-btn" 
                            class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition shadow-2xs cursor-pointer" 
                            title="Bulan Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <button type="button" id="cal-today-btn" 
                            class="ml-1 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-[#1867c0] hover:bg-blue-50 transition shadow-2xs cursor-pointer">
                        Hari Ini
                    </button>
                </div>
            </div>

            <!-- Legend Info Bar (Neat 2x2 Grid on Mobile, Inline Flex on Desktop) -->
            <div class="px-4 sm:px-6 py-2.5 sm:py-3 bg-slate-50 border-b border-slate-100">
                <div class="grid grid-cols-2 md:flex md:flex-wrap md:items-center gap-2 sm:gap-3 text-[11px] text-slate-600">
                    <span class="hidden md:inline-flex font-bold text-slate-700 mr-1">Keterangan:</span>
                    
                    <div class="flex items-center gap-2 bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/80 shadow-2xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                        <span class="font-medium text-slate-700 truncate">Plotting Aktif</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/80 shadow-2xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="font-medium text-slate-700 truncate">Absensi Disetujui</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/80 shadow-2xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                        <span class="font-medium text-slate-700 truncate">Absensi Pending</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/80 shadow-2xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 shrink-0"></span>
                        <span class="font-medium text-slate-700 truncate">Absensi Ditolak</span>
                    </div>
                </div>
            </div>

            <!-- Main Calendar Grid & Detail Split Panel -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 p-4 sm:p-6">
                
                <!-- Left: Calendar Matrix Grid (8 Cols on LG) -->
                <div class="lg:col-span-7 xl:col-span-8">
                    <!-- Day Name Headers -->
                    <div class="grid grid-cols-7 gap-1 text-center mb-1.5">
                        <div class="py-1 text-[11px] font-bold text-red-500 uppercase tracking-wider">Min</div>
                        <div class="py-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Sen</div>
                        <div class="py-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Sel</div>
                        <div class="py-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Rab</div>
                        <div class="py-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Kam</div>
                        <div class="py-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Jum</div>
                        <div class="py-1 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Sab</div>
                    </div>

                    <!-- Days Container -->
                    <div id="cal-days-grid" class="grid grid-cols-7 gap-1 sm:gap-1.5">
                        <!-- Dynamic Calendar Cells Generated by JS -->
                    </div>
                </div>

                <!-- Right: Selected Date Assignment & Activity Details (4-5 Cols on LG) -->
                <div class="lg:col-span-5 xl:col-span-4 bg-slate-50 border border-slate-200/80 rounded-xl p-4 sm:p-5 flex flex-col justify-between">
                    <div>
                        <!-- Header Date Selected -->
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-3.5">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Detail Penugasan</span>
                                <h4 id="selected-date-title" class="text-sm sm:text-base font-bold text-slate-900 mt-0.5">
                                    Pilih Tanggal
                                </h4>
                            </div>
                            <span id="selected-events-count" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-[#1867c0]">
                                0 Aktivitas
                            </span>
                        </div>

                        <!-- Activity Events List on Selected Day -->
                        <div id="selected-events-list" class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
                            <!-- Injected by JavaScript -->
                        </div>
                    </div>

                    <!-- Bottom Quick Links -->
                    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                        <a href="<?= \Core\Guard::url('/superadmin/plotting') ?>" class="text-slate-600 hover:text-[#1867c0] font-semibold inline-flex items-center gap-1">
                            Kelola Plotting &rarr;
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- Calendar Interactive Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Data dari Controller PHP
        const absensiList  = <?= json_encode($calendarAbsensi, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];
        const plottingList = <?= json_encode($calendarPlotting, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        let currentDate = new Date();
        let selectedDateStr = formatDateKey(new Date());

        const monthYearEl  = document.getElementById('cal-month-year');
        const daysGridEl   = document.getElementById('cal-days-grid');
        const prevBtn      = document.getElementById('cal-prev-btn');
        const nextBtn      = document.getElementById('cal-next-btn');
        const todayBtn     = document.getElementById('cal-today-btn');
        const dateTitleEl  = document.getElementById('selected-date-title');
        const eventsListEl = document.getElementById('selected-events-list');
        const countBadgeEl = document.getElementById('selected-events-count');

        function formatDateKey(d) {
            const year  = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day   = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function formatIndonesianDate(d) {
            const dayName   = dayNames[d.getDay()];
            const dayNumber = d.getDate();
            const monthName = monthNames[d.getMonth()];
            const year      = d.getFullYear();
            return `${dayName}, ${dayNumber} ${monthName} ${year}`;
        }

        // Ambil data penugasan & absensi untuk tanggal tertentu
        function getEventsForDate(dateStr) {
            const result = {
                absensi: [],
                plotting: []
            };

            // 1. Cek Absensi pada tanggal ini
            absensiList.forEach(item => {
                if (item.tanggal && item.tanggal.substring(0, 10) === dateStr) {
                    result.absensi.push(item);
                }
            });

            // 2. Cek Plotting aktif yang mencakup tanggal ini
            plottingList.forEach(item => {
                if (item.periode_mulai && item.periode_selesai) {
                    const start = item.periode_mulai.substring(0, 10);
                    const end   = item.periode_selesai.substring(0, 10);
                    if (dateStr >= start && dateStr <= end) {
                        result.plotting.push(item);
                    }
                }
            });

            return result;
        }

        function renderCalendar() {
            const year  = currentDate.getFullYear();
            const month = currentDate.getMonth();

            monthYearEl.textContent = `${monthNames[month]} ${year}`;
            daysGridEl.innerHTML = '';

            const firstDayOfMonth = new Date(year, month, 1).getDay(); // 0 = Minggu
            const daysInMonth     = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();

            const todayStr = formatDateKey(new Date());

            // 1. Hari dari Bulan Sebelumnya (Filler)
            for (let i = firstDayOfMonth - 1; i >= 0; i--) {
                const dayNum = daysInPrevMonth - i;
                const prevDate = new Date(year, month - 1, dayNum);
                const prevDateStr = formatDateKey(prevDate);
                const cell = createDateCell(dayNum, prevDateStr, false, false);
                daysGridEl.appendChild(cell);
            }

            // 2. Hari Bulan Ini
            for (let dayNum = 1; dayNum <= daysInMonth; dayNum++) {
                const dateObj = new Date(year, month, dayNum);
                const dateStr = formatDateKey(dateObj);
                const isToday = (dateStr === todayStr);
                const isSelected = (dateStr === selectedDateStr);
                const cell = createDateCell(dayNum, dateStr, true, isToday, isSelected);
                daysGridEl.appendChild(cell);
            }

            // 3. Hari dari Bulan Berikutnya (Filler sisa 42 sel grid)
            const totalCellsRendered = firstDayOfMonth + daysInMonth;
            const remainingCells = (totalCellsRendered > 35 ? 42 : 35) - totalCellsRendered;
            for (let dayNum = 1; dayNum <= remainingCells; dayNum++) {
                const nextDate = new Date(year, month + 1, dayNum);
                const nextDateStr = formatDateKey(nextDate);
                const cell = createDateCell(dayNum, nextDateStr, false, false);
                daysGridEl.appendChild(cell);
            }

            renderSelectedDateDetails();
        }

        function createDateCell(dayNum, dateStr, isCurrentMonth, isToday, isSelected = false) {
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = `min-h-[64px] sm:min-h-[74px] p-1.5 sm:p-2 rounded-xl border text-left flex flex-col justify-between transition-all duration-150 cursor-pointer ${
                isCurrentMonth ? 'bg-white hover:border-[#1867c0]' : 'bg-slate-50/60 text-slate-300 border-slate-100 hover:bg-slate-100/80'
            } ${
                isSelected ? 'ring-2 ring-[#1867c0] border-[#1867c0] bg-blue-50/30' : 'border-slate-200/80'
            }`;

            // Bagian Nomor Tanggal
            const topRow = document.createElement('div');
            topRow.className = 'flex items-center justify-between w-full';

            const numSpan = document.createElement('span');
            numSpan.className = `text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full ${
                isToday 
                    ? 'bg-[#1867c0] text-white shadow-2xs' 
                    : isCurrentMonth ? 'text-slate-700' : 'text-slate-400'
            }`;
            numSpan.textContent = dayNum;
            topRow.appendChild(numSpan);

            cell.appendChild(topRow);

            // Indikator Penugasan & Absensi
            const events = getEventsForDate(dateStr);
            const totalEvents = events.absensi.length + events.plotting.length;

            const indicatorsContainer = document.createElement('div');
            indicatorsContainer.className = 'flex flex-wrap gap-1 mt-1 w-full';

            if (events.plotting.length > 0) {
                const dot = document.createElement('span');
                dot.className = 'inline-block w-2 h-2 rounded-full bg-blue-500';
                dot.title = `${events.plotting.length} Plotting Aktif`;
                indicatorsContainer.appendChild(dot);
            }

            if (events.absensi.length > 0) {
                events.absensi.forEach(ab => {
                    const dot = document.createElement('span');
                    let colorClass = 'bg-amber-500';
                    if (ab.status_verifikasi === 'disetujui') colorClass = 'bg-emerald-500';
                    if (ab.status_verifikasi === 'ditolak') colorClass = 'bg-red-500';
                    dot.className = `inline-block w-2 h-2 rounded-full ${colorClass}`;
                    dot.title = `Absensi: ${ab.status_verifikasi}`;
                    indicatorsContainer.appendChild(dot);
                });
            }

            // Teks ringkas jika di layar tablet/desktop
            if (totalEvents > 0) {
                const badge = document.createElement('span');
                badge.className = 'hidden sm:block text-[9px] font-semibold text-slate-500 truncate mt-0.5';
                badge.textContent = `${totalEvents} tugas`;
                indicatorsContainer.appendChild(badge);
            }

            cell.appendChild(indicatorsContainer);

            // Handler Klik Tanggal
            cell.addEventListener('click', () => {
                selectedDateStr = dateStr;
                renderCalendar();
            });

            return cell;
        }

        function renderSelectedDateDetails() {
            const parts = selectedDateStr.split('-');
            const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            dateTitleEl.textContent = formatIndonesianDate(d);

            const events = getEventsForDate(selectedDateStr);
            const totalCount = events.absensi.length + events.plotting.length;
            countBadgeEl.textContent = `${totalCount} Aktivitas`;

            eventsListEl.innerHTML = '';

            if (totalCount === 0) {
                eventsListEl.innerHTML = `
                    <div class="text-center py-8 text-slate-400">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs font-semibold text-slate-600">Tidak ada jadwal pada tanggal ini</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Belum ada plotting aktif atau absensi praktikum yang tercatat.</p>
                    </div>
                `;
                return;
            }

            // Render Plotting Aktif
            if (events.plotting.length > 0) {
                events.plotting.forEach(p => {
                    const card = document.createElement('div');
                    card.className = 'p-3 bg-white border border-blue-200/90 rounded-xl shadow-2xs';
                    card.innerHTML = `
                        <div class="flex items-center justify-between mb-1">
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded bg-blue-50 text-[#1867c0] border border-blue-200">
                                Plotting Aktif
                            </span>
                        </div>
                        <h5 class="text-xs font-bold text-slate-900">${escapeHtml(p.nama_matkul || 'Mata Kuliah')}</h5>
                        <p class="text-[11px] text-slate-600 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Asdos: <strong>${escapeHtml(p.nama_asdos || '—')}</strong></span>
                        </p>
                        <p class="text-[10px] text-slate-500 mt-0.5">
                            Dosen: ${escapeHtml(p.nama_dosen || '—')} &bull; Periode: ${escapeHtml(p.periode_mulai || '')} s/d ${escapeHtml(p.periode_selesai || '')}
                        </p>
                    `;
                    eventsListEl.appendChild(card);
                });
            }

            // Render Riwayat Absensi
            if (events.absensi.length > 0) {
                events.absensi.forEach(a => {
                    let statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded bg-amber-50 text-amber-700 border border-amber-200">Pending</span>';
                    if (a.status_verifikasi === 'disetujui') {
                        statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Disetujui</span>';
                    } else if (a.status_verifikasi === 'ditolak') {
                        statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded bg-red-50 text-red-700 border border-red-200">Ditolak</span>';
                    }

                    const card = document.createElement('div');
                    card.className = 'p-3 bg-white border border-slate-200 rounded-xl shadow-2xs';
                    card.innerHTML = `
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-bold text-slate-700">Pertemuan Ke-${escapeHtml(String(a.pertemuan_ke || '1'))}</span>
                            ${statusBadge}
                        </div>
                        <h5 class="text-xs font-bold text-slate-900">${escapeHtml(a.nama_matkul || 'Praktikum Lab')}</h5>
                        <p class="text-[11px] text-slate-600 mt-0.5">
                            Asdos: <strong>${escapeHtml(a.nama_asdos || '—')}</strong>
                            ${a.jam_mulai ? `&bull; <span class="text-slate-500 font-mono text-[10px]">${a.jam_mulai} - ${a.jam_selesai || ''}</span>` : ''}
                        </p>
                        ${a.deskripsi_tugas ? `<p class="text-[11px] text-slate-500 mt-1 italic line-clamp-2">"${escapeHtml(a.deskripsi_tugas)}"</p>` : ''}
                    `;
                    eventsListEl.appendChild(card);
                });
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Event Listeners
        prevBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        todayBtn.addEventListener('click', () => {
            currentDate = new Date();
            selectedDateStr = formatDateKey(new Date());
            renderCalendar();
        });

        // Inisialisasi awal
        renderCalendar();
    });
    </script>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mb-16 md:mb-0 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 font-medium">
            &copy; <?= date('Y') ?> Laboratorium Sistem Informasi. All rights reserved.
        </div>
    </footer>

    <!-- Floating Bottom Navbar (Khusus Mobile) -->
    <?php require_once __DIR__ . '/../Templates/superadmin_bottom_nav.php'; ?>

</body>
</html>
