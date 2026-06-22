<?php $__env->startSection('title', 'Kelola Materi Class'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalAssignment = count($assignmentsWithSubjects ?? []);
?>

<div class="tm-container">

    
    <section class="tm-stats">
        <div class="tm-stat-card card-teal">
            <div class="tm-stat-info">
                <p>Total Penugasan</p>
                <h2><?php echo e($totalAssignment); ?> <span>Kelas</span></h2>
            </div>
        </div>
    </section>

    
    <section class="tm-card">
        <div class="tm-card-head">
            <div>
                <h2>Daftar Bidang Ajar</h2>
                <small>Semua kombinasi kelas dan mata pelajaran yang Anda ampu</small>
            </div>
        </div>

        <div class="tm-table-responsive">
            <table class="tm-table">
                <thead>
                    <tr>
                        <th style="width: 30%">Program Kelas</th>
                        <th style="width: 30%">Mata Pelajaran</th>
                        <th style="width: 20%">Durasi</th>
                        <th class="text-end" style="width: 20%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignmentsWithSubjects ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            
                            <td>
                                <div class="tm-class-info">
                                    <strong><?php echo e($assign->classModel->program_name ?? 'Kelas'); ?></strong>
                                    <small>ID #<?php echo e($assign->class_id); ?></small>
                                </div>
                            </td>

                            
                            <td>
                                <span class="tm-subject-pill-teal">
                                    <?php echo e($assign->subject_name ?? 'Mata Pelajaran'); ?>

                                </span>
                            </td>

                            
                            <td>
                                <span class="tm-muted">20 Minggu</span>
                            </td>

                            
                            <td class="text-end">
                                <a href="<?php echo e(route('pengajar.materi.pilih', ['class_id' => $assign->class_id, 'subject_name' => $assign->subject_name])); ?>"
                                   class="tm-btn-manage-teal">
                                    Kelola Materi
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="tm-empty">
                                    <div class="tm-empty-icon"><i class="fa-solid fa-folder-open"></i></div>
                                    <strong>Belum ada penugasan mengajar</strong>
                                    <span>Admin akademik belum mendaftarkan kelas pengampu untuk Anda saat ini.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    :root {
        --spekta-red-dark: #c5352c;
        --spekta-red: #e53935;
        --spekta-teal: #2ea8ab;
        --spekta-teal-dark: #1e878a;
        --spekta-teal-light: rgba(46, 168, 171, 0.12);
        --spekta-red-light: rgba(229, 57, 53, 0.06);
        --spekta-gray: #9e9e9e;
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .tm-container {
        padding: 10px;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── STATS SUMMARY (TANPA RUANG KOSONG) ── */
    .tm-stats {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        max-width: 400px;
    }

    .tm-stat-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 16px 22px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.2s ease;
        flex: 1;
    }

    .tm-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
        border-color: var(--spekta-teal);
    }

    .tm-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .tm-stat-info h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }

    .tm-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* ── CARD ── */
    .tm-card {
        background: var(--spekta-white);
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .01);
        border: 1px solid var(--border-soft);
    }

    .tm-card-head {
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }

    .tm-card-head h2 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tm-card-head small {
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
        margin-left: 28px;
    }

    /* ── TABLE ── */
    .tm-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tm-table th {
        padding: 12px 14px;
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        border-bottom: 2px solid var(--spekta-gray-light);
        text-align: left;
        letter-spacing: 0.05em;
        font-weight: 800;
    }

    .tm-table th.text-end,
    .tm-table td.text-end {
        text-align: right;
    }

    .tm-table td {
        padding: 14px;
        border-bottom: 1px solid var(--spekta-gray-light);
        vertical-align: middle;
    }

    .tm-table tbody tr:hover {
        background: #fafbfc;
    }

    .tm-class-info strong {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        text-transform: uppercase;
    }

    .tm-class-info small {
        color: var(--text-muted);
        font-weight: 700;
        font-size: 10px;
        display: block;
        margin-top: 2px;
    }

    /* ── MATA PELAJARAN TEAL ── */
    .tm-subject-pill-teal {
        background: var(--spekta-teal-light);
        padding: 6px 14px;
        border-radius: 6px;
        color: var(--spekta-teal-dark);
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(46, 168, 171, 0.15);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        transition: all 0.2s ease;
    }

    .tm-subject-pill-teal:hover {
        background: var(--spekta-teal);
        color: #ffffff;
        border-color: var(--spekta-teal);
        box-shadow: 0 3px 10px rgba(46, 168, 171, 0.2);
    }

    /* ── TOMBOL KELOLA MATERI TEAL ── */
    .tm-btn-manage-teal {
        background: linear-gradient(135deg, var(--spekta-teal) 0%, var(--spekta-teal-dark) 100%);
        padding: 8px 18px;
        border-radius: 8px;
        text-decoration: none;
        color: var(--spekta-white);
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 3px 10px rgba(46, 168, 171, 0.2);
        letter-spacing: 0.03em;
        position: relative;
        overflow: hidden;
        min-width: 130px;
        justify-content: center;
    }

    .tm-btn-manage-teal::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
    }

    .tm-btn-manage-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 168, 171, 0.35);
    }

    .tm-btn-manage-teal:hover::before {
        left: 100%;
    }

    .tm-btn-manage-teal:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(46, 168, 171, 0.2);
    }

    .tm-muted {
        color: var(--text-muted);
        font-weight: 700;
        font-size: 12px;
    }

    /* ── EMPTY ── */
    .tm-empty {
        padding: 40px;
        text-align: center;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .tm-empty-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 8px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: var(--spekta-gray-light);
        color: var(--spekta-gray);
        font-size: 18px;
    }

    .tm-empty strong {
        display: block;
        color: var(--text-main);
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .tm-empty span {
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 500;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .tm-stats {
            max-width: 100%;
        }

        .tm-table-responsive {
            overflow-x: auto;
        }

        .tm-table th,
        .tm-table td {
            padding: 10px 12px;
            font-size: 11px;
        }

        .tm-btn-manage-teal {
            min-width: 100px;
            padding: 6px 14px;
            font-size: 10px;
        }

        .tm-subject-pill-teal {
            font-size: 10px;
            padding: 4px 10px;
        }

        .tm-card-head small {
            margin-left: 0;
            display: block;
            margin-top: 4px;
        }
    }

    @media (max-width: 600px) {
        .tm-stat-card {
            padding: 14px 16px;
        }

        .tm-stat-info h2 {
            font-size: 22px;
        }

        .tm-card {
            padding: 14px;
        }

        .tm-table th,
        .tm-table td {
            padding: 8px 10px;
            font-size: 10px;
        }

        .tm-class-info strong {
            font-size: 11px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/materi/index.blade.php ENDPATH**/ ?>