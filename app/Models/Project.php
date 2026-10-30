<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $guarded = ['id'];

    // Relasi ke Klien (Wajib ada untuk Select relationship 'client')
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Relasi ke Tasks (Tahapan Tugas)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Relasi ke Vault Akun
    public function accounts(): HasMany
    {
        return $this->hasMany(AppAccount::class);
    }

    // Relasi ke Pemasukan
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    // Relasi ke Jadwal
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}