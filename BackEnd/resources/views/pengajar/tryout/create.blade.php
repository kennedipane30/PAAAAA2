@extends('layouts.spekta')

@section('title', 'Input Soal TO - ' . $subjectName)

@section('content')
<div class="cp-page">

    {{-- ── 1. HEADER ── --}}
    <section class="cp-header">
        <div class="cp-header-left">
            <h1>Input Soal: <span style="color: var(--spekta-teal);">{{ $subjectName }}</span></h1>
            <p>Program: {{ $classModel->program_name }} (ID Kelas: #{{ $classId }})</p>
        </div>
        <div class="cp-header-actions">
            <a href="{{ route('pengajar.tryout.index') }}" class="cp-secondary-btn">
                Kembali ke Dashboard
            </a>
        </div>
    </section>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="tm-alert-modern success">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="tm-alert-modern error">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="tm-alert-modern warning">
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    {{-- ── 2. BOX IMPORT CSV ── --}}
    <section class="cp-main-card mb-4" style="border: 2px solid var(--border-soft); background: var(--spekta-white); margin-bottom: 24px; border-radius: 16px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.01);">
        <div class="card-header" style="margin-bottom: 14px;">
            <h2 style="color: var(--text-main); font-size: 15px; font-weight: 800; margin: 0 0 4px 0;">Import Soal via CSV</h2>
            <p style="font-size: 11px; color: var(--text-muted); font-weight: 600; margin: 0;">Gunakan fitur ini untuk mengunggah soal dalam jumlah banyak sekaligus.</p>
        </div>

        <form action="{{ route('pengajar.tryout.import_csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="subject_name" value="{{ $subjectName }}">

            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <input type="file" name="file_csv" class="tm-input" accept=".csv" required style="padding: 9px; border: 1px solid var(--border-soft); background: var(--spekta-gray-light);">
                </div>
                <button type="submit" class="cp-submit-teal">
                    MULAI IMPORT
                </button>
            </div>
            <small style="display: block; margin-top: 8px; color: var(--text-muted); font-size: 10px; font-weight: 700;">*Pastikan urutan kolom: Mata Pelajaran, Pertanyaan, Opsi A, B, C, D, E, Kunci, Pembahasan.</small>
        </form>
    </section>

    {{-- ── 3. MAIN WORKSPACE GRID ── --}}
    <div class="tm-grid-layout">

        {{-- KOLOM KIRI: FORM INPUT SOAL --}}
        <section class="cp-main-card">
            <div class="card-header" style="margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--spekta-gray-light);">
                <h2 id="form-title" style="font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">Tambah Soal Baru (Manual)</h2>
                <p id="form-subtitle" style="font-size: 11px; color: var(--text-muted); font-weight: 600; margin: 0;">Isi detail pertanyaan dan pilihan jawaban di bawah ini.</p>
            </div>

            <form action="{{ route('pengajar.tryout.store') }}" method="POST" id="soalForm" class="sc-form">
                @csrf
                <input type="hidden" name="draft_id" id="draft_id" value="">
                <input type="hidden" name="class_id" value="{{ $classId }}">
                <input type="hidden" name="subject_name" value="{{ $subjectName }}">

                <div class="form-group mb-3">
                    <label class="tm-label">Pertanyaan</label>
                    <textarea name="question" id="question" rows="5" class="tm-input" placeholder="Tulis soal di sini..." required></textarea>
                </div>

                <div class="options-grid">
                    @foreach(['a','b','c','d','e'] as $opt)
                    <div class="form-group mb-3">
                        <label class="tm-label">Opsi {{ strtoupper($opt) }}</label>
                        <input type="text" name="option_{{ $opt }}" id="option_{{ $opt }}" class="tm-input" required>
                    </div>
                    @endforeach

                    <div class="form-group mb-3">
                        <label class="tm-label">Kunci Jawaban</label>
                        <select name="correct_answer" id="correct_answer" class="tm-input" required>
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                            <option value="E">Opsi E</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label class="tm-label">Pembahasan (Opsional)</label>
                    <textarea name="explanation" id="explanation" rows="3" class="tm-input" placeholder="Jelaskan cara pengerjaannya..."></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" id="btn-submit" class="btn-submit-teal">
                        <span id="btn-text">Kirim Soal ke Admin</span>
                    </button>

                    <button type="button" id="btn-cancel" onclick="cancelEdit()" style="display: none; flex: 1; background: var(--spekta-gray); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 13px;">
                        Batal
                    </button>
                </div>
            </form>
        </section>

        {{-- KOLOM KANAN: DAFTAR SOAL TERUPLOAD --}}
        <section class="cp-main-card">
            <div class="card-header" style="margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--spekta-gray-light);">
                <h2 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0;">Soal Terkirim ({{ count($existingSoal) }})</h2>
                <p style="font-size: 11px; color: var(--text-muted); font-weight: 600; margin: 0;">Berikut adalah draf soal yang sudah Anda buat.</p>
            </div>

            <div class="soal-list-scroll">
                @forelse($existingSoal as $index => $soal)
                    <div class="soal-item" id="soal-{{ $soal['id'] ?? $soal->id }}">
                        <div class="soal-header">
                            <span class="soal-number">#{{ count($existingSoal) - $index }}</span>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" onclick='editSoal(@json($soal))' class="btn-action-edit" title="Edit Soal">
                                    Edit
                                </button>

                                <form action="{{ route('pengajar.tryout.destroy_draft', $soal['id'] ?? $soal->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete-item" title="Hapus Draf" onclick="return confirm('Apakah Anda yakin ingin menghapus draf soal ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="soal-preview">
                            {{ Str::limit(strip_tags($soal['question'] ?? $soal->question), 120) }}
                        </p>
                        <div class="soal-footer">
                            <span class="key-badge">Kunci: <strong>{{ $soal['correct_answer'] ?? $soal->correct_answer }}</strong></span>
                            <small class="date-text">Dibuat:
                                @php
                                    $createdAt = $soal['created_at'] ?? ($soal->created_at ?? null);
                                    if ($createdAt) {
                                        echo date('d/m H:i', strtotime($createdAt)) . ' WIB';
                                    } else {
                                        echo 'Baru saja';
                                    }
                                @endphp
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <strong>Belum ada soal dikirim.</strong>
                        <span>Silakan input manual atau import CSV.</span>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<script>
    function editSoal(data) {
        document.getElementById('form-title').innerText = "Edit Draf Soal #" + (data.id || data.draft_id);
        document.getElementById('form-subtitle').innerText = "Pastikan perubahan Anda sudah benar sebelum menekan tombol perbarui.";
        document.getElementById('btn-text').innerText = "Perbarui Soal Sekarang";
        document.getElementById('btn-cancel').style.display = "block";
        document.getElementById('btn-submit').style.background = "linear-gradient(135deg, #d97706 0%, #b45309 100%)";

        document.getElementById('draft_id').value = data.id || data.draft_id || '';
        document.getElementById('question').value = data.question || '';
        document.getElementById('option_a').value = data.option_a || '';
        document.getElementById('option_b').value = data.option_b || '';
        document.getElementById('option_c').value = data.option_c || '';
        document.getElementById('option_d').value = data.option_d || '';
        document.getElementById('option_e').value = data.option_e || '';
        document.getElementById('correct_answer').value = data.correct_answer || 'A';
        document.getElementById('explanation').value = data.explanation || '';

        window.scrollTo({ top: 150, behavior: 'smooth' });
    }

    function cancelEdit() {
        document.getElementById('soalForm').reset();
        document.getElementById('draft_id').value = "";
        document.getElementById('question').value = "";
        document.getElementById('option_a').value = "";
        document.getElementById('option_b').value = "";
        document.getElementById('option_c').value = "";
        document.getElementById('option_d').value = "";
        document.getElementById('option_e').value = "";
        document.getElementById('explanation').value = "";
        document.getElementById('correct_answer').value = "A";
        document.getElementById('form-title').innerText = "Tambah Soal Baru (Manual)";
        document.getElementById('form-subtitle').innerText = "Isi detail pertanyaan dan pilihan jawaban di bawah ini.";
        document.getElementById('btn-text').innerText = "Kirim Soal ke Admin";
        document.getElementById('btn-cancel').style.display = "none";
        document.getElementById('btn-submit').style.background = "linear-gradient(135deg, var(--spekta-teal) 0%, var(--spekta-teal-dark) 100%)";
    }
</script>

<style>
    .cp-page { padding: 10px; font-family: 'Montserrat', sans-serif; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

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
    .cp-header h1 span { color: var(--spekta-teal); }
    .cp-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    .cp-secondary-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        color: var(--text-muted);
        border-radius: 12px;
        padding: 10px 16px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cp-secondary-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
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

    /* Layout Grid */
    .tm-grid-layout { display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px; }
    .cp-main-card { background: var(--spekta-white); border-radius: 16px; padding: 20px; border: 1px solid var(--border-soft); box-shadow: 0 4px 15px rgba(0,0,0,0.01); }

    /* Inputs */
    .tm-label { display: block; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 8px; }
    .tm-input {
        width: 100%; padding: 11px; border-radius: 10px; border: 1px solid var(--border-soft);
        background: var(--spekta-gray-light); font-weight: 600; font-size: 12px; outline: none; transition: 0.25s;
    }
    .tm-input:focus { border-color: var(--spekta-teal); background: var(--spekta-white); box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12); }

    /* Options Layout */
    .options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    /* ── TOMBOL SUBMIT TEAL ── */
    .btn-submit-teal {
        flex: 2;
        border: none;
        padding: 12px 20px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--spekta-teal) 0%, var(--spekta-teal-dark) 100%);
        color: var(--spekta-white);
        font-weight: 800;
        cursor: pointer;
        transition: all 0.25s ease;
        font-size: 13px;
        box-shadow: 0 4px 12px rgba(46, 168, 171, 0.25);
        font-family: inherit;
        letter-spacing: 0.04em;
        position: relative;
        overflow: hidden;
    }

    .btn-submit-teal::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
    }

    .btn-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 168, 171, 0.35);
    }

    .btn-submit-teal:hover::before {
        left: 100%;
    }

    .btn-submit-teal:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(46, 168, 171, 0.2);
    }

    /* ── TOMBOL IMPORT TEAL ── */
    .cp-submit-teal {
        height: 42px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--spekta-teal) 0%, var(--spekta-teal-dark) 100%);
        color: var(--spekta-white);
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 4px 12px rgba(46, 168, 171, 0.25);
        transition: all 0.25s ease;
        padding: 0 24px;
        letter-spacing: 0.04em;
        position: relative;
        overflow: hidden;
    }

    .cp-submit-teal::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s ease;
    }

    .cp-submit-teal:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 168, 171, 0.35);
    }

    .cp-submit-teal:hover::before {
        left: 100%;
    }

    .cp-submit-teal:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(46, 168, 171, 0.2);
    }

    /* List Soal Terupload */
    .soal-list-scroll { max-height: 700px; overflow-y: auto; padding-right: 8px; }
    .soal-list-scroll::-webkit-scrollbar { width: 4px; }
    .soal-list-scroll::-webkit-scrollbar-track { background: var(--spekta-gray-light); }
    .soal-list-scroll::-webkit-scrollbar-thumb { background: var(--spekta-gray); border-radius: 10px; }

    .soal-item {
        padding: 16px;
        background: var(--spekta-gray-light);
        border-radius: 12px;
        margin-bottom: 12px;
        border: 1px solid var(--border-soft);
        transition: 0.25s;
    }
    .soal-item:hover {
        border-color: var(--spekta-gray);
        background: var(--spekta-white);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        transform: translateY(-2px);
    }

    .soal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .soal-number {
        font-weight: 900;
        color: var(--spekta-red);
        font-size: 11px;
        background: var(--spekta-red-light);
        padding: 3px 8px;
        border-radius: 6px;
    }

    .btn-action-edit {
        border: none;
        cursor: pointer;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        transition: 0.2s;
        background: #e0f2fe;
        color: #0369a1;
    }
    .btn-action-edit:hover {
        background: #bae6fd;
        color: #02507d;
    }

    .btn-action-delete-item {
        border: none;
        cursor: pointer;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        transition: 0.2s;
        background: var(--spekta-red-light);
        color: var(--spekta-red);
    }
    .btn-action-delete-item:hover {
        background: #fecaca;
        color: #991b1b;
    }

    .soal-preview {
        font-size: 12px;
        color: var(--text-main);
        margin: 10px 0;
        font-weight: 700;
        line-height: 1.5;
    }

    .soal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .key-badge {
        background: #e6f7ed;
        color: #15803d;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
    }
    .date-text {
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 48px;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .empty-state strong {
        display: block;
        color: var(--text-main);
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    @media (max-width: 1000px) {
        .tm-grid-layout { grid-template-columns: 1fr; }
        .options-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .cp-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }

        .cp-submit-teal {
            width: 100%;
            justify-content: center;
        }

        .btn-submit-teal {
            width: 100%;
        }

        .soal-list-scroll {
            max-height: 400px;
        }
    }
</style>
@endsection
