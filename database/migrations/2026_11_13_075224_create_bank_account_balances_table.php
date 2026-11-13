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
        Schema::create('bank_account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')
                ->constrained('bank_accounts')
                ->cascadeOnDelete();
            $table->decimal('balance', 15, 2); // Nominal saldo saat dicatat
            $table->date('recorded_date');     // Tanggal perubahan/pencatatan saldo
            $table->string('note')->nullable(); // Opsional: keterangan (misal: "Saldo Awal", "Penyesuaian Saldo")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_account_balances');
    }
};
