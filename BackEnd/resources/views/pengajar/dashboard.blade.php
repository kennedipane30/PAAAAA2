@extends('layouts.spekta')

@section('title', 'Dashboard Pengajar  Spekta Academy ')
@section('subtitle', 'Spekta Academy')

@section('content')
@php
    $teacher = Auth::user();
    $jadwalUtama = $jadwalMendatang->first();
@endphp

<div class="td-page">

    {{-- ── GRID UTAMA DASHBOARD ── --}}
    <div class="td-dashboard-grid">

        {{-- KOLOM KIRI (KONTEN UTAMA) --}}
        <div class="td-main-col">

            {{-- Sapaan yang Lebih Elegan --}}
            <header class="td-welcome-header">
                <div class="td-welcome-badge">
                    <span class="td-breadcrumb-capsule">Spekta Teacher Workspace</span>
                </div>
                <div class="td-welcome-content">
                    <h1>Selamat Datang, <span class="td-name-highlight">{{ $teacher->name }}!</span></h1>
                    <p>Pantau agenda mengajar, kelola materi, dan tinjau aktivitas kelas Anda secara terpusat.</p>
                </div>
                <div class="td-welcome-decoration">
                    <span class="td-decoration-dot"></span>
                    <span class="td-decoration-dot"></span>
                    <span class="td-decoration-dot"></span>
                </div>
            </header>

            {{-- KARTU STATISTIK DENGAN WARNA FULL --}}
            <section class="td-stats-grid">
                <!-- Card 1: Kelas Diampu -->
                <div class="td-stat-card card-blue">
                    <div class="td-stat-content">
                        <span class="td-stat-label">Kelas Diampu</span>
                        <div class="td-stat-value">{{ number_format($totalKelas) }}</div>
                        <span class="td-stat-sub">Kelas</span>
                    </div>
                </div>

                <!-- Card 2: Materi Diupload -->
                <a href="{{ route('pengajar.materi.index') }}" class="td-stat-card card-teal clickable-card">
                    <div class="td-stat-content">
                        <span class="td-stat-label">Materi Diupload</span>
                        <div class="td-stat-value">{{ number_format($totalMateri) }}</div>
                        <span class="td-stat-sub">Berkas</span>
                    </div>
                </a>

                <!-- Card 3: Latihan Soal -->
                <a href="{{ route('pengajar.latihan.index') }}" class="td-stat-card card-orange clickable-card">
                    <div class="td-stat-content">
                        <span class="td-stat-label">Latihan Soal</span>
                        <div class="td-stat-value">{{ number_format($totalLatihan) }}</div>
                        <span class="td-stat-sub">Tugas</span>
                    </div>
                </a>

                <!-- Card 4: Tryout Dibuat -->
                <a href="{{ route('pengajar.tryout.index') }}" class="td-stat-card card-purple clickable-card">
                    <div class="td-stat-content">
                        <span class="td-stat-label">Tryout Dibuat</span>
                        <div class="td-stat-value">{{ number_format($totalTryout) }}</div>
                        <span class="td-stat-sub">Draf</span>
                    </div>
                </a>
            </section>

            {{-- JADWAL KELAS REGULER --}}
            <div class="td-panel">
                <div class="td-panel-heading">
                    <div class="heading-text">
                        <span class="panel-kicker">Teaching Schedule</span>
                        <h2>Jadwal Kelas Reguler</h2>
                        <p>Agenda kelas reguler yang sudah ditetapkan oleh admin akademik.</p>
                    </div>
                    <a href="{{ route('pengajar.absensi.index') }}" class="btn-outline-primary">
                        Cek Absensi
                    </a>
                </div>

                <div class="td-schedule-list">
                    @forelse($jadwalMendatang as $item)
                        @php
                            $isToday = \Carbon\Carbon::parse($item->date)->isToday();
                            $dateObj = \Carbon\Carbon::parse($item->date);
                        @endphp

                        <article class="td-schedule-item {{ $isToday ? 'is-today' : '' }}">
                            <div class="td-date-badge">
                                <strong>{{ $dateObj->format('d') }}</strong>
                                <span>{{ $dateObj->translatedFormat('M') }}</span>
                            </div>

                            <div class="td-schedule-details">
                                <div class="schedule-head">
                                    <h3>{{ $item->title }}</h3>
                                    @if($isToday)
                                        <span class="badge-live">Hari Ini</span>
                                    @else
                                        <span class="badge-scheduled">Terjadwal</span>
                                    @endif
                                </div>
                                <p class="program-name">{{ $item->class->program_name ?? 'Program Kelas' }}</p>

                                <div class="schedule-meta">
                                    <span>
                                        {{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB
                                    </span>
                                    <span>
                                        {{ $dateObj->translatedFormat('l') }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="td-empty-state">
                            <div class="td-empty-icon">
                                <i class="fa-regular fa-calendar"></i>
                            </div>
                            <strong>Belum ada jadwal mengajar.</strong>
                            <p>Jadwal akan muncul setelah admin akademik mempublikasikan jadwal kelas.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- JADWAL DEDICATED TUTOR --}}
            <div class="td-panel" style="margin-top: 24px;">
                <div class="td-panel-heading">
                    <div class="heading-text">
                        <span class="panel-kicker" style="color: var(--spekta-teal);">Private Session</span>
                        <h2>Jadwal Dedicated Tutor</h2>
                        <p>Sesi privat yang diajukan oleh siswa dan telah disetujui/dikonfirmasi oleh admin untuk Anda.</p>
                    </div>
                </div>

                <div class="td-tutor-grid">
                    @forelse($jadwalTutor as $tutor)
                        @php
                            $studentName = $tutor->student->user->name ?? 'Nama Siswa';
                            $subjectName = $tutor->material->title ?? $tutor->material->material_name ?? 'Materi Privat Umum';
                            $dateObj = \Carbon\Carbon::parse($tutor->date);
                            $isTodayTutor = $dateObj->isToday();
                        @endphp

                        <article class="td-tutor-card {{ $isTodayTutor ? 'is-today' : '' }}">
                            <div class="tutor-header">
                                <div class="student-avatar">
                                    {{ strtoupper(substr($studentName, 0, 1)) }}
                                </div>
                                <div class="student-info">
                                    <h3>{{ $studentName }}</h3>
                                    @if($isTodayTutor)
                                        <span class="badge-tutor today">Hari Ini</span>
                                    @else
                                        <span class="badge-tutor">Terkonfirmasi</span>
                                    @endif
                                </div>
                            </div>

                            <div class="tutor-body">
                                <p class="subject-title">{{ $subjectName }}</p>

                                <div class="tutor-meta-box">
                                    <div>
                                        <span>{{ $dateObj->translatedFormat('l, d M Y') }}</span>
                                    </div>
                                    <div>
                                        <span>{{ substr($tutor->time, 0, 5) }} WIB</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="td-empty-state">
                            <div class="td-empty-icon">
                                <i class="fa-regular fa-user"></i>
                            </div>
                            <strong>Belum ada jadwal Dedicated Tutor.</strong>
                            <p>Jika ada permintaan tutor dari siswa yang disetujui admin untuk Anda, jadwalnya akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (SIDEBAR AGENDA/KALENDER) ── --}}
        <aside class="td-sidebar-col">

            <!-- Widget Kalender Hari Ini -->
            <div class="td-side-widget date-widget">
                <div class="td-date-box">
                    <span>{{ now()->translatedFormat('l') }}</span>
                    <strong>{{ now()->translatedFormat('d') }}</strong>
                    <small>{{ now()->translatedFormat('F Y') }}</small>
                </div>
            </div>

            <!-- Widget Agenda Terdekat -->
            <div class="td-side-widget agenda-widget">
                <div class="widget-header">
                    <h3>Agenda Terdekat</h3>
                </div>

                <div class="widget-body">
                    @if($jadwalUtama)
                        <div class="agenda-item-active">
                            <span class="agenda-class-badge">{{ $jadwalUtama->class->program_name ?? 'Program Kelas' }}</span>
                            <strong>{{ $jadwalUtama->title }}</strong>
                            <small>{{ substr($jadwalUtama->start_time, 0, 5) }} WIB</small>
                        </div>
                    @else
                        <div class="agenda-empty">
                            <strong>Tidak ada agenda</strong>
                            <span>Belum ada jadwal kelas terdekat untuk Anda.</span>
                        </div>
                    @endif
                </div>
            </div>

        </aside>

    </div>
</div>

<style>
    :root {
        --spekta-red-dark: #c5352c;
        --spekta-red: #e53935;
        --spekta-teal: #14b8a6;
        --spekta-teal-dark: #0d9488;
        --spekta-blue: #2563eb;
        --spekta-blue-dark: #1d4ed8;
        --spekta-orange: #f59e0b;
        --spekta-orange-dark: #d97706;
        --spekta-purple: #8b5cf6;
        --spekta-purple-dark: #7c3aed;
        --spekta-gray: #9e9e9e;
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .td-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── DASHBOARD GRID ── */
    .td-dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 24px;
        align-items: start;
    }

    /* Kolom Kiri */
    .td-main-col {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ── WELCOME HEADER YANG LEBIH ELEGAN ── */
    .td-welcome-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 16px;
        padding: 24px 28px;
        border: 1px solid var(--border-soft);
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .td-welcome-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(20, 184, 166, 0.05) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(60px, -80px);
        pointer-events: none;
    }

    .td-welcome-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(229, 57, 53, 0.04) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(-40px, 60px);
        pointer-events: none;
    }

    .td-welcome-badge {
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }

    .td-breadcrumb-capsule {
        display: inline-block;
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.12), rgba(20, 184, 166, 0.06));
        color: var(--spekta-teal);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 20px;
        border: 1px solid rgba(20, 184, 166, 0.15);
    }

    .td-welcome-content {
        position: relative;
        z-index: 1;
    }

    .td-welcome-content h1 {
        margin: 0 0 8px;
        color: var(--text-main);
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .td-welcome-content h1 .td-name-highlight {
        background: linear-gradient(135deg, var(--spekta-teal), var(--spekta-teal-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
    }

    .td-welcome-content h1 .td-name-highlight::after {
        content: '';
        position: absolute;
        bottom: 2px;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--spekta-teal), transparent);
        border-radius: 2px;
        -webkit-text-fill-color: transparent;
    }

    .td-welcome-content p {
        margin: 0;
        color: var(--text-muted);
        font-size: 14px;
        font-weight: 500;
        line-height: 1.6;
        max-width: 90%;
    }

    .td-welcome-decoration {
        position: absolute;
        right: 28px;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        gap: 8px;
        z-index: 1;
    }

    .td-decoration-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--spekta-teal);
        opacity: 0.3;
        animation: dotPulse 2s ease-in-out infinite;
    }

    .td-decoration-dot:nth-child(2) {
        animation-delay: 0.3s;
        opacity: 0.5;
    }

    .td-decoration-dot:nth-child(3) {
        animation-delay: 0.6s;
        opacity: 0.7;
    }

    @keyframes dotPulse {
        0%, 100% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.5); opacity: 0.8; }
    }

    /* ── STATISTIK CARDS DENGAN WARNA FULL ── */
    .td-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .td-stat-card {
        border-radius: 14px;
        padding: 22px 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: none;
        color: #ffffff;
    }

    .td-stat-card.clickable-card {
        cursor: pointer;
        text-decoration: none;
        color: #ffffff;
    }

    .td-stat-card.clickable-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    }

    /* Warna Full untuk Kartu */
    .td-stat-card.card-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .td-stat-card.card-blue:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
    }

    .td-stat-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .td-stat-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .td-stat-card.card-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    .td-stat-card.card-orange:hover {
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
    }

    .td-stat-card.card-purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }
    .td-stat-card.card-purple:hover {
        box-shadow: 0 8px 30px rgba(139, 92, 246, 0.4);
    }

    .td-stat-content {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .td-stat-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 6px;
    }

    .td-stat-value {
        font-size: 32px;
        font-weight: 900;
        color: #ffffff;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .td-stat-sub {
        display: block;
        font-size: 12px;
        font-weight: 500;
        opacity: 0.8;
        color: rgba(255, 255, 255, 0.85);
    }

    /* Efek dekoratif pada card */
    .td-stat-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        pointer-events: none;
    }

    .td-stat-card::before {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -20%;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.03);
        pointer-events: none;
    }

    /* ── PANELS ── */
    .td-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.3s ease;
    }

    .td-panel:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }

    .td-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }

    .panel-kicker {
        display: block;
        font-size: 10px;
        font-weight: 800;
        color: var(--spekta-red);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
        position: relative;
    }

    .panel-kicker::after {
        content: '';
        display: inline-block;
        width: 20px;
        height: 2px;
        background: var(--spekta-red);
        margin-left: 8px;
        vertical-align: middle;
        border-radius: 2px;
    }

    .heading-text h2 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 800;
        color: var(--text-main);
    }

    .heading-text p {
        margin: 0;
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .btn-outline-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        color: var(--text-muted);
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-outline-primary:hover {
        background: var(--spekta-teal);
        color: #fff;
        border-color: var(--spekta-teal);
    }

    /* SCHEDULE LIST */
    .td-schedule-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
    }

    .td-schedule-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        background: var(--spekta-white);
        transition: all 0.2s;
    }

    .td-schedule-item:hover {
        border-color: var(--spekta-teal);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    }

    .td-schedule-item.is-today {
        border-color: var(--spekta-teal);
        background: rgba(20, 184, 166, 0.05);
    }

    .td-date-badge {
        width: 50px;
        height: 50px;
        background: var(--spekta-gray-light);
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
    }

    .td-schedule-item.is-today .td-date-badge {
        background: rgba(20, 184, 166, 0.1);
        border-color: var(--spekta-teal);
    }

    .td-date-badge strong {
        font-size: 18px;
        font-weight: 800;
        line-height: 1;
        color: var(--text-main);
    }

    .td-schedule-item.is-today .td-date-badge strong {
        color: var(--spekta-teal);
    }

    .td-date-badge span {
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .td-schedule-details {
        flex-grow: 1;
        min-width: 0;
    }

    .schedule-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2px;
    }

    .schedule-head h3 {
        margin: 0;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .badge-live {
        font-size: 9px;
        font-weight: 800;
        background: rgba(20, 184, 166, 0.15);
        color: var(--spekta-teal);
        padding: 2px 10px;
        border-radius: 4px;
    }

    .badge-scheduled {
        font-size: 9px;
        font-weight: 600;
        background: var(--spekta-gray-light);
        color: var(--text-muted);
        padding: 2px 10px;
        border-radius: 4px;
    }

    .program-name {
        margin: 0 0 8px;
        font-size: 11px;
        color: var(--spekta-red);
        font-weight: 700;
    }

    .schedule-meta {
        display: flex;
        gap: 12px;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* DEDICATED TUTOR */
    .td-tutor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
    }

    .td-tutor-card {
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        background: var(--spekta-white);
        padding: 16px;
        display: flex;
        flex-direction: column;
        transition: all 0.2s;
    }

    .td-tutor-card:hover {
        border-color: var(--spekta-teal);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    }

    .td-tutor-card.is-today {
        border-color: var(--spekta-teal);
        background: rgba(20, 184, 166, 0.05);
    }

    .tutor-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }

    .student-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--spekta-teal), var(--spekta-teal-dark));
        color: var(--spekta-white);
        display: grid;
        place-items: center;
        font-size: 15px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .td-tutor-card.is-today .student-avatar {
        box-shadow: 0 0 20px rgba(20, 184, 166, 0.25);
    }

    .student-info h3 {
        margin: 0 0 2px;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
    }

    .badge-tutor {
        font-size: 9px;
        font-weight: 600;
        color: var(--text-muted);
        background: var(--spekta-gray-light);
        padding: 2px 10px;
        border-radius: 4px;
    }

    .badge-tutor.today {
        background: var(--spekta-teal);
        color: #fff;
        font-weight: 800;
    }

    .tutor-body .subject-title {
        margin: 0 0 10px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-main);
    }

    .tutor-meta-box {
        display: flex;
        justify-content: space-between;
        background: var(--spekta-gray-light);
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid var(--border-soft);
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .td-tutor-card.is-today .tutor-meta-box {
        background: var(--spekta-white);
        border-color: rgba(20, 184, 166, 0.3);
    }

    /* ── EMPTY STATE YANG LEBIH ELEGAN ── */
    .td-empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, #fafbfc 0%, #f3f4f6 100%);
        border-radius: 12px;
        border: 1px dashed var(--border-soft);
        transition: all 0.3s ease;
    }

    .td-empty-state:hover {
        border-color: var(--spekta-teal);
        background: linear-gradient(135deg, #f8fafc 0%, #f0fdf4 100%);
    }

    .td-empty-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 14px;
        background: var(--spekta-white);
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 24px;
        color: var(--spekta-teal);
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        border: 1px solid var(--border-soft);
    }

    .td-empty-state strong {
        display: block;
        font-size: 14px;
        color: var(--text-main);
        margin-bottom: 4px;
        font-weight: 800;
    }

    .td-empty-state p {
        margin: 0;
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
        line-height: 1.5;
        max-width: 400px;
        margin: 0 auto;
    }

    /* ── SIDEBAR ── */
    .td-sidebar-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: sticky;
        top: 20px;
    }

    .td-side-widget {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.3s ease;
    }

    .td-side-widget:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }

    .td-date-box {
        text-align: center;
    }

    .td-date-box span {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: var(--spekta-teal);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 4px;
    }

    .td-date-box strong {
        display: block;
        font-size: 38px;
        font-weight: 900;
        line-height: 1;
        color: var(--text-main);
    }

    .td-date-box small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .td-side-widget .widget-header {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }

    .td-side-widget .widget-header h3 {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-main);
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.02em;
    }

    .agenda-item-active {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 14px;
        background: rgba(20, 184, 166, 0.06);
        border-radius: 10px;
        border-left: 4px solid var(--spekta-teal);
    }

    .agenda-class-badge {
        font-size: 9px;
        font-weight: 800;
        color: var(--spekta-teal);
        text-transform: uppercase;
    }

    .agenda-item-active strong {
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.4;
    }

    .agenda-item-active small {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .agenda-empty {
        text-align: center;
        padding: 20px 10px;
        color: var(--text-muted);
    }

    .agenda-empty strong {
        display: block;
        font-size: 12px;
        color: var(--text-main);
        margin-bottom: 2px;
        font-weight: 800;
    }

    .agenda-empty span {
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .td-dashboard-grid {
            grid-template-columns: 1fr;
        }
        .td-sidebar-col {
            position: static;
        }
        .td-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .td-welcome-decoration {
            display: none;
        }
        .td-welcome-content p {
            max-width: 100%;
        }
    }

    @media (max-width: 600px) {
        .td-stats-grid {
            grid-template-columns: 1fr;
        }
        .td-schedule-list,
        .td-tutor-grid {
            grid-template-columns: 1fr;
        }
        .td-panel-heading {
            flex-direction: column;
            gap: 12px;
        }
        .td-welcome-header {
            padding: 18px 16px;
        }
        .td-welcome-content h1 {
            font-size: 20px;
        }
        .td-welcome-content p {
            font-size: 12px;
        }
    }
</style>
@endsection
