<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. SURAT KONTRAK KERJA SEDERHANA
        Schema::create('work_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contract_number')->unique();
            $table->string('job_title');
            $table->text('scope_of_work')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('contract_value', 15, 2)->nullable(); // Bisa dikosongkan/menyusul
            $table->enum('status', ['draft', 'active', 'completed', 'canceled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. SURAT PENUGASAN KE TIM
        Schema::create('assignment_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('letter_number')->unique();
            $table->string('assignment_title');
            $table->text('assigned_personnel'); // Nama-nama tim yang ditugaskan
            $table->text('assignment_duty'); // Tugas dan kewajiban
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location')->nullable();
            $table->timestamps();
        });

        // 3. KWITANSI PENGELUARAN
        Schema::create('expense_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->nullable()->constrained()->nullOnDelete();
            $table->string('receipt_number')->unique();
            $table->string('recipient_name'); // Telah terima dari
            $table->string('payer_name'); // Diserahkan kepada / penerima dana
            $table->decimal('amount', 15, 2);
            $table->text('for_payment'); // Untuk keperluan
            $table->date('receipt_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_receipts');
        Schema::dropIfExists('assignment_letters');
        Schema::dropIfExists('work_contracts');
    }
};
