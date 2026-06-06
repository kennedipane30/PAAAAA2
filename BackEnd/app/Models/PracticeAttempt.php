<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeAttempt extends Model
{
    protected $connection = 'pgsql_practice';
    protected $table = 'practice_attempts';

    protected $fillable = [
        'user_id',
        'practice_question_id',
        'attempts_count',
        'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'attempts_count' => 'integer',
    ];

    public function practiceQuestion()
    {
        return $this->belongsTo(PracticeQuestion::class, 'practice_question_id', 'practice_question_id');
    }
}
