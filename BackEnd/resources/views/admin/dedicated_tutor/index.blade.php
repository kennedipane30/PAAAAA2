@extends('layouts.spekta')

@section('title', 'Tutor Request Management')
@section('subtitle', 'Sistem Manajemen Permintaan Dedicated Tutor')

@section('content')
@php
    $tutorCollection = collect($tutors);

    $totalRequests = $tutorCollection->count();
    $pendingRequests = $tutorCollection->where('status', 'pending')->count();
    $confirmedRequests = $tutorCollection->where('status', 'confirmed')->count();
    $rejectedRequests = $tutorCollection->where('status', 'rejected')->count();

    $todayRequests = $tutorCollection->filter(function ($item) {
        return $item->date && \Carbon\Carbon::parse($item->date)->isToday();
    })->count();

    $allTeachers = \App\Models\User::where('role_id', 2)->orderBy('name')->get();
@endphp

<div class="dt-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Permintaan Dedicated Tutor</h1>
            <p>Kelola permintaan tutor privat siswa, tetapkan pengajar yang sesuai, dan pantau status konfirmasi secara real-time.</p>
        </div>
    </section>

    {{-- ── STATS CARDS ── --}}
    <section class="dt-summary">
        <div class="dt-stat-card card-blue">
            <div class="dt-stat-info">
                <p>Total Request</p>
                <strong>{{ $totalRequests }}</strong>
                <span class="dt-stat-sub">{{ $todayRequests }} Hari Ini</span>
            </div>
        </div>

        <div class="dt-stat-card card-orange">
            <div class="dt-stat-info">
                <p>Pending</p>
                <strong>{{ $pendingRequests }}</strong>
            </div>
            @if($pendingRequests > 0)
                <span class="dt-pulse-dot"></span>
            @endif
        </div>

        <div class="dt-stat-card card-teal">
            <div class="dt-stat-info">
                <p>Confirmed</p>
                <strong>{{ $confirmedRequests }}</strong>
            </div>
        </div>

        <div class="dt-stat-card card-red">
            <div class="dt-stat-info">
                <p>Rejected</p>
                <strong>{{ $rejectedRequests }}</strong>
            </div>
        </div>
    </section>

    {{-- ── MAIN LIST ── --}}
    <section class="dt-main-grid">
        <div class="dt-request-list">
            @forelse($tutors as $t)
                @php
                    $studentName = $t->student->user->name ?? 'N/A';
                    $topicTitle = $t->material->title ?? 'General Topic';
                    $subjectName = $t->material->material_name ?? $topicTitle;
                    $materialClassId = $t->material->class_id ?? null;

                    $qualifiedTeachers = collect();
                    if ($t->status === 'pending' && $materialClassId && $subjectName) {
                        $qualifiedTeachers = \App\Models\TeacherAssignment::whereHas('subject', function($q) use ($subjectName) {
                                $q->where('material_name', $subjectName);
                            })
                            ->where('class_id', $materialClassId)
                            ->with('teacher')
                            ->get();
                    }

                    $initial = strtoupper(substr($studentName, 0, 1));
                    $avatarColors = ['#e53935','#2ea8ab','#c5352c','#9e9e9e','#1f2937'];
                    $avatarBg = $avatarColors[crc32($studentName) % count($avatarColors)];
                @endphp

                <article class="dt-card {{ $t->status }}">
                    <div class="dt-card-info">
                        <div class="dt-avatar" style="background: {{ $avatarBg }}">{{ $initial }}</div>
                        <div class="dt-details">
                            <div class="dt-head">
                                <h3>{{ $studentName }}</h3>
                                <span class="dt-badge {{ $t->status }}">
                                    {{ strtoupper($t->status) }}
                                </span>
                            </div>

                            <div class="dt-meta-row">
                                <div class="meta-item">
                                    <small>Topik Pembelajaran</small>
                                    <strong>{{ $subjectName }}</strong>
                                </div>
                                <div class="meta-item">
                                    <small>Jadwal Diajukan</small>
                                    <strong>{{ \Carbon\Carbon::parse($t->date)->translatedFormat('d M Y') }} • {{ $t->time }} WIB</strong>
                                </div>
                                <div class="meta-item">
                                    <small>Guru Utama</small>
                                    <strong style="color: var(--spekta-teal);">{{ $t->teacher->name ?? 'Belum Ditugaskan' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dt-card-action">
                        @if($t->status === 'pending')
                            <form action="{{ route('admin.tutor.update', $t->dedicated_tutor_id) }}" method="POST" class="dt-assign-form">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">

                                <label>Tetapkan Pengajar</label>
                                <div class="select-wrapper">
                                    <select name="teacher_id" required>
                                        <option value="">Pilih pengajar...</option>
                                        @if($qualifiedTeachers->isNotEmpty())
                                            <optgroup label="Pengajar Ahli Materi Ini">
                                                @foreach($qualifiedTeachers as $assign)
                                                    <option value="{{ $assign->teacher->usersID }}">{{ $assign->teacher->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        <optgroup label="Semua Pengajar">
                                            @foreach($allTeachers as $teacher)
                                                <option value="{{ $teacher->usersID }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                                <button type="submit" class="btn-confirm-teal">Konfirmasi Jadwal</button>
                            </form>
                        @else
                            <div class="dt-resolved-state {{ $t->status }}">
                                <div class="resolved-icon">
                                    {{ $t->status === 'confirmed' ? '✓' : '✗' }}
                                </div>
                                <div class="resolved-text">
                                    <strong>Request {{ ucfirst($t->status) }}</strong>
                                    <span>Guru: {{ $t->teacher->name ?? '-' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="dt-empty">
                    <strong>Belum ada permintaan Dedicated Tutor</strong>
                    <span>Permintaan privat siswa yang masuk melalui aplikasi siswa akan muncul di sini.</span>
                </div>
            @endforelse
        </div>
    </section>
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
        --spekta-blue-light: rgba(37, 99, 235, 0.08);
        --spekta-orange: #f59e0b;
        --spekta-orange-dark: #d97706;
        --spekta-orange-light: rgba(245, 158, 11, 0.08);
        --spekta-gray: #9e9e9e;
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .dt-page {
        padding: 10px;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

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

    /* ── STATS CARDS ── */
    .dt-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .dt-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .dt-stat-card:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .dt-stat-card.card-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .dt-stat-card.card-blue:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
    }

    .dt-stat-card.card-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    .dt-stat-card.card-orange:hover {
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
    }

    .dt-stat-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .dt-stat-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .dt-stat-card.card-red {
        background: linear-gradient(135deg, #e53935 0%, #c5352c 100%);
        box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
    }
    .dt-stat-card.card-red:hover {
        box-shadow: 0 8px 30px rgba(229, 57, 53, 0.4);
    }

    .dt-stat-card::after {
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

    .dt-stat-card::before {
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

    .dt-stat-info {
        position: relative;
        z-index: 1;
    }

    .dt-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        color: rgba(255, 255, 255, 0.9);
    }

    .dt-stat-info strong {
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        display: block;
        line-height: 1.2;
    }

    .dt-stat-sub {
        font-size: 10px;
        font-weight: 600;
        opacity: 0.8;
        color: rgba(255, 255, 255, 0.85);
        display: block;
        margin-top: 4px;
    }

    .dt-pulse-dot {
        position: absolute;
        top: 14px; right: 14px;
        width: 6px; height: 6px;
        background: #f59e0b;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
        animation: pulseOrange 1.5s infinite;
    }
    @keyframes pulseOrange {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    /* ── REQUEST LIST ── */
    .dt-request-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .dt-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dt-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        border-color: #14b8a6;
    }

    .dt-card-info {
        padding: 20px;
        display: flex;
        gap: 16px;
        flex: 1;
    }

    .dt-avatar {
        width: 48px;
        height: 48px;
        border-radius: 99px;
        display: grid;
        place-items: center;
        font-size: 16px;
        font-weight: 900;
        color: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(0,0,0,0.06);
    }

    .dt-details {
        flex: 1;
    }

    .dt-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .dt-head h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .dt-badge {
        font-size: 9px;
        padding: 3px 10px;
        border-radius: 6px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .dt-badge.pending {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fde68a;
    }
    .dt-badge.confirmed {
        background: #e6f7ed;
        color: #15803d;
        border: 1px solid #a7f3d0;
    }
    .dt-badge.rejected {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .dt-meta-row {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .meta-item small {
        font-size: 10px;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .meta-item strong {
        font-size: 12px;
        color: #111827;
        font-weight: 600;
    }

    /* ── CARD ACTION ── */
    .dt-card-action {
        width: 280px;
        background: #f9fafb;
        border-left: 1px solid #e5e7eb;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .dt-assign-form label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .select-wrapper select {
        width: 100%;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        font-size: 12px;
        font-weight: 500;
        color: #111827;
        margin-bottom: 10px;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .select-wrapper select:focus {
        border-color: #14b8a6;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    /* ── TOMBOL KONFIRMASI TEAL ── */
    .btn-confirm-teal {
        width: 100%;
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff;
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.25);
        font-family: inherit;
        letter-spacing: 0.02em;
    }

    .btn-confirm-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(20, 184, 166, 0.35);
    }

    .btn-confirm-teal:active {
        transform: scale(0.97);
    }

    /* ── RESOLVED STATE ── */
    .dt-resolved-state {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .resolved-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 14px;
        font-weight: 700;
    }

    .dt-resolved-state.confirmed .resolved-icon {
        background: #e6f7ed;
        color: #15803d;
    }
    .dt-resolved-state.rejected .resolved-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .resolved-text {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .resolved-text strong {
        font-size: 13px;
        color: #111827;
        font-weight: 700;
    }

    .resolved-text span {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ── EMPTY STATE ── */
    .dt-empty {
        text-align: center;
        padding: 48px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px dashed #e5e7eb;
    }

    .dt-empty strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .dt-empty span {
        display: block;
        color: #6b7280;
        font-size: 12px;
        font-weight: 500;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .dt-summary {
            grid-template-columns: repeat(2, 1fr);
        }

        .dt-card {
            flex-direction: column;
        }

        .dt-card-action {
            width: 100%;
            border-left: none;
            border-top: 1px solid #e5e7eb;
        }
    }

    @media (max-width: 768px) {
        .welcome-card {
            padding: 20px;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .dt-summary {
            grid-template-columns: 1fr;
        }

        .dt-card-info {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .dt-head {
            justify-content: center;
        }

        .dt-meta-row {
            flex-direction: column;
            gap: 12px;
        }

        .dt-card-action {
            padding: 16px;
        }

        .dt-resolved-state {
            justify-content: center;
        }
    }
</style>
@endsection
