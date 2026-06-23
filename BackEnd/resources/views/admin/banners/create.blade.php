@extends('layouts.spekta')

@section('title', 'Tambah Banner')
@section('subtitle', 'Upload banner carousel homepage mobile')

@section('content')
<div class="bn-form-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Tambah Banner</h1>
            <p>Upload banner baru untuk carousel homepage mobile Spekta Academy.</p>
        </div>
        <div class="welcome-action">
            <a href="{{ route('admin.banners.index') }}" class="back-btn">
                Kembali
            </a>
        </div>
    </section>

    @if($errors->any())
        <div class="bn-form-alert error">
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

    {{-- ── FORM GRID ── --}}
    <section class="bn-form-grid">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="bn-form-card">
            @csrf

            <div class="bn-card-heading">
                <div>
                    <h2>Informasi Banner</h2>
                    <p>Lengkapi informasi banner agar tampil rapi di aplikasi mobile.</p>
                </div>
            </div>

            <div class="bn-input-group">
                <label>Title</label>
                <div class="bn-input-wrap">
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Promo Mei 2026">
                </div>
            </div>

            <div class="bn-input-group full">
                <label>Description</label>
                <div class="bn-input-wrap">
                    <textarea name="description" rows="4" placeholder="Tulis deskripsi singkat banner...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="bn-input-group">
                <label>Image</label>
                <div class="bn-input-wrap">
                    <input type="file" name="image" id="bannerImageInput" accept="image/*" required>
                </div>
            </div>

            <div class="bn-input-group">
                <label>Link</label>
                <div class="bn-input-wrap">
                    <input type="text" name="link" value="{{ old('link') }}" placeholder="/promo atau https://...">
                </div>
            </div>

            <div class="bn-input-group">
                <label>Order</label>
                <div class="bn-input-wrap">
                    <input type="number" name="order_position" value="{{ old('order_position', 0) }}" min="0">
                </div>
            </div>

            <div class="bn-switch-box">
                <label class="bn-switch">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <span></span>
                </label>
                <div>
                    <strong>Aktifkan Banner</strong>
                    <small>Banner aktif akan muncul pada carousel mobile.</small>
                </div>
            </div>

            <div class="bn-form-actions">
                <a href="{{ route('admin.banners.index') }}" class="bn-cancel-btn">Batal</a>
                <button type="submit" class="bn-submit-teal">Simpan Banner</button>
            </div>
        </form>

        <aside class="bn-preview-card">
            <div class="bn-preview-heading">
                <h3>Preview Banner</h3>
                <p>Pratinjau gambar yang akan diunggah.</p>
            </div>

            <div class="bn-preview-image" id="bannerPreviewBox">
                <div>
                    <span>Preview gambar akan tampil di sini</span>
                </div>
            </div>

            <div class="bn-preview-note">
                <span>Gunakan rasio gambar lebar seperti 16:9 agar banner terlihat maksimal di carousel.</span>
            </div>
        </aside>
    </section>
</div>

<script>
    const bannerImageInput = document.getElementById('bannerImageInput');
    const bannerPreviewBox = document.getElementById('bannerPreviewBox');

    if (bannerImageInput && bannerPreviewBox) {
        bannerImageInput.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (event) {
                bannerPreviewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Banner">`;
            };

            reader.readAsDataURL(file);
        });
    }
</script>

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

    .bn-form-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        padding-bottom: 40px;
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
    .bn-form-alert {
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
    }

    .bn-form-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .bn-form-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
        font-weight: 600;
    }

    /* ── FORM GRID ── */
    .bn-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 24px;
        align-items: start;
    }

    .bn-form-card,
    .bn-preview-card {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        padding: 24px;
    }

    .bn-card-heading {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
    }

    .bn-card-heading h2,
    .bn-preview-heading h3 {
        margin: 0 0 6px;
        font-size: 16px;
        font-weight: 800;
        color: #111827;
    }

    .bn-card-heading p,
    .bn-preview-heading p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ── FORM ── */
    .bn-form-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .bn-card-heading,
    .bn-input-group.full,
    .bn-switch-box,
    .bn-form-actions {
        grid-column: 1 / -1;
    }

    .bn-input-group {
        display: flex;
        flex-direction: column;
    }

    .bn-input-group label {
        margin-bottom: 6px;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .bn-input-wrap {
        position: relative;
        flex: 1;
    }

    .bn-input-wrap input,
    .bn-input-wrap textarea {
        width: 100%;
        padding: 0 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        outline: none;
        color: #111827;
        font-size: 12px;
        font-weight: 500;
        font-family: inherit;
        transition: all 0.25s ease;
    }

    .bn-input-wrap input {
        height: 44px;
    }

    .bn-input-wrap textarea {
        resize: vertical;
        padding: 12px 14px;
        min-height: 80px;
        line-height: 1.5;
    }

    .bn-input-wrap input:focus,
    .bn-input-wrap textarea:focus {
        background: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .bn-input-wrap input::placeholder,
    .bn-input-wrap textarea::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .bn-input-wrap input[type="file"] {
        padding-top: 10px;
    }

    /* ── SWITCH ── */
    .bn-switch-box {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        border-radius: 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
    }

    .bn-switch {
        position: relative;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .bn-switch input {
        display: none;
    }

    .bn-switch span {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: #9e9e9e;
        cursor: pointer;
        transition: 0.25s ease;
    }

    .bn-switch span::after {
        content: "";
        position: absolute;
        width: 18px;
        height: 18px;
        top: 3px;
        left: 3px;
        border-radius: 999px;
        background: #ffffff;
        transition: 0.25s ease;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
    }

    .bn-switch input:checked + span {
        background: #14b8a6;
    }

    .bn-switch input:checked + span::after {
        transform: translateX(20px);
    }

    .bn-switch-box strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 700;
    }

    .bn-switch-box small {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 500;
        margin-top: 2px;
    }

    /* ── FORM ACTIONS ── */
    .bn-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px;
        padding-top: 18px;
        border-top: 1px solid #f3f4f6;
    }

    .bn-cancel-btn {
        height: 44px;
        padding: 0 22px;
        border-radius: 10px;
        background: #f3f4f6;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        font-family: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
        cursor: pointer;
    }

    .bn-cancel-btn:hover {
        background: #e5e7eb;
    }

    /* ── TOMBOL SIMPAN TEAL ── */
    .bn-submit-teal {
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

    .bn-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .bn-submit-teal:active {
        transform: scale(0.97);
    }

    /* ── PREVIEW ── */
    .bn-preview-card {
        position: sticky;
        top: 20px;
    }

    .bn-preview-heading {
        margin-bottom: 14px;
    }

    .bn-preview-image {
        width: 100%;
        height: 180px;
        border-radius: 12px;
        border: 1px dashed #9e9e9e;
        background: #f9fafb;
        overflow: hidden;
        display: grid;
        place-items: center;
        text-align: center;
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
    }

    .bn-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bn-preview-note {
        margin-top: 14px;
        padding: 12px 16px;
        border-radius: 10px;
        background: #f0fdf4;
        color: #6b7280;
        font-size: 11px;
        font-weight: 500;
        line-height: 1.5;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .bn-form-grid {
            grid-template-columns: 1fr;
        }

        .bn-preview-card {
            position: static;
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
            min-width: unset;
            width: 100%;
        }

        .back-btn {
            width: 100%;
            justify-content: center;
        }

        .bn-form-card {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .bn-form-actions {
            flex-direction: column-reverse;
        }

        .bn-cancel-btn,
        .bn-submit-teal {
            width: 100%;
            justify-content: center;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .bn-preview-image {
            height: 140px;
        }
    }
</style>
@endsection
