<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id('question_id'); // Primary Key

            // 1. Relasi
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');
            $table->foreignId('tryout_id')->nullable()->constrained('tryouts', 'tryout_id')->onDelete('cascade');

            // ✨ TAMBAHAN DARI FILE MODIFY: Mata Pelajaran
            $table->string('subject', 100)->nullable();

            // 2. Atribut Urutan
            // ✨ MODIFIKASI: Menggunakan default(0) dari file modify
            $table->integer('question_number')->default(0);

            // 3. Konten Soal
            $table->text('question');
            $table->string('question_image')->nullable();

            // 4. Opsi Jawaban (Teks)
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->text('option_e')->nullable();

            // 5. Opsi Jawaban (Gambar)
            $table->string('option_a_image')->nullable();
            $table->string('option_b_image')->nullable();
            $table->string('option_c_image')->nullable();
            $table->string('option_d_image')->nullable();
            $table->string('option_e_image')->nullable();

            // 6. Kunci & Penjelasan
            $table->char('correct_answer', 1); // A, B, C, D, atau E
            $table->text('explanation')->nullable();

            // 7. Scoring
            $table->integer('points')->default(1);

            $table->timestamps();

            // ✨ TAMBAHAN DARI FILE MODIFY: INDEXES (Untuk kecepatan query)
            $table->index('tryout_id');
            $table->index('subject');
            $table->index('question_number');
            $table->index(['tryout_id', 'question_number']); // Composite index
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
