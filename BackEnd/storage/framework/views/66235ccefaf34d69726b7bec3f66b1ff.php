<?php $__env->startSection('title', 'Edit Announcement'); ?>
<?php $__env->startSection('subtitle', 'Perbarui data pengumuman Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="ae-page">

    
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Edit Announcement</h1>
            <p>Perbarui judul, deskripsi, atau gambar pengumuman yang sudah dipublikasikan.</p>
        </div>
        <div class="welcome-action">
            <a href="<?php echo e(route('admin.announcement.index')); ?>" class="back-btn">
                Kembali
            </a>
        </div>
    </section>

    <?php if($errors->any()): ?>
        <div class="ae-alert error">
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    
    <section class="ae-grid">
        <form action="<?php echo e(route('admin.announcement.update', $announcement->announcement_id)); ?>" method="POST" enctype="multipart/form-data" class="ae-form-card">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="ae-card-heading">
                <div>
                    <h2>Form Edit Pengumuman</h2>
                    <p>Cukup ubah data pengumuman yang ingin diperbarui.</p>
                </div>
            </div>

            <div class="ae-input-group">
                <label>Headline Title</label>
                <div class="ae-input-wrap">
                    <input type="text" name="title" value="<?php echo e(old('title', $announcement->title)); ?>" required>
                </div>
            </div>

            <div class="ae-input-group full">
                <label>Content Details</label>
                <div class="ae-input-wrap">
                    <textarea name="description" rows="7" required><?php echo e(old('description', $announcement->description)); ?></textarea>
                </div>
            </div>

            <div class="ae-input-group">
                <label>Replace Visual Media</label>
                <div class="ae-input-wrap">
                    <input type="file" name="image" id="announcementImageInput" accept="image/*">
                </div>
            </div>

            <div class="ae-current-info">
                <div class="ae-mini-avatar">
                    <?php echo e(strtoupper(substr($announcement->title, 0, 1))); ?>

                </div>
                <div>
                    <strong><?php echo e($announcement->title); ?></strong>
                    <span>Dibuat <?php echo e($announcement->created_at?->translatedFormat('d M Y, H:i')); ?></span>
                    <small>Kosongkan file jika tidak ingin mengganti gambar lama.</small>
                </div>
            </div>

            <div class="ae-actions">
                <a href="<?php echo e(route('admin.announcement.index')); ?>" class="ae-cancel-btn">Batal</a>

                <button type="submit" class="ae-submit-teal">
                    Update Announcement Data
                </button>
            </div>
        </form>

        <aside class="ae-preview-card">
            <div class="ae-preview-heading">
                <h3>Visual Preview</h3>
                <p>Gambar saat ini atau gambar baru yang dipilih.</p>
            </div>

            <div class="ae-preview-image" id="announcementPreviewBox">
                <?php if($announcement->image): ?>
                    <img src="<?php echo e(asset('storage/' . $announcement->image)); ?>" alt="<?php echo e($announcement->title); ?>">
                <?php else: ?>
                    <div>
                        <span>Tidak ada gambar</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ae-preview-note">
                <span>Gambar yang rapi akan membuat pengumuman terlihat lebih profesional pada aplikasi.</span>
            </div>
        </aside>
    </section>

</div>

<script>
    const announcementImageInput = document.getElementById('announcementImageInput');
    const announcementPreviewBox = document.getElementById('announcementPreviewBox');

    if (announcementImageInput && announcementPreviewBox) {
        announcementImageInput.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (event) {
                announcementPreviewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Announcement">`;
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

    .ae-page {
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
    .ae-alert {
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
    }

    .ae-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .ae-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
        font-weight: 600;
    }

    /* ── FORM GRID ── */
    .ae-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 24px;
        align-items: start;
    }

    .ae-form-card,
    .ae-preview-card {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        padding: 24px;
    }

    .ae-card-heading {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
    }

    .ae-card-heading h2,
    .ae-preview-heading h3 {
        margin: 0 0 6px;
        font-size: 16px;
        font-weight: 800;
        color: #111827;
    }

    .ae-card-heading p,
    .ae-preview-heading p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ── FORM ── */
    .ae-form-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .ae-card-heading,
    .ae-input-group.full,
    .ae-current-info,
    .ae-actions {
        grid-column: 1 / -1;
    }

    .ae-input-group {
        display: flex;
        flex-direction: column;
    }

    .ae-input-group label {
        margin-bottom: 6px;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .ae-input-wrap {
        position: relative;
        flex: 1;
    }

    .ae-input-wrap input,
    .ae-input-wrap textarea {
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

    .ae-input-wrap input {
        height: 44px;
    }

    .ae-input-wrap textarea {
        resize: vertical;
        padding: 12px 14px;
        min-height: 120px;
        line-height: 1.5;
    }

    .ae-input-wrap input:focus,
    .ae-input-wrap textarea:focus {
        background: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .ae-input-wrap input::placeholder,
    .ae-input-wrap textarea::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .ae-input-wrap input[type="file"] {
        padding-top: 10px;
    }

    /* ── CURRENT INFO ── */
    .ae-current-info {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        border-radius: 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
    }

    .ae-mini-avatar {
        width: 44px;
        height: 44px;
        border-radius: 99px;
        background: rgba(20, 184, 166, 0.12);
        color: #0d9488;
        display: grid;
        place-items: center;
        font-size: 16px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .ae-current-info strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 700;
    }

    .ae-current-info span {
        display: block;
        color: #6b7280;
        font-size: 11px;
        font-weight: 500;
        margin-top: 2px;
    }

    .ae-current-info small {
        display: block;
        color: #dc2626;
        font-size: 10px;
        font-weight: 600;
        margin-top: 2px;
    }

    /* ── FORM ACTIONS ── */
    .ae-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px;
        padding-top: 18px;
        border-top: 1px solid #f3f4f6;
    }

    .ae-cancel-btn {
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

    .ae-cancel-btn:hover {
        background: #e5e7eb;
    }

    /* ── TOMBOL UPDATE TEAL ── */
    .ae-submit-teal {
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

    .ae-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .ae-submit-teal:active {
        transform: scale(0.97);
    }

    /* ── PREVIEW ── */
    .ae-preview-card {
        position: sticky;
        top: 20px;
    }

    .ae-preview-heading {
        margin-bottom: 14px;
    }

    .ae-preview-image {
        width: 100%;
        height: 200px;
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

    .ae-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ae-preview-note {
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
        .ae-grid {
            grid-template-columns: 1fr;
        }

        .ae-preview-card {
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

        .ae-form-card {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .ae-actions {
            flex-direction: column-reverse;
        }

        .ae-cancel-btn,
        .ae-submit-teal {
            width: 100%;
            justify-content: center;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .ae-preview-image {
            height: 150px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/announcement/edit.blade.php ENDPATH**/ ?>