@extends('layouts.spekta')

@section('title', 'Absesnsi Class Mingguan')

@section('content')
@php
    $doneCount = count($doneWeeks);
    $progress = round(($doneCount / 20) * 100);
@endphp

<div class="abs-page">

    {{-- HEADER TANPA KEMBALI DAN BADGE --}}
    <section class="abs-header">
        <div class="abs-header-left">
            <h1>{{ $class->program_name }}</h1>
            <p>Rekapitulasi mingguan untuk bidang ajar <strong>{{ $subject }}</strong>.</p>
        </div>

        <div class="abs-progress-box">
            <strong>{{ $doneCount }}/20</strong>
            <span>Minggu Selesai</span>
            <div class="progress-bar-wrap">
                <em style="width: {{ $progress }}%"></em>
            </div>
        </div>
    </section>

    {{-- TOMBOL KEMBALI TERPISAH --}}
    <div class="abs-nav">
        <a href="{{ route('pengajar.absensi.index') }}" class="abs-back-btn">Kembali ke Daftar Kelas</a>
    </div>

    {{-- GRID MINGGU --}}
    <section class="abs-panel">
        <div class="abs-week-guide">
            <div>
                <span class="guide-dot green"></span>
                <span>Hijau berarti absensi sudah diisi (rekap dapat dilihat).</span>
            </div>
            <div>
                <span class="guide-dot gray"></span>
                <span>Putih berarti absensi kosong (bisa mulai input).</span>
            </div>
        </div>

        <div class="abs-week-grid">
            @for($i = 1; $i <= 20; $i++)
                @php $isDone = in_array($i, $doneWeeks); @endphp

                <a href="{{ $isDone ? route('pengajar.absensi.recap', [$class->class_id, $subject, $i]) : route('pengajar.absensi.create', [$class->class_id, $subject, $i]) }}"
                   class="abs-week-cell {{ $isDone ? 'done' : 'open' }}">
                    <small>Week</small>
                    <strong>{{ $i }}</strong>
                    <span>{{ $isDone ? 'Lihat Rekap' : 'Mulai Absen' }}</span>
                </a>
            @endfor
        </div>
    </section>

</div>

<style>
    :root {
        --spekta-red-dark: #c5352c;
        --spekta-red: #e53935;
        --spekta-teal: #2ea8ab;
        --spekta-teal-light: rgba(46, 168, 171, 0.08);
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

    /* ── HEADER ── */
    .abs-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 16px;
        gap: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--spekta-gray-light);
    }

    .abs-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .abs-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 14px;
        font-weight: 500;
    }

    .abs-header p strong {
        color: var(--spekta-teal);
        font-weight: 700;
    }

    /* ── PROGRESS BOX ── */
    .abs-progress-box {
        width: 200px;
        flex-shrink: 0;
        padding: 14px 18px;
        border-radius: 12px;
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .abs-progress-box strong {
        display: block;
        font-size: 24px;
        font-weight: 900;
        color: var(--text-main);
        line-height: 1.2;
    }

    .abs-progress-box span {
        display: block;
        margin: 4px 0 10px;
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .progress-bar-wrap {
        height: 6px;
        border-radius: 999px;
        background: var(--spekta-gray-light);
        overflow: hidden;
    }

    .progress-bar-wrap em {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: var(--spekta-teal);
        box-shadow: 0 0 8px rgba(46,168,171,0.3);
        transition: width 0.6s ease;
    }

    /* ── NAV ── */
    .abs-nav {
        margin-bottom: 24px;
    }

    .abs-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        color: var(--text-muted);
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }

    .abs-back-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    /* ── PANEL ── */
    .abs-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
    }

    /* ── GUIDE ── */
    .abs-week-guide {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--spekta-gray-light);
        margin-bottom: 20px;
    }

    .abs-week-guide div {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
    }

    .guide-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .guide-dot.green { background: #16a34a; }
    .guide-dot.gray { background: var(--spekta-gray); }

    /* ── GRID ── */
    .abs-week-grid {
        display: grid;
        grid-template-columns: repeat(10, minmax(0, 1fr));
        gap: 12px;
    }

    .abs-week-cell {
        min-height: 100px;
        border-radius: 14px;
        border: 2px solid var(--border-soft);
        display: grid;
        place-items: center;
        align-content: center;
        text-align: center;
        transition: all 0.25s ease;
        background: var(--spekta-white);
        text-decoration: none;
        padding: 10px 6px;
    }

    .abs-week-cell small {
        color: var(--text-muted);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .abs-week-cell strong {
        color: var(--text-main);
        font-size: 26px;
        font-weight: 900;
        line-height: 1.2;
        margin: 4px 0;
    }

    .abs-week-cell span {
        color: var(--text-muted);
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* ── DONE ── */
    .abs-week-cell.done {
        background: #e6f7ed;
        border-color: #bbf7d0;
    }

    .abs-week-cell.done strong,
    .abs-week-cell.done span {
        color: #15803d;
    }

    .abs-week-cell.done:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(22, 163, 74, 0.12);
        border-color: #16a34a;
    }

    /* ── OPEN ── */
    .abs-week-cell.open:hover {
        border-color: var(--spekta-teal);
        background: var(--spekta-teal-light);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(46, 168, 171, 0.08);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1200px) {
        .abs-week-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); }
    }

    @media (max-width: 760px) {
        .abs-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .abs-progress-box {
            width: 100%;
        }

        .abs-week-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .abs-header h1 {
            font-size: 20px;
        }

        .abs-header p {
            font-size: 12px;
        }

        .abs-panel {
            padding: 16px;
        }

        .abs-week-cell {
            min-height: 80px;
        }

        .abs-week-cell strong {
            font-size: 20px;
        }
    }

    @media (max-width: 480px) {
        .abs-week-guide {
            flex-direction: column;
            gap: 10px;
        }

        .abs-back-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection
