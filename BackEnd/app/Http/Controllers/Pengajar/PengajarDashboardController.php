<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\Schedule;
use App\Models\TeacherAssignment;
use App\Models\ClassModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PengajarDashboardController extends Controller
{
    /**
     * Ambil data materi dari Microservice Materi (Port 9001)
     */
    private function getMaterialsFromMicroservice(?int $classId = null): array
    {
        try {
            $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
            $url = $goUrl . '/api/materials';

            if ($classId) {
                $url .= '?class_id=' . $classId;
            }

            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil materi dari microservice: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Ambil data tryout dari Microservice Tryout (Port 9002)
     */
    private function getTryoutsFromMicroservice(?int $classId = null): array
    {
        try {
            $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
            $url = $goUrl . '/api/tryouts';

            if ($classId) {
                $url .= '?class_id=' . $classId;
            }

            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil tryout dari microservice: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Ambil data practice dari Microservice Practice (Port 9003)
     */
    private function getPracticeFromMicroservice(?int $classId = null): array
    {
        try {
            $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');
            $url = $goUrl . '/api/tryouts';

            if ($classId) {
                $url .= '?class_id=' . $classId;
            }

            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                return $response->json() ?? [];
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil practice dari microservice: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Hitung total materi dari microservice berdasarkan assignments
     */
    private function getTotalMateriFromMicroservice($assignments): int
    {
        $total = 0;
        $classIds = $assignments->pluck('class_id')->unique();

        foreach ($classIds as $classId) {
            $materials = $this->getMaterialsFromMicroservice($classId);
            $total += count($materials);
        }

        return $total;
    }

    /**
     * Hitung total mapel unik dari assignments (tanpa query ke database)
     */
    private function getTotalMapelFromAssignments($assignments): int
    {
        return $assignments->pluck('subject_id')->unique()->count();
    }

    /**
     * Dashboard utama pengajar
     */
    public function index(): View
    {
        $teacherId = Auth::user()->usersID;
        $today = Carbon::today();

        // Ambil data assignments (penugasan guru ke kelas dan mata pelajaran)
        $assignments = TeacherAssignment::with(['classModel'])
            ->where('user_id', $teacherId)
            ->latest()
            ->get();

        // Statistik dasar
        $totalKelas = $assignments->pluck('class_id')->unique()->count();
        $totalMapel = $this->getTotalMapelFromAssignments($assignments);
        $totalMateri = $this->getTotalMateriFromMicroservice($assignments);

        // Data dari microservice
        $tryouts = $this->getTryoutsFromMicroservice();
        $totalTryout = count($tryouts);

        $practices = $this->getPracticeFromMicroservice();
        $totalLatihan = count($practices);

        // Kumpulkan semua materi untuk ditampilkan di dashboard
        $allMaterials = [];
        $classIds = $assignments->pluck('class_id')->unique();

        foreach ($classIds as $classId) {
            $materials = $this->getMaterialsFromMicroservice($classId);
            foreach ($materials as $material) {
                $material['class_id'] = $classId;
                $allMaterials[] = $material;
            }
        }

        // Urutkan berdasarkan created_at terbaru
        usort($allMaterials, function($a, $b) {
            $timeA = $a['created_at'] ?? $a['updated_at'] ?? '1970-01-01';
            $timeB = $b['created_at'] ?? $b['updated_at'] ?? '1970-01-01';
            return strtotime($timeB) - strtotime($timeA);
        });

        $materiTerbaru = array_slice($allMaterials, 0, 5);

        // Jadwal mengajar mendatang
        $jadwalMendatang = Schedule::where('teacher_id', $teacherId)
            ->whereDate('date', '>=', $today->toDateString())
            ->with('class')
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(8)
            ->get();

        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
            ->whereDate('date', $today->toDateString())
            ->count();

        // Jadwal dedicated tutor
        $jadwalTutor = DedicatedTutor::where('teacher_id', $teacherId)
            ->where('status', 'confirmed')
            ->whereDate('date', '>=', $today->toDateString())
            ->with(['student.user'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(8)
            ->get();

        $tutorHariIni = DedicatedTutor::where('teacher_id', $teacherId)
            ->where('status', 'confirmed')
            ->whereDate('date', $today->toDateString())
            ->count();

        // Aktivitas terbaru
        $aktivitasTerbaru = collect();

        // Aktivitas dari materi
        foreach (array_slice($allMaterials, 0, 3) as $materi) {
            $class = ClassModel::find($materi['class_id'] ?? 0);
            $className = $class ? $class->program_name : 'Kelas ' . ($materi['class_id'] ?? '');

            $aktivitasTerbaru->push([
                'type' => 'Materi',
                'icon' => 'fa-book-open',
                'title' => $materi['title'] ?? $materi['subject_name'] ?? 'Materi baru',
                'subtitle' => 'Materi di ' . $className,
                'time' => $materi['created_at'] ?? $materi['updated_at'] ?? now(),
                'sort_time' => strtotime($materi['created_at'] ?? $materi['updated_at'] ?? now()),
            ]);
        }

        // Aktivitas dari jadwal
        foreach ($jadwalMendatang->take(3) as $jadwal) {
            $aktivitasTerbaru->push([
                'type' => 'Jadwal',
                'icon' => 'fa-calendar-days',
                'title' => $jadwal->title,
                'subtitle' => ($jadwal->class->program_name ?? 'Program') . ' • ' . Carbon::parse($jadwal->date)->translatedFormat('d M Y'),
                'time' => $jadwal->created_at,
                'sort_time' => Carbon::parse($jadwal->date . ' ' . $jadwal->start_time)->timestamp,
            ]);
        }

        // Aktivitas dari tutor
        foreach ($jadwalTutor->take(3) as $tutor) {
            $aktivitasTerbaru->push([
                'type' => 'Tutor',
                'icon' => 'fa-headset',
                'title' => $tutor->student->user->name ?? 'Siswa',
                'subtitle' => 'Sesi dedicated tutor terkonfirmasi',
                'time' => $tutor->created_at,
                'sort_time' => Carbon::parse($tutor->date . ' ' . $tutor->time)->timestamp,
            ]);
        }

        // Urutkan aktivitas berdasarkan waktu terbaru
        $aktivitasTerbaru = $aktivitasTerbaru
            ->sortByDesc('sort_time')
            ->take(6)
            ->values();

        return view('pengajar.dashboard', compact(
            'assignments',
            'totalKelas',
            'totalMapel',
            'totalMateri',
            'totalLatihan',
            'totalTryout',
            'jadwalMendatang',
            'jadwalHariIni',
            'jadwalTutor',
            'tutorHariIni',
            'materiTerbaru',
            'aktivitasTerbaru'
        ));
    }
}
