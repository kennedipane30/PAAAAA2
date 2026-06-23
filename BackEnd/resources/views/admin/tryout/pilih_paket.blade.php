@extends('layouts.spekta')

@section('title', 'Pilih Paket Tryout')

@section('content')
@php
    $serviceError = isset($serviceError) ? $serviceError : false;
@endphp

<div class="cp-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Paket Tryout: <span style="color: #0d9488;">{{ $class->program_name }}</span></h1>
            <p>Pilih salah satu paket di bawah ini untuk melihat rekapitulasi daftar nilai siswa.</p>
        </div>
        <div class="welcome-action">
            <a href="{{ route('admin.scores.index') }}" class="back-btn">Kembali</a>
        </div>
    </section>

    @if($serviceError)
        <div class="sc-alert warning">
            <span>Server tryout sedang bermasalah. Data mungkin tidak lengkap.</span>
        </div>
    @endif

    {{-- ── TABLE ── --}}
    <div class="cp-main-card">
        <div class="cp-table-wrap">
            <table class="cp-table">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>Durasi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tryouts as $to)
                        @if($to)
                            @php
                                $toObj = (object) $to;
                                $duration = $toObj->duration ?? ($toObj->duration_minutes ?? 0);
                                $tryoutId = $toObj->tryout_id ?? ($toObj->id ?? 0);
                            @endphp
                            <tr>
                                <td class="to-package-title">
                                    <div class="to-package-cell">
                                        <strong>{{ $toObj->title ?? 'Untitled Package' }}</strong>
                                    </div>
                                </td>

                                <td class="to-duration-cell">
                                    {{ $duration }} Menit
                                </td>

                                <td class="text-right">
                                    <a href="{{ route('admin.scores.result', $tryoutId) }}" class="to-btn-rekap-teal">
                                        Rekap Nilai
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="cp-empty-state">
                                    <strong>Belum ada paket tryout yang diterbitkan untuk kelas ini.</strong>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    :root {
        --spekta-teal: #14b8a6;
        --spekta-teal-dark: #0d9488;
        --spekta-teal-light: rgba(20, 184, 166, 0.08);
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

    /* ── WELCOME CARD ── */
    .welcome-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-left: 5px solid #14b8a6;
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
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

    .welcome-action {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 14px;
        border-left: 1px solid #e5e7eb;
        padding-left: 24px;
        min-width: 140px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 40px;
        padding: 0 18px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #ffffff;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .back-btn:hover {
        background: #f9fafb;
        border-color: #14b8a6;
        color: #14b8a6;
    }

    /* ── ALERT ── */
    .sc-alert {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-weight: 700;
        font-size: 13px;
    }

    .sc-alert.warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    /* ── TABLE ── */
    .cp-main-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #edf0f4;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .cp-table-wrap { overflow-x: auto; border-radius: 12px; }
    .cp-table { width: 100%; border-collapse: collapse; min-width: 500px; }
    .cp-table th { text-align: left; padding: 10px 14px; font-size: 9px; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #f3f4f6; font-weight: 700; letter-spacing: 0.08em; }
    .cp-table td { padding: 12px 14px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; font-size: 12px; font-weight: 500; }
    .cp-table tbody tr:last-child td { border-bottom: none; }
    .cp-table tbody tr:hover { background: #fafbfc; }

    .to-package-cell strong { font-size: 13px; font-weight: 600; color: #111827; }

    .to-duration-cell { color: #6b7280; font-size: 12px; font-weight: 500; }

    /* ── TOMBOL REKAP NILAI TEAL ── */
    .to-btn-rekap-teal {
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

    .to-btn-rekap-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(20, 184, 166, 0.3);
    }

    .cp-empty-state {
        padding: 40px;
        text-align: center;
        color: #6b7280;
        font-size: 12px;
        font-weight: 500;
    }

    .cp-empty-state strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .text-right { text-align: right; }

    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }

        .welcome-action {
            border-left: none;
            padding-left: 0;
            min-width: unset;
            width: 100%;
        }

        .back-btn {
            width: 100%;
            justify-content: center;
        }

        .welcome-text h1 {
            font-size: 18px;
        }
    }
</style>
@endsection
