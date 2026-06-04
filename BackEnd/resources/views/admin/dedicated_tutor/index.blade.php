@extends('layouts.spekta')

@section('title', 'Tutor Request Management')
@section('subtitle', 'Sistem Manajemen Permintaan Dedicated Tutor')

@section('content')
@php
    $tutorCollection = collect($tutors);

    $totalRequests = $tutorCollection->count();
    $pendingRequests = $tutorCollection->where('status', 'pending')->count();
    $confirmedRequests = $tutorCollection->where('status', 'confirmed')->count();
    $rejectedRequests = $tutorCollection->where('status', 'rejected')->count();

    $todayRequests = $tutorCollection->filter(function ($item) {
        return $item->date && \Carbon\Carbon::parse($item->date)->isToday();
    })->count();

    // MODIFIKASI: Ambil semua pengajar menggunakan role_id = 2 (sesuai standar aplikasi Anda)
    $allTeachers = \App\Models\User::where('role_id', 2)->orderBy('name')->get();
@endphp

<div class="dt-page">

    {{-- HERO --}}
    <section class="dt-hero">
        <div class="dt-hero-content">
            <div class="dt-kicker">
                <i class="fa-solid fa-headset"></i>
                <span>Dedicated Tutor Center</span>
            </div>
            <h1>Tutor Request Management</h1>
            <p>Kelola permintaan tutor privat siswa, tetapkan pengajar yang sesuai, dan pantau status konfirmasi.</p>
            <div class="dt-hero-tags">
                <span><i class="fa-solid fa-clock"></i> {{ $pendingRequests }} Pending</span>
                <span><i class="fa-solid fa-circle-check"></i> {{ $confirmedRequests }} Confirmed</span>
                <span><i class="fa-solid fa-calendar-day"></i> {{ $todayRequests }} Hari Ini</span>
            </div>
        </div>
        <div class="dt-hero-panel">
            <div class="dt-ring"><strong>{{ $totalRequests }}</strong><span>Total Request</span></div>
        </div>
    </section>

    {{-- SUMMARY STRIP --}}
    <section class="dt-summary">
        <div><span>Total</span><strong>{{ $totalRequests }}</strong></div>
        <div><span>Pending</span><strong>{{ $pendingRequests }}</strong></div>
        <div><span>Confirmed</span><strong>{{ $confirmedRequests }}</strong></div>
        <div><span>Rejected</span><strong>{{ $rejectedRequests }}</strong></div>
    </section>

    {{-- MAIN GRID --}}
    <section class="dt-main-grid">
        <div class="dt-request-panel">
            <div class="dt-request-list">
                @forelse($tutors as $t)
                    @php
                        $studentName = $t->student->user->name ?? 'N/A';
                        $topicTitle = $t->material->title ?? 'General Topic';
                        $subjectName = $t->material->material_name ?? $topicTitle;
                        $materialClassId = $t->material->class_id ?? null;

                        // MODIFIKASI: Cari pengajar ahli berdasarkan relasi subject (material_name)
                        $qualifiedTeachers = collect();
                        if ($t->status === 'pending' && $materialClassId && $subjectName) {
                            $qualifiedTeachers = \App\Models\TeacherAssignment::whereHas('subject', function($q) use ($subjectName) {
                                    $q->where('material_name', $subjectName);
                                })
                                ->where('class_id', $materialClassId)
                                ->with('teacher')
                                ->get();
                        }
                    @endphp

                    <article class="dt-request-card {{ $t->status }}">
                        <div class="dt-request-main">
                            <div class="dt-student-avatar">{{ strtoupper(substr($studentName, 0, 1)) }}</div>
                            <div class="dt-request-info">
                                <div class="dt-request-head">
                                    <h3>{{ $studentName }}</h3>
                                    <span class="dt-status {{ $t->status }}">{{ strtoupper($t->status) }}</span>
                                </div>
                                <div class="dt-info-grid">
                                    <div><small>Topik</small><strong>{{ $subjectName }}</strong></div>
                                    <div><small>Jadwal</small><strong>{{ $t->date }}</strong><span>{{ $t->time }} WIB</span></div>
                                    <div><small>Guru</small><strong>{{ $t->teacher->name ?? 'Belum ada' }}</strong></div>
                                </div>
                            </div>
                        </div>

                        <div class="dt-action-area">
                            @if($t->status === 'pending')
                                <form action="{{ route('admin.tutor.update', $t->dedicated_tutor_id) }}" method="POST" id="form-{{ $t->dedicated_tutor_id }}">
                                    @csrf
                                    <input type="hidden" name="status" value="confirmed">
                                    <label>Assign Teacher</label>
                                    <select name="teacher_id" required>
                                        <option value="">Pilih pengajar...</option>
                                        @if($qualifiedTeachers->isNotEmpty())
                                            <optgroup label="Pengajar Ahli Materi Ini">
                                                @foreach($qualifiedTeachers as $assign)
                                                    <option value="{{ $assign->teacher->usersID }}">{{ $assign->teacher->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        <optgroup label="Semua Pengajar">
                                            @foreach($allTeachers as $teacher)
                                                <option value="{{ $teacher->usersID }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    <div class="dt-action-buttons">
                                        <button type="submit" class="dt-confirm-btn">Confirm</button>
                                    </div>
                                </form>
                            @else
                                <div class="dt-final-state {{ $t->status }}">
                                    <strong>Request {{ $t->status }}</strong>
                                    <span>Guru: {{ $t->teacher->name ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="dt-empty">Belum ada permintaan.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>

<style>
    /* CSS disederhanakan untuk memastikan tampilan tetap profesional */
    .dt-page { padding: 20px; font-family: 'Inter', sans-serif; }
    .dt-hero { background: linear-gradient(135deg, #d90429 0%, #2b2d42 100%); border-radius: 20px; padding: 30px; color: #fff; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .dt-hero h1 { font-size: 28px; margin-bottom: 10px; font-weight: 900; }
    .dt-hero-tags span { background: rgba(255,255,255,0.15); padding: 5px 12px; border-radius: 10px; font-size: 11px; margin-right: 10px; }
    .dt-ring { background: #fff; color: #111; width: 100px; height: 100px; border-radius: 50%; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .dt-summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
    .dt-summary div { background: #fff; padding: 15px; border-radius: 15px; border: 1px solid #eee; text-align: center; }
    .dt-summary strong { font-size: 24px; display: block; color: #d90429; }
    .dt-request-card { background: #fff; border-radius: 15px; padding: 20px; margin-bottom: 15px; border: 1px solid #eee; display: grid; grid-template-columns: 1fr 250px; gap: 20px; }
    .dt-student-avatar { width: 50px; height: 50px; background: #fff1f2; color: #d90429; border-radius: 12px; display: grid; place-items: center; font-weight: 900; }
    .dt-info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 10px; }
    .dt-info-grid div { background: #f8fafc; padding: 10px; border-radius: 10px; }
    .dt-info-grid small { font-size: 9px; color: #94a3b8; text-transform: uppercase; }
    .dt-info-grid strong { display: block; font-size: 12px; }
    .dt-status { font-size: 10px; padding: 3px 8px; border-radius: 10px; }
    .dt-status.pending { background: #fef3c7; color: #d97706; }
    .dt-status.confirmed { background: #dcfce7; color: #16a34a; }
    .dt-confirm-btn { width: 100%; background: #16a34a; color: #fff; border: none; padding: 10px; border-radius: 10px; font-weight: 800; cursor: pointer; margin-top: 10px; }
    .dt-action-area select { width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd; }
</style>
@endsection
