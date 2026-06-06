<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_attempts', function (Blueprint $table) {
            $table->id();

            // ID User/Siswa yang mengerjakan
            $table->foreignId('user_id');

            // Relasi ke tabel practice_questions
            $table->foreignId('practice_question_id')
                  ->constrained('practice_questions', 'practice_question_id')
                  ->onDelete('cascade');

            // Mencatat jumlah percobaan (0, 1, atau 2)
            $table->integer('attempts_count')->default(0);

            // Mencatat apakah akhirnya menjawab benar atau salah
            $table->boolean('is_correct')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_attempts');
    }
};
