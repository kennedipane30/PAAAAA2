@extends('layouts.spekta')

@section('title', 'Edit Jadwal')
@section('subtitle', 'Sistem Manajemen Terpadu Spekta Academy')

@section('content')
<div class="sc-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>Edit Jadwal Pembelajaran</h1>
            <p>Perbarui informasi jadwal yang telah dipublikasikan.</p>
        </div>
    </section>

    @if(session('error'))
        <div class="sc-alert error">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ── FORM PANEL ── --}}
    <section class="sc-top-grid">
        <div class="sc-panel sc-form-panel">
            <div class="sc-panel-heading">
                <h2>Edit Jadwal</h2>
            </div>

            <form action="{{ route('admin.jadwal.update', $schedule->schedule_id) }}" method="POST" class="sc-form" id="editScheduleForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="teacher_id" id="teacherIdHidden" value="{{ $schedule->teacher_id }}">
                <input type="hidden" name="title" id="autoTitle" value="{{ $schedule->subject_name ?? $schedule->title }}">

                <div class="sc-input-row">
                    <div class="sc-input-group">
                        <label>Program</label>
                        <select name="class_id" id="classSelect" required>
                            <option value="">Pilih Program</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->class_id }}"
                                    {{ $class->class_id == $schedule->class_id ? 'selected' : '' }}>
                                    {{ $class->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sc-input-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" id="subjectSelect" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->subject_id }}"
                                    {{ $subject->subject_id == $schedule->subject_id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="sc-input-group">
                    <label>Pengajar Terdaftar</label>
                    <input type="text" id="teacherNameDisplay" class="sc-input-readonly"
                           readonly value="{{ $schedule->teacher->name ?? 'Akan terisi otomatis...' }}">
                </div>

                <div class="sc-input-row three-col">
                    <div class="sc-input-group">
                        <label>Hari / Tanggal</label>
                        <input type="date" name="date" id="scheduleDate"
                               value="{{ $schedule->date }}" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Mulai</label>
                        <input type="time" name="start_time" id="startTime"
                               value="{{ $schedule->start_time }}" required>
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Selesai</label>
                        <input type="time" name="end_time" id="endTime"
                               value="{{ $schedule->end_time }}" required>
                    </div>
                </div>

                <div class="sc-form-actions">
                    <a href="{{ route('admin.jadwal.index') }}" class="sc-btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="sc-submit-teal">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<style>
    :root {
        --spekta-teal: #14b8a6;
        --spekta-teal-dark: #0d9488;
        --spekta-teal-light: rgba(20, 184, 166, 0.08);
        --spekta-red-light: rgba(229, 57, 53, 0.06);
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .sc-page {
        font-family: 'Montserrat', sans-serif;
        padding: 10px;
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

    /* ── ALERT ── */
    .sc-alert {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 12px 18px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-weight: 700;
        font-size: 13px;
    }

    .sc-alert.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* ── FORM PANEL ── */
    .sc-top-grid {
        display: block;
        margin-bottom: 24px;
    }

    .sc-panel {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #edf0f4;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .sc-panel-heading {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
    }

    .sc-panel-heading h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #111827;
    }

    /* ── FORM ── */
    .sc-input-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .sc-input-row.three-col {
        grid-template-columns: 1fr 1fr 1fr;
    }

    .sc-input-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .sc-input-group label {
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .sc-input-group input,
    .sc-input-group select {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        font-weight: 500;
        font-size: 12px;
        color: #111827;
        outline: none;
        transition: all 0.25s ease;
        font-family: inherit;
    }

    .sc-input-group input:focus,
    .sc-input-group select:focus {
        background: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.08);
    }

    .sc-input-readonly {
        background: #f9fafb !important;
        border-color: #e5e7eb !important;
        color: #6b7280 !important;
        cursor: not-allowed;
    }

    /* ── FORM ACTIONS ── */
    .sc-form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 8px;
        padding-top: 18px;
        border-top: 1px solid #f3f4f6;
    }

    .sc-btn-secondary {
        padding: 0 22px;
        height: 44px;
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

    .sc-btn-secondary:hover {
        background: #e5e7eb;
    }

    /* ── TOMBOL SIMPAN TEAL ── */
    .sc-submit-teal {
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

    .sc-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .sc-submit-teal:active {
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

        .sc-panel {
            padding: 16px;
        }

        .sc-input-row,
        .sc-input-row.three-col {
            grid-template-columns: 1fr;
        }

        .sc-form-actions {
            flex-direction: column-reverse;
        }

        .sc-btn-secondary,
        .sc-submit-teal {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const BASE_URL = "{{ url('/') }}";
        const classSelect = document.getElementById('classSelect');
        const subjectSelect = document.getElementById('subjectSelect');
        const teacherIdHidden = document.getElementById('teacherIdHidden');
        const teacherNameDisplay = document.getElementById('teacherNameDisplay');
        const autoTitle = document.getElementById('autoTitle');

        const initialSubjectId = subjectSelect.value;
        const initialClassId = classSelect.value;

        classSelect.addEventListener('change', function() {
            const classId = this.value;

            subjectSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Memuat...</option>';
            teacherNameDisplay.value = '';
            teacherIdHidden.value = '';
            autoTitle.value = '';

            if (classId) {
                fetch(`${BASE_URL}/admin/jadwal/get-subjects/${classId}`)
                    .then(res => res.json())
                    .then(data => {
                        subjectSelect.disabled = false;
                        subjectSelect.innerHTML = '<option value="">Pilih Mata Pelajaran</option>';

                        if (data.length > 0) {
                            data.forEach(sub => {
                                const selected = (sub.subject_id == initialSubjectId && classId == initialClassId) ? 'selected' : '';
                                subjectSelect.innerHTML += `<option value="${sub.subject_id}" ${selected}>${sub.name}</option>`;
                            });

                            if (subjectSelect.value) {
                                subjectSelect.dispatchEvent(new Event('change'));
                            }
                        } else {
                            subjectSelect.innerHTML = '<option value="">Belum ada mapel</option>';
                        }
                    })
                    .catch(() => {
                        subjectSelect.disabled = false;
                        subjectSelect.innerHTML = '<option value="">Gagal memuat mapel</option>';
                    });
            } else {
                subjectSelect.innerHTML = '<option value="">Pilih program dahulu</option>';
            }
        });

        subjectSelect.addEventListener('change', function() {
            const classId = classSelect.value;
            const subjectId = this.value;

            const selectedText = subjectSelect.options[subjectSelect.selectedIndex]?.text || '';
            autoTitle.value = selectedText;

            if (classId && subjectId) {
                teacherNameDisplay.value = 'Mencari pengajar...';

                fetch(`${BASE_URL}/admin/jadwal/get-teacher/${classId}/${subjectId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.teacher_id) {
                            teacherNameDisplay.value = data.teacher_name;
                            teacherIdHidden.value = data.teacher_id;
                        } else {
                            teacherNameDisplay.value = 'Guru belum ditugaskan';
                            teacherIdHidden.value = '';
                        }
                    })
                    .catch(() => {
                        teacherNameDisplay.value = 'Error mengambil data guru';
                    });
            }
        });

        // Trigger untuk load data awal jika ada class yang dipilih
        if (classSelect.value) {
            classSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
