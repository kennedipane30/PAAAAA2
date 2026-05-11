@extends('layouts.spekta')
@section('title', 'Manajemen Absensi')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">📝 Daftar Absensi</h2>
    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Pilih kelas yang dijadwalkan hari ini</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    @foreach($assignments as $as)
        @php $canAbsen = in_array($as->class_id, $jadwalHariIni); @endphp
        <div class="bg-white p-8 rounded-[30px] shadow-sm border-l-[10px] transition {{ $canAbsen ? 'border-green-500 shadow-xl' : 'border-gray-100 opacity-60' }}">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-black text-gray-800 uppercase">{{ $as->classModel->program_name }}</h3>
                    <p class="text-[#990000] font-black text-[10px] uppercase">Bidang: {{ $as->subject_name }}</p>
                </div>
                @if($canAbsen)
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[9px] font-black uppercase">Jadwal Aktif</span>
                @endif
            </div>

            @if($canAbsen)
                <a href="{{ route('pengajar.absensi.show', $as->class_id) }}"
                   class="block w-full bg-[#990000] text-white py-4 rounded-2xl font-black text-center text-[11px] uppercase tracking-widest hover:bg-red-800 shadow-lg shadow-red-100 transition">
                   Buka Form Absen &rarr;
                </a>
            @else
                <div class="p-4 bg-gray-50 rounded-2xl text-center border border-dashed">
                    <p class="text-[10px] font-black text-gray-400 uppercase italic">Absensi Terkunci (Tidak Ada Jadwal)</p>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
