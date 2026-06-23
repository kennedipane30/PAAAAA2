<?php $__env->startSection('title', 'Rekap Nilai - Pilih Kelas'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">

    
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Rekap Nilai Tryout</h1>
            <p>Pilih program kelas di bawah ini untuk melihat daftar paket tryout dan hasil ujian siswa.</p>
        </div>
    </section>

    
    <div class="cp-grid">
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="cp-main-card class-card">
            <h3><?php echo e($class->program_name); ?></h3>
            <p>ID Kelas: #<?php echo e($class->class_id); ?></p>

            <a href="<?php echo e(route('admin.scores.pilih_tryout', $class->class_id)); ?>" class="cp-primary-btn-teal">
                Lihat Daftar Paket
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

    /* ── SELECTION GRID ── */
    .cp-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    /* ── CLASS CARD ── */
    .cp-main-card.class-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 20px;
        border: 1px solid #e5e7eb;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .cp-main-card.class-card:hover {
        transform: translateY(-4px);
        border-color: #14b8a6;
        box-shadow: 0 8px 24px rgba(20, 184, 166, 0.08);
    }

    .cp-main-card h3 {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }

    .cp-main-card p {
        font-size: 11px;
        color: #6b7280;
        margin: 0 0 20px;
        font-weight: 500;
    }

    /* ── TOMBOL LIHAT DAFTAR PAKET TEAL ── */
    .cp-primary-btn-teal {
        display: block;
        text-decoration: none;
        padding: 10px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.03em;
        transition: all 0.25s ease;
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff !important;
        box-shadow: 0 3px 12px rgba(20, 184, 166, 0.2);
    }

    .cp-primary-btn-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(20, 184, 166, 0.3);
    }

    .cp-primary-btn-teal:active {
        transform: scale(0.97);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .welcome-card {
            padding: 20px;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .cp-grid {
            grid-template-columns: 1fr;
        }

        .cp-main-card.class-card {
            padding: 20px 16px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/tryout/pilih_kelas.blade.php ENDPATH**/ ?>