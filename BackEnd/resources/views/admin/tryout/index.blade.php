@extends('layouts.spekta')

@section('title', 'Master Tryout - Spekta Academy')

@section('content')
@php
    $totalDraftsCount = collect($classes)->sum(function($c) use ($draftStatus) {
        return $draftStatus[$c->class_id]->total ?? 0;
    });
@endphp

<div class="cp-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Manajemen Paket TO</h1>
            <p>Kurasi setoran soal dari pengajar dan publikasikan menjadi paket Tryout resmi untuk aplikasi mobile siswa.</p>
        </div>
    </section>

    {{-- SYSTEM ALERTS --}}
    @if(session('success'))
        <div class="tm-alert-modern success">
            <div>
                <strong>OPERASI BERHASIL</strong>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="tm-alert-modern error">
            <div>
                <strong>OPERASI GAGAL</strong>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ── STATS SUMMARY GRID ── --}}
    <section class="cp-stats">
        <div class="cp-stat-card card-red">
            <div class="cp-stat-info">
                <p>Paket TO Live</p>
                <h2>{{ count($activePackages) }} <span>Paket</span></h2>
            </div>
            @if(count($activePackages) > 0)
                <span class="cp-pulse-dot"></span>
            @endif
        </div>

        <div class="cp-stat-card card-teal">
            <div class="cp-stat-info">
                <p>Antrean Setoran</p>
                <h2>{{ number_format($totalDraftsCount) }} <span>Soal</span></h2>
            </div>
        </div>

        <div class="cp-stat-card card-blue">
            <div class="cp-stat-info">
                <p>Target Program</p>
                <h2>{{ count($classes) }} <span>Kelas</span></h2>
            </div>
        </div>
    </section>

    {{-- ── MAIN WORKSPACE GRID ── --}}
    <div class="tm-grid-layout">

        <section class="cp-main-card">
            <div class="card-header-flex">
                <div>
                    <h2>Setoran Soal Pengajar</h2>
                    <p>Draf soal masuk yang menunggu antrean publikasi.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="cp-table-modern">
                    <thead>
                        <tr>
                            <th>PROGRAM KELAS</th>
                            <th>STATUS DRAF</th>
                            <th class="text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $c)
                            @php
                                $totalSoal = $draftStatus[$c->class_id]->total ?? 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="program-info">
                                        <div>
                                            <strong>{{ $c->program_name }}</strong>
                                            <small>ID Kelas: #{{ $c->class_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($totalSoal > 0)
                                        <span class="badge-status success">
                                            {{ $totalSoal }} Soal Baru
                                        </span>
                                    @else
                                        <span class="badge-status empty">0 Soal Masuk</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="action-group-end">
                                        @if($totalSoal > 0)
                                            <a href="{{ route('admin.tryout.export_draft', $c->class_id) }}" class="btn-icon-sm green-soft">CSV</a>
                                        @endif

                                        <a href="{{ route('admin.tryout.review', $c->class_id) }}" class="btn-review-teal">
                                            Review
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="cp-main-card">
            <div class="card-header-flex">
                <div>
                    <h2>Paket TO Terbit</h2>
                    <p>Katalog paket yang sudah aktif di aplikasi siswa.</p>
                </div>
            </div>

            <div class="active-list">
                @forelse($activePackages as $pkg)
                    <div class="package-item-card">
                        <div class="pkg-info">
                            <strong>{{ $pkg->title }}</strong>
                            <span>{{ $pkg->class_name }} • {{ $pkg->total_questions }} Soal • {{ $pkg->duration }} menit</span>
                            <small class="pkg-id">ID: #{{ $pkg->tryout_id }}</small>
                        </div>
                        <form action="{{ route('admin.tryout.destroy_package', $pkg->tryout_id) }}" method="POST" onsubmit="return confirm('Hapus paket ini dari HP siswa?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-del-pkg" title="Hapus Paket">Hapus</button>
                        </form>
                    </div>
                @empty
                    <div class="empty-state-lite">
                        <p>Belum ada paket dipublikasikan.</p>
                        <small>Publish paket tryout dari draf yang tersedia.</small>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<style>
    :root {
        --spekta-red-dark: #c5352c;
        --spekta-red: #e53935;
        --spekta-teal: #14b8a6;
        --spekta-teal-dark: #0d9488;
        --spekta-teal-light: rgba(20, 184, 166, 0.08);
        --spekta-red-light: rgba(229, 57, 53, 0.06);
        --spekta-blue: #2563eb;
        --spekta-blue-dark: #1d4ed8;
        --spekta-gray: #9e9e9e;
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .cp-page { padding: 10px; font-family: 'Montserrat', sans-serif; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── WELCOME CARD ── */
    .welcome-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-left: 5px solid #14b8a6;
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }

    .welcome-card::after {
        content: "";
        position: absolute;
        width: 200px;
        height: 200px;
        right: -60px;
        top: -60px;
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.05) 0%, rgba(20, 184, 166, 0.02) 100%);
        border-radius: 999px;
        pointer-events: none;
    }

    .welcome-text {
        position: relative;
        z-index: 1;
    }

    .welcome-text h1 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #111827;
    }

    .welcome-text p {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ── ALERTS ── */
    .tm-alert-modern {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 700;
        border-left: 6px solid;
        font-size: 13px;
    }
    .tm-alert-modern.success {
        background: #e6f7ed;
        color: #15803d;
        border-color: #22c55e;
    }
    .tm-alert-modern.error {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #ef4444;
    }
    .tm-alert-modern p {
        margin: 2px 0 0;
        font-size: 12px;
        opacity: 0.9;
        font-weight: 500;
    }

    /* ── STATS ── */
    .cp-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .cp-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .cp-stat-card:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .cp-stat-card.card-red {
        background: linear-gradient(135deg, #e53935 0%, #c5352c 100%);
        box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
    }
    .cp-stat-card.card-red:hover {
        box-shadow: 0 8px 30px rgba(229, 57, 53, 0.4);
    }

    .cp-stat-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .cp-stat-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .cp-stat-card.card-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .cp-stat-card.card-blue:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
    }

    .cp-stat-card::after {
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

    .cp-stat-card::before {
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

    .cp-stat-info {
        position: relative;
        z-index: 1;
    }

    .cp-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        color: rgba(255, 255, 255, 0.9);
    }

    .cp-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }

    .cp-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        opacity: 0.8;
        color: rgba(255, 255, 255, 0.85);
    }

    .cp-pulse-dot {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 6px;
        height: 6px;
        background: #e53935;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7);
        animation: pulseRed 1.5s infinite;
    }
    @keyframes pulseRed {
        0% { box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(229, 57, 53, 0); }
        100% { box-shadow: 0 0 0 0 rgba(229, 57, 53, 0); }
    }

    /* ── GRID ── */
    .tm-grid-layout {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 24px;
    }

    .cp-main-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #edf0f4;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .card-header-flex h2 {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 4px;
    }

    .card-header-flex p {
        font-size: 11px;
        color: #6b7280;
        margin: 0;
        font-weight: 500;
    }

    /* ── TABLE ── */
    .table-responsive {
        overflow-x: auto;
    }

    .cp-table-modern {
        width: 100%;
        border-collapse: collapse;
    }

    .cp-table-modern th {
        font-size: 9px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        padding: 10px 14px;
        border-bottom: 2px solid #f3f4f6;
        letter-spacing: 0.08em;
    }

    .cp-table-modern td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
        font-size: 12px;
        font-weight: 500;
    }

    .cp-table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .cp-table-modern tbody tr:hover {
        background: #fafbfc;
    }

    .program-info strong {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .program-info small {
        font-size: 10px;
        color: #6b7280;
        font-weight: 500;
        display: block;
        margin-top: 2px;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        height: 22px;
        padding: 0 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-status.success {
        background: #e6f7ed;
        color: #15803d;
    }

    .badge-status.empty {
        background: #f3f4f6;
        color: #6b7280;
    }

    .action-group-end {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }

    .btn-icon-sm {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        background: #e6f7ed;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .btn-icon-sm:hover {
        background: #15803d;
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* ── TOMBOL REVIEW TEAL ── */
    .btn-review-teal {
        padding: 6px 16px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.25s ease;
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 3px 10px rgba(20, 184, 166, 0.2);
    }

    .btn-review-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(20, 184, 166, 0.3);
    }

    /* ── PACKAGE LIST ── */
    .package-item-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #f9fafb;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 12px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .package-item-card:hover {
        border-color: #14b8a6;
        background: #ffffff;
        transform: translateY(-2px);
    }

    .pkg-info {
        flex: 1;
    }

    .pkg-info strong {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .pkg-info span {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    .pkg-info .pkg-id {
        font-size: 9px;
        color: #94a3b8;
        display: block;
        margin-top: 2px;
    }

    .btn-del-pkg {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-del-pkg:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .empty-state-lite {
        text-align: center;
        padding: 30px;
        color: #6b7280;
        font-weight: 500;
    }

    .empty-state-lite p {
        margin: 0 0 4px;
        font-size: 13px;
    }

    .empty-state-lite small {
        font-size: 11px;
        color: #9ca3af;
    }

    .text-right {
        text-align: right;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .tm-grid-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .welcome-card {
            padding: 20px;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .cp-stats {
            grid-template-columns: 1fr;
        }

        .cp-main-card {
            padding: 16px;
        }

        .package-item-card {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
    }
</style>
@endsection
