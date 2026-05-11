@extends('layouts.spekta')
@section('title', 'Input Absensi Siswa')

@section('content')
<div class="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100">
    {{-- HEADER INFO --}}
    <div class="flex justify-between items-center mb-10 border-b pb-6">
        <div>
            {{-- ✨ MODIFIKASI: Menggunakan 'class' sesuai relasi terbaru di Model Schedule --}}
            <h3 class="text-2xl font-black text-gray-800 uppercase">
                {{ $isAssigned->class->program_name ?? 'Program Tidak Ditemukan' }}
            </h3>
            <p class="text-sm font-bold text-[#990000] uppercase tracking-widest">
                Sesi: {{ $isAssigned->title }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-black text-gray-400 uppercase">Tanggal</p>
            <p class="text-lg font-black text-gray-800">{{ date('d M Y') }}</p>
        </div>
    </div>

    {{-- NOTIFIKASI JIKA SUDAH PERNAH ABSEN --}}
    @if($hasAttendance)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8 text-blue-700 text-xs font-bold uppercase tracking-widest rounded-r-xl shadow-sm">
            <i class="fas fa-info-circle mr-2"></i> Sesi ini sudah pernah diabsen. Mengirim ulang akan memperbarui data sebelumnya.
        </div>
    @endif

    {{-- PESAN SUKSES / ERROR --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-2xl mb-6 font-bold text-xs uppercase">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-2xl mb-6 font-bold text-xs uppercase">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pengajar.absensi.store') }}" method="POST">
        @csrf
        {{-- Mengirim ID Jadwal --}}
        <input type="hidden" name="schedule_id" value="{{ $isAssigned->schedule_id }}">

        <div class="space-y-4">
            @foreach($siswas as $s)
            <div class="flex items-center justify-between p-6 bg-gray-50 rounded-3xl border border-gray-100 hover:bg-white hover:shadow-md transition duration-300">
                <div>
                    <p class="font-black text-gray-800 text-sm uppercase">{{ $s->user->name }}</p>
                    <p class="text-[10px] font-bold text-gray-400">NISN: {{ $s->user->student->national_id_number ?? '-' }}</p>
                </div>

                <div class="flex gap-4">
                    @foreach(['present' => 'Hadir', 'permission' => 'Izin', 'absent' => 'Alpa'] as $val => $lbl)
                    <label class="cursor-pointer group">
                        <input type="radio"
                               name="status[{{ $s->user->usersID }}]"
                               value="{{ $val }}"
                               class="hidden peer"
                               required>
                        <div class="px-5 py-2.5 rounded-xl text-[9px] font-black uppercase border border-gray-200 transition
                                    peer-checked:bg-[#990000] peer-checked:text-white peer-checked:border-[#990000]
                                    peer-checked:shadow-lg peer-checked:shadow-red-100">
                            {{ $lbl }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- TOMBOL SIMPAN --}}
        @if($siswas->count() > 0)
            <button type="submit" class="w-full mt-10 bg-green-600 text-white py-5 rounded-[25px] font-black text-[11px] uppercase tracking-widest shadow-xl shadow-green-100 hover:bg-green-700 transition transform active:scale-95">
                Selesaikan & Simpan Kehadiran
            </button>
        @else
            <div class="p-10 text-center text-gray-400 font-bold uppercase text-xs italic">
                Belum ada siswa aktif yang terdaftar di program ini.
            </div>
        @endif
    </form>
</div>
@endsection
