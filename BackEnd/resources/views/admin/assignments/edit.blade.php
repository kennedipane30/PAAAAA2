@extends('layouts.spekta')

@section('title', 'Edit Penugasan')
@section('subtitle', 'Sistem Manajemen Terpadu Spekta Academy')

@section('content')
<div class="am-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Edit Penugasan Pengajar</h1>
            <p>Perbarui relasi pengajar untuk mata pelajaran yang ditugaskan.</p>
        </div>
    </section>

    {{-- ── FORM EDIT ── --}}
    <section class="am-grid-top">
        <div class="am-card am-form-card">
            <div class="am-card-header">
                <h2>Form Edit Penugasan</h2>
                <a href="{{ route('admin.assignments.index') }}" class="am-btn-back">Kembali</a>
            </div>

            @if(session('error'))
                <div class="am-alert error">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.assignments.update', $assignment->id) }}" method="POST" class="am-form">
                @csrf
                @method('PUT')

                <div class="am-field">
                    <label>Pilih Pengajar</label>
                    <select name="teacher_id" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->usersID }}"
                                {{ $teacher->usersID == $assignment->user_id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="am-field">
                    <label>Pilih Program</label>
                    <select name="class_id" id="class-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->class_id }}"
                                {{ $class->class_id == $assignment->class_id ? 'selected' : '' }}>
                                {{ $class->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="am-field">
                    <label>Pilih Mata Pelajaran</label>
                    <select name="subject_id" id="subject-select" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->material_id }}"
                                data-name="{{ $subject->material_name }}"
                                {{ $subject->material_id == $assignment->subject_id ? 'selected' : '' }}>
                                {{ $subject->material_name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="subject_name" id="subject-name-hidden" value="{{ $assignment->subject_name }}">
                </div>

                <div class="am-form-actions">
                    <a href="{{ route('admin.assignments.index') }}" class="am-btn-secondary">Batal</a>
                    <button type="submit" class="am-btn-submit-teal">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        <div class="am-card">
            <div class="am-card-header">
                <h2>Informasi Penugasan</h2>
            </div>
            <div class="am-info-list">
                <div class="am-info-item">
                    <span class="am-info-label">Pengajar Saat Ini</span>
                    <strong class="am-info-value">{{ $assignment->teacher->name ?? 'N/A' }}</strong>
                </div>
                <div class="am-info-item">
                    <span class="am-info-label">Program</span>
                    <strong class="am-info-value">{{ $assignment->classModel->program_name ?? 'N/A' }}</strong>
                </div>
                <div class="am-info-item">
                    <span class="am-info-label">Mata Pelajaran</span>
                    <strong class="am-info-value">{{ $assignment->subject_name ?? 'N/A' }}</strong>
                </div>
                <div class="am-info-item">
                    <span class="am-info-label">ID Penugasan</span>
                    <strong class="am-info-value">#{{ $assignment->id }}</strong>
                </div>
            </div>
            <div class="am-info-note">
                <span>Perubahan akan langsung diterapkan setelah disimpan.</span>
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    var initialSubjectId = $('#subject-select').val();
    var initialSubjectName = $('#subject-select option:selected').data('name') || '';
    var initialClassId = $('#class-select').val();

    $('#class-select').on('change', function() {
        var classId = $(this).val();
        var subjectSelect = $('#subject-select');
        var subjectNameHidden = $('#subject-name-hidden');

        if (classId) {
            subjectSelect.prop('disabled', true).html('<option>Memuat Mapel...</option>');
            $.ajax({
                url: "{{ url('/admin/get-subjects-by-class') }}/" + classId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    subjectSelect.prop('disabled', false).empty();
                    subjectSelect.append('<option value="">-- Pilih Mapel --</option>');

                    var hasMatch = false;
                    if(data.length > 0) {
                        $.each(data, function(key, value) {
                            var selected = '';
                            if (classId == initialClassId && value.material_id == initialSubjectId) {
                                selected = 'selected';
                                hasMatch = true;
                            }
                            subjectSelect.append('<option value="' + value.material_id + '" data-name="' + value.material_name + '" ' + selected + '>' + value.material_name + '</option>');
                        });

                        if (!hasMatch && data.length > 0) {
                            var firstOption = subjectSelect.find('option:eq(1)');
                            firstOption.prop('selected', true);
                            subjectNameHidden.val(firstOption.data('name'));
                        }
                    } else {
                        subjectSelect.html('<option value="">Tidak ada mapel untuk kelas ini</option>');
                        subjectNameHidden.val('');
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

    if ($('#class-select').val()) {
        $('#class-select').trigger('change');
    }
});
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

    .am-btn-back {
        color: #6b7280;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        background: #f3f4f6;
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
    }

    .am-btn-back:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .am-alert {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-weight: 700;
        font-size: 13px;
    }

    .am-alert.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

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

    .am-field select:disabled {
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
        color: #6b7280 !important;
        cursor: not-allowed;
    }

    .am-form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 10px;
    }

    .am-btn-secondary {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #e5e7eb;
        background: #f3f4f6;
        color: #374151;
        transition: all 0.2s ease;
    }

    .am-btn-secondary:hover {
        background: #e5e7eb;
    }

    /* ── TOMBOL SIMPAN TEAL ── */
    .am-btn-submit-teal {
        padding: 10px 24px;
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

    .am-btn-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .am-btn-submit-teal:active {
        transform: scale(0.97);
    }

    /* ── INFO LIST ── */
    .am-info-list {
        display: grid;
        gap: 12px;
    }

    .am-info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .am-info-item:last-child {
        border-bottom: none;
    }

    .am-info-label {
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .am-info-value {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
    }

    .am-info-note {
        margin-top: 16px;
        padding: 12px 16px;
        background: rgba(20, 184, 166, 0.06);
        border-radius: 10px;
        font-size: 12px;
        font-weight: 500;
        color: #0d9488;
    }

    @media (max-width: 768px) {
        .welcome-card {
            padding: 20px;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .am-grid-top {
            grid-template-columns: 1fr;
        }

        .am-form-actions {
            flex-direction: column;
        }

        .am-btn-secondary,
        .am-btn-submit-teal {
            width: 100%;
            justify-content: center;
        }

        .am-card {
            padding: 16px;
        }
    }
</style>
@endsection
