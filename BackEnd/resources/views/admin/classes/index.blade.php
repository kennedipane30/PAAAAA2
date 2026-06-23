@extends('layouts.spekta')

@section('title', 'Dashboard Program - Spekta Academy')

@section('content')
@php
    // Hitung statistik global
    $totalClasses = \App\Models\ClassModel::count();
    $totalStudents = \App\Models\User::where('role_id', 3)->count();
    $totalTeachers = \App\Models\User::where('role_id', 2)->count();
    $totalEnrollments = \App\Models\Enrollment::where('status', 'active')->count();

    // Mapping nama program ke nama file gambar
    $programImages = [
        'CALON ABDI NEGARA' => 'abdi_negara.png',
        'PTN & UNHAN' => 'ptn_unhan.png',
        'SMA & SMP REGULER' => 'regular.png',
        'SMA FAVORIT' => 'favort.png',
    ];
@endphp

<div class="cp-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Program Kelas</h1>
            <p>Monitor statistik program, jumlah siswa, dan penugasan pengajar.</p>
        </div>
    </section>

    {{-- ── STATISTIK GLOBAL ── --}}
    <section class="cp-stats">
        <div class="cp-stat-card card-blue">
            <div class="cp-stat-info">
                <p>Total Program</p>
                <h2>{{ number_format($totalClasses) }}</h2>
            </div>
        </div>

        <div class="cp-stat-card card-teal">
            <div class="cp-stat-info">
                <p>Total Siswa</p>
                <h2>{{ number_format($totalStudents) }}</h2>
            </div>
        </div>

        <div class="cp-stat-card card-orange">
            <div class="cp-stat-info">
                <p>Total Pengajar</p>
                <h2>{{ number_format($totalTeachers) }}</h2>
            </div>
        </div>

        <div class="cp-stat-card card-purple">
            <div class="cp-stat-info">
                <p>Pendaftaran Aktif</p>
                <h2>{{ number_format($totalEnrollments) }}</h2>
            </div>
        </div>
    </section>

    {{-- ── DAFTAR PROGRAM (2 KOLOM) ── --}}
    <section class="cp-main-panel">
        <div class="cp-panel-heading">
            <h2>Statistik Program Kelas</h2>
            <p>Detail lengkap per program: jumlah siswa dan pengajar.</p>
        </div>

        <div class="cp-program-grid">
            @forelse($classes as $class)
                @php
                    $studentCount = \App\Models\Enrollment::where('class_id', $class->class_id)
                        ->where('status', 'active')
                        ->count();

                    $teacherCount = \App\Models\TeacherAssignment::where('class_id', $class->class_id)
                        ->distinct('user_id')
                        ->count('user_id');

                    $imageFile = $programImages[$class->program_name] ?? 'default.png';
                    $imageUrl = asset($imageFile);
                @endphp

                <div class="cp-card">
                    <div class="cp-card-img">
                        <img src="{{ $imageUrl }}" alt="{{ $class->program_name }}">
                        <div class="cp-badge-price">Rp {{ number_format($class->price, 0, ',', '.') }}</div>
                    </div>

                    <div class="cp-card-content">
                        <div class="cp-card-header">
                            <span class="cp-id">ID: #{{ $class->class_id }}</span>
                            <h3 class="cp-class-name">{{ $class->program_name }}</h3>
                            <p class="cp-class-desc">{{ \Illuminate\Support\Str::limit($class->description, 100) }}</p>
                        </div>

                        <div class="cp-stats-mini">
                            <div class="stat-item">
                                <div>
                                    <strong>{{ number_format($studentCount) }}</strong>
                                    <span>Siswa Aktif</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div>
                                    <strong>{{ number_format($teacherCount) }}</strong>
                                    <span>Pengajar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="cp-empty-state">
                    <strong>Belum ada program kelas yang dibuat.</strong>
                </div>
            @endforelse
        </div>

        @if(method_exists($classes, 'links'))
        <div class="cp-pagination">
            {{ $classes->links() }}
        </div>
        @endif
    </section>
</div>

<style>
    :root {
        --spekta-blue: #2563eb;
        --spekta-blue-dark: #1d4ed8;
        --spekta-blue-light: rgba(37, 99, 235, 0.08);
        --spekta-teal: #14b8a6;
        --spekta-teal-dark: #0d9488;
        --spekta-teal-light: rgba(20, 184, 166, 0.08);
        --spekta-orange: #f59e0b;
        --spekta-orange-dark: #d97706;
        --spekta-orange-light: rgba(245, 158, 11, 0.08);
        --spekta-purple: #8b5cf6;
        --spekta-purple-dark: #7c3aed;
        --spekta-purple-light: rgba(139, 92, 246, 0.08);
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .cp-page {
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

    /* ── STATISTIK GLOBAL ── */
    .cp-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
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

    .cp-stat-card.card-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .cp-stat-card.card-blue:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
    }

    .cp-stat-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .cp-stat-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .cp-stat-card.card-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    .cp-stat-card.card-orange:hover {
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
    }

    .cp-stat-card.card-purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }
    .cp-stat-card.card-purple:hover {
        box-shadow: 0 8px 30px rgba(139, 92, 246, 0.4);
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
    }

    /* ── MAIN PANEL ── */
    .cp-main-panel {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #edf0f4;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .cp-panel-heading {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
    }

    .cp-panel-heading h2 {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 4px;
    }

    .cp-panel-heading p {
        font-size: 12px;
        color: #6b7280;
        margin: 0;
        font-weight: 500;
    }

    /* ── PROGRAM GRID (2 KOLOM) ── */
    .cp-program-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .cp-card {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .cp-card:hover {
        border-color: #14b8a6;
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.08);
        transform: translateY(-4px);
    }

    .cp-card-img {
        width: 100%;
        height: 140px;
        overflow: hidden;
        position: relative;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cp-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .cp-card:hover .cp-card-img img {
        transform: scale(1.03);
    }

    .cp-badge-price {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: #ffffff;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 10px;
        backdrop-filter: blur(6px);
        letter-spacing: 0.02em;
    }

    .cp-card-content {
        padding: 16px 18px 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .cp-card-header {
        margin-bottom: 2px;
    }

    .cp-id {
        font-size: 10px;
        color: #94a3b8;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .cp-class-name {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin: 4px 0 0;
        line-height: 1.3;
    }

    .cp-class-desc {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
        margin: 6px 0 0;
        font-weight: 400;
    }

    /* ── STATS MINI ── */
    .cp-stats-mini {
        display: flex;
        gap: 20px;
        background: #f8fafc;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #eef2f6;
        margin-top: auto;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-item strong {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        display: block;
        line-height: 1.2;
    }

    .stat-item span {
        font-size: 10px;
        color: #6b7280;
        font-weight: 600;
        display: block;
    }

    /* ── EMPTY STATE ── */
    .cp-empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px dashed #e5e7eb;
    }

    .cp-empty-state strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    /* ── PAGINATION ── */
    .cp-pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .cp-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .cp-program-grid {
            grid-template-columns: repeat(2, 1fr);
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

        .cp-main-panel {
            padding: 16px;
        }

        .cp-program-grid {
            grid-template-columns: 1fr;
        }

        .cp-card-img {
            height: 120px;
        }

        .cp-stats-mini {
            flex-direction: column;
            gap: 6px;
        }

        .cp-class-name {
            font-size: 15px;
        }
    }

    @media (max-width: 480px) {
        .cp-card-img {
            height: 100px;
        }

        .cp-card-content {
            padding: 12px 14px 14px;
        }

        .cp-stats-mini {
            padding: 8px 12px;
        }

        .stat-item strong {
            font-size: 13px;
        }
    }
</style>
@endsection
