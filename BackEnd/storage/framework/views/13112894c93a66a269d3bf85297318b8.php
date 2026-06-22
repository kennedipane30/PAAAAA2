<?php $__env->startSection('title', 'Rekap Absensi'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalData = $data->count();
    $hadir = $data->where('status', 'h')->count();
    $izin = $data->where('status', 'i')->count();
    $alpa = $data->where('status', 'a')->count();
?>

<div class="recap-container">

    
    <div class="recap-header">
        <div class="recap-header-left">
            <h1 class="recap-title">Rekap Absensi</h1>
            <p class="recap-subtitle"><?php echo e($class->program_name); ?> <span class="separator">•</span> <?php echo e($subject); ?> <span class="separator">•</span> Minggu <?php echo e($week); ?></p>
        </div>
    </div>

    
    <div class="recap-nav">
        <a href="<?php echo e(route('pengajar.absensi.weeks', [$class->class_id, $subject])); ?>" class="back-link">Kembali ke Daftar Minggu</a>
    </div>

    
    <div class="stats-wrapper">
        <div class="stat-item stat-hadir">
            <div class="stat-info">
                <span class="stat-label">HADIR</span>
                <strong class="stat-value"><?php echo e($hadir); ?></strong>
            </div>
        </div>
        <div class="stat-item stat-izin">
            <div class="stat-info">
                <span class="stat-label">IZIN</span>
                <strong class="stat-value"><?php echo e($izin); ?></strong>
            </div>
        </div>
        <div class="stat-item stat-alpa">
            <div class="stat-info">
                <span class="stat-label">ALPA</span>
                <strong class="stat-value"><?php echo e($alpa); ?></strong>
            </div>
        </div>
        <div class="stat-item stat-total">
            <div class="stat-info">
                <span class="stat-label">TOTAL</span>
                <strong class="stat-value"><?php echo e($totalData); ?></strong>
            </div>
        </div>
    </div>

    
    <div class="table-card">
        <div class="table-card-header">
            <div class="card-title">
                <h3>Daftar Kehadiran Siswa</h3>
            </div>
            <div class="card-badge"><?php echo e($totalData); ?> Siswa</div>
        </div>

        <div class="table-responsive">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-student">NAMA SISWA</th>
                        <th class="col-status">STATUS KEHADIRAN</th>
                        <th class="col-action">TANGGAL INPUT & AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="student-row">
                        <td class="no-cell"><?php echo e(str_pad($index + 1, 2, '0', STR_PAD_LEFT)); ?></td>
                        <td class="student-cell">
                            <div class="student-avatar">
                                <?php echo e(strtoupper(substr($row->user->name ?? 'S', 0, 1))); ?>

                            </div>
                            <div class="student-detail">
                                <strong class="student-name"><?php echo e($row->user->name ?? 'N/A'); ?></strong>
                                <span class="student-role">Siswa Aktif</span>
                            </div>
                        </td>
                        <td class="status-cell">
                            <?php if($row->status == 'h'): ?>
                                <span class="status-badge hadir">HADIR</span>
                            <?php elseif($row->status == 'i'): ?>
                                <span class="status-badge izin">IZIN</span>
                            <?php else: ?>
                                <span class="status-badge alpa">ALPA</span>
                            <?php endif; ?>
                        </td>
                        <td class="action-cell">
                            <div class="action-wrapper">
                                <span class="date-input"><?php echo e($row->date ? date('d M Y', strtotime($row->date)) : '-'); ?></span>
                                <div class="action-buttons-group">
                                    <a href="<?php echo e(route('pengajar.absensi.edit', [$class->class_id, $subject, $week])); ?>"
                                       class="action-icon edit-icon">Edit</a>
                                    <a href="<?php echo e(route('pengajar.absensi.export-pdf', [$class->class_id, $subject, $week])); ?>"
                                       class="action-icon export-icon" target="_blank">Export</a>
                                    <button type="button" class="action-icon delete-icon" onclick="confirmDelete(<?php echo e($week); ?>)">Hapus</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="empty-row">
                            <div class="empty-state">
                                <strong>Belum Ada Data Absensi</strong>
                                <span>Silakan isi absensi terlebih dahulu</span>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div id="deleteModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Hapus Absensi</h3>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus seluruh data absensi untuk <strong>Minggu ke-<span id="weekNumber"></span></strong>?</p>
                <p class="warning-text">Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal()">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn-delete-confirm">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let deleteWeek = null;

    function confirmDelete(week) {
        deleteWeek = week;
        const modal = document.getElementById('deleteModal');
        const weekSpan = document.getElementById('weekNumber');
        weekSpan.textContent = week;

        const form = document.getElementById('deleteForm');
        const url = "<?php echo e(route('pengajar.absensi.destroy', [$class->class_id, $subject, ':week'])); ?>";
        form.action = url.replace(':week', week);

        modal.style.display = 'flex';
    }

    function closeModal() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
        deleteWeek = null;
    }

    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }

    <?php if(session('success')): ?>
        showToast('<?php echo e(session('success')); ?>');
    <?php endif; ?>

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">×</button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
</script>

<style>
    .recap-container {
        padding: 24px 32px;
        background: #f5f7fa;
        min-height: 100vh;
        font-family: 'Montserrat', -apple-system, sans-serif;
    }

    /* ── HEADER ── */
    .recap-header {
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e8edf2;
    }

    .recap-title {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
        letter-spacing: -0.3px;
    }

    .recap-subtitle {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
        margin: 0;
    }

    .recap-subtitle .separator {
        margin: 0 8px;
        color: #cbd5e1;
    }

    /* ── NAV ── */
    .recap-nav {
        margin-bottom: 24px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .back-link:hover {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #cbd5e1;
    }

    /* ── STATS ── */
    .stats-wrapper {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-item {
        background: white;
        padding: 18px 22px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        transition: all 0.25s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }

    .stat-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .stat-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        display: block;
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 30px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
    }

    .stat-hadir .stat-value { color: #10b981; }
    .stat-izin .stat-value { color: #f59e0b; }
    .stat-alpa .stat-value { color: #ef4444; }
    .stat-total .stat-value { color: #3b82f6; }

    /* ── TABLE ── */
    .table-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .table-card-header {
        padding: 18px 24px;
        background: white;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-title h3 {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .card-badge {
        padding: 4px 14px;
        background: #f1f5f9;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
    }

    .table-responsive { overflow-x: auto; }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attendance-table thead { background: #f8fafc; }

    .attendance-table th {
        padding: 14px 20px;
        text-align: left;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        border-bottom: 2px solid #e2e8f0;
    }

    .attendance-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .attendance-table tbody tr:last-child td {
        border-bottom: none;
    }

    .student-row:hover { background: #fafcff; }

    .col-no { width: 70px; }
    .col-student { width: 35%; }
    .col-status { width: 20%; }
    .col-action { width: 40%; }

    .no-cell {
        font-weight: 700;
        color: #94a3b8;
        font-size: 13px;
    }

    /* ── STUDENT CELL ── */
    .student-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e6f7f5 0%, #d1f0ee 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        color: #2ea8ab;
        flex-shrink: 0;
    }

    .student-detail { flex: 1; }

    .student-name {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .student-role {
        font-size: 10px;
        font-weight: 600;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 2px 10px;
        border-radius: 10px;
        display: inline-block;
    }

    /* ── STATUS BADGE ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .status-badge.hadir { background: #d1fae5; color: #059669; }
    .status-badge.izin { background: #fed7aa; color: #ea580c; }
    .status-badge.alpa { background: #fee2e2; color: #dc2626; }

    /* ── ACTION ── */
    .action-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .date-input {
        padding: 6px 14px;
        background: #f8fafc;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        color: #475569;
        border: 1px solid #eef2f6;
    }

    .action-buttons-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .action-icon {
        padding: 5px 14px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
    }

    .edit-icon { background: #dbeafe; color: #2563eb; }
    .edit-icon:hover { background: #2563eb; color: white; transform: translateY(-1px); }

    .export-icon { background: #d1fae5; color: #059669; }
    .export-icon:hover { background: #059669; color: white; transform: translateY(-1px); }

    .delete-icon { background: #fee2e2; color: #dc2626; }
    .delete-icon:hover { background: #dc2626; color: white; transform: translateY(-1px); }

    /* ── EMPTY ── */
    .empty-row td { padding: 60px 20px !important; text-align: center; }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .empty-state strong { font-size: 15px; color: #475569; }
    .empty-state span { font-size: 12px; color: #94a3b8; }

    /* ── MODAL ── */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.5);
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(4px);
    }

    .modal-dialog {
        max-width: 440px;
        width: 90%;
        animation: modalFadeIn 0.25s ease;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-24px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .modal-header {
        padding: 20px 24px 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .modal-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .modal-body { padding: 20px 24px; }

    .modal-body p {
        color: #475569;
        line-height: 1.6;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .warning-text {
        font-size: 12px;
        color: #dc2626 !important;
        font-weight: 600;
    }

    .modal-footer {
        padding: 16px 24px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-cancel {
        padding: 9px 22px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel:hover { background: #f1f5f9; }

    .btn-delete-confirm {
        padding: 9px 22px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        background: #dc2626;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-delete-confirm:hover { background: #b91c1c; }

    /* ── TOAST ── */
    .toast {
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 14px 20px;
        background: #0f172a;
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 14px;
        z-index: 1100;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .toast button {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 18px;
        cursor: pointer;
        padding: 0 4px;
    }

    .toast button:hover { color: white; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        .recap-container { padding: 16px; }

        .stats-wrapper {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .recap-title { font-size: 22px; }

        .action-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .action-buttons-group { margin-top: 8px; }
    }

    @media (max-width: 640px) {
        .table-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .col-status, .col-date { min-width: 120px; }

        .recap-subtitle {
            font-size: 12px;
        }

        .recap-subtitle .separator {
            display: none;
        }

        .recap-subtitle br {
            display: block;
        }

        .stat-item {
            padding: 14px 16px;
        }

        .stat-value {
            font-size: 24px;
        }

        .back-link {
            width: 100%;
            justify-content: center;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/absensi/recap.blade.php ENDPATH**/ ?>