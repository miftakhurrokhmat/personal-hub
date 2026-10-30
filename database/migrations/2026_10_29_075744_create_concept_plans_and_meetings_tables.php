<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Pipa Konsep & Ide Rencana (Sebelum masuk Jadwal / Task)
        Schema::create('concept_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('category', ['pekerjaan', 'desa', 'keluarga', 'pribadi'])->default('pekerjaan');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'review', 'terealisasi', 'dibatalkan'])->default('draft');
            $table->timestamps();
        });

        // 2. Notulensi Rapat (Terhubung ke Proyek & Task)
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title'); // Judul / Topik Bahasan Rapat
            $table->dateTime('meeting_date');
            $table->string('location_or_link')->nullable(); // Ruang / Google Meet
            $table->text('attendees')->nullable(); // Peserta hadir
            $table->longText('discussion_notes'); // Poin diskusi
            $table->longText('action_plans')->nullable(); // Kesepakatan / Tindak lanjut
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_minutes');
        Schema::dropIfExists('concept_plans');
    }
};