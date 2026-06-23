@extends('layouts.spekta')

@section('title', 'Student Management')
@section('subtitle', 'Sistem Manajemen Data Siswa Spekta Academy')

@section('content')
<div class="ss-page">

    {{-- ── 1. WELCOME CARD (Container Personal) ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Manajemen Siswa</h1>
            <p>Kelola data siswa Spekta Academy secara efisien berdasarkan data pendaftaran dari aplikasi.</p>
        </div>
    </section>

    {{-- ── 2. STAT CARDS (WARNA FULL SEPERTI DASHBOARD) ── --}}
    <section class="ss-stats">

        <!-- Card: Total Siswa -->
        <div class="ss-stat-card card-blue">
            <div class="ss-stat-info">
                <p class="ss-stat-label">Total Siswa</p>
                <h2 class="ss-stat-val">{{ number_format($totalSiswa ?? 0) }}</h2>
            </div>
        </div>

        <!-- Card: Siswa Aktif -->
        <div class="ss-stat-card card-teal">
            <div class="ss-stat-info">
                <p class="ss-stat-label">Siswa Aktif</p>
                <h2 class="ss-stat-val">{{ number_format($siswaAktif ?? 0) }}</h2>
            </div>
        </div>

        <!-- Card: Siswa Baru -->
        <div class="ss-stat-card card-orange">
            <div class="ss-stat-info">
                <p class="ss-stat-label">Siswa Baru Bulan Ini</p>
                <h2 class="ss-stat-val">{{ number_format($siswaBaruBulanIni ?? 0) }}</h2>
            </div>
        </div>

    </section>

    {{-- ── 3. MAIN GRID ── --}}
    <section class="ss-main-grid">

        <div class="ss-table-panel">

            {{-- Toolbar Pencarian --}}
            <form method="GET" action="{{ route('admin.siswa.index') }}" class="ss-toolbar">
                <div class="ss-search">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama siswa, NIS, atau email..."
                    >
                </div>

                <button type="submit" class="ss-btn-search-teal">
                    Cari
                </button>
            </form>

            {{-- Tabel Siswa --}}
            <div class="ss-table-wrap">
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $s)
                            @php
                                $student         = $s->student;
                                $latestEnrollment= ($latestEnrollmentMap ?? collect())->get($s->usersID);
                                $activeClass     = $latestEnrollment?->class;
                                $status          = $latestEnrollment?->status ?? 'registered';

                                $statusMap = [
                                    'active'     => ['label' => 'Aktif',      'cls' => 'active'],
                                    'pending'    => ['label' => 'Pending',    'cls' => 'pending'],
                                    'expired'    => ['label' => 'Expired',    'cls' => 'expired'],
                                    'registered' => ['label' => 'Registered', 'cls' => 'registered'],
                                ];
                                $st = $statusMap[$status] ?? $statusMap['registered'];

                                $initial = strtoupper(substr($s->name, 0, 1));
                                $avatarColors = ['#e53935','#2ea8ab','#c5352c','#9e9e9e','#1f2937'];
                                $avatarBg = $avatarColors[crc32($s->name) % count($avatarColors)];
                            @endphp
                            <tr>
                                {{-- Nama & Profil Siswa --}}
                                <td>
                                    <div class="ss-student">
                                        <div class="ss-avatar" style="background:{{ $avatarBg }}">
                                            {{ $initial }}
                                        </div>
                                        <div class="ss-student-info">
                                            <strong>{{ $s->name }}</strong>
                                            <span>NIS: {{ $student?->national_id_number ?? '-' }}</span>
                                            <small>{{ $s->email }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kelas --}}
                                <td>
                                    @if($activeClass)
                                        <span class="ss-class-badge">Kelas {{ $activeClass->class_id }}</span>
                                    @else
                                        <span class="ss-muted">—</span>
                                    @endif
                                </td>

                                {{-- Program --}}
                                <td>
                                    <span class="ss-program-name">{{ $activeClass?->program_name ?? '—' }}</span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span class="ss-status {{ $st['cls'] }}">
                                        {{ $st['label'] }}
                                    </span>
                                </td>

                                {{-- Tanggal Daftar --}}
                                <td class="ss-date">
                                    {{ $s->created_at?->translatedFormat('d M Y') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="ss-empty">
                                        <strong>Belum ada data siswa</strong>
                                        <span>Data siswa akan muncul setelah siswa mendaftar melalui aplikasi.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Navigasi Halaman (Pagination) --}}
            <div class="ss-pagination">
                <p>
                    Menampilkan
                    <strong>{{ $siswas->firstItem() ?? 0 }}</strong>–<strong>{{ $siswas->lastItem() ?? 0 }}</strong>
                    dari <strong>{{ number_format($siswas->total() ?? 0) }}</strong> siswa
                </p>

                @if(method_exists($siswas, 'hasPages') && $siswas->hasPages())
                    <div class="ss-pages">
                        @if($siswas->onFirstPage())
                            <span class="ss-page-btn disabled">‹</span>
                        @else
                            <a href="{{ $siswas->previousPageUrl() }}" class="ss-page-btn">‹</a>
                        @endif

                        @foreach(range(1, $siswas->lastPage()) as $page)
                            @if($page == 1 || $page == $siswas->lastPage() || abs($page - $siswas->currentPage()) <= 1)
                                <a href="{{ $siswas->url($page) }}"
                                   class="ss-page-btn {{ $page == $siswas->currentPage() ? 'active' : '' }}">
                                    {{ $page }}
                                </a>
                            @elseif(abs($page - $siswas->currentPage()) == 2)
                                <span class="ss-page-dots">…</span>
                            @endif
                        @endforeach

                        @if($siswas->hasMorePages())
                            <a href="{{ $siswas->nextPageUrl() }}" class="ss-page-btn">›</a>
                        @else
                            <span class="ss-page-btn disabled">›</span>
                        @endif
                    </div>
                @endif
            </div>

        </div>

    </section>

</div>

<style>
/* ── Base ─────────────────────────────────────────────────── */
.ss-page {
    width: 100%;
    font-family: 'Montserrat', system-ui, sans-serif;
    color: #1f2937;
    animation: slideInUp 0.4s ease-out;
}

@keyframes slideInUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ── WELCOME CARD (Container Personal) ── */
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

/* ── STAT CARDS (WARNA FULL SEPERTI DASHBOARD) ── */
.ss-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0,1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.ss-stat-card {
    border-radius: 12px;
    padding: 18px 20px;
    color: #ffffff;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    position: relative;
    overflow: hidden;
}

.ss-stat-card:hover {
    transform: translateY(-3px) scale(1.01);
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}

/* Warna Full untuk Kartu */
.ss-stat-card.card-blue {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}
.ss-stat-card.card-blue:hover {
    box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
}

.ss-stat-card.card-teal {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
}
.ss-stat-card.card-teal:hover {
    box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
}

.ss-stat-card.card-orange {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}
.ss-stat-card.card-orange:hover {
    box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
}

/* Efek dekoratif pada card */
.ss-stat-card::after {
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

.ss-stat-card::before {
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

.ss-stat-info {
    position: relative;
    z-index: 1;
}

.ss-stat-label {
    margin: 0 0 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    opacity: 0.85;
    color: rgba(255, 255, 255, 0.9);
}

.ss-stat-val {
    margin: 0;
    font-size: 26px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
}

/* ── Main Grid ────────────────────────────────────────────── */
.ss-main-grid {
    display: block;
    margin-bottom: 18px;
}

/* ── Table Panel ──────────────────────────────────────────── */
.ss-table-panel {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 14px;
    padding: 14px 16px;
    box-shadow: 0 2px 8px rgba(15,23,42,.01);
}

/* toolbar */
.ss-toolbar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.ss-search {
    position: relative;
    flex: 1;
    min-width: 180px;
}
.ss-search input {
    width: 100%;
    height: 34px;
    padding: 0 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    font-size: 11px;
    font-weight: 500;
    color: #1f2937;
    outline: none;
    transition: all 0.2s ease;
}
.ss-search input:focus {
    background: #fff;
    border-color: #2ea8ab;
    box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.1);
}
.ss-search input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

/* ── TOMBOL CARI TEAL ── */
.ss-btn-search-teal {
    height: 34px;
    padding: 0 14px;
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    transition: all 0.25s ease;
    box-shadow: 0 3px 10px rgba(20, 184, 166, 0.2);
}
.ss-btn-search-teal:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 16px rgba(20, 184, 166, 0.3);
}
.ss-btn-search-teal:active {
    transform: scale(0.97);
}

/* table */
.ss-table-wrap { overflow-x: auto; border-radius: 10px; }

.ss-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

.ss-table thead tr {
    background: #f9fafb;
}

.ss-table th {
    padding: 8px 12px;
    color: #6b7280;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid #e5e7eb;
}

.ss-table td {
    padding: 10px 12px;
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.ss-table tbody tr:last-child td { border-bottom: none; }

.ss-table tbody tr:hover {
    background: #fafbfc;
}

/* student cell */
.ss-student {
    display: flex;
    align-items: center;
    gap: 8px;
}

.ss-avatar {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    border-radius: 99px;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.ss-student-info strong {
    display: block;
    color: #111827;
    font-size: 12px;
    font-weight: 700;
}

.ss-student-info span,
.ss-student-info small {
    display: block;
    color: #9e9e9e;
    font-size: 9px;
    font-weight: 500;
    margin-top: 1px;
}

/* class badge */
.ss-class-badge {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 8px;
    background: #f3f4f6;
    color: #4b5563;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
    white-space: nowrap;
}

/* program name */
.ss-program-name {
    font-size: 10px;
    font-weight: 600;
    color: #4b5563;
}

/* status */
.ss-status {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 8px;
    border-radius: 4px;
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
}

.ss-status.active     { background: #e6f7ed; color: #16a34a; }
.ss-status.pending    { background: #fff7ed; color: #c2410c; }
.ss-status.expired    { background: #fee2e2; color: #dc2626; }
.ss-status.registered { background: #e0f2fe; color: #0269a1; }

/* date */
.ss-date {
    color: #6b7280;
    font-size: 10px;
    font-weight: 600;
}

/* muted */
.ss-muted { color: #d1d5db; font-size: 12px; }

/* empty */
.ss-empty {
    padding: 28px 14px;
    text-align: center;
}
.ss-empty strong {
    display: block;
    color: #111827;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 4px;
}
.ss-empty span {
    display: block;
    color: #9e9e9e;
    font-size: 11px;
    font-weight: 500;
}

/* pagination */
.ss-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
    margin-top: 4px;
    flex-wrap: wrap;
}
.ss-pagination p {
    margin: 0;
    font-size: 10px;
    color: #6b7280;
    font-weight: 600;
}
.ss-pagination p strong { color: #111827; font-weight: 700; }

.ss-pages {
    display: flex;
    align-items: center;
    gap: 3px;
}
.ss-page-btn {
    min-width: 26px;
    height: 26px;
    display: grid;
    place-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #6b7280;
    font-size: 10px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}
.ss-page-btn:hover:not(.disabled):not(.active) {
    border-color: #2ea8ab;
    color: #2ea8ab;
    background: rgba(46, 168, 171, 0.04);
}
.ss-page-btn.active {
    background: #1f2937;
    color: #fff;
    border-color: #1f2937;
    box-shadow: 0 2px 6px rgba(31, 41, 55, 0.15);
}
.ss-page-btn.disabled { opacity: .4; pointer-events: none; }
.ss-page-dots { color: #9ca3af; font-size: 11px; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 1280px) {
    .ss-stats { grid-template-columns: repeat(2,1fr); }
}

@media (max-width: 768px) {
    .welcome-card {
        padding: 20px;
    }

    .welcome-text h1 {
        font-size: 18px;
    }

    .ss-stats { grid-template-columns: 1fr; }
    .ss-toolbar { flex-direction: column; }
    .ss-pagination { flex-direction: column; align-items: flex-start; }
    .ss-stat-val { font-size: 22px; }
    .ss-search { min-width: 100%; }
    .ss-btn-search-teal { width: 100%; justify-content: center; }
}
</style>
@endsection
