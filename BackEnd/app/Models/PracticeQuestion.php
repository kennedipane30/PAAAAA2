<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PracticeAttempt; // ✨ TAMBAHKAN BARIS INI


class PracticeQuestion extends Model
{
    protected $connection = 'pgsql_practice';
    protected $table = 'practice_questions';
    protected $primaryKey = 'practice_question_id';

    protected $fillable = [
        'class_id', 'subject', 'week', 'question', 'option_a', 'option_b',
        'option_c', 'option_d', 'correct_answer', 'hint', 'explanation' // MODIFIKASI: Tambah 'hint'
    ];

    // Relasi ke tabel riwayat percobaan (One to Many)
    public function attempts()
    {
        return $this->hasMany(PracticeAttempt::class, 'practice_question_id', 'practice_question_id');
    }
}
