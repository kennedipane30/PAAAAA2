@extends('layouts.spekta')

@section('title', 'Management - Announcement')
@section('subtitle', 'Sistem Manajemen Pengumuman Spekta Academy')

@section('content')
@php
    $announcementCollection = method_exists($announcements, 'getCollection') ? $announcements->getCollection() : collect($announcements);
    $totalAnnouncement = method_exists($announcements, 'total') ? $announcements->total() : $announcementCollection->count();

    $todayAnnouncements = $announcementCollection->filter(function($item) {
        return $item->created_at && $item->created_at->isToday();
    })->count();
@endphp

<div class="an-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Management Announcement</h1>
            <p>Buat, kelola, dan publikasikan pengumuman untuk pengguna Spekta Academy.</p>
        </div>
        <div class="welcome-action">
            <button type="button" class="an-primary-btn-teal" onclick="toggleAnnouncementForm()">
                <span id="toggle-text">Buat Pengumuman</span>
            </button>
        </div>
    </section>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="an-alert success">
            <div>
                <strong>Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="an-alert error">
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ── STATS GRID ── --}}
    <section class="an-stats">
        <div class="an-stat-card card-blue">
            <div class="an-stat-info">
                <p>Total Pengumuman</p>
                <h2>{{ number_format($totalAnnouncement) }}</h2>
            </div>
        </div>

        <div class="an-stat-card card-teal">
            <div class="an-stat-info">
                <p>Bulan Ini</p>
                <h2>{{ number_format($announcementCollection->filter(fn($item) => $item->created_at && $item->created_at->isCurrentMonth())->count()) }}</h2>
            </div>
        </div>

        <div class="an-stat-card card-orange">
            <div class="an-stat-info">
                <p>Hari Ini</p>
                <h2>{{ number_format($todayAnnouncements) }}</h2>
            </div>
            @if($todayAnnouncements > 0)
                <span class="an-pulse-dot"></span>
            @endif
        </div>
    </section>

    {{-- ── MAIN GRID (FORM & PREVIEW COLLAPSIBLE) ── --}}
    <section class="an-main-grid" id="announcementFormSection">

        <div class="an-form-panel">
            <div class="an-panel-heading">
                <div>
                    <h2>Create New Announcement</h2>
                    <p>Isi judul, gambar sampul, dan deskripsi pengumuman yang akan ditampilkan.</p>
                </div>
            </div>

            <form action="{{ route('admin.announcement.store') }}" method="POST" enctype="multipart/form-data" class="an-form">
                @csrf

                <div class="an-input-group full">
                    <label>Announcement Title</label>
                    <div class="an-input-wrap">
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Enter headline..." required>
                    </div>
                </div>

                <div class="an-input-group full">
                    <label>Cover Image</label>
                    <div class="an-input-wrap">
                        <input type="file" name="image" id="announcementImageInput" accept="image/*" required>
                    </div>
                </div>

                <div class="an-input-group full">
                    <label>Full Description</label>
                    <div class="an-input-wrap no-icon">
                        <textarea name="description" rows="5" placeholder="Write the announcement details here..." required>{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="an-form-action">
                    <button type="submit" class="an-submit-teal">
                        Publish Announcement Now
                    </button>
                </div>
            </form>
        </div>

        <aside class="an-preview-panel">
            <div class="an-panel-heading">
                <h2>Preview Cover</h2>
                <p>Pratinjau gambar pengumuman yang akan diunggah.</p>
            </div>

            <div class="an-preview-image" id="announcementPreviewBox">
                <div class="an-preview-empty">
                    <span>Preview gambar akan tampil di sini</span>
                </div>
            </div>

            <div class="an-preview-note">
                <span>Gunakan gambar yang jelas agar pengumuman terlihat menarik di aplikasi.</span>
            </div>
        </aside>

    </section>

    {{-- ── LIST PENGUMUMAN ── --}}
    <section class="an-list-panel">
        <div class="an-panel-heading">
            <div>
                <h2>Daftar Pengumuman</h2>
                <p>Kelola semua pengumuman yang sudah dipublikasikan.</p>
            </div>
        </div>

        <div class="an-card-grid">
            @forelse($announcements as $row)
                <article class="an-card">
                    <div class="an-card-image">
                        @if($row->image)
                            <img src="{{ asset('storage/' . $row->image) }}" alt="{{ $row->title }}">
                        @else
                            <div class="an-no-image">
                                <span>No Image</span>
                            </div>
                        @endif

                        <div class="an-date-badge">
                            {{ $row->created_at?->translatedFormat('d M Y') ?? '-' }}
                        </div>
                    </div>

                    <div class="an-card-body">
                        <h3>{{ $row->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($row->description, 120) }}</p>

                        <div class="an-card-meta">
                            <span>{{ $row->created_at?->diffForHumans() ?? '-' }}</span>
                        </div>

                        <div class="an-actions">
                            <a href="{{ route('admin.announcement.edit', $row->announcement_id) }}" class="edit">Edit</a>

                            <form action="{{ route('admin.announcement.destroy', $row->announcement_id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete">Delete</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="an-empty">
                    <strong>Belum ada pengumuman.</strong>
                    <p>Buat pengumuman pertama Anda melalui form di atas.</p>
                </div>
            @endforelse
        </div>

        @if(method_exists($announcements, 'hasPages') && $announcements->hasPages())
            <div class="an-pagination">
                {{ $announcements->links() }}
            </div>
        @endif
    </section>

</div>

<script>
    function toggleAnnouncementForm() {
        const formSection = document.getElementById('announcementFormSection');
        const toggleText = document.getElementById('toggle-text');

        if (formSection.classList.contains('show')) {
            formSection.classList.remove('show');
            toggleText.innerText = "Buat Pengumuman";
        } else {
            formSection.classList.add('show');
            toggleText.innerText = "Sembunyikan Form";
            formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const announcementImageInput = document.getElementById('announcementImageInput');
        const announcementPreviewBox = document.getElementById('announcementPreviewBox');

        if (announcementImageInput && announcementPreviewBox) {
            announcementImageInput.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    announcementPreviewBox.innerHTML = `
                        <div class="an-preview-empty">
                            <span>Preview gambar akan tampil di sini</span>
                        </div>`;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    announcementPreviewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Announcement" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">`;
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>

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

    .an-page {
        width: 100%;
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

    /* ── TOMBOL BUAT PENGUMUMAN TEAL ── */
    .an-primary-btn-teal {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 42px;
        padding: 0 20px;
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
        cursor: pointer;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .an-primary-btn-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .an-primary-btn-teal:active {
        transform: scale(0.97);
    }

    /* ── ALERTS ── */
    .an-alert {
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
    }
    .an-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }
    .an-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .an-alert strong { display: block; margin-bottom: 2px; font-weight: 700;}
    .an-alert ul { margin: 4px 0 0; padding-left: 20px; }

    /* ── STATS ── */
    .an-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .an-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .an-stat-card:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .an-stat-card.card-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .an-stat-card.card-blue:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
    }

    .an-stat-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .an-stat-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .an-stat-card.card-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    .an-stat-card.card-orange:hover {
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
    }

    .an-stat-card::after {
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

    .an-stat-card::before {
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

    .an-stat-info {
        position: relative;
        z-index: 1;
    }

    .an-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        color: rgba(255, 255, 255, 0.9);
    }

    .an-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }

    .an-pulse-dot {
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

    /* ── MAIN GRID ── */
    .an-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 24px;
        align-items: start;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        margin-bottom: 0;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin-bottom 0.4s ease;
    }

    .an-main-grid.show {
        max-height: 1000px;
        opacity: 1;
        margin-bottom: 24px;
    }

    .an-form-panel, .an-preview-panel {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .an-preview-panel {
        position: sticky;
        top: 24px;
    }

    .an-panel-heading {
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .an-panel-heading h2 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .an-panel-heading p {
        margin: 0;
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ── FORM ── */
    .an-form {
        display: grid;
        gap: 16px;
    }

    .an-input-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .an-input-wrap {
        position: relative;
        display: flex;
    }

    .an-input-wrap input,
    .an-input-wrap textarea {
        width: 100%;
        padding: 0 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        font-size: 12px;
        color: #111827;
        font-family: inherit;
        outline: none;
        transition: all 0.25s ease;
        font-weight: 500;
    }

    .an-input-wrap input {
        height: 44px;
    }

    .an-input-wrap input[type="file"] {
        padding-top: 10px;
    }

    .an-input-wrap.no-icon textarea {
        padding: 12px 14px;
        resize: vertical;
        min-height: 100px;
    }

    .an-input-wrap input:focus,
    .an-input-wrap textarea:focus {
        background: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .an-form-action {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    /* ── TOMBOL PUBLISH TEAL ── */
    .an-submit-teal {
        height: 44px;
        padding: 0 28px;
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 0.02em;
    }

    .an-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .an-submit-teal:active {
        transform: scale(0.97);
    }

    /* ── PREVIEW ── */
    .an-preview-image {
        width: 100%;
        height: 180px;
        border-radius: 12px;
        border: 1px dashed #9e9e9e;
        background: #f9fafb;
        display: grid;
        place-items: center;
        margin-bottom: 16px;
        overflow: hidden;
    }

    .an-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .an-preview-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
    }

    .an-preview-note {
        padding: 12px 16px;
        border-radius: 10px;
        background: #f0fdf4;
        color: #6b7280;
        font-size: 11px;
        font-weight: 500;
        line-height: 1.5;
    }

    /* ── LIST PANEL ── */
    .an-list-panel {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .an-card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .an-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
    }

    .an-card:hover {
        border-color: #14b8a6;
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }

    .an-card-image {
        height: 150px;
        background: #f3f4f6;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid #e5e7eb;
    }

    .an-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .an-no-image {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
    }

    .an-date-badge {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(31, 41, 55, 0.85);
        color: #ffffff;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        backdrop-filter: blur(4px);
    }

    .an-card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .an-card-body h3 {
        margin: 0 0 6px;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        line-height: 1.4;
    }

    .an-card-body p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.6;
        flex-grow: 1;
        font-weight: 500;
    }

    .an-card-meta {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #f3f4f6;
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
    }

    /* ── ACTIONS ── */
    .an-actions {
        display: flex;
        gap: 8px;
        margin-top: 14px;
    }

    .an-actions form {
        flex: 1;
        margin: 0;
    }

    .an-actions a,
    .an-actions button {
        height: 32px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.2s ease;
        padding: 0 14px;
    }

    .an-actions .edit {
        background: #dbeafe;
        color: #2563eb;
    }

    .an-actions .edit:hover {
        background: #3b82f6;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .an-actions .delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .an-actions .delete:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* ── EMPTY STATE ── */
    .an-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px dashed #e5e7eb;
    }

    .an-empty strong {
        display: block;
        font-size: 14px;
        color: #111827;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .an-empty p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    .an-pagination {
        margin-top: 20px;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .an-card-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 900px) {
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

        .an-primary-btn-teal {
            width: 100%;
            justify-content: center;
        }

        .an-stats {
            grid-template-columns: 1fr;
        }

        .an-main-grid {
            grid-template-columns: 1fr;
        }

        .an-preview-panel {
            position: static;
        }

        .an-card-grid {
            grid-template-columns: 1fr;
        }

        .an-form-action {
            justify-content: flex-start;
        }

        .an-submit-teal {
            width: 100%;
            justify-content: center;
        }

        .welcome-text h1 {
            font-size: 18px;
        }
    }
</style>
@endsection
