<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankAccount extends Model
{
    protected $guarded = ['id'];

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function balances(): HasMany
    {
        return $this->hasMany(BankAccountBalance::class)
            ->orderByDesc('recorded_date')
            ->orderByDesc('id');
    }

    public function latestBalance(): HasOne
    {
        return $this->hasOne(BankAccountBalance::class)->latestOfMany();
    }

    protected static function booted(): void
    {
        // 1. Trigger saat akun bank baru pertama kali dibuat
        static::created(function (BankAccount $bankAccount) {
            $bankAccount->balances()->create([
                'balance'       => $bankAccount->balance ?? 0, // Sesuaikan nama kolom saldo
                'recorded_date' => now()->toDateString(),
                'note'          => 'Saldo Awal Pembuatan Akun',
            ]);
        });

        // 2. Trigger saat akun bank di-edit dan nominal saldonya berubah
        static::updated(function (BankAccount $bankAccount) {
            if ($bankAccount->wasChanged('balance')) { // Sesuaikan nama kolom saldo
                $bankAccount->balances()->create([
                    'balance'       => $bankAccount->balance, // Sesuaikan nama kolom saldo
                    'recorded_date' => now()->toDateString(),
                    'note'          => 'Perubahan Saldo Awal / Berjalan',
                ]);
            }
        });
    }
}
