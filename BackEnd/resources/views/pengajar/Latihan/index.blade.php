@extends('layouts.spekta')

@section('title', 'Latihan Soal - Spekta Academy')

@section('content')
@php
    $assignmentCollection = collect($assignmentsWithSubjects ?? []);
    $totalAssignment = $assignmentCollection->count();
@endphp

<div class="cp-page">

    {{-- ── 1. HEADER ── --}}
    <section class="cp-header">
        <div class="cp-header-left">
            <h1>Latihan Soal</h1>
            <p>Pilih bidang ajar Anda untuk mengelola kumpulan bank soal latihan mingguan secara berkala.</p>
        </div>
    </section>

    @if(session('success'))
        <div class="tm-alert-modern success">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="tm-alert-modern error">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ERROR HANDLING MICROSERVICE --}}
    @if(isset($serviceError) && $serviceError)
        <div class="tm-alert-modern warning">
            <span>⚠️ Server latihan soal sedang bermasalah. Data mungkin tidak dapat dimuat. Silakan coba lagi nanti.</span>
        </div>
    @endif

    {{-- ── 2. STATS SUMMARY ── --}}
    <section class="cp-stats">
        <div class="cp-stat-card card-teal">
            <div class="cp-stat-info">
                <p>Total Penugasan</p>
                <h2>{{ $totalAssignment }} <span>Kelas</span></h2>
            </div>
        </div>
    </section>

    {{-- ── 3. MAIN CARD ── --}}
    <section class="cp-main-card">
        <div class="card-header-flex">
            <div>
                <h2>Daftar Bidang Ajar</h2>
                <p>Semua kombinasi kelas dan mata pelajaran latihan yang Anda ampu.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="cp-table-modern">
                <thead>
                    <tr>
                        <th width="35%">PROGRAM KELAS</th>
                        <th width="25%" class="text-center">MATA PELAJARAN</th>
                        <th width="20%" class="text-center">DURASI</th>
                        <th width="20%" class="text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignmentsWithSubjects as $assign)
                        <tr>
                            <td>
                                <div class="program-info">
                                    <div>
                                        <strong>{{ $assign->classModel->program_name ?? 'Program' }}</strong>
                                        <small>ID Kelas: #{{ $assign->class_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="subject-tag-teal">
                                    {{ $assign->subject_name }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="duration-text">20 Minggu</span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('pengajar.latihan.pilih', [$assign->class_id, $assign->subject_name]) }}"
                                   class="btn-manage-teal">
                                    Kelola Latihan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="cp-empty-state">
                                    <strong>Belum ada penugasan bidang ajar untuk Anda saat ini.</strong>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

    .cp-page {
        font-family: 'Montserrat', sans-serif;
        padding: 10px;
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Header ── */
    .cp-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }

    .cp-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .cp-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* Alerts */
    .tm-alert-modern {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 13px;
    }
    .tm-alert-modern.success {
        background: #e6f7ed;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .tm-alert-modern.error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .tm-alert-modern.warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    /* ── Stats ── */
    .cp-stats {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        max-width: 400px;
    }

    .cp-stat-card {
        background: var(--spekta-white);
        border-radius: 14px;
        padding: 16px 22px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.25s ease;
        flex: 1;
    }

    .cp-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
        border-color: var(--spekta-teal);
    }

    .cp-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .cp-stat-info h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }

    .cp-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* ── Main Card ── */
    .cp-main-card {
        background: var(--spekta-white);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .card-header-flex {
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }

    .card-header-flex h2 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 4px 0;
    }

    .card-header-flex p {
        font-size: 11px;
        color: var(--text-muted);
        margin: 0;
        font-weight: 600;
    }

    /* ── Table ── */
    .table-responsive { overflow-x: auto; }

    .cp-table-modern {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .cp-table-modern th {
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        padding: 12px 14px;
        border-bottom: 2px solid var(--spekta-gray-light);
        letter-spacing: 0.05em;
    }

    .cp-table-modern td {
        padding: 14px;
        border-bottom: 1px solid var(--spekta-gray-light);
        vertical-align: middle;
    }

    .cp-table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .cp-table-modern tbody tr:hover {
        background: #fafbfc;
    }

    /* ── Program Info ── */
    .program-info strong {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        text-transform: uppercase;
    }

    .program-info small {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
        display: block;
    }

    /* ── Mata Pelajaran Teal ── */
    .subject-tag-teal {
        background: var(--spekta-teal-light);
        color: var(--spekta-teal-dark);
        padding: 6px 14px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 12px;
        text-transform: uppercase;
        border: 1px solid rgba(46, 168, 171, 0.15);
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.03em;
        transition: all 0.2s ease;
    }

    .subject-tag-teal:hover {
        background: var(--spekta-teal);
        color: #ffffff;
        border-color: var(--spekta-teal);
        box-shadow: 0 3px 10px rgba(46, 168, 171, 0.2);
    }

    /* ── Durasi ── */
    .duration-text {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* ── Tombol Kelola Latihan Teal ── */
    .btn-manage-teal {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--spekta-teal) 0%, var(--spekta-teal-dark) 100%);
        color: var(--spekta-white);
        padding: 8px 18px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.25s ease;
        font-weight: 800;
        font-size: 11px;
        box-shadow: 0 3px 10px rgba(46, 168, 171, 0.2);
        letter-spacing: 0.03em;
        white-space: nowrap;
        position: relative;
        overflow: hidden;
        min-width: 130px;
    }

    .btn-manage-teal::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
    }

    .btn-manage-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 168, 171, 0.35);
    }

    .btn-manage-teal:hover::before {
        left: 100%;
    }

    .btn-manage-teal:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(46, 168, 171, 0.2);
    }

    /* ── Empty State ── */
    .cp-empty-state {
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

    .cp-empty-state strong {
        display: block;
        color: var(--text-main);
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    /* ── Utilities ── */
    .text-center { text-align: center; }
    .text-right { text-align: right; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .cp-stats {
            max-width: 100%;
        }

        .cp-table-modern {
            min-width: 500px;
        }

        .cp-table-modern th,
        .cp-table-modern td {
            padding: 10px 12px;
            font-size: 11px;
        }

        .btn-manage-teal {
            min-width: 100px;
            padding: 6px 14px;
            font-size: 10px;
        }

        .subject-tag-teal {
            font-size: 10px;
            padding: 4px 10px;
        }

        .program-info strong {
            font-size: 11px;
        }
    }

    @media (max-width: 600px) {
        .cp-stat-card {
            padding: 14px 16px;
        }

        .cp-stat-info h2 {
            font-size: 22px;
        }

        .cp-main-card {
            padding: 14px;
        }

        .cp-table-modern th,
        .cp-table-modern td {
            padding: 8px 10px;
            font-size: 10px;
        }

        .btn-manage-teal {
            min-width: 80px;
            padding: 5px 10px;
            font-size: 9px;
        }
    }
</style>
@endsection
