<?php $__env->startSection('title', 'Bank Soal Tryout - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $assignmentCollection = collect($assignmentsWithSubjects ?? []);
    $totalAssignment = $assignmentCollection->count();
?>

<div class="cp-page">

    
    <section class="cp-header">
        <div class="cp-header-left">
            <h1>Tryout Question Center</h1>
            <p>Kontribusikan draf soal terbaik Anda. Admin akan mengkurasi draf tersebut menjadi satu paket Tryout resmi.</p>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="tm-alert-modern success">
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="tm-alert-modern error">
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('warning')): ?>
        <div class="tm-alert-modern warning">
            <span><?php echo e(session('warning')); ?></span>
        </div>
    <?php endif; ?>

    
    <section class="cp-stats">
        <div class="cp-stat-card card-teal">
            <div class="cp-stat-info">
                <p>Penugasan Kelas</p>
                <h2><?php echo e($totalAssignment); ?> <span>Kelas</span></h2>
            </div>
        </div>

        <div class="cp-stat-card card-red">
            <div class="cp-stat-info">
                <p>Soal Disetor</p>
                <h2><?php echo e($totalSoalSelesai ?? 0); ?> <span>Soal</span></h2>
            </div>
            <?php if(($totalSoalSelesai ?? 0) > 0): ?>
                <span class="cp-pulse-dot"></span>
            <?php endif; ?>
        </div>

        <div class="cp-stat-card card-gray">
            <div class="cp-stat-info">
                <p>Target Publikasi</p>
                <h2><?php echo e($totalAssignment); ?> <span>Paket TO</span></h2>
            </div>
        </div>
    </section>

    
    <section class="cp-main-card">
        <div class="card-header-flex">
            <div>
                <h2>Daftar Penugasan Soal</h2>
                <p>Klik tombol input untuk mengelola soal di setiap mata pelajaran.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="cp-table-modern">
                <thead>
                    <tr>
                        <th width="30%">PROGRAM KELAS</th>
                        <th width="25%" class="text-center">MATA PELAJARAN</th>
                        <th width="25%" class="text-center">PROGRES SETORAN</th>
                        <th width="20%" class="text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignmentsWithSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $subjectName = $assign->subject_name;
                            $count = $assign->total_soal ?? 0;
                        ?>
                        <tr>
                            <td>
                                <div class="program-info">
                                    <div>
                                        <strong><?php echo e($assign->classModel->program_name ?? 'Program'); ?></strong>
                                        <small>ID Kelas: #<?php echo e($assign->class_id); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="subject-tag-teal">
                                    <?php echo e($subjectName); ?>

                                </span>
                            </td>
                            <td class="text-center">
                                <div class="progress-container-flex">
                                    <div class="contribution-info <?php echo e($count > 0 ? 'active' : ''); ?>">
                                        <div class="info-content">
                                            <strong><?php echo e($count); ?> Soal</strong>
                                            <span><?php echo e($count > 0 ? 'TERUPLOAD' : 'BELUM ADA'); ?></span>
                                        </div>
                                    </div>

                                    <?php if($count > 0): ?>
                                        <form action="<?php echo e(route('pengajar.tryout.deleteAll')); ?>" method="POST" onsubmit="return confirm('Tarik kembali semua soal <?php echo e($subjectName); ?>?')">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="class_id" value="<?php echo e($assign->class_id); ?>">
                                            <input type="hidden" name="subject_name" value="<?php echo e($subjectName); ?>">
                                            <button type="submit" class="btn-action-delete" title="Tarik/Hapus Semua Draf Mapel Ini">
                                                Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-right">
                                <a href="<?php echo e(route('pengajar.tryout.create', [$assign->class_id, $subjectName])); ?>"
                                   class="btn-manage-teal <?php echo e($count > 0 ? 'btn-has-content' : ''); ?>">
                                    <span><?php echo e($count > 0 ? 'EDIT / TAMBAH' : 'INPUT SOAL'); ?></span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="cp-empty-state">
                                    <strong>Belum ada penugasan soal untuk Anda saat ini.</strong>
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

    .cp-page {
        font-family: 'Montserrat', sans-serif;
        padding: 10px;
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Header ── */
    .cp-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }

    .cp-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .cp-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* Alerts */
    .tm-alert-modern {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 13px;
    }
    .tm-alert-modern.success {
        background: #e6f7ed;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .tm-alert-modern.error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .tm-alert-modern.warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    /* ── Stats ── */
    .cp-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .cp-stat-card {
        background: var(--spekta-white);
        border-radius: 14px;
        padding: 16px 20px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.25s ease;
        position: relative;
    }
    .cp-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .cp-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .cp-stat-card.card-red:hover { border-color: var(--spekta-red); }
    .cp-stat-card.card-gray:hover { border-color: var(--spekta-gray); }

    .cp-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .cp-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    .cp-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .cp-pulse-dot {
        position: absolute;
        top: 14px; right: 14px;
        width: 6px; height: 6px;
        background: var(--spekta-red);
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7);
        animation: pulseRed 1.5s infinite;
    }
    @keyframes pulseRed {
        0% { box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(229, 57, 53, 0); }
        100% { box-shadow: 0 0 0 0 rgba(229, 57, 53, 0); }
    }

    /* ── Main Card ── */
    .cp-main-card {
        background: var(--spekta-white);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .card-header-flex {
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }

    .card-header-flex h2 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 4px 0;
    }

    .card-header-flex p {
        font-size: 11px;
        color: var(--text-muted);
        margin: 0;
        font-weight: 600;
    }

    /* ── Table ── */
    .table-responsive { overflow-x: auto; }

    .cp-table-modern {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .cp-table-modern th {
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        padding: 12px 14px;
        border-bottom: 2px solid var(--spekta-gray-light);
        letter-spacing: 0.05em;
    }

    .cp-table-modern td {
        padding: 14px;
        border-bottom: 1px solid var(--spekta-gray-light);
        vertical-align: middle;
    }

    .cp-table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .cp-table-modern tbody tr:hover {
        background: #fafbfc;
    }

    /* ── Program Info ── */
    .program-info strong {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        text-transform: uppercase;
    }

    .program-info small {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 2px;
        display: block;
    }

    /* ── Mata Pelajaran Teal ── */
    .subject-tag-teal {
        background: var(--spekta-teal-light);
        color: var(--spekta-teal-dark);
        padding: 6px 14px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 12px;
        text-transform: uppercase;
        border: 1px solid rgba(46, 168, 171, 0.15);
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.03em;
        transition: all 0.2s ease;
    }

    .subject-tag-teal:hover {
        background: var(--spekta-teal);
        color: #ffffff;
        border-color: var(--spekta-teal);
        box-shadow: 0 3px 10px rgba(46, 168, 171, 0.2);
    }

    /* ── Progress ── */
    .progress-container-flex {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .contribution-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        background: var(--spekta-gray-light);
        border-radius: 10px;
        border: 1px solid var(--border-soft);
        min-width: 150px;
        text-align: left;
    }

    .contribution-info.active {
        background: #e6f7ed;
        border-color: #bbf7d0;
    }

    .info-content strong {
        display: block;
        font-size: 12px;
        color: var(--text-main);
        line-height: 1.2;
        font-weight: 800;
    }

    .info-content span {
        font-size: 8px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ── Tombol Hapus ── */
    .btn-action-delete {
        background: var(--spekta-red-light);
        color: var(--spekta-red);
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.2s;
        font-size: 10px;
        font-weight: 700;
    }
    .btn-action-delete:hover {
        background: #fecaca;
        color: #991b1b;
        transform: scale(1.05);
    }

    /* ── Tombol Kelola Teal ── */
    .btn-manage-teal {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--spekta-teal) 0%, var(--spekta-teal-dark) 100%);
        color: var(--spekta-white);
        padding: 8px 18px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.25s ease;
        font-weight: 800;
        font-size: 11px;
        box-shadow: 0 3px 10px rgba(46, 168, 171, 0.2);
        letter-spacing: 0.03em;
        white-space: nowrap;
        position: relative;
        overflow: hidden;
        min-width: 130px;
    }

    .btn-manage-teal::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
    }

    .btn-manage-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 168, 171, 0.35);
    }

    .btn-manage-teal:hover::before {
        left: 100%;
    }

    .btn-manage-teal:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(46, 168, 171, 0.2);
    }

    .btn-manage-teal.btn-has-content {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    .btn-manage-teal.btn-has-content:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
    }

    /* ── Empty State ── */
    .cp-empty-state {
        padding: 40px;
        text-align: center;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .cp-empty-state strong {
        display: block;
        color: var(--text-main);
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }

    @media (max-width: 768px) {
        .cp-stats {
            grid-template-columns: 1fr;
        }

        .cp-table-modern {
            min-width: 500px;
        }

        .cp-table-modern th,
        .cp-table-modern td {
            padding: 10px 12px;
            font-size: 11px;
        }

        .btn-manage-teal {
            min-width: 100px;
            padding: 6px 14px;
            font-size: 10px;
        }

        .subject-tag-teal {
            font-size: 10px;
            padding: 4px 10px;
        }

        .program-info strong {
            font-size: 11px;
        }

        .contribution-info {
            min-width: 100px;
            padding: 6px 10px;
        }
    }

    @media (max-width: 600px) {
        .cp-stat-card {
            padding: 14px 16px;
        }

        .cp-stat-info h2 {
            font-size: 20px;
        }

        .cp-main-card {
            padding: 14px;
        }

        .cp-table-modern th,
        .cp-table-modern td {
            padding: 8px 10px;
            font-size: 10px;
        }

        .btn-manage-teal {
            min-width: 80px;
            padding: 5px 10px;
            font-size: 9px;
        }

        .progress-container-flex {
            flex-direction: column;
            gap: 6px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/tryout/index.blade.php ENDPATH**/ ?>