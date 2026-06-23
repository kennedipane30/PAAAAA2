@extends('layouts.spekta')

@section('title', 'Teacher Management')
@section('subtitle', 'Sistem Manajemen Data Pengajar Spekta Academy')

@section('content')
<div class="tp-page">

    {{-- ── 1. WELCOME CARD (Container Personal) ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Manajemen Pengajar</h1>
            <p>Kelola data pengajar Spekta Academy secara efisien.</p>
        </div>
        <div class="welcome-action">
            <a href="{{ route('admin.manajemen-pengajar.create') }}" class="tp-btn-primary-teal">
                Tambah Pengajar
            </a>
        </div>
    </section>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="tp-alert success">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── 2. MAIN PANEL ── --}}
    <section class="tp-main-grid">

        <div class="tp-table-panel">

            {{-- Top Control Bar --}}
            <div class="tp-table-top-bar">

                {{-- Total Pengajar Info --}}
                <div class="tp-total-stat">
                    <div class="tp-total-info">
                        <span class="tp-total-label">Total Pengajar</span>
                        <div class="tp-total-val-wrap">
                            <span class="tp-total-val">{{ number_format($totalPengajar ?? 0) }}</span>
                            <span class="tp-total-sub">terdaftar</span>
                        </div>
                    </div>
                </div>

                {{-- Toolbar / Search --}}
                <form method="GET" action="{{ route('admin.manajemen-pengajar.index') }}" class="tp-toolbar">
                    <div class="tp-search">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama pengajar, NIP, atau email..."
                        >
                    </div>

                    <button type="submit" class="tp-btn-search-teal">
                        Cari
                    </button>
                </form>

            </div>

            {{-- Table --}}
            <div class="tp-table-wrap">
                <table class="tp-table">
                    <thead>
                        <tr>
                            <th>Nama Pengajar</th>
                            <th>Bidang Ajar</th>
                            <th>Status</th>
                            <th>Kelas Aktif</th>
                            <th>Tanggal Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            @php
                                $teacherAssignments = ($assignmentMap ?? collect())->get($teacher->usersID, collect());
                                $subjectList = $teacherAssignments->pluck('subject_name')->filter()->unique()->values();
                                $classCount = $teacherAssignments->pluck('class_id')->filter()->unique()->count();

                                if (($scheduleCountMap ?? collect())->has($teacher->usersID)) {
                                    $classCount = max($classCount, (int) $scheduleCountMap[$teacher->usersID]);
                                }

                                $initial = strtoupper(substr($teacher->name, 0, 1));
                                $avatarColors = ['#e53935','#2ea8ab','#c5352c','#9e9e9e','#1f2937'];
                                $avatarBg = $avatarColors[crc32($teacher->name) % count($avatarColors)];
                            @endphp
                            <tr>
                                {{-- Nama Pengajar --}}
                                <td>
                                    <div class="tp-teacher">
                                        <div class="tp-avatar" style="background:{{ $avatarBg }}">
                                            {{ $initial }}
                                        </div>
                                        <div class="tp-teacher-info">
                                            <strong>{{ $teacher->name }}</strong>
                                            <span>NIP: {{ str_pad($teacher->usersID, 6, '0', STR_PAD_LEFT) }}</span>
                                            <small>{{ $teacher->email }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Bidang Ajar --}}
                                <td>
                                    @if($subjectList->count() > 0)
                                        <div class="tp-subject-list">
                                            @foreach($subjectList->take(3) as $subject)
                                                <span class="tp-subject-badge">{{ $subject }}</span>
                                            @endforeach
                                            @if($subjectList->count() > 3)
                                                <span class="tp-subject-badge more">+{{ $subjectList->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="tp-muted">Belum ditugaskan</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($teacher->is_verified)
                                        <span class="tp-status active">
                                            AKTIF
                                        </span>
                                    @else
                                        <span class="tp-status inactive">
                                            NONAKTIF
                                        </span>
                                    @endif
                                </td>

                                {{-- Kelas Aktif --}}
                                <td class="tp-class-count">
                                    @if($classCount > 0)
                                        <span class="tp-class-badge">{{ $classCount }} kelas</span>
                                    @else
                                        <span class="tp-muted">—</span>
                                    @endif
                                </td>

                                {{-- Tanggal --}}
                                <td class="tp-date">
                                    {{ $teacher->created_at?->translatedFormat('d M Y') ?? '-' }}
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <div class="tp-actions">
                                        <a href="{{ route('admin.manajemen-pengajar.edit', $teacher->usersID) }}" class="tp-act edit" title="Edit">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.manajemen-pengajar.destroy', $teacher->usersID) }}" method="POST" onsubmit="return confirm('Hapus akun pengajar ini?')" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="tp-act delete" title="Hapus">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="tp-empty">
                                        <strong>Belum ada data pengajar</strong>
                                        <span>Tambahkan akun pengajar melalui tombol Tambah Pengajar.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="tp-pagination">
                <p>
                    Menampilkan
                    <strong>{{ $teachers->firstItem() ?? 0 }}</strong>–<strong>{{ $teachers->lastItem() ?? 0 }}</strong>
                    dari <strong>{{ number_format($teachers->total() ?? 0) }}</strong> pengajar
                </p>

                @if(method_exists($teachers, 'hasPages') && $teachers->hasPages())
                    <div class="tp-pages">
                        @if($teachers->onFirstPage())
                            <span class="tp-page-btn disabled">‹</span>
                        @else
                            <a href="{{ $teachers->previousPageUrl() }}" class="tp-page-btn">‹</a>
                        @endif

                        @foreach(range(1, $teachers->lastPage()) as $page)
                            @if($page == 1 || $page == $teachers->lastPage() || abs($page - $teachers->currentPage()) <= 1)
                                <a href="{{ $teachers->url($page) }}"
                                   class="tp-page-btn {{ $page == $teachers->currentPage() ? 'active' : '' }}">
                                    {{ $page }}
                                </a>
                            @elseif(abs($page - $teachers->currentPage()) == 2)
                                <span class="tp-page-dots">…</span>
                            @endif
                        @endforeach

                        @if($teachers->hasMorePages())
                            <a href="{{ $teachers->nextPageUrl() }}" class="tp-page-btn">›</a>
                        @else
                            <span class="tp-page-btn disabled">›</span>
                        @endif
                    </div>
                @endif
            </div>

        </div>

    </section>

</div>

<style>
/* ── Base ─────────────────────────────────────────────────── */
.tp-page {
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
    min-width: 160px;
}

/* ── TOMBOL TAMBAH PENGAJAR TEAL ── */
.tp-btn-primary-teal {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 42px;
    padding: 0 20px;
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: #fff;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    letter-spacing: 0.02em;
}
.tp-btn-primary-teal:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    color: #fff;
}
.tp-btn-primary-teal:active {
    transform: scale(0.97);
}

/* Alert */
.tp-alert {
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 24px;
    display: flex;
    gap: 10px;
    align-items: center;
    font-size: 13px;
    font-weight: 700;
}
.tp-alert.success {
    background: #e6f7ed;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

/* ── Main Grid & Table Panel ──────────────────────────────── */
.tp-main-grid {
    display: block;
    margin-bottom: 22px;
}

.tp-table-panel {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(15,23,42,.01);
}

/* Top Control Bar */
.tp-table-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    margin-bottom: 22px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f3f4f6;
    flex-wrap: wrap;
}

.tp-total-stat {
    display: flex;
    align-items: center;
    gap: 12px;
}

.tp-total-info {
    display: flex;
    flex-direction: column;
}
.tp-total-label {
    font-size: 10px;
    color: #9e9e9e;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 2px;
}
.tp-total-val-wrap {
    display: flex;
    align-items: baseline;
    gap: 4px;
}
.tp-total-val {
    font-size: 24px;
    font-weight: 900;
    color: #111827;
    line-height: 1;
}
.tp-total-sub {
    font-size: 11px;
    color: #9e9e9e;
    font-weight: 600;
}

/* toolbar / search */
.tp-toolbar {
    display: flex;
    gap: 10px;
    flex: 1;
    max-width: 450px;
    justify-content: flex-end;
}

.tp-search {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.tp-search input {
    width: 100%;
    height: 40px;
    padding: 0 14px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #f9fafb;
    font-size: 12px;
    font-weight: 500;
    color: #1f2937;
    outline: none;
    transition: all 0.2s ease;
}
.tp-search input:focus {
    background: #fff;
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
}
.tp-search input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

/* ── TOMBOL CARI TEAL ── */
.tp-btn-search-teal {
    height: 40px;
    padding: 0 18px;
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    transition: all 0.25s ease;
    box-shadow: 0 3px 12px rgba(20, 184, 166, 0.2);
    letter-spacing: 0.02em;
}
.tp-btn-search-teal:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(20, 184, 166, 0.3);
}
.tp-btn-search-teal:active {
    transform: scale(0.97);
}

/* table */
.tp-table-wrap { overflow-x: auto; border-radius: 12px; }

.tp-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.tp-table thead tr {
    background: #f9fafb;
}

.tp-table th {
    padding: 10px 14px;
    color: #6b7280;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid #e5e7eb;
}

.tp-table td {
    padding: 12px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.tp-table tbody tr:last-child td { border-bottom: none; }
.tp-table tbody tr { transition: background-color 0.15s ease; }
.tp-table tbody tr:hover { background: #fafbfc; }

/* teacher cell */
.tp-teacher {
    display: flex;
    align-items: center;
    gap: 10px;
}

.tp-avatar {
    width: 34px;
    height: 34px;
    flex-shrink: 0;
    border-radius: 99px;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 13px;
    font-weight: 900;
    box-shadow: 0 3px 8px rgba(0,0,0,0.06);
}

.tp-teacher-info strong {
    display: block;
    color: #111827;
    font-size: 13px;
    font-weight: 800;
}

.tp-teacher-info span,
.tp-teacher-info small {
    display: block;
    color: #9e9e9e;
    font-size: 10px;
    font-weight: 600;
    margin-top: 1px;
}

/* subject list */
.tp-subject-list {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.tp-subject-badge {
    display: inline-flex;
    align-items: center;
    height: 22px;
    padding: 0 8px;
    background: rgba(20, 184, 166, 0.1);
    color: #0d9488;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}
.tp-subject-badge.more {
    background: #f3f4f6;
    color: #6b7280;
}

/* status */
.tp-status {
    display: inline-flex;
    align-items: center;
    height: 22px;
    padding: 0 10px;
    border-radius: 6px;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
}

.tp-status.active {
    background: #e6f7ed;
    color: #16a34a;
}
.tp-status.inactive {
    background: #fee2e2;
    color: #dc2626;
}

/* class badge */
.tp-class-badge {
    display: inline-flex;
    align-items: center;
    height: 22px;
    padding: 0 8px;
    background: #f3f4f6;
    color: #4b5563;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}

/* date */
.tp-date {
    color: #6b7280;
    font-size: 11px;
    font-weight: 600;
}

/* muted */
.tp-muted { color: #d1d5db; font-size: 14px; }

/* actions */
.tp-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: flex-start;
}

.tp-act {
    padding: 4px 12px;
    border-radius: 6px;
    border: none;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.tp-act:hover {
    transform: translateY(-1px);
}

.tp-act.edit {
    background: #e0f2fe;
    color: #0369a1;
}
.tp-act.edit:hover {
    background: #bae6fd;
    color: #02507d;
}

.tp-act.delete {
    background: #fee2e2;
    color: #b91c1c;
}
.tp-act.delete:hover {
    background: #fecaca;
    color: #991b1b;
}

/* empty */
.tp-empty { padding: 36px 18px; text-align: center; }
.tp-empty strong { display: block; color: #111827; font-size: 14px; font-weight: 800; margin-bottom: 4px; }
.tp-empty span { display: block; color: #9e9e9e; font-size: 12px; font-weight: 600; }

/* pagination */
.tp-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
    margin-top: 4px;
    flex-wrap: wrap;
}
.tp-pagination p { margin: 0; font-size: 11px; color: #6b7280; font-weight: 700; }
.tp-pagination p strong { color: #111827; font-weight: 800; }

.tp-pages { display: flex; align-items: center; gap: 4px; }
.tp-page-btn {
    min-width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}
.tp-page-btn:hover:not(.disabled):not(.active) {
    border-color: #14b8a6;
    color: #14b8a6;
    background: rgba(20, 184, 166, 0.04);
}
.tp-page-btn.active {
    background: #1f2937;
    color: #fff;
    border-color: #1f2937;
}
.tp-page-btn.disabled { opacity: .4; pointer-events: none; cursor: default; }
.tp-page-dots { color: #9ca3af; font-size: 12px; }

/* Responsive Layout */
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

    .tp-btn-primary-teal {
        width: 100%;
        justify-content: center;
    }

    .tp-table-top-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .tp-toolbar {
        width: 100%;
        max-width: none;
    }

    .tp-pagination {
        flex-direction: column;
        align-items: flex-start;
    }

    .welcome-text h1 {
        font-size: 18px;
    }
}
</style>
@endsection
