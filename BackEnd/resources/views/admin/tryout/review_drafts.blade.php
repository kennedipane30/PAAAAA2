@extends('layouts.spekta')

@section('title', 'Kurasi Paket Tryout - Spekta Academy')

@section('content')
<div class="cp-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Kurasi Soal: <span style="color: #0d9488;">{{ $class->program_name }}</span></h1>
            <p>Satukan draf soal terbaik dari para pengajar, atur batas durasi, lalu terbitkan ke aplikasi mobile siswa.</p>
        </div>
        <div class="welcome-action">
            <div class="draft-badge">
                <strong>{{ $drafts->count() }}</strong>
                <span>Draf Soal</span>
            </div>
            <a href="{{ route('admin.tryout.index') }}" class="back-btn">Kembali ke Master</a>
        </div>
    </section>

    @if(session('error'))
        <div class="sc-alert error">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── PUBLISH PANEL ── --}}
    <section class="cp-publish-panel">
        <div class="cp-panel-heading">
            <div>
                <h2>Pengaturan Publikasi Paket TO</h2>
                <p>Masukkan judul paket tryout dan alokasikan durasi waktu sebelum diterbitkan ke siswa.</p>
            </div>
        </div>

        <form action="{{ route('admin.tryout.publish') }}" method="POST">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->class_id }}">

            <div class="cp-input-row">
                <div class="cp-input-group">
                    <label>Judul Paket Tryout Resmi</label>
                    <input type="text" name="title" placeholder="Contoh: Tryout Akbar Nasional 2024" required>
                </div>
                <div class="cp-input-group">
                    <label>Durasi (Menit)</label>
                    <input type="number" name="duration" value="90" required min="1">
                </div>
                <div class="cp-btn-align">
                    <button type="submit" class="cp-btn-publish-teal">PUBLISH KE MOBILE</button>
                </div>
            </div>
        </form>
    </section>

    {{-- ── DAFTAR SOAL ── --}}
    <div class="cp-questions-wrapper">
        <h4 class="cp-section-title">Daftar Detail Soal (Tinjauan Admin)</h4>

        @foreach($drafts as $index => $d)
            <div class="soal-card">
                <div class="soal-card-header">
                    <div class="header-tags">
                        <span class="soal-badge">SOAL #{{ $index + 1 }}</span>
                        <span class="subject-badge">{{ $d->subject_name }}</span>
                    </div>

                    <form action="{{ route('admin.tryout.draft.delete', $d->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini dari draf?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete-soal">Hapus Soal</button>
                    </form>
                </div>

                <div class="soal-body">
                    <div class="soal-text">{!! $d->question !!}</div>

                    <div class="options-grid">
                        @foreach(['a','b','c','d','e'] as $opt)
                            @php
                                $isCorrect = (strtoupper($opt) == strtoupper($d->correct_answer));
                            @endphp
                            <div class="option-item {{ $isCorrect ? 'correct' : '' }}">
                                <span class="opt-label">{{ strtoupper($opt) }}</span>
                                <span class="opt-text">{{ $d->{'option_'.$opt} }}</span>
                                @if($isCorrect)
                                    <span class="check-icon">✓</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($d->explanation)
                        <div class="explanation-box">
                            <strong>PEMBAHASAN:</strong>
                            <p>{{ $d->explanation }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
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
    }

    .draft-badge {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        padding: 6px 14px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
    }

    .draft-badge strong {
        font-size: 18px;
        font-weight: 800;
        color: #dc2626;
    }

    .draft-badge span {
        font-size: 10px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
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

    .sc-alert.error {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* ── PUBLISH PANEL ── */
    .cp-publish-panel {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #edf0f4;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
    }

    .cp-panel-heading {
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .cp-panel-heading h2 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .cp-panel-heading p {
        margin: 0;
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    .cp-input-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1.5fr;
        gap: 15px;
        align-items: end;
    }

    .cp-input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .cp-input-group label {
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .cp-input-group input {
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        font-weight: 500;
        outline: none;
        transition: all 0.25s ease;
        font-family: inherit;
        font-size: 12px;
        color: #111827;
        width: 100%;
    }

    .cp-input-group input:focus {
        border-color: #14b8a6;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .cp-btn-align {
        padding-top: 18px;
    }

    /* ── TOMBOL PUBLISH TEAL ── */
    .cp-btn-publish-teal {
        width: 100%;
        height: 42px;
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
        cursor: pointer;
        letter-spacing: 0.03em;
    }

    .cp-btn-publish-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .cp-btn-publish-teal:active {
        transform: scale(0.97);
    }

    /* ── QUESTIONS ── */
    .cp-questions-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .cp-section-title {
        font-weight: 700;
        color: #111827;
        font-size: 15px;
        margin: 0 0 4px 4px;
    }

    .soal-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        transition: all 0.25s ease;
    }

    .soal-card:hover {
        border-color: #14b8a6;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
    }

    .soal-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .header-tags {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .soal-badge {
        background: #1f2937;
        color: #ffffff;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .subject-badge {
        background: rgba(20, 184, 166, 0.1);
        color: #0d9488;
        font-size: 10px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .btn-delete-soal {
        background: transparent;
        border: none;
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .btn-delete-soal:hover {
        color: #dc2626;
        background: #fee2e2;
    }

    .soal-text {
        font-size: 14px;
        color: #111827;
        line-height: 1.6;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .options-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
    }

    .option-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: #f9fafb;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
        position: relative;
        gap: 8px;
    }

    .option-item.correct {
        background: rgba(20, 184, 166, 0.08);
        border-color: #14b8a6;
        color: #0d9488;
        font-weight: 700;
    }

    .opt-label {
        font-weight: 700;
        opacity: 0.4;
        flex-shrink: 0;
    }

    .opt-text {
        flex: 1;
    }

    .check-icon {
        color: #14b8a6;
        font-weight: 700;
        flex-shrink: 0;
    }

    .explanation-box {
        margin-top: 20px;
        padding: 14px;
        background: #fffbeb;
        border-radius: 10px;
        border: 1px solid #fef3c7;
    }

    .explanation-box strong {
        display: block;
        font-size: 9px;
        color: #92400e;
        margin-bottom: 6px;
        letter-spacing: 1px;
        font-weight: 700;
    }

    .explanation-box p {
        font-size: 12px;
        color: #78350f;
        margin: 0;
        line-height: 1.6;
        font-weight: 500;
    }

    @media (max-width: 1024px) {
        .cp-input-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .cp-btn-align {
            padding-top: 0;
        }

        .options-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
        }

        .welcome-action {
            border-left: none;
            padding-left: 0;
            flex-wrap: wrap;
            width: 100%;
        }

        .back-btn {
            width: 100%;
            justify-content: center;
        }

        .draft-badge {
            width: 100%;
            justify-content: center;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .soal-card {
            padding: 16px;
        }
    }
</style>
@endsection
