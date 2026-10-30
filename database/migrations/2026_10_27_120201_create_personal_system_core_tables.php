<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. MASTER WILAYAH
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['kota', 'kabupaten'])->default('kabupaten');
            $table->timestamps();
        });

        // 2. REKENING BANK & E-WALLET
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['bank', 'digital_bank', 'ewallet', 'cash'])->default('bank');
            $table->string('account_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('holder_name')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. KLIEN, MITRA & INSTITUSI
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('institution')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('category', ['kampus', 'lsp', 'mitra_training', 'klien_it', 'desa', 'lainnya']);
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. PROYEK & PENUGASAN
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('role_context', ['dosen_kampus', 'asesor', 'trainer', 'it_sysadmin', 'web_dev']);
            $table->enum('status', ['prospek', 'aktif', 'on_hold', 'selesai'])->default('aktif');
            $table->decimal('contract_value', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 5. TUGAS / TAHAPAN PROYEK (DENGAN SORTING ORDER)
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('order_column')->default(1);
            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. VAULT KREDENSIAL SERVER & APLIKASI
        Schema::create('app_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_name');
            $table->string('host_or_url');
            $table->unsignedSmallInteger('port')->nullable();
            $table->enum('protocol_type', ['ssh', 'web_http', 'database', 'panel_cpanel', 'api_service', 'lainnya'])->default('web_http');
            $table->string('username');
            $table->text('password');
            $table->text('secret_key_or_token')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. PEMASUKAN & PENAGIHAN
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('source', ['kampus', 'asesor', 'trainer', 'proyek_it', 'hibah', 'lainnya']);
            $table->decimal('amount', 15, 2);
            $table->enum('payment_status', ['draft', 'billed', 'paid'])->default('paid');
            $table->date('billed_at')->nullable();
            $table->date('due_date')->nullable();
            $table->date('received_at')->nullable();
            $table->timestamps();
        });

        // 8. PENGELUARAN
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('category', ['operasional_it', 'rumah_tangga', 'peralatan', 'sosial_warga', 'lainnya']);
            $table->decimal('amount', 15, 2);
            $table->date('spent_at');
            $table->timestamps();
        });

        // 9. UTANG & PIUTANG
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('person_name');
            $table->enum('type', ['utang_saya', 'piutang_orang']);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('settled_amount', 15, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->enum('status', ['unpaid', 'partially_paid', 'settled'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 10. JADWAL WORK-LIFE BALANCE
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->enum('category', ['pekerjaan', 'desa', 'keluarga', 'pribadi']);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedSmallInteger('duration_hours')->default(1);
            $table->string('location_name')->nullable();
            $table->timestamps();
        });

        // 11. TARGET FINANSIAL & REM BELANJA
        Schema::create('monthly_targets', function (Blueprint $table) {
            $table->id();
            $table->string('period')->unique();
            $table->decimal('income_target', 15, 2);
            $table->decimal('max_expense_limit', 15, 2);
            $table->unsignedSmallInteger('weekly_work_cap_hours')->default(40);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_targets');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('debts');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('app_accounts');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('provinces');
    }
};
