@extends('layouts.spekta')

@section('title', 'Banner Management')
@section('subtitle', 'Kelola banner carousel homepage mobile')

@section('content')
@php
    $bannersCollection = method_exists($banners, 'getCollection') ? $banners->getCollection() : collect($banners);
    $totalBanners = method_exists($banners, 'total') ? $banners->total() : $bannersCollection->count();
    $activeBanners = $bannersCollection->where('is_active', true)->count();
    $inactiveBanners = max($totalBanners - $activeBanners, 0);
@endphp

<div class="bn-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Banner Management</h1>
            <p>Kelola banner carousel untuk homepage mobile Spekta Academy.</p>
        </div>
        <div class="welcome-action">
            <a href="{{ route('admin.banners.create') }}" class="bn-primary-btn-teal">
                Tambah Banner Baru
            </a>
        </div>
    </section>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="bn-alert success">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── STATS GRID ── --}}
    <section class="bn-stats">
        <div class="bn-stat-card card-blue">
            <div class="bn-stat-info">
                <p>Total Banner</p>
                <h2>{{ number_format($totalBanners) }}</h2>
            </div>
        </div>

        <div class="bn-stat-card card-teal">
            <div class="bn-stat-info">
                <p>Banner Aktif</p>
                <h2>{{ number_format($activeBanners) }}</h2>
            </div>
            @if($activeBanners > 0)
                <span class="bn-pulse-dot"></span>
            @endif
        </div>

        <div class="bn-stat-card card-orange">
            <div class="bn-stat-info">
                <p>Nonaktif</p>
                <h2>{{ number_format($inactiveBanners) }}</h2>
            </div>
        </div>
    </section>

    {{-- ── MAIN CONTENT ── --}}
    <section class="bn-main-grid">

        <div class="bn-list-panel">
            <div class="bn-panel-heading">
                <div>
                    <h2>Daftar Banner</h2>
                    <p>Atur banner promosi yang muncul pada aplikasi mobile.</p>
                </div>
            </div>

            <div class="bn-banner-list">
                @forelse($banners as $banner)
                    @php
                        $imageUrl = $banner->image_url ?? null;

                        if (!$imageUrl && !empty($banner->image)) {
                            if (\Illuminate\Support\Str::startsWith($banner->image, ['http://', 'https://'])) {
                                $imageUrl = $banner->image;
                            } elseif (\Illuminate\Support\Str::startsWith($banner->image, ['storage/'])) {
                                $imageUrl = asset($banner->image);
                            } else {
                                $imageUrl = asset('storage/' . ltrim($banner->image, '/'));
                            }
                        } elseif ($imageUrl && !\Illuminate\Support\Str::startsWith($imageUrl, ['http://', 'https://'])) {
                            $imageUrl = asset($imageUrl);
                        }
                    @endphp

                    <article class="bn-banner-card">
                        <div class="bn-banner-image">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $banner->title ?? 'Banner' }}">
                            @else
                                <div class="bn-no-image">
                                    <span>No Image</span>
                                </div>
                            @endif

                            <div class="bn-image-badge">
                                #{{ $banner->order_position ?? 0 }}
                            </div>
                        </div>

                        <div class="bn-banner-content">
                            <div class="bn-banner-title-row">
                                <div>
                                    <h3>{{ $banner->title ?? 'Tanpa Judul' }}</h3>
                                    <p>{{ $banner->description ? \Illuminate\Support\Str::limit($banner->description, 145) : 'Tidak ada deskripsi banner.' }}</p>
                                </div>

                                @if($banner->is_active)
                                    <span class="bn-status active">Aktif</span>
                                @else
                                    <span class="bn-status inactive">Nonaktif</span>
                                @endif
                            </div>

                            <div class="bn-meta-grid">
                                <div>
                                    <span>Link Tujuan</span>
                                    <strong>{{ $banner->link ?: 'Tidak ada link' }}</strong>
                                </div>

                                <div>
                                    <span>Urutan</span>
                                    <strong>{{ $banner->order_position ?? 0 }}</strong>
                                </div>

                                <div>
                                    <span>Tanggal Dibuat</span>
                                    <strong>{{ $banner->created_at?->translatedFormat('d M Y') ?? '-' }}</strong>
                                </div>
                            </div>

                            <div class="bn-actions">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="edit">Edit</a>

                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Hapus banner ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bn-empty">
                        <strong>Belum ada banner.</strong>
                        <p>Tambahkan banner pertama untuk carousel homepage mobile Anda.</p>
                        <a href="{{ route('admin.banners.create') }}" class="bn-primary-btn-teal" style="margin-top: 15px; display: inline-flex;">Tambah Banner Pertama</a>
                    </div>
                @endforelse
            </div>

            @if(method_exists($banners, 'hasPages') && $banners->hasPages())
                <div class="bn-pagination">
                    {{ $banners->links() }}
                </div>
            @endif
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

    .bn-page {
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

    /* ── TOMBOL TAMBAH BANNER TEAL ── */
    .bn-primary-btn-teal {
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

    .bn-primary-btn-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .bn-primary-btn-teal:active {
        transform: scale(0.97);
    }

    /* ── ALERTS ── */
    .bn-alert {
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
    }
    .bn-alert.success {
        background: #e6f7ed;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    /* ── STATS ── */
    .bn-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .bn-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .bn-stat-card:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    /* ── WARNA URUTAN SESUAI DASHBOARD (BIRU, TEAL, KUNING) ── */
    .bn-stat-card.card-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .bn-stat-card.card-blue:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
    }

    .bn-stat-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .bn-stat-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .bn-stat-card.card-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    .bn-stat-card.card-orange:hover {
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
    }

    .bn-stat-card::after {
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

    .bn-stat-card::before {
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

    .bn-stat-info {
        position: relative;
        z-index: 1;
    }

    .bn-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        color: rgba(255, 255, 255, 0.9);
    }

    .bn-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }

    .bn-pulse-dot {
        position: absolute;
        top: 14px; right: 14px;
        width: 6px; height: 6px;
        background: #14b8a6;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.7);
        animation: pulseTeal 1.5s infinite;
    }
    @keyframes pulseTeal {
        0% { box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(20, 184, 166, 0); }
        100% { box-shadow: 0 0 0 0 rgba(20, 184, 166, 0); }
    }

    /* ── MAIN PANEL ── */
    .bn-main-grid {
        display: block;
        margin-bottom: 24px;
    }

    .bn-list-panel {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .bn-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
        flex-wrap: wrap;
        gap: 15px;
    }

    .bn-panel-heading h2 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .bn-panel-heading p {
        margin: 0;
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ── BANNER LIST ── */
    .bn-banner-list {
        display: grid;
        gap: 16px;
    }

    .bn-banner-card {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 20px;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.25s ease;
    }

    .bn-banner-card:hover {
        border-color: #14b8a6;
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }

    /* ── IMAGE ── */
    .bn-banner-image {
        height: 150px;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
    }

    .bn-banner-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .bn-no-image {
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

    .bn-image-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 3px 10px;
        border-radius: 6px;
        background: rgba(31, 41, 55, 0.85);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        backdrop-filter: blur(4px);
    }

    /* ── CONTENT ── */
    .bn-banner-content {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .bn-banner-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
    }

    .bn-banner-title-row h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 15px;
        font-weight: 700;
    }

    .bn-banner-title-row p {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.5;
    }

    /* ── STATUS ── */
    .bn-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 6px;
    }

    .bn-status.active {
        background: #e6f7ed;
        color: #15803d;
    }

    .bn-status.inactive {
        background: #f3f4f6;
        color: #9e9e9e;
    }

    /* ── META GRID ── */
    .bn-meta-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 12px;
        padding: 12px;
        border-radius: 10px;
        background: #f9fafb;
        margin: 12px 0;
    }

    .bn-meta-grid span {
        display: block;
        color: #6b7280;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }

    .bn-meta-grid strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── ACTIONS ── */
    .bn-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .bn-actions form {
        margin: 0;
    }

    .bn-actions a,
    .bn-actions button {
        height: 32px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .bn-actions .edit {
        background: #dbeafe;
        color: #2563eb;
    }

    .bn-actions .edit:hover {
        background: #3b82f6;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .bn-actions .delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .bn-actions .delete:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* ── EMPTY STATE ── */
    .bn-empty {
        text-align: center;
        padding: 48px;
        background: #f9fafb;
        border-radius: 16px;
        border: 1px dashed #e5e7eb;
    }

    .bn-empty strong {
        display: block;
        font-size: 14px;
        color: #111827;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .bn-empty p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    .bn-pagination {
        margin-top: 20px;
    }

    /* ── RESPONSIVE ── */
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

        .bn-primary-btn-teal {
            width: 100%;
            justify-content: center;
        }

        .bn-stats {
            grid-template-columns: 1fr;
        }

        .bn-banner-card {
            grid-template-columns: 1fr;
        }

        .bn-banner-image {
            height: 160px;
        }

        .bn-meta-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .bn-panel-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .welcome-text h1 {
            font-size: 18px;
        }
    }
</style>
@endsection
