@extends('layouts.spekta')

@section('title', 'Manajemen Absensi')

@section('content')
@php
    $totalAssignment = count($assignmentsWithSubjects ?? []);

    $activeToday = collect($assignmentsWithSubjects ?? [])->filter(function($as) use ($jadwalHariIni) {
        return in_array($as->class_id, $jadwalHariIni);
    })->count();
@endphp

<div class="abs-page">

    {{-- HEADER --}}
    <section class="abs-header">
        <div class="abs-header-left">
            <h1>Manajemen Absensi</h1>
            <p>Kelola kehadiran siswa berdasarkan program kelas, bidang ajar, dan pertemuan mingguan secara berkala.</p>
        </div>
    </section>

    @if(session('success'))
        <div class="abs-alert success">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- STATS SUMMARY --}}
    <section class="abs-stats">
        <div class="abs-stat-card card-teal">
            <div class="abs-stat-info">
                <p>Penugasan Kelas</p>
                <h2>{{ $totalAssignment }} <span>Kelas</span></h2>
            </div>
        </div>

        <div class="abs-stat-card card-red">
            <div class="abs-stat-info">
                <p>Jadwal Hari Ini</p>
                <h2>{{ $activeToday }} <span>Kelas</span></h2>
            </div>
            @if($activeToday > 0)
                <span class="abs-pulse-dot"></span>
            @endif
        </div>
    </section>

    {{-- LIST PANEL --}}
    <section class="abs-panel">
        <div class="abs-panel-head">
            <div>
                <h2>Daftar Kelas Absensi</h2>
                <p>Pilih kelas dan bidang ajar di bawah ini untuk membuka daftar pertemuan mingguan.</p>
            </div>
        </div>

        @if(empty($assignmentsWithSubjects) || count($assignmentsWithSubjects) == 0)
            <div class="abs-empty">
                <strong>Belum ada penugasan materi.</strong>
                <span>Admin akademik perlu menugaskan Anda pada program dan mata pelajaran tertentu terlebih dahulu.</span>
            </div>
        @else
            <div class="abs-table-wrap">
                <table class="abs-table">
                    <thead>
                        <tr>
                            <th>Program Kelas</th>
                            <th>Bidang Ajar</th>
                            <th>Status Hari Ini</th>
                            <th>Informasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($assignmentsWithSubjects as $as)
                            @php
                                $canAbsenToday = in_array($as->class_id, $jadwalHariIni);
                            @endphp

                            <tr>
                                <td>
                                    <div class="abs-class-name">
                                        <strong>{{ $as->classModel->program_name ?? 'Program Kelas' }}</strong>
                                        <span>ID Kelas: #{{ $as->class_id }}</span>
                                    </div>
                                </td>

                                <td>
                                    <span class="abs-subject-badge-teal">
                                        {{ $as->subject_name }}
                                    </span>
                                </td>

                                <td>
                                    @if($canAbsenToday)
                                        <span class="abs-status active">Aktif Hari Ini</span>
                                    @else
                                        <span class="abs-status neutral">Tidak Ada Jadwal</span>
                                    @endif
                                </td>

                                <td>
                                    <p class="abs-note">
                                        @if($canAbsenToday)
                                            Anda memiliki jadwal mengajar hari ini. Absensi dapat dilakukan melalui daftar minggu.
                                        @else
                                            Anda tetap dapat membuka rekap mingguan meskipun tidak ada jadwal mengajar hari ini.
                                        @endif
                                    </p>
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('pengajar.absensi.weeks', [$as->class_id, $as->subject_name]) }}" class="abs-action-teal">
                                        Buka Minggu
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

</div>

<style>
    :root {
        --spekta-red-dark: #c5352c;
        --spekta-red: #e53935;
        --spekta-teal: #2ea8ab;
        --spekta-teal-dark: #1e878a;
        --spekta-teal-light: rgba(46, 168, 171, 0.12);
        --spekta-red-light: rgba(229, 57, 53, 0.06);
        --spekta-gray: #9e9e9e;
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .abs-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .abs-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .abs-breadcrumb-capsule {
        display: inline-block;
        background: var(--spekta-red-light);
        color: var(--spekta-red-dark);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 8px;
    }
    .abs-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .abs-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    .abs-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .abs-stat-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 18px 22px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.2s ease;
        position: relative;
    }
    .abs-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .abs-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .abs-stat-card.card-red:hover { border-color: var(--spekta-red); }

    .abs-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .abs-stat-info h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    .abs-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .abs-pulse-dot {
        position: absolute;
        top: 14px; right: 14px;
        width: 6px; height: 6px;
        background: var(--spekta-red);
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7);
        animation: pulseRed 1.5s infinite;
    }
    @keyframes pulseRed {
        0% { box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(229, 57, 53, 0); }
        100% { box-shadow: 0 0 0 0 rgba(229, 57, 53, 0); }
    }

    .abs-alert {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-weight: 700;
        font-size: 13px;
    }
    .abs-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }

    .abs-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }
    .abs-panel-head {
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .abs-panel-head h2 {
        margin: 0;
        color: var(--text-main);
        font-size: 15px;
        font-weight: 800;
    }
    .abs-panel-head p {
        margin: 4px 0 0;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
    }

    .abs-table-wrap { overflow-x: auto; }
    .abs-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }
    .abs-table th {
        text-align: left;
        padding: 12px 14px;
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        border-bottom: 2px solid var(--spekta-gray-light);
        font-weight: 800;
        letter-spacing: 0.05em;
    }
    .abs-table td {
        padding: 14px;
        border-bottom: 1px solid var(--spekta-gray-light);
        vertical-align: middle;
    }
    .abs-table tbody tr:hover { background: #fafbfc; }

    .abs-class-name strong {
        display: block;
        color: var(--text-main);
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .abs-class-name span {
        display: block;
        margin-top: 4px;
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 700;
    }

    /* ── SUBJECT BADGE TEAL ── */
    .abs-subject-badge-teal {
        display: inline-flex;
        align-items: center;
        height: 26px;
        padding: 0 14px;
        border-radius: 8px;
        background: var(--spekta-teal-light);
        color: var(--spekta-teal-dark);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
        border: 1px solid rgba(46, 168, 171, 0.15);
        letter-spacing: 0.04em;
        transition: all 0.2s ease;
    }

    .abs-subject-badge-teal:hover {
        background: var(--spekta-teal);
        color: #ffffff;
        border-color: var(--spekta-teal);
        box-shadow: 0 3px 10px rgba(46, 168, 171, 0.2);
    }

    .abs-status {
        display: inline-flex;
        align-items: center;
        height: 22px;
        padding: 0 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .abs-status.active {
        background: #e6f7ed;
        color: #15803d;
        box-shadow: 0 2px 6px rgba(22, 163, 74, 0.1);
    }
    .abs-status.neutral {
        background: var(--spekta-gray-light);
        color: var(--text-muted);
    }

    .abs-note {
        margin: 0;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 500;
        line-height: 1.5;
        max-width: 450px;
    }

    /* ── TOMBOL TEAL ── */
    .abs-action-teal {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 34px;
        padding: 0 20px;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--spekta-teal) 0%, var(--spekta-teal-dark) 100%);
        color: var(--spekta-white);
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 3px 10px rgba(46, 168, 171, 0.2);
        letter-spacing: 0.03em;
        position: relative;
        overflow: hidden;
        min-width: 120px;
    }

    .abs-action-teal::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
    }

    .abs-action-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 168, 171, 0.35);
    }

    .abs-action-teal:hover::before {
        left: 100%;
    }

    .abs-action-teal:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(46, 168, 171, 0.2);
    }

    .text-center {
        text-align: center;
    }

    .abs-empty {
        padding: 40px;
        text-align: center;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .abs-empty strong {
        display: block;
        color: var(--text-main);
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    @media (max-width: 900px) {
        .abs-header { flex-direction: column; align-items: flex-start; }
        .abs-stats { grid-template-columns: 1fr; }
        .abs-table-wrap th:nth-child(4),
        .abs-table-wrap td:nth-child(4) { display: none; }
    }

    @media (max-width: 600px) {
        .abs-action-teal {
            min-width: 90px;
            padding: 0 12px;
            font-size: 10px;
            height: 30px;
        }
        .abs-subject-badge-teal {
            font-size: 9px;
            padding: 0 10px;
            height: 22px;
        }
    }
</style>
@endsection
