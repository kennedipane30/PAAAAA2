<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id'); // Primary Key

            // Foreign Key ke Jadwal
            $table->foreignId('schedule_id')
                  ->constrained('schedules', 'schedule_id')
                  ->onDelete('cascade');

            // Foreign Key ke Siswa (usersID)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('usersID')->on('users')->onDelete('cascade');

            // Status Kehadiran
            $table->enum('status', ['present', 'permission', 'absent']);

            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendances');
    }
};
