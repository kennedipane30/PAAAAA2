@extends('layouts.spekta')

@section('title', 'Schedule Management')
@section('subtitle', 'Sistem Manajemen Terpadu Spekta Academy')

@section('content')
@php
    $userRole = auth()->user()->role->name ?? 'admin';
    $isAdmin = $userRole === 'admin';
    $isTeacher = $userRole === 'teacher';
    $isStudent = $userRole === 'student';
@endphp

<div class="sc-page">

    {{-- ── WELCOME CARD ── --}}
    <section class="welcome-card">
        <div class="welcome-text">
            <h1>
                @if($isAdmin) Atur Waktu Pembelajaran
                @elseif($isTeacher) Jadwal Mengajar Saya
                @else Jadwal Kelas Saya
                @endif
            </h1>
            <p>Tentukan hari dan jam belajar berdasarkan penugasan pengajar yang sudah diatur sebelumnya.</p>
        </div>
    </section>

    @if(session('success'))
        <div class="sc-alert success">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── STATS CARDS ── --}}
    <section class="sc-stats">
        <div class="sc-stat-card card-red">
            <div class="sc-stat-info">
                <p>Total Jadwal</p>
                <strong>{{ number_format($totalJadwalBulanIni ?? 0) }}</strong>
            </div>
        </div>
        <div class="sc-stat-card card-teal">
            <div class="sc-stat-info">
                <p>Hari Ini</p>
                <strong>{{ number_format($jadwalHariIni ?? 0) }}</strong>
            </div>
        </div>
        <div class="sc-stat-card card-gray">
            <div class="sc-stat-info">
                <p>Selesai</p>
                <strong>{{ number_format($jadwalSelesaiTotal ?? 0) }}</strong>
            </div>
        </div>
    </section>

    {{-- ── FORM BUAT JADWAL (HANYA UNTUK ADMIN) ── --}}
    @if($isAdmin)
    <section class="sc-top-grid">
        <div class="sc-panel sc-form-panel">
            <div class="sc-panel-heading">
                <h2>Buat Jadwal Baru</h2>
            </div>

            <form action="{{ route('admin.jadwal.store') }}" method="POST" class="sc-form" id="scheduleForm">
                @csrf

                <input type="hidden" name="teacher_id" id="teacherIdHidden">
                <input type="hidden" name="title" id="autoTitle">

                <div class="sc-input-row">
                    <div class="sc-input-group">
                        <label>Program</label>
                        <select name="class_id" id="classSelect" required>
                            <option value="">Pilih Program</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->class_id }}">{{ $class->program_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sc-input-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" id="subjectSelect" required disabled>
                            <option value="">Pilih program dahulu</option>
                        </select>
                    </div>
                </div>

                <div class="sc-input-group">
                    <label>Pengajar Terdaftar</label>
                    <input type="text" id="teacherNameDisplay" class="sc-input-readonly" readonly placeholder="Akan terisi otomatis berdasarkan mata pelajaran...">
                </div>

                <div class="sc-input-row three-col">
                    <div class="sc-input-group">
                        <label>Hari / Tanggal</label>
                        <input type="date" name="date" id="scheduleDate" required min="{{ date('Y-m-d') }}">
                        <small class="error-msg" id="dateError">Tanggal tidak boleh kurang dari hari ini</small>
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Mulai</label>
                        <input type="time" name="start_time" id="startTime" required>
                        <small class="error-msg" id="startTimeError">Jam mulai tidak valid</small>
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Selesai</label>
                        <input type="time" name="end_time" id="endTime" required>
                        <small class="error-msg" id="endTimeError">Jam selesai harus setelah jam mulai</small>
                    </div>
                </div>

                <button type="submit" class="sc-submit-teal">
                    Publikasikan Jadwal
                </button>
            </form>
        </div>
    </section>
    @endif

    {{-- ── TABLE JADWAL ── --}}
    <section class="sc-table-panel">
        <div class="sc-table-wrap">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Program</th>
                        <th>Mata Pelajaran</th>
                        <th>Pengajar</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $row)
                        @php
                            $start = \Carbon\Carbon::parse($row->date . ' ' . $row->start_time);
                            $end = \Carbon\Carbon::parse($row->date . ' ' . $row->end_time);
                            $now = now();
                            $status = $now->between($start, $end) ? 'ongoing' : ($now->greaterThan($end) ? 'finished' : 'scheduled');
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $start->translatedFormat('d M Y') }}</strong>
                                <small>{{ $start->format('H:i') }} - {{ $end->format('H:i') }}</small>
                            </td>
                            <td>{{ $row->class->program_name ?? '-' }}</td>
                            <td>{{ $row->subject_name ?? $row->title }}</td>
                            <td>{{ $row->teacher->name ?? '-' }}</td>
                            <td><span class="sc-status-badge {{ $status }}">{{ ucfirst($status) }}</span></td>
                            <td>
                                <div class="sc-actions-wrap">
                                    <a href="{{ route('admin.jadwal.edit', $row->schedule_id) }}" class="btn-edit">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.jadwal.destroy', $row->schedule_id) }}" method="POST" style="display: inline-flex;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete" onclick="return confirm('Hapus jadwal ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="sc-empty-state">
                                    <strong>Belum ada jadwal yang diatur.</strong>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    :root {
        --spekta-red-dark: #c5352c;
        --spekta-red: #e53935;
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
    .sc-alert.success {
        background: #e6f7ed;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    /* ── STATS CARDS ── */
    .sc-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .sc-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .sc-stat-card:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .sc-stat-card.card-red {
        background: linear-gradient(135deg, #e53935 0%, #c5352c 100%);
        box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
    }
    .sc-stat-card.card-red:hover {
        box-shadow: 0 8px 30px rgba(229, 57, 53, 0.4);
    }

    .sc-stat-card.card-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
    }
    .sc-stat-card.card-teal:hover {
        box-shadow: 0 8px 30px rgba(20, 184, 166, 0.4);
    }

    .sc-stat-card.card-gray {
        background: linear-gradient(135deg, #9e9e9e 0%, #6b7280 100%);
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    }
    .sc-stat-card.card-gray:hover {
        box-shadow: 0 8px 30px rgba(107, 114, 128, 0.4);
    }

    .sc-stat-card::after {
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

    .sc-stat-card::before {
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

    .sc-stat-info {
        position: relative;
        z-index: 1;
    }

    .sc-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        color: rgba(255, 255, 255, 0.9);
    }

    .sc-stat-info strong {
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        display: block;
        line-height: 1.2;
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

    .error-msg {
        color: #dc2626;
        font-size: 10px;
        font-weight: 600;
        display: none;
        margin-top: 2px;
    }

    .error-msg.show {
        display: block;
    }

    /* ── TOMBOL SUBMIT TEAL ── */
    .sc-submit-teal {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        color: #ffffff;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        margin-top: 8px;
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: inherit;
        letter-spacing: 0.02em;
    }

    .sc-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.35);
    }

    .sc-submit-teal:active {
        transform: scale(0.97);
    }

    /* ── TABLE ── */
    .sc-table-panel {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #edf0f4;
    }

    .sc-table-wrap {
        overflow-x: auto;
    }

    .sc-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .sc-table th {
        text-align: left;
        padding: 10px 14px;
        font-size: 9px;
        color: #6b7280;
        text-transform: uppercase;
        border-bottom: 2px solid #f3f4f6;
        font-weight: 700;
        letter-spacing: 0.08em;
    }

    .sc-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 12px;
        font-weight: 500;
        color: #374151;
        vertical-align: middle;
    }

    .sc-table tbody tr:last-child td {
        border-bottom: none;
    }

    .sc-table tbody tr:hover {
        background: #fafbfc;
    }

    .sc-table td strong {
        display: block;
        font-weight: 700;
        color: #111827;
        font-size: 12px;
    }

    .sc-table td small {
        color: #9ca3af;
        font-weight: 500;
        font-size: 11px;
    }

    /* ── STATUS BADGE ── */
    .sc-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 22px;
        padding: 0 10px;
        border-radius: 6px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .sc-status-badge.ongoing {
        background: #e6f7ed;
        color: #15803d;
    }
    .sc-status-badge.scheduled {
        background: #e0f2fe;
        color: #0269a1;
    }
    .sc-status-badge.finished {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* ── ACTION BUTTONS ── */
    .sc-actions-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-start;
    }

    .btn-edit {
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

    .btn-edit:hover {
        background: #3b82f6;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-delete {
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

    .btn-delete:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* ── EMPTY STATE ── */
    .sc-empty-state {
        padding: 32px;
        text-align: center;
        color: #6b7280;
    }

    .sc-empty-state strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .text-center {
        text-align: center;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        .sc-stats {
            grid-template-columns: 1fr;
        }

        .sc-input-row,
        .sc-input-row.three-col {
            grid-template-columns: 1fr;
        }

        .welcome-card {
            padding: 20px;
        }

        .welcome-text h1 {
            font-size: 18px;
        }

        .sc-panel {
            padding: 16px;
        }
    }

    @media (max-width: 768px) {
        .sc-table {
            min-width: 600px;
        }

        .sc-actions-wrap {
            flex-direction: column;
            gap: 4px;
        }

        .btn-edit,
        .btn-delete {
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

        const dateInput = document.getElementById('scheduleDate');
        const startTimeInput = document.getElementById('startTime');
        const endTimeInput = document.getElementById('endTime');
        const dateError = document.getElementById('dateError');
        const startTimeError = document.getElementById('startTimeError');
        const endTimeError = document.getElementById('endTimeError');
        const form = document.getElementById('scheduleForm');

        const today = new Date().toISOString().split('T')[0];
        if (dateInput) {
            dateInput.setAttribute('min', today);
        }

        function validateDate() {
            const selectedDate = dateInput.value;
            if (!selectedDate) return true;

            const selected = new Date(selectedDate);
            const todayDate = new Date();
            todayDate.setHours(0, 0, 0, 0);

            if (selected < todayDate) {
                dateError.classList.add('show');
                return false;
            } else {
                dateError.classList.remove('show');
                return true;
            }
        }

        function validateStartTime() {
            const selectedDate = dateInput.value;
            const startTime = startTimeInput.value;

            if (!selectedDate || !startTime) return true;

            const selectedDateTime = new Date(`${selectedDate}T${startTime}`);
            const now = new Date();

            const isToday = selectedDate === today;

            if (isToday && selectedDateTime < now) {
                startTimeError.textContent = 'Jam mulai tidak boleh kurang dari jam sekarang';
                startTimeError.classList.add('show');
                return false;
            } else {
                startTimeError.classList.remove('show');
                return true;
            }
        }

        function validateEndTime() {
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;

            if (!startTime || !endTime) return true;

            if (endTime <= startTime) {
                endTimeError.classList.add('show');
                return false;
            } else {
                endTimeError.classList.remove('show');
                return true;
            }
        }

        if (dateInput) {
            dateInput.addEventListener('change', function() {
                validateDate();
                if (dateInput.value === today) {
                    validateStartTime();
                } else {
                    startTimeError.classList.remove('show');
                }
            });
        }

        if (startTimeInput) {
            startTimeInput.addEventListener('change', validateStartTime);
        }

        if (endTimeInput) {
            endTimeInput.addEventListener('change', validateEndTime);
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                const isDateValid = validateDate();
                const isStartTimeValid = validateStartTime();
                const isEndTimeValid = validateEndTime();

                if (!isDateValid || !isStartTimeValid || !isEndTimeValid) {
                    e.preventDefault();
                    let errorMsg = 'Jadwal tidak valid:\n';
                    if (!isDateValid) errorMsg += '- Tanggal tidak boleh kurang dari hari ini\n';
                    if (!isStartTimeValid) errorMsg += '- Jam mulai tidak valid (jika hari ini, tidak boleh kurang dari jam sekarang)\n';
                    if (!isEndTimeValid) errorMsg += '- Jam selesai harus setelah jam mulai\n';
                    alert(errorMsg);
                }
            });
        }

        if (classSelect) {
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
                                    subjectSelect.innerHTML += `<option value="${sub.subject_id}">${sub.name}</option>`;
                                });
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

                const selectedText = subjectSelect.options[subjectSelect.selectedIndex].text;
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
        }
    });
</script>
@endsection
