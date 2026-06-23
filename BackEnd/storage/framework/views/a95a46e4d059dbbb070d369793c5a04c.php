<?php $__env->startSection('title', 'Hasil Nilai Siswa'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">

    
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Hasil: <span style="color: #0d9488;"><?php echo e($tryoutTitle ?? 'Paket Tryout'); ?></span></h1>
            <p>Total <?php echo e(count($results)); ?> siswa telah menyelesaikan ujian ini secara nasional.</p>
        </div>
        <div class="welcome-action">
            <a href="<?php echo e(route('admin.scores.index')); ?>" class="back-btn">Kembali</a>
        </div>
    </section>

    
    <div class="cp-main-card">
        <div class="cp-table-wrap">
            <table class="cp-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Jawaban Benar</th>
                        <th>Skor Akhir</th>
                        <th>Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if($res): ?>
                            <?php
                                $resObj = (object) json_decode(json_encode($res));
                                $studentName = $resObj->user_data->name ?? 'Siswa tidak ditemukan';
                                $studentEmail = $resObj->user_data->email ?? '-';
                                $createdAt = isset($resObj->created_at) ? \Carbon\Carbon::parse($resObj->created_at) : null;
                            ?>
                            <tr>
                                <td>
                                    <div class="cp-student-cell">
                                        <div class="cp-student-avatar">
                                            <?php echo e(strtoupper(substr($studentName, 0, 1))); ?>

                                        </div>
                                        <div class="cp-student-info">
                                            <strong><?php echo e($studentName); ?></strong>
                                            <span><?php echo e($studentEmail); ?></span>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-correct">
                                    <?php echo e($resObj->total_correct ?? 0); ?> Soal
                                </td>

                                <td>
                                    <span class="cp-score-badge">
                                        <?php echo e($resObj->score ?? 0); ?>

                                    </span>
                                </td>

                                <td class="cp-date-cell">
                                    <?php echo e($createdAt ? $createdAt->format('d M Y, H:i') : '-'); ?> WIB
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="cp-empty-state">
                                    <strong>Belum ada siswa yang mengerjakan tryout ini.</strong>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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

    .welcome-text h1 span {
        color: #0d9488;
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

    /* ── TABLE ── */
    .cp-main-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #edf0f4;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .cp-table-wrap {
        overflow-x: auto;
        border-radius: 12px;
    }

    .cp-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .cp-table th {
        text-align: left;
        padding: 10px 14px;
        font-size: 9px;
        color: #6b7280;
        text-transform: uppercase;
        border-bottom: 2px solid #f3f4f6;
        font-weight: 700;
        letter-spacing: 0.08em;
    }

    .cp-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
        font-size: 12px;
        font-weight: 500;
    }

    .cp-table tbody tr:last-child td {
        border-bottom: none;
    }

    .cp-table tbody tr:hover {
        background: #fafbfc;
    }

    /* ── STUDENT CELL ── */
    .cp-student-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cp-student-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: rgba(20, 184, 166, 0.1);
        color: #0d9488;
        font-weight: 700;
        font-size: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }

    .cp-student-info strong {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .cp-student-info span {
        display: block;
        font-size: 10px;
        color: #6b7280;
        font-weight: 500;
        margin-top: 1px;
    }

    /* ── CORRECT TEXT ── */
    .text-correct {
        color: #16a34a;
        font-weight: 600;
        font-size: 13px;
    }

    /* ── SCORE BADGE ── */
    .cp-score-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 26px;
        padding: 0 12px;
        border-radius: 8px;
        background: #1f2937;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(31, 41, 55, 0.12);
    }

    /* ── DATE ── */
    .cp-date-cell {
        color: #6b7280;
        font-size: 11px;
        font-weight: 500;
    }

    /* ── EMPTY STATE ── */
    .cp-empty-state {
        padding: 40px;
        text-align: center;
        color: #6b7280;
        font-size: 12px;
        font-weight: 500;
    }

    .cp-empty-state strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
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

        .back-btn {
            width: 100%;
            justify-content: center;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .cp-table {
            min-width: 500px;
        }

        .cp-table th,
        .cp-table td {
            padding: 8px 10px;
            font-size: 11px;
        }

        .cp-student-avatar {
            width: 28px;
            height: 28px;
            font-size: 10px;
        }

        .cp-score-badge {
            font-size: 11px;
            min-width: 36px;
            height: 22px;
            padding: 0 8px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/tryout/scores.blade.php ENDPATH**/ ?>