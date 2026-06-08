<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'class_id',
        'subject_id',  // atau 'subject_name' jika pakai opsi 2
        'teacher_id',
        'title',
        'date',
        'start_time',
        'end_time',
        'meeting_link',
        'status'
    ];

    // ✅ HAPUS relasi subject() karena tidak ada foreign key lagi
    // atau jika pakai subject_name, tidak perlu relasi

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
