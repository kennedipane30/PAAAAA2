<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\TeacherAssignment; // Logika Master Key
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    /**
     * Menampilkan daftar kelas yang ditugaskan (Master Key)
     * dan mengecek apakah ada jadwal hari ini.
     */
    public function index(): View
    {
        $teacherId = Auth::user()->usersID;
        $today = date('Y-m-d');

        // 1. Ambil kelas berdasarkan Penugasan Admin (Master Key)
        $assignments = TeacherAssignment::with('classModel')
                        ->where('user_id', $teacherId)
                        ->get();

        // 2. Ambil daftar class_id yang memiliki jadwal mengajar hari ini
        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
                            ->whereDate('date', $today)
                            ->pluck('class_id')
                            ->toArray();

        return view('pengajar.absensi.index', compact('assignments', 'jadwalHariIni'));
    }

    /**
     * Menampilkan form absensi untuk siswa di kelas tertentu.
     */
   public function show($class_id)
{
    $teacherId = Auth::user()->usersID;
    $today = date('Y-m-d');

    $isAssigned = Schedule::where('class_id', $class_id)
                        ->where('teacher_id', $teacherId)
                        ->whereDate('date', $today)
                        // ✨ PERBAIKAN: Pastikan ini 'class', bukan 'classModel'
                        ->with('class')
                        ->first();

    if (!$isAssigned) {
        return redirect()->route('pengajar.absensi.index')->with('info', 'Jadwal tidak ditemukan.');
    }

    $siswas = Enrollment::where('class_id', $class_id)
                ->where('status', 'active')
                ->with(['user.student'])
                ->get();

    $hasAttendance = \App\Models\Attendance::where('schedule_id', $isAssigned->schedule_id)
                                ->where('date', $today)
                                ->exists();

    return view('pengajar.absensi.show', compact('siswas', 'isAssigned', 'hasAttendance'));
}
    /**
     * Menyimpan data absensi secara massal.
     */
    public function store(Request $request)
    {
        if (!$request->has('status')) {
            return back()->with('error', 'Tidak ada data siswa untuk disimpan.');
        }

        foreach ($request->status as $usersID => $status) {
            Attendance::updateOrCreate(
                [
                    'schedule_id' => $request->schedule_id,
                    'user_id'     => $usersID,
                    'date'        => date('Y-m-d')
                ],
                ['status' => $status]
            );
        }

        $schedule = Schedule::find($request->schedule_id);
        return redirect()->route('pengajar.absensi.show', $schedule->class_id)
                         ->with('success', 'Absensi berhasil disimpan!');
    }

    /**
     * Melihat detail absensi yang sudah diisi.
     */
    public function detail($schedule_id): View
    {
        $schedule = Schedule::with('classModel')->findOrFail($schedule_id);
        $attendances = Attendance::where('schedule_id', $schedule_id)
                                 ->where('date', date('Y-m-d'))
                                 ->with('user')
                                 ->get();

        return view('pengajar.absensi.detail', compact('attendances', 'schedule'));
    }
}
