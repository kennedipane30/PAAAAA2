<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    use HasFactory;

    protected $table = 'attendances';
    protected $primaryKey = 'attendance_id';
    protected $fillable = ['schedule_id', 'user_id', 'status', 'date'];

    // Relasi ke Siswa
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    // Relasi ke Jadwal
    public function schedule() {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }
}
