<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajarDashboardController extends Controller
{
    public function index(): View
    {
        // 1. Ambil ID pengajar yang sedang login
        $teacherId = Auth::user()->usersID;
        $today = date('Y-m-d');

        // 2. Hitung total jadwal milik pengajar ini
        $totalJadwal = Schedule::where('teacher_id', $teacherId)->count();

        // 3. Hitung total materi yang diupload pengajar ini
        $totalMateri = Material::where('user_id', $teacherId)->count();

        // 4. Ambil jadwal hari ini + Nama Kelasnya
        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
                            ->whereDate('date', $today)
                            // ✨ MODIFIKASI: Ubah 'classModel' menjadi 'class' agar sesuai dengan Model Schedule
                            ->with('class')
                            ->orderBy('start_time', 'asc')
                            ->get();

        return view('pengajar.dashboard', compact('totalJadwal', 'totalMateri', 'jadwalHariIni'));
    }

    /**
     * Fungsi untuk melihat semua jadwal mengajar (History)
     */
    public function jadwalSaya(): View
    {
        $teacherId = Auth::user()->usersID;

        $jadwal = Schedule::where('teacher_id', $teacherId)
                    // ✨ MODIFIKASI: Ubah 'classModel' menjadi 'class'
                    ->with('class')
                    ->orderBy('date', 'asc')
                    ->get();

        return view('pengajar.jadwal.index', compact('jadwal'));
    }
}
