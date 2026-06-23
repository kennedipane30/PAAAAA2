<?php $__env->startSection('title', 'Promo Management'); ?>
<?php $__env->startSection('subtitle', 'Kelola strategi diskon Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $promoCollection = method_exists($promos, 'getCollection') ? $promos->getCollection() : collect($promos);
    $totalPromo = method_exists($promos, 'total') ? $promos->total() : $promoCollection->count();
    $totalQuota = $promoCollection->sum('quota');

    $activePromoCount = $promoCollection->filter(function($row) {
        $endDate = $row->end_date ? \Carbon\Carbon::parse($row->end_date) : null;
        $isExpired = $endDate ? now()->greaterThan($endDate->endOfDay()) : false;
        return ($row->is_active ?? true) && !$isExpired;
    })->count();
?>

<div class="pm-page">

    
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Manajemen Promo</h1>
            <p>Kelola strategi diskon untuk setiap program kelas Spekta Academy.</p>
        </div>
        <div class="welcome-action">
            <button type="button" class="pm-primary-btn-teal" onclick="togglePromoForm()">
                <span id="toggle-text">Buat Promo Baru</span>
            </button>
        </div>
    </section>

    
    <?php if(session('success')): ?>
        <div class="pm-alert success">
            <div>
                <strong>Berhasil!</strong>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="pm-alert error">
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

    
    <section class="pm-stats">
        <div class="pm-stat-card card-blue">
            <div class="pm-stat-info">
                <p>Total Promo</p>
                <h2><?php echo e(number_format($totalPromo)); ?></h2>
            </div>
        </div>

        <div class="pm-stat-card card-teal">
            <div class="pm-stat-info">
                <p>Total Kuota</p>
                <h2><?php echo e(number_format($totalQuota)); ?></h2>
            </div>
        </div>

        <div class="pm-stat-card card-orange">
            <div class="pm-stat-info">
                <p>Promo Aktif</p>
                <h2><?php echo e(number_format($activePromoCount)); ?></h2>
            </div>
            <?php if($activePromoCount > 0): ?>
                <span class="pm-pulse-dot"></span>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="pm-form-panel" id="promoForm" <?php if(isset($editPromo)): ?> style="max-height: 800px; padding: 20px; border: 1px solid var(--border-soft); opacity: 1;" <?php endif; ?>>
        <div class="pm-panel-heading">
            <div>
                <h2><?php if(isset($editPromo)): ?> Edit Kode Promo <?php else: ?> Buat Kode Promo Baru <?php endif; ?></h2>
                <p><?php if(isset($editPromo)): ?> Perbarui data promo yang sudah ada <?php else: ?> Masukkan kode promo, target kelas, besaran diskon, kuota, dan periode promo. <?php endif; ?></p>
            </div>
        </div>

        <form action="<?php if(isset($editPromo)): ?> <?php echo e(route('admin.promo.update', $editPromo->promotion_id)); ?> <?php else: ?> <?php echo e(route('admin.promo.store')); ?> <?php endif; ?>" method="POST" class="pm-form" id="promoFormElement">
            <?php echo csrf_field(); ?>
            <?php if(isset($editPromo)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

            <div class="pm-input-group">
                <label>Kode Promo</label>
                <div class="pm-input-wrap">
                    <input type="text" name="code" id="promoCode" value="<?php echo e(isset($editPromo) ? $editPromo->code : old('code')); ?>" placeholder="CONTOH: SPEKTA50" required style="text-transform: uppercase;">
                </div>
                <small class="error-msg" id="codeError">Kode promo harus diisi</small>
            </div>

            <div class="pm-input-group">
                <label>Target Kelas</label>
                <div class="pm-input-wrap">
                    <select name="class_id" id="classId" required>
                        <option value="">Pilih Kelas</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->class_id); ?>" <?php echo e((isset($editPromo) && $editPromo->class_id == $c->class_id) ? 'selected' : (old('class_id') == $c->class_id ? 'selected' : '')); ?>>
                                <?php echo e($c->program_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <small class="error-msg" id="classError">Pilih target kelas</small>
            </div>

            <div class="pm-input-group">
                <label>Besar Diskon</label>
                <div class="pm-discount-wrap">
                    <input type="number" name="discount_value" id="discountValue" value="<?php echo e(isset($editPromo) ? ($editPromo->discount_percent ?? 0) : old('discount_value')); ?>" placeholder="Nilai diskon" required min="1">
                    <button type="button" id="btn-toggle-type" onclick="toggleDiscountType()" title="Klik untuk mengubah mata uang">
                        <span id="display-type"><?php echo e(isset($editPromo) && $editPromo->discount_type == 'fixed' ? 'Rp' : '%'); ?></span>
                    </button>
                </div>
                <small class="error-msg" id="discountError">Diskon harus diisi dengan angka positif</small>
            </div>
            <input type="hidden" name="discount_type" id="input_discount_type" value="<?php echo e(isset($editPromo) ? $editPromo->discount_type : old('discount_type', 'percent')); ?>">

            <div class="pm-input-group">
                <label>Kuota Penggunaan</label>
                <div class="pm-input-wrap">
                    <input type="number" name="quota" id="quota" value="<?php echo e(isset($editPromo) ? $editPromo->quota : old('quota', 100)); ?>" placeholder="Contoh: 100" min="1" required>
                </div>
                <small class="error-msg" id="quotaError">Kuota minimal 1</small>
            </div>

            <div class="pm-input-group">
                <label>Tanggal Mulai</label>
                <div class="pm-input-wrap">
                    <input type="date" name="start_date" id="startDate" value="<?php echo e(isset($editPromo) ? $editPromo->start_date : old('start_date')); ?>" required min="<?php echo e(date('Y-m-d')); ?>">
                </div>
                <small class="error-msg" id="startDateError">Tanggal mulai tidak boleh kurang dari hari ini</small>
            </div>

            <div class="pm-input-group">
                <label>Tanggal Berakhir</label>
                <div class="pm-input-wrap">
                    <input type="date" name="end_date" id="endDate" value="<?php echo e(isset($editPromo) ? $editPromo->end_date : old('end_date')); ?>" required>
                </div>
                <small class="error-msg" id="endDateError">Tanggal berakhir harus minimal 1 hari setelah tanggal mulai</small>
            </div>

            <div class="pm-form-action">
                <button type="submit" class="pm-submit-teal">
                    <?php if(isset($editPromo)): ?> Perbarui Promo <?php else: ?> Simpan & Terbitkan Promo <?php endif; ?>
                </button>
                <?php if(isset($editPromo)): ?>
                    <a href="<?php echo e(route('admin.promo.index')); ?>" class="pm-cancel-btn">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    
    <section class="pm-promo-panel">
        <div class="pm-panel-heading">
            <div>
                <h2>Promo Berjalan</h2>
                <p>Daftar kode promo yang sudah dibuat untuk program kelas.</p>
            </div>
        </div>

        <div class="pm-promo-grid">
            <?php $__empty_1 = true; $__currentLoopData = $promos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $discountType = $row->discount_type ?? 'percent';
                    $discountValue = $row->discount_value ?? $row->discount_percent ?? 0;

                    $discountText = $discountType === 'fixed'
                        ? 'Rp ' . number_format($discountValue, 0, ',', '.')
                        : (int) $discountValue . '%';

                    $startDate = $row->start_date ? \Carbon\Carbon::parse($row->start_date) : null;
                    $endDate = $row->end_date ? \Carbon\Carbon::parse($row->end_date) : null;

                    $isExpired = $endDate ? now()->greaterThan($endDate->endOfDay()) : false;
                    $isActive = ($row->is_active ?? true) && !$isExpired;
                ?>

                <article class="pm-promo-card">
                    <div class="pm-promo-top">
                        <div class="pm-promo-title-area">
                            <span class="pm-code"><?php echo e($row->code); ?></span>
                            <h3><?php echo e($row->class->program_name ?? 'Program tidak tersedia'); ?></h3>
                        </div>
                        <div class="pm-discount">
                            <strong><?php echo e($discountText); ?></strong>
                            <small>Potongan</small>
                        </div>
                    </div>

                    <div class="pm-promo-meta">
                        <div class="meta-item">
                            <span>Kuota</span>
                            <strong><?php echo e(number_format($row->quota ?? 0)); ?></strong>
                        </div>
                        <div class="meta-item">
                            <span>Status</span>
                            <?php if($isActive): ?>
                                <span class="pm-card-status active">Aktif</span>
                            <?php else: ?>
                                <span class="pm-card-status inactive">Nonaktif</span>
                            <?php endif; ?>
                        </div>
                        <div class="meta-item">
                            <span>Mulai</span>
                            <strong><?php echo e($startDate ? $startDate->translatedFormat('d M Y') : '-'); ?></strong>
                        </div>
                        <div class="meta-item">
                            <span>Berakhir</span>
                            <strong><?php echo e($endDate ? $endDate->translatedFormat('d M Y') : '-'); ?></strong>
                        </div>
                    </div>

                    <div class="pm-card-actions">
                        <a href="<?php echo e(route('admin.promo.edit', $row->promotion_id)); ?>" class="pm-edit-btn">Edit</a>
                        <form action="<?php echo e(route('admin.promo.destroy', $row->promotion_id)); ?>" method="POST" onsubmit="return confirm('Hapus promo ini secara permanen?')" style="display: inline-block; width: 100%;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="pm-stop-btn">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="pm-empty">
                    <strong>Belum ada promo.</strong>
                    <p>Buat kode promo pertama Anda melalui form di atas.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if(method_exists($promos, 'hasPages') && $promos->hasPages()): ?>
            <div class="pm-pagination">
                <?php echo e($promos->links()); ?>

            </div>
        <?php endif; ?>
    </section>

</div>

<script>
    function togglePromoForm() {
        const formPanel = document.getElementById('promoForm');
        const toggleText = document.getElementById('toggle-text');

        if (formPanel.classList.contains('show')) {
            formPanel.classList.remove('show');
            toggleText.innerText = "Buat Promo Baru";
        } else {
            formPanel.classList.add('show');
            toggleText.innerText = "Sembunyikan Form";
            formPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function toggleDiscountType() {
        const inputType = document.getElementById('input_discount_type');
        const displayType = document.getElementById('display-type');

        if (!inputType || !displayType) return;

        if (inputType.value === 'percent') {
            inputType.value = 'fixed';
            displayType.innerText = 'Rp';
        } else {
            inputType.value = 'percent';
            displayType.innerText = '%';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const inputType = document.getElementById('input_discount_type');
        const displayType = document.getElementById('display-type');
        if (inputType && displayType) {
            displayType.innerText = inputType.value === 'fixed' ? 'Rp' : '%';
        }

        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const promoCodeInput = document.getElementById('promoCode');
        const classSelect = document.getElementById('classId');
        const discountValueInput = document.getElementById('discountValue');
        const quotaInput = document.getElementById('quota');
        const form = document.getElementById('promoFormElement');

        const startDateError = document.getElementById('startDateError');
        const endDateError = document.getElementById('endDateError');
        const codeError = document.getElementById('codeError');
        const classError = document.getElementById('classError');
        const discountError = document.getElementById('discountError');
        const quotaError = document.getElementById('quotaError');

        const today = new Date().toISOString().split('T')[0];
        if (startDateInput) {
            startDateInput.setAttribute('min', today);
        }

        function validateStartDate() {
            const startDate = startDateInput.value;
            if (!startDate) return true;

            if (startDate < today) {
                startDateError.classList.add('show');
                return false;
            } else {
                startDateError.classList.remove('show');
                return true;
            }
        }

        function validateEndDate() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            if (!startDate || !endDate) return true;

            const startDateObj = new Date(startDate);
            const minEndDate = new Date(startDateObj);
            minEndDate.setDate(startDateObj.getDate() + 1);

            const endDateObj = new Date(endDate);

            minEndDate.setHours(0, 0, 0, 0);
            endDateObj.setHours(0, 0, 0, 0);

            if (endDateObj < minEndDate) {
                endDateError.classList.add('show');
                return false;
            } else {
                endDateError.classList.remove('show');
                return true;
            }
        }

        function validatePromoCode() {
            const code = promoCodeInput.value.trim();
            if (!code) {
                codeError.classList.add('show');
                return false;
            } else {
                codeError.classList.remove('show');
                return true;
            }
        }

        function validateClass() {
            const classId = classSelect.value;
            if (!classId) {
                classError.classList.add('show');
                return false;
            } else {
                classError.classList.remove('show');
                return true;
            }
        }

        function validateDiscount() {
            const discount = discountValueInput.value;
            if (!discount || discount <= 0) {
                discountError.classList.add('show');
                return false;
            } else {
                discountError.classList.remove('show');
                return true;
            }
        }

        function validateQuota() {
            const quota = quotaInput.value;
            if (!quota || quota < 1) {
                quotaError.classList.add('show');
                return false;
            } else {
                quotaError.classList.remove('show');
                return true;
            }
        }

        if (startDateInput) {
            startDateInput.addEventListener('change', function() {
                validateStartDate();
                validateEndDate();

                const startDate = startDateInput.value;
                if (startDate && endDateInput) {
                    const startDateObj = new Date(startDate);
                    const minEndDateObj = new Date(startDateObj);
                    minEndDateObj.setDate(startDateObj.getDate() + 1);
                    const minEndDateStr = minEndDateObj.toISOString().split('T')[0];
                    endDateInput.setAttribute('min', minEndDateStr);
                }
            });
        }

        if (endDateInput) {
            endDateInput.addEventListener('change', validateEndDate);
        }

        if (promoCodeInput) promoCodeInput.addEventListener('input', validatePromoCode);
        if (classSelect) classSelect.addEventListener('change', validateClass);
        if (discountValueInput) discountValueInput.addEventListener('input', validateDiscount);
        if (quotaInput) quotaInput.addEventListener('input', validateQuota);

        if (startDateInput && startDateInput.value && endDateInput) {
            const startDate = startDateInput.value;
            const startDateObj = new Date(startDate);
            const minEndDateObj = new Date(startDateObj);
            minEndDateObj.setDate(startDateObj.getDate() + 1);
            const minEndDateStr = minEndDateObj.toISOString().split('T')[0];
            endDateInput.setAttribute('min', minEndDateStr);
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                const isStartDateValid = validateStartDate();
                const isEndDateValid = validateEndDate();
                const isCodeValid = validatePromoCode();
                const isClassValid = validateClass();
                const isDiscountValid = validateDiscount();
                const isQuotaValid = validateQuota();

                if (!isStartDateValid || !isEndDateValid || !isCodeValid || !isClassValid || !isDiscountValid || !isQuotaValid) {
                    e.preventDefault();
                    let errorMsg = 'Harap periksa kembali data yang Anda masukkan:\n';
                    if (!isCodeValid) errorMsg += '- Kode promo tidak boleh kosong\n';
                    if (!isClassValid) errorMsg += '- Pilih target kelas\n';
                    if (!isDiscountValid) errorMsg += '- Diskon harus diisi dengan angka positif\n';
                    if (!isQuotaValid) errorMsg += '- Kuota minimal 1\n';
                    if (!isStartDateValid) errorMsg += '- Tanggal mulai tidak boleh kurang dari hari ini\n';
                    if (!isEndDateValid) errorMsg += '- Tanggal berakhir harus minimal 1 hari setelah tanggal mulai\n';
                    alert(errorMsg);
                }
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

    .pm-page {
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

    /* ── TOMBOL BUAT PROMO TEAL ── */
    .pm-primary-btn-teal {
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

    .pm-primary-btn-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .pm-primary-btn-teal:active {
        transform: scale(0.97);
    }

    /* ── ALERTS ── */
    .pm-alert {
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 13px;
        font-weight: 700;
    }
    .pm-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }
    .pm-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .pm-alert strong { display: block; margin-bottom: 2px; font-weight: 800;}
    .pm-alert ul { margin: 4px 0 0; padding-left: 20px; }

    /* ── STATS ── */
    .pm-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .pm-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .pm-stat-card:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    /* ── WARNA URUTAN SESUAI DASHBOARD (BIRU, TEAL, ORANYE) ── */
    .pm-stat-card.card-blue {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    .pm-stat-card.card-blue:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.4);
    }

    .pm-stat-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .pm-stat-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .pm-stat-card.card-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    .pm-stat-card.card-orange:hover {
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
    }

    .pm-stat-card::after {
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

    .pm-stat-card::before {
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

    .pm-stat-info {
        position: relative;
        z-index: 1;
    }

    .pm-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        color: rgba(255, 255, 255, 0.9);
    }

    .pm-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }

    .pm-pulse-dot {
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

    /* ── FORM PANEL ── */
    .pm-form-panel {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
        max-height: 0;
        overflow: hidden;
        padding: 0 20px;
        border-width: 0;
        opacity: 0;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, padding 0.4s ease, border-width 0.4s ease;
    }

    .pm-form-panel.show {
        max-height: 800px;
        padding: 24px;
        border: 1px solid #edf0f4;
        opacity: 1;
    }

    .pm-panel-heading {
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .pm-panel-heading h2 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 800;
        color: #111827;
    }

    .pm-panel-heading p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ── FORM ── */
    .pm-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .pm-input-group {
        display: flex;
        flex-direction: column;
    }

    .pm-input-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .pm-input-wrap {
        position: relative;
        display: flex;
    }

    .pm-input-wrap input,
    .pm-input-wrap select {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        font-size: 12px;
        font-weight: 500;
        color: #111827;
        font-family: inherit;
        outline: none;
        transition: all 0.25s ease;
    }

    .pm-input-wrap input:focus,
    .pm-input-wrap select:focus {
        background: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .pm-discount-wrap {
        position: relative;
        display: flex;
    }

    .pm-discount-wrap input {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid #e5e7eb;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        background: #f9fafb;
        font-size: 12px;
        font-weight: 500;
        color: #111827;
        font-family: inherit;
        outline: none;
        transition: all 0.25s ease;
        border-right: none;
    }

    .pm-discount-wrap input:focus {
        background: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .pm-discount-wrap button {
        width: 50px;
        height: 44px;
        border: 1px solid #e5e7eb;
        border-left: none;
        background: #f9fafb;
        color: #6b7280;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12px;
    }

    .pm-discount-wrap button:hover {
        background: #e5e7eb;
        color: #14b8a6;
    }

    .error-msg {
        color: #dc2626;
        font-size: 10px;
        font-weight: 600;
        display: none;
        margin-top: 4px;
    }

    .error-msg.show {
        display: block;
    }

    /* ── FORM ACTIONS ── */
    .pm-form-action {
        grid-column: 1 / -1;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 8px;
        padding-top: 18px;
        border-top: 1px solid #f3f4f6;
    }

    .pm-submit-teal {
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

    .pm-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .pm-submit-teal:active {
        transform: scale(0.97);
    }

    .pm-cancel-btn {
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

    .pm-cancel-btn:hover {
        background: #e5e7eb;
    }

    /* ── PROMO GRID ── */
    .pm-promo-panel {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .pm-promo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .pm-promo-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
    }

    .pm-promo-card:hover {
        border-color: #14b8a6;
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }

    .pm-promo-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .pm-code {
        display: inline-block;
        background: rgba(20, 184, 166, 0.1);
        color: #0d9488;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        border: 1px solid rgba(20, 184, 166, 0.15);
    }

    .pm-promo-title-area h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        line-height: 1.4;
    }

    .pm-discount {
        text-align: right;
    }

    .pm-discount strong {
        display: block;
        font-size: 20px;
        font-weight: 900;
        color: #e53935;
        line-height: 1;
    }

    .pm-discount small {
        font-size: 9px;
        color: #6b7280;
        font-weight: 700;
        text-transform: uppercase;
    }

    .pm-promo-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        background: #f9fafb;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 14px;
        flex-grow: 1;
    }

    .meta-item span {
        display: block;
        font-size: 9px;
        color: #6b7280;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .meta-item strong {
        font-size: 12px;
        color: #111827;
        font-weight: 700;
    }

    .pm-card-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
    }

    .pm-card-status.active {
        color: #15803d;
    }
    .pm-card-status.inactive {
        color: #9e9e9e;
    }

    /* ── CARD ACTIONS ── */
    .pm-card-actions {
        display: flex;
        gap: 10px;
        margin-top: 8px;
    }

    .pm-edit-btn {
        flex: 1;
        background: #dbeafe;
        color: #2563eb;
        border: none;
        padding: 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pm-edit-btn:hover {
        background: #3b82f6;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .pm-stop-btn {
        flex: 1;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        padding: 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.2s ease;
        width: 100%;
    }

    .pm-stop-btn:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* ── EMPTY STATE ── */
    .pm-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px dashed #e5e7eb;
    }

    .pm-empty strong {
        display: block;
        font-size: 14px;
        color: #111827;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .pm-empty p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .pm-promo-grid {
            grid-template-columns: repeat(2, 1fr);
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

        .pm-primary-btn-teal {
            width: 100%;
            justify-content: center;
        }

        .pm-stats {
            grid-template-columns: 1fr;
        }

        .pm-form {
            grid-template-columns: 1fr;
        }

        .pm-promo-grid {
            grid-template-columns: 1fr;
        }

        .pm-form-action {
            flex-direction: column-reverse;
        }

        .pm-submit-teal,
        .pm-cancel-btn {
            width: 100%;
            justify-content: center;
        }

        .pm-card-actions {
            flex-direction: column;
            gap: 8px;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .pm-form-panel.show {
            padding: 16px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/promo/index.blade.php ENDPATH**/ ?>