<?php $__env->startSection('title', 'Matrix Penugasan'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $assignmentCollection = collect($assignments);
    $uniqueSubjectNames = collect($subjects)->pluck('material_name')->unique()->sort();
    $classCollection = collect($classes);

    $totalAssignments = $assignmentCollection->count();
    $totalSlots = max($classCollection->count() * $uniqueSubjectNames->count(), 1);

    $subjectCoverage = $uniqueSubjectNames->map(function ($name) use ($assignmentCollection, $subjects) {
        $ids = collect($subjects)->where('material_name', $name)->pluck('material_id');
        return [
            'name' => $name,
            'total' => $assignmentCollection->whereIn('subject_id', $ids)->count(),
        ];
    })->sortByDesc('total')->values();
?>

<div class="am-page">

    
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Penugasan Pengajar</h1>
            <p>Atur relasi pengajar untuk setiap mata pelajaran di database lokal.</p>
        </div>
    </section>

    
    <section class="am-strip">
        <div class="am-strip-card card-blue">
            <span>Total Penugasan</span>
            <strong><?php echo e($totalAssignments); ?></strong>
        </div>
        <div class="am-strip-card card-teal">
            <span>Slot Tersedia</span>
            <strong><?php echo e($totalSlots); ?></strong>
        </div>
        <div class="am-strip-card card-orange">
            <span>Slot Kosong</span>
            <strong><?php echo e(max($totalSlots - $totalAssignments, 0)); ?></strong>
        </div>
    </section>

    
    <section class="am-grid-top">
        <div class="am-card am-form-card">
            <div class="am-card-header">
                <h2>Tambah Penugasan Baru</h2>
            </div>
            <form action="<?php echo e(route('admin.assignments.store')); ?>" method="POST" class="am-form">
                <?php echo csrf_field(); ?>
                <div class="am-field">
                    <label>Pilih Pengajar</label>
                    <select name="teacher_id" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($teacher->usersID); ?>"><?php echo e($teacher->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="am-field">
                    <label>Pilih Program</label>
                    <select name="class_id" id="class-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->class_id); ?>"><?php echo e($class->program_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="am-field">
                    <label>Pilih Mata Pelajaran</label>
                    <select name="subject_id" id="subject-select" required disabled class="am-select-disabled">
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                    </select>
                    <input type="hidden" name="subject_name" id="subject-name-hidden">
                </div>
                <button type="submit" class="am-btn-submit-teal">Simpan Penugasan</button>
            </form>
        </div>

        <div class="am-card">
            <div class="am-card-header">
                <h2>Cakupan Mapel</h2>
            </div>
            <div class="am-coverage-list">
                <?php $__empty_1 = true; $__currentLoopData = $subjectCoverage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="am-progress-row">
                        <div class="am-progress-label">
                            <span><?php echo e($sc['name']); ?></span>
                            <strong><?php echo e($sc['total']); ?></strong>
                        </div>
                        <div class="am-progress-bg">
                            <em style="width: <?php echo e($totalAssignments > 0 ? ($sc['total'] / $totalAssignments) * 100 : 0); ?>%"></em>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="am-empty-coverage">
                        <span>Belum ada cakupan mapel terdaftar</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="am-card am-matrix-card">
        <div class="am-card-header">
            <div>
                <h2>Peta Penugasan (Matrix View)</h2>
                <p class="am-matrix-subtitle">Data dikelola berdasarkan tabel <strong>Materials</strong></p>
            </div>
        </div>
        <div class="am-table-scroll">
            <table class="am-matrix-table">
                <thead>
                    <tr>
                        <th class="sticky-col">PROGRAM KELAS</th>
                        <?php $__currentLoopData = $uniqueSubjectNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo e($name); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="sticky-col"><strong><?php echo e($class->program_name); ?></strong></td>
                            <?php $__currentLoopData = $uniqueSubjectNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $targetMaterial = collect($subjects)->where('class_id', $class->class_id)->where('material_name', $name)->first();
                                    $assigned = null;
                                    if($targetMaterial) {
                                        $assigned = $assignmentCollection->where('class_id', $class->class_id)
                                                                         ->where('subject_id', $targetMaterial->material_id)
                                                                         ->first();
                                    }
                                ?>
                                <td>
                                    <?php if($targetMaterial): ?>
                                        <?php if($assigned): ?>
                                            <div class="am-slot filled">
                                                <span><?php echo e($assigned->teacher->name ?? 'Guru'); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="am-slot empty">Kosong</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="am-slot locked">-</div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="am-card">
        <div class="am-card-header">
            <h2>Daftar Penugasan Aktif</h2>
        </div>
        <div class="am-table-list-wrap">
            <table class="am-list-table">
                <thead>
                    <tr>
                        <th>Pengajar</th>
                        <th>Program</th>
                        <th>Mata Pelajaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="am-t-cell">
                                    <div class="am-avatar"><?php echo e(strtoupper(substr($assign->teacher->name ?? 'P', 0, 1))); ?></div>
                                    <strong><?php echo e($assign->teacher->name ?? 'N/A'); ?></strong>
                                </div>
                            </td>
                            <td><?php echo e($assign->classModel->program_name ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge-subject"><?php echo e($assign->subject_name ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <div class="am-actions-wrap">
                                    <a href="<?php echo e(route('admin.assignments.edit', $assign->id)); ?>" class="am-btn-edit">Edit</a>
                                    <form action="<?php echo e(route('admin.assignments.destroy', $assign->id)); ?>" method="POST" style="display: inline-flex;">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="am-btn-del" onclick="return confirm('Hapus penugasan ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="am-empty-state">
                                    <span>Belum ada data penugasan yang aktif.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#class-select').on('change', function() {
        var classId = $(this).val();
        var subjectSelect = $('#subject-select');
        var subjectNameHidden = $('#subject-name-hidden');

        if (classId) {
            subjectSelect.prop('disabled', true).html('<option>Memuat Mapel...</option>');
            $.ajax({
                url: "<?php echo e(url('/admin/get-subjects-by-class')); ?>/" + classId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    subjectSelect.prop('disabled', false).empty();
                    subjectSelect.append('<option value="">-- Pilih Mapel --</option>');
                    if(data.length > 0) {
                        $.each(data, function(key, value) {
                            subjectSelect.append('<option value="' + value.material_id + '" data-name="' + value.material_name + '">' + value.material_name + '</option>');
                        });
                    } else {
                        subjectSelect.html('<option value="">Tidak ada mapel untuk kelas ini</option>');
                    }
                }
            });
        } else {
            subjectSelect.prop('disabled', true).html('<option value="">-- Pilih Kelas Terlebih Dahulu --</option>');
            subjectNameHidden.val('');
        }
    });

    $(document).on('change', '#subject-select', function() {
        var selectedOption = $(this).find('option:selected');
        var subjectName = selectedOption.data('name');
        $('#subject-name-hidden').val(subjectName);
    });
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

    .am-page {
        font-family: 'Montserrat', sans-serif;
        padding: 10px;
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

    /* ── METRIK CARDS ── */
    .am-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .am-strip-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .am-strip-card:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .am-strip-card.card-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .am-strip-card.card-blue:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
    }

    .am-strip-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .am-strip-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .am-strip-card.card-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    .am-strip-card.card-orange:hover {
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
    }

    .am-strip-card::after {
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

    .am-strip-card::before {
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

    .am-strip-card span {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        color: rgba(255, 255, 255, 0.9);
        display: block;
        margin-bottom: 4px;
    }

    .am-strip-card strong {
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        display: block;
        line-height: 1.2;
    }

    /* ── GRID TOP ── */
    .am-grid-top {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .am-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #edf0f4;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .am-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .am-card-header h2 {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .am-matrix-subtitle {
        font-size: 11px;
        color: #6b7280;
        margin: 4px 0 0 0;
        font-weight: 500;
    }

    /* ── FORM ── */
    .am-form {
        display: grid;
        gap: 15px;
    }

    .am-field label {
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.05em;
    }

    .am-field select {
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        font-weight: 500;
        font-family: inherit;
        font-size: 12px;
        outline: none;
        transition: all 0.25s ease;
        color: #111827;
    }

    .am-field select:focus {
        border-color: #14b8a6;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .am-select-disabled {
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
        color: #6b7280 !important;
        cursor: not-allowed;
    }

    /* ── TOMBOL SIMPAN TEAL ── */
    .am-btn-submit-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff;
        border: none;
        padding: 12px 18px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
        font-family: inherit;
        letter-spacing: 0.02em;
    }

    .am-btn-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .am-btn-submit-teal:active {
        transform: scale(0.97);
    }

    /* ── COVERAGE ── */
    .am-progress-row {
        margin-bottom: 12px;
    }

    .am-progress-row:last-child {
        margin-bottom: 0;
    }

    .am-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .am-progress-label span {
        font-weight: 600;
        color: #111827;
    }

    .am-progress-label strong {
        font-weight: 700;
        color: #14b8a6;
    }

    .am-progress-bg {
        height: 6px;
        background: #f3f4f6;
        border-radius: 10px;
        overflow: hidden;
    }

    .am-progress-bg em {
        display: block;
        height: 100%;
        background: linear-gradient(90deg, #14b8a6, #0d9488);
        border-radius: 10px;
        box-shadow: 0 0 8px rgba(20, 184, 166, 0.2);
    }

    .am-empty-coverage {
        padding: 20px;
        text-align: center;
        color: #6b7280;
        font-size: 11px;
        font-weight: 500;
    }

    /* ── MATRIX TABLE ── */
    .am-table-scroll {
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }

    .am-matrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .am-matrix-table th {
        background: #f9fafb;
        padding: 10px 14px;
        text-align: left;
        font-size: 9px;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 1px solid #e5e7eb;
        letter-spacing: 0.08em;
    }

    .am-matrix-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
        font-size: 12px;
        font-weight: 500;
    }

    .sticky-col {
        position: sticky;
        left: 0;
        background: #ffffff !important;
        z-index: 10;
        border-right: 2px solid #f3f4f6;
        min-width: 160px;
        font-weight: 600;
    }

    /* ── SLOT ── */
    .am-slot {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
        min-height: 30px;
        transition: all 0.2s;
    }

    .am-slot.filled {
        background: rgba(20, 184, 166, 0.1);
        color: #0d9488;
        border: 1px solid rgba(20, 184, 166, 0.12);
    }

    .am-slot.empty {
        background: #fee2e2;
        color: #dc2626;
        border: 1px dashed rgba(220, 38, 38, 0.2);
    }

    .am-slot.locked {
        background: #f3f4f6;
        color: #9e9e9e;
    }

    /* ── LIST TABLE ── */
    .am-table-list-wrap {
        overflow-x: auto;
    }

    .am-list-table {
        width: 100%;
        border-collapse: collapse;
    }

    .am-list-table th {
        text-align: left;
        padding: 10px 14px;
        font-size: 9px;
        color: #6b7280;
        text-transform: uppercase;
        border-bottom: 2px solid #f3f4f6;
        font-weight: 700;
        letter-spacing: 0.08em;
    }

    .am-list-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
        font-size: 12px;
        font-weight: 500;
    }

    .am-list-table tbody tr:last-child td {
        border-bottom: none;
    }

    .am-list-table tbody tr:hover {
        background: #fafbfc;
    }

    .am-t-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .am-avatar {
        width: 30px;
        height: 30px;
        background: rgba(20, 184, 166, 0.1);
        color: #0d9488;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 11px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }

    .badge-subject {
        background: #dbeafe;
        color: #2563eb;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
    }

    .am-actions-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
    }

    .am-btn-edit {
        padding: 4px 12px;
        border-radius: 6px;
        border: none;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        background: #dbeafe;
        color: #2563eb;
    }

    .am-btn-edit:hover {
        background: #3b82f6;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .am-btn-del {
        padding: 4px 12px;
        border-radius: 6px;
        border: none;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fee2e2;
        color: #dc2626;
    }

    .am-btn-del:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .am-empty-state {
        padding: 20px;
        text-align: center;
        color: #6b7280;
        font-size: 11px;
        font-weight: 500;
    }

    .text-center {
        text-align: center;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1024px) {
        .am-grid-top {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .welcome-card {
            padding: 20px;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .am-strip {
            grid-template-columns: 1fr;
        }

        .am-card {
            padding: 16px;
        }

        .am-strip-card strong {
            font-size: 20px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/assignments/index.blade.php ENDPATH**/ ?>