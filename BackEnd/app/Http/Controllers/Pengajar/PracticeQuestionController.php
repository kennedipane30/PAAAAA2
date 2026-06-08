<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PracticeQuestionController extends Controller
{
    private function getAssignmentsWithSubjects(): array
    {
        $userId = Auth::user()->usersID;

        $assignments = TeacherAssignment::with(['classModel'])
            ->where('user_id', $userId)
            ->get();

        $result = [];
        foreach ($assignments as $assignment) {
            $result[] = (object) [
                'class_id'     => $assignment->class_id,
                'classModel'   => $assignment->classModel,
                'subject_name' => $assignment->subject_name,
                'subject_id'   => $assignment->subject_id,
            ];
        }

        usort($result, function($a, $b) {
            $keyA = $a->class_id . '-' . $a->subject_name;
            $keyB = $b->class_id . '-' . $b->subject_name;
            return strcmp($keyA, $keyB);
        });

        return $result;
    }

    private function hasAssignment(int $classId, string $subjectName): bool
    {
        return TeacherAssignment::where('user_id', Auth::user()->usersID)
            ->where('class_id', $classId)
            ->where('subject_name', $subjectName)
            ->exists();
    }

    public function index()
    {
        $assignmentsWithSubjects = $this->getAssignmentsWithSubjects();
        return view('pengajar.Latihan.index', compact('assignmentsWithSubjects'));
    }

    public function selectPractice($class_id, $subject_name)
    {
        if (!$this->hasAssignment($class_id, $subject_name)) {
            abort(403, 'Anda tidak ditugaskan untuk kelas dan mata pelajaran ini.');
        }

        $class = ClassModel::findOrFail($class_id);

        $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');
        $response = Http::timeout(5)->get("$goUrl/api/tryouts", [
            'class_id' => $class_id
        ]);

        $allQuestions = [];
        if ($response->successful()) {
            $allQuestions = $response->json() ?? [];
        }

        $filteredQuestions = array_filter($allQuestions, function($q) use ($subject_name) {
            return isset($q['subject']) && $q['subject'] == $subject_name;
        });

        $practices = [];
        foreach ($filteredQuestions as $q) {
            $week = $q['week'];
            if (!isset($practices[$week])) {
                $practices[$week] = 0;
            }
            $practices[$week]++;
        }

        $practiceList = [];
        foreach ($practices as $week => $total) {
            $practiceList[] = (object) ['week' => $week, 'total_soal' => $total];
        }
        usort($practiceList, function($a, $b) {
            return $a->week <=> $b->week;
        });

        // ✅ PERBAIKAN: Kirim sebagai 'practices' agar sesuai dengan view
        return view('pengajar.Latihan.pilih', [
            'class'        => $class,
            'subject_name' => $subject_name,
            'practices'    => $practiceList,  // ✅ view menerima $practices
        ]);
    }

    public function storeCSV(Request $request, $class_id)
    {
        $request->validate([
            'subject'  => 'required|string',
            'week'     => 'required|integer|min:1|max:20',
            'file_csv' => 'required|file|mimes:csv,txt'
        ]);

        if (!$this->hasAssignment($class_id, $request->subject)) {
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), "r");

        fgetcsv($handle, 2000, ";");

        $questionsForSync = [];
        $rowIndex = 0;

        while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
            if (!isset($row[0]) || empty(trim($row[0]))) continue;

            $questionsForSync[] = [
                'class_id'            => (int) $class_id,
                'subject'             => trim($request->subject),
                'week'                => (int) $request->week,
                'question'            => trim($row[0]),
                'option_a'            => trim($row[1] ?? '-'),
                'option_b'            => trim($row[2] ?? '-'),
                'option_c'            => trim($row[3] ?? '-'),
                'option_d'            => trim($row[4] ?? '-'),
                'correct_answer'      => strtoupper(trim($row[5] ?? 'A')),
                'hint'                => $row[6] ?? null,
                'explanation'         => $row[7] ?? null,
            ];
            $rowIndex++;
        }
        fclose($handle);

        if (empty($questionsForSync)) {
            return back()->with('error', 'Tidak ada data yang valid di file CSV.');
        }

        try {
            $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');
            $response = Http::timeout(30)->post($goUrl . '/api/practice/sync', $questionsForSync);

            if ($response->successful()) {
                return back()->with('success', "Berhasil! $rowIndex soal latihan minggu ke-{$request->week} telah disimpan.");
            } else {
                return back()->with('error', 'Gagal menyimpan ke microservice.');
            }

        } catch (\Exception $e) {
            Log::error("Koneksi ke Go Practice Service terputus: " . $e->getMessage());
            return back()->with('error', 'Gagal terhubung ke microservice Practice.');
        }
    }

    public function destroyByWeek($class_id, $subject, $week)
    {
        if (!$this->hasAssignment($class_id, $subject)) {
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        try {
            $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');
            $response = Http::timeout(10)->delete("$goUrl/api/practice/class/$class_id/week/$week", [
                'subject' => $subject
            ]);

            if ($response->successful()) {
                return back()->with('success', "Semua soal minggu ke-$week berhasil dihapus!");
            } else {
                return back()->with('error', 'Gagal menghapus: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error("Delete Error: " . $e->getMessage());
            return back()->with('error', 'Gagal terhubung ke microservice Practice.');
        }
    }

    public function showQuestions($class_id, $subject, $week)
    {
        if (!$this->hasAssignment($class_id, $subject)) {
            abort(403, 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');
        $response = Http::timeout(5)->get("$goUrl/api/tryouts", [
            'class_id' => $class_id
        ]);

        $questions = [];
        if ($response->successful()) {
            $allQuestions = $response->json() ?? [];
            $questions = array_filter($allQuestions, function($q) use ($subject, $week) {
                return isset($q['subject']) && $q['subject'] == $subject && $q['week'] == (int)$week;
            });
            $questions = array_values($questions);
        }

        $class = ClassModel::findOrFail($class_id);

        return view('pengajar.Latihan.questions', compact('class', 'subject', 'week', 'questions'));
    }
}
